<?php

namespace App\Events;

use App\Models\UserMatch;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMatchEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public UserMatch $match;
    public int $notifyUserId;

    public function __construct(UserMatch $match, int $notifyUserId)
    {
        $this->match = $match;
        $this->notifyUserId = $notifyUserId;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->notifyUserId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'match.created';
    }

    public function broadcastWith(): array
    {
        $match = $this->match;
        $otherUserId = $match->user1_id === $this->notifyUserId ? $match->user2_id : $match->user1_id;
        $otherUser = \App\Models\User::find($otherUserId);

        return [
            'matchId' => $match->id,
            'otherUser' => [
                'id' => $otherUser->id,
                'name' => $otherUser->name,
                'photo' => $otherUser->primaryPhoto?->thumbnail_url,
            ],
            'timestamp' => $match->created_at->toIso8601String(),
        ];
    }
}
