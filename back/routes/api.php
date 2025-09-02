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
use App\Http\Controllers\ImageUploadController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\FileController;

// ========================================
// ROUTES D'AUTHENTIFICATION
// ========================================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/use', [AuthController::class, 'index']);

Route::middleware('auth:api')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// ========================================
// ROUTES PUBLIQUES (sans authentification)
// ========================================

// Stages - Consultation publique
Route::get('/internships', [InternshipController::class, 'index']);
Route::get('/internships/search', [InternshipController::class, 'search']);
Route::get('/internships/{id}', [InternshipController::class, 'show']);

// Cours - Consultation publique
Route::get('/courses', [CourseController::class, 'index']);

// Actualités - Consultation publique
Route::get('/news', [NewsController::class, 'index']);
Route::get('/news/{id}', [NewsController::class, 'show']);
Route::get('/news/stats', [NewsController::class, 'stats']);

// Vidéos - Consultation publique
Route::get('/videos', [VideoController::class, 'index']);
Route::get('/videos/{id}', [VideoController::class, 'show']);
Route::get('/instructors/{instructorId}/videos', [VideoController::class, 'instructorVideos']);

// Documents - Consultation publique
Route::get('/documents', [DocumentController::class, 'index']);
Route::get('/documents/{id}', [DocumentController::class, 'show']);
Route::get('/documents/search', [DocumentController::class, 'search']);

// Écoles - Consultation publique
Route::get('/schools', [SchoolController::class, 'index']);
Route::get('/schools/{id}', [SchoolController::class, 'show']);

// ========================================
// ROUTES PROTÉGÉES (avec authentification)
// ========================================

