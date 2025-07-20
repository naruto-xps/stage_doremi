<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use Illuminate\Http\Request;
class ChapterController extends Controller
{
    public function index($courseId)
    {
        $chapters = Chapter::where('course_id', $courseId)->get();
        return response()->json($chapters);
    }

    public function show($courseId, $chapterId)
    {
        $chapter = Chapter::where('course_id', $courseId)->findOrFail($chapterId);
        return response()->json($chapter);
    }

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

    public function update(Request $request, $courseId, $chapterId)
    {
        $chapter = Chapter::where('course_id', $courseId)->findOrFail($chapterId);
        $chapter->update($request->all());
        return response()->json($chapter);
    }

    public function destroy($courseId, $chapterId)
    {
        $chapter = Chapter::where('course_id', $courseId)->findOrFail($chapterId);
        $chapter->delete();
        return response()->json(['message' => 'Chapter deleted successfully']);
    }
}
