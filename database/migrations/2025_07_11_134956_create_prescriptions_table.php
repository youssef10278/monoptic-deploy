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
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();

            // Informations générales
            $table->string('prescriber'); // Nom du médecin/prescripteur
            $table->date('prescription_date'); // Date de l'ordonnance

            // Mesures Œil Droit (OD)
            $table->decimal('od_sph', 5, 2)->nullable(); // Sphère OD
            $table->decimal('od_cyl', 5, 2)->nullable(); // Cylindre OD
            $table->integer('od_axe')->nullable(); // Axe OD (0-180°)
            $table->decimal('od_add', 4, 2)->nullable(); // Addition OD

            // Mesures Œil Gauche (OG)
            $table->decimal('og_sph', 5, 2)->nullable(); // Sphère OG
            $table->decimal('og_cyl', 5, 2)->nullable(); // Cylindre OG
            $table->integer('og_axe')->nullable(); // Axe OG (0-180°)
            $table->decimal('og_add', 4, 2)->nullable(); // Addition OG

            // Écarts pupillaires
            $table->decimal('pupillary_distance_l', 4, 1)->nullable(); // Distance pupillaire gauche
            $table->decimal('pupillary_distance_r', 4, 1)->nullable(); // Distance pupillaire droite

            // Fichier scan de l'ordonnance
            $table->string('scan_path')->nullable(); // Chemin du fichier scanné

            // Clés étrangères
            $table->unsignedBigInteger('client_id'); // Clé étrangère vers clients
            $table->uuid('tenant_id'); // Clé étrangère vers tenants

            $table->timestamps();

            // Définir les clés étrangères
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            // Index pour améliorer les performances
            $table->index(['client_id']);
            $table->index(['tenant_id', 'prescription_date']);
            $table->index(['prescription_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
