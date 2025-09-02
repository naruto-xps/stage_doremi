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

        // Filtres existants
        if ($request->has('theme')) {
            $query->where('theme', $request->theme);
        }

        if ($request->has('level')) {
            $query->where('level', $request->level);
        }

        if ($request->has('school')) {
            $query->where('school', $request->school);
        }

        // Nouveaux filtres
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('difficulty_level')) {
            $query->where('difficulty_level', $request->difficulty_level);
        }

        if ($request->has('education_level')) {
            $query->where('education_level', $request->education_level);
        }

        if ($request->has('is_premium')) {
            $query->where('is_premium', $request->boolean('is_premium'));
        }

        if ($request->has('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        if ($request->has('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        if ($request->has('rating_min')) {
            $query->where('rating', '>=', $request->rating_min);
        }

        // Tri
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (in_array($sortBy, ['title', 'rating', 'students_count', 'chapters_count', 'price', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $courses = $query->with('teacher')->get();

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
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'thumbnail' => 'nullable|string|url',
            'duration' => 'nullable|string|max:100',
            'chapters_count' => 'nullable|integer|min:0',
            'rating' => 'nullable|numeric|min:0|max:5',
            'students_count' => 'nullable|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'theme' => 'required|string|max:255',
            'level' => 'required|string|max:255',
            'difficulty_level' => 'nullable|string|in:beginner,intermediate,advanced',
            'category' => 'nullable|string|max:255',
            'education_level' => 'nullable|string|in:ecolier,collegien,lyceen,etudiant',
            'is_premium' => 'boolean',
            'school' => 'nullable|string|max:255',
        ]);

        // Ajouter automatiquement le teacher_id de l'utilisateur connecté
        $validated['teacher_id'] = $request->user()->id;
        
        // Valeurs par défaut
        $validated['chapters_count'] = $validated['chapters_count'] ?? 0;
        $validated['rating'] = $validated['rating'] ?? 0.00;
        $validated['students_count'] = $validated['students_count'] ?? 0;
        $validated['difficulty_level'] = $validated['difficulty_level'] ?? 'beginner';
        $validated['is_premium'] = $validated['is_premium'] ?? false;

        $course = Course::create($validated);
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
        
        // Vérifier que l'utilisateur est le propriétaire du cours
        if ($course->teacher_id !== $request->user()->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'thumbnail' => 'nullable|string|url',
            'duration' => 'nullable|string|max:100',
            'chapters_count' => 'nullable|integer|min:0',
            'rating' => 'nullable|numeric|min:0|max:5',
            'students_count' => 'nullable|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'theme' => 'sometimes|string|max:255',
            'level' => 'sometimes|string|max:255',
            'difficulty_level' => 'nullable|string|in:beginner,intermediate,advanced',
            'category' => 'nullable|string|max:255',
            'education_level' => 'nullable|string|in:ecolier,collegien,lyceen,etudiant',
            'is_premium' => 'boolean',
            'school' => 'nullable|string|max:255',
        ]);

        $course->update($validated);
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
