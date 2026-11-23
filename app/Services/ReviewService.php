<?php

namespace App\Services;

use App\Enums\InnovationStatus;
use App\Models\Innovation;
use App\Models\InnovationStatusHistory;
use App\Models\Review;
use App\Models\ReviewerComment;
use App\Models\User;
use App\Notifications\InnovationReviewedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class ReviewService
{
    /**
     * Start reviewing an innovation (transitions submitted -> under_review).
     */
    public function startReview(Innovation $innovation, User $reviewer): Innovation
    {
        if (in_array($innovation->status, [InnovationStatus::SUBMITTED, InnovationStatus::RESUBMITTED], true)) {
            DB::transaction(function () use ($innovation, $reviewer) {
                $previousStatus = $innovation->status;
                $innovation->update([
                    'status' => InnovationStatus::UNDER_REVIEW,
                ]);

                InnovationStatusHistory::create([
                    'innovation_id' => $innovation->id,
                    'changed_by_user_id' => $reviewer->id,
                    'from_status' => $previousStatus,
                    'to_status' => InnovationStatus::UNDER_REVIEW,
                    'remarks' => "Evaluation started by {$reviewer->name}.",
                    'created_at' => now(),
                ]);
            });
        }

        return $innovation;
    }

    /**
     * Post an inter-reviewer collaborative comment.
     */
    public function postComment(Innovation $innovation, User $reviewer, string $commentText): ReviewerComment
    {
        return ReviewerComment::create([
            'innovation_id' => $innovation->id,
            'user_id' => $reviewer->id,
            'comment' => $commentText,
        ]);
    }

    /**
     * Submit formal evaluation and decision.
     *
     * @param array{rating: int, decision: string, feedback: string, internal_notes?: ?string} $data
     */
    public function submitReview(Innovation $innovation, User $reviewer, array $data): Review
    {
        $review = DB::transaction(function () use ($innovation, $reviewer, $data) {
            $previousStatus = $innovation->status;

            // Map string decision to InnovationStatus enum
            $newStatus = match ($data['decision']) {
                'approved' => InnovationStatus::APPROVED,
                'rejected' => InnovationStatus::REJECTED,
                'revision_requested' => InnovationStatus::REVISION_REQUESTED,
            };

            // 1. Create Review record
            $review = Review::create([
                'innovation_id' => $innovation->id,
                'reviewer_id' => $reviewer->id,
                'rating' => (int) $data['rating'],
                'decision' => $data['decision'],
                'feedback' => $data['feedback'],
                'internal_notes' => $data['internal_notes'] ?? null,
            ]);

            // 2. Update innovation status
            $innovation->update([
                'status' => $newStatus,
            ]);

            // 3. Log audit history
            InnovationStatusHistory::create([
                'innovation_id' => $innovation->id,
                'changed_by_user_id' => $reviewer->id,
                'from_status' => $previousStatus,
                'to_status' => $newStatus,
                'remarks' => "Evaluation decision: {$newStatus->label()} (Rating: {$data['rating']}/5). Feedback: {$data['feedback']}",
                'created_at' => now(),
            ]);

            return $review;
        });

        // Notify Lead Author and Co-Innovators
        $recipients = collect([$innovation->author])->merge($innovation->collaborators)->filter()->unique('id');
        Notification::send($recipients, new InnovationReviewedNotification($innovation, $review));

        return $review;
    }
}
