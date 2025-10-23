<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic',
                'plan_type' => 'basic',
                'description' => 'Plan de base pour commencer votre apprentissage',
                'price_monthly' => 9.99,
                'price_yearly' => 99.99,
                'features' => [
                    'Accès aux cours de base',
                    'Support communautaire',
                    'Certificats de fin de cours',
                    'Accès limité à la bibliothèque'
                ],
                'max_courses' => 5,
                'max_downloads' => 10,
                'priority_support' => false
            ],
            [
                'name' => 'Premium',
                'plan_type' => 'premium',
                'description' => 'Plan premium avec toutes les fonctionnalités avancées',
                'price_monthly' => 19.99,
                'price_yearly' => 199.99,
                'features' => [
                    'Accès à tous les cours',
                    'Support prioritaire',
                    'Certificats premium',
                    'Accès complet à la bibliothèque',
                    'Cours en direct',
                    'Mentorat personnalisé'
                ],
                'max_courses' => -1, // Illimité
                'max_downloads' => -1, // Illimité
                'priority_support' => true
            ],
            [
                'name' => 'Enterprise',
                'plan_type' => 'enterprise',
                'description' => 'Plan entreprise pour les équipes et organisations',
                'price_monthly' => 49.99,
                'price_yearly' => 499.99,
                'features' => [
                    'Tout du plan Premium',
                    'Gestion d\'équipe',
                    'Analytics avancés',
                    'API d\'intégration',
                    'Support dédié 24/7',
                    'Formation sur mesure',
                    'Rapports personnalisés'
                ],
                'max_courses' => -1,
                'max_downloads' => -1,
                'priority_support' => true
            ]
        ];

        // Créer la table des plans si elle n'existe pas
        if (!DB::getSchemaBuilder()->hasTable('subscription_plans')) {
            DB::statement('
                CREATE TABLE subscription_plans (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    plan_type VARCHAR(50) NOT NULL UNIQUE,
                    description TEXT,
                    price_monthly DECIMAL(8,2) NOT NULL,
                    price_yearly DECIMAL(8,2) NOT NULL,
                    features JSON,
                    max_courses INT NOT NULL,
                    max_downloads INT NOT NULL,
                    priority_support BOOLEAN DEFAULT FALSE,
                    is_active BOOLEAN DEFAULT TRUE,
                    created_at TIMESTAMP NULL,
                    updated_at TIMESTAMP NULL
                )
            ');
        }

        // Insérer ou mettre à jour les plans
        foreach ($plans as $plan) {
            DB::table('subscription_plans')->updateOrInsert(
                ['plan_type' => $plan['plan_type']],
                [
                    'name' => $plan['name'],
                    'description' => $plan['description'],
                    'price_monthly' => $plan['price_monthly'],
                    'price_yearly' => $plan['price_yearly'],
                    'features' => json_encode($plan['features']),
                    'max_courses' => $plan['max_courses'],
                    'max_downloads' => $plan['max_downloads'],
                    'priority_support' => $plan['priority_support'],
                    'is_active' => true,
                    'updated_at' => now()
                ]
            );
        }

        $this->command->info('Plans d\'abonnement créés avec succès !');
    }
}
