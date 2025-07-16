<?php

use Illuminate\Support\Facades\Route;

// Route de health check pour Railway
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'app' => config('app.name')
    ]);
});

// Route pour servir l'application SPA Vue.js
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
