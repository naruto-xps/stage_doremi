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
        Schema::create('document_ratings', function (Blueprint $table) {
            $table->id();
            
            // Relations
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Note et commentaire
            $table->integer('rating'); // 1 à 5 étoiles
            $table->text('comment')->nullable();
            
            $table->timestamps();
            
            // Contrainte unique pour éviter les doublons
            $table->unique(['document_id', 'user_id']);
            
            // Index pour les performances
            $table->index(['document_id', 'rating']);
            $table->index(['user_id', 'rating']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_ratings');
    }
};
