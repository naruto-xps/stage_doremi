<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class CommentController extends Controller
{
    /**
     * Récupérer les commentaires d'une actualité
     */
    public function index($newsId): JsonResponse
    {
        $news = News::findOrFail($newsId);
        $comments = $news->comments()->with('news')->get();

        return response()->json([
            'data' => $comments,
            'total' => $comments->count()
        ]);
    }

    /**
     * Créer un nouveau commentaire
     */
    public function store(Request $request, $newsId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'author_name' => 'required|string|max:255',
                'author_email' => 'nullable|email|max:255',
                'content' => 'required|string|min:3|max:1000',
            ]);

            $news = News::findOrFail($newsId);

            $comment = Comment::create([
                'news_id' => $news->id,
                'author_name' => $validated['author_name'],
                'author_email' => $validated['author_email'] ?? null,
                'content' => $validated['content'],
                'is_approved' => true, // Auto-approuver pour l'instant
            ]);

            // Incrémenter le compteur de commentaires de l'actualité
            $news->incrementComments();

            return response()->json([
                'message' => 'Commentaire ajouté avec succès',
                'data' => $comment,
                'comments_count' => $news->fresh()->comments_count
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de l\'ajout du commentaire',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Incrémenter les likes d'un commentaire
     */
    public function like($id): JsonResponse
    {
        try {
            $comment = Comment::findOrFail($id);
            $comment->incrementLikes();

            return response()->json([
                'message' => 'Like ajouté',
                'likes_count' => $comment->fresh()->likes_count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors du like',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer un commentaire (admin uniquement)
     */
    public function destroy($id): JsonResponse
    {
        try {
            $comment = Comment::findOrFail($id);
            $news = $comment->news;
            
            $comment->delete();
            
            // Décrémenter le compteur de commentaires
            $news->decrement('comments_count');

            return response()->json([
                'message' => 'Commentaire supprimé avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la suppression',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
