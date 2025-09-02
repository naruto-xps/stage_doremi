<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Progression;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Certificats",
 *     description="API pour la gestion des certificats et récompenses"
 * )
 */
class CertificateController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/certificates/{userId}",
     *     summary="Obtenir les certificats d'un utilisateur",
     *     tags={"Certificats"},
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
     *         description="Certificats récupérés avec succès"
     *     ),
     *     @OA\Response(response=403, description="Accès refusé")
     * )
     */
    public function index($userId)
    {
        if (Auth::id() != $userId && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $certificates = Certificate::where('user_id', $userId)
            ->with(['course', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($certificates);
    }

    /**
     * @OA\Post(
     *     path="/api/certificates",
     *     summary="Créer un certificat pour un utilisateur",
     *     tags={"Certificats"},
     *     security={{"BearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="course_id", type="integer"),
     *             @OA\Property(property="score", type="integer"),
     *             @OA\Property(property="completion_date", type="string", format="date"),
     *             @OA\Property(property="valid_until", type="string", format="date"),
     *             @OA\Property(property="metadata", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Certificat créé avec succès"
     *     ),
     *     @OA\Response(response=422, description="Données invalides")
     * )
     */
    public function store(Request $request)
    {
        // Seuls les admins et les formateurs peuvent créer des certificats
        if (!in_array(Auth::user()->role, ['admin', 'teacher'])) {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'score' => 'required|integer|min:0|max:100',
            'completion_date' => 'required|date',
            'valid_until' => 'nullable|date|after:completion_date',
            'metadata' => 'nullable|json'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Vérifier que l'utilisateur a terminé le cours
        $progression = Progression::where('user_id', $request->user_id)
            ->where('course_id', $request->course_id)
            ->where('progress', 100)
            ->first();

        if (!$progression) {
            return response()->json(['message' => 'L\'utilisateur n\'a pas terminé ce cours'], 422);
        }

        // Vérifier qu'un certificat n'existe pas déjà
        $existingCertificate = Certificate::where('user_id', $request->user_id)
            ->where('course_id', $request->course_id)
            ->first();

        if ($existingCertificate) {
            return response()->json(['message' => 'Un certificat existe déjà pour ce cours'], 422);
        }

        $certificate = Certificate::create($request->all());

        return response()->json($certificate, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/certificates/{id}",
     *     summary="Obtenir un certificat spécifique",
     *     tags={"Certificats"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du certificat",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Certificat récupéré avec succès"
     *     ),
     *     @OA\Response(response=404, description="Certificat non trouvé")
     * )
     */
    public function show($id)
    {
        $certificate = Certificate::with(['course', 'user'])->findOrFail($id);

        // Vérifier que l'utilisateur connecté peut voir ce certificat
        if (Auth::id() != $certificate->user_id && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        return response()->json($certificate);
    }

    /**
     * @OA\Delete(
     *     path="/api/certificates/{id}",
     *     summary="Supprimer un certificat",
     *     tags={"Certificats"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du certificat",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Certificat supprimé avec succès"
     *     ),
     *     @OA\Response(response=404, description="Certificat non trouvé")
     * )
     */
    public function destroy($id)
    {
        $certificate = Certificate::findOrFail($id);

        // Seuls les admins peuvent supprimer des certificats
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $certificate->delete();

        return response()->json(['message' => 'Certificat supprimé avec succès']);
    }

    /**
     * @OA\Get(
     *     path="/api/certificates/validate/{certificateId}",
     *     summary="Valider un certificat",
     *     tags={"Certificats"},
     *     @OA\Parameter(
     *         name="certificateId",
     *         in="path",
     *         description="ID du certificat à valider",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Certificat validé avec succès"
     *     ),
     *     @OA\Response(response=404, description="Certificat non trouvé")
     * )
     */
    public function validate($certificateId)
    {
        $certificate = Certificate::with(['course', 'user'])->findOrFail($certificateId);

        // Vérifier si le certificat est encore valide
        $isValid = true;
        $message = 'Certificat valide';

        if ($certificate->valid_until && now()->isAfter($certificate->valid_until)) {
            $isValid = false;
            $message = 'Certificat expiré';
        }

        return response()->json([
            'certificate' => $certificate,
            'is_valid' => $isValid,
            'message' => $message,
            'validated_at' => now()
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/certificates/stats/{userId}",
     *     summary="Obtenir les statistiques des certificats d'un utilisateur",
     *     tags={"Certificats"},
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
     *         description="Statistiques récupérées avec succès"
     *     )
     * )
     */
    public function getStats($userId)
    {
        if (Auth::id() != $userId && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $certificates = Certificate::where('user_id', $userId)->get();

        $stats = [
            'total_certificates' => $certificates->count(),
            'valid_certificates' => $certificates->filter(function($cert) {
                return !$cert->valid_until || now()->isBefore($cert->valid_until);
            })->count(),
            'expired_certificates' => $certificates->filter(function($cert) {
                return $cert->valid_until && now()->isAfter($cert->valid_until);
            })->count(),
            'average_score' => $certificates->avg('score'),
            'certificates_by_year' => $certificates->groupBy(function($cert) {
                return $cert->created_at->format('Y');
            })->map->count()
        ];

        return response()->json($stats);
    }
}
