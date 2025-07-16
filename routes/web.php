<?php

use Illuminate\Support\Facades\Route;

// Route de health check pour Railway
Route::get('/health', function () {
    return response('OK', 200)->header('Content-Type', 'text/plain');
});

// Route alternative pour healthcheck
Route::get('/', function () {
    return response('Monoptic OK', 200)->header('Content-Type', 'text/plain');
});

// Route pour servir l'application SPA Vue.js
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
