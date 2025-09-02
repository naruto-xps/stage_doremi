<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use App\Models\Progression;
use App\Models\InternshipRequest;
use App\Models\Payment;
use App\Models\Document;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * @OA\Tag(
 *     name="Analytics",
 *     description="API pour les analytics et rapports"
 * )
 */
class AnalyticsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/analytics/courses",
     *     summary="Obtenir les statistiques des cours",
     *     tags={"Analytics"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="period",
     *         in="query",
     *         description="Période (7d, 30d, 90d, 1y)",
     *         required=false,
     *         @OA\Schema(type="string", default="30d")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Statistiques des cours récupérées avec succès"
     *     ),
     *     @OA\Response(response=403, description="Accès refusé")
     * )
     */
    public function getCourseStats(Request $request)
    {
        // Seuls les admins peuvent accéder aux analytics
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $period = $request->get('period', '30d');
        $startDate = $this->getStartDate($period);

        $stats = [
            'total_courses' => Course::count(),
            'active_courses' => Course::where('status', 'active')->count(),
            'courses_created_period' => Course::where('created_at', '>=', $startDate)->count(),
            'total_enrollments' => Progression::count(),
            'enrollments_period' => Progression::where('created_at', '>=', $startDate)->count(),
            'completion_rate' => $this->calculateCompletionRate(),
            'popular_categories' => $this->getPopularCategories(),
            'top_courses' => $this->getTopCourses(),
            'courses_by_education_level' => $this->getCoursesByEducationLevel(),
            'courses_by_difficulty' => $this->getCoursesByDifficulty()
        ];

        return response()->json($stats);
    }

    /**
     * @OA\Get(
     *     path="/api/analytics/users",
     *     summary="Obtenir les statistiques des utilisateurs",
     *     tags={"Analytics"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="period",
     *         in="query",
     *         description="Période (7d, 30d, 90d, 1y)",
     *         required=false,
     *         @OA\Schema(type="string", default="30d")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Statistiques des utilisateurs récupérées avec succès"
     *     )
     * )
     */
    public function getUserStats(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $period = $request->get('period', '30d');
        $startDate = $this->getStartDate($period);

        $stats = [
            'total_users' => User::count(),
            'users_period' => User::where('created_at', '>=', $startDate)->count(),
            'users_by_role' => $this->getUsersByRole(),
            'premium_users' => User::where('is_premium', true)->count(),
            'active_users' => $this->getActiveUsers($startDate),
            'user_growth' => $this->getUserGrowth($startDate),
            'top_contributors' => $this->getTopContributors(),
            'user_retention' => $this->getUserRetention($startDate)
        ];

        return response()->json($stats);
    }

    /**
     * @OA\Get(
     *     path="/api/analytics/revenue",
     *     summary="Obtenir les statistiques de revenus",
     *     tags={"Analytics"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="period",
     *         in="query",
     *         description="Période (7d, 30d, 90d, 1y)",
     *         required=false,
     *         @OA\Schema(type="string", default="30d")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Statistiques de revenus récupérées avec succès"
     *     )
     * )
     */
    public function getRevenueStats(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $period = $request->get('period', '30d');
        $startDate = $this->getStartDate($period);

        $stats = [
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'revenue_period' => Payment::where('status', 'completed')
                ->where('created_at', '>=', $startDate)
                ->sum('amount'),
            'subscriptions_count' => Payment::where('status', 'completed')->count(),
            'revenue_by_plan' => $this->getRevenueByPlan($startDate),
            'monthly_revenue' => $this->getMonthlyRevenue($startDate),
            'top_paying_users' => $this->getTopPayingUsers(),
            'conversion_rate' => $this->getConversionRate()
        ];

        return response()->json($stats);
    }

    /**
     * @OA\Get(
     *     path="/api/analytics/content",
     *     summary="Obtenir les statistiques du contenu",
     *     tags={"Analytics"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Statistiques du contenu récupérées avec succès"
     *     )
     * )
     */
    public function getContentStats()
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $stats = [
            'total_documents' => Document::count(),
            'published_documents' => Document::where('status', 'published')->count(),
            'total_videos' => Video::count(),
            'total_internships' => InternshipRequest::count(),
            'content_by_category' => $this->getContentByCategory(),
            'most_viewed_content' => $this->getMostViewedContent(),
            'content_engagement' => $this->getContentEngagement()
        ];

        return response()->json($stats);
    }

    /**
     * @OA\Get(
     *     path="/api/reports/generate",
     *     summary="Générer un rapport personnalisé",
     *     tags={"Analytics"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Type de rapport (courses, users, revenue, content)",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="period",
     *         in="query",
     *         description="Période (7d, 30d, 90d, 1y)",
     *         required=false,
     *         @OA\Schema(type="string", default="30d")
     *     ),
     *     @OA\Parameter(
     *         name="format",
     *         in="query",
     *         description="Format du rapport (json, csv)",
     *         required=false,
     *         @OA\Schema(type="string", default="json")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rapport généré avec succès"
     *     )
     * )
     */
    public function generateReport(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $type = $request->get('type');
        $period = $request->get('period', '30d');
        $format = $request->get('format', 'json');

        $startDate = $this->getStartDate($period);

        switch ($type) {
            case 'courses':
                $data = $this->generateCourseReport($startDate);
                break;
            case 'users':
                $data = $this->generateUserReport($startDate);
                break;
            case 'revenue':
                $data = $this->generateRevenueReport($startDate);
                break;
            case 'content':
                $data = $this->generateContentReport($startDate);
                break;
            default:
                return response()->json(['message' => 'Type de rapport invalide'], 400);
        }

        if ($format === 'csv') {
            return $this->generateCSV($data, $type);
        }

        return response()->json([
            'report_type' => $type,
            'period' => $period,
            'generated_at' => now(),
            'data' => $data
        ]);
    }

    // Méthodes privées pour les calculs

    private function getStartDate($period)
    {
        switch ($period) {
            case '7d':
                return Carbon::now()->subDays(7);
            case '30d':
                return Carbon::now()->subDays(30);
            case '90d':
                return Carbon::now()->subDays(90);
            case '1y':
                return Carbon::now()->subYear();
            default:
                return Carbon::now()->subDays(30);
        }
    }

    private function calculateCompletionRate()
    {
        $totalEnrollments = Progression::count();
        if ($totalEnrollments === 0) return 0;

        $completedCourses = Progression::where('progress', 100)->count();
        return round(($completedCourses / $totalEnrollments) * 100, 2);
    }

    private function getPopularCategories()
    {
        return Course::select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
    }

    private function getTopCourses()
    {
        return Course::orderBy('students_count', 'desc')
            ->orderBy('rating', 'desc')
            ->limit(10)
            ->get(['id', 'title', 'students_count', 'rating', 'category']);
    }

    private function getCoursesByEducationLevel()
    {
        return Course::select('education_level', DB::raw('count(*) as count'))
            ->groupBy('education_level')
            ->get();
    }

    private function getCoursesByDifficulty()
    {
        return Course::select('level', DB::raw('count(*) as count'))
            ->groupBy('level')
            ->get();
    }

    private function getUsersByRole()
    {
        return User::select('role', DB::raw('count(*) as count'))
            ->groupBy('role')
            ->get();
    }

    private function getActiveUsers($startDate)
    {
        return Progression::where('updated_at', '>=', $startDate)
            ->distinct('user_id')
            ->count();
    }

    private function getUserGrowth($startDate)
    {
        $currentUsers = User::where('created_at', '>=', $startDate)->count();
        $previousPeriod = Carbon::parse($startDate)->subDays(30);
        $previousUsers = User::where('created_at', '>=', $previousPeriod)
            ->where('created_at', '<', $startDate)
            ->count();

        if ($previousUsers === 0) return 100;

        return round((($currentUsers - $previousUsers) / $previousUsers) * 100, 2);
    }

    private function getTopContributors()
    {
        return User::where('role', 'teacher')
            ->withCount('courses')
            ->orderBy('courses_count', 'desc')
            ->limit(10)
            ->get(['id', 'name', 'email', 'courses_count']);
    }

    private function getUserRetention($startDate)
    {
        // Logique simplifiée pour le taux de rétention
        $totalUsers = User::count();
        $activeUsers = Progression::where('updated_at', '>=', $startDate)
            ->distinct('user_id')
            ->count();

        return round(($activeUsers / $totalUsers) * 100, 2);
    }

    private function getRevenueByPlan($startDate)
    {
        return Payment::where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->join('subscriptions', 'payments.subscription_id', '=', 'subscriptions.id')
            ->select('subscriptions.plan_type', DB::raw('sum(payments.amount) as total_revenue'))
            ->groupBy('subscriptions.plan_type')
            ->get();
    }

    private function getMonthlyRevenue($startDate)
    {
        return Payment::where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('sum(amount) as revenue')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    private function getTopPayingUsers()
    {
        return Payment::where('status', 'completed')
            ->join('users', 'payments.user_id', '=', 'users.id')
            ->select('users.id', 'users.name', 'users.email', DB::raw('sum(payments.amount) as total_spent'))
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderBy('total_spent', 'desc')
            ->limit(10)
            ->get();
    }

    private function getConversionRate()
    {
        $totalUsers = User::count();
        $premiumUsers = User::where('is_premium', true)->count();

        if ($totalUsers === 0) return 0;

        return round(($premiumUsers / $totalUsers) * 100, 2);
    }

    private function getContentByCategory()
    {
        return Document::select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->get();
    }

    private function getMostViewedContent()
    {
        return Document::orderBy('views', 'desc')
            ->limit(10)
            ->get(['id', 'title', 'views', 'download_count', 'category']);
    }

    private function getContentEngagement()
    {
        $totalViews = Document::sum('views');
        $totalDownloads = Document::sum('download_count');
        $totalDocuments = Document::count();

        return [
            'average_views' => $totalDocuments > 0 ? round($totalViews / $totalDocuments, 2) : 0,
            'average_downloads' => $totalDocuments > 0 ? round($totalDownloads / $totalDocuments, 2) : 0,
            'engagement_rate' => $totalViews > 0 ? round(($totalDownloads / $totalViews) * 100, 2) : 0
        ];
    }

    private function generateCourseReport($startDate)
    {
        return [
            'summary' => [
                'total_courses' => Course::count(),
                'courses_created_period' => Course::where('created_at', '>=', $startDate)->count(),
                'completion_rate' => $this->calculateCompletionRate()
            ],
            'courses' => Course::where('created_at', '>=', $startDate)
                ->with(['chapters', 'progression'])
                ->get()
        ];
    }

    private function generateUserReport($startDate)
    {
        return [
            'summary' => [
                'total_users' => User::count(),
                'new_users_period' => User::where('created_at', '>=', $startDate)->count(),
                'premium_users' => User::where('is_premium', true)->count()
            ],
            'users' => User::where('created_at', '>=', $startDate)
                ->with(['progression', 'subscriptions'])
                ->get()
        ];
    }

    private function generateRevenueReport($startDate)
    {
        return [
            'summary' => [
                'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
                'revenue_period' => Payment::where('status', 'completed')
                    ->where('created_at', '>=', $startDate)
                    ->sum('amount')
            ],
            'payments' => Payment::where('created_at', '>=', $startDate)
                ->with(['user', 'subscription'])
                ->get()
        ];
    }

    private function generateContentReport($startDate)
    {
        return [
            'summary' => [
                'total_documents' => Document::count(),
                'total_videos' => Video::count(),
                'total_views' => Document::sum('views') + Video::sum('views')
            ],
            'documents' => Document::where('created_at', '>=', $startDate)->get(),
            'videos' => Video::where('created_at', '>=', $startDate)->get()
        ];
    }

    private function generateCSV($data, $type)
    {
        // Logique simplifiée pour la génération CSV
        $filename = "report_{$type}_" . date('Y-m-d_H-i-s') . ".csv";
        
        return response()->json([
            'message' => 'Rapport CSV généré',
            'filename' => $filename,
            'data' => $data
        ]);
    }
}
