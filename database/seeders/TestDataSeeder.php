<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Tenant;
use App\Models\Client;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Enums\UserRole;
use Carbon\Carbon;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer le tenant de test
        $tenant = Tenant::where('name', 'Optique Test')->first();
        
        if (!$tenant) {
            $this->command->error('Tenant "Optique Test" non trouvé. Veuillez d\'abord créer un tenant.');
            return;
        }

        // Récupérer l'utilisateur admin
        $user = User::where('tenant_id', $tenant->id)->first();
        
        if (!$user) {
            $this->command->error('Utilisateur admin non trouvé pour ce tenant.');
            return;
        }

        $this->command->info('Création des données de test pour le tenant: ' . $tenant->name);

        // Créer des catégories de produits
        $categories = [
            ['name' => 'Montures'],
            ['name' => 'Verres'],
            ['name' => 'Lentilles'],
            ['name' => 'Accessoires'],
        ];

        foreach ($categories as $categoryData) {
            ProductCategory::firstOrCreate(
                ['name' => $categoryData['name'], 'tenant_id' => $tenant->id]
            );
        }

        $this->command->info('Catégories créées.');

        // Créer des produits
        $montureCategory = ProductCategory::where('name', 'Montures')->where('tenant_id', $tenant->id)->first();
        $verreCategory = ProductCategory::where('name', 'Verres')->where('tenant_id', $tenant->id)->first();
        $lentilleCategory = ProductCategory::where('name', 'Lentilles')->where('tenant_id', $tenant->id)->first();

        $products = [
            [
                'name' => 'Ray-Ban Aviator',
                'selling_price' => 150.00,
                'purchase_price' => 75.00,
                'quantity' => 25,
                'product_category_id' => $montureCategory->id,
                'brand' => 'Ray-Ban',
                'type' => 'frame'
            ],
            [
                'name' => 'Oakley Sport',
                'selling_price' => 200.00,
                'purchase_price' => 100.00,
                'quantity' => 15,
                'product_category_id' => $montureCategory->id,
                'brand' => 'Oakley',
                'type' => 'frame'
            ],
            [
                'name' => 'Verres progressifs',
                'selling_price' => 300.00,
                'purchase_price' => 150.00,
                'quantity' => 50,
                'product_category_id' => $verreCategory->id,
                'brand' => 'Essilor',
                'type' => 'lens'
            ],
            [
                'name' => 'Lentilles mensuelles',
                'selling_price' => 45.00,
                'purchase_price' => 20.00,
                'quantity' => 100,
                'product_category_id' => $lentilleCategory->id,
                'brand' => 'Acuvue',
                'type' => 'contact_lens'
            ],
        ];

        foreach ($products as $productData) {
            Product::firstOrCreate(
                ['name' => $productData['name'], 'tenant_id' => $tenant->id],
                array_merge($productData, ['tenant_id' => $tenant->id])
            );
        }

        $this->command->info('Produits créés.');

        // Créer des clients
        $clients = [
            [
                'name' => 'Jean Dupont',
                'email' => 'jean.dupont@email.com',
                'phone' => '0123456789',
                'address' => '123 Rue de la Paix, Paris',
                'credit_balance' => 0
            ],
            [
                'name' => 'Marie Martin',
                'email' => 'marie.martin@email.com',
                'phone' => '0987654321',
                'address' => '456 Avenue des Champs, Lyon',
                'credit_balance' => 150.00
            ],
            [
                'name' => 'Pierre Durand',
                'email' => 'pierre.durand@email.com',
                'phone' => '0147258369',
                'address' => '789 Boulevard Saint-Germain, Marseille',
                'credit_balance' => 0
            ],
        ];

        foreach ($clients as $clientData) {
            Client::firstOrCreate(
                ['email' => $clientData['email'], 'tenant_id' => $tenant->id],
                array_merge($clientData, ['tenant_id' => $tenant->id])
            );
        }

        $this->command->info('Clients créés.');

        // Créer des ventes
        $clients = Client::where('tenant_id', $tenant->id)->get();
        $products = Product::where('tenant_id', $tenant->id)->get();

        // Vente d'aujourd'hui
        $sale1 = Sale::create([
            'total_amount' => 350.00,
            'paid_amount' => 350.00,
            'client_id' => $clients[0]->id,
            'user_id' => $user->id,
            'tenant_id' => $tenant->id,
            'status' => 'livre',
            'type' => 'vente_directe',
            'created_at' => Carbon::now(),
        ]);

        SaleItem::create([
            'sale_id' => $sale1->id,
            'product_id' => $products[0]->id,
            'quantity' => 1,
            'unit_price' => 150.00,
            'total_price' => 150.00,
        ]);

        SaleItem::create([
            'sale_id' => $sale1->id,
            'product_id' => $products[2]->id,
            'quantity' => 1,
            'unit_price' => 200.00,
            'total_price' => 200.00,
        ]);

        // Vente d'hier
        $sale2 = Sale::create([
            'total_amount' => 245.00,
            'paid_amount' => 100.00,
            'client_id' => $clients[1]->id,
            'user_id' => $user->id,
            'tenant_id' => $tenant->id,
            'status' => 'en_commande',
            'type' => 'dossier_lunettes',
            'created_at' => Carbon::yesterday(),
        ]);

        SaleItem::create([
            'sale_id' => $sale2->id,
            'product_id' => $products[1]->id,
            'quantity' => 1,
            'unit_price' => 200.00,
            'total_price' => 200.00,
        ]);

        SaleItem::create([
            'sale_id' => $sale2->id,
            'product_id' => $products[3]->id,
            'quantity' => 1,
            'unit_price' => 45.00,
            'total_price' => 45.00,
        ]);

        // Vente de la semaine dernière
        $sale3 = Sale::create([
            'total_amount' => 150.00,
            'paid_amount' => 150.00,
            'client_id' => $clients[2]->id,
            'user_id' => $user->id,
            'tenant_id' => $tenant->id,
            'status' => 'livre',
            'type' => 'vente_directe',
            'created_at' => Carbon::now()->subDays(5),
        ]);

        SaleItem::create([
            'sale_id' => $sale3->id,
            'product_id' => $products[0]->id,
            'quantity' => 1,
            'unit_price' => 150.00,
            'total_price' => 150.00,
        ]);

        $this->command->info('Ventes créées.');
        $this->command->info('Données de test créées avec succès !');
    }
}
