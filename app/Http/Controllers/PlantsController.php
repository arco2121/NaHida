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

class PlantsController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function show(Request $request): View
    {
        $user = $request->user();

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
