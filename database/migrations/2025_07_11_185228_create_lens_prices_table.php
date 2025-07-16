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
        Schema::create('lens_prices', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id');
            $table->string('type'); // unifocal, bifocal, progressif
            $table->string('material'); // organique, polycarbonate, haut_indice
            $table->string('treatment')->nullable(); // anti_reflet, anti_rayure, photochromique
            $table->decimal('price', 8, 2);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Clés étrangères
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            // Index pour améliorer les performances
            $table->index(['tenant_id', 'type']);
            $table->index(['tenant_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lens_prices');
    }
};
