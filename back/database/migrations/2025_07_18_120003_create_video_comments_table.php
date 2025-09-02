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
        Schema::create('video_comments', function (Blueprint $table) {
            $table->id();
            
            // Relations
            $table->foreignId('video_id')->constrained('videos')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Contenu
            $table->text('content');
            
            // Système de réponses (commentaires imbriqués)
            $table->foreignId('parent_id')->nullable()->constrained('video_comments')->onDelete('cascade');
            
            // Modération
            $table->boolean('is_approved')->default(true);
            
            $table->timestamps();
            
            // Index pour les performances
            $table->index(['video_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['parent_id', 'created_at']);
            $table->index('is_approved');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_comments');
    }
};
