<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\User;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer un utilisateur formateur
        $teacher = User::where('role', 'teacher')->first();
        
        if (!$teacher) {
            // Créer un formateur si aucun n'existe
            $teacher = User::create([
                'name' => 'Professeur',
                'surname' => 'Dupont',
                'email' => 'professeur@doremi.edu',
                'password' => bcrypt('password'),
                'role' => 'teacher',
                'is_verified' => true,
            ]);
        }

        $courses = [
            [
                'title' => 'Mathématiques CM2',
                'description' => 'Révisions et exercices pour les élèves de CM2 - Fractions, géométrie et calculs',
                'thumbnail' => 'https://images.unsplash.com/photo-1635070041078-e363dbe005cb?w=800&h=400&fit=crop&crop=center',
                'duration' => '3h 20min',
                'chapters_count' => 10,
                'rating' => 4.3,
                'students_count' => 312,
                'price' => null,
                'theme' => 'Mathématiques',
                'level' => 'CM2',
                'difficulty_level' => 'beginner',
                'category' => 'Mathématiques',
                'education_level' => 'ecolier',
                'is_premium' => false,
                'school' => 'École Primaire DoReMi',
                'teacher_id' => $teacher->id,
            ],
            [
                'title' => 'SVT - Sciences de la Vie et de la Terre',
                'description' => 'Comprendre le corps humain, la reproduction et l\'environnement pour les collégiens',
                'thumbnail' => 'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?w=800&h=400&fit=crop&crop=center',
                'duration' => '5h 30min',
                'chapters_count' => 15,
                'rating' => 4.9,
                'students_count' => 198,
                'price' => 39.99,
                'theme' => 'Sciences',
                'level' => '4ème',
                'difficulty_level' => 'intermediate',
                'category' => 'SVT',
                'education_level' => 'collegien',
                'is_premium' => true,
                'school' => 'Collège DoReMi',
                'teacher_id' => $teacher->id,
            ],
            [
                'title' => 'Philosophie Terminale',
                'description' => 'Préparation au BAC - Les grands thèmes philosophiques et méthodologie',
                'thumbnail' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=800&h=400&fit=crop&crop=center',
                'duration' => '8h 45min',
                'chapters_count' => 20,
                'rating' => 4.7,
                'students_count' => 156,
                'price' => 59.99,
                'theme' => 'Philosophie',
                'level' => 'Terminale',
                'difficulty_level' => 'advanced',
                'category' => 'Philosophie',
                'education_level' => 'lyceen',
                'is_premium' => true,
                'school' => 'Lycée DoReMi',
                'teacher_id' => $teacher->id,
            ],
            [
                'title' => 'Introduction à la comptabilité',
                'description' => 'Maîtrisez les principes fondamentaux de la comptabilité générale',
                'thumbnail' => 'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?w=800&h=400&fit=crop&crop=center',
                'duration' => '4h 15min',
                'chapters_count' => 12,
                'rating' => 4.6,
                'students_count' => 156,
                'price' => 79.99,
                'theme' => 'Comptabilité',
                'level' => 'Licence',
                'difficulty_level' => 'intermediate',
                'category' => 'Comptabilité',
                'education_level' => 'etudiant',
                'is_premium' => true,
                'school' => 'Université DoReMi',
                'teacher_id' => $teacher->id,
            ],
        ];

        foreach ($courses as $courseData) {
            Course::create($courseData);
        }
    }
} 