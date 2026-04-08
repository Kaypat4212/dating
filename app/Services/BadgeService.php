<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\User;

/**
 * Central service for awarding badges to users.
 * Call BadgeService::award($user, 'badge_key') from any controller/listener.
 * Idempotent — silently skips if badge already awarded.
 */
class BadgeService
{
    /**
     * Award a badge to a user by key if the badge exists and the user doesn't already have it.
     * Returns true if newly awarded, false if already had it or badge doesn't exist.
     */
    public static function award(User $user, string $key): bool
    {
        $badge = Badge::where('key', $key)->where('is_active', true)->first();
        if (! $badge) {
            return false;
        }

        $already = $user->badges()->where('badge_id', $badge->id)->exists();
        if ($already) {
            return false;
        }

        $user->badges()->attach($badge->id, ['earned_at' => now()]);

        return true;
    }

    /**
     * Check streak and award appropriate streak badges.
     */
    public static function checkStreakBadges(User $user): void
    {
        $streak = $user->login_streak ?? 0;
        if ($streak >= 3)   static::award($user, 'streak_3');
        if ($streak >= 7)   static::award($user, 'streak_7');
        if ($streak >= 30)  static::award($user, 'streak_30');
        if ($streak >= 100) static::award($user, 'streak_100');
    }

    /**
     * Check likes-received and award popularity badges.
     * Pass the total likes_received count.
     */
    public static function checkLikeBadges(User $user, int $likesReceived): void
    {
        if ($likesReceived >= 10)  static::award($user, 'likes_10');
        if ($likesReceived >= 50)  static::award($user, 'likes_50');
        if ($likesReceived >= 100) static::award($user, 'likes_100');
    }

    /**
     * Check match count and award social badges.
     */
    public static function checkMatchBadges(User $user, int $matchCount): void
    {
        if ($matchCount >= 1)  static::award($user, 'first_match');
        if ($matchCount >= 5)  static::award($user, 'matches_5');
        if ($matchCount >= 25) static::award($user, 'matches_25');
    }
}
