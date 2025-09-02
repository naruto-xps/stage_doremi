<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\Progression;
use App\Models\InternshipRequest;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Dashboard",
 *     description="API pour le tableau de bord et les statistiques"
 * )
 */
class DashboardController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/dashboard/stats/{userId}",
     *     summary="Obtenir les statistiques du tableau de bord pour un utilisateur",
     *     tags={"Dashboard"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="ID de l'utilisateur",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Statistiques récupérées avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="totalCourses", type="integer"),
     *             @OA\Property(property="completedCourses", type="integer"),
     *             @OA\Property(property="inProgressCourses", type="integer"),
     *             @OA\Property(property="totalHours", type="integer"),
     *             @OA\Property(property="certificates", type="integer"),
     *             @OA\Property(property="unreadMessages", type="integer"),
     *             @OA\Property(property="recentActivity", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(response=403, description="Accès refusé")
     * )
     */
    public function getStats($userId)
    {
        // Vérifier que l'utilisateur connecté peut accéder à ces données
        if (Auth::id() != $userId && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $user = User::findOrFail($userId);

        // Statistiques des cours
        $totalCourses = Course::count();
        $userProgressions = Progression::where('user_id', $userId)->get();
        
        $completedCourses = $userProgressions->where('progress', 100)->count();
        $inProgressCourses = $userProgressions->where('progress', '>', 0)->where('progress', '<', 100)->count();
        
        // Calculer les heures totales (estimation basée sur la durée des cours)
        $totalHours = $userProgressions->sum(function($progression) {
            $course = Course::find($progression->course_id);
            if ($course && $course->duration) {
                // Convertir la durée en heures (ex: "2h 30min" -> 2.5)
                preg_match('/(\d+)h\s*(\d+)?min?/', $course->duration, $matches);
                $hours = (int)($matches[1] ?? 0);
                $minutes = (int)($matches[2] ?? 0);
                return $hours + ($minutes / 60);
            }
            return 0;
        });

        // Certificats (cours terminés)
        $certificates = $completedCourses;

        // Messages non lus
        $unreadMessages = Message::where('recipient_id', $userId)
            ->where('is_read', false)
            ->count();

        // Activité récente
        $recentActivity = $this->getRecentActivity($userId);

        return response()->json([
            'totalCourses' => $totalCourses,
            'completedCourses' => $completedCourses,
            'inProgressCourses' => $inProgressCourses,
            'totalHours' => round($totalHours, 1),
            'certificates' => $certificates,
            'unreadMessages' => $unreadMessages,
            'recentActivity' => $recentActivity
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/dashboard/recommendations/{userId}",
     *     summary="Obtenir les cours recommandés pour un utilisateur",
     *     tags={"Dashboard"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="ID de l'utilisateur",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Recommandations récupérées avec succès"
     *     )
     * )
     */
    public function getRecommendations($userId)
    {
        if (Auth::id() != $userId && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $user = User::findOrFail($userId);
        
        // Cours déjà suivis
        $followedCourseIds = Progression::where('user_id', $userId)
            ->pluck('course_id')
            ->toArray();

        // Recommandations basées sur le niveau d'éducation et les préférences
        $recommendations = Course::whereNotIn('id', $followedCourseIds)
            ->where('education_level', $user->education_level ?? 'etudiant')
            ->orderBy('rating', 'desc')
            ->orderBy('students_count', 'desc')
            ->limit(6)
            ->get();

        return response()->json($recommendations);
    }

    /**
     * @OA\Get(
     *     path="/api/dashboard/recent-activity/{userId}",
     *     summary="Obtenir l'activité récente d'un utilisateur",
     *     tags={"Dashboard"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="ID de l'utilisateur",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Activité récente récupérée avec succès"
     *     )
     * )
     */
    public function getRecentActivity($userId)
    {
        if (Auth::id() != $userId && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $activities = [];

        // Progression des cours
        $recentProgressions = Progression::where('user_id', $userId)
            ->with('course')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentProgressions as $progression) {
            $activities[] = [
                'type' => 'course_progress',
                'title' => 'Progression dans ' . $progression->course->title,
                'description' => 'Progression: ' . $progression->progress . '%',
                'timestamp' => $progression->updated_at,
                'icon' => 'book-open'
            ];
        }

        // Messages reçus
        $recentMessages = Message::where('recipient_id', $userId)
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        foreach ($recentMessages as $message) {
            $activities[] = [
                'type' => 'message',
                'title' => 'Message de ' . $message->sender->name,
                'description' => substr($message->content, 0, 50) . '...',
                'timestamp' => $message->created_at,
                'icon' => 'message-square'
            ];
        }

        // Candidatures de stage
        $recentApplications = InternshipRequest::where('user_id', $userId)
            ->with('internship')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        foreach ($recentApplications as $application) {
            $activities[] = [
                'type' => 'internship_application',
                'title' => 'Candidature pour ' . $application->internship->title,
                'description' => 'Statut: ' . $application->status,
                'timestamp' => $application->created_at,
                'icon' => 'briefcase'
            ];
        }

        // Trier par timestamp et limiter à 10 activités
        usort($activities, function($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });

        return array_slice($activities, 0, 10);
    }

    /**
     * @OA\Get(
     *     path="/api/dashboard/progress/{userId}",
     *     summary="Obtenir la progression globale d'un utilisateur",
     *     tags={"Dashboard"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="ID de l'utilisateur",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Progression récupérée avec succès"
     *     )
     * )
     */
    public function getProgress($userId)
    {
        if (Auth::id() != $userId && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $progressions = Progression::where('user_id', $userId)
            ->with('course')
            ->get();

        $totalProgress = 0;
        $coursesProgress = [];

        foreach ($progressions as $progression) {
            $totalProgress += $progression->progress;
            $coursesProgress[] = [
                'courseId' => $progression->course_id,
                'courseTitle' => $progression->course->title,
                'progress' => $progression->progress,
                'lastAccessed' => $progression->updated_at
            ];
        }

        $averageProgress = $progressions->count() > 0 ? round($totalProgress / $progressions->count(), 1) : 0;

        return response()->json([
            'averageProgress' => $averageProgress,
            'totalCourses' => $progressions->count(),
            'coursesProgress' => $coursesProgress
        ]);
    }


}
