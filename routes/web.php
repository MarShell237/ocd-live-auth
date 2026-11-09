<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ModeController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::resource('modes', ModeController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    Route::resource('packages', PackageController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    Route::patch('/reservations/{reservation}/confirm', [ReservationController::class, 'confirm'])->name('reservations.confirm');
    Route::patch('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');
});

require __DIR__.'/settings.php';
