<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\WorkshopController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    Route::middleware('role:admin')->group(function () {
        Route::post('workshops', [WorkshopController::class, 'store'])->name('workshops.store');
        Route::put('workshops/{workshop}', [WorkshopController::class, 'update'])->name('workshops.update');
    });
});

require __DIR__.'/settings.php';
