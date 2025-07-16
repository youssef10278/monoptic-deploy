<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductCategory;

class ContactLensesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer la catégorie lentilles ou la créer
        $category = ProductCategory::firstOrCreate([
            'name' => 'Lentilles de Contact',
            'tenant_id' => '0197fa96-04d3-7378-9f24-131fcfbbae99' // Tenant de test
        ]);

        // Lentilles Acuvue Oasys
        $acuvueVariations = [
            ['power' => -1.00, 'bc' => 8.4, 'diameter' => 14.0],
            ['power' => -1.25, 'bc' => 8.4, 'diameter' => 14.0],
            ['power' => -1.50, 'bc' => 8.4, 'diameter' => 14.0],
            ['power' => -2.00, 'bc' => 8.4, 'diameter' => 14.0],
            ['power' => -2.25, 'bc' => 8.4, 'diameter' => 14.0],
            ['power' => -2.50, 'bc' => 8.4, 'diameter' => 14.0],
            ['power' => -2.75, 'bc' => 8.4, 'diameter' => 14.0],
            ['power' => -3.00, 'bc' => 8.4, 'diameter' => 14.0],
            ['power' => -1.00, 'bc' => 8.8, 'diameter' => 14.0],
            ['power' => -1.25, 'bc' => 8.8, 'diameter' => 14.0],
            ['power' => -2.00, 'bc' => 8.8, 'diameter' => 14.0],
            ['power' => -2.75, 'bc' => 8.8, 'diameter' => 14.0],
        ];

        foreach ($acuvueVariations as $variation) {
            Product::create([
                'name' => 'Acuvue Oasys',
                'brand' => 'Johnson & Johnson',
                'reference' => 'AO-' . str_replace(['-', '.'], ['M', ''], $variation['power']) . '-' . str_replace('.', '', $variation['bc']),
                'selling_price' => 35.90,
                'quantity' => rand(5, 25),
                'product_category_id' => $category->id,
                'tenant_id' => '0197fa96-04d3-7378-9f24-131fcfbbae99',
                'type' => 'contact_lenses',
                'attributes' => json_encode($variation)
            ]);
        }

        // Lentilles Biofinity
        $biofinityVariations = [
            ['power' => -1.00, 'bc' => 8.6, 'diameter' => 14.0],
            ['power' => -1.50, 'bc' => 8.6, 'diameter' => 14.0],
            ['power' => -2.00, 'bc' => 8.6, 'diameter' => 14.0],
            ['power' => -2.50, 'bc' => 8.6, 'diameter' => 14.0],
            ['power' => -3.00, 'bc' => 8.6, 'diameter' => 14.0],
            ['power' => -1.25, 'bc' => 8.6, 'diameter' => 14.0],
            ['power' => -2.25, 'bc' => 8.6, 'diameter' => 14.0],
            ['power' => -2.75, 'bc' => 8.6, 'diameter' => 14.0],
        ];

        foreach ($biofinityVariations as $variation) {
            Product::create([
                'name' => 'Biofinity',
                'brand' => 'CooperVision',
                'reference' => 'BF-' . str_replace(['-', '.'], ['M', ''], $variation['power']) . '-' . str_replace('.', '', $variation['bc']),
                'selling_price' => 28.50,
                'quantity' => rand(3, 20),
                'product_category_id' => $category->id,
                'tenant_id' => '0197fa96-04d3-7378-9f24-131fcfbbae99',
                'type' => 'contact_lenses',
                'attributes' => json_encode($variation)
            ]);
        }

        // Lentilles Dailies Total1
        $dailiesVariations = [
            ['power' => -1.00, 'bc' => 8.5, 'diameter' => 14.1],
            ['power' => -1.25, 'bc' => 8.5, 'diameter' => 14.1],
            ['power' => -1.50, 'bc' => 8.5, 'diameter' => 14.1],
            ['power' => -2.00, 'bc' => 8.5, 'diameter' => 14.1],
            ['power' => -2.25, 'bc' => 8.5, 'diameter' => 14.1],
            ['power' => -2.50, 'bc' => 8.5, 'diameter' => 14.1],
        ];

        foreach ($dailiesVariations as $variation) {
            Product::create([
                'name' => 'Dailies Total1',
                'brand' => 'Alcon',
                'reference' => 'DT1-' . str_replace(['-', '.'], ['M', ''], $variation['power']) . '-' . str_replace('.', '', $variation['bc']),
                'selling_price' => 42.90,
                'quantity' => rand(8, 30),
                'product_category_id' => $category->id,
                'tenant_id' => '0197fa96-04d3-7378-9f24-131fcfbbae99',
                'type' => 'contact_lenses',
                'attributes' => json_encode($variation)
            ]);
        }
    }
}
