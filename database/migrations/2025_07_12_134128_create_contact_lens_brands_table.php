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
        Schema::create('contact_lens_brands', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id');
            $table->string('name')->unique(); // Nom de la marque (ex: "Acuvue", "Biofinity")
            $table->string('normalized_name')->index(); // Version normalisée pour la recherche
            $table->integer('usage_count')->default(1); // Nombre d'utilisations pour le tri
            $table->timestamp('last_used_at')->useCurrent(); // Dernière utilisation
            $table->timestamps();

            // Index pour la recherche rapide
            $table->index(['tenant_id', 'normalized_name']);
            $table->index(['tenant_id', 'usage_count']);

            // Contrainte tenant
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_lens_brands');
    }
};
