<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\WorkshopController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    Route::get('workshops', [WorkshopController::class, 'index'])->name('workshops.index');
    Route::middleware('role:employee')->group(function () {
        Route::post('workshops/{workshop}/register', [RegistrationController::class, 'store'])->name('workshops.register');
    });

    Route::middleware('role:admin')->group(function () {
        Route::get('workshops/{workshop}', [WorkshopController::class, 'show'])->name('workshops.show');
        Route::post('workshops', [WorkshopController::class, 'store'])->name('workshops.store');
        Route::put('workshops/{workshop}', [WorkshopController::class, 'update'])->name('workshops.update');
        Route::delete('workshops/{workshop}', [WorkshopController::class, 'destroy'])->name('workshops.destroy');
    });
});

require __DIR__.'/settings.php';
