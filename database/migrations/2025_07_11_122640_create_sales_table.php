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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->decimal('total_amount', 10, 2); // Montant total de la vente
            $table->unsignedBigInteger('client_id'); // Clé étrangère vers clients
            $table->unsignedBigInteger('user_id'); // Clé étrangère vers users (employé qui a fait la vente)
            $table->uuid('tenant_id'); // Clé étrangère vers tenants
            $table->timestamps();

            // Définir les clés étrangères
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            // Index pour améliorer les performances
            $table->index(['tenant_id', 'created_at']);
            $table->index(['client_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
