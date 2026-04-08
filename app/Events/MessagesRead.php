<?php

namespace App\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

/**
 * Broadcast when the recipient opens a conversation and stamps read_at.
 * Only premium senders listen to this to show the blue double-tick.
 */
class MessagesRead implements ShouldBroadcastNow
{
    use SerializesModels;

    public function __construct(
        public readonly int $conversationId,
        public readonly int $readerUserId,   // the user who just read
        public readonly string $readAt       // ISO timestamp
    ) {}

    public function broadcastOn(): array
    {
        return [new \Illuminate\Broadcasting\PrivateChannel("conversation.{$this->conversationId}")];
    }

    public function broadcastAs(): string
    {
        return 'messages.read';
    }

    public function broadcastWith(): array
    {
        return [
            'reader_user_id' => $this->readerUserId,
            'read_at'        => $this->readAt,
        ];
    }
}
