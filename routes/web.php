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

// Route de diagnostic pour déboguer les problèmes 502
Route::get('/debug', function () {
    try {
        return response()->json([
            'status' => 'ok',
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'undefined',
            'script_name' => $_SERVER['SCRIPT_NAME'] ?? 'undefined',
            'request_uri' => $_SERVER['REQUEST_URI'] ?? 'undefined',
            'server_port' => $_SERVER['SERVER_PORT'] ?? 'undefined',
            'app_env' => env('APP_ENV'),
            'app_debug' => env('APP_DEBUG'),
            'app_url' => env('APP_URL'),
            'cache_config' => config('cache.default'),
            'session_driver' => config('session.driver'),
            'database_connection' => config('database.default'),
            'routes_cached' => app()->routesAreCached(),
            'config_cached' => app()->configurationIsCached(),
            'timestamp' => now()->toISOString(),
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ], 500);
    }
});

// Route de test de base de données
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

// Route principale
Route::get('/', function () {
    return response('Monoptic Application - Port: ' . env('PORT', 80) . ' - Laravel ' . app()->version(), 200)
        ->header('Content-Type', 'text/plain');
});

// Route pour servir l'application SPA Vue.js
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
