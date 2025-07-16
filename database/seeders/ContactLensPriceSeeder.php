<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ContactLensPrice;
use App\Models\Tenant;

class ContactLensPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer le tenant de test
        $tenant = Tenant::where('name', 'Optique Test Refactor')->first();

        if (!$tenant) {
            $this->command->error('Tenant "Optique Test Refactor" non trouvé');
            return;
        }

        $prices = [
            // Lentilles sphériques
            [
                'tenant_id' => $tenant->id,
                'lens_type' => 'spherique',
                'duration' => 'journaliere',
                'box_size' => '30',
                'base_price' => 25.00,
                'complexity_multiplier' => 1.00,
                'description' => 'Lentilles sphériques journalières - boîte de 30'
            ],
            [
                'tenant_id' => $tenant->id,
                'lens_type' => 'spherique',
                'duration' => 'mensuelle',
                'box_size' => '6',
                'base_price' => 30.00,
                'complexity_multiplier' => 1.00,
                'description' => 'Lentilles sphériques mensuelles - boîte de 6'
            ],

            // Lentilles toriques
            [
                'tenant_id' => $tenant->id,
                'lens_type' => 'torique',
                'duration' => 'journaliere',
                'box_size' => '30',
                'base_price' => 35.00,
                'complexity_multiplier' => 1.20,
                'description' => 'Lentilles toriques journalières - boîte de 30'
            ],
            [
                'tenant_id' => $tenant->id,
                'lens_type' => 'torique',
                'duration' => 'mensuelle',
                'box_size' => '6',
                'base_price' => 40.00,
                'complexity_multiplier' => 1.20,
                'description' => 'Lentilles toriques mensuelles - boîte de 6'
            ],

            // Lentilles multifocales
            [
                'tenant_id' => $tenant->id,
                'lens_type' => 'multifocale',
                'duration' => 'journaliere',
                'box_size' => '30',
                'base_price' => 45.00,
                'complexity_multiplier' => 1.30,
                'description' => 'Lentilles multifocales journalières - boîte de 30'
            ],
            [
                'tenant_id' => $tenant->id,
                'lens_type' => 'multifocale',
                'duration' => 'mensuelle',
                'box_size' => '6',
                'base_price' => 50.00,
                'complexity_multiplier' => 1.30,
                'description' => 'Lentilles multifocales mensuelles - boîte de 6'
            ],

            // Lentilles cosmétiques
            [
                'tenant_id' => $tenant->id,
                'lens_type' => 'cosmétique',
                'duration' => 'mensuelle',
                'box_size' => '2',
                'base_price' => 20.00,
                'complexity_multiplier' => 1.00,
                'description' => 'Lentilles cosmétiques mensuelles - paire'
            ],
        ];

        foreach ($prices as $price) {
            ContactLensPrice::create($price);
        }

        $this->command->info('Prix des lentilles de contact créés avec succès !');
    }
}
