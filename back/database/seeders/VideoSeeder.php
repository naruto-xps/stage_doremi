<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Video;
use App\Models\User;

class VideoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer un instructeur existant
        $instructor = User::where('role', 'teacher')->first();
        
        if (!$instructor) {
            // Créer un instructeur si aucun n'existe
            $instructor = User::create([
                'name' => 'John',
                'surname' => 'Doe',
                'email' => 'john.doe@example.com',
                'password' => bcrypt('password'),
                'role' => 'teacher',
                'is_verified' => true
            ]);
        }

        // Créer des exemples de vidéos
        $videos = [
            [
                'title' => 'Introduction à Laravel 10',
                'description' => 'Apprenez les bases de Laravel 10, le framework PHP moderne et élégant.',
                'file_url' => 'https://example.com/videos/laravel-intro.mp4',
                'file_name' => 'laravel-intro.mp4',
                'file_size' => 52428800, // 50MB
                'duration' => 1800, // 30 minutes
                'thumbnail' => 'https://images.unsplash.com/photo-1517077304055-6e89abbf09b0?w=400&h=225&fit=crop',
                'category' => 'programmation',
                'difficulty_level' => 'beginner',
                'is_premium' => false,
                'price' => null,
                'tags' => ['laravel', 'php', 'web', 'framework'],
                'status' => 'published'
            ],
            [
                'title' => 'React Hooks Avancés',
                'description' => 'Maîtrisez les hooks personnalisés et les patterns avancés de React.',
                'file_url' => 'https://example.com/videos/react-hooks.mp4',
                'file_name' => 'react-hooks.mp4',
                'file_size' => 73400320, // 70MB
                'duration' => 2700, // 45 minutes
                'thumbnail' => 'https://images.unsplash.com/photo-1633356122544-f134324a6cee?w=400&h=225&fit=crop',
                'category' => 'programmation',
                'difficulty_level' => 'intermediate',
                'is_premium' => true,
                'price' => 19.99,
                'tags' => ['react', 'javascript', 'frontend', 'hooks'],
                'status' => 'published'
            ],
            [
                'title' => 'Design UI/UX pour Débutants',
                'description' => 'Créez des interfaces utilisateur modernes et intuitives.',
                'file_url' => 'https://example.com/videos/ui-ux-design.mp4',
                'file_name' => 'ui-ux-design.mp4',
                'file_size' => 41943040, // 40MB
                'duration' => 2400, // 40 minutes
                'thumbnail' => 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=400&h=225&fit=crop',
                'category' => 'design',
                'difficulty_level' => 'beginner',
                'is_premium' => false,
                'price' => null,
                'tags' => ['design', 'ui', 'ux', 'interface'],
                'status' => 'published'
            ],
            [
                'title' => 'Marketing Digital Avancé',
                'description' => 'Stratégies avancées pour développer votre présence en ligne.',
                'file_url' => 'https://example.com/videos/digital-marketing.mp4',
                'file_name' => 'digital-marketing.mp4',
                'file_size' => 62914560, // 60MB
                'duration' => 3600, // 1 heure
                'thumbnail' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=400&h=225&fit=crop',
                'category' => 'marketing',
                'difficulty_level' => 'advanced',
                'is_premium' => true,
                'price' => 29.99,
                'tags' => ['marketing', 'digital', 'strategie', 'online'],
                'status' => 'published'
            ]
        ];

        foreach ($videos as $videoData) {
            Video::create(array_merge($videoData, [
                'instructor_id' => $instructor->id
            ]));
        }

        $this->command->info('✅ Vidéos d\'exemple créées avec succès !');
    }
}
