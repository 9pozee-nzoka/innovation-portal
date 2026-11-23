<?php

namespace App\Notifications;

use App\Models\Innovation;
use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InnovationReviewedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Innovation $innovation,
        public Review $review
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $decisionLabel = match ($this->review->decision) {
            'approved' => 'Approved',
            'revision_requested' => 'Revision Requested',
            'rejected' => 'Rejected',
            default => ucfirst(str_replace('_', ' ', $this->review->decision)),
        };

        $subject = "[KSG Innovation Portal] Proposal Decision: {$this->innovation->title} — {$decisionLabel}";

        $message = (new MailMessage)
            ->subject($subject)
            ->greeting("Dear {$notifiable->name},");

        if ($this->review->decision === 'approved') {
            $message->line("🎉 **Congratulations!** Your innovation proposal has been reviewed and **Approved** by the Kenya School of Government Evaluation Committee.")
                    ->line("**Committee Rating:** ★ {$this->review->rating}/5");
        } elseif ($this->review->decision === 'revision_requested') {
            $message->line("The KSG Evaluation Committee has reviewed your proposal and requested **Specific Revisions** before it can be approved.")
                    ->line("**Committee Rating:** ★ {$this->review->rating}/5")
                    ->line("Please review the detailed feedback below, update your proposal accordingly, and resubmit it through the portal.");
        } else {
            $message->line("The KSG Evaluation Committee has evaluated your innovation proposal with decision: **Rejected**.")
                    ->line("**Committee Rating:** ★ {$this->review->rating}/5");
        }

        $message->line("---")
                ->line("**Reviewer Feedback:**")
                ->line("_{$this->review->feedback}_")
                ->line("---")
                ->action('View Innovation & Review Details', route('innovations.show', $this->innovation))
                ->salutation("Kenya School of Government — Innovation Directorate");

        return $message;
    }
}
