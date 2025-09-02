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
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            
            // Informations de base
            $table->string('title');
            $table->text('description');
            $table->string('file_url'); // URL du fichier vidéo
            $table->string('file_name'); // Nom original du fichier
            $table->bigInteger('file_size'); // Taille en bytes
            $table->integer('duration'); // Durée en secondes
            
            // Thumbnail et image
            $table->string('thumbnail')->nullable(); // URL de la miniature
            
            // Relations
            $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade');
            
            // Catégorisation
            $table->string('category'); // ex: "programmation", "design", "marketing"
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced']);
            
            // Monétisation
            $table->boolean('is_premium')->default(false);
            $table->decimal('price', 8, 2)->nullable(); // Prix en euros
            
            // Statistiques
            $table->integer('views')->default(0);
            $table->integer('likes')->default(0);
            $table->integer('dislikes')->default(0);
            
            // Statut et métadonnées
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->json('tags')->nullable(); // Tags pour la recherche
            
            $table->timestamps();
            
            // Index pour les performances
            $table->index(['instructor_id', 'status']);
            $table->index(['category', 'difficulty_level']);
            $table->index(['is_premium', 'status']);
            $table->index('views');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
