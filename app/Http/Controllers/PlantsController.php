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
            'title' => "La mia pianta",
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
            'plant_variant'  => ['sometimes', 'nullable', 'integer', 'min:0', 'max:6'],
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
     * Storico annaffiature di una pianta (JSON).
     */
    public function history(Request $request, int $id): JsonResponse
    {
        $plant = Plant::where('user_id', $request->user()->user_id)->findOrFail($id);

        $events = $plant->wateringEvents()
            ->latest('watered_at')
            ->take(30)
            ->get()
            ->map(fn($ev) => [
                'source'           => $ev->source,
                'watered_at_human' => $ev->watered_at->locale('it')->diffForHumans(),
                'watered_at_date'  => $ev->watered_at->format('d/m/Y H:i'),
            ]);

        return response()->json(['events' => $events]);
    }
}
