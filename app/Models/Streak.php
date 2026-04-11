<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * Snapchat-style streak tracking between matched users
 * A streak is maintained by daily interactions (messages, snaps,calls)
 */
class Streak extends Model
{
    protected $fillable = [
        'user1_id',
        'user2_id',
        'count',
        'last_interaction_date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'last_interaction_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Record an interaction and update streak
     */
    public static function recordInteraction(int $userId1, int $userId2): void
    {
        // Ensure user1_id < user2_id for consistency
        [$user1, $user2] = $userId1 < $userId2 ? [$userId1, $userId2] : [$userId2, $userId1];

        $streak = self::firstOrCreate(
            ['user1_id' => $user1, 'user2_id' => $user2],
            ['count' => 0, 'is_active' => true]
        );

        $today = Carbon::today();
        $lastDate = $streak->last_interaction_date;

        if (!$lastDate) {
            // First interaction
            $streak->update([
                'count' => 1,
                'last_interaction_date' => $today,
                'is_active' => true,
            ]);
        } elseif ($lastDate->isToday()) {
            // Already interacted today, no change
            return;
        } elseif ($lastDate->isYesterday()) {
            // Consecutive day - increment streak
            $streak->increment('count');
            $streak->update(['last_interaction_date' => $today]);
        } else {
            // Streak broken - reset to 1
            $streak->update([
                'count' => 1,
                'last_interaction_date' => $today,
                'is_active' => true,
            ]);
        }
    }

    /**
     * Get active streak count between two users
     */
    public static function getStreakCount(int $userId1, int $userId2): int
    {
        [$user1, $user2] = $userId1 < $userId2 ? [$userId1, $userId2] : [$userId2, $userId1];

        $streak = self::where('user1_id', $user1)
            ->where('user2_id', $user2)
            ->first();

        if (!$streak || !$streak->is_active) {
            return 0;
        }

        // Check if streak is still valid (last interaction was yesterday or today)
        if ($streak->last_interaction_date?->isBefore(Carbon::yesterday())) {
            $streak->update(['is_active' => false]);
            return 0;
        }

        return $streak->count;
    }

    public function user1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user1_id');
    }

    public function user2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user2_id');
    }
}
