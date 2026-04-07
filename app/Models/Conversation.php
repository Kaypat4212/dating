<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $match_id
 * @property string $disappear_after  off|1h|24h|7d
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Conversation extends Model
{
    protected $fillable = ['match_id', 'disappear_after'];

    public function match(): BelongsTo
    {
        return $this->belongsTo(UserMatch::class, 'match_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function latestMessage(): HasMany
    {
        return $this->hasMany(Message::class)->latest('created_at')->limit(1);
    }

    public function unreadCountFor(int $userId): int
    {
        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->whereNull('read_at')
            ->count();
    }

    /** Compute the expires_at timestamp for a new message based on the room setting. */
    public function expiresAtForNewMessage(): ?\Illuminate\Support\Carbon
    {
        return match ($this->disappear_after ?? 'off') {
            '1h'  => now()->addHour(),
            '24h' => now()->addDay(),
            '7d'  => now()->addWeek(),
            default => null,
        };
    }
}
