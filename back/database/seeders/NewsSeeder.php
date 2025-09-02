<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\News;
use App\Models\User;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un utilisateur admin si il n'existe pas
        $admin = User::firstOrCreate(
            ['email' => 'admin@doremi.com'],
            [
                'name' => 'Admin',
                'surname' => 'DOREMI',
                'password' => bcrypt('password'),
                'role' => 'admin'
            ]
        );

        $newsData = [
            [
                'title' => 'Offre spéciale : -50% sur tous les cours Premium',
                'content' => 'Profitez de notre offre exceptionnelle ! Tous les cours Premium sont à -50% jusqu\'à la fin du mois. Une opportunité unique de développer vos compétences à prix réduit.',
                'excerpt' => 'Offre exceptionnelle de -50% sur tous les cours Premium',
                'image' => 'https://images.unsplash.com/photo-1517077304055-6e89abbf09b0?w=800&h=400&fit=crop',
                'featured_image' => 'https://images.unsplash.com/photo-1517077304055-6e89abbf09b0?w=800&h=400&fit=crop',
                'author' => 'Équipe Marketing',
                'location' => 'Tout le Sénégal',
                'read_time' => '2 min',
                'type' => 'announcement',
                'priority' => 'high',
                'status' => 'published',
                'is_featured' => true,
                'is_urgent' => false,
                'category' => 'Promotion',
                'subcategory' => 'Cours',
                'tags' => ['promotion', 'cours', 'premium', 'réduction'],
                'target_audience' => ['étudiants', 'professionnels'],
                'meta_description' => 'Offre exceptionnelle de -50% sur tous les cours Premium DOREMI',
                'meta_keywords' => 'cours, premium, promotion, formation, sénégal',
                'gallery' => [
                    'https://images.unsplash.com/photo-1517077304055-6e89abbf09b0?w=800&h=400&fit=crop',
                    'https://images.unsplash.com/photo-1523050854058-8df90110c9e1?w=800&h=400&fit=crop'
                ],
                'published_at' => now(),
                'expires_at' => now()->addDays(30),
                'views_count' => 3247,
                'likes_count' => 189,
                'comments_count' => 45,
                'shares_count' => 23,
                'author_id' => $admin->id
            ],
            [
                'title' => 'Nouveau : Pack Formation Complète Développement Web',
                'content' => 'Découvrez notre nouveau pack formation complète en développement web. HTML, CSS, JavaScript, React, Node.js - tout inclus ! Prix spécial lancement.',
                'excerpt' => 'Pack formation complète en développement web avec prix spécial',
                'image' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9e1?w=800&h=400&fit=crop',
                'featured_image' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9e1?w=800&h=400&fit=crop',
                'author' => 'Équipe Formation',
                'location' => 'Dakar',
                'read_time' => '3 min',
                'type' => 'news',
                'priority' => 'high',
                'status' => 'published',
                'is_featured' => true,
                'is_urgent' => false,
                'category' => 'Nouveau Produit',
                'subcategory' => 'Formation',
                'tags' => ['développement web', 'formation', 'nouveau', 'pack'],
                'target_audience' => ['développeurs', 'étudiants', 'professionnels'],
                'meta_description' => 'Pack formation complète en développement web DOREMI',
                'meta_keywords' => 'développement web, formation, html, css, javascript, react',
                'gallery' => [
                    'https://images.unsplash.com/photo-1523050854058-8df90110c9e1?w=800&h=400&fit=crop'
                ],
                'published_at' => now()->subDays(5),
                'expires_at' => null,
                'views_count' => 2156,
                'likes_count' => 156,
                'comments_count' => 32,
                'shares_count' => 18,
                'author_id' => $admin->id
            ],
            [
                'title' => 'Cours de français intensif - Inscriptions ouvertes',
                'content' => 'Préparez-vous aux examens avec notre cours de français intensif. 3 mois de formation, 4h par semaine. Garantie de réussite ou remboursé !',
                'excerpt' => 'Cours de français intensif avec garantie de réussite',
                'image' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=800&h=400&fit=crop',
                'featured_image' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=800&h=400&fit=crop',
                'author' => 'Centre Linguistique',
                'location' => 'Dakar',
                'read_time' => '2 min',
                'type' => 'announcement',
                'priority' => 'high',
                'status' => 'published',
                'is_featured' => true,
                'is_urgent' => false,
                'category' => 'Formation',
                'subcategory' => 'Langues',
                'tags' => ['français', 'intensif', 'examens', 'garantie'],
                'target_audience' => ['étudiants', 'candidats examens'],
                'meta_description' => 'Cours de français intensif avec garantie de réussite',
                'meta_keywords' => 'français, cours, intensif, examens, dakar',
                'gallery' => [
                    'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=800&h=400&fit=crop'
                ],
                'published_at' => now()->subDays(7),
                'expires_at' => now()->addDays(60),
                'views_count' => 1893,
                'likes_count' => 134,
                'comments_count' => 28,
                'shares_count' => 15,
                'author_id' => $admin->id
            ],
            [
                'title' => 'Stages en entreprise - Plus de 100 offres disponibles',
                'content' => 'Trouvez votre stage idéal ! Plus de 100 entreprises partenaires proposent des stages dans tous les domaines. CV et lettre de motivation inclus.',
                'excerpt' => 'Plus de 100 offres de stage disponibles dans tous les domaines',
                'image' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=800&h=400&fit=crop',
                'featured_image' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=800&h=400&fit=crop',
                'author' => 'Service Placement',
                'location' => 'Sénégal',
                'read_time' => '2 min',
                'type' => 'announcement',
                'priority' => 'medium',
                'status' => 'published',
                'is_featured' => false,
                'is_urgent' => false,
                'category' => 'Stage',
                'subcategory' => 'Emploi',
                'tags' => ['stage', 'entreprise', 'emploi', 'placement'],
                'target_audience' => ['étudiants', 'jeunes diplômés'],
                'meta_description' => 'Plus de 100 offres de stage disponibles au Sénégal',
                'meta_keywords' => 'stage, entreprise, emploi, sénégal, placement',
                'gallery' => [
                    'https://images.unsplash.com/photo-1552664730-d307ca884978?w=800&h=400&fit=crop'
                ],
                'published_at' => now()->subDays(10),
                'expires_at' => null,
                'views_count' => 4421,
                'likes_count' => 267,
                'comments_count' => 78,
                'shares_count' => 45,
                'author_id' => $admin->id
            ],
            [
                'title' => 'Pack Bureautique Office - Formation certifiante',
                'content' => 'Maîtrisez Word, Excel, PowerPoint et Access. Formation certifiante Microsoft Office. Certificat reconnu internationalement.',
                'excerpt' => 'Formation certifiante Microsoft Office avec certificat international',
                'image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=800&h=400&fit=crop',
                'featured_image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=800&h=400&fit=crop',
                'author' => 'Centre de Certification',
                'location' => 'Dakar',
                'read_time' => '2 min',
                'type' => 'announcement',
                'priority' => 'medium',
                'status' => 'published',
                'is_featured' => false,
                'is_urgent' => false,
                'category' => 'Certification',
                'subcategory' => 'Bureautique',
                'tags' => ['office', 'microsoft', 'certification', 'bureautique'],
                'target_audience' => ['professionnels', 'étudiants', 'demandeurs d\'emploi'],
                'meta_description' => 'Formation certifiante Microsoft Office DOREMI',
                'meta_keywords' => 'office, microsoft, certification, bureautique, dakar',
                'gallery' => [
                    'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=800&h=400&fit=crop'
                ],
                'published_at' => now()->subDays(12),
                'expires_at' => null,
                'views_count' => 2987,
                'likes_count' => 198,
                'comments_count' => 56,
                'shares_count' => 34,
                'author_id' => $admin->id
            ]
        ];

        foreach ($newsData as $newsItem) {
            News::create($newsItem);
        }

        $this->command->info('News seeded successfully!');
    }
} 