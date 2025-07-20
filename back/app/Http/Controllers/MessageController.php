<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index($conversationId)
    {
        $messages = Message::where('conversation_id', $conversationId)->get();
        return response()->json($messages);
    }

    public function store(Request $request)
    {
        $message = Message::create($request->all());
        return response()->json($message, 201);
    }
}

