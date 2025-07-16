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
            $table->string('type')->default('vente_directe')->after('client_id');

            // Modifier la colonne status pour supporter le nouveau cycle de vie
            $table->string('status')->default('devis')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('type');
            // Note: Reverting status column change would require more complex logic
        });
    }
};
