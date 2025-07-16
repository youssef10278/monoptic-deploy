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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nom du client
            $table->string('email')->nullable(); // Email du client (optionnel)
            $table->string('phone')->nullable(); // Téléphone du client (optionnel)
            $table->text('address')->nullable(); // Adresse du client (optionnel)
            $table->uuid('tenant_id'); // Clé étrangère vers tenants
            $table->timestamps();

            // Définir la clé étrangère
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            // Index pour améliorer les performances des requêtes multi-tenant
            $table->index(['tenant_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
