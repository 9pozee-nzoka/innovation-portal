<?php

use App\Enums\InnovationStatus;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ReviewerApprovalController;
use App\Http\Controllers\InnovationController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewerDashboardController;
use App\Models\Innovation;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $stats = [
        'total' => Innovation::count(),
        'under_review' => Innovation::whereIn('status', [InnovationStatus::SUBMITTED, InnovationStatus::UNDER_REVIEW, InnovationStatus::RESUBMITTED])->count(),
        'approved' => Innovation::where('status', InnovationStatus::APPROVED)->count(),
        'innovators' => User::count(),
    ];

    $recentInnovations = Innovation::with(['author', 'attachments'])
        ->whereIn('status', [InnovationStatus::SUBMITTED, InnovationStatus::UNDER_REVIEW, InnovationStatus::APPROVED])
        ->latest('submitted_at')
        ->take(3)
        ->get();

    return view('welcome', compact('stats', 'recentInnovations'));
})->name('home');

Route::get('/dashboard', function () {
    if (auth()->user()->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    }

    if (auth()->user()->hasRole('reviewer') && !auth()->user()->hasRole('innovator')) {
        return redirect()->route('reviewer.dashboard');
    }

    return redirect()->route('innovations.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('innovations', InnovationController::class);
    Route::post('innovations/{innovation}/submit', [InnovationController::class, 'submit'])->name('innovations.submit');
    Route::delete('innovations/{innovation}/attachments/{attachment}', [InnovationController::class, 'destroyAttachment'])->name('innovations.attachments.destroy');

    // Reviewer Portal Routes
    Route::middleware(['role_or_permission:reviewer|admin|view reviewer dashboard'])
        ->prefix('reviewer')
        ->name('reviewer.')
        ->group(function () {
            Route::get('/dashboard', [ReviewerDashboardController::class, 'index'])->name('dashboard');
            Route::get('/innovations/{innovation}/evaluate', [ReviewerDashboardController::class, 'evaluate'])->name('evaluate');
            Route::post('/innovations/{innovation}/review', [ReviewerDashboardController::class, 'storeReview'])->name('review.store');
            Route::post('/innovations/{innovation}/comment', [ReviewerDashboardController::class, 'storeComment'])->name('comment.store');
        });

    // Admin Reviewer Approvals
    Route::middleware(['role:admin'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
            Route::get('/reviewers', [ReviewerApprovalController::class, 'index'])->name('reviewers.index');
            Route::post('/reviewers/{user}/approve', [ReviewerApprovalController::class, 'approve'])->name('reviewers.approve');
            Route::post('/reviewers/{user}/reject', [ReviewerApprovalController::class, 'reject'])->name('reviewers.reject');
        });

    // OTP Two-Factor / Email Passcode Routes
    Route::get('/otp/verify', [OtpController::class, 'show'])->name('otp.verify');
    Route::post('/otp/verify', [OtpController::class, 'verify'])->name('otp.verify.submit');
    Route::post('/otp/resend', [OtpController::class, 'resend'])->name('otp.resend');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
