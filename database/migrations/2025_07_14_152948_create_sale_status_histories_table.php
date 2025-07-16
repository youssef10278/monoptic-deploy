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
        Schema::create('sale_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->onDelete('cascade');
            $table->string('old_status')->nullable(); // Ancien statut
            $table->string('new_status'); // Nouveau statut
            $table->text('comment')->nullable(); // Commentaire optionnel
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Utilisateur qui a fait le changement
            $table->timestamp('changed_at'); // Date du changement
            $table->timestamps();

            // Index pour optimiser les requêtes
            $table->index(['sale_id', 'changed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_status_histories');
    }
};
