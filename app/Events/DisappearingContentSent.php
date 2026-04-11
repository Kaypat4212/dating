<?php

namespace App\Events;

use App\Models\DisappearingContent;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DisappearingContentSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly DisappearingContent $content)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->content->recipient_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'disappearing.content.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'id'         => $this->content->id,
            'sender_id'  => $this->content->sender_id,
            'sender'     => $this->content->sender->name,
            'media_type' => $this->content->media_type,
            'created_at' => $this->content->created_at->toISOString(),
        ];
    }
}
