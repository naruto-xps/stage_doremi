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
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->enum('type', ['news', 'scholarship', 'announcement']);
            $table->boolean('is_published')->default(true);
            
            // Métadonnées de base
            $table->string('image')->nullable();
            $table->string('author')->nullable();
            $table->string('location')->nullable();
            $table->string('read_time')->nullable();
            
            // Gestion des priorités et statuts
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->enum('status', ['draft', 'published', 'archived'])->default('published');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_urgent')->default(false);
            
            // Métriques d'engagement
            $table->integer('views_count')->default(0);
            $table->integer('likes_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->integer('shares_count')->default(0);
            
            // Informations de publication
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('publish_schedule')->nullable();
            $table->foreignId('author_id')->nullable()->constrained('users');
            
            // Catégorisation avancée
            $table->string('category')->nullable();
            $table->string('subcategory')->nullable();
            $table->json('tags')->nullable();
            $table->json('target_audience')->nullable();
            
            // Contenu enrichi
            $table->text('excerpt')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->string('featured_image')->nullable();
            $table->json('gallery')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
