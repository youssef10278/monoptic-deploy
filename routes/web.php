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

// Route de diagnostic pour les assets
Route::get('/debug/assets', function () {
    $buildPath = public_path('build');
    $assetsPath = public_path('build/assets');

    $info = [
        'timestamp' => now()->toDateTimeString(),
        'build_directory_exists' => is_dir($buildPath),
        'assets_directory_exists' => is_dir($assetsPath),
        'build_contents' => [],
        'assets_contents' => [],
        'file_tests' => [],
        'environment' => [
            'APP_ENV' => env('APP_ENV'),
            'APP_URL' => env('APP_URL'),
            'APP_DEBUG' => env('APP_DEBUG'),
        ]
    ];

    if (is_dir($buildPath)) {
        $info['build_contents'] = array_slice(scandir($buildPath), 2); // Remove . and ..
    }

    if (is_dir($assetsPath)) {
        $files = array_slice(scandir($assetsPath), 2);
        $info['assets_contents'] = $files;

        // Test first few files
        foreach (array_slice($files, 0, 3) as $file) {
            $filePath = $assetsPath . '/' . $file;
            $info['file_tests'][$file] = [
                'exists' => file_exists($filePath),
                'readable' => is_readable($filePath),
                'size' => file_exists($filePath) ? filesize($filePath) : 0,
                'mime_type' => file_exists($filePath) ? mime_content_type($filePath) : null,
                'permissions' => file_exists($filePath) ? substr(sprintf('%o', fileperms($filePath)), -4) : null,
            ];
        }
    }

    return response()->json($info, 200, [], JSON_PRETTY_PRINT);
});

Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
