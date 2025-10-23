<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Subscription;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Test Paiements",
 *     description="API de test pour le système de paiement"
 * )
 */
class TestPaymentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/test/payment-system",
     *     summary="Tester le système de paiement complet",
     *     tags={"Test Paiements"},
     *     @OA\Response(
     *         response=200,
     *         description="Test réussi"
     *     )
     * )
     */
    public function testPaymentSystem()
    {
        try {
            // Test 1: Vérifier les modèles
            $user = User::where('email', 'test.payment@example.com')->first();
            if (!$user) {
                return response()->json(['error' => 'Utilisateur de test non trouvé. Exécutez d\'abord le seeder.'], 404);
            }

            // Test 2: Vérifier les relations
            $subscriptions = $user->subscriptions;
            $payments = $user->payments;
            $activeSubscription = $user->getActiveSubscription();

            // Test 3: Vérifier la logique métier
            $isPremium = $user->isPremiumUser();
            $hasActiveSubscription = $user->hasActiveSubscription();

            // Test 4: Vérifier les données
            $subscriptionData = $subscriptions->map(function ($sub) {
                return [
                    'id' => $sub->id,
                    'plan_type' => $sub->plan_type,
                    'amount' => $sub->amount,
                    'status' => $sub->status,
                    'is_active' => $sub->isActive(),
                    'days_remaining' => $sub->getDaysRemaining(),
                    'features' => $sub->features
                ];
            });

            $paymentData = $payments->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'amount' => $payment->getFormattedAmount(),
                    'status' => $payment->status,
                    'is_successful' => $payment->isSuccessful(),
                    'transaction_id' => $payment->transaction_id
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Système de paiement fonctionnel',
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'is_premium' => $user->is_premium,
                    'premium_expires_at' => $user->premium_expires_at,
                    'is_premium_user' => $isPremium,
                    'has_active_subscription' => $hasActiveSubscription
                ],
                'subscriptions' => $subscriptionData,
                'payments' => $paymentData,
                'active_subscription' => $activeSubscription ? [
                    'id' => $activeSubscription->id,
                    'plan_type' => $activeSubscription->plan_type,
                    'end_date' => $activeSubscription->end_date,
                    'days_remaining' => $activeSubscription->getDaysRemaining()
                ] : null,
                'database_tables' => [
                    'users_count' => User::count(),
                    'subscriptions_count' => Subscription::count(),
                    'payments_count' => Payment::count()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors du test: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/test/payment-relations",
     *     summary="Tester les relations entre les modèles",
     *     tags={"Test Paiements"},
     *     @OA\Response(
     *         response=200,
     *         description="Relations testées avec succès"
     *     )
     * )
     */
    public function testRelations()
    {
        try {
            // Test des relations Eloquent
            $user = User::with(['subscriptions', 'payments'])->first();
            
            if (!$user) {
                return response()->json(['error' => 'Aucun utilisateur trouvé'], 404);
            }

            // Test des relations directes
            $subscriptionCount = $user->subscriptions()->count();
            $paymentCount = $user->payments()->count();

            // Test des relations inverses
            $subscription = Subscription::with('user')->first();
            $payment = Payment::with(['user', 'subscription'])->first();

            return response()->json([
                'success' => true,
                'relations_test' => [
                    'user_subscriptions_count' => $subscriptionCount,
                    'user_payments_count' => $paymentCount,
                    'subscription_user_exists' => $subscription ? $subscription->user ? true : false : false,
                    'payment_user_exists' => $payment ? $payment->user ? true : false : false,
                    'payment_subscription_exists' => $payment ? $payment->subscription ? true : false : false
                ],
                'sample_data' => [
                    'user' => $user->only(['id', 'name', 'email', 'is_premium']),
                    'subscription' => $subscription ? $subscription->only(['id', 'plan_type', 'amount', 'user_id']) : null,
                    'payment' => $payment ? $payment->only(['id', 'amount', 'user_id', 'subscription_id']) : null
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors du test des relations: ' . $e->getMessage()
            ], 500);
        }
    }
}
