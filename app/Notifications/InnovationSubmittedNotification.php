<?php

namespace App\Notifications;

use App\Models\Innovation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InnovationSubmittedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Innovation $innovation
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
        $author = $this->innovation->author;
        $department = $author->department ? " ({$author->department})" : '';
        $isResubmission = $this->innovation->status->value === 'resubmitted';
        $prefix = $isResubmission ? 'Resubmitted Proposal' : 'New Innovation Proposal';

        return (new MailMessage)
            ->subject("[KSG Innovation Portal] {$prefix}: {$this->innovation->title}")
            ->greeting("Dear {$notifiable->name},")
            ->line("An innovation proposal has been {$this->innovation->status->label()} for committee evaluation.")
            ->line("**Title:** {$this->innovation->title}")
            ->line("**Lead Innovator:** {$author->name}{$department}")
            ->line("**Supporting Attachments:** " . $this->innovation->attachments->count() . " document(s)")
            ->action('Open Reviewer Evaluation Workspace', route('reviewer.evaluate', $this->innovation))
            ->line("Please review the proposal, leave any questions on the internal discussion board, and submit your score and decision.")
            ->salutation("Kenya School of Government — Innovation Directorate");
    }
}
