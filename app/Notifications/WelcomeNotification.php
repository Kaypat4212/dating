<?php

namespace App\Notifications;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $tpl = EmailTemplate::findByKey('welcome');
        if ($tpl) {
            ['subject' => $subject, 'html' => $html] = $tpl->render([
                '{user_name}' => $notifiable->name,
                '{app_name}'  => config('app.name'),
                '{app_url}'   => config('app.url'),
                '{setup_url}' => config('app.url') . '/setup/step/1',
            ]);
            return (new MailMessage)->subject($subject)->view('emails.dynamic', ['html' => $html, 'subject' => $subject]);
        }

        return (new MailMessage)
            ->subject('Welcome to ' . config('app.name') . '! 💕 Your account is ready')
            ->markdown('emails.welcome', [
                'user'    => $notifiable,
                'appName' => config('app.name', 'HeartsConnect'),
                'appUrl'  => config('app.url'),
            ]);
    }
}
