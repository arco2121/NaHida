<?php

include_once 'functions.php';

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\PlantsController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => renderPage());
Route::get('/dashboard', [DashboardController::class, 'show'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/plants',       [PlantsController::class, 'index'])->middleware(['auth', 'verified'])->name('plants');
Route::get('/plants/{id}',  [PlantsController::class, 'show'])->middleware(['auth', 'verified'])->name('plants.show');
Route::get('/plants/create',  [PlantsController::class, 'create'])->middleware(['auth', 'verified'])->name('plants.create');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// --- Device / MQTT ---
// Toggle del led
Route::post('/device/toggle-led', [DeviceController::class, 'toggleLed']);

//Invio configurazioni in json
Route::post('/device/send-config', [DeviceController::class, 'sendConfig']);

// Controlla se il dispositivo è online
Route::get('/device/status', [DeviceController::class, 'getStatus']);

// TEST
Route::get('/test', fn() => renderPage("test"));

require __DIR__.'/auth.php';
