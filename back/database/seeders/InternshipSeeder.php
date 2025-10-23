<?php

namespace Database\Seeders;

use App\Models\Internship;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InternshipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $internships = [
            [
                'title' => 'Stage Développement Web Frontend',
                'description' => 'Rejoignez notre équipe pour développer des interfaces web modernes avec React et Vue.js. Vous travaillerez sur des projets réels et apprendrez les meilleures pratiques du développement frontend.',
                'location' => 'Dakar, Sénégal',
                'company' => 'TechHub Senegal',
            ],
            [
                'title' => 'Stage Développement Backend Laravel',
                'description' => 'Opportunité de stage pour développer des APIs robustes avec Laravel. Vous serez responsable de la conception et de l\'implémentation des services backend.',
                'location' => 'Dakar, Sénégal',
                'company' => 'Digital Solutions Inc',
            ],
            [
                'title' => 'Stage Data Science & Analytics',
                'description' => 'Travaillez avec des données réelles et développez vos compétences en analyse de données, machine learning et visualisation. Utilisez Python, pandas et TensorFlow.',
                'location' => 'Dakar, Sénégal',
                'company' => 'DataViz Africa',
            ],
            [
                'title' => 'Stage DevOps & Cloud Infrastructure',
                'description' => 'Gérez l\'infrastructure cloud, déployez des applications et optimisez les pipelines CI/CD. Expérience avec Docker, Kubernetes et AWS.',
                'location' => 'Dakar, Sénégal',
                'company' => 'CloudTech Solutions',
            ],
            [
                'title' => 'Stage Mobile Development (Flutter)',
                'description' => 'Développez des applications mobiles cross-platform avec Flutter. Travaillez sur des projets innovants pour iOS et Android.',
                'location' => 'Dakar, Sénégal',
                'company' => 'MobileFirst Studios',
            ],
            [
                'title' => 'Stage Design UX/UI',
                'description' => 'Créez des interfaces utilisateur magnifiques et intuitives. Travaillez avec Figma, Adobe XD et participez à des ateliers de design thinking.',
                'location' => 'Dakar, Sénégal',
                'company' => 'Creative Design Agency',
            ],
        ];

        foreach ($internships as $internship) {
            Internship::create($internship);
        }
    }
}
