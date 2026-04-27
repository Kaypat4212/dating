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
    protected $fillable = [
        'match_id', 
        'disappear_after', 
        'is_pinned_user1', 
        'is_pinned_user2', 
        'hidden_for_user1', 
        'hidden_for_user2'
    ];

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

    /** Check if conversation is pinned for a specific user */
    public function isPinnedFor(int $userId): bool
    {
        $match = $this->match;
        return $match->user1_id === $userId 
            ? $this->is_pinned_user1 
            : $this->is_pinned_user2;
    }

    /** Check if conversation is hidden for a specific user */
    public function isHiddenFor(int $userId): bool
    {
        $match = $this->match;
        return $match->user1_id === $userId 
            ? $this->hidden_for_user1 
            : $this->hidden_for_user2;
    }

    /** Toggle pin status for a user */
    public function togglePinFor(int $userId): void
    {
        $match = $this->match;
        if ($match->user1_id === $userId) {
            $this->update(['is_pinned_user1' => !$this->is_pinned_user1]);
        } else {
            $this->update(['is_pinned_user2' => !$this->is_pinned_user2]);
        }
    }

    /** Hide conversation for a user */
    public function hideFor(int $userId): void
    {
        $match = $this->match;
        if ($match->user1_id === $userId) {
            $this->update(['hidden_for_user1' => true]);
        } else {
            $this->update(['hidden_for_user2' => true]);
        }
    }
}
