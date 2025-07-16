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
        Schema::dropIfExists('prescriptions');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recréer la table si nécessaire (optionnel)
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->uuid('client_id');
            $table->string('prescriber');
            $table->date('prescription_date');
            $table->decimal('od_sph', 4, 2)->nullable();
            $table->decimal('od_cyl', 4, 2)->nullable();
            $table->integer('od_axe')->nullable();
            $table->decimal('od_add', 3, 2)->nullable();
            $table->decimal('og_sph', 4, 2)->nullable();
            $table->decimal('og_cyl', 4, 2)->nullable();
            $table->integer('og_axe')->nullable();
            $table->decimal('og_add', 3, 2)->nullable();
            $table->decimal('pupillary_distance_r', 4, 1)->nullable();
            $table->decimal('pupillary_distance_l', 4, 1)->nullable();
            $table->string('scan_path')->nullable();
            $table->uuid('tenant_id');
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }
};
