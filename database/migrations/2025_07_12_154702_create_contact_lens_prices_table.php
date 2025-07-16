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
        Schema::create('contact_lens_prices', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id');
            $table->string('lens_type'); // spherique, torique, multifocale, cosmétique, rigide, sclerale
            $table->string('brand')->nullable(); // Marque spécifique (optionnel)
            $table->string('duration'); // journaliere, hebdomadaire, mensuelle, trimestrielle, annuelle
            $table->string('box_size'); // 6, 30, 90 lentilles
            $table->decimal('base_price', 8, 2); // Prix de base
            $table->decimal('complexity_multiplier', 4, 2)->default(1.00); // Multiplicateur selon complexité
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Index pour la recherche rapide
            $table->index(['tenant_id', 'lens_type', 'duration', 'box_size']);
            $table->index(['tenant_id', 'is_active']);

            // Contrainte de clé étrangère
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_lens_prices');
    }
};
