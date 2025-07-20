<?php

namespace App\Http\Controllers;

use App\Models\InternshipRequest;
use Illuminate\Http\Request;

class InternshipRequestController extends Controller
{
    public function index($userId)
    {
        $requests = InternshipRequest::where('user_id', $userId)->get();
        return response()->json($requests);
    }

    public function store(Request $request)
    {
        $internshipRequest = InternshipRequest::create($request->all());
        return response()->json($internshipRequest, 201);
    }

    public function update(Request $request, $id)
    {
        $internshipRequest = InternshipRequest::findOrFail($id);
        $internshipRequest->update($request->all());
        return response()->json($internshipRequest);
    }

    public function destroy($id)
    {
        $internshipRequest = InternshipRequest::findOrFail($id);
        $internshipRequest->delete();
        return response()->json(['message' => 'Internship request deleted successfully']);
    }
}

