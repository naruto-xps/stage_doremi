<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\VideoView;
use App\Models\VideoLike;
use App\Models\VideoComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class VideoController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/videos",
     *     summary="Lister toutes les vidéos",
     *     tags={"Vidéos"},
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Filtrer par catégorie",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="difficulty",
     *         in="query",
     *         description="Filtrer par niveau de difficulté",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="instructor",
     *         in="query",
     *         description="Filtrer par instructeur",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des vidéos"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = Video::with(['instructor'])
            ->published()
            ->orderBy('created_at', 'desc');

        // Filtres
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('difficulty')) {
            $query->where('difficulty_level', $request->difficulty);
        }

        if ($request->has('instructor')) {
            $query->where('instructor_id', $request->instructor);
        }

        $videos = $query->paginate(12);
        return response()->json($videos);
    }

    /**
     * @OA\Get(
     *     path="/api/videos/{id}",
     *     summary="Afficher une vidéo spécifique",
     *     tags={"Vidéos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la vidéo",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails de la vidéo"
     *     )
     * )
     */
    public function show($id)
    {
        $video = Video::with(['instructor', 'comments.user', 'likes'])
            ->published()
            ->findOrFail($id);

        // Incrémenter le compteur de vues
        $video->incrementViews();

        // Enregistrer la vue
        VideoView::create([
            'video_id' => $id,
            'user_id' => Auth::id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'viewed_at' => now()
        ]);

        return response()->json($video);
    }

    /**
     * @OA\Post(
     *     path="/api/videos",
     *     summary="Créer une nouvelle vidéo",
     *     tags={"Vidéos"},
     *     security={{"BearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "description", "file_url"},
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="file_url", type="string"),
     *             @OA\Property(property="file_name", type="string"),
     *             @OA\Property(property="file_size", type="integer"),
     *             @OA\Property(property="duration", type="integer"),
     *             @OA\Property(property="thumbnail", type="string"),
     *             @OA\Property(property="category", type="string"),
     *             @OA\Property(property="difficulty_level", type="string"),
     *             @OA\Property(property="is_premium", type="boolean"),
     *             @OA\Property(property="price", type="number"),
     *             @OA\Property(property="tags", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Vidéo créée avec succès"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'file_url' => 'required|string',
            'file_name' => 'required|string|max:255',
            'file_size' => 'required|integer|min:0',
            'duration' => 'required|integer|min:0',
            'thumbnail' => 'nullable|string',
            'category' => 'required|string|max:100',
            'difficulty_level' => 'required|in:beginner,intermediate,advanced',
            'is_premium' => 'boolean',
            'price' => 'nullable|numeric|min:0',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Vérifier que l'utilisateur est un instructeur
        if (Auth::user()->role !== 'teacher') {
            return response()->json(['message' => 'Seuls les instructeurs peuvent créer des vidéos'], 403);
        }

        $video = Video::create(array_merge($request->all(), [
            'instructor_id' => Auth::id(),
            'status' => 'published'
        ]));

        $video->load('instructor');
        return response()->json($video, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/videos/{id}",
     *     summary="Mettre à jour une vidéo",
     *     tags={"Vidéos"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la vidéo",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Vidéo mise à jour avec succès"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $video = Video::findOrFail($id);

        // Vérifier que l'utilisateur est l'instructeur de la vidéo
        if ($video->instructor_id !== Auth::id()) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'thumbnail' => 'nullable|string',
            'category' => 'sometimes|string|max:100',
            'difficulty_level' => 'sometimes|in:beginner,intermediate,advanced',
            'is_premium' => 'sometimes|boolean',
            'price' => 'nullable|numeric|min:0',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $video->update($request->all());
        $video->load('instructor');

        return response()->json($video);
    }

    /**
     * @OA\Delete(
     *     path="/api/videos/{id}",
     *     summary="Supprimer une vidéo",
     *     tags={"Vidéos"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la vidéo",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Vidéo supprimée avec succès"
     *     )
     * )
     */
    public function destroy($id)
    {
        $video = Video::findOrFail($id);

        // Vérifier que l'utilisateur est l'instructeur de la vidéo
        if ($video->instructor_id !== Auth::id()) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        // Supprimer les fichiers associés
        if ($video->file_url) {
            Storage::delete($video->file_url);
        }

        if ($video->thumbnail) {
            Storage::delete($video->thumbnail);
        }

        $video->delete();

        return response()->json(['message' => 'Vidéo supprimée avec succès']);
    }

    /**
     * @OA\Post(
     *     path="/api/videos/{id}/like",
     *     summary="Liker/disliker une vidéo",
     *     tags={"Vidéos"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la vidéo",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"type"},
     *             @OA\Property(property="type", type="string", enum={"like", "dislike"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Like/dislike ajouté avec succès"
     *     )
     * )
     */
    public function toggleLike(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:like,dislike'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $video = Video::findOrFail($id);
        $userId = Auth::id();
        $type = $request->type;

        // Supprimer l'ancien like/dislike s'il existe
        VideoLike::where('video_id', $id)
            ->where('user_id', $userId)
            ->delete();

        // Ajouter le nouveau like/dislike
        VideoLike::create([
            'video_id' => $id,
            'user_id' => $userId,
            'type' => $type
        ]);

        // Mettre à jour les compteurs
        $video->likes = VideoLike::where('video_id', $id)->likes()->count();
        $video->dislikes = VideoLike::where('video_id', $id)->dislikes()->count();
        $video->save();

        return response()->json([
            'message' => ucfirst($type) . ' ajouté avec succès',
            'likes' => $video->likes,
            'dislikes' => $video->dislikes
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/videos/{id}/comments",
     *     summary="Ajouter un commentaire à une vidéo",
     *     tags={"Vidéos"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la vidéo",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"content"},
     *             @OA\Property(property="content", type="string"),
     *             @OA\Property(property="parent_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Commentaire ajouté avec succès"
     *     )
     * )
     */
    public function addComment(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:video_comments,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $video = Video::findOrFail($id);

        $comment = VideoComment::create([
            'video_id' => $id,
            'user_id' => Auth::id(),
            'content' => $request->content,
            'parent_id' => $request->parent_id,
            'is_approved' => true // Auto-approuver pour l'instant
        ]);

        $comment->load('user');

        return response()->json($comment, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/instructors/{instructorId}/videos",
     *     summary="Lister les vidéos d'un instructeur",
     *     tags={"Vidéos"},
     *     @OA\Parameter(
     *         name="instructorId",
     *         in="path",
     *         description="ID de l'instructeur",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des vidéos de l'instructeur"
     *     )
     * )
     */
    public function instructorVideos($instructorId)
    {
        $videos = Video::with(['instructor'])
            ->byInstructor($instructorId)
            ->published()
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return response()->json($videos);
    }
}
