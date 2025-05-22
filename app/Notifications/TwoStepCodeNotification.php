<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TwoStepCodeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected string $code) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Confirmation Code')
            ->line('Your confirmation code is: ' . $this->code)
            ->line('This code will expire in 10 minutes.');
    }
}
