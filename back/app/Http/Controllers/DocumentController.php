<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentDownload;
use App\Models\DocumentView;
use App\Models\DocumentRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/documents",
     *     summary="Lister tous les documents de la bibliothèque",
     *     tags={"Bibliothèque"},
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Filtrer par type de document",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Filtrer par catégorie",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="genre",
     *         in="query",
     *         description="Filtrer par genre",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="country",
     *         in="query",
     *         description="Filtrer par pays",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des documents"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = Document::with(['instructor'])
            ->published()
            ->orderBy('upload_date', 'desc');

        // Filtres
        if ($request->has('type')) {
            $query->byType($request->type);
        }

        if ($request->has('category')) {
            $query->byCategory($request->type);
        }

        if ($request->has('genre')) {
            $query->byGenre($request->genre);
        }

        if ($request->has('country')) {
            $query->byCountry($request->country);
        }

        if ($request->has('instructor')) {
            $query->byInstructor($request->instructor);
        }

        $documents = $query->paginate(12);
        return response()->json($documents);
    }

    /**
     * @OA\Get(
     *     path="/api/documents/{id}",
     *     summary="Afficher un document spécifique",
     *     tags={"Bibliothèque"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du document",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails du document"
     *     )
     * )
     */
    public function show($id)
    {
        $document = Document::with(['instructor', 'ratings.user'])
            ->published()
            ->findOrFail($id);

        // Incrémenter le compteur de vues
        $document->incrementViews();

        // Enregistrer la vue
        DocumentView::create([
            'document_id' => $id,
            'user_id' => Auth::id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'viewed_at' => now()
        ]);

        return response()->json($document);
    }

    /**
     * @OA\Post(
     *     path="/api/documents",
     *     summary="Créer un nouveau document",
     *     tags={"Bibliothèque"},
     *     security={{"BearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "author", "description", "type", "category", "genre"},
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="author", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="type", type="string"),
     *             @OA\Property(property="category", type="string"),
     *             @OA\Property(property="genre", type="string"),
     *             @OA\Property(property="file_url", type="string"),
     *             @OA\Property(property="file_name", type="string"),
     *             @OA\Property(property="size", type="integer"),
     *             @OA\Property(property="is_premium", type="boolean"),
     *             @OA\Property(property="tags", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="country", type="string"),
     *             @OA\Property(property="duration", type="integer"),
     *             @OA\Property(property="thumbnail", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Document créé avec succès"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:pdf,doc,ppt,video,audio,book',
            'category' => 'required|string|max:100',
            'genre' => 'required|string|max:100',
            'file_url' => 'nullable|string',
            'file_name' => 'nullable|string|max:255',
            'size' => 'nullable|integer|min:0',
            'is_premium' => 'boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'country' => 'nullable|string|max:100',
            'duration' => 'nullable|integer|min:0',
            'thumbnail' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Vérifier que l'utilisateur est un instructeur ou admin
        if (!in_array(Auth::user()->role, ['teacher', 'admin'])) {
            return response()->json(['message' => 'Seuls les instructeurs et administrateurs peuvent créer des documents'], 403);
        }

        $document = Document::create(array_merge($request->all(), [
            'instructor_id' => Auth::id(),
            'status' => 'published',
            'upload_date' => now()
        ]));

        $document->load('instructor');
        return response()->json($document, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/documents/{id}",
     *     summary="Mettre à jour un document",
     *     tags={"Bibliothèque"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du document",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Document mis à jour avec succès"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $document = Document::findOrFail($id);

        // Vérifier que l'utilisateur est l'instructeur du document ou un admin
        if ($document->instructor_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'author' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'category' => 'sometimes|string|max:100',
            'genre' => 'sometimes|string|max:100',
            'is_premium' => 'sometimes|boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'country' => 'nullable|string|max:100',
            'thumbnail' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $document->update($request->all());
        $document->load('instructor');

        return response()->json($document);
    }

    /**
     * @OA\Delete(
     *     path="/api/documents/{id}",
     *     summary="Supprimer un document",
     *     tags={"Bibliothèque"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du document",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Document supprimé avec succès"
     *     )
     * )
     */
    public function destroy($id)
    {
        $document = Document::findOrFail($id);

        // Vérifier que l'utilisateur est l'instructeur du document ou un admin
        if ($document->instructor_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        // Supprimer les fichiers associés
        if ($document->file_url) {
            Storage::delete($document->file_url);
        }

        if ($document->thumbnail) {
            Storage::delete($document->thumbnail);
        }

        $document->delete();

        return response()->json(['message' => 'Document supprimé avec succès']);
    }

    /**
     * @OA\Post(
     *     path="/api/documents/{id}/download",
     *     summary="Télécharger un document",
     *     tags={"Bibliothèque"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du document",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Téléchargement enregistré"
     *     )
     * )
     */
    public function download($id)
    {
        $document = Document::findOrFail($id);

        // Vérifier l'accès premium si nécessaire
        if ($document->is_premium && !Auth::user()->is_premium) {
            return response()->json(['message' => 'Ce document nécessite un abonnement Premium'], 403);
        }

        // Incrémenter le compteur de téléchargements
        $document->incrementDownloads();

        // Enregistrer le téléchargement
        DocumentDownload::create([
            'document_id' => $id,
            'user_id' => Auth::id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'downloaded_at' => now()
        ]);

        return response()->json([
            'message' => 'Téléchargement enregistré',
            'download_count' => $document->download_count + 1
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/documents/{id}/rate",
     *     summary="Noter un document",
     *     tags={"Bibliothèque"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du document",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"rating"},
     *             @OA\Property(property="rating", type="integer", minimum=1, maximum=5),
     *             @OA\Property(property="comment", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Note ajoutée avec succès"
     *     )
     * )
     */
    public function rate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $document = Document::findOrFail($id);
        $userId = Auth::id();

        // Vérifier si l'utilisateur a déjà noté ce document
        $existingRating = DocumentRating::where('document_id', $id)
            ->where('user_id', $userId)
            ->first();

        if ($existingRating) {
            // Mettre à jour la note existante
            $existingRating->update([
                'rating' => $request->rating,
                'comment' => $request->comment
            ]);
            $rating = $existingRating;
        } else {
            // Créer une nouvelle note
            $rating = DocumentRating::create([
                'document_id' => $id,
                'user_id' => $userId,
                'rating' => $request->rating,
                'comment' => $request->comment
            ]);
        }

        // Mettre à jour la note moyenne du document
        $averageRating = DocumentRating::where('document_id', $id)->avg('rating');
        $document->update(['rating' => round($averageRating, 1)]);

        $rating->load('user');

        return response()->json([
            'message' => 'Note ajoutée avec succès',
            'rating' => $rating,
            'average_rating' => $document->rating
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/documents/search",
     *     summary="Rechercher des documents",
     *     tags={"Bibliothèque"},
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Terme de recherche",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Résultats de la recherche"
     *     )
     * )
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (!$query) {
            return response()->json(['message' => 'Terme de recherche requis'], 400);
        }

        $documents = Document::with(['instructor'])
            ->published()
            ->where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('author', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhereJsonContains('tags', $query);
            })
            ->orderBy('upload_date', 'desc')
            ->paginate(12);

        return response()->json($documents);
    }
}
