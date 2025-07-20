<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function index($studentId)
    {
        $conversations = Conversation::where('student_id', $studentId)->get();
        return response()->json($conversations);
    }

    public function store(Request $request)
    {
        $conversation = Conversation::create($request->all());
        return response()->json($conversation, 201);
    }
}
