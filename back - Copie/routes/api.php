<?php

use App\Http\Controllers\InternshipController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use Illuminate\Http\Request;
use App\Http\Controllers\ProgressionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\InternshipRequestController;
use App\Http\Controllers\NewsController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Route protégée
Route::middleware('auth:api')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
});


Route::get('/internships', [InternshipController::class, 'index']); // Afficher les offres de stage
Route::get('/internships/{id}', [InternshipController::class, 'show']); // Afficher une offre de stage spécifique
Route::middleware('auth:api')->group(function () {
    Route::post('/internships', [InternshipController::class, 'store'])->middleware('role:admin'); // Créer une offre de stage (admin)
    Route::put('/internships/{id}', [InternshipController::class, 'update'])->middleware('role:admin'); // Modifier une offre de stage (admin)
    Route::delete('/internships/{id}', [InternshipController::class, 'destroy'])->middleware('role:admin'); // Supprimer une offre de stage (admin)
});


Route::middleware('auth:api')->group(function () {
    Route::get('/users/{userId}/internship-requests', [InternshipRequestController::class, 'index']); // Afficher les demandes de stage d'un utilisateur
    Route::post('/internship-requests', [InternshipRequestController::class, 'store']); // Soumettre une demande de stage
    Route::put('/internship-requests/{id}', [InternshipRequestController::class, 'update']); // Mettre à jour le statut de la demande
    Route::delete('/internship-requests/{id}', [InternshipRequestController::class, 'destroy']); // Supprimer une demande de stage
});



Route::get('/courses', [CourseController::class, 'index']); // Filtrer et afficher les cours
Route::middleware('auth:api')->group(function () {

    Route::get('/courses/{id}', [CourseController::class, 'show']); // Afficher un cours spécifique
    Route::post('/courses', [CourseController::class, 'store'])->middleware('role:teacher'); // Créer un cours (formateur)
    Route::put('/courses/{id}', [CourseController::class, 'update'])->middleware('role:teacher'); // Modifier un cours (formateur)
    Route::delete('/courses/{id}', [CourseController::class, 'destroy'])->middleware('role:teacher'); // Supprimer un cours (formateur)
});


Route::get('/news/{id}', [NewsController::class, 'show']); // Afficher une actualité spécifique
Route::get('/news', [NewsController::class, 'index']); // Afficher les actualités
Route::middleware('auth:api')->group(function () {
    Route::post('/news', [NewsController::class, 'store']);//->middleware('role:admin'); // Créer une actualité (admin)
    Route::put('/news/{id}', [NewsController::class, 'update'])->middleware('role:admin'); // Modifier une actualité (admin)
    Route::delete('/news/{id}', [NewsController::class, 'destroy'])->middleware('role:admin'); // Supprimer une actualité (admin)
});


Route::middleware('auth:api')->group(function () {
    Route::get('/courses/{courseId}/chapters', [ChapterController::class, 'index']); // Afficher les chapitres d'un cours
    Route::get('/courses/{courseId}/chapters/{chapterId}', [ChapterController::class, 'show']); // Afficher un chapitre spécifique
    Route::post('/courses/{courseId}/chapters', [ChapterController::class, 'store'])->middleware('role:teacher'); // Ajouter un chapitre (formateur)
    Route::put('/courses/{courseId}/chapters/{chapterId}', [ChapterController::class, 'update'])->middleware('role:teacher'); // Modifier un chapitre (formateur)
    Route::delete('/courses/{courseId}/chapters/{chapterId}', [ChapterController::class, 'destroy'])->middleware('role:teacher'); // Supprimer un chapitre (formateur)
});


Route::middleware('auth:api')->group(function () {
    Route::get('/documents',  [DocumentController::class, 'index']); // Afficher les documents
    Route::post('/documents', [DocumentController::class, 'store'])->middleware('role:teacher'); // Ajouter un document (formateur)
    Route::delete('/documents/{id}', [DocumentController::class, 'destroy'])->middleware('role:teacher'); // Supprimer un document (formateur)
});

Route::middleware('auth:api')->group(function () {
    Route::get('/users/{userId}/courses/{courseId}/progression', [ProgressionController::class, 'show']); // Afficher la progression d'un étudiant
    Route::post('/users/{userId}/courses/{courseId}/progression', [ProgressionController::class, 'store']); // Créer une progression
    Route::put('/users/{userId}/courses/{courseId}/progression', [ProgressionController::class, 'update']); // Mettre à jour la progression
});

Route::middleware('auth:api')->group(function () {
    Route::get('/users/{studentId}/conversations', [ConversationController::class, 'index']); // Afficher les conversations d'un étudiant
    Route::post('/conversations', [ConversationController::class, 'store'])->middleware('role:student'); // Créer une conversation
});

Route::middleware('auth:api')->group(function () {
    Route::get('/conversations/{conversationId}/messages', [MessageController::class, 'index']); // Afficher les messages d'une conversation
    Route::post('/messages', [MessageController::class, 'store']); // Envoyer un message
});

Route::middleware('auth:api')->group(function () {
    Route::get('/schools', [SchoolController::class, 'index']); // Afficher toutes les écoles
    Route::get('/schools/{id}', [SchoolController::class, 'show']); // Afficher une école spécifique
    Route::post('/schools', [SchoolController::class, 'store'])->middleware('role:admin'); // Créer une école (admin)
    Route::put('/schools/{id}', [SchoolController::class, 'update'])->middleware('role:admin'); // Modifier une école (admin)
    Route::delete('/schools/{id}', [SchoolController::class, 'destroy'])->middleware('role:admin'); // Supprimer une école (admin)
});
