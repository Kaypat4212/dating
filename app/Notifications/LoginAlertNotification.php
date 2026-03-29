<?php

namespace App\Notifications;

use App\Models\EmailTemplate;
use App\Services\MailSettingsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoginAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly string $ip,
        public readonly string $device,
        public readonly string $location,
        public readonly string $loginTime,
    ) {}

    public function via(object $notifiable): array
    {
        if (! MailSettingsService::emailEnabled('email_login_alert_enabled')
            || ! ($notifiable->preferences?->wantsEmail('email_login_alert') ?? true)) {
            return [];
        }

        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $tpl = EmailTemplate::findByKey('login_alert');
        if ($tpl) {
            ['subject' => $subject, 'html' => $html] = $tpl->render([
                '{user_name}'   => $notifiable->name,
                '{ip}'          => $this->ip,
                '{device}'      => $this->device,
                '{login_time}'  => $this->loginTime,
                '{app_name}'    => config('app.name'),
                '{app_url}'     => config('app.url'),
                '{settings_url}' => config('app.url') . '/profile/settings',
            ]);
            return (new MailMessage)->subject($subject)->view('emails.dynamic', ['html' => $html, 'subject' => $subject]);
        }

        return (new MailMessage)
            ->subject('New Login to Your ' . config('app.name') . ' Account')
            ->markdown('emails.login-alert', [
                'user'      => $notifiable,
                'ip'        => $this->ip,
                'device'    => $this->device,
                'location'  => $this->location,
                'loginTime' => $this->loginTime,
                'appName'   => config('app.name', 'HeartsConnect'),
                'appUrl'    => config('app.url'),
            ]);
    }
}
