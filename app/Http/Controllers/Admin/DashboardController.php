<?php

namespace App\Http\Controllers\Admin;

use App\Enums\InnovationStatus;
use App\Http\Controllers\Controller;
use App\Models\Innovation;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        // Overview Statistics
        $stats = [
            'total_innovations' => Innovation::count(),
            'pending_review' => Innovation::whereIn('status', [
                InnovationStatus::SUBMITTED,
                InnovationStatus::UNDER_REVIEW,
                InnovationStatus::RESUBMITTED
            ])->count(),
            'approved' => Innovation::where('status', InnovationStatus::APPROVED)->count(),
            'rejected' => Innovation::where('status', InnovationStatus::REJECTED)->count(),
            'revision_requested' => Innovation::where('status', InnovationStatus::REVISION_REQUESTED)->count(),
            'total_users' => User::count(),
            'innovators' => User::role('innovator')->count(),
            'reviewers' => User::role('reviewer')->count(),
            'pending_reviewers' => User::where('reviewer_status', 'pending')->count(),
            'total_reviews' => Review::count(),
            'average_rating' => round(Review::avg('rating'), 1),
        ];

        // Recent Innovations (Last 7 days)
        $recentInnovations = Innovation::with(['author', 'attachments'])
            ->where('created_at', '>=', now()->subDays(7))
            ->latest()
            ->take(5)
            ->get();

        // Innovations by Status
        $innovationsByStatus = Innovation::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->status->value => $item->count];
            });

        // Monthly Submission Trends (Last 6 months)
        $submissionTrends = Innovation::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Top Innovators
        $topInnovators = User::withCount('innovations')
            ->having('innovations_count', '>', 0)
            ->orderByDesc('innovations_count')
            ->take(5)
            ->get();

        // Top Reviewers
        $topReviewers = User::withCount('reviews')
            ->having('reviews_count', '>', 0)
            ->orderByDesc('reviews_count')
            ->take(5)
            ->get();

        // Recent Reviews
        $recentReviews = Review::with(['innovation', 'reviewer'])
            ->latest()
            ->take(5)
            ->get();

        // Department Statistics
        $departmentStats = User::select('department', DB::raw('count(*) as count'))
            ->whereNotNull('department')
            ->groupBy('department')
            ->orderByDesc('count')
            ->take(8)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentInnovations',
            'innovationsByStatus',
            'submissionTrends',
            'topInnovators',
            'topReviewers',
            'recentReviews',
            'departmentStats'
        ));
    }
}
