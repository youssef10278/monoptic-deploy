<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductCategory;
use App\Models\Tenant;

class CreateDefaultCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monopti:create-categories {--tenant-id= : ID du tenant spécifique}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Créer les catégories de produits par défaut pour tous les tenants ou un tenant spécifique';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->option('tenant-id');
        
        // Catégories par défaut
        $defaultCategories = [
            'Montures',
            'Verres',
            'Lentilles de contact',
            'Accessoires'
        ];

        if ($tenantId) {
            // Créer pour un tenant spécifique
            $tenant = Tenant::find($tenantId);
            if (!$tenant) {
                $this->error("Tenant avec l'ID {$tenantId} non trouvé.");
                return 1;
            }
            
            $this->createCategoriesForTenant($tenant, $defaultCategories);
        } else {
            // Créer pour tous les tenants
            $tenants = Tenant::all();
            
            if ($tenants->isEmpty()) {
                $this->error('Aucun tenant trouvé dans la base de données.');
                return 1;
            }

            foreach ($tenants as $tenant) {
                $this->createCategoriesForTenant($tenant, $defaultCategories);
            }
        }

        $this->info('✅ Catégories créées avec succès !');
        return 0;
    }

    /**
     * Créer les catégories pour un tenant donné
     */
    private function createCategoriesForTenant($tenant, $categories)
    {
        $this->info("📁 Création des catégories pour: {$tenant->name} (ID: {$tenant->id})");
        
        foreach ($categories as $categoryName) {
            $category = ProductCategory::firstOrCreate([
                'name' => $categoryName,
                'tenant_id' => $tenant->id
            ], [
                'name' => $categoryName,
                'tenant_id' => $tenant->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            if ($category->wasRecentlyCreated) {
                $this->line("  ✅ Créé: {$categoryName}");
            } else {
                $this->line("  ⚠️  Existe déjà: {$categoryName}");
            }
        }
    }
}
