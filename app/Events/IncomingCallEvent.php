<?php

namespace App\Events;

use App\Models\VoiceCall;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class IncomingCallEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly VoiceCall $call) {}

    public function broadcastOn(): array
    {
        // Broadcast on the callee's private channel so only they receive it
        return [new PrivateChannel('user.' . $this->call->callee_id)];
    }

    public function broadcastAs(): string
    {
        return 'incoming-call';
    }

    public function broadcastWith(): array
    {
        $caller = $this->call->caller;
        return [
            'call_id'        => $this->call->id,
            'room_url'       => $this->call->room_url ?? ('https://meet.jit.si/' . $this->call->channel_name),
            'call_type'      => $this->call->call_type ?? 'voice',
            'caller_id'      => $caller->id,
            'caller_name'    => $caller->name,
            'caller_photo'   => $caller->primaryPhoto?->thumbnail_url,
            'conversation_id' => $this->call->conversation_id,
        ];
    }
}
