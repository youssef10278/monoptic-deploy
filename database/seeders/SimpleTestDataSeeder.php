<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SimpleTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer le tenant de test
        $tenant = DB::table('tenants')->where('name', 'Optique Test')->first();
        
        if (!$tenant) {
            $this->command->error('Tenant "Optique Test" non trouvé.');
            return;
        }

        // Récupérer l'utilisateur admin
        $user = DB::table('users')->where('tenant_id', $tenant->id)->first();
        
        if (!$user) {
            $this->command->error('Utilisateur admin non trouvé pour ce tenant.');
            return;
        }

        $this->command->info('Création des données de test pour le tenant: ' . $tenant->name);

        // Créer des catégories de produits
        $categories = [
            ['name' => 'Montures', 'tenant_id' => $tenant->id, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Verres', 'tenant_id' => $tenant->id, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Lentilles', 'tenant_id' => $tenant->id, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Accessoires', 'tenant_id' => $tenant->id, 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($categories as $category) {
            DB::table('product_categories')->insertOrIgnore($category);
        }

        $this->command->info('Catégories créées.');

        // Récupérer les IDs des catégories
        $montureCategory = DB::table('product_categories')->where('name', 'Montures')->where('tenant_id', $tenant->id)->first();
        $verreCategory = DB::table('product_categories')->where('name', 'Verres')->where('tenant_id', $tenant->id)->first();
        $lentilleCategory = DB::table('product_categories')->where('name', 'Lentilles')->where('tenant_id', $tenant->id)->first();

        // Créer des produits
        $products = [
            [
                'name' => 'Ray-Ban Aviator',
                'brand' => 'Ray-Ban',
                'selling_price' => 150.00,
                'purchase_price' => 75.00,
                'quantity' => 25,
                'product_category_id' => $montureCategory->id,
                'tenant_id' => $tenant->id,
                'type' => 'frame',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Oakley Sport',
                'brand' => 'Oakley',
                'selling_price' => 200.00,
                'purchase_price' => 100.00,
                'quantity' => 15,
                'product_category_id' => $montureCategory->id,
                'tenant_id' => $tenant->id,
                'type' => 'frame',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Verres progressifs',
                'brand' => 'Essilor',
                'selling_price' => 300.00,
                'purchase_price' => 150.00,
                'quantity' => 50,
                'product_category_id' => $verreCategory->id,
                'tenant_id' => $tenant->id,
                'type' => 'lens',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Lentilles mensuelles',
                'brand' => 'Acuvue',
                'selling_price' => 45.00,
                'purchase_price' => 20.00,
                'quantity' => 100,
                'product_category_id' => $lentilleCategory->id,
                'tenant_id' => $tenant->id,
                'type' => 'contact_lens',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];

        foreach ($products as $product) {
            DB::table('products')->insertOrIgnore($product);
        }

        $this->command->info('Produits créés.');

        // Créer des clients
        $clients = [
            [
                'name' => 'Jean Dupont',
                'email' => 'jean.dupont@email.com',
                'phone' => '0123456789',
                'address' => '123 Rue de la Paix, Paris',
                'tenant_id' => $tenant->id,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Marie Martin',
                'email' => 'marie.martin@email.com',
                'phone' => '0987654321',
                'address' => '456 Avenue des Champs, Lyon',
                'tenant_id' => $tenant->id,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Pierre Durand',
                'email' => 'pierre.durand@email.com',
                'phone' => '0147258369',
                'address' => '789 Boulevard Saint-Germain, Marseille',
                'tenant_id' => $tenant->id,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];

        foreach ($clients as $client) {
            DB::table('clients')->insertOrIgnore($client);
        }

        $this->command->info('Clients créés.');

        // Récupérer les clients et produits créés
        $clientsData = DB::table('clients')->where('tenant_id', $tenant->id)->get();
        $productsData = DB::table('products')->where('tenant_id', $tenant->id)->get();

        // Créer des ventes
        $sales = [
            [
                'total_amount' => 350.00,
                'paid_amount' => 350.00,
                'client_id' => $clientsData[0]->id,
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
                'status' => 'livre',
                'type' => 'vente_directe',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'total_amount' => 245.00,
                'paid_amount' => 100.00,
                'client_id' => $clientsData[1]->id,
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
                'status' => 'en_commande',
                'type' => 'dossier_lunettes',
                'created_at' => Carbon::yesterday(),
                'updated_at' => Carbon::yesterday()
            ],
            [
                'total_amount' => 150.00,
                'paid_amount' => 150.00,
                'client_id' => $clientsData[2]->id,
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
                'status' => 'livre',
                'type' => 'vente_directe',
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(5)
            ],
        ];

        foreach ($sales as $sale) {
            $saleId = DB::table('sales')->insertGetId($sale);
            
            // Créer les items de vente correspondants
            if ($saleId == 1) { // Première vente
                DB::table('sale_items')->insert([
                    ['sale_id' => $saleId, 'product_id' => $productsData[0]->id, 'quantity' => 1, 'price' => 150.00, 'created_at' => now(), 'updated_at' => now()],
                    ['sale_id' => $saleId, 'product_id' => $productsData[2]->id, 'quantity' => 1, 'price' => 200.00, 'created_at' => now(), 'updated_at' => now()],
                ]);
            } elseif ($saleId == 2) { // Deuxième vente
                DB::table('sale_items')->insert([
                    ['sale_id' => $saleId, 'product_id' => $productsData[1]->id, 'quantity' => 1, 'price' => 200.00, 'created_at' => Carbon::yesterday(), 'updated_at' => Carbon::yesterday()],
                    ['sale_id' => $saleId, 'product_id' => $productsData[3]->id, 'quantity' => 1, 'price' => 45.00, 'created_at' => Carbon::yesterday(), 'updated_at' => Carbon::yesterday()],
                ]);
            } else { // Troisième vente
                DB::table('sale_items')->insert([
                    ['sale_id' => $saleId, 'product_id' => $productsData[0]->id, 'quantity' => 1, 'price' => 150.00, 'created_at' => Carbon::now()->subDays(5), 'updated_at' => Carbon::now()->subDays(5)],
                ]);
            }
        }

        $this->command->info('Ventes créées.');
        $this->command->info('Données de test créées avec succès !');
    }
}
