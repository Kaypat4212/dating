<?php

namespace App\Notifications;

use App\Models\EmailTemplate;
use App\Models\Message;
use App\Models\User;
use App\Services\MailSettingsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Message $message,
        public readonly User $sender
    ) {
    }

    public function via(object $notifiable): array
    {
        $channels = ['database', 'broadcast'];

        if (MailSettingsService::emailEnabled('email_feature_usage_enabled')
            && ($notifiable->preferences?->wantsEmail('email_new_message') ?? true)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $convUrl = route('conversations.show', $this->message->conversation_id);
        $appName = config('app.name', 'HeartsConnect');

        $tpl = EmailTemplate::findByKey('new_message');
        if ($tpl) {
            ['subject' => $subject, 'html' => $html] = $tpl->render([
                '{user_name}'        => $notifiable->name,
                '{sender_name}'      => $this->sender->name,
                '{message_preview}'  => '● ● ●',   // blurred — never expose content in email
                '{conversation_url}' => $convUrl,
                '{app_name}'         => $appName,
                '{app_url}'          => config('app.url'),
            ]);
            return (new MailMessage)->subject($subject)->view('emails.dynamic', ['html' => $html, 'subject' => $subject]);
        }

        return (new MailMessage)
            ->subject("{$this->sender->name} sent you a message on {$appName} 💬")
            ->markdown('emails.new-message', [
                'user'            => $notifiable,
                'sender'          => $this->sender,
                'blurred'         => true,           // tells template to blur content
                'conversationUrl' => $convUrl,
                'appName'         => $appName,
                'appUrl'          => config('app.url'),
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'            => 'new_message',
            'message_id'      => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'sender_id'       => $this->sender->id,
            'sender_name'     => $this->sender->name,
            'preview'         => substr($this->message->body, 0, 80),
            'url'             => route('conversations.show', $this->message->conversation_id),
        ];
    }
}
