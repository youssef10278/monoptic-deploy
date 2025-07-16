<?php

use Illuminate\Support\Facades\Route;

// Route pour servir l'application SPA Vue.js
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
