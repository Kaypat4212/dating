<?php

namespace App\Notifications;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PremiumPurchasedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly string $plan,
        public readonly string $expiresAt,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $tpl = EmailTemplate::findByKey('premium_purchased');
        if ($tpl) {
            ['subject' => $subject, 'html' => $html] = $tpl->render([
                '{user_name}'    => $notifiable->name,
                '{plan}'         => ucfirst(str_replace('_', ' ', $this->plan)),
                '{expires_at}'   => $this->expiresAt,
                '{app_name}'     => config('app.name'),
                '{app_url}'      => config('app.url'),
                '{discover_url}' => route('discover.index'),
            ]);
            return (new MailMessage)->subject($subject)->view('emails.dynamic', ['html' => $html, 'subject' => $subject]);
        }

        return (new MailMessage)
            ->subject('🌟 Welcome to ' . config('app.name') . ' Premium!')
            ->markdown('emails.premium', [
                'user'      => $notifiable,
                'plan'      => $this->plan,
                'expiresAt' => $this->expiresAt,
                'appName'   => config('app.name', 'HeartsConnect'),
                'appUrl'    => config('app.url'),
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'       => 'premium_purchased',
            'plan'       => $this->plan,
            'expires_at' => $this->expiresAt,
            'message'    => "🌟 You're now a Premium member! Enjoy all premium features.",
            'url'        => route('premium.show'),
        ];
    }
}
