<?php

include_once 'functions.php';

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\PlantsController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => renderPage());
Route::get('/dashboard', [DashboardController::class, 'show'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/plants',          [PlantsController::class, 'index'])->name('plants.index');
    Route::get('/plants/create',   [PlantsController::class, 'create'])->name('plants.create');
    Route::get('/plants/{id}',     [PlantsController::class, 'show'])->name('plants.show');
    Route::post('/plants/store',   [PlantsController::class, 'store'])->name('plants.store');
    Route::patch('/plants/{id}',   [PlantsController::class, 'update'])->name('plants.update');
    Route::post('/plants/{id}/water', [PlantsController::class, 'water'])->name('plants.water');

    // Device management
    Route::post('/plants/{id}/device',   [DeviceController::class, 'linkDevice'])->name('plants.device.link');
    Route::delete('/plants/{id}/device', [DeviceController::class, 'unlinkDevice'])->name('plants.device.unlink');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// --- Device / MQTT ---
Route::post('/device/toggle-led',  [DeviceController::class, 'toggleLed']);
Route::post('/device/send-config', [DeviceController::class, 'sendConfig']);
Route::get('/device/status',       [DeviceController::class, 'getStatus']);

// TEST
Route::get('/test', fn() => renderPage("test"));

require __DIR__.'/auth.php';
