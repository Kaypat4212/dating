<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserMatch;
use App\Services\CompatibilityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class SwipeController extends Controller
{
    private const DECK_SIZE = 10;

    public function __construct(private readonly CompatibilityService $compat) {}

    public function deck(Request $request): View
    {
        $user = $request->user();
        $isSwipeRestricted = (bool) $user->swipes_restricted;
        [$profiles, $fallbackToGlobal] = $isSwipeRestricted
            ? [collect(), false]
            : $this->getProfiles($request);

        // Detect if free user has already exhausted their daily like limit
        $limitResetAt = null;
        if (!$isSwipeRestricted && !$user->isPremiumActive()) {
            $key = "likes:{$user->id}";
            if (RateLimiter::tooManyAttempts($key, 15)) {
                $limitResetAt = now()->addSeconds(RateLimiter::availableIn($key))->toISOString();
            }
        }

        // Notes remaining for free users (3 per day)
        $noteLimitPerDay      = 3;
        $notesRemainingToday  = $noteLimitPerDay;
        if (!$user->isPremiumActive()) {
            $noteKey             = "notes:{$user->id}";
            $notesUsedToday      = RateLimiter::attempts($noteKey);
            $notesRemainingToday = max(0, $noteLimitPerDay - $notesUsedToday);
        }
        $isPremium = $user->isPremiumActive();

        $passportEligible = $user->isPassportEligible();
        $passportActive   = $user->isPassportActive();
        $passportCountry  = $user->passport_country;

        return view('discover.swipe', compact(
            'profiles', 'isSwipeRestricted', 'fallbackToGlobal', 'limitResetAt',
            'passportEligible', 'passportActive', 'passportCountry',
            'isPremium', 'notesRemainingToday', 'noteLimitPerDay'
        ));
    }

    public function fetchDeck(Request $request): JsonResponse
    {
        if ($request->user()->swipes_restricted) {
            return response()->json(['profiles' => [], 'restricted' => true]);
        }

        [$profiles, $fallbackToGlobal] = $this->getProfiles($request);

        return response()->json(['profiles' => $profiles, 'fallback' => $fallbackToGlobal]);
    }

    /**
     * Returns [Collection $profiles, bool $fallbackToGlobal]
     *
     * Country rules:
     *  - Passport mode ON + country set → filter by passport_country
     *  - Passport mode ON + no country   → global browse
     *  - Passport mode OFF               → strict same-country filter, never global
     */
    private function getProfiles(Request $request): array
    {
        $user = $request->user()->load(['blocks', 'blockedBy', 'sentLikes', 'preferences', 'profile.interests']);

        $excludeIds = collect([$user->id])
            ->merge($user->blocks->pluck('blocked_id'))
            ->merge($user->blockedBy->pluck('blocker_id'))
            ->merge($user->sentLikes->pluck('receiver_id'))
            ->unique()->values();

        $prefs     = $user->preferences;
        $myProfile = $user->profile;
        $lat       = $myProfile?->latitude;
        $lng       = $myProfile?->longitude;
        $maxKm     = $prefs?->max_distance_km !== null ? (int) $prefs->max_distance_km : null;

        // ── Exclude admin accounts from dating pool ───────────────────────────
        $adminIds = User::role('admin')->pluck('id');
        if (!$adminIds->contains(1)) {
            $adminIds->push(1);
        }
        $excludeIds = $excludeIds->merge($adminIds)->unique()->values();

        // ── Determine target country ──────────────────────────────────────────
        // passportActive + country set → passport country
        // passportActive + no country  → no country restriction (global)
        // normal users                 → own country (strict, no fallback)
        $passportActive    = $user->isPassportActive();
        $passportCountry   = $user->passport_country;
        $myCountry         = $myProfile?->country;

        $targetCountry     = null; // null = no country restriction
        $strictCountry     = false;

        if ($passportActive) {
            if (!empty($passportCountry)) {
                $targetCountry = $passportCountry; // browse selected country
            }
            // else: global — $targetCountry stays null
        } else {
            // Non-passport: prefer same country; if country unknown, show global rather than nothing
            $targetCountry = $myCountry ?: null;
            $strictCountry = !empty($myCountry);
        }

        // ── Build base query ──────────────────────────────────────────────────
        $base = User::query()
            ->join('profiles', 'users.id', '=', 'profiles.user_id')
            ->where('users.is_banned', false)
            ->whereNotIn('users.id', $excludeIds)
            ->whereNotNull('users.email_verified_at')
            ->whereNotNull('users.username')
            ->where('users.profile_complete', true)
            ->where('profiles.is_paused', false);

        // ── Country filter ────────────────────────────────────────────────────
        if (!empty($targetCountry)) {
            $base->where('profiles.country', $targetCountry);
        } elseif ($strictCountry) {
            // Own country is unknown → return empty (never global for non-passport)
            return [collect(), false];
        }

        // ── Gender filter ─────────────────────────────────────────────────────
        if ($prefs?->seeking_gender && $prefs->seeking_gender !== 'everyone') {
            $base->where('users.gender', $prefs->seeking_gender);
        } elseif ($user->seeking && $user->seeking !== 'everyone') {
            $base->where('users.gender', $user->seeking);
        }

        // ── Age filter ────────────────────────────────────────────────────────
        $minAge = $prefs?->min_age ?? 18;
        $maxAge = $prefs?->max_age ?? 99;
        $base->whereRaw('TIMESTAMPDIFF(YEAR, users.date_of_birth, CURDATE()) BETWEEN ? AND ?', [$minAge, $maxAge]);

        // ── Verified-only filter (only available to verified users) ───────────
        if ($request->boolean('verified_only') && $user->is_verified) {
            $base->where('users.is_verified', true);
        }

        // ── Haversine expression ──────────────────────────────────────────────
        $haversine = null;
        if ($lat !== null && $lng !== null) {
            $haversine = '( 6371 * acos('
                . ' cos( radians(?) ) * cos( radians(profiles.latitude) )'
                . ' * cos( radians(profiles.longitude) - radians(?) )'
                . ' + sin( radians(?) ) * sin( radians(profiles.latitude) )'
                . ' ) )';
        }

        // ── Distance sub-filter within the country pool ───────────────────────
        if ($maxKm !== null && $haversine !== null) {
            $filtered = (clone $base)
                ->selectRaw("users.*, {$haversine} AS distance_km", [$lat, $lng, $lat])
                ->whereNotNull('profiles.latitude')
                ->whereNotNull('profiles.longitude')
                ->having('distance_km', '<=', $maxKm)
                ->orderByRaw('distance_km ASC')
                ->limit(self::DECK_SIZE)
                ->get();

            if ($filtered->isNotEmpty()) {
                return [$this->attachScores($user, $filtered), false];
            }
            // Distance sub-filter yielded nothing — fall back to full country pool
        }

        // ── State preference filter (non-passport only, soft preference) ──────
        // Try same-state first; fall back to full country pool if too few results.
        if (!$passportActive) {
            $preferredState = $prefs?->preferred_state ?? $myProfile?->state ?? null;
            if (!empty($preferredState)) {
                $stateQuery = (clone $base)->where('profiles.state', $preferredState)->limit(self::DECK_SIZE);
                if ($haversine !== null) {
                    $stateQuery->selectRaw(
                        "users.*, IF(profiles.latitude IS NOT NULL AND profiles.longitude IS NOT NULL, {$haversine}, NULL) AS distance_km",
                        [$lat, $lng, $lat]
                    );
                } else {
                    $stateQuery->select('users.*');
                }
                $stateProfiles = $stateQuery->inRandomOrder()->get();
                if ($stateProfiles->count() >= 3) {
                    return [$this->attachScores($user, $stateProfiles), false];
                }
                // Too few — fall through to full country pool
            }
        }

        // ── Country pool (random, with distance_km for display if GPS available) ─
        $query = (clone $base)->limit(self::DECK_SIZE);

        if ($haversine !== null) {
            $query->selectRaw(
                "users.*, IF(profiles.latitude IS NOT NULL AND profiles.longitude IS NOT NULL, {$haversine}, NULL) AS distance_km",
                [$lat, $lng, $lat]
            );
        } else {
            $query->select('users.*');
        }

        $query->inRandomOrder();
        $profiles = $query->get();

        return [$this->attachScores($user, $profiles), false];
    }

    private function attachScores(User $authUser, \Illuminate\Support\Collection $profiles): \Illuminate\Support\Collection
    {
        $profiles->load([
            'profile.interests',
            'primaryPhoto',
            'photos' => fn ($q) => $q->where('is_approved', true)->orderBy('is_primary', 'desc'),
        ]);

        return $profiles->each(fn ($p) => $p->compat_score = $this->compat->score($authUser, $p));
    }
}
