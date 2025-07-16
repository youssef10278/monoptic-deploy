<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Vérifier et corriger la table users si nécessaire
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                // Ajouter tenant_id seulement s'il n'existe pas
                if (!Schema::hasColumn('users', 'tenant_id')) {
                    $table->uuid('tenant_id')->nullable()->after('email_verified_at');
                }
                
                // Ajouter role seulement s'il n'existe pas
                if (!Schema::hasColumn('users', 'role')) {
                    $table->string('role')->default('employe')->after('tenant_id');
                }
            });
            
            // Ajouter la clé étrangère seulement si elle n'existe pas
            if (Schema::hasTable('tenants') && !$this->foreignKeyExists('users', 'users_tenant_id_foreign')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('set null');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ne rien faire en rollback pour éviter les problèmes
    }
    
    /**
     * Vérifier si une clé étrangère existe
     */
    private function foreignKeyExists($table, $keyName)
    {
        $connection = Schema::getConnection();
        $dbName = $connection->getDatabaseName();
        
        if ($connection->getDriverName() === 'pgsql') {
            $query = "SELECT constraint_name FROM information_schema.table_constraints 
                     WHERE table_name = ? AND constraint_name = ? AND table_schema = 'public'";
            return $connection->select($query, [$table, $keyName]) ? true : false;
        }
        
        return false;
    }
};
