<?php

namespace App\Notifications;

use App\Models\DisappearingContent;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DisappearingContentNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly DisappearingContent $content,
        private readonly User $sender
    ) {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type'       => 'disappearing_content',
            'sender_id'  => $this->sender->id,
            'sender'     => $this->sender->name,
            'content_id' => $this->content->id,
            'media_type' => $this->content->media_type,
            'message'    => "{$this->sender->name} sent you a snap!",
        ];
    }
}
