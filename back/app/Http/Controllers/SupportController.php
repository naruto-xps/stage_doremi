<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\FAQ;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Support",
 *     description="API pour le support et l'aide utilisateur"
 * )
 */
class SupportController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/support/tickets/{userId}",
     *     summary="Obtenir les tickets de support d'un utilisateur",
     *     tags={"Support"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="ID de l'utilisateur",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filtrer par statut",
     *         required=false,
     *         @OA\Schema(type="string", enum={"open", "in_progress", "resolved", "closed"})
     *     ),
     *     @OA\Parameter(
     *         name="priority",
     *         in="query",
     *         description="Filtrer par priorité",
     *         required=false,
     *         @OA\Schema(type="string", enum={"low", "medium", "high", "urgent"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tickets de support récupérés avec succès"
     *     ),
     *     @OA\Response(response=403, description="Accès refusé")
     * )
     */
    public function getUserTickets($userId, Request $request)
    {
        if (Auth::id() != $userId && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $query = SupportTicket::where('user_id', $userId);

        // Filtres
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->get('priority'));
        }

        $tickets = $query->orderBy('created_at', 'desc')->get();

        return response()->json($tickets);
    }

    /**
     * @OA\Post(
     *     path="/api/support/tickets",
     *     summary="Créer un nouveau ticket de support",
     *     tags={"Support"},
     *     security={{"BearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="subject", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="category", type="string", enum={"technical", "billing", "course", "general"}),
     *             @OA\Property(property="priority", type="string", enum={"low", "medium", "high", "urgent"}),
     *             @OA\Property(property="attachments", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Ticket de support créé avec succès"
     *     ),
     *     @OA\Response(response=422, description="Données invalides")
     * )
     */
    public function createTicket(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'category' => 'required|in:technical,billing,course,general',
            'priority' => 'required|in:low,medium,high,urgent',
            'attachments' => 'nullable|array',
            'attachments.*' => 'string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Vérifier que l'utilisateur connecté peut créer ce ticket
        if (Auth::id() != $request->user_id && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $ticket = SupportTicket::create($request->all());

        return response()->json($ticket, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/support/tickets/{id}",
     *     summary="Obtenir un ticket de support spécifique",
     *     tags={"Support"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du ticket",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ticket de support récupéré avec succès"
     *     ),
     *     @OA\Response(response=404, description="Ticket non trouvé")
     * )
     */
    public function getTicket($id)
    {
        $ticket = SupportTicket::with(['user', 'replies'])->findOrFail($id);

        // Vérifier que l'utilisateur connecté peut voir ce ticket
        if (Auth::id() != $ticket->user_id && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        return response()->json($ticket);
    }

    /**
     * @OA\Put(
     *     path="/api/support/tickets/{id}",
     *     summary="Mettre à jour un ticket de support",
     *     tags={"Support"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du ticket",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string"),
     *             @OA\Property(property="priority", type="string"),
     *             @OA\Property(property="assigned_to", type="integer"),
     *             @OA\Property(property="admin_notes", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ticket de support mis à jour avec succès"
     *     ),
     *     @OA\Response(response=404, description="Ticket non trouvé")
     * )
     */
    public function updateTicket(Request $request, $id)
    {
        $ticket = SupportTicket::findOrFail($id);

        // Seuls les admins peuvent modifier les tickets
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|in:open,in_progress,resolved,closed',
            'priority' => 'sometimes|in:low,medium,high,urgent',
            'assigned_to' => 'sometimes|exists:users,id',
            'admin_notes' => 'sometimes|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $ticket->update($request->all());

        return response()->json($ticket);
    }

    /**
     * @OA\Delete(
     *     path="/api/support/tickets/{id}",
     *     summary="Supprimer un ticket de support",
     *     tags={"Support"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du ticket",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ticket de support supprimé avec succès"
     *     ),
     *     @OA\Response(response=404, description="Ticket non trouvé")
     * )
     */
    public function deleteTicket($id)
    {
        $ticket = SupportTicket::findOrFail($id);

        // Seuls les admins peuvent supprimer les tickets
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $ticket->delete();

        return response()->json(['message' => 'Ticket de support supprimé avec succès']);
    }

    /**
     * @OA\Get(
     *     path="/api/support/faq",
     *     summary="Obtenir la FAQ",
     *     tags={"Support"},
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Filtrer par catégorie",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Rechercher dans la FAQ",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="FAQ récupérée avec succès"
     *     )
     * )
     */
    public function getFAQ(Request $request)
    {
        $query = FAQ::where('is_active', true);

        // Filtre par catégorie
        if ($request->has('category')) {
            $query->where('category', $request->get('category'));
        }

        // Recherche
        if ($request->has('search')) {
            $searchTerm = $request->get('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('question', 'like', "%{$searchTerm}%")
                  ->orWhere('answer', 'like', "%{$searchTerm}%");
            });
        }

        $faqs = $query->orderBy('order', 'asc')->get();

        return response()->json($faqs);
    }

    /**
     * @OA\Post(
     *     path="/api/support/faq",
     *     summary="Créer une nouvelle entrée FAQ",
     *     tags={"Support"},
     *     security={{"BearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="question", type="string"),
     *             @OA\Property(property="answer", type="string"),
     *             @OA\Property(property="category", type="string"),
     *             @OA\Property(property="order", type="integer"),
     *             @OA\Property(property="is_active", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="FAQ créée avec succès"
     *     ),
     *     @OA\Response(response=422, description="Données invalides")
     * )
     */
    public function createFAQ(Request $request)
    {
        // Seuls les admins peuvent créer des FAQ
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $validator = Validator::make($request->all(), [
            'question' => 'required|string|max:500',
            'answer' => 'required|string|max:2000',
            'category' => 'required|string|max:100',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $faq = FAQ::create($request->all());

        return response()->json($faq, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/support/faq/{id}",
     *     summary="Mettre à jour une entrée FAQ",
     *     tags={"Support"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la FAQ",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="question", type="string"),
     *             @OA\Property(property="answer", type="string"),
     *             @OA\Property(property="category", type="string"),
     *             @OA\Property(property="order", type="integer"),
     *             @OA\Property(property="is_active", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="FAQ mise à jour avec succès"
     *     ),
     *     @OA\Response(response=404, description="FAQ non trouvée")
     * )
     */
    public function updateFAQ(Request $request, $id)
    {
        $faq = FAQ::findOrFail($id);

        // Seuls les admins peuvent modifier les FAQ
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $validator = Validator::make($request->all(), [
            'question' => 'sometimes|string|max:500',
            'answer' => 'sometimes|string|max:2000',
            'category' => 'sometimes|string|max:100',
            'order' => 'sometimes|integer|min:0',
            'is_active' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $faq->update($request->all());

        return response()->json($faq);
    }

    /**
     * @OA\Delete(
     *     path="/api/support/faq/{id}",
     *     summary="Supprimer une entrée FAQ",
     *     tags={"Support"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la FAQ",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="FAQ supprimée avec succès"
     *     ),
     *     @OA\Response(response=404, description="FAQ non trouvée")
     * )
     */
    public function deleteFAQ($id)
    {
        $faq = FAQ::findOrFail($id);

        // Seuls les admins peuvent supprimer les FAQ
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $faq->delete();

        return response()->json(['message' => 'FAQ supprimée avec succès']);
    }

    /**
     * @OA\Get(
     *     path="/api/support/categories",
     *     summary="Obtenir les catégories de support",
     *     tags={"Support"},
     *     @OA\Response(
     *         response=200,
     *         description="Catégories récupérées avec succès"
     *     )
     * )
     */
    public function getCategories()
    {
        $categories = [
            'technical' => 'Problèmes techniques',
            'billing' => 'Facturation et paiements',
            'course' => 'Cours et contenu',
            'general' => 'Questions générales'
        ];

        return response()->json($categories);
    }
}
