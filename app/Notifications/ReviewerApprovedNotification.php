<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewerApprovedNotification extends Notification
{
    use Queueable;

    public function __construct() {}

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
        return (new MailMessage)
            ->subject('[KSG Innovation Portal] Reviewer Access Approved')
            ->greeting("Dear {$notifiable->name},")
            ->line("Your Reviewer access has been approved. You may now evaluate proposals in the Reviewer Workspace.")
            ->action('Access Reviewer Workspace', route('reviewer.dashboard'))
            ->line("Thank you for contributing your expertise to evaluating and advancing innovations at the Kenya School of Government.")
            ->salutation("Kenya School of Government — Innovation Directorate");
    }
}
