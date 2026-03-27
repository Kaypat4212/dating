<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatRoomMember extends Model
{
    protected $fillable = [
        'room_id', 'user_id', 'role',
        'is_muted', 'is_banned', 'last_read_at',
    ];

    protected function casts(): array
    {
        return [
            'is_muted'    => 'boolean',
            'is_banned'   => 'boolean',
            'last_read_at' => 'datetime',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(ChatRoom::class, 'room_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
