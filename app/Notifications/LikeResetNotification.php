<?php

namespace App\Notifications;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LikeResetNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $tpl = EmailTemplate::findByKey('like_reset');
        if ($tpl) {
            ['subject' => $subject, 'html' => $html] = $tpl->render([
                '{user_name}'   => $notifiable->name,
                '{app_name}'    => config('app.name'),
                '{swipe_url}'   => route('swipe.deck'),
                '{premium_url}' => route('premium.show'),
            ]);
            return (new MailMessage)->subject($subject)->view('emails.dynamic', ['html' => $html, 'subject' => $subject]);
        }

        return (new MailMessage)
            ->subject('💖 Your daily likes have reset — go find your match!')
            ->greeting('Hey ' . $notifiable->name . '!')
            ->line('Your 15 free daily likes have just reset.')
            ->line('Head back and discover new profiles waiting for you.')
            ->action('Start Swiping Now', route('swipe.deck'))
            ->line('Upgrade to Premium anytime for unlimited likes and more!')
            ->salutation('With love, ' . config('app.name'));
    }
}
