<?php

namespace App\Notifications;

use App\Models\User;
use App\Services\MailSettingsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WaveReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly User $sender,
        public readonly string $emoji = '👋',
    ) {
    }

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if (MailSettingsService::emailEnabled('email_feature_usage_enabled')) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("{$this->sender->name} sent you a wave {$this->emoji}")
            ->line("{$this->sender->name} waved at you {$this->emoji}")
            ->action('View Waves', route('wave.received'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'wave_received',
            'sender_id' => $this->sender->id,
            'sender_name' => $this->sender->name,
            'emoji' => $this->emoji,
            'message' => "{$this->sender->name} sent you a wave {$this->emoji}",
            'url' => route('wave.received'),
        ];
    }
}
