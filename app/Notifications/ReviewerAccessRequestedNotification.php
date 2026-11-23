<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewerAccessRequestedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public User $applicant
    ) {}

    /**
     * Delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Mail representation.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $dept = $this->applicant->department ? " ({$this->applicant->department})" : '';
        $phone = $this->applicant->phone ? " | Phone: {$this->applicant->phone}" : '';

        return (new MailMessage)
            ->subject('[KSG Innovation Portal] Action Required: New Reviewer Account Approval Request')
            ->greeting("Dear Administrator ({$notifiable->name}),")
            ->line("A new staff member has registered requesting **Reviewer / Evaluation Committee** access.")
            ->line("**Applicant:** {$this->applicant->name}{$dept}")
            ->line("**Email:** {$this->applicant->email}{$phone}")
            ->line("**Status:** Awaiting Administrator Approval (Zero portal access granted currently).")
            ->action('Review & Approve Applicant', route('admin.reviewers.index'))
            ->line("Please verify the applicant's committee appointment and approve or decline access in the Admin Console.")
            ->salutation("Kenya School of Government — Innovation Directorate");
    }
}
