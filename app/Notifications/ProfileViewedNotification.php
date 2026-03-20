<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ProfileViewedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly User $viewer)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'          => 'profile_viewed',
            'viewer_id'     => $this->viewer->id,
            'viewer_name'   => $this->viewer->name,
            'viewer_username' => $this->viewer->username,
            'message'       => "Someone viewed your profile. Upgrade to premium to see who visited you!",
            'url'           => route('profile.who-viewed'),
        ];
    }
}
