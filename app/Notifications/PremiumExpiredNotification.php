<?php

namespace App\Notifications;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PremiumExpiredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $tpl = EmailTemplate::findByKey('premium_expired');
        if ($tpl) {
            ['subject' => $subject, 'html' => $html] = $tpl->render([
                '{user_name}'   => $notifiable->name,
                '{app_name}'    => config('app.name'),
                '{app_url}'     => config('app.url'),
                '{premium_url}' => route('premium.show'),
            ]);
            return (new MailMessage)->subject($subject)->view('emails.dynamic', ['html' => $html, 'subject' => $subject]);
        }

        return (new MailMessage)
            ->subject('Your ' . config('app.name') . ' Premium has expired')
            ->markdown('emails.premium-expired', [
                'user'    => $notifiable,
                'appName' => config('app.name', 'HeartsConnect'),
                'appUrl'  => config('app.url'),
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'    => 'premium_expired',
            'message' => 'Your Premium membership has expired. Renew to keep your benefits!',
            'url'     => route('premium.show'),
        ];
    }
}
