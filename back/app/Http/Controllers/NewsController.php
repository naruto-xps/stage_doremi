<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class NewsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/news",
     *     summary="Lister toutes les actualités",
     *     tags={"Actualités"},
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Filtrer par catégorie",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="priority",
     *         in="query",
     *         description="Filtrer par priorité",
     *         required=false,
     *         @OA\Schema(type="string", enum={"low", "medium", "high"})
     *     ),
     *     @OA\Parameter(
     *         name="featured",
     *         in="query",
     *         description="Filtrer les actualités en vedette",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Recherche textuelle",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des actualités"
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = News::query();

        // Filtres
        if ($request->has('category')) {
            $query->byCategory($request->category);
        }

        if ($request->has('priority')) {
            $query->byPriority($request->priority);
        }

        if ($request->has('type')) {
            $query->byType($request->type);
        }

        if ($request->boolean('featured')) {
            $query->featured();
        }

        if ($request->boolean('urgent')) {
            $query->urgent();
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // Par défaut, afficher seulement les actualités publiées
        if (!$request->boolean('admin')) {
            $query->published()->notExpired();
        }

        // Tri
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $news = $query->paginate($perPage);

        return response()->json($news);
    }

    /**
     * @OA\Get(
     *     path="/api/news/{id}",
     *     summary="Afficher une actualité spécifique",
     *     tags={"Actualités"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'actualité",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails de l'actualité"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Actualité non trouvée"
     *     )
     * )
     */
    public function show($id): JsonResponse
    {
        $news = News::findOrFail($id);
        
        // Incrémenter le compteur de vues
        $news->incrementViews();
        
        return response()->json($news);
    }

    /**
     * @OA\Post(
     *     path="/api/news",
     *     summary="Créer une nouvelle actualité",
     *     tags={"Actualités"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "content"},
     *             @OA\Property(property="title", type="string", example="Nouvelle rentrée"),
     *             @OA\Property(property="content", type="string", example="La rentrée scolaire commence le 15 septembre."),
     *             @OA\Property(property="excerpt", type="string", example="Résumé de l'actualité"),
     *             @OA\Property(property="image", type="string", example="https://example.com/image.jpg"),
     *             @OA\Property(property="category", type="string", example="Éducation"),
     *             @OA\Property(property="priority", type="string", example="medium", enum={"low", "medium", "high"}),
     *             @OA\Property(property="is_featured", type="boolean", example=false),
     *             @OA\Property(property="tags", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="published_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Actualité créée avec succès"
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'author' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'read_time' => 'nullable|string|max:50',
            'type' => ['required', Rule::in(['news', 'scholarship', 'announcement'])],
            'priority' => ['nullable', Rule::in(['low', 'medium', 'high'])],
            'status' => ['nullable', Rule::in(['draft', 'published', 'archived'])],
            'is_featured' => 'boolean',
            'is_urgent' => 'boolean',
            'category' => 'nullable|string|max:100',
            'subcategory' => 'nullable|string|max:100',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'target_audience' => 'nullable|array',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:255',
            'gallery' => 'nullable|array',
            'gallery.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'published_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:published_at',
            'publish_schedule' => 'nullable|date|after:now'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        $data['author_id'] = Auth::id();
        $data['status'] = $data['status'] ?? 'draft';
        $data['priority'] = $data['priority'] ?? 'medium';

        // Traitement des images uploadées
        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage($request->file('image'), 'news');
        }

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $this->uploadImage($request->file('featured_image'), 'news/featured');
        }

        // Traitement de la galerie
        if ($request->hasFile('gallery')) {
            $galleryPaths = [];
            foreach ($request->file('gallery') as $galleryImage) {
                $galleryPaths[] = $this->uploadImage($galleryImage, 'news/gallery');
            }
            $data['gallery'] = $galleryPaths;
        }

        $news = News::create($data);

        return response()->json($news, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/news/{id}",
     *     summary="Mettre à jour une actualité",
     *     tags={"Actualités"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'actualité à mettre à jour",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Titre mis à jour"),
     *             @OA\Property(property="content", type="string", example="Contenu mis à jour de l'actualité.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Actualité mise à jour avec succès"
     *     )
     * )
     */
    public function update(Request $request, $id): JsonResponse
    {
        $news = News::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'excerpt' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'author' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'read_time' => 'nullable|string|max:50',
            'type' => ['sometimes', 'required', Rule::in(['news', 'scholarship', 'announcement'])],
            'priority' => ['nullable', Rule::in(['low', 'medium', 'high'])],
            'status' => ['nullable', Rule::in(['draft', 'published', 'archived'])],
            'is_featured' => 'boolean',
            'is_urgent' => 'boolean',
            'category' => 'nullable|string|max:100',
            'subcategory' => 'nullable|string|max:100',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'target_audience' => 'nullable|array',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:255',
            'gallery' => 'nullable|array',
            'gallery.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'published_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:published_at',
            'publish_schedule' => 'nullable|date|after:now'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();

        // Traitement des nouvelles images
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($news->image && !filter_var($news->image, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($news->image);
            }
            $data['image'] = $this->uploadImage($request->file('image'), 'news');
        }

        if ($request->hasFile('featured_image')) {
            // Supprimer l'ancienne image si elle existe
            if ($news->featured_image && !filter_var($news->featured_image, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($news->featured_image);
            }
            $data['featured_image'] = $this->uploadImage($request->file('featured_image'), 'news/featured');
        }

        // Traitement de la galerie
        if ($request->hasFile('gallery')) {
            $galleryPaths = [];
            foreach ($request->file('gallery') as $galleryImage) {
                $galleryPaths[] = $this->uploadImage($galleryImage, 'news/gallery');
            }
            $data['gallery'] = $galleryPaths;
        }

        $news->update($data);

        return response()->json($news);
    }

    /**
     * @OA\Delete(
     *     path="/api/news/{id}",
     *     summary="Supprimer une actualité",
     *     tags={"Actualités"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'actualité",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Actualité supprimée avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Actualité non trouvée"
     *     )
     * )
     */
    public function destroy($id): JsonResponse
    {
        $news = News::findOrFail($id);
        $news->delete();

        return response()->json(['message' => 'Actualité supprimée avec succès']);
    }

    /**
     * Actions spéciales
     */
    public function toggleFeatured($id): JsonResponse
    {
        $news = News::findOrFail($id);
        $news->toggleFeatured();

        return response()->json([
            'message' => 'Statut vedette modifié',
            'is_featured' => $news->is_featured
        ]);
    }

    public function toggleUrgent($id): JsonResponse
    {
        $news = News::findOrFail($id);
        $news->toggleUrgent();

        return response()->json([
            'message' => 'Statut urgent modifié',
            'is_urgent' => $news->is_urgent
        ]);
    }

    public function publish($id): JsonResponse
    {
        $news = News::findOrFail($id);
        
        if (!$news->canBePublished()) {
            return response()->json(['message' => 'Cette actualité ne peut pas être publiée'], 400);
        }

        $news->publish();

        return response()->json(['message' => 'Actualité publiée avec succès']);
    }

    public function archive($id): JsonResponse
    {
        $news = News::findOrFail($id);
        $news->archive();

        return response()->json(['message' => 'Actualité archivée avec succès']);
    }

    public function incrementLikes($id): JsonResponse
    {
        $news = News::findOrFail($id);
        $news->incrementLikes();

        return response()->json([
            'message' => 'Like ajouté',
            'likes_count' => $news->likes_count
        ]);
    }

    public function incrementShares($id): JsonResponse
    {
        $news = News::findOrFail($id);
        $news->incrementShares();

        return response()->json([
            'message' => 'Partage comptabilisé',
            'shares_count' => $news->shares_count
        ]);
    }

    /**
     * Statistiques
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total' => News::count(),
            'published' => News::published()->count(),
            'draft' => News::where('status', 'draft')->count(),
            'archived' => News::where('status', 'archived')->count(),
            'featured' => News::featured()->count(),
            'urgent' => News::urgent()->count(),
            'total_views' => News::sum('views_count'),
            'total_likes' => News::sum('likes_count'),
            'total_comments' => News::sum('comments_count'),
            'total_shares' => News::sum('shares_count'),
            'by_category' => News::selectRaw('category, COUNT(*) as count')
                                   ->whereNotNull('category')
                                   ->groupBy('category')
                                   ->get(),
            'by_priority' => News::selectRaw('priority, COUNT(*) as count')
                                   ->groupBy('priority')
                                   ->get()
        ];

        return response()->json($stats);
    }

    /**
     * Upload d'image
     */
    private function uploadImage($file, $path)
    {
        try {
            $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs($path, $fileName, 'public');
            
            return $filePath;
        } catch (\Exception $e) {
            throw new \Exception('Erreur lors de l\'upload de l\'image: ' . $e->getMessage());
        }
    }
}
