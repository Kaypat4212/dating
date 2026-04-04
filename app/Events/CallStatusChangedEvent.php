<?php

namespace App\Events;

use App\Models\VoiceCall;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CallStatusChangedEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly VoiceCall $call,
        public readonly int       $notifyUserId  // which user to notify
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('user.' . $this->notifyUserId)];
    }

    public function broadcastAs(): string
    {
        return 'call-status-changed';
    }

    public function broadcastWith(): array
    {
        return [
            'call_id' => $this->call->id,
            'status'  => $this->call->status,
        ];
    }
}
