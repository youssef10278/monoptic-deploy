<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Api\TenantController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\ProductCategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ReportController;

use App\Http\Controllers\Api\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\Api\SuperAdmin\UserController as SuperAdminUserController;
use App\Http\Controllers\Api\DashboardController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Routes publiques (sans authentification)
Route::post('/login', [LoginController::class, 'login']);

// Routes protégées (avec authentification Sanctum)
Route::middleware(['auth:sanctum'])->group(function () {
    // Route pour déconnexion
    Route::post('/logout', [LoginController::class, 'logout']);

    // Route pour récupérer les informations de l'utilisateur connecté
    Route::get('/user', function (Request $request) {
        $user = $request->user();
        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'tenant_id' => $user->tenant_id,
                    'tenant' => $user->tenant ? [
                        'id' => $user->tenant->id,
                        'name' => $user->tenant->name,
                        'status' => $user->tenant->status,
                    ] : null,
                ],
            ]
        ]);
    });

    // Routes pour la gestion des tenants (réservées aux Super Administrateurs)
    Route::get('/tenants', [TenantController::class, 'index']);
    Route::get('/tenants/{id}', [TenantController::class, 'show']);
    Route::post('/tenants', [TenantController::class, 'store']);
    Route::put('/tenants/{id}', [TenantController::class, 'update']);

    // Route pour le tableau de bord tenant
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Route pour le tableau de bord Super Admin
    Route::get('/super-admin/dashboard', [SuperAdminDashboardController::class, 'index']);

    // Route pour la gestion des utilisateurs (réservée aux Super Administrateurs)
    Route::put('/super-admin/users/{user}', [SuperAdminUserController::class, 'update']);

    // Route pour les clients à crédit (DOIT être avant la route resource clients)
    Route::get('clients/credits', [ClientController::class, 'credits']);

    // Route pour l'historique des ventes d'un client (DOIT être avant la route resource clients)
    Route::get('clients/{id}/sales', [ClientController::class, 'sales']);

    // Routes pour la gestion des clients (pour AdminMagasin et Employe)
    Route::apiResource('clients', ClientController::class);



    // Routes pour la gestion du stock (pour AdminMagasin et Employe)
    Route::apiResource('product-categories', ProductCategoryController::class);
    Route::apiResource('products', ProductController::class);

    // Route de debug pour les catégories
    Route::get('/debug/categories', function (Request $request) {
        $user = auth()->user();
        $categories = \App\Models\ProductCategory::where('tenant_id', $user->tenant_id)->get();

        return response()->json([
            'user_id' => $user->id,
            'tenant_id' => $user->tenant_id,
            'categories_count' => $categories->count(),
            'categories' => $categories->toArray()
        ]);
    });

    // Routes pour la gestion des ventes (pour AdminMagasin et Employe)
    Route::get('sales', [SaleController::class, 'index']);
    Route::get('sales/{sale}', [SaleController::class, 'show']);
    Route::post('sales', [SaleController::class, 'store']);

    // Routes pour les marques de lentilles de contact
    Route::get('contact-lens-brands/search', [App\Http\Controllers\Api\ContactLensBrandController::class, 'search']);
    Route::get('contact-lens-brands/popular', [App\Http\Controllers\Api\ContactLensBrandController::class, 'popular']);

    // Routes pour les prix de lentilles de contact
    Route::post('contact-lens-prices/calculate', [App\Http\Controllers\Api\ContactLensPriceController::class, 'calculatePrice']);

    // Routes pour la gestion des paiements (pour AdminMagasin et Employe)
    Route::get('payments', [PaymentController::class, 'index']);
    Route::post('payments', [PaymentController::class, 'store']);

    // Routes pour les rapports et analyses (pour AdminMagasin et Employe)
    Route::get('reports/sales', [ReportController::class, 'salesReport']);
    Route::get('reports/product-analysis', [ReportController::class, 'productAnalysis']);
    Route::get('reports/stock-value', [ReportController::class, 'stockValue']);



    // Route pour mettre à jour le statut d'une vente
    Route::put('sales/{sale}/status', [SaleController::class, 'updateStatus']);

    // Routes pour la configuration de l'opticien
    Route::get('optician/config', function () {
        // Retourner une configuration par défaut pour l'instant
        return response()->json([
            'name' => 'Optique Vision Plus',
            'slogan' => 'Votre Opticien de Confiance',
            'address' => [
                'street' => '123 Avenue Mohammed V',
                'city' => 'Casablanca',
                'postalCode' => '20000',
                'country' => 'Maroc'
            ],
            'contact' => [
                'phone' => '+212 522 123 456',
                'email' => 'contact@optiquevision.ma',
                'website' => 'www.optiquevision.ma'
            ],
            'legal' => [
                'rc' => 'RC 123456',
                'patente' => 'PAT 789012',
                'cnss' => 'CNSS 345678',
                'ice' => 'ICE 001234567890123'
            ],
            'logo' => [
                'url' => null,
                'showOnDocuments' => true
            ],
            'display' => [
                'showSlogan' => true,
                'showWebsite' => true,
                'showLegalInfo' => true,
                'headerStyle' => 'standard'
            ]
        ]);
    });

    Route::post('optician/config', function (Request $request) {
        // Pour l'instant, juste retourner succès
        // Plus tard, sauvegarder en base de données
        return response()->json(['success' => true, 'message' => 'Configuration sauvegardée']);
    });
});
