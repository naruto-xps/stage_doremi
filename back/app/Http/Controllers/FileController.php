<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB; // Added missing import

/**
 * @OA\Tag(
 *     name="Fichiers",
 *     description="API pour la gestion des fichiers"
 * )
 */
class FileController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/files/upload",
     *     summary="Upload de fichiers",
     *     tags={"Fichiers"},
     *     security={{"BearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="file", type="string", format="binary"),
     *                 @OA\Property(property="category", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="tags", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Fichier uploadé avec succès"
     *     ),
     *     @OA\Response(response=422, description="Données invalides")
     * )
     */
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:10240', // 10MB max
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
            'tags' => 'nullable|string|max:200'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $mimeType = $file->getMimeType();
        $size = $file->getSize();

        // Générer un nom unique pour le fichier
        $fileName = Str::random(40) . '.' . $extension;
        $path = 'uploads/' . date('Y/m/d');

        // Stocker le fichier
        $filePath = Storage::disk('public')->putFileAs($path, $file, $fileName);

        if (!$filePath) {
            return response()->json(['message' => 'Erreur lors du stockage du fichier'], 500);
        }

        // Créer l'enregistrement en base
        $fileRecord = File::create([
            'user_id' => Auth::id(),
            'original_name' => $originalName,
            'file_name' => $fileName,
            'file_path' => $filePath,
            'mime_type' => $mimeType,
            'size' => $size,
            'extension' => $extension,
            'category' => $request->get('category'),
            'description' => $request->get('description'),
            'tags' => $request->get('tags') ? explode(',', $request->get('tags')) : [],
            'download_count' => 0
        ]);

        return response()->json([
            'message' => 'Fichier uploadé avec succès',
            'file' => $fileRecord
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/files/{id}",
     *     summary="Télécharger un fichier",
     *     tags={"Fichiers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du fichier",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Fichier téléchargé avec succès"
     *     ),
     *     @OA\Response(response=404, description="Fichier non trouvé")
     * )
     */
    public function download($id)
    {
        $file = File::findOrFail($id);

        // Vérifier que le fichier existe sur le disque
        if (!Storage::disk('public')->exists($file->file_path)) {
            return response()->json(['message' => 'Fichier non trouvé sur le serveur'], 404);
        }

        // Incrémenter le compteur de téléchargements
        $file->increment('download_count');

        // Retourner le fichier pour téléchargement
        return Storage::disk('public')->download($file->file_path, $file->original_name);
    }

    /**
     * @OA\Delete(
     *     path="/api/files/{id}",
     *     summary="Supprimer un fichier",
     *     tags={"Fichiers"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du fichier",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Fichier supprimé avec succès"
     *     ),
     *     @OA\Response(response=404, description="Fichier non trouvé")
     * )
     */
    public function destroy($id)
    {
        $file = File::findOrFail($id);

        // Vérifier que l'utilisateur connecté peut supprimer ce fichier
        if (Auth::id() != $file->user_id && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        // Supprimer le fichier du disque
        if (Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        // Supprimer l'enregistrement en base
        $file->delete();

        return response()->json(['message' => 'Fichier supprimé avec succès']);
    }

    /**
     * @OA\Get(
     *     path="/api/files/user/{userId}",
     *     summary="Obtenir les fichiers d'un utilisateur",
     *     tags={"Fichiers"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="ID de l'utilisateur",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Filtrer par catégorie",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Rechercher dans les noms de fichiers",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Fichiers récupérés avec succès"
     *     )
     * )
     */
    public function getUserFiles($userId, Request $request)
    {
        if (Auth::id() != $userId && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $query = File::where('user_id', $userId);

        // Filtre par catégorie
        if ($request->has('category')) {
            $query->where('category', $request->get('category'));
        }

        // Recherche
        if ($request->has('search')) {
            $searchTerm = $request->get('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('original_name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        $files = $query->orderBy('created_at', 'desc')->get();

        return response()->json($files);
    }

    /**
     * @OA\Get(
     *     path="/api/files/{id}/info",
     *     summary="Obtenir les informations d'un fichier",
     *     tags={"Fichiers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du fichier",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Informations du fichier récupérées avec succès"
     *     ),
     *     @OA\Response(response=404, description="Fichier non trouvé")
     * )
     */
    public function getFileInfo($id)
    {
        $file = File::with('user')->findOrFail($id);

        // Ajouter l'URL de téléchargement
        $file->download_url = route('files.download', $id);
        $file->file_size_formatted = $this->formatFileSize($file->size);

        return response()->json($file);
    }

    /**
     * @OA\Put(
     *     path="/api/files/{id}",
     *     summary="Mettre à jour les informations d'un fichier",
     *     tags={"Fichiers"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du fichier",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="category", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="tags", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Fichier mis à jour avec succès"
     *     ),
     *     @OA\Response(response=404, description="Fichier non trouvé")
     * )
     */
    public function update(Request $request, $id)
    {
        $file = File::findOrFail($id);

        if (Auth::id() != $file->user_id && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $validator = Validator::make($request->all(), [
            'category' => 'sometimes|string|max:100',
            'description' => 'sometimes|string|max:500',
            'tags' => 'sometimes|string|max:200'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $updateData = $request->only(['category', 'description']);
        
        if ($request->has('tags')) {
            $updateData['tags'] = explode(',', $request->get('tags'));
        }

        $file->update($updateData);

        return response()->json([
            'message' => 'Fichier mis à jour avec succès',
            'file' => $file
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/files/categories",
     *     summary="Obtenir les catégories de fichiers",
     *     tags={"Fichiers"},
     *     @OA\Response(
     *         response=200,
     *         description="Catégories récupérées avec succès"
     *     )
     * )
     */
    public function getCategories()
    {
        $categories = File::select('category')
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values();

        return response()->json($categories);
    }

    /**
     * @OA\Post(
     *     path="/api/files/{id}/share",
     *     summary="Partager un fichier (générer un lien temporaire)",
     *     tags={"Fichiers"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du fichier",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="expires_at", type="string", format="date-time"),
     *             @OA\Property(property="password", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lien de partage généré avec succès"
     *     )
     * )
     */
    public function share(Request $request, $id)
    {
        $file = File::findOrFail($id);

        if (Auth::id() != $file->user_id && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $validator = Validator::make($request->all(), [
            'expires_at' => 'required|date|after:now',
            'password' => 'nullable|string|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Générer un token de partage
        $shareToken = Str::random(60);
        
        // Stocker les informations de partage (dans un cache ou une table dédiée)
        $shareData = [
            'file_id' => $id,
            'user_id' => Auth::id(),
            'expires_at' => $request->get('expires_at'),
            'password' => $request->get('password'),
            'created_at' => now()
        ];

        // Ici, vous pourriez stocker dans une table `file_shares` ou dans le cache
        // Pour l'exemple, on simule avec un message de succès
        
        $shareUrl = route('files.shared', $shareToken);

        return response()->json([
            'message' => 'Lien de partage généré avec succès',
            'share_url' => $shareUrl,
            'expires_at' => $request->get('expires_at'),
            'has_password' => !empty($request->get('password'))
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/files/stats/{userId}",
     *     summary="Obtenir les statistiques des fichiers d'un utilisateur",
     *     tags={"Fichiers"},
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
    public function getFileStats($userId)
    {
        if (Auth::id() != $userId && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $files = File::where('user_id', $userId);

        $stats = [
            'total_files' => $files->count(),
            'total_size' => $files->sum('size'),
            'total_size_formatted' => $this->formatFileSize($files->sum('size')),
            'total_downloads' => $files->sum('download_count'),
            'files_by_category' => $files->select('category', DB::raw('count(*) as count'))
                ->groupBy('category')
                ->get(),
            'files_by_extension' => $files->select('extension', DB::raw('count(*) as count'))
                ->groupBy('extension')
                ->get(),
            'recent_uploads' => $files->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(['id', 'original_name', 'created_at', 'size'])
        ];

        return response()->json($stats);
    }

    // Méthodes utilitaires

    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}
