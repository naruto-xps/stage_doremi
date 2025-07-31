<?php

namespace App\Http\Controllers;

use App\Models\Progression;
use Illuminate\Http\Request;

class ProgressionController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/users/{userId}/courses/{courseId}/progression",
     *     summary="Voir la progression d'un utilisateur dans un cours",
     *     tags={"Progressions"},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="ID de l'utilisateur",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="courseId",
     *         in="path",
     *         description="ID du cours",
     *         required=true,
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Progression de l'utilisateur",
     *         @OA\JsonContent(ref="#/components/schemas/Progression")
     *     )
     * )
     */
    public function show($userId, $courseId)
    {
        $progression = Progression::where('user_id', $userId)->where('course_id', $courseId)->first();
        return response()->json($progression);
    }

    /**
     * @OA\Post(
     *     path="/api/progressions",
     *     summary="Créer une progression pour un utilisateur",
     *     tags={"Progressions"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "course_id", "percentage"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="course_id", type="integer", example=5),
     *             @OA\Property(property="percentage", type="number", format="float", example=35.5)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Progression créée avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/Progression")
     *     )
     * )
     */
    public function store(Request $request)
    {
        $progression = Progression::create($request->all());
        return response()->json($progression, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/users/{userId}/courses/{courseId}/progression",
     *     summary="Mettre à jour la progression d'un utilisateur",
     *     tags={"Progressions"},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="ID de l'utilisateur",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="courseId",
     *         in="path",
     *         description="ID du cours",
     *         required=true,
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="percentage", type="number", format="float", example=85.0)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Progression mise à jour",
     *         @OA\JsonContent(ref="#/components/schemas/Progression")
     *     )
     * )
     */
    public function update(Request $request, $userId, $courseId)
    {
        $progression = Progression::where('user_id', $userId)->where('course_id', $courseId)->first();
        $progression->update($request->all());
        return response()->json($progression);
    }
}
