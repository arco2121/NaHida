<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlantPreviewController;

Route::post('/plants/{id}/preview', [PlantPreviewController::class, 'store']);
