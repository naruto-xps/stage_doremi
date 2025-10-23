<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Notifications",
 *     description="API pour la gestion des notifications"
 * )
 */
class NotificationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/notifications/{userId}",
     *     summary="Obtenir les notifications d'un utilisateur",
     *     tags={"Notifications"},
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
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Nombre d'éléments par page",
     *         required=false,
     *         @OA\Schema(type="integer", default=20)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notifications récupérées avec succès"
     *     ),
     *     @OA\Response(response=403, description="Accès refusé")
     * )
     */
    public function index($userId, Request $request)
    {
        // Vérifier que l'utilisateur connecté peut accéder à ces données
        if (Auth::id() != $userId && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $page = $request->get('page', 1);
        $limit = $request->get('limit', 20);

        $notifications = Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($limit, ['*'], 'page', $page);

        return response()->json($notifications);
    }

    /**
     * @OA\Post(
     *     path="/api/notifications",
     *     summary="Créer une nouvelle notification",
     *     tags={"Notifications"},
     *     security={{"BearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="type", type="string", enum={"info", "success", "warning", "error", "course", "message", "system"}),
     *             @OA\Property(property="priority", type="string", enum={"low", "medium", "high", "urgent"}),
     *             @OA\Property(property="category", type="string", enum={"course", "message", "system", "promotion", "reminder"}),
     *             @OA\Property(property="action_url", type="string"),
     *             @OA\Property(property="action_text", type="string"),
     *             @OA\Property(property="metadata", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Notification créée avec succès"
     *     ),
     *     @OA\Response(response=422, description="Données invalides")
     * )
     */
    public function store(Request $request)
    {
        // Seuls les admins et les formateurs peuvent créer des notifications
        if (!in_array(Auth::user()->role, ['admin', 'teacher'])) {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'type' => 'required|in:info,success,warning,error,course,message,system',
            'priority' => 'required|in:low,medium,high,urgent',
            'category' => 'required|in:course,message,system,promotion,reminder',
            'action_url' => 'nullable|url',
            'action_text' => 'nullable|string|max:100',
            'metadata' => 'nullable|json'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $notification = Notification::create($request->all());

        return response()->json($notification, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/notifications/{id}/read",
     *     summary="Marquer une notification comme lue",
     *     tags={"Notifications"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la notification",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notification marquée comme lue"
     *     ),
     *     @OA\Response(response=404, description="Notification non trouvée")
     * )
     */
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);

        // Vérifier que l'utilisateur connecté peut modifier cette notification
        if (Auth::id() != $notification->user_id && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $notification->update(['is_read' => true]);

        return response()->json(['message' => 'Notification marquée comme lue']);
    }

    /**
     * @OA\Put(
     *     path="/api/notifications/{id}/archive",
     *     summary="Archiver une notification",
     *     tags={"Notifications"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la notification",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notification archivée"
     *     ),
     *     @OA\Response(response=404, description="Notification non trouvée")
     * )
     */
    public function archive($id)
    {
        $notification = Notification::findOrFail($id);

        if (Auth::id() != $notification->user_id && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $notification->update(['is_archived' => true]);

        return response()->json(['message' => 'Notification archivée']);
    }

    /**
     * @OA\Delete(
     *     path="/api/notifications/{id}",
     *     summary="Supprimer une notification",
     *     tags={"Notifications"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la notification",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notification supprimée"
     *     ),
     *     @OA\Response(response=404, description="Notification non trouvée")
     * )
     */
    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);

        if (Auth::id() != $notification->user_id && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $notification->delete();

        return response()->json(['message' => 'Notification supprimée']);
    }

    /**
     * @OA\Get(
     *     path="/api/notifications/unread-count/{userId}",
     *     summary="Obtenir le nombre de notifications non lues d'un utilisateur",
     *     tags={"Notifications"},
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
     *         description="Nombre de notifications non lues"
     *     )
     * )
     */
    public function getUnreadCount($userId)
    {
        if (Auth::id() != $userId && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $count = Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->where('is_archived', false)
            ->count();

        return response()->json(['unread_count' => $count]);
    }

    /**
     * @OA\Post(
     *     path="/api/notifications/mark-all-read/{userId}",
     *     summary="Marquer toutes les notifications d'un utilisateur comme lues",
     *     tags={"Notifications"},
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
     *         description="Toutes les notifications marquées comme lues"
     *     )
     * )
     */
    public function markAllAsRead($userId)
    {
        if (Auth::id() != $userId && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['message' => 'Toutes les notifications marquées comme lues']);
    }
}
