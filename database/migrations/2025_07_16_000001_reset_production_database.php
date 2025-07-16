<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cette migration s'exécute seulement en production pour nettoyer
        if (app()->environment('production')) {
            
            // Supprimer toutes les tables existantes pour repartir à zéro
            $tables = [
                'sale_status_histories',
                'contact_lens_prices', 
                'contact_lens_brands',
                'payments',
                'sale_items',
                'sales',
                'products',
                'product_categories',
                'clients',
                'personal_access_tokens',
                'users',
                'tenants',
                'jobs',
                'cache',
                'migrations'
            ];
            
            foreach ($tables as $table) {
                if (Schema::hasTable($table)) {
                    Schema::dropIfExists($table);
                }
            }
            
            echo "Database cleaned for fresh deployment\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ne rien faire
    }
};
