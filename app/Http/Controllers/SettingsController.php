<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlantRequest;
use App\Models\Plant;
use App\Models\WateringEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Overview di tutte le piante dell'utente.
     */
    public function index(Request $request): View
    {
        $user   = $request->user();
        return renderPage('settings', [
            'title'  => 'Settings',
            'user'   => $user
        ]);
    }
}
