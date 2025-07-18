<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProductCategory;
use App\Models\Tenant;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer tous les tenants existants
        $tenants = Tenant::all();

        // Catégories par défaut pour chaque tenant
        $defaultCategories = [
            'Montures',
            'Verres',
            'Lentilles de contact',
            'Accessoires'
        ];

        foreach ($tenants as $tenant) {
            foreach ($defaultCategories as $categoryName) {
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

            $this->command->info("Catégories créées pour le tenant: {$tenant->name} (ID: {$tenant->id})");
        }

        $this->command->info('Toutes les catégories par défaut ont été créées !');
    }
}
