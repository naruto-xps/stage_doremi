<?php

namespace App\Http\Controllers;

use App\Models\Progression;
use Illuminate\Http\Request;

class ProgressionController extends Controller
{
    public function show($userId, $courseId)
    {
        $progression = Progression::where('user_id', $userId)->where('course_id', $courseId)->first();
        return response()->json($progression);
    }

    public function store(Request $request)
    {
        $progression = Progression::create($request->all());
        return response()->json($progression, 201);
    }

    public function update(Request $request, $userId, $courseId)
    {
        $progression = Progression::where('user_id', $userId)->where('course_id', $courseId)->first();
        $progression->update($request->all());
        return response()->json($progression);
    }
}

