<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
class NewsController extends Controller
{
    public function index()
    {
        $news = News::all();
        return response()->json($news);
    }

    public function show($id)
    {
        $news = News::findOrFail($id);
        return response()->json($news);
    }

    public function store(Request $request)
    {
        $news = News::create($request->all());
        return response()->json($news, 201);
    }

    public function update(Request $request, $id)
    {
        $news = News::findOrFail($id);
        $news->update($request->all());
        return response()->json($news);
    }

    public function destroy($id)
    {
        $news = News::findOrFail($id);
        $news->delete();
        return response()->json(['message' => 'News deleted successfully']);
    }
}

