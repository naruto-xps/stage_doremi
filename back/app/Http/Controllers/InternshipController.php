<?php

namespace App\Http\Controllers;

use App\Models\Internship;
use Illuminate\Http\Request;

class InternshipController extends Controller
{
    public function index()
    {
        $internships = Internship::all();
        return response()->json($internships);
    }

    public function show($id)
    {
        $internship = Internship::findOrFail($id);
        return response()->json($internship);
    }

    public function store(Request $request)
    {
        $internship = Internship::create($request->all());
        return response()->json($internship, 201);
    }

    public function update(Request $request, $id)
    {
        $internship = Internship::findOrFail($id);
        $internship->update($request->all());
        return response()->json($internship);
    }

    public function destroy($id)
    {
        $internship = Internship::findOrFail($id);
        $internship->delete();
        return response()->json(['message' => 'Internship deleted successfully']);
    }
}
