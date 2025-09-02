<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Subscription;
use App\Models\Payment;
use Illuminate\Support\Facades\Hash;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un utilisateur de test pour les paiements
        $user = User::firstOrCreate(
            ['email' => 'test.payment@example.com'],
            [
                'name' => 'Test',
                'surname' => 'Payment',
                'password' => Hash::make('password'),
                'role' => 'student',
                'is_premium' => false
            ]
        );

        // Créer un abonnement premium
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_type' => 'premium',
            'amount' => 29.99,
            'currency' => 'EUR',
            'duration_months' => 12,
            'start_date' => now(),
            'end_date' => now()->addMonths(12),
            'status' => 'active',
            'auto_renew' => true,
            'payment_method' => 'card',
            'description' => 'Abonnement Premium annuel',
            'features' => [
                'unlimited_courses',
                'premium_content',
                'priority_support',
                'certificates'
            ]
        ]);

        // Créer un paiement réussi
        Payment::create([
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'amount' => 29.99,
            'currency' => 'EUR',
            'payment_method' => 'card',
            'transaction_id' => 'TXN_' . uniqid(),
            'status' => 'completed',
            'gateway' => 'stripe',
            'gateway_transaction_id' => 'pi_' . uniqid(),
            'processed_at' => now()
        ]);

        // Mettre à jour le statut premium de l'utilisateur
        $user->update([
            'is_premium' => true,
            'premium_expires_at' => $subscription->end_date
        ]);

        // Créer un autre utilisateur avec un abonnement basique
        $user2 = User::firstOrCreate(
            ['email' => 'test.basic@example.com'],
            [
                'name' => 'Test',
                'surname' => 'Basic',
                'password' => Hash::make('password'),
                'role' => 'student',
                'is_premium' => false
            ]
        );

        $subscription2 = Subscription::create([
            'user_id' => $user2->id,
            'plan_type' => 'basic',
            'amount' => 9.99,
            'currency' => 'EUR',
            'duration_months' => 1,
            'start_date' => now()->subDays(15),
            'end_date' => now()->addDays(15),
            'status' => 'active',
            'auto_renew' => true,
            'payment_method' => 'card',
            'description' => 'Abonnement Basique mensuel',
            'features' => [
                'limited_courses',
                'basic_support'
            ]
        ]);

        Payment::create([
            'user_id' => $user2->id,
            'subscription_id' => $subscription2->id,
            'amount' => 9.99,
            'currency' => 'EUR',
            'payment_method' => 'card',
            'transaction_id' => 'TXN_' . uniqid(),
            'status' => 'completed',
            'gateway' => 'stripe',
            'gateway_transaction_id' => 'pi_' . uniqid(),
            'processed_at' => now()->subDays(15)
        ]);

        $user2->update([
            'is_premium' => true,
            'premium_expires_at' => $subscription2->end_date
        ]);

        $this->command->info('Payment seeder completed successfully!');
        $this->command->info("Created test users:");
        $this->command->info("- {$user->email} (Premium - 12 months)");
        $this->command->info("- {$user2->email} (Basic - 1 month)");
    }
}
