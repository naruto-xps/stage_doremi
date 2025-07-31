<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use Illuminate\Http\Request;

class ChapterController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/courses/{courseId}/chapters",
     *     summary="Lister les chapitres d'un cours",
     *     tags={"Chapitres"},
     *     @OA\Parameter(
     *         name="courseId",
     *         in="path",
     *         required=true,
     *         description="ID du cours",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des chapitres"
     *     )
     * )
     */
    public function index($courseId)
    {
        $chapters = Chapter::where('course_id', $courseId)->get();
        return response()->json($chapters);
    }

    /**
     * @OA\Get(
     *     path="/api/courses/{courseId}/chapters/{chapterId}",
     *     summary="Afficher un chapitre spécifique",
     *     tags={"Chapitres"},
     *     @OA\Parameter(
     *         name="courseId",
     *         in="path",
     *         required=true,
     *         description="ID du cours",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="chapterId",
     *         in="path",
     *         required=true,
     *         description="ID du chapitre",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails du chapitre"
     *     )
     * )
     */
    public function show($courseId, $chapterId)
    {
        $chapter = Chapter::where('course_id', $courseId)->findOrFail($chapterId);
        return response()->json($chapter);
    }

    /**
     * @OA\Post(
     *     path="/api/courses/{courseId}/chapters",
     *     summary="Créer un nouveau chapitre pour un cours",
     *     tags={"Chapitres"},
     *     @OA\Parameter(
     *         name="courseId",
     *         in="path",
     *         required=true,
     *         description="ID du cours",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "content"},
     *             @OA\Property(property="title", type="string", example="Chapitre 1 : Introduction"),
     *             @OA\Property(property="content", type="string", example="Contenu du chapitre..."),
     *             @OA\Property(property="video_url", type="string", example="https://youtube.com/..."),
     *             @OA\Property(property="pdf_path", type="string", example="/storage/chapters/intro.pdf"),
     *             @OA\Property(property="audio_path", type="string", example="/storage/chapters/intro.mp3"),
     *             @OA\Property(property="order", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Chapitre créé avec succès"
     *     )
     * )
     */
    public function store(Request $request, $courseId)
    {
        $chapter = Chapter::create([
            'course_id' => $courseId,
            'title' => $request->title,
            'content' => $request->content,
            'video_url' => $request->video_url,
            'pdf_path' => $request->pdf_path,
            'audio_path' => $request->audio_path,
            'order' => $request->order
        ]);
        return response()->json($chapter, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/courses/{courseId}/chapters/{chapterId}",
     *     summary="Mettre à jour un chapitre",
     *     tags={"Chapitres"},
     *     @OA\Parameter(
     *         name="courseId",
     *         in="path",
     *         required=true,
     *         description="ID du cours",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="chapterId",
     *         in="path",
     *         required=true,
     *         description="ID du chapitre à mettre à jour",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Chapitre mis à jour"),
     *             @OA\Property(property="content", type="string", example="Nouveau contenu du chapitre"),
     *             @OA\Property(property="video_url", type="string", example="https://youtube.com/..."),
     *             @OA\Property(property="pdf_path", type="string", example="/storage/chapters/intro.pdf"),
     *             @OA\Property(property="audio_path", type="string", example="/storage/chapters/intro.mp3"),
     *             @OA\Property(property="order", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Chapitre mis à jour avec succès"
     *     )
     * )
     */
    public function update(Request $request, $courseId, $chapterId)
    {
        $chapter = Chapter::where('course_id', $courseId)->findOrFail($chapterId);
        $chapter->update($request->all());
        return response()->json($chapter);
    }

    /**
     * @OA\Delete(
     *     path="/api/courses/{courseId}/chapters/{chapterId}",
     *     summary="Supprimer un chapitre",
     *     tags={"Chapitres"},
     *     @OA\Parameter(
     *         name="courseId",
     *         in="path",
     *         required=true,
     *         description="ID du cours",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="chapterId",
     *         in="path",
     *         required=true,
     *         description="ID du chapitre à supprimer",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Chapitre supprimé avec succès"
     *     )
     * )
     */
    public function destroy($courseId, $chapterId)
    {
        $chapter = Chapter::where('course_id', $courseId)->findOrFail($chapterId);
        $chapter->delete();
        return response()->json(['message' => 'Chapter deleted successfully']);
    }
}
