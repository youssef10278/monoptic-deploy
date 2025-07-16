<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

// Routes de health check pour Railway
Route::get('/health', function () {
    return response('OK', 200)->header('Content-Type', 'text/plain');
});

Route::get('/api/health', function () {
    return response()->json([
        'status' => 'ok',
        'app' => 'monoptic',
        'port' => env('PORT', 80),
        'env' => env('APP_ENV'),
        'debug' => env('APP_DEBUG'),
        'url' => env('APP_URL'),
        'db' => env('DB_CONNECTION'),
        'timestamp' => now()
    ]);
});

// Routes de debug - SEULEMENT en développement
if (env('APP_ENV') !== 'production') {
    Route::get('/debug', function () {
        try {
            return response()->json([
                'status' => 'ok',
                'laravel_version' => app()->version(),
                'php_version' => PHP_VERSION,
                'app_env' => env('APP_ENV'),
                'routes_cached' => app()->routesAreCached(),
                'config_cached' => app()->configurationIsCached(),
                'timestamp' => now()->toISOString(),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    });

    Route::get('/test-db', function () {
        try {
            DB::connection()->getPdo();
            $tables = DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public'");
            return response()->json([
                'database' => 'connected',
                'connection' => config('database.default'),
                'tables_count' => count($tables),
                'timestamp' => now()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'database' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    });
}

// Route principale - Interface Monoptic
Route::get('/', function () {
    return view('app');
});

// Route pour servir les assets Vite
Route::get('/build/assets/{file}', function ($file) {
    $path = public_path("build/assets/{$file}");

    if (!file_exists($path)) {
        abort(404);
    }

    $extension = pathinfo($file, PATHINFO_EXTENSION);
    $mimeTypes = [
        'js' => 'application/javascript',
        'mjs' => 'application/javascript',
        'css' => 'text/css',
        'map' => 'application/json'
    ];

    $mimeType = $mimeTypes[$extension] ?? 'text/plain';

    return response()->file($path, [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'public, max-age=31536000'
    ]);
})->where('file', '.*');

// Route pour servir l'application SPA Vue.js (exclut /build)
Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!build).*');
