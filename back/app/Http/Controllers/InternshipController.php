<?php

namespace App\Http\Controllers;

use App\Models\Internship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class InternshipController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/internships",
     *     summary="Lister tous les stages",
     *     tags={"Stages"},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des stages"
     *     )
     * )
     */
    public function index()
    {
        $internships = Internship::with('recruiter')->get();
        return response()->json($internships);
    }

    /**
     * @OA\Get(
     *     path="/api/internships/{id}",
     *     summary="Afficher un stage spécifique",
     *     tags={"Stages"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du stage",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails du stage"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Stage non trouvé"
     *     )
     * )
     */
    public function show($id)
    {
        $internship = Internship::with('recruiter')->findOrFail($id);
        
        // Incrémenter le compteur de vues
        $internship->increment('views');
        
        return response()->json($internship);
    }

    /**
     * @OA\Post(
     *     path="/api/internships",
     *     summary="Créer un nouveau stage",
     *     tags={"Stages"},
     *     security={{"BearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "company", "description", "location", "recruiter_id"},
     *             @OA\Property(property="title", type="string", example="Stage Développement Web"),
     *             @OA\Property(property="company", type="string", example="Société Tech"),
     *             @OA\Property(property="description", type="string", example="Description du stage"),
     *             @OA\Property(property="location", type="string", example="Dakar"),
     *             @OA\Property(property="duration", type="string", example="6 mois"),
     *             @OA\Property(property="requirements", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="application_deadline", type="string", format="date"),
     *             @OA\Property(property="salary", type="string", example="150000 FCFA"),
     *             @OA\Property(property="type", type="string", enum={"stage", "alternance", "emploi"}),
     *             @OA\Property(property="remote", type="boolean"),
     *             @OA\Property(property="is_premium", type="boolean"),
     *             @OA\Property(property="tags", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="recruiter_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Stage créé avec succès"
     *     )
     * )
     */
    public function store(Request $request)
    {
        // Vérifier que l'utilisateur est connecté et est un recruteur
        if (!Auth::check() || Auth::user()->role !== 'recruiter') {
            return response()->json(['message' => 'Seuls les recruteurs peuvent créer des offres de stage'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'duration' => 'nullable|string|max:255',
            'requirements' => 'nullable|array',
            'requirements.*' => 'string|max:255',
            'application_deadline' => 'nullable|date|after:today',
            'salary' => 'nullable|string|max:255',
            'type' => 'nullable|in:stage,alternance,emploi',
            'remote' => 'nullable|boolean',
            'is_premium' => 'nullable|boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:100',
            'recruiter_id' => 'prohibited' // L'ID du recruteur est automatiquement défini
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $internship = Internship::create(array_merge($request->all(), [
            'recruiter_id' => Auth::id()
        ]));
        
        // Charger la relation recruiter
        $internship->load('recruiter');
        
        return response()->json($internship, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/internships/{id}",
     *     summary="Mettre à jour un stage",
     *     tags={"Stages"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du stage à mettre à jour",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="company", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="location", type="string"),
     *             @OA\Property(property="duration", type="string"),
     *             @OA\Property(property="requirements", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="application_deadline", type="string", format="date"),
     *             @OA\Property(property="salary", type="string"),
     *             @OA\Property(property="type", type="string"),
     *             @OA\Property(property="remote", type="boolean"),
     *             @OA\Property(property="is_premium", type="boolean"),
     *             @OA\Property(property="tags", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="status", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Stage mis à jour avec succès"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        // Vérifier que l'utilisateur est connecté et est un recruteur
        if (!Auth::check() || Auth::user()->role !== 'recruiter') {
            return response()->json(['message' => 'Seuls les recruteurs peuvent modifier des offres de stage'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'company' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'location' => 'sometimes|string|max:255',
            'duration' => 'nullable|string|max:255',
            'requirements' => 'nullable|array',
            'requirements.*' => 'string|max:255',
            'application_deadline' => 'nullable|date',
            'salary' => 'nullable|string|max:255',
            'type' => 'nullable|in:stage,alternance,emploi',
            'remote' => 'nullable|boolean',
            'is_premium' => 'nullable|boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:100',
            'status' => 'nullable|in:active,expired,closed'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $internship = Internship::findOrFail($id);
        
        // Vérifier que le recruteur connecté est le propriétaire de l'offre
        if ($internship->recruiter_id !== Auth::id()) {
            return response()->json(['message' => 'Vous ne pouvez modifier que vos propres offres de stage'], 403);
        }
        
        $internship->update($request->all());
        
        // Charger la relation recruiter
        $internship->load('recruiter');
        
        return response()->json($internship);
    }

    /**
     * @OA\Delete(
     *     path="/api/internships/{id}",
     *     summary="Supprimer un stage",
     *     tags={"Stages"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du stage à supprimer",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Stage supprimé avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Stage non trouvé"
     *     )
     * )
     */
    public function destroy($id)
    {
        // Vérifier que l'utilisateur est connecté et est un recruteur
        if (!Auth::check() || Auth::user()->role !== 'recruiter') {
            return response()->json(['message' => 'Seuls les recruteurs peuvent supprimer des offres de stage'], 403);
        }

        $internship = Internship::findOrFail($id);
        
        // Vérifier que le recruteur connecté est le propriétaire de l'offre
        if ($internship->recruiter_id !== Auth::id()) {
            return response()->json(['message' => 'Vous ne pouvez supprimer que vos propres offres de stage'], 403);
        }
        
        $internship->delete();
        return response()->json(['message' => 'Stage supprimé avec succès']);
    }

    /**
     * @OA\Get(
     *     path="/api/internships/my-offers",
     *     summary="Lister mes offres de stage (pour les recruteurs)",
     *     tags={"Stages"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des offres du recruteur connecté"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès refusé - Seuls les recruteurs peuvent accéder à cette ressource"
     *     )
     * )
     */
    public function myOffers()
    {
        // Vérifier que l'utilisateur est connecté et est un recruteur
        if (!Auth::check() || Auth::user()->role !== 'recruiter') {
            return response()->json(['message' => 'Seuls les recruteurs peuvent voir leurs offres de stage'], 403);
        }

        $internships = Internship::where('recruiter_id', Auth::id())
            ->with('recruiter')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($internships);
    }

    /**
     * @OA\Get(
     *     path="/api/internships/search",
     *     summary="Rechercher des stages",
     *     tags={"Stages"},
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Terme de recherche",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Type de stage",
     *         @OA\Schema(type="string", enum={"stage", "alternance", "emploi"})
     *     ),
     *     @OA\Parameter(
     *         name="location",
     *         in="query",
     *         description="Localisation",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="remote",
     *         in="query",
     *         description="Télétravail possible",
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Résultats de la recherche"
     *     )
     * )
     */
    public function search(Request $request)
    {
        $query = Internship::with('recruiter')->where('status', 'active');

        if ($request->has('q')) {
            $searchTerm = $request->q;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('company', 'like', "%{$searchTerm}%")
                  ->orWhere('location', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('location')) {
            $query->where('location', 'like', "%{$request->location}%");
        }

        if ($request->has('remote')) {
            $query->where('remote', $request->remote);
        }

        $internships = $query->orderBy('created_at', 'desc')->get();
        
        return response()->json($internships);
    }
}
