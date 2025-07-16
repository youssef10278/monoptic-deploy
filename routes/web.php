<?php

use Illuminate\Support\Facades\Route;

// Routes de health check pour Railway
Route::get('/health', function () {
    return response('OK', 200)->header('Content-Type', 'text/plain');
});

Route::get('/api/health', function () {
    return response()->json([
        'status' => 'ok',
        'app' => 'monoptic',
        'port' => env('PORT', 80),
        'timestamp' => now()
    ]);
});

// Route principale
Route::get('/', function () {
    return response('Monoptic Application - Port: ' . env('PORT', 80), 200)
        ->header('Content-Type', 'text/plain');
});

// Route pour servir l'application SPA Vue.js
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
