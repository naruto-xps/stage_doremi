<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/conversations/{conversationId}/messages",
     *     summary="Récupérer les messages d'une conversation",
     *     tags={"Messages"},
     *     @OA\Parameter(
     *         name="conversationId",
     *         in="path",
     *         description="ID de la conversation",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des messages",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Message"))
     *     )
     * )
     */
    public function index($conversationId)
    {
        $messages = Message::where('conversation_id', $conversationId)->get();
        return response()->json($messages);
    }

    /**
     * @OA\Post(
     *     path="/api/messages",
     *     summary="Envoyer un message dans une conversation",
     *     tags={"Messages"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"conversation_id", "sender_id", "content"},
     *             @OA\Property(property="conversation_id", type="integer", example=1),
     *             @OA\Property(property="sender_id", type="integer", example=3),
     *             @OA\Property(property="content", type="string", example="Bonjour, j’ai une question.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Message envoyé",
     *         @OA\JsonContent(ref="#/components/schemas/Message")
     *     )
     * )
     */
    public function store(Request $request)
    {
        $message = Message::create($request->all());
        return response()->json($message, 201);
    }
}