Route::middleware('auth:api')->group(function () {
    
    // ========================================
    // GESTION DES STAGES (Recruteurs)
    // ========================================
    Route::get('/internships/my-offers', [InternshipController::class, 'myOffers']);
    Route::post('/internships', [InternshipController::class, 'store']);
    Route::put('/internships/{id}', [InternshipController::class, 'update']);
    Route::delete('/internships/{id}', [InternshipController::class, 'destroy']);

    // ========================================
    // DEMANDES DE STAGE (Étudiants)
    // ========================================
    Route::get('/users/{userId}/internship-requests', [InternshipRequestController::class, 'index']);
    Route::post('/internship-requests', [InternshipRequestController::class, 'store']);
    Route::put('/internship-requests/{id}', [InternshipRequestController::class, 'update']);
    Route::delete('/internship-requests/{id}', [InternshipRequestController::class, 'destroy']);
    
    // Gestion des demandes (Admin/Recruteurs)
    Route::get('/internship-requests', [InternshipRequestController::class, 'allRequests']);
    Route::get('/internship-requests/{id}', [InternshipRequestController::class, 'show']);

    // ========================================
    // GESTION DES COURS (Formateurs)
    // ========================================
    Route::get('/courses/{id}', [CourseController::class, 'show']);
    Route::post('/courses', [CourseController::class, 'store'])->middleware('role:teacher');
    Route::put('/courses/{id}', [CourseController::class, 'update'])->middleware('role:teacher');
    Route::delete('/courses/{id}', [CourseController::class, 'destroy'])->middleware('role:teacher');

    // ========================================
    // CHAPITRES DES COURS (Formateurs)
    // ========================================
    Route::get('/courses/{courseId}/chapters', [ChapterController::class, 'index']);
    Route::get('/courses/{courseId}/chapters/{chapterId}', [ChapterController::class, 'show']);
    Route::post('/courses/{courseId}/chapters', [ChapterController::class, 'store'])->middleware('role:teacher');
    Route::put('/courses/{courseId}/chapters/{chapterId}', [ChapterController::class, 'update'])->middleware('role:teacher');
    Route::delete('/courses/{courseId}/chapters/{chapterId}', [ChapterController::class, 'destroy'])->middleware('role:teacher');

    // ========================================
    // PROGRESSION DES ÉTUDIANTS
    // ========================================
    Route::get('/users/{userId}/courses/{courseId}/progression', [ProgressionController::class, 'show']);
    Route::post('/users/{userId}/courses/{courseId}/progression', [ProgressionController::class, 'store']);
    Route::put('/users/{userId}/courses/{courseId}/progression', [ProgressionController::class, 'update']);

    // ========================================
    // CONVERSATIONS ET MESSAGES
    // ========================================
    Route::get('/users/{studentId}/conversations', [ConversationController::class, 'index']);
    Route::post('/conversations', [ConversationController::class, 'store'])->middleware('role:student');
    
    Route::get('/conversations/{conversationId}/messages', [MessageController::class, 'index']);
    Route::post('/messages', [MessageController::class, 'store']);

    // ========================================
    // GESTION DES ÉCOLES (Admin)
    // ========================================
    Route::post('/schools', [SchoolController::class, 'store'])->middleware('role:admin');
    Route::put('/schools/{id}', [SchoolController::class, 'update'])->middleware('role:admin');
    Route::delete('/schools/{id}', [SchoolController::class, 'destroy'])->middleware('role:admin');

    // ========================================
    // GESTION DES ACTUALITÉS (Admin)
    // ========================================
    Route::post('/news', [NewsController::class, 'store'])->middleware('role:admin');
    Route::put('/news/{id}', [NewsController::class, 'update'])->middleware('role:admin');
    Route::delete('/news/{id}', [NewsController::class, 'destroy'])->middleware('role:admin');
    
    // Actions spéciales (admin uniquement)
    Route::patch('/news/{id}/toggle-featured', [NewsController::class, 'toggleFeatured'])->middleware('role:admin');
    Route::patch('/news/{id}/toggle-urgent', [NewsController::class, 'toggleUrgent'])->middleware('role:admin');
    Route::patch('/news/{id}/publish', [NewsController::class, 'publish'])->middleware('role:admin');
    Route::patch('/news/{id}/archive', [NewsController::class, 'archive'])->middleware('role:admin');
    
    // Actions d'engagement (utilisateurs connectés)
    Route::post('/news/{id}/like', [NewsController::class, 'incrementLikes']);
    Route::post('/news/{id}/share', [NewsController::class, 'incrementShares']);

    // ========================================
    // GESTION DES VIDÉOS (Formateurs)
    // ========================================
    Route::post('/videos', [VideoController::class, 'store'])->middleware('role:teacher');
    Route::put('/videos/{id}', [VideoController::class, 'update'])->middleware('role:teacher');
    Route::delete('/videos/{id}', [VideoController::class, 'destroy'])->middleware('role:teacher');
    Route::post('/videos/{id}/like', [VideoController::class, 'toggleLike']);
    Route::post('/videos/{id}/comments', [VideoController::class, 'addComment']);

    // ========================================
    // GESTION DES DOCUMENTS (Formateurs/Admin)
    // ========================================
    Route::post('/documents', [DocumentController::class, 'store'])->middleware('role:teacher,admin');
    Route::put('/documents/{id}', [DocumentController::class, 'update'])->middleware('role:teacher,admin');
    Route::delete('/documents/{id}', [DocumentController::class, 'destroy'])->middleware('role:teacher,admin');
    Route::post('/documents/{id}/download', [DocumentController::class, 'download']);
    Route::post('/documents/{id}/rate', [DocumentController::class, 'rate']);

    // ========================================
    // UPLOAD D'IMAGES
    // ========================================
    Route::post('/upload/news-image', [ImageUploadController::class, 'uploadNewsImage']);
    Route::post('/upload/image', [ImageUploadController::class, 'uploadImage']);
    Route::delete('/upload/image', [ImageUploadController::class, 'deleteImage']);
    Route::get('/upload/image-url', [ImageUploadController::class, 'getImageUrl']);

    // ========================================
    // DASHBOARD & STATISTIQUES
    // ========================================
    Route::get('/dashboard/stats/{userId}', [DashboardController::class, 'getStats']);
    Route::get('/dashboard/recommendations/{userId}', [DashboardController::class, 'getRecommendations']);
    Route::get('/dashboard/recent-activity/{userId}', [DashboardController::class, 'getRecentActivity']);
    Route::get('/dashboard/progress/{userId}', [DashboardController::class, 'getProgress']);

    // ========================================
    // NOTIFICATIONS
    // ========================================
    Route::get('/notifications/{userId}', [NotificationController::class, 'index']);
    Route::post('/notifications', [NotificationController::class, 'store']);
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::put('/notifications/{id}/archive', [NotificationController::class, 'archive']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
    Route::get('/notifications/unread-count/{userId}', [NotificationController::class, 'getUnreadCount']);
    Route::post('/notifications/mark-all-read/{userId}', [NotificationController::class, 'markAllAsRead']);

    // ========================================
    // CERTIFICATS & RÉCOMPENSES
    // ========================================
    Route::get('/certificates/{userId}', [CertificateController::class, 'index']);
    Route::post('/certificates', [CertificateController::class, 'store']);
    Route::get('/certificates/{id}', [CertificateController::class, 'show']);
    Route::delete('/certificates/{id}', [CertificateController::class, 'destroy']);
    Route::get('/certificates/validate/{certificateId}', [CertificateController::class, 'validate']);
    Route::get('/certificates/stats/{userId}', [CertificateController::class, 'getStats']);

    // ========================================
    // RECHERCHE
    // ========================================
    Route::get('/search', [SearchController::class, 'globalSearch']);
    Route::get('/search/courses', [SearchController::class, 'searchCourses']);
    Route::get('/search/documents', [SearchController::class, 'searchDocuments']);
    Route::get('/search/internships', [SearchController::class, 'searchInternships']);
    Route::get('/search/suggestions', [SearchController::class, 'getSuggestions']);

    // ========================================
    // PAIEMENTS & ABONNEMENTS
    // ========================================
    Route::get('/subscriptions/{userId}', [PaymentController::class, 'getSubscriptions']);
    Route::post('/subscriptions', [PaymentController::class, 'createSubscription']);
    Route::put('/subscriptions/{id}', [PaymentController::class, 'updateSubscription']);
    Route::delete('/subscriptions/{id}', [PaymentController::class, 'deleteSubscription']);
    Route::post('/subscriptions/{id}/cancel', [PaymentController::class, 'cancelSubscription']);
    Route::post('/subscriptions/{id}/renew', [PaymentController::class, 'renewSubscription']);
    Route::post('/payments', [PaymentController::class, 'processPayment']);
    Route::get('/payments/history/{userId}', [PaymentController::class, 'getPaymentHistory']);

    // ========================================
    // SUPPORT & AIDE
    // ========================================
    Route::get('/support/tickets/{userId}', [SupportController::class, 'getUserTickets']);
    Route::post('/support/tickets', [SupportController::class, 'createTicket']);
    Route::get('/support/tickets/{id}', [SupportController::class, 'getTicket']);
    Route::put('/support/tickets/{id}', [SupportController::class, 'updateTicket']);
    Route::delete('/support/tickets/{id}', [SupportController::class, 'deleteTicket']);
    Route::get('/support/faq', [SupportController::class, 'getFAQ']);
    Route::post('/support/faq', [SupportController::class, 'createFAQ']);
    Route::put('/support/faq/{id}', [SupportController::class, 'updateFAQ']);
    Route::delete('/support/faq/{id}', [SupportController::class, 'deleteFAQ']);
    Route::get('/support/categories', [SupportController::class, 'getCategories']);

    // ========================================
    // ANALYTICS & RAPPORTS
    // ========================================
    Route::get('/analytics/courses', [AnalyticsController::class, 'getCourseStats']);
    Route::get('/analytics/users', [AnalyticsController::class, 'getUserStats']);
    Route::get('/analytics/revenue', [AnalyticsController::class, 'getRevenueStats']);
    Route::get('/analytics/content', [AnalyticsController::class, 'getContentStats']);
    Route::get('/reports/generate', [AnalyticsController::class, 'generateReport']);

    // ========================================
    // GESTION DES FICHIERS
    // ========================================
    Route::post('/files/upload', [FileController::class, 'upload']);
    Route::get('/files/{id}', [FileController::class, 'download']);
    Route::delete('/files/{id}', [FileController::class, 'destroy']);
    Route::get('/files/user/{userId}', [FileController::class, 'getUserFiles']);
    Route::get('/files/{id}/info', [FileController::class, 'getFileInfo']);
    Route::put('/files/{id}', [FileController::class, 'update']);
    Route::get('/files/categories', [FileController::class, 'getCategories']);
    Route::post('/files/{id}/share', [FileController::class, 'share']);
    Route::get('/files/stats/{userId}', [FileController::class, 'getFileStats']);
});

// ========================================
// ROUTES DE TEST (pour développement)
// ========================================
Route::get('/test/payment-system', [App\Http\Controllers\TestPaymentController::class, 'testPaymentSystem']);
Route::get('/test/payment-relations', [App\Http\Controllers\TestPaymentController::class, 'testRelations']);
