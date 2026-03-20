<?php

namespace App\Notifications;

use App\Services\MailSettingsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FeatureUsageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly string $feature,
        public readonly string $summary,
        public readonly ?string $url = null,
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
        $mail = (new MailMessage)
            ->subject("Activity Update: {$this->feature}")
            ->greeting("Hi {$notifiable->name},")
            ->line($this->summary)
            ->line('We\'ll keep sending your activity updates automatically.');

        if ($this->url) {
            $mail->action('Open Feature', $this->url);
        }

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'feature_usage',
            'feature' => $this->feature,
            'summary' => $this->summary,
            'url' => $this->url,
        ];
    }
}
