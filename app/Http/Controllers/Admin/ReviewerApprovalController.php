<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\ReviewerApprovedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class ReviewerApprovalController extends Controller
{
    /**
     * Display a listing of pending and past reviewer applications.
     */
    public function index(): View
    {
        $pendingReviewers = User::where('reviewer_status', 'pending')
            ->latest()
            ->paginate(15);

        $pastReviewers = User::whereIn('reviewer_status', ['approved', 'rejected'])
            ->latest('reviewer_approved_at')
            ->take(20)
            ->get();

        return view('admin.reviewers.index', compact('pendingReviewers', 'pastReviewers'));
    }

    /**
     * Approve a pending reviewer application.
     */
    public function approve(User $user): RedirectResponse
    {
        if ($user->reviewer_status !== 'pending') {
            return back()->with('error', 'This user application is not pending approval.');
        }

        $user->update([
            'reviewer_status' => 'approved',
            'reviewer_approved_at' => now(),
        ]);

        Role::findOrCreate('reviewer');
        $user->assignRole('reviewer');

        // Send email notification to user
        $user->notify(new ReviewerApprovedNotification());

        return back()->with(
            'status',
            "Reviewer account for {$user->name} has been approved. Notification email dispatched."
        );
    }

    /**
     * Reject a pending reviewer application.
     */
    public function reject(User $user): RedirectResponse
    {
        if ($user->reviewer_status !== 'pending') {
            return back()->with('error', 'This user application is not pending approval.');
        }

        $user->update([
            'reviewer_status' => 'rejected',
        ]);

        return back()->with(
            'status',
            "Reviewer application for {$user->name} has been declined."
        );
    }
}
