<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CompleteProfileNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $tries = 3;

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Complete Your CareerCompass Profile')
            ->greeting('Hello!')
            ->line('Thank you for joining CareerCompass! To get the most out of your experience, please complete your profile.')
            ->action('Complete Profile', route('profile.edit'))
            ->line('This will help us provide personalized career recommendations tailored to your interests and skills.')
            ->salutation('Best regards, The CareerCompass Team');
    }
}