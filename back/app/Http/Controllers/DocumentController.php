<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
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

    public function store(Request $request)
    {
        $document = Document::create($request->all());
        return response()->json($document, 201);
    }

    public function destroy($id)
    {
        $document = Document::findOrFail($id);
        $document->delete();
        return response()->json(['message' => 'Document deleted successfully']);
    }
}

