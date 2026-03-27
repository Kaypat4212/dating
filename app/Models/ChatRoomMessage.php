<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatRoomMessage extends Model
{
    protected $fillable = [
        'room_id', 'user_id', 'reply_to_id', 'content',
        'type', 'attachment_url', 'is_deleted', 'is_flagged',
    ];

    protected function casts(): array
    {
        return [
            'is_deleted' => 'boolean',
            'is_flagged' => 'boolean',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(ChatRoom::class, 'room_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(ChatRoomMessage::class, 'reply_to_id');
    }
}
