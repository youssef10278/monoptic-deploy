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
        Schema::table('sale_items', function (Blueprint $table) {
            // Rendre product_id nullable (garder le type unsignedBigInteger)
            $table->unsignedBigInteger('product_id')->nullable()->change();

            // Ajouter description pour les articles sur mesure
            $table->text('description')->nullable()->after('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropColumn('description');
            // Note: Reverting product_id nullable change would require more complex logic
        });
    }
};
