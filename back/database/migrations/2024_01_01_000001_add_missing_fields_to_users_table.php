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
        Schema::table('users', function (Blueprint $table) {
            // Ajouter les champs manquants
            $table->enum('student_cycle', ['lyceen', 'licence', 'master', 'doctorat'])->nullable();
            $table->boolean('is_verified')->default(false);
            $table->string('phone')->nullable();
            
            // Note: La modification de l'enum role sera gérée par une migration séparée
            // pour éviter les problèmes de compatibilité PostgreSQL
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['student_cycle', 'is_verified', 'phone']);
        });
    }
};
