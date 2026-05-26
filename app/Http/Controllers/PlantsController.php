<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlantRequest;
use App\Models\Plant;
use App\Models\WateringEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlantsController extends Controller
{
    /**
     * Overview di tutte le piante dell'utente.
     */
    public function index(Request $request): View
    {
        $user   = $request->user();
        $plants = Plant::where('user_id', $user->user_id)
            ->with(['sensorReadings' => fn($q) => $q->latest('recorded_at')->limit(1)])
            ->orderBy('plant_name')
            ->get();

        return renderPage('plants.index', [
            'title'  => 'Le mie piante',
            'user'   => $user,
            'plants' => $plants,
        ]);
    }

    /**
     * Dettaglio singola pianta.
     */
    public function show(Request $request, int $id): View
    {
        $plant = Plant::where('user_id', $request->user()->user_id)
            ->with(['sensorReadings' => fn($q) => $q->latest('recorded_at')->limit(50)])
            ->findOrFail($id);

        return renderPage('plants.show', [
            'title' => 'La mia pianta',
            'user'  => $request->user(),
            'plant' => $plant,
        ]);
    }

    /**
     * Form di aggiunta pianta.
     */
    public function create(): View
    {
        return renderPage('plants.create', [
            'title' => 'Aggiungi pianta',
        ]);
    }

    /**
     * Salva la nuova pianta.
     */
    public function store(StorePlantRequest $request): RedirectResponse
    {
        $plant = Plant::create([
            ...$request->validated(),
            'user_id' => $request->user()->user_id,
        ]);

        return redirect()->route('plants.show', $plant->plant_id)
            ->with('success', "'{$plant->plant_name}' aggiunta con successo!");
    }

    /**
     * Aggiorna i dati di una pianta via PATCH (chiamata JSON dal frontend).
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $plant = Plant::where('user_id', $request->user()->user_id)->findOrFail($id);

        $validated = $request->validate([
            'plant_name'     => ['sometimes', 'string', 'max:100'],
            'notes'          => ['sometimes', 'nullable', 'string', 'max:500'],
            'hum_min'        => ['sometimes', 'numeric', 'min:0', 'max:100'],
            'hum_max'        => ['sometimes', 'numeric', 'min:0', 'max:100', 'gte:hum_min'],
            'temp_min'       => ['sometimes', 'numeric', 'min:-10', 'max:60'],
            'temp_max'       => ['sometimes', 'numeric', 'min:-10', 'max:60', 'gte:temp_min'],
            'soil_hum_min'   => ['sometimes', 'numeric', 'min:0', 'max:100'],
            'soil_hum_max'   => ['sometimes', 'numeric', 'min:0', 'max:100', 'gte:soil_hum_min'],
            'watering_cycle' => ['sometimes', 'integer', 'min:1'],
            'plant_variant'  => ['sometimes', 'nullable', 'integer', 'min:0', 'max:7'],
            'plant_color'    => ['sometimes', 'nullable', 'integer', 'min:0', 'max:5'],
            'flower_color'   => ['sometimes', 'nullable', 'integer', 'min:0', 'max:6'],
            'pot_color'      => ['sometimes', 'nullable', 'integer', 'min:0', 'max:2'],
        ]);

        $plant->update($validated);

        return response()->json(['status' => 'ok', 'plant' => $plant->fresh()]);
    }

    /**
     * Registra un'annaffiatura manuale.
     */
    public function water(Request $request, int $id): JsonResponse
    {
        $plant = Plant::where('user_id', $request->user()->user_id)->findOrFail($id);

        WateringEvent::create([
            'plant_id' => $plant->plant_id,
            'source'   => 'manual_app',
        ]);

        return response()->json(['status' => 'ok', 'watered_at' => now()->toDateTimeString()]);
    }

    /**
     * Storico misto: annaffiature + letture anomale, in ordine cronologico.
     */
    public function history(Request $request, int $id): JsonResponse
    {
        $plant = Plant::where('user_id', $request->user()->user_id)->findOrFail($id);

        // Annaffiature
        $waterings = $plant->wateringEvents()
            ->latest('watered_at')
            ->take(30)
            ->get()
            ->map(fn($ev) => [
                'type'       => 'watering',
                'label'      => match($ev->source) {
                    'button'     => 'Annaffiatura (bottone ESP)',
                    'manual_app' => 'Annaffiatura manuale',
                    'scheduled'  => 'Annaffiatura automatica',
                    default      => 'Annaffiatura',
                },
                'detail'     => null,
                'date'       => $ev->watered_at,
                // ✅ formato "11 mag 09:14"
                'date_str'   => $ev->watered_at->locale('it')->isoFormat('D MMM HH:mm'),
                'date_human' => $ev->watered_at->locale('it')->diffForHumans(),
            ]);

        // Letture anomale
        $warnings = $plant->sensorReadings()
            ->latest('recorded_at')
            ->take(60)
            ->get()
            ->filter(function ($r) use ($plant) {
                return ($r->temperature !== null && ($r->temperature < $plant->temp_min || $r->temperature > $plant->temp_max))
                    || ($r->humidity !== null && ($r->humidity < $plant->hum_min || $r->humidity > $plant->hum_max))
                    || ($r->soil_humidity !== null && ($r->soil_humidity < $plant->soil_hum_min || $r->soil_humidity > $plant->soil_hum_max));
            })
            ->map(function ($r) use ($plant) {
                $issues = [];
                if ($r->temperature !== null) {
                    if ($r->temperature > $plant->temp_max)      $issues[] = 'Temperatura alta: ' . round($r->temperature, 1) . '°C';
                    elseif ($r->temperature < $plant->temp_min)  $issues[] = 'Temperatura bassa: ' . round($r->temperature, 1) . '°C';
                }
                if ($r->humidity !== null) {
                    if ($r->humidity > $plant->hum_max)          $issues[] = 'Umidità alta: ' . round($r->humidity) . '%';
                    elseif ($r->humidity < $plant->hum_min)      $issues[] = 'Umidità bassa: ' . round($r->humidity) . '%';
                }
                if ($r->soil_humidity !== null) {
                    if ($r->soil_humidity > $plant->soil_hum_max)   $issues[] = 'Suolo umido: ' . round($r->soil_humidity) . '%';
                    elseif ($r->soil_humidity < $plant->soil_hum_min) $issues[] = 'Suolo secco: ' . round($r->soil_humidity) . '%';
                }
                return [
                    'type'       => 'warning',
                    'label'      => count($issues) === 1 ? $issues[0] : 'Parametri fuori range',
                    'detail'     => count($issues) > 1 ? implode(', ', $issues) : null,
                    'date'       => $r->recorded_at,
                    'date_str'   => $r->recorded_at->locale('it')->isoFormat('D MMM HH:mm'),
                    'date_human' => $r->recorded_at->locale('it')->diffForHumans(),
                ];
            });

        $events = $waterings->concat($warnings)
            ->sortByDesc('date')
            ->take(30)
            ->values()
            ->map(fn($ev) => collect($ev)->except('date')->all());

        return response()->json(['events' => $events]);
    }

    public function latestReading(Request $request, int $id): JsonResponse
    {
        $plant = Plant::where('user_id', $request->user()->user_id)->findOrFail($id);

        $reading = $plant->sensorReadings()
            ->latest('recorded_at')
            ->first();

        if (!$reading) {
            return response()->json(['reading' => null]);
        }

        return response()->json([
            'reading' => [
                'temperature'   => $reading->temperature,
                'humidity'      => $reading->humidity,
                'soil_humidity' => $reading->soil_humidity,
                'luminosity'    => $reading->luminosity,
                'recorded_at'   => $reading->recorded_at->toDateTimeString(),
            ]
        ]);
    }
}
