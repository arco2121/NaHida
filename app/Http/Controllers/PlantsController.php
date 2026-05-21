<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlantRequest;
use App\Models\Plant;
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
            'title'  => 'Piante',
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
            'title' => $plant->plant_name,
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
}
