<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CompatibilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DailyMatchController extends Controller
{
    public function __construct(private readonly CompatibilityService $compat) {}

    /**
     * Return today's curated daily spark for the authenticated user.
     * Picks the highest-compatibility unseen candidate for today; uses a 24-h
     * cache so the result is stable across page reloads.
     */
    public function show(Request $request)
    {
        $user    = $request->user()->load(['profile.interests', 'preferences']);
        $profile = $user->profile;

        $cacheKey = "daily_spark_{$user->id}_" . now()->toDateString();

        $matchData = Cache::remember($cacheKey, now()->endOfDay(), function () use ($user, $profile) {
            $userCountry = $profile?->country ?? null;
            $userState   = $profile?->state   ?? null;

            $seed = (int) ($user->id . now()->format('Ymd'));

            $baseQuery = fn () => User::where('users.id', '!=', $user->id)
                ->where('is_banned', false)
                ->where('profile_complete', true)
                ->whereNotNull('email_verified_at')
                ->whereNotNull('username')
                ->when($user->seeking && $user->seeking !== 'everyone', fn ($q) => $q->where('gender', $user->seeking))
                ->join('profiles', 'profiles.user_id', '=', 'users.id')
                ->where('profiles.is_paused', false)
                ->with(['profile.interests', 'primaryPhoto'])
                ->select('users.*');

            // Candidate pool: prefer same country+state, fall back to country, then global
            $candidates = null;
            if ($userCountry && $userState) {
                $candidates = $baseQuery()->where('profiles.country', $userCountry)->where('profiles.state', $userState)->inRandomOrder()->limit(30)->get();
            }
            if ((!$candidates || $candidates->isEmpty()) && $userCountry) {
                $candidates = $baseQuery()->where('profiles.country', $userCountry)->inRandomOrder()->limit(30)->get();
            }
            if (!$candidates || $candidates->isEmpty()) {
                $candidates = $baseQuery()->orderByRaw('RAND(' . $seed . ')')->limit(30)->get();
            }

            if ($candidates->isEmpty()) {
                return null;
            }

            // Pick the highest-compatibility candidate
            $best      = null;
            $bestScore = -1;
            foreach ($candidates as $candidate) {
                $score = $this->compat->score($user, $candidate);
                if ($score > $bestScore) {
                    $bestScore = $score;
                    $best      = $candidate;
                }
            }

            if (!$best) return null;

            return [
                'id'            => $best->id,
                'name'          => $best->name,
                'age'           => $best->age,
                'city'          => optional($best->profile)->city,
                'country'       => optional($best->profile)->country,
                'state'         => optional($best->profile)->state,
                'headline'      => optional($best->profile)->headline,
                'photo'         => optional($best->primaryPhoto)->thumbnail_url,
                'username'      => $best->username,
                'compat_score'  => $bestScore,
                'is_verified'   => (bool) $best->is_verified,
            ];
        });

        return response()->json(['match' => $matchData]);
    }
}
