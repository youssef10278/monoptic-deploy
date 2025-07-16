<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\User;
use App\Models\ProductCategory;
use App\Models\Product;
use App\Models\ContactLensBrand;
use App\Models\ContactLensPrice;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ProductionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un tenant de démonstration
        $tenant = Tenant::create([
            'id' => Str::uuid(),
            'name' => 'Optique Vision Plus',
            'status' => 'active',
            'subscription_end_date' => now()->addYear(),
        ]);

        // Créer un super administrateur
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@monoptic.com',
            'password' => Hash::make('password123'),
            'role' => UserRole::SuperAdmin,
            'tenant_id' => null,
        ]);

        // Créer un administrateur de magasin
        User::create([
            'name' => 'Admin Magasin',
            'email' => 'admin@optiquevision.ma',
            'password' => Hash::make('password123'),
            'role' => UserRole::AdminMagasin,
            'tenant_id' => $tenant->id,
        ]);

        // Créer un employé
        User::create([
            'name' => 'Employé Test',
            'email' => 'employe@optiquevision.ma',
            'password' => Hash::make('password123'),
            'role' => UserRole::Employe,
            'tenant_id' => $tenant->id,
        ]);

        // Créer des catégories de produits
        $categories = [
            ['name' => 'Montures', 'description' => 'Montures de lunettes'],
            ['name' => 'Verres', 'description' => 'Verres correcteurs'],
            ['name' => 'Lentilles', 'description' => 'Lentilles de contact'],
            ['name' => 'Accessoires', 'description' => 'Accessoires optiques'],
        ];

        foreach ($categories as $categoryData) {
            ProductCategory::create([
                'name' => $categoryData['name'],
                'description' => $categoryData['description'],
                'tenant_id' => $tenant->id,
            ]);
        }

        // Créer quelques produits de démonstration
        $montureCategory = ProductCategory::where('name', 'Montures')->first();
        $verreCategory = ProductCategory::where('name', 'Verres')->first();

        Product::create([
            'name' => 'Ray-Ban Aviator',
            'description' => 'Lunettes de soleil classiques',
            'price' => 150.00,
            'stock_quantity' => 10,
            'category_id' => $montureCategory->id,
            'tenant_id' => $tenant->id,
            'brand' => 'Ray-Ban',
        ]);

        Product::create([
            'name' => 'Verres progressifs',
            'description' => 'Verres progressifs haute qualité',
            'price' => 250.00,
            'stock_quantity' => 50,
            'category_id' => $verreCategory->id,
            'tenant_id' => $tenant->id,
            'brand' => 'Essilor',
        ]);

        // Créer des marques de lentilles
        $brands = [
            'Acuvue', 'Biofinity', 'Air Optix', 'Dailies', 'PureVision'
        ];

        foreach ($brands as $brand) {
            ContactLensBrand::create([
                'name' => $brand,
                'tenant_id' => $tenant->id,
            ]);
        }

        // Créer des prix de lentilles
        $acuvueBrand = ContactLensBrand::where('name', 'Acuvue')->first();
        
        ContactLensPrice::create([
            'brand_id' => $acuvueBrand->id,
            'type' => 'monthly',
            'price' => 45.00,
            'tenant_id' => $tenant->id,
        ]);

        ContactLensPrice::create([
            'brand_id' => $acuvueBrand->id,
            'type' => 'daily',
            'price' => 25.00,
            'tenant_id' => $tenant->id,
        ]);
    }
}
