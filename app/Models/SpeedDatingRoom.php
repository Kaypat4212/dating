<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpeedDatingRoom extends Model
{
    protected $fillable = [
        'user1_id', 'user2_id', 'duration_minutes',
        'started_at', 'ended_at', 'status',
        'connect_user1', 'connect_user2',
    ];

    protected function casts(): array
    {
        return [
            'started_at'    => 'datetime',
            'ended_at'      => 'datetime',
            'connect_user1' => 'boolean',
            'connect_user2' => 'boolean',
        ];
    }

    public function user1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user1_id');
    }

    public function user2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user2_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SpeedDatingMessage::class, 'room_id')->orderBy('created_at');
    }

    public function getOtherUser(int $authId): User
    {
        return $this->user1_id === $authId ? $this->user2 : $this->user1;
    }

    public function isExpired(): bool
    {
        return $this->started_at !== null
            && now()->diffInMinutes($this->started_at) >= $this->duration_minutes;
    }

    public function secondsRemaining(): int
    {
        if ($this->started_at === null) return $this->duration_minutes * 60;
        $elapsed = now()->diffInSeconds($this->started_at, false);
        return max(0, ($this->duration_minutes * 60) - $elapsed);
    }

    public function bothWantToConnect(): bool
    {
        return $this->connect_user1 && $this->connect_user2;
    }
}
