<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Document;
use App\Models\Internship;
use App\Models\News;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Recherche",
 *     description="API pour la recherche globale et avancée"
 * )
 */
class SearchController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/search",
     *     summary="Recherche globale sur toutes les ressources",
     *     tags={"Recherche"},
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Terme de recherche",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Type de ressource (courses, documents, internships, news, videos)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Nombre de résultats par type",
     *         required=false,
     *         @OA\Schema(type="integer", default=5)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Résultats de recherche"
     *     )
     * )
     */
    public function globalSearch(Request $request)
    {
        $query = $request->get('q');
        $type = $request->get('type');
        $limit = $request->get('limit', 5);

        if (!$query) {
            return response()->json(['message' => 'Terme de recherche requis'], 400);
        }

        $results = [];

        // Recherche dans les cours
        if (!$type || $type === 'courses') {
            $courses = Course::where('title', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->orWhere('category', 'like', "%{$query}%")
                ->orWhere('instructor', 'like', "%{$query}%")
                ->limit($limit)
                ->get();

            $results['courses'] = $courses;
        }

        // Recherche dans les documents
        if (!$type || $type === 'documents') {
            $documents = Document::where('title', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->orWhere('author', 'like', "%{$query}%")
                ->orWhere('category', 'like', "%{$query}%")
                ->orWhere('genre', 'like', "%{$query}%")
                ->limit($limit)
                ->get();

            $results['documents'] = $documents;
        }

        // Recherche dans les stages
        if (!$type || $type === 'internships') {
            $internships = Internship::where('title', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->orWhere('company', 'like', "%{$query}%")
                ->orWhere('location', 'like', "%{$query}%")
                ->limit($limit)
                ->get();

            $results['internships'] = $internships;
        }

        // Recherche dans les actualités
        if (!$type || $type === 'news') {
            $news = News::where('title', 'like', "%{$query}%")
                ->orWhere('content', 'like', "%{$query}%")
                ->orWhere('category', 'like', "%{$query}%")
                ->limit($limit)
                ->get();

            $results['news'] = $news;
        }

        // Recherche dans les vidéos
        if (!$type || $type === 'videos') {
            $videos = Video::where('title', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->orWhere('category', 'like', "%{$query}%")
                ->limit($limit)
                ->get();

            $results['videos'] = $videos;
        }

        // Statistiques des résultats
        $totalResults = array_sum(array_map('count', $results));

        return response()->json([
            'query' => $query,
            'total_results' => $totalResults,
            'results' => $results
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/search/courses",
     *     summary="Recherche avancée de cours",
     *     tags={"Recherche"},
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Terme de recherche",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Catégorie de cours",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="level",
     *         in="query",
     *         description="Niveau de difficulté",
     *         required=false,
     *         @OA\Schema(type="string", enum={"beginner", "intermediate", "advanced"})
     *     ),
     *     @OA\Parameter(
     *         name="education_level",
     *         in="query",
     *         description="Niveau d'éducation",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="instructor",
     *         in="query",
     *         description="Nom de l'instructeur",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="is_premium",
     *         in="query",
     *         description="Cours premium uniquement",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="min_rating",
     *         in="query",
     *         description="Note minimale",
     *         required=false,
     *         @OA\Schema(type="number", minimum=0, maximum=5)
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Tri (rating, students_count, created_at, title)",
     *         required=false,
     *         @OA\Schema(type="string", default="rating")
     *     ),
     *     @OA\Parameter(
     *         name="order",
     *         in="query",
     *         description="Ordre (asc, desc)",
     *         required=false,
     *         @OA\Schema(type="string", default="desc")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Numéro de page",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Résultats de recherche de cours"
     *     )
     * )
     */
    public function searchCourses(Request $request)
    {
        $query = Course::query();

        // Recherche textuelle
        if ($request->has('q')) {
            $searchTerm = $request->get('q');
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('instructor', 'like', "%{$searchTerm}%");
            });
        }

        // Filtres
        if ($request->has('category')) {
            $query->where('category', $request->get('category'));
        }

        if ($request->has('level')) {
            $query->where('level', $request->get('level'));
        }

        if ($request->has('education_level')) {
            $query->where('education_level', $request->get('education_level'));
        }

        if ($request->has('instructor')) {
            $query->where('instructor', 'like', "%{$request->get('instructor')}%");
        }

        if ($request->has('is_premium')) {
            $query->where('is_premium', $request->get('is_premium'));
        }

        if ($request->has('min_rating')) {
            $query->where('rating', '>=', $request->get('min_rating'));
        }

        // Tri
        $sort = $request->get('sort', 'rating');
        $order = $request->get('order', 'desc');
        
        if (in_array($sort, ['rating', 'students_count', 'created_at', 'title'])) {
            $query->orderBy($sort, $order);
        }

        // Pagination
        $perPage = 12;
        $courses = $query->paginate($perPage);

        return response()->json($courses);
    }

    /**
     * @OA\Get(
     *     path="/api/search/documents",
     *     summary="Recherche avancée de documents",
     *     tags={"Recherche"},
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Terme de recherche",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Type de document",
     *         required=false,
     *         @OA\Schema(type="string", enum={"pdf", "doc", "ppt", "video", "audio", "book"})
     *     ),
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Catégorie",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="author",
     *         in="query",
     *         description="Auteur",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="is_premium",
     *         in="query",
     *         description="Documents premium uniquement",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="min_rating",
     *         in="query",
     *         description="Note minimale",
     *         required=false,
     *         @OA\Schema(type="number", minimum=0, maximum=5)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Résultats de recherche de documents"
     *     )
     * )
     */
    public function searchDocuments(Request $request)
    {
        $query = Document::where('status', 'published');

        // Recherche textuelle
        if ($request->has('q')) {
            $searchTerm = $request->get('q');
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('author', 'like', "%{$searchTerm}%");
            });
        }

        // Filtres
        if ($request->has('type')) {
            $query->where('type', $request->get('type'));
        }

        if ($request->has('category')) {
            $query->where('category', $request->get('category'));
        }

        if ($request->has('author')) {
            $query->where('author', 'like', "%{$request->get('author')}%");
        }

        if ($request->has('is_premium')) {
            $query->where('is_premium', $request->get('is_premium'));
        }

        if ($request->has('min_rating')) {
            $query->where('rating', '>=', $request->get('min_rating'));
        }

        // Tri par popularité
        $query->orderBy('views', 'desc')
               ->orderBy('download_count', 'desc');

        $documents = $query->paginate(12);

        return response()->json($documents);
    }

    /**
     * @OA\Get(
     *     path="/api/search/internships",
     *     summary="Recherche avancée de stages",
     *     tags={"Recherche"},
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Terme de recherche",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="location",
     *         in="query",
     *         description="Localisation",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Type de stage",
     *         required=false,
     *         @OA\Schema(type="string", enum={"stage", "alternance", "emploi"})
     *     ),
     *     @OA\Parameter(
     *         name="remote",
     *         in="query",
     *         description="Télétravail possible",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="is_premium",
     *         in="query",
     *         description="Offres premium uniquement",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Résultats de recherche de stages"
     *     )
     * )
     */
    public function searchInternships(Request $request)
    {
        $query = Internship::query();

        // Recherche textuelle
        if ($request->has('q')) {
            $searchTerm = $request->get('q');
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('company', 'like', "%{$searchTerm}%");
            });
        }

        // Filtres
        if ($request->has('location')) {
            $query->where('location', 'like', "%{$request->get('location')}%");
        }

        if ($request->has('type')) {
            $query->where('type', $request->get('type'));
        }

        if ($request->has('remote')) {
            $query->where('remote', $request->get('remote'));
        }

        if ($request->has('is_premium')) {
            $query->where('is_premium', $request->get('is_premium'));
        }

        // Tri par date de publication
        $query->orderBy('created_at', 'desc');

        $internships = $query->paginate(12);

        return response()->json($internships);
    }

    /**
     * @OA\Get(
     *     path="/api/search/suggestions",
     *     summary="Obtenir des suggestions de recherche",
     *     tags={"Recherche"},
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Terme de recherche partiel",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Suggestions de recherche"
     *     )
     * )
     */
    public function getSuggestions(Request $request)
    {
        $query = $request->get('q');
        
        if (strlen($query) < 2) {
            return response()->json(['suggestions' => []]);
        }

        $suggestions = [];

        // Suggestions de cours
        $courseSuggestions = Course::where('title', 'like', "%{$query}%")
            ->orWhere('category', 'like', "%{$query}%")
            ->limit(3)
            ->pluck('title')
            ->toArray();

        $suggestions = array_merge($suggestions, $courseSuggestions);

        // Suggestions de catégories
        $categorySuggestions = Course::where('category', 'like', "%{$query}%")
            ->distinct()
            ->limit(2)
            ->pluck('category')
            ->toArray();

        $suggestions = array_merge($suggestions, $categorySuggestions);

        // Suggestions d'instructeurs
        $instructorSuggestions = Course::where('instructor', 'like', "%{$query}%")
            ->distinct()
            ->limit(2)
            ->pluck('instructor')
            ->toArray();

        $suggestions = array_merge($suggestions, $instructorSuggestions);

        // Supprimer les doublons et limiter
        $suggestions = array_unique($suggestions);
        $suggestions = array_slice($suggestions, 0, 8);

        return response()->json(['suggestions' => $suggestions]);
    }
}
