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
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_id'); // Clé étrangère vers sales
            $table->unsignedBigInteger('product_id'); // Clé étrangère vers products
            $table->integer('quantity'); // Quantité vendue
            $table->decimal('price', 10, 2); // Prix au moment de la vente
            $table->timestamps();

            // Définir les clés étrangères
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            // Index pour améliorer les performances
            $table->index(['sale_id']);
            $table->index(['product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};
