<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/courses",
     *     summary="Lister les cours avec filtres optionnels",
     *     tags={"Cours"},
     *     @OA\Parameter(
     *         name="theme",
     *         in="query",
     *         description="Filtrer par thème",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="level",
     *         in="query",
     *         description="Filtrer par niveau",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="school",
     *         in="query",
     *         description="Filtrer par école",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des cours filtrés"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = Course::query();

        if ($request->has('theme')) {
            $query->where('theme', $request->theme);
        }

        if ($request->has('level')) {
            $query->where('level', $request->level);
        }

        if ($request->has('school')) {
            $query->where('school', $request->school);
        }

        $courses = $query->get();

        return response()->json($courses);
    }

    /**
     * @OA\Get(
     *     path="/api/courses/{id}",
     *     summary="Afficher un cours avec ses chapitres",
     *     tags={"Cours"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du cours",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails du cours avec chapitres"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cours non trouvé"
     *     )
     * )
     */
    public function show($id)
    {
        $course = Course::with('chapters')->findOrFail($id);
        return response()->json($course);
    }

    /**
     * @OA\Post(
     *     path="/api/courses",
     *     summary="Créer un nouveau cours",
     *     tags={"Cours"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "theme", "level", "school"},
     *             @OA\Property(property="title", type="string", example="Mathématiques 101"),
     *             @OA\Property(property="theme", type="string", example="Mathématiques"),
     *             @OA\Property(property="level", type="string", example="Licence"),
     *             @OA\Property(property="school", type="string", example="Université DoReMi")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Cours créé avec succès"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $course = Course::create($request->all());
        return response()->json($course, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/courses/{id}",
     *     summary="Mettre à jour un cours",
     *     tags={"Cours"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du cours à mettre à jour",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Physique 101"),
     *             @OA\Property(property="theme", type="string", example="Physique"),
     *             @OA\Property(property="level", type="string", example="Licence"),
     *             @OA\Property(property="school", type="string", example="Université DoReMi")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cours mis à jour avec succès"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        $course->update($request->all());
        return response()->json($course);
    }

    /**
     * @OA\Delete(
     *     path="/api/courses/{id}",
     *     summary="Supprimer un cours",
     *     tags={"Cours"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du cours à supprimer",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cours supprimé avec succès"
     *     )
     * )
     */
    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();
        return response()->json(['message' => 'Course deleted successfully']);
    }
}
