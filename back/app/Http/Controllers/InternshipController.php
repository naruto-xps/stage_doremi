<?php

namespace App\Http\Controllers;

use App\Models\Internship;
use Illuminate\Http\Request;

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
        $internships = Internship::all();
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
        $internship = Internship::findOrFail($id);
        return response()->json($internship);
    }

    /**
     * @OA\Post(
     *     path="/api/internships",
     *     summary="Créer un nouveau stage",
     *     tags={"Stages"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "company", "description", "start_date", "end_date"},
     *             @OA\Property(property="title", type="string", example="Stage Développement Web"),
     *             @OA\Property(property="company", type="string", example="Société Tech"),
     *             @OA\Property(property="description", type="string", example="Stage pour développement d'applications web."),
     *             @OA\Property(property="start_date", type="string", format="date", example="2025-09-01"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2025-12-31")
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
        $internship = Internship::create($request->all());
        return response()->json($internship, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/internships/{id}",
     *     summary="Mettre à jour un stage",
     *     tags={"Stages"},
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
     *             @OA\Property(property="title", type="string", example="Stage Dev Web mis à jour"),
     *             @OA\Property(property="company", type="string", example="Nouvelle Société"),
     *             @OA\Property(property="description", type="string", example="Description mise à jour."),
     *             @OA\Property(property="start_date", type="string", format="date", example="2025-09-01"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2025-12-31")
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
        $internship = Internship::findOrFail($id);
        $internship->update($request->all());
        return response()->json($internship);
    }

    /**
     * @OA\Delete(
     *     path="/api/internships/{id}",
     *     summary="Supprimer un stage",
     *     tags={"Stages"},
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
        $internship = Internship::findOrFail($id);
        $internship->delete();
        return response()->json(['message' => 'Internship deleted successfully']);
    }
}
