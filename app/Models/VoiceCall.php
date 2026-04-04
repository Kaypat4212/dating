<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VoiceCall extends Model
{
    protected $fillable = [
        'conversation_id',
        'caller_id',
        'callee_id',
        'channel_name',
        'status',
        'started_at',
        'ended_at',
        'seen_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at'   => 'datetime',
            'seen_at'    => 'datetime',
        ];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function caller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'caller_id');
    }

    public function callee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'callee_id');
    }

    public function durationSeconds(): ?int
    {
        if ($this->started_at && $this->ended_at) {
            return (int) $this->ended_at->diffInSeconds($this->started_at);
        }
        return null;
    }
}
