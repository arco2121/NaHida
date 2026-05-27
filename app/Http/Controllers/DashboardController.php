<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Plant;
use App\Models\SensorReading;
use App\Models\WateringEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function show(Request $request): View
    {
        $user = $request->user();
        $userId = $user->user_id;

        $attentionPlants = Plant::where('user_id', $userId)
            ->whereHas('sensorReadings', function ($query) {
                $query->whereRaw('sensor_readings.recorded_at = (SELECT MAX(sr.recorded_at) FROM sensor_readings sr WHERE sr.plant_id = plants.plant_id)')
                    ->where(function ($q) {
                        $q->whereColumn('sensor_readings.humidity', '<', 'plants.hum_min')
                            ->orWhereColumn('sensor_readings.humidity', '>', 'plants.hum_max')
                            ->orWhereColumn('sensor_readings.temperature', '<', 'plants.temp_min')
                            ->orWhereColumn('sensor_readings.temperature', '>', 'plants.temp_max')
                            ->orWhereColumn('sensor_readings.soil_humidity', '<', 'plants.soil_hum_min')
                            ->orWhereColumn('sensor_readings.soil_humidity', '>', 'plants.soil_hum_max');
                    });
            })->get();

        $nextWaterings = Plant::where('user_id', $userId)
    ->with(['wateringEvents' => fn($q) => $q->latest('watered_at')->limit(1)])
    ->get()
    ->sortBy(function ($plant) {
        $lastWatering = $plant->wateringEvents->first();
        $base = $lastWatering ? $lastWatering->watered_at : $plant->created_at;
        return $base->addHours($plant->watering_cycle);
    })
    ->take(3);

        $attentionIds = $attentionPlants->pluck('plant_id')->toArray();
        $yourPlants = collect($attentionPlants)->take(3);

        if ($yourPlants->count() < 3) {
            $needed = 3 - $yourPlants->count();

            $randomPlants = Plant::where('user_id', $userId)
                ->whereNotIn('plant_id', $attentionIds)
                ->inRandomOrder()
                ->take($needed)
                ->get();

            $yourPlants = $yourPlants->merge($randomPlants);
        }

        $activities = collect();

        WateringEvent::whereHas('plant', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })
            ->with('plant')
            ->latest('watered_at')
            ->take(4)
            ->get()
            ->each(function ($event) use ($activities) {
                $activities->push([
                    'type' => 'watering',
                    'message' => "La pianta '{$event->plant->plant_name}' è stata annaffiata.",
                    'date' => $event->watered_at,
                ]);
            });

        Plant::where('user_id', $userId)
            ->latest('created_at')
            ->take(4)
            ->get()
            ->each(function ($plant) use ($activities) {
                $activities->push([
                    'type' => 'creation',
                    'message' => "Nuova pianta registrata nel sistema: '{$plant->plant_name}'.",
                    'date' => $plant->created_at,
                ]);
            });

        SensorReading::whereHas('plant', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })
            ->join('plants', 'sensor_readings.plant_id', '=', 'plants.plant_id')
            ->where(function ($q) {
                $q->whereColumn('sensor_readings.humidity', '<', 'plants.hum_min')
                    ->orWhereColumn('sensor_readings.humidity', '>', 'plants.hum_max')
                    ->orWhereColumn('sensor_readings.temperature', '<', 'plants.temp_min')
                    ->orWhereColumn('sensor_readings.temperature', '>', 'plants.temp_max')
                    ->orWhereColumn('sensor_readings.soil_humidity', '<', 'plants.soil_hum_min')
                    ->orWhereColumn('sensor_readings.soil_humidity', '>', 'plants.soil_hum_max');
            })
            ->select('sensor_readings.*')
            ->latest('sensor_readings.recorded_at')
            ->take(4)
            ->get()
            ->each(function ($reading) use ($activities) {
                $activities->push([
                    'type' => 'warning',
                    'message' => "Attenzione! I parametri di '{$reading->plant->plant_name}' sono fuori range ottimale.",
                    'date' => $reading->recorded_at,
                ]);
            });

        $recentActivity = $activities->sortByDesc('date')->take(4)->values();

        return renderPage('dashboard', [
            'title' => 'Dashboard',
            'user' => $user,
            'attentionPlants' => $attentionPlants,
            'nextWaterings' => $nextWaterings,
            'recentActivity' => $recentActivity,
            'yourPlants' => $yourPlants,
        ]);
    }
}
