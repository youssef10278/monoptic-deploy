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
        Schema::table('sales', function (Blueprint $table) {
            $table->string('status')->default('pending'); // pending, partially_paid, paid
            $table->decimal('paid_amount', 10, 2)->default(0); // Montant déjà payé

            // Index pour améliorer les performances des requêtes
            $table->index(['status']);
            $table->index(['tenant_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['tenant_id', 'status']);
            $table->dropColumn(['status', 'paid_amount']);
        });
    }
};
