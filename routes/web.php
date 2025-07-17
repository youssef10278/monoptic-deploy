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

// Route principale - Landing Page MONOPTI
Route::get('/', [App\Http\Controllers\LandingController::class, 'index'])->name('landing');

// Routes pour la landing page
Route::get('/demo', [App\Http\Controllers\LandingController::class, 'requestDemo'])->name('demo.request');
Route::post('/contact', [App\Http\Controllers\LandingController::class, 'contact'])->name('contact');

// Route pour l'application - Interface Monoptic
Route::get('/app', function () {
    return view('app');
})->name('app');

// Route de diagnostic pour les utilisateurs
Route::get('/debug/users', function () {
    $users = \App\Models\User::all(['id', 'name', 'email', 'role', 'created_at']);
    $tenants = \App\Models\Tenant::all(['id', 'name', 'status', 'created_at']);

    return response()->json([
        'timestamp' => now()->toDateTimeString(),
        'users_count' => $users->count(),
        'tenants_count' => $tenants->count(),
        'users' => $users,
        'tenants' => $tenants,
        'database_tables' => \DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'") ?:
                            \DB::select("SELECT table_name as name FROM information_schema.tables WHERE table_schema = 'public'"),
    ], 200, [], JSON_PRETTY_PRINT);
});

// Route d'urgence pour créer un admin
Route::get('/debug/create-admin', function () {
    try {
        // Vérifier si un super admin existe déjà
        $existingAdmin = \App\Models\User::where('role', \App\Enums\UserRole::SuperAdmin)->first();

        if ($existingAdmin) {
            return response()->json([
                'status' => 'exists',
                'message' => 'Super admin already exists',
                'admin' => [
                    'email' => $existingAdmin->email,
                    'name' => $existingAdmin->name,
                    'created_at' => $existingAdmin->created_at
                ]
            ], 200, [], JSON_PRETTY_PRINT);
        }

        // Créer un super admin
        $admin = \App\Models\User::create([
            'name' => 'Super Admin',
            'email' => 'admin@monoptic.com',
            'password' => \Hash::make('password123'),
            'role' => \App\Enums\UserRole::SuperAdmin,
            'tenant_id' => null,
        ]);

        return response()->json([
            'status' => 'created',
            'message' => 'Super admin created successfully',
            'admin' => [
                'email' => $admin->email,
                'password' => 'password123',
                'name' => $admin->name
            ]
        ], 200, [], JSON_PRETTY_PRINT);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500, [], JSON_PRETTY_PRINT);
    }
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

// Routes pour l'authentification et l'application
Route::middleware(['web'])->group(function () {
    // Routes d'authentification
    Route::get('/login', function () {
        return view('app');
    })->name('login');

    Route::get('/register', function () {
        return view('app');
    })->name('register');

    // Toutes les autres routes de l'application
    Route::get('/{any}', function () {
        return view('app');
    })->where('any', '^(?!api).*$'); // Exclut les routes API
});
