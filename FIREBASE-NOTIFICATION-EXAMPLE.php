<?php

/**
 * EXAMPLE: How to add Firebase Push Notifications to NewMatchNotification
 * 
 * This is an example showing how to modify NewMatchNotification.php
 * to include Firebase Cloud Messaging push notifications.
 */

namespace App\Notifications;

use App\Models\EmailTemplate;
use App\Models\User;
use App\Models\UserMatch;
use App\Notifications\Concerns\SendsFirebasePushNotification;  // ADD THIS
use App\Services\MailSettingsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMatchNotificationExample extends Notification implements ShouldQueue
{
    use Queueable;
    use SendsFirebasePushNotification;  // ADD THIS TRAIT

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

    // ADD THIS METHOD to send push notification after the notification is created
    public function afterCommit(): void
    {
        // This runs after the database transaction commits
        // Perfect for sending push notifications
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
            
            // SEND PUSH NOTIFICATION HERE (after email is prepared)
            $this->sendFCM(
                $notifiable,
                "New Match! 💕",
                "You matched with {$this->otherUser->name}!",
                [
                    'type' => 'match',
                    'match_id' => $this->match->id,
                    'user_id' => $this->otherUser->id,
                    'url' => route('conversations.show', $this->match->conversation_id ?? '#'),
                ]
            );
            
            return (new MailMessage)->subject($subject)->view('emails.dynamic', ['html' => $html, 'subject' => $subject]);
        }

        // FALLBACK: Send push even if no email template
        $this->sendFCM(
            $notifiable,
            "New Match! 💕", 
            "You matched with {$this->otherUser->name}!",
            ['type' => 'match', 'url' => '/matches']
        );

        return (new MailMessage)
            ->subject('New Match!')
            ->line("You matched with {$this->otherUser->name}!")
            ->action('View Match', route('conversations.show', $this->match->conversation_id ?? '#'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'match_id'       => $this->match->id,
            'other_user_id'  => $this->otherUser->id,
            'other_user_name' => $this->otherUser->name,
            'message'        => "You matched with {$this->otherUser->name}!",
        ];
    }
}

/**
 * ALTERNATIVE APPROACH: Send push notification separately
 * 
 * If you prefer more control, you can send FCM from the controller/service
 * that creates the notification:
 */

// Example from a controller or service:
/*
use App\Services\FirebaseCloudMessagingService;

// Send regular notification
$user->notify(new NewMatchNotification($match, $otherUser));

// Then send FCM separately if user has token
if ($user->fcm_token) {
    $fcm = app(FirebaseCloudMessagingService::class);
    $fcm->sendToDevice(
        $user->fcm_token,
        "New Match! 💕",
        "You matched with {$otherUser->name}!",
        [
            'type' => 'match',
            'match_id' => $match->id,
            'url' => route('conversations.show', $match->conversation_id)
        ]
    );
}
*/
