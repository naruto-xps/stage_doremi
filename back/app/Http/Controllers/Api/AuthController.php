<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class AuthController extends Controller
{
    //recuperation des users
    public function getUser(){
        $user = User::with('role')->get();
        return response()->json($user);
    }
    // 📥 Inscription
    public function register(Request $request){
        $validator = FacadesValidator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',


        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $token = $user->createToken('LaravelPassportToken')->accessToken;
        return response()->json([
            'user' => $user,
            'token' => $token
        ], 201);
    }

    // 🔐 Connexion
    public function login(Request $request){
        $credentials = $request->only('email', 'password');
        if (!Auth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user = Auth::user();
        $token = $user->createToken('LaravelPassportToken')->accessToken;
        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }

    function logout(Request $request){
        // Logique pour déconnecter l'utilisateur
        $user = $request->user();
        $user->tokens()->delete();
        return response()->json(['message' => 'Déconnexion réussie']);
    }
    function user(Request $request){
        // Logique pour récupérer les informations de l'utilisateur connecté
        return response()->json(Auth::user());
    }




}
