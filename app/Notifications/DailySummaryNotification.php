<?php

namespace App\Notifications;

use App\Services\MailSettingsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DailySummaryNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly array $stats)
    {
    }

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if (MailSettingsService::emailEnabled('email_daily_summary_enabled')) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Daily Dating Summary')
            ->greeting("Hi {$notifiable->name},")
            ->line('Here is your account performance for the last 24 hours:')
            ->line("• Profile views: {$this->stats['profile_views_today']}")
            ->line("• New likes: {$this->stats['likes_today']}")
            ->line("• New matches: {$this->stats['matches_today']}")
            ->line("• Unread messages: {$this->stats['unread_messages']}")
            ->action('Open Dashboard', route('dashboard'))
            ->line('Keep your profile active to increase your visibility.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'daily_summary',
            'stats' => $this->stats,
            'url' => route('dashboard'),
        ];
    }
}
