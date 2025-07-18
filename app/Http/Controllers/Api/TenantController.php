<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Models\ProductCategory;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TenantController extends Controller
{
    /**
     * Afficher la liste de tous les tenants
     */
    public function index(): JsonResponse
    {
        try {
            $tenants = Tenant::with(['users' => function ($query) {
                $query->where('role', UserRole::AdminMagasin);
            }])
            ->orderBy('created_at', 'desc')
            ->get();

            return response()->json([
                'success' => true,
                'data' => $tenants->map(function ($tenant) {
                    return [
                        'id' => $tenant->id,
                        'name' => $tenant->name,
                        'status' => $tenant->status,
                        'subscription_end_date' => $tenant->subscription_end_date,
                        'created_at' => $tenant->created_at,
                        'admin' => $tenant->users->first() ? [
                            'name' => $tenant->users->first()->name,
                            'email' => $tenant->users->first()->email,
                        ] : null,
                        'users_count' => $tenant->users->count(),
                    ];
                })
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des tenants',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Créer un nouveau tenant avec son administrateur
     */
    public function store(Request $request): JsonResponse
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'store_name' => 'required|string|max:255',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|string|min:6',
        ], [
            'store_name.required' => 'Le nom du magasin est requis',
            'admin_name.required' => 'Le nom de l\'administrateur est requis',
            'admin_email.required' => 'L\'email de l\'administrateur est requis',
            'admin_email.email' => 'L\'email doit être valide',
            'admin_email.unique' => 'Cet email est déjà utilisé',
            'admin_password.required' => 'Le mot de passe est requis',
            'admin_password.min' => 'Le mot de passe doit contenir au moins 6 caractères',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données de validation invalides',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Créer le tenant
            $tenant = Tenant::create([
                'name' => $request->store_name,
                'status' => 'trial', // Par défaut en période d'essai
                'subscription_end_date' => now()->addDays(30), // 30 jours d'essai
            ]);

            // Créer l'utilisateur administrateur du magasin
            $admin = User::create([
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'password' => Hash::make($request->admin_password),
                'role' => UserRole::AdminMagasin,
                'tenant_id' => $tenant->id,
            ]);

            // CRÉER AUTOMATIQUEMENT LES CATÉGORIES PAR DÉFAUT
            $this->createDefaultCategories($tenant->id);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tenant créé avec succès',
                'data' => [
                    'tenant' => [
                        'id' => $tenant->id,
                        'name' => $tenant->name,
                        'status' => $tenant->status,
                        'subscription_end_date' => $tenant->subscription_end_date,
                        'created_at' => $tenant->created_at,
                    ],
                    'admin' => [
                        'id' => $admin->id,
                        'name' => $admin->name,
                        'email' => $admin->email,
                        'role' => $admin->role,
                    ]
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du tenant',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher un tenant spécifique avec ses utilisateurs
     */
    public function show($id): JsonResponse
    {
        try {
            $tenant = Tenant::with(['users'])->find($id);

            if (!$tenant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant non trouvé'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $tenant
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération du tenant',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mettre à jour un tenant (gestion des abonnements)
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $user = auth()->user();

            // Trouver le tenant
            $tenant = Tenant::find($id);
            if (!$tenant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant non trouvé'
                ], 404);
            }

            // Validation des données
            $validator = Validator::make($request->all(), [
                'status' => 'sometimes|required|in:trial,active,suspended,cancelled',
                'subscription_end_date' => 'sometimes|nullable|date|after:today',
                'name' => 'sometimes|required|string|max:255',
            ], [
                'status.in' => 'Le statut doit être : trial, active, suspended ou cancelled',
                'subscription_end_date.after' => 'La date de fin d\'abonnement doit être dans le futur',
                'name.required' => 'Le nom du tenant est requis',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données de validation invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Mettre à jour les champs fournis
            if ($request->has('status')) {
                $tenant->status = $request->status;
            }

            if ($request->has('subscription_end_date')) {
                $tenant->subscription_end_date = $request->subscription_end_date;
            }

            if ($request->has('name')) {
                $tenant->name = $request->name;
            }

            $tenant->save();

            // Recharger le tenant avec les relations pour la réponse
            $tenant->load(['users']);
            $tenant->loadCount(['users', 'clients', 'products', 'sales']);

            return response()->json([
                'success' => true,
                'message' => 'Tenant mis à jour avec succès',
                'data' => $tenant
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du tenant',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Créer des catégories par défaut pour un nouveau tenant
     */
    private function createDefaultCategories($tenantId)
    {
        $defaultCategories = [
            'Montures',
            'Accessoires'
        ];

        foreach ($defaultCategories as $categoryName) {
            ProductCategory::firstOrCreate([
                'name' => $categoryName,
                'tenant_id' => $tenantId
            ], [
                'name' => $categoryName,
                'tenant_id' => $tenantId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        \Log::info('TenantController - Catégories par défaut créées pour nouveau tenant', [
            'tenant_id' => $tenantId,
            'categories' => $defaultCategories
        ]);
    }
}
