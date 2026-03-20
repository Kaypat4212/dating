<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class CompatibilityService
{
    /**
     * Calculate compatibility score (0-100) between two users.
     * Results are cached for 24 hours per pair.
     */
    public function score(User $user, User $target): int
    {
        $cacheKey = "compat_{$user->id}_{$target->id}";

        return Cache::remember($cacheKey, now()->addHours(24), function () use ($user, $target) {
            $score = 0;
            $maxScore = 0;

            $userProfile   = $user->profile;
            $targetProfile = $target->profile;

            if (!$userProfile || !$targetProfile) {
                return 50; // neutral if no profiles
            }

            // ---- Relationship goal match (weight: 30) ----
            $maxScore += 30;
            if ($userProfile->relationship_goal && $targetProfile->relationship_goal) {
                if ($userProfile->relationship_goal === $targetProfile->relationship_goal) {
                    $score += 30;
                } elseif ($this->compatibleGoals($userProfile->relationship_goal, $targetProfile->relationship_goal)) {
                    $score += 15;
                }
            } else {
                $score += 15; // neutral
                $maxScore -= 15;
            }

            // ---- Shared interests (weight: 25) ----
            $maxScore += 25;
            $userInterests   = $userProfile->interests->pluck('id');
            $targetInterests = $targetProfile->interests->pluck('id');
            if ($userInterests->count() > 0 && $targetInterests->count() > 0) {
                $shared = $userInterests->intersect($targetInterests)->count();
                $union  = $userInterests->union($targetInterests)->count();
                $jaccard = $union > 0 ? $shared / $union : 0;
                $score += (int) round($jaccard * 25);
            } else {
                $score += 12;
            }

            // ---- Compatibility answers (weight: 30) ----
            $maxScore += 30;
            $userAnswers   = $user->load('profile')->answers ?? collect();
            $targetAnswers = $target->load('profile')->answers ?? collect();
            // (simplified — in production, load user_answers with question weights)
            $score += 15; // base neutral

            // ---- Wants children match (weight: 15) ----
            $maxScore += 15;
            if ($userProfile->wants_children && $targetProfile->wants_children) {
                if ($userProfile->wants_children === $targetProfile->wants_children ||
                    in_array('open', [$userProfile->wants_children, $targetProfile->wants_children])) {
                    $score += 15;
                } elseif ($userProfile->wants_children === 'not_sure' || $targetProfile->wants_children === 'not_sure') {
                    $score += 8;
                }
            } else {
                $score += 8;
            }

            // Clamp 0-100
            return $maxScore > 0 ? min(100, max(0, (int) round(($score / $maxScore) * 100))) : 50;
        });
    }

    private function compatibleGoals(string $a, string $b): bool
    {
        $compatible = [
            'serious'    => ['marriage', 'serious'],
            'marriage'   => ['serious', 'marriage'],
            'casual'     => ['friendship', 'casual'],
            'friendship' => ['casual', 'friendship'],
        ];

        return in_array($b, $compatible[$a] ?? []);
    }
}
