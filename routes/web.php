<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

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
