<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Plans d'abonnement par défaut
    |--------------------------------------------------------------------------
    |
    | Configuration des plans d'abonnement disponibles dans l'application
    |
    */

    'plans' => [
        'basic' => [
            'name' => 'Basic',
            'description' => 'Plan de base pour commencer votre apprentissage',
            'price_monthly' => 9.99,
            'price_yearly' => 99.99,
            'currency' => 'EUR',
            'features' => [
                'Accès aux cours de base',
                'Support communautaire',
                'Certificats de fin de cours',
                'Accès limité à la bibliothèque'
            ],
            'limits' => [
                'max_courses' => 5,
                'max_downloads' => 10,
                'priority_support' => false
            ]
        ],

        'premium' => [
            'name' => 'Premium',
            'description' => 'Plan premium avec toutes les fonctionnalités avancées',
            'price_monthly' => 19.99,
            'price_yearly' => 199.99,
            'currency' => 'EUR',
            'features' => [
                'Accès à tous les cours',
                'Support prioritaire',
                'Certificats premium',
                'Accès complet à la bibliothèque',
                'Cours en direct',
                'Mentorat personnalisé'
            ],
            'limits' => [
                'max_courses' => -1, // Illimité
                'max_downloads' => -1, // Illimité
                'priority_support' => true
            ]
        ],

        'enterprise' => [
            'name' => 'Enterprise',
            'description' => 'Plan entreprise pour les équipes et organisations',
            'price_monthly' => 49.99,
            'price_yearly' => 499.99,
            'currency' => 'EUR',
            'features' => [
                'Tout du plan Premium',
                'Gestion d\'équipe',
                'Analytics avancés',
                'API d\'intégration',
                'Support dédié 24/7',
                'Formation sur mesure',
                'Rapports personnalisés'
            ],
            'limits' => [
                'max_courses' => -1,
                'max_downloads' => -1,
                'priority_support' => true
            ]
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration des paiements
    |--------------------------------------------------------------------------
    |
    | Paramètres généraux pour la gestion des paiements
    |
    */

    'payment' => [
        'default_currency' => 'EUR',
        'supported_currencies' => ['EUR', 'USD', 'GBP'],
        'auto_renewal' => true,
        'grace_period_days' => 7, // Période de grâce après expiration
        'max_failed_payments' => 3, // Nombre max d'échecs avant suspension
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration des notifications
    |--------------------------------------------------------------------------
    |
    | Paramètres pour les notifications liées aux abonnements
    |
    */

    'notifications' => [
        'expiration_warning_days' => [30, 7, 1], // Jours avant expiration pour avertir
        'renewal_reminder_days' => [7, 3, 1], // Jours avant renouvellement pour rappeler
        'payment_failed_retry_days' => [1, 3, 7], // Jours entre les tentatives de paiement
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration des fonctionnalités premium
    |--------------------------------------------------------------------------
    |
    | Définition des fonctionnalités disponibles selon le plan
    |
    */

    'features' => [
        'basic' => [
            'courses' => ['basic', 'intermediate'],
            'documents' => ['limited'],
            'videos' => ['basic'],
            'support' => 'community',
            'certificates' => true,
            'analytics' => false
        ],
        'premium' => [
            'courses' => ['basic', 'intermediate', 'advanced', 'expert'],
            'documents' => ['unlimited'],
            'videos' => ['unlimited'],
            'support' => 'priority',
            'certificates' => true,
            'analytics' => 'basic',
            'live_courses' => true,
            'mentoring' => true
        ],
        'enterprise' => [
            'courses' => ['all'],
            'documents' => ['unlimited'],
            'videos' => ['unlimited'],
            'support' => 'dedicated',
            'certificates' => true,
            'analytics' => 'advanced',
            'live_courses' => true,
            'mentoring' => true,
            'team_management' => true,
            'api_access' => true,
            'custom_reports' => true
        ]
    ]
];
