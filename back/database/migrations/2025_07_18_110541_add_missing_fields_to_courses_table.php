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
        Schema::table('courses', function (Blueprint $table) {
            // Champs pour l'image de couverture
            $table->string('thumbnail')->nullable()->after('description');
            
            // Champ pour la durée du cours
            $table->string('duration')->nullable()->after('thumbnail');
            
            // Champ pour le nombre de chapitres
            $table->integer('chapters_count')->default(0)->after('duration');
            
            // Champ pour la note/évaluation
            $table->decimal('rating', 3, 2)->default(0.00)->after('chapters_count');
            
            // Champ pour le nombre d'étudiants
            $table->integer('students_count')->default(0)->after('rating');
            
            // Champ pour le prix du cours
            $table->decimal('price', 8, 2)->nullable()->after('students_count');
            
            // Champ pour le niveau de difficulté (beginner, intermediate, advanced)
            $table->string('difficulty_level')->default('beginner')->after('price');
            
            // Champ pour la catégorie (équivalent à theme mais plus spécifique)
            $table->string('category')->nullable()->after('difficulty_level');
            
            // Champ pour le niveau d'éducation (ecolier, collegien, lyceen, etudiant)
            $table->string('education_level')->nullable()->after('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'thumbnail',
                'duration',
                'chapters_count',
                'rating',
                'students_count',
                'price',
                'difficulty_level',
                'category',
                'education_level'
            ]);
        });
    }
}; 