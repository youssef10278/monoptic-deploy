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
        Schema::dropIfExists('lens_prices');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recréer la table si on veut revenir en arrière
        Schema::create('lens_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['unifocal', 'bifocal', 'progressif']);
            $table->enum('material', ['organique', 'polycarbonate', 'haut_indice']);
            $table->enum('treatment', ['anti_reflet', 'anti_rayure', 'photochromique', 'anti_lumiere_bleue'])->nullable();
            $table->decimal('price', 8, 2);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Index pour optimiser les recherches
            $table->index(['tenant_id', 'type', 'material', 'treatment']);
        });
    }
};
