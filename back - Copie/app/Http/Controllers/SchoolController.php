<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/schools",
     *     summary="Liste de toutes les écoles",
     *     tags={"Écoles"},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des écoles",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/School"))
     *     )
     * )
     */
    public function index()
    {
        $schools = School::all();
        return response()->json($schools);
    }

    /**
     * @OA\Post(
     *     path="/api/schools",
     *     summary="Créer une nouvelle école",
     *     tags={"Écoles"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "address"},
     *             @OA\Property(property="name", type="string", example="École Sainte Marie"),
     *             @OA\Property(property="address", type="string", example="Dakar, Sénégal")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="École créée avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/School")
     *     )
     * )
     */
    public function store(Request $request)
    {
        $school = School::create($request->all());
        return response()->json($school, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/schools/{id}",
     *     summary="Afficher une école spécifique",
     *     tags={"Écoles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'école",
     *         required=true,
     *         @OA\Schema(type="integer", example=2)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails de l'école",
     *         @OA\JsonContent(ref="#/components/schemas/School")
     *     )
     * )
     */
    public function show($id)
    {
        $school = School::findOrFail($id);
        return response()->json($school);
    }

    /**
     * @OA\Put(
     *     path="/api/schools/{id}",
     *     summary="Mettre à jour une école",
     *     tags={"Écoles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'école",
     *         required=true,
     *         @OA\Schema(type="integer", example=2)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="École Saint Michel"),
     *             @OA\Property(property="address", type="string", example="Thiès, Sénégal")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="École mise à jour",
     *         @OA\JsonContent(ref="#/components/schemas/School")
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $school = School::findOrFail($id);
        $school->update($request->all());
        return response()->json($school);
    }

    /**
     * @OA\Delete(
     *     path="/api/schools/{id}",
     *     summary="Supprimer une école",
     *     tags={"Écoles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'école",
     *         required=true,
     *         @OA\Schema(type="integer", example=2)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="École supprimée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="School deleted successfully")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $school = School::findOrFail($id);
        $school->delete();
        return response()->json(['message' => 'School deleted successfully']);
    }
}
