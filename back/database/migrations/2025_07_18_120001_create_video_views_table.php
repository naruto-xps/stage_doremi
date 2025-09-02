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
        Schema::create('video_views', function (Blueprint $table) {
            $table->id();
            
            // Relations
            $table->foreignId('video_id')->constrained('videos')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Informations de tracking
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('viewed_at');
            
            $table->timestamps();
            
            // Index pour les performances
            $table->index(['video_id', 'viewed_at']);
            $table->index(['user_id', 'viewed_at']);
            $table->index('ip_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_views');
    }
};
