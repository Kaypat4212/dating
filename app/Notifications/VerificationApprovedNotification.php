<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerificationApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🎉 Your HeartsConnect profile is now verified!')
            ->greeting("Congratulations, {$notifiable->name}!")
            ->line('Your identity has been verified and you now have the ✅ Verified badge on your profile.')
            ->line('Verified profiles get more trust and visibility from other members.')
            ->action('View Your Profile', route('profile.show', $notifiable->username));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'    => 'verification_approved',
            'message' => '✅ Your profile is now verified! The badge is live on your profile.',
            'url'     => route('profile.show', $notifiable->username),
        ];
    }
}
