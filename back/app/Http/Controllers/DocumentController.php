<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/documents",
     *     summary="Lister les documents avec filtres optionnels",
     *     tags={"Documents"},
     *     @OA\Parameter(
     *         name="theme",
     *         in="query",
     *         description="Filtrer par thème",
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
     *         description="Liste des documents filtrés"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = Document::query();

        if ($request->has('theme')) {
            $query->where('theme', $request->theme);
        }

        if ($request->has('school')) {
            $query->where('school', $request->school);
        }

        $documents = $query->get();
        return response()->json($documents);
    }

    /**
     * @OA\Post(
     *     path="/api/documents",
     *     summary="Créer un nouveau document",
     *     tags={"Documents"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "theme", "school", "file_path"},
     *             @OA\Property(property="title", type="string", example="Plan de cours Mathématiques"),
     *             @OA\Property(property="theme", type="string", example="Mathématiques"),
     *             @OA\Property(property="school", type="string", example="Lycée DoReMi"),
     *             @OA\Property(property="file_path", type="string", example="/storage/documents/plan_cours_math.pdf")
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
        $document = Document::create($request->all());
        return response()->json($document, 201);
    }

    /**
     * @OA\Delete(
     *     path="/api/documents/{id}",
     *     summary="Supprimer un document",
     *     tags={"Documents"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du document à supprimer",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Document supprimé avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Document non trouvé"
     *     )
     * )
     */
    public function destroy($id)
    {
        $document = Document::findOrFail($id);
        $document->delete();
        return response()->json(['message' => 'Document deleted successfully']);
    }
}
