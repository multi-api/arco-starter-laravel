<?php

use App\Http\Controllers\DemoController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [DemoController::class, 'show'])->name('home');

// The demo page's endpoints are public on purpose; each one checks that the
// resource it exercises exists.
Route::prefix('demo')->group(function () {
    Route::post('job', [DemoController::class, 'dispatchJob']);
    Route::get('job', [DemoController::class, 'jobResult']);
    Route::post('files', [DemoController::class, 'upload']);
    Route::post('counter', [DemoController::class, 'counter']);
    Route::post('log', [DemoController::class, 'log']);
    Route::post('error', [DemoController::class, 'error']);
});

// Login, settings and the dashboard all need a database. Without one only
// the public pages exist, so these routes are not registered at all.
if (config('starter.database')) {
    Route::middleware(['auth'])->group(function () {
        Route::get('dashboard', function () {
            return Inertia::render('dashboard');
        })->name('dashboard');
    });

    require __DIR__.'/settings.php';
    require __DIR__.'/auth.php';
}
