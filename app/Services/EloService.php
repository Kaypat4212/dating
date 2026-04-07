<?php

namespace App\Services;

use App\Models\User;

class EloService
{
    private const K         = 16;   // base K-factor for a like
    private const K_MATCH   = 10;   // bonus for mutual match
    private const PASS_DECAY = 3;   // penalty per pass
    private const FLOOR     = 100;  // minimum score
    private const DEFAULT   = 1000; // starting score

    /**
     * Called when $swiper right-swipes (likes) $target.
     * Target gains score proportional to how high-rated the swiper is.
     */
    public function onLike(User $swiper, User $target): void
    {
        $swiperElo = $swiper->elo_score ?? self::DEFAULT;
        $targetElo = $target->elo_score ?? self::DEFAULT;

        // Expected: probability target is "above" swiper in the pool
        $expected = 1 / (1 + pow(10, ($targetElo - $swiperElo) / 400));
        $gain     = (int) round(self::K * (1 - $expected));

        $target->increment('elo_score', max(1, $gain));
    }

    /**
     * Called when $swiper passes (left-swipes) on $target.
     * Small Elo penalty for target.
     */
    public function onPass(User $target): void
    {
        $current = $target->elo_score ?? self::DEFAULT;
        $target->update(['elo_score' => max(self::FLOOR, $current - self::PASS_DECAY)]);
    }

    /**
     * Called when a mutual match is created — both users get a boost.
     */
    public function onMatch(User $user1, User $user2): void
    {
        $user1->increment('elo_score', self::K_MATCH);
        $user2->increment('elo_score', self::K_MATCH);
    }
}
