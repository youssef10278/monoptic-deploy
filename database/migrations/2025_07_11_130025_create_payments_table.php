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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_id'); // Clé étrangère vers sales
            $table->decimal('amount', 10, 2); // Montant de ce paiement
            $table->string('payment_method'); // cash, card, transfer, check, etc.
            $table->uuid('tenant_id'); // Clé étrangère vers tenants
            $table->timestamps();

            // Définir les clés étrangères
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            // Index pour améliorer les performances
            $table->index(['sale_id']);
            $table->index(['tenant_id', 'created_at']);
            $table->index(['payment_method']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
