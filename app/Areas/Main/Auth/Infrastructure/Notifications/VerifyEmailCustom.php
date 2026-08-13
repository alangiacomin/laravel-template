<?php

namespace App\Areas\Main\Auth\Infrastructure\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmailCustom extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $spaUrl;

    public function __construct(string $spaUrl)
    {
        $this->spaUrl = $spaUrl;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return new MailMessage()
            ->subject(__('verification.email_subject'))
            ->greeting(__('verification.email_greeting', ['name' => $notifiable->name]))
            ->line(__('verification.email_line1'))
            ->action(__('verification.email_action'), $this->spaUrl)
            ->line(__('verification.email_line2'))
            ->salutation(__('verification.email_salutation'));
    }
}
