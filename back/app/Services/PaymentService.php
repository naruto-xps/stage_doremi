<?php

namespace App\Services;

use App\Models\User;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class PaymentService
{
    /**
     * Créer un nouvel abonnement avec paiement
     */
    public function createSubscriptionWithPayment(array $data): array
    {
        DB::beginTransaction();
        
        try {
            // Vérifier que l'utilisateur n'a pas déjà un abonnement actif
            $existingSubscription = Subscription::where('user_id', $data['user_id'])
                ->where('status', 'active')
                ->first();

            if ($existingSubscription) {
                throw new Exception('L\'utilisateur a déjà un abonnement actif');
            }

            // Calculer les dates
            $startDate = now();
            $endDate = $startDate->copy()->addMonths($data['duration_months']);

            // Créer l'abonnement
            $subscription = Subscription::create([
                'user_id' => $data['user_id'],
                'plan_type' => $data['plan_type'],
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'EUR',
                'duration_months' => $data['duration_months'],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => 'pending', // En attente de paiement
                'auto_renew' => $data['auto_renew'] ?? true,
                'payment_method' => $data['payment_method'],
                'description' => $data['description'] ?? null,
                'features' => $data['features'] ?? null
            ]);

            // Créer le paiement
            $payment = Payment::create([
                'user_id' => $data['user_id'],
                'subscription_id' => $subscription->id,
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'EUR',
                'payment_method' => $data['payment_method'],
                'transaction_id' => $data['transaction_id'] ?? $this->generateTransactionId(),
                'status' => 'pending',
                'gateway' => $data['gateway'] ?? null,
                'gateway_transaction_id' => $data['gateway_transaction_id'] ?? null,
                'metadata' => $data['metadata'] ?? null
            ]);

            DB::commit();

            return [
                'success' => true,
                'subscription' => $subscription,
                'payment' => $payment,
                'message' => 'Abonnement et paiement créés avec succès'
            ];

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création de l\'abonnement: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Erreur lors de la création de l\'abonnement: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Traiter un paiement réussi
     */
    public function processSuccessfulPayment(string $transactionId, array $gatewayData = []): array
    {
        DB::beginTransaction();
        
        try {
            $payment = Payment::where('transaction_id', $transactionId)->first();
            
            if (!$payment) {
                throw new Exception('Paiement non trouvé');
            }

            if ($payment->isSuccessful()) {
                throw new Exception('Le paiement a déjà été traité');
            }

            // Mettre à jour le statut du paiement
            $payment->update([
                'status' => 'completed',
                'gateway_transaction_id' => $gatewayData['gateway_transaction_id'] ?? null,
                'metadata' => array_merge($payment->metadata ?? [], $gatewayData),
                'processed_at' => now()
            ]);

            // Activer l'abonnement
            $subscription = $payment->subscription;
            $subscription->update(['status' => 'active']);

            // Mettre à jour le statut premium de l'utilisateur
            $user = $payment->user;
            $user->update([
                'is_premium' => true,
                'premium_expires_at' => $subscription->end_date
            ]);

            DB::commit();

            return [
                'success' => true,
                'payment' => $payment,
                'subscription' => $subscription,
                'message' => 'Paiement traité avec succès'
            ];

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors du traitement du paiement: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Erreur lors du traitement du paiement: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Traiter un échec de paiement
     */
    public function processFailedPayment(string $transactionId, string $failureReason): array
    {
        try {
            $payment = Payment::where('transaction_id', $transactionId)->first();
            
            if (!$payment) {
                throw new Exception('Paiement non trouvé');
            }

            // Mettre à jour le statut du paiement
            $payment->update([
                'status' => 'failed',
                'failure_reason' => $failureReason,
                'processed_at' => now()
            ]);

            // Désactiver l'abonnement
            $subscription = $payment->subscription;
            $subscription->update(['status' => 'suspended']);

            return [
                'success' => true,
                'payment' => $payment,
                'subscription' => $subscription,
                'message' => 'Échec de paiement traité'
            ];

        } catch (Exception $e) {
            Log::error('Erreur lors du traitement de l\'échec de paiement: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Erreur lors du traitement de l\'échec de paiement: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Renouveler un abonnement
     */
    public function renewSubscription(int $subscriptionId): array
    {
        DB::beginTransaction();
        
        try {
            $subscription = Subscription::findOrFail($subscriptionId);
            
            if (!$subscription->canRenew()) {
                throw new Exception('L\'abonnement ne peut pas être renouvelé');
            }

            // Calculer la nouvelle date de fin
            $newEndDate = $subscription->end_date->addMonths($subscription->duration_months);

            // Mettre à jour l'abonnement
            $subscription->update([
                'end_date' => $newEndDate,
                'status' => 'active'
            ]);

            // Mettre à jour le statut premium de l'utilisateur
            $user = $subscription->user;
            $user->update([
                'is_premium' => true,
                'premium_expires_at' => $newEndDate
            ]);

            DB::commit();

            return [
                'success' => true,
                'subscription' => $subscription,
                'message' => 'Abonnement renouvelé avec succès'
            ];

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors du renouvellement de l\'abonnement: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Erreur lors du renouvellement de l\'abonnement: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Annuler un abonnement
     */
    public function cancelSubscription(int $subscriptionId): array
    {
        DB::beginTransaction();
        
        try {
            $subscription = Subscription::findOrFail($subscriptionId);
            
            // Mettre à jour l'abonnement
            $subscription->update([
                'status' => 'cancelled',
                'auto_renew' => false
            ]);

            // Désactiver le statut premium de l'utilisateur
            $user = $subscription->user;
            $user->update([
                'is_premium' => false,
                'premium_expires_at' => null
            ]);

            DB::commit();

            return [
                'success' => true,
                'subscription' => $subscription,
                'message' => 'Abonnement annulé avec succès'
            ];

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de l\'annulation de l\'abonnement: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'annulation de l\'abonnement: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtenir les statistiques de paiement pour un utilisateur
     */
    public function getUserPaymentStats(int $userId): array
    {
        $user = User::findOrFail($userId);
        
        $totalPayments = $user->payments()->count();
        $successfulPayments = $user->payments()->where('status', 'completed')->count();
        $totalSpent = $user->payments()->where('status', 'completed')->sum('amount');
        $activeSubscription = $user->getActiveSubscription();
        
        return [
            'total_payments' => $totalPayments,
            'successful_payments' => $successfulPayments,
            'failed_payments' => $totalPayments - $successfulPayments,
            'total_spent' => $totalSpent,
            'has_active_subscription' => $user->hasActiveSubscription(),
            'subscription_status' => $activeSubscription ? $activeSubscription->status : null,
            'subscription_expires' => $activeSubscription ? $activeSubscription->end_date : null
        ];
    }

    /**
     * Générer un ID de transaction unique
     */
    private function generateTransactionId(): string
    {
        do {
            $transactionId = 'TXN_' . strtoupper(Str::random(16));
        } while (Payment::where('transaction_id', $transactionId)->exists());
        
        return $transactionId;
    }

    /**
     * Vérifier et mettre à jour les abonnements expirés
     */
    public function checkExpiredSubscriptions(): array
    {
        $expiredSubscriptions = Subscription::where('status', 'active')
            ->where('end_date', '<=', now())
            ->get();

        $updatedCount = 0;
        
        foreach ($expiredSubscriptions as $subscription) {
            $subscription->update(['status' => 'expired']);
            
            // Désactiver le statut premium de l'utilisateur
            $user = $subscription->user;
            $user->update([
                'is_premium' => false,
                'premium_expires_at' => null
            ]);
            
            $updatedCount++;
        }

        return [
            'success' => true,
            'expired_count' => $updatedCount,
            'message' => "{$updatedCount} abonnement(s) marqué(s) comme expiré(s)"
        ];
    }
}
