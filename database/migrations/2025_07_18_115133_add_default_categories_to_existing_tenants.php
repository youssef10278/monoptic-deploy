<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Tenant;
use App\Models\ProductCategory;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Catégories par défaut requises
        $defaultCategories = [
            'Montures',
            'Accessoires'
        ];

        // Récupérer tous les tenants existants
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            foreach ($defaultCategories as $categoryName) {
                // Créer la catégorie si elle n'existe pas déjà
                ProductCategory::firstOrCreate([
                    'name' => $categoryName,
                    'tenant_id' => $tenant->id
                ], [
                    'name' => $categoryName,
                    'tenant_id' => $tenant->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        \Log::info('Migration - Catégories par défaut ajoutées pour tous les tenants existants', [
            'tenants_count' => $tenants->count(),
            'categories' => $defaultCategories
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ne pas supprimer les catégories lors du rollback
        // car elles peuvent être utilisées par des produits
    }
};
