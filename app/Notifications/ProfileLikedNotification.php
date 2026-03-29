<?php

namespace App\Notifications;

use App\Models\EmailTemplate;
use App\Models\User;
use App\Services\MailSettingsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;



class ProfileLikedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly User $liker)
    {
    }

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if (MailSettingsService::emailEnabled('email_feature_usage_enabled')
            && ($notifiable->preferences?->wantsEmail('email_profile_liked') ?? true)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $tpl = EmailTemplate::findByKey('profile_liked');
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
            ->subject('Someone liked your profile on ' . config('app.name') . '! 😍')
            ->markdown('emails.profile-liked', [
                'user'    => $notifiable,
                'appName' => config('app.name', 'HeartsConnect'),
                'appUrl'  => config('app.url'),
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'           => 'profile_liked',
            'liker_id'       => $this->liker->id,
            'liker_name'     => $this->liker->name,
            'liker_username' => $this->liker->username,
            'message'        => "Someone liked your profile! Upgrade to premium to see who. 😍",
            'url'            => route('premium.show'),
        ];
    }
}
