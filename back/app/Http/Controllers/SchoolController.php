<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    public function index()
    {
        $schools = School::all();
        return response()->json($schools);
    }

    public function store(Request $request)
    {
        $school = School::create($request->all());
        return response()->json($school, 201);
    }

    public function show($id)
    {
        $school = School::findOrFail($id);
        return response()->json($school);
    }

    public function update(Request $request, $id)
    {
        $school = School::findOrFail($id);
        $school->update($request->all());
        return response()->json($school);
    }

    public function destroy($id)
    {
        $school = School::findOrFail($id);
        $school->delete();
        return response()->json(['message' => 'School deleted successfully']);
    }
}
