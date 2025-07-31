<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/news",
     *     summary="Lister toutes les actualités",
     *     tags={"Actualités"},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des actualités"
     *     )
     * )
     */
    public function index()
    {
        $news = News::all();
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
    public function show($id)
    {
        $news = News::findOrFail($id);
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
     *             @OA\Property(property="content", type="string", example="La rentrée scolaire commence le 15 septembre.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Actualité créée avec succès"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $news = News::create($request->all());
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
     *             @OA\Property(property="content", type="string", example="Contenu mis à jour de l’actualité.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Actualité mise à jour avec succès"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $news = News::findOrFail($id);
        $news->update($request->all());
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
     *         description="ID de l'actualité à supprimer",
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
    public function destroy($id)
    {
        $news = News::findOrFail($id);
        $news->delete();
        return response()->json(['message' => 'News deleted successfully']);
    }
}
