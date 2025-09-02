<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Document;
use App\Models\User;

class LibrarySeeder extends Seeder
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
                'name' => 'Mariama',
                'surname' => 'Bâ',
                'email' => 'mariama.ba@example.com',
                'password' => bcrypt('password'),
                'role' => 'teacher',
                'is_verified' => true
            ]);
        }

        // Créer des exemples de documents pour la bibliothèque
        $documents = [
            [
                'title' => 'Une si longue lettre',
                'author' => 'Mariama Bâ',
                'description' => 'Roman épistolaire qui dénonce la condition féminine au Sénégal et plus largement en Afrique. Un chef-d\'œuvre de la littérature africaine.',
                'type' => 'book',
                'category' => 'Littérature Sénégalaise',
                'genre' => 'Roman',
                'upload_date' => '2024-01-15',
                'size' => 2200000, // 2.1 MB
                'file_url' => 'https://example.com/books/une-si-longue-lettre.pdf',
                'file_name' => 'une-si-longue-lettre.pdf',
                'is_premium' => false,
                'download_count' => 1250,
                'tags' => ['féminisme', 'société', 'afrique', 'classique'],
                'image_url' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=400&h=600&fit=crop',
                'rating' => 4.8,
                'country' => 'Sénégal',
                'status' => 'published',
                'views' => 2500
            ],
            [
                'title' => 'Sous l\'orage',
                'author' => 'Seydou Badian',
                'description' => 'Premier roman malien qui explore les conflits entre tradition et modernité à travers l\'histoire de Kany et Samou.',
                'type' => 'book',
                'category' => 'Littérature Africaine',
                'genre' => 'Roman',
                'upload_date' => '2024-01-20',
                'size' => 1800000, // 1.8 MB
                'file_url' => 'https://example.com/books/sous-l-orage.pdf',
                'file_name' => 'sous-l-orage.pdf',
                'is_premium' => false,
                'download_count' => 980,
                'tags' => ['tradition', 'modernité', 'amour', 'mali'],
                'image_url' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=400&h=600&fit=crop',
                'rating' => 4.6,
                'country' => 'Mali',
                'status' => 'published',
                'views' => 1800
            ],
            [
                'title' => 'L\'appel des arènes',
                'author' => 'Aminata Sow Fall',
                'description' => 'Roman qui met en scène les valeurs traditionnelles sénégalaises face à la modernité, centré sur la lutte traditionnelle.',
                'type' => 'book',
                'category' => 'Littérature Sénégalaise',
                'genre' => 'Roman',
                'upload_date' => '2024-02-01',
                'size' => 2300000, // 2.3 MB
                'file_url' => 'https://example.com/books/l-appel-des-arenes.pdf',
                'file_name' => 'l-appel-des-arenes.pdf',
                'is_premium' => true,
                'download_count' => 756,
                'tags' => ['tradition', 'sport', 'identité', 'culture'],
                'image_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=600&fit=crop',
                'rating' => 4.7,
                'country' => 'Sénégal',
                'status' => 'published',
                'views' => 1200
            ],
            [
                'title' => 'Manuel de Mathématiques - Terminale',
                'author' => 'Ministère de l\'Éducation',
                'description' => 'Manuel officiel de mathématiques pour les élèves de terminale au Sénégal.',
                'type' => 'pdf',
                'category' => 'Éducation',
                'genre' => 'Manuel',
                'upload_date' => '2024-01-10',
                'size' => 15200000, // 15.2 MB
                'file_url' => 'https://example.com/education/maths-terminale.pdf',
                'file_name' => 'maths-terminale.pdf',
                'is_premium' => false,
                'download_count' => 2340,
                'tags' => ['mathématiques', 'éducation', 'lycée', 'officiel'],
                'image_url' => 'https://images.unsplash.com/photo-1635070041078-e363dbe005cb?w=400&h=600&fit=crop',
                'rating' => 4.3,
                'country' => 'Sénégal',
                'status' => 'published',
                'views' => 3500
            ],
            [
                'title' => 'Histoire du Sénégal - De l\'Antiquité à nos jours',
                'author' => 'Dr. Mamadou Diouf',
                'description' => 'Ouvrage de référence sur l\'histoire complète du Sénégal, de ses origines à l\'époque contemporaine.',
                'type' => 'pdf',
                'category' => 'Histoire',
                'genre' => 'Essai',
                'upload_date' => '2024-01-25',
                'size' => 8700000, // 8.7 MB
                'file_url' => 'https://example.com/history/histoire-senegal.pdf',
                'file_name' => 'histoire-senegal.pdf',
                'is_premium' => true,
                'download_count' => 567,
                'tags' => ['histoire', 'sénégal', 'référence', 'académique'],
                'image_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=600&fit=crop',
                'rating' => 4.7,
                'country' => 'Sénégal',
                'status' => 'published',
                'views' => 890
            ],
            [
                'title' => 'Introduction à la Programmation Python',
                'author' => 'Formateur DOREMI',
                'description' => 'Cours complet d\'introduction à Python pour débutants, avec exercices pratiques et projets.',
                'type' => 'video',
                'category' => 'Programmation',
                'genre' => 'Tutoriel',
                'upload_date' => '2024-02-15',
                'size' => 52428800, // 50 MB
                'file_url' => 'https://example.com/videos/python-intro.mp4',
                'file_name' => 'python-intro.mp4',
                'is_premium' => false,
                'download_count' => 320,
                'tags' => ['python', 'programmation', 'débutant', 'tutoriel'],
                'image_url' => 'https://images.unsplash.com/photo-1635070041078-e363dbe005cb?w=400&h=600&fit=crop',
                'rating' => 4.9,
                'country' => 'Sénégal',
                'status' => 'published',
                'views' => 1200,
                'duration' => 3600, // 1 heure
                'thumbnail' => 'https://images.unsplash.com/photo-1635070041078-e363dbe005cb?w=400&h=200&fit=crop'
            ]
        ];

        foreach ($documents as $documentData) {
            Document::create(array_merge($documentData, [
                'instructor_id' => $instructor->id
            ]));
        }

        $this->command->info('✅ Documents d\'exemple de la bibliothèque créés avec succès !');
    }
}
