<?php

namespace App\Notifications;

use App\Services\MailSettingsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WeeklyDigestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly array $stats)
    {
    }

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if (MailSettingsService::emailEnabled('email_weekly_digest_enabled')) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('💌 Your Weekly Dating Summary — ' . config('app.name'))
            ->view('emails.weekly-digest', [
                'user'   => $notifiable,
                'stats'  => $this->stats,
                'appName' => config('app.name', 'HeartsConnect'),
                'appUrl'  => config('app.url'),
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'              => 'weekly_digest',
            'likes_this_week'   => $this->stats['likes_this_week'],
            'matches_this_week' => $this->stats['matches_this_week'],
            'url'               => route('dashboard'),
        ];
    }
}
