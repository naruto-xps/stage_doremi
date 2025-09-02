<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="Paiements",
 *     description="API pour la gestion des paiements et abonnements"
 * )
 */
class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }
    /**
     * @OA\Get(
     *     path="/api/subscriptions/{userId}",
     *     summary="Obtenir les abonnements d'un utilisateur",
     *     tags={"Paiements"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="ID de l'utilisateur",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Abonnements récupérés avec succès"
     *     ),
     *     @OA\Response(response=403, description="Accès refusé")
     * )
     */
    public function getSubscriptions($userId)
    {
        if (Auth::id() != $userId && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $subscriptions = Subscription::where('user_id', $userId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($subscriptions);
    }

    /**
     * @OA\Post(
     *     path="/api/subscriptions",
     *     summary="Créer un nouvel abonnement",
     *     tags={"Paiements"},
     *     security={{"BearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="plan_type", type="string", enum={"basic", "premium", "enterprise"}),
     *             @OA\Property(property="amount", type="number"),
     *             @OA\Property(property="currency", type="string", default="EUR"),
     *             @OA\Property(property="duration_months", type="integer"),
     *             @OA\Property(property="payment_method", type="string"),
     *             @OA\Property(property="auto_renew", type="boolean", default=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Abonnement créé avec succès"
     *     ),
     *     @OA\Response(response=422, description="Données invalides")
     * )
     */
    public function createSubscription(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'plan_type' => 'required|in:basic,premium,enterprise',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'duration_months' => 'required|integer|min:1|max:60',
            'payment_method' => 'required|string',
            'auto_renew' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $result = $this->paymentService->createSubscriptionWithPayment($request->all());

        if ($result['success']) {
            return response()->json($result['subscription'], 201);
        } else {
            return response()->json(['message' => $result['message']], 422);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/subscriptions/{id}",
     *     summary="Mettre à jour un abonnement",
     *     tags={"Paiements"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'abonnement",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="plan_type", type="string"),
     *             @OA\Property(property="amount", type="number"),
     *             @OA\Property(property="auto_renew", type="boolean"),
     *             @OA\Property(property="status", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Abonnement mis à jour avec succès"
     *     ),
     *     @OA\Response(response=404, description="Abonnement non trouvé")
     * )
     */
    public function updateSubscription(Request $request, $id)
    {
        $subscription = Subscription::findOrFail($id);

        // Vérifier que l'utilisateur connecté peut modifier cet abonnement
        if (Auth::id() != $subscription->user_id && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $validator = Validator::make($request->all(), [
            'plan_type' => 'sometimes|in:basic,premium,enterprise',
            'amount' => 'sometimes|numeric|min:0',
            'auto_renew' => 'sometimes|boolean',
            'status' => 'sometimes|in:active,suspended,cancelled,expired'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $subscription->update($request->all());

        return response()->json($subscription);
    }

    /**
     * @OA\Delete(
     *     path="/api/subscriptions/{id}",
     *     summary="Supprimer un abonnement",
     *     tags={"Paiements"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'abonnement",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Abonnement supprimé avec succès"
     *     ),
     *     @OA\Response(response=404, description="Abonnement non trouvé")
     * )
     */
    public function deleteSubscription($id)
    {
        $subscription = Subscription::findOrFail($id);

        // Seuls les admins peuvent supprimer des abonnements
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $subscription->delete();

        return response()->json(['message' => 'Abonnement supprimé avec succès']);
    }

    /**
     * @OA\Post(
     *     path="/api/payments",
     *     summary="Traiter un paiement",
     *     tags={"Paiements"},
     *     security={{"BearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="subscription_id", type="integer"),
     *             @OA\Property(property="amount", type="number"),
     *             @OA\Property(property="currency", type="string"),
     *             @OA\Property(property="payment_method", type="string"),
     *             @OA\Property(property="transaction_id", type="string"),
     *             @OA\Property(property="status", type="string", enum={"pending", "completed", "failed", "refunded"}),
     *             @OA\Property(property="metadata", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Paiement traité avec succès"
     *     ),
     *     @OA\Response(response=422, description="Données invalides")
     * )
     */
    public function processPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required|string',
            'status' => 'required|in:completed,failed',
            'gateway_data' => 'nullable|array',
            'failure_reason' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->status === 'completed') {
            $result = $this->paymentService->processSuccessfulPayment(
                $request->transaction_id,
                $request->gateway_data ?? []
            );
        } else {
            $result = $this->paymentService->processFailedPayment(
                $request->transaction_id,
                $request->failure_reason ?? 'Raison non spécifiée'
            );
        }

        if ($result['success']) {
            return response()->json($result, 200);
        } else {
            return response()->json(['message' => $result['message']], 422);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/payments/history/{userId}",
     *     summary="Obtenir l'historique des paiements d'un utilisateur",
     *     tags={"Paiements"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="ID de l'utilisateur",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Numéro de page",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Historique des paiements récupéré avec succès"
     *     )
     * )
     */
    public function getPaymentHistory($userId, Request $request)
    {
        if (Auth::id() != $userId && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $page = $request->get('page', 1);
        $perPage = 20;

        $payments = Payment::where('user_id', $userId)
            ->with('subscription')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json($payments);
    }

    /**
     * @OA\Post(
     *     path="/api/subscriptions/{id}/cancel",
     *     summary="Annuler un abonnement",
     *     tags={"Paiements"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'abonnement",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Abonnement annulé avec succès"
     *     ),
     *     @OA\Response(response=404, description="Abonnement non trouvé")
     * )
     */
    public function cancelSubscription($id)
    {
        $subscription = Subscription::findOrFail($id);

        if (Auth::id() != $subscription->user_id && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $result = $this->paymentService->cancelSubscription($id);

        if ($result['success']) {
            return response()->json(['message' => 'Abonnement annulé avec succès']);
        } else {
            return response()->json(['message' => $result['message']], 422);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/subscriptions/{id}/renew",
     *     summary="Renouveler un abonnement",
     *     tags={"Paiements"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'abonnement",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Abonnement renouvelé avec succès"
     *     ),
     *     @OA\Response(response=404, description="Abonnement non trouvé")
     * )
     */
    public function renewSubscription($id)
    {
        $subscription = Subscription::findOrFail($id);

        if (Auth::id() != $subscription->user_id && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $result = $this->paymentService->renewSubscription($id);

        if ($result['success']) {
            return response()->json(['message' => 'Abonnement renouvelé avec succès']);
        } else {
            return response()->json(['message' => $result['message']], 422);
        }
    }
}
