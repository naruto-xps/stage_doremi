<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use OpenApi\Annotations as OA;


class AuthController extends Controller
{
    //
    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Inscription d’un utilisateur",
     *     tags={"Authentification"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Utilisateur créé",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", ref="#/components/schemas/User"),
     *             @OA\Property(property="token", type="string")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Données invalides")
     * )
     */
    // 📥 Inscription
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'surname' => 'nullable|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,teacher,student,recruiter',
            'student_cycle' => 'nullable|in:lyceen,licence,master,doctorat',
            'cv' => 'nullable|file|mimes:pdf|max:5120' // 5MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $userData = [
            'name' => $request->name,
            'surname' => $request->surname ?? $request->name, // Utiliser name comme surname si non fourni
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_verified' => false,
        ];

        // Ajouter student_cycle si fourni
        if ($request->has('student_cycle')) {
            $userData['student_cycle'] = $request->student_cycle;
        }

        // Gérer l'upload du CV si fourni
        if ($request->hasFile('cv')) {
            $cvFile = $request->file('cv');
            $cvPath = $cvFile->store('cvs', 'public');
            $userData['cv_path'] = $cvPath;
        }

        $user = User::create($userData);

        $token = $user->createToken('LaravelPassportToken')->accessToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ], 201);
    }
    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Auth"},
     *     summary="Connexion d'un utilisateur",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="naruto@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Connexion réussie",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Connexion réussie"),
     *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Identifiants invalides")
     * )
     */
    // 🔐 Connexion
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials',
                'error' => 'Unauthorized'
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('LaravelPassportToken')->accessToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }
    /**
     * @OA\Get(
     *     path="/api/user",
     *     tags={"Auth"},
     *     summary="Récupérer les informations de l'utilisateur connecté",
     *     security={{"BearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Informations de l'utilisateur récupérées avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(response=401, description="Non autorisé")
     * )
     */
    function user(Request $request)
    {
        // Logique pour récupérer les informations de l'utilisateur connecté
        return response()->json($request->user());
    }
    /**
     * @OA\Post(
     *     path="/api/logout",
     *     tags={"Auth"},
     *     summary="Déconnexion de l'utilisateur",
     *     security={{"BearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Déconnexion réussie",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Déconnexion réussie")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé")
     * )
     */
    function logout(Request $request)
    {
        // Logique pour déconnecter l'utilisateur
        $user = $request->user();
        $user->tokens()->delete();
        return response()->json(['message' => 'Déconnexion réussie']);
    }
}
