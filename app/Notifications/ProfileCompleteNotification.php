<?php

namespace App\Notifications;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProfileCompleteNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $tpl = EmailTemplate::findByKey('profile_complete');
        if ($tpl) {
            ['subject' => $subject, 'html' => $html] = $tpl->render([
                '{user_name}' => $notifiable->name,
                '{app_name}'  => config('app.name'),
                '{app_url}'   => config('app.url'),
                '{swipe_url}' => config('app.url') . '/swipe',
            ]);
            return (new MailMessage)->subject($subject)->view('emails.dynamic', ['html' => $html, 'subject' => $subject]);
        }

        return (new MailMessage)
            ->subject('🎉 Well Done! Your Profile is Complete!')
            ->markdown('emails.profile-complete', [
                'user'    => $notifiable,
                'appName' => config('app.name', 'HeartsConnect'),
                'appUrl'  => config('app.url'),
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'    => 'profile_complete',
            'title'   => '🎉 Profile Complete!',
            'message' => 'Congratulations! Your profile is now complete. Start swiping to find your perfect match!',
            'icon'    => '🎉',
            'url'     => '/swipe',
        ];
    }
}
