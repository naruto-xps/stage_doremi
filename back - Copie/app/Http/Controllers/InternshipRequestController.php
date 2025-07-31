<?php

namespace App\Http\Controllers;

use App\Models\InternshipRequest;
use Illuminate\Http\Request;

class InternshipRequestController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/users/{userId}/internship-requests",
     *     summary="Lister les demandes de stage d'un utilisateur",
     *     tags={"Demandes de stage"},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="ID de l'utilisateur",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des demandes de stage"
     *     )
     * )
     */
    public function index($userId)
    {
        $requests = InternshipRequest::where('user_id', $userId)->get();
        return response()->json($requests);
    }

    /**
     * @OA\Post(
     *     path="/api/internship-requests",
     *     summary="Créer une nouvelle demande de stage",
     *     tags={"Demandes de stage"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "internship_id", "status"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="internship_id", type="integer", example=5),
     *             @OA\Property(property="status", type="string", example="pending")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Demande de stage créée avec succès"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $internshipRequest = InternshipRequest::create($request->all());
        return response()->json($internshipRequest, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/internship-requests/{id}",
     *     summary="Mettre à jour une demande de stage",
     *     tags={"Demandes de stage"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la demande de stage",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="approved")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Demande de stage mise à jour avec succès"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $internshipRequest = InternshipRequest::findOrFail($id);
        $internshipRequest->update($request->all());
        return response()->json($internshipRequest);
    }

    /**
     * @OA\Delete(
     *     path="/api/internship-requests/{id}",
     *     summary="Supprimer une demande de stage",
     *     tags={"Demandes de stage"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la demande de stage à supprimer",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Demande de stage supprimée avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Demande de stage non trouvée"
     *     )
     * )
     */
    public function destroy($id)
    {
        $internshipRequest = InternshipRequest::findOrFail($id);
        $internshipRequest->delete();
        return response()->json(['message' => 'Internship request deleted successfully']);
    }
}
