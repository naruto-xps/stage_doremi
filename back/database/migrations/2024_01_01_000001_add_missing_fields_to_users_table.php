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
            
            // Modifier l'enum role pour inclure recruiter
            $table->enum('role', ['admin', 'teacher', 'student', 'recruiter', 'user'])->default('user')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['student_cycle', 'is_verified', 'phone']);
            $table->enum('role', ['admin', 'teacher', 'student', 'user'])->default('user')->change();
        });
    }
};
