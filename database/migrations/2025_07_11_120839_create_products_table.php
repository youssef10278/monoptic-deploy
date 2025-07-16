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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nom du produit
            $table->string('reference')->nullable(); // Référence du produit
            $table->decimal('purchase_price', 10, 2)->nullable(); // Prix d'achat
            $table->decimal('selling_price', 10, 2); // Prix de vente
            $table->integer('quantity')->default(0); // Quantité en stock
            $table->string('barcode')->nullable(); // Code-barres
            $table->unsignedBigInteger('product_category_id'); // Clé étrangère vers product_categories
            $table->uuid('tenant_id'); // Clé étrangère vers tenants
            $table->timestamps();

            // Définir les clés étrangères
            $table->foreign('product_category_id')->references('id')->on('product_categories')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            // Index pour améliorer les performances
            $table->index(['tenant_id', 'product_category_id']);
            $table->index(['tenant_id', 'name']);
            $table->index('barcode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
