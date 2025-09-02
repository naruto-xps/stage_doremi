<?php

namespace App\Http\Controllers;

use App\Models\InternshipRequest;
use App\Models\Internship;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        $requests = InternshipRequest::with(['internship', 'user'])
            ->where('user_id', $userId)
            ->get();
        return response()->json($requests);
    }

    /**
     * @OA\Get(
     *     path="/api/internship-requests",
     *     summary="Lister toutes les demandes de stage (admin)",
     *     tags={"Demandes de stage"},
     *     @OA\Response(
     *         response=200,
     *         description="Liste de toutes les demandes de stage"
     *     )
     * )
     */
    public function allRequests()
    {
        $requests = InternshipRequest::with(['internship', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
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
     *             required={"user_id", "internship_id", "motivation"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="internship_id", type="integer", example=5),
     *             @OA\Property(property="motivation", type="string", example="Motivation pour ce stage"),
     *             @OA\Property(property="user_name", type="string", example="Jean Dupont"),
     *             @OA\Property(property="user_email", type="string", example="jean@example.com"),
     *             @OA\Property(property="user_role", type="string", enum={"student", "instructor"}),
     *             @OA\Property(property="user_phone", type="string", example="+221 77 123 45 67"),
     *             @OA\Property(property="cv_file_data", type="string", example="base64_encoded_cv"),
     *             @OA\Property(property="cv_file_name", type="string", example="mon_cv.pdf"),
     *             @OA\Property(property="cv_file_size", type="integer", example=1024000),
     *             @OA\Property(property="cover_letter", type="string", example="Lettre de motivation")
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
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'internship_id' => 'required|exists:internships,id',
            'motivation' => 'required|string|max:1000',
            'user_name' => 'nullable|string|max:255',
            'user_email' => 'nullable|email|max:255',
            'user_role' => 'nullable|in:student,instructor',
            'user_phone' => 'nullable|string|max:255',
            'cv_file_data' => 'nullable|string',
            'cv_file_name' => 'nullable|string|max:255',
            'cv_file_size' => 'nullable|integer|min:0',
            'cover_letter' => 'nullable|string|max:2000'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Vérifier que l'utilisateur n'a pas déjà postulé pour ce stage
        $existingRequest = InternshipRequest::where('user_id', $request->user_id)
            ->where('internship_id', $request->internship_id)
            ->first();

        if ($existingRequest) {
            return response()->json([
                'message' => 'Vous avez déjà postulé pour ce stage'
            ], 409);
        }

        // Récupérer les informations utilisateur si non fournies
        if (!$request->user_name || !$request->user_email) {
            $user = User::find($request->user_id);
            if ($user) {
                $request->merge([
                    'user_name' => $request->user_name ?: $user->name,
                    'user_email' => $request->user_email ?: $user->email
                ]);
            }
        }

        $internshipRequest = InternshipRequest::create($request->all());
        
        // Charger les relations
        $internshipRequest->load(['internship', 'user']);
        
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
     *             @OA\Property(property="status", type="string", enum={"pending", "reviewed", "accepted", "rejected"}),
     *             @OA\Property(property="admin_notes", type="string", example="Notes de l'administrateur")
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
        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|in:pending,reviewed,accepted,rejected',
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $internshipRequest = InternshipRequest::findOrFail($id);
        $internshipRequest->update($request->all());
        
        // Charger les relations
        $internshipRequest->load(['internship', 'user']);
        
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
        return response()->json(['message' => 'Demande de stage supprimée avec succès']);
    }

    /**
     * @OA\Get(
     *     path="/api/internship-requests/{id}",
     *     summary="Afficher une demande de stage spécifique",
     *     tags={"Demandes de stage"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la demande de stage",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails de la demande de stage"
     *     )
     * )
     */
    public function show($id)
    {
        $internshipRequest = InternshipRequest::with(['internship', 'user'])
            ->findOrFail($id);
        return response()->json($internshipRequest);
    }
}
