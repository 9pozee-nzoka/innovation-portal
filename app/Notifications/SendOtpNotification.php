<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendOtpNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $otpCode
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
        return (new MailMessage)
            ->subject('[KSG Innovation Portal] Your One-Time Passcode (OTP)')
            ->greeting("Dear {$notifiable->name},")
            ->line('Your one-time passcode for accessing the Kenya School of Government Innovation Portal is:')
            ->line("# {$this->otpCode}")
            ->line('This passcode is valid for **10 minutes**. For your security, never share this code with anyone.')
            ->line('If you did not initiate this request, please report it to KSG ICT Security.')
            ->salutation('Kenya School of Government — ICT & Innovation');
    }
}
