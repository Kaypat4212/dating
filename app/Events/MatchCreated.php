<?php

namespace App\Events;

use App\Models\UserMatch;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MatchCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly UserMatch $match)
    {
    }

    /** Broadcast to both matched users. */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->match->user1_id),
            new PrivateChannel('user.' . $this->match->user2_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'match.created';
    }

    public function broadcastWith(): array
    {
        $match = $this->match->load(['user1.profile', 'user2.profile']);

        return [
            'match_id'  => $match->id,
            'user1_id'  => $match->user1_id,
            'user2_id'  => $match->user2_id,
            'user1_name' => $match->user1->name,
            'user2_name' => $match->user2->name,
        ];
    }
}
