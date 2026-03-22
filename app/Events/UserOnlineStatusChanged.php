<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserOnlineStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $userId;
    public bool $isOnline;
    public ?string $lastSeen;

    public function __construct(int $userId, bool $isOnline, ?string $lastSeen = null)
    {
        $this->userId = $userId;
        $this->isOnline = $isOnline;
        $this->lastSeen = $lastSeen ?? now()->toIso8601String();
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('user-status'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'user.status.changed';
    }

    public function broadcastWith(): array
    {
        return [
            'userId' => $this->userId,
            'isOnline' => $this->isOnline,
            'lastSeen' => $this->lastSeen,
        ];
    }
}
