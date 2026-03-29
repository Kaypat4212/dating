<?php

namespace App\Notifications;

use App\Models\EmailTemplate;
use App\Models\User;
use App\Models\UserMatch;
use App\Services\MailSettingsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMatchNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly UserMatch $match,
        public readonly User $otherUser
    ) {
    }

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if (MailSettingsService::emailEnabled('email_feature_usage_enabled')
            && ($notifiable->preferences?->wantsEmail('email_new_match') ?? true)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $tpl = EmailTemplate::findByKey('new_match');
        if ($tpl) {
            ['subject' => $subject, 'html' => $html] = $tpl->render([
                '{user_name}'        => $notifiable->name,
                '{match_name}'       => $this->otherUser->name,
                '{conversation_url}' => route('conversations.show', $this->match->conversation_id ?? '#'),
                '{app_name}'         => config('app.name'),
                '{app_url}'          => config('app.url'),
            ]);
            return (new MailMessage)->subject($subject)->view('emails.dynamic', ['html' => $html, 'subject' => $subject]);
        }

        return (new MailMessage)
            ->subject("It's a Match! 💕 You and {$this->otherUser->name} liked each other")
            ->markdown('emails.new-match', [
                'user'            => $notifiable,
                'otherUser'       => $this->otherUser,
                'conversationUrl' => route('conversations.show', $this->match->conversation_id ?? '#'),
                'appName'         => config('app.name', 'HeartsConnect'),
                'appUrl'          => config('app.url'),
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'         => 'new_match',
            'match_id'     => $this->match->id,
            'other_user_id' => $this->otherUser->id,
            'other_user_name' => $this->otherUser->name,
            'message'      => "You matched with {$this->otherUser->name}! Start chatting now.",
            'url'          => route('conversations.show', $this->match->conversation_id ?? '#'),
        ];
    }
}
