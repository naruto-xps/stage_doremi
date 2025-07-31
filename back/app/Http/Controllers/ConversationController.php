<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/students/{studentId}/conversations",
     *     summary="Lister les conversations d'un élève",
     *     tags={"Conversations"},
     *     @OA\Parameter(
     *         name="studentId",
     *         in="path",
     *         required=true,
     *         description="ID de l'élève",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des conversations de l'élève"
     *     )
     * )
     */
    public function index($studentId)
    {
        $conversations = Conversation::where('student_id', $studentId)->get();
        return response()->json($conversations);
    }

    /**
     * @OA\Post(
     *     path="/api/conversations",
     *     summary="Créer une nouvelle conversation",
     *     tags={"Conversations"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"student_id", "subject", "message"},
     *             @OA\Property(property="student_id", type="integer", example=1),
     *             @OA\Property(property="subject", type="string", example="Demande de réinscription"),
     *             @OA\Property(property="message", type="string", example="Bonjour, je souhaite me réinscrire pour l'année prochaine.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Conversation créée avec succès"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $conversation = Conversation::create($request->all());
        return response()->json($conversation, 201);
    }
}
