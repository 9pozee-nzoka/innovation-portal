<?php

namespace App\Http\Controllers;

use App\Enums\InnovationStatus;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\StoreReviewerCommentRequest;
use App\Models\Innovation;
use App\Services\ReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewerDashboardController extends Controller
{
    public function __construct(
        protected ReviewService $reviewService
    ) {}

    /**
     * Display the Reviewer Evaluation Dashboard.
     */
    public function index(Request $request): View
    {
        $filter = $request->query('tab', 'queue'); // queue | in_review | decided | all

        $counts = [
            'queue' => Innovation::whereIn('status', [InnovationStatus::SUBMITTED, InnovationStatus::RESUBMITTED])->count(),
            'in_review' => Innovation::where('status', InnovationStatus::UNDER_REVIEW)->count(),
            'decided' => Innovation::whereIn('status', [InnovationStatus::APPROVED, InnovationStatus::REJECTED, InnovationStatus::REVISION_REQUESTED])->count(),
            'all' => Innovation::where('status', '!=', InnovationStatus::DRAFT)->count(),
        ];

        $query = Innovation::query()
            ->with(['author', 'attachments'])
            ->withCount(['reviews', 'collaborators', 'reviewerComments'])
            ->latest('submitted_at');

        match ($filter) {
            'queue' => $query->whereIn('status', [InnovationStatus::SUBMITTED, InnovationStatus::RESUBMITTED]),
            'in_review' => $query->where('status', InnovationStatus::UNDER_REVIEW),
            'decided' => $query->whereIn('status', [InnovationStatus::APPROVED, InnovationStatus::REJECTED, InnovationStatus::REVISION_REQUESTED]),
            default => $query->where('status', '!=', InnovationStatus::DRAFT),
        };

        $innovations = $query->paginate(10)->withQueryString();

        return view('reviewer.index', [
            'innovations' => $innovations,
            'counts' => $counts,
            'activeTab' => $filter,
        ]);
    }

    /**
     * Show evaluation screen for a specific innovation.
     */
    public function evaluate(Request $request, Innovation $innovation): View
    {
        // If it's submitted or resubmitted, start the review automatically
        if (in_array($innovation->status, [InnovationStatus::SUBMITTED, InnovationStatus::RESUBMITTED], true)) {
            $this->reviewService->startReview($innovation, $request->user());
        }

        $innovation->load([
            'author',
            'collaborators',
            'attachments.uploader',
            'reviews.reviewer',
            'reviewerComments.author',
            'statusHistories.actor',
        ]);

        return view('reviewer.evaluate', [
            'innovation' => $innovation,
        ]);
    }

    /**
     * Store an evaluation decision and rating.
     */
    public function storeReview(StoreReviewRequest $request, Innovation $innovation): RedirectResponse
    {
        $this->reviewService->submitReview(
            innovation: $innovation,
            reviewer: $request->user(),
            data: $request->validated()
        );

        return redirect()
            ->route('reviewer.dashboard')
            ->with('status', "Evaluation decision submitted for '{$innovation->title}'.");
    }

    /**
     * Post an inter-reviewer comment.
     */
    public function storeComment(StoreReviewerCommentRequest $request, Innovation $innovation): RedirectResponse
    {
        $this->reviewService->postComment(
            innovation: $innovation,
            reviewer: $request->user(),
            commentText: $request->input('comment')
        );

        return back()->with('status', 'Comment posted to the reviewer discussion board.');
    }
}
