<?php

namespace App\Http\Controllers;

use App\Models\Interest;
use App\Models\Profile;
use App\Models\User;
use App\Services\CompatibilityService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DiscoverController extends Controller
{
    public function index(Request $request): View
    {
        $user  = $request->user()->load(['profile', 'preferences', 'blocks', 'blockedBy']);
        $prefs = $user->preferences;

        $lat = $user->profile?->latitude  ?? 0;
        $lng = $user->profile?->longitude ?? 0;

        // Premium / trial config
        $isPremium           = $user->isPremiumActive();
        $locationUses        = (int) ($user->location_filter_uses ?? 0);
        $freeLocationTrials  = 2;
        $locationLimitReached = !$isPremium && $locationUses >= $freeLocationTrials;

        // IDs to exclude: self, blocked users, users who blocked me
        $excludeIds = collect([$user->id])
            ->merge($user->blocks->pluck('blocked_id'))
            ->merge($user->blockedBy->pluck('blocker_id'))
            ->unique()
            ->values();

        // Build base query
        $query = User::query()
            ->join('profiles', 'users.id', '=', 'profiles.user_id')
            ->where('users.is_banned', false)
            ->whereNotIn('users.id', $excludeIds)
            ->whereNotNull('users.email_verified_at')
            ->whereNotNull('users.username')
            ->where('users.profile_complete', true)
            ->where('profiles.is_paused', false);

        // Gender filter — request override takes priority over stored preference
        $seekingGender = $request->input('seeking_gender', $prefs?->seeking_gender);
        if ($seekingGender && $seekingGender !== 'everyone') {
            $query->where('users.gender', $seekingGender);
        } elseif (!$seekingGender && $user->seeking && $user->seeking !== 'everyone') {
            $query->where('users.gender', $user->seeking);
        }

        // Age filter
        $minAge = (int) $request->input('min_age', $prefs?->min_age ?? 18);
        $maxAge = (int) $request->input('max_age', $prefs?->max_age ?? 99);
        $query->whereRaw('TIMESTAMPDIFF(YEAR, users.date_of_birth, CURDATE()) BETWEEN ? AND ?', [$minAge, $maxAge]);

        // Online filter
        if ($request->boolean('online_only') || $prefs?->show_online_only) {
            $query->where('users.last_active_at', '>=', now()->subMinutes(15));
        }

        // New members filter
        if ($request->boolean('new_members')) {
            $query->where('users.created_at', '>=', now()->subDays(7));
        }

        // Verified-only filter — only available to verified users
        if ($request->boolean('verified_only') && $user->is_verified) {
            $query->where('users.is_verified', true);
        }

        // Body type filter — request param overrides stored preference
        if ($request->filled('body_type')) {
            $query->where('profiles.body_type', $request->input('body_type'));
        } elseif ($prefs && !empty($prefs->body_types)) {
            $query->whereIn('profiles.body_type', $prefs->body_types);
        }

        // Relationship goal filter
        if ($request->filled('relationship_goal')) {
            $query->where('profiles.relationship_goal', $request->input('relationship_goal'));
        }

        // Interest-based filter — narrows results to users who share selected interests
        $interestFilter = $request->input('interest_id');
        if ($interestFilter) {
            $query->whereExists(function ($sub) use ($interestFilter) {
                $sub->selectRaw(1)
                    ->from('profile_interest')
                    ->whereColumn('profile_interest.profile_id', 'profiles.id')
                    ->where('profile_interest.interest_id', (int) $interestFilter);
            });
        }

        // "Has location set" — when location filter is active, exclude profiles with no city
        $requireLocation = $request->boolean('has_location');
        if ($requireLocation) {
            $query->whereNotNull('profiles.city')->where('profiles.city', '!=', '');
        }

        // City / location filter — premium-only; free users get FREE_LOCATION_TRIALS total uses.
        $profileCity    = $user->profile?->city    ?? '';
        $profileCountry = $user->profile?->country ?? '';

        $requestedCity    = $request->has('city')    ? trim($request->input('city',    '')) : null;
        $requestedCountry = $request->has('country') ? trim($request->input('country', '')) : null;

        $isCustomCity    = $requestedCity    !== null && $requestedCity    !== $profileCity;
        $isCustomCountry = $requestedCountry !== null && $requestedCountry !== $profileCountry;
        $isCustomLocation = $isCustomCity || $isCustomCountry;

        if (!$isPremium && $isCustomLocation) {
            if ($locationLimitReached) {
                $requestedCity    = $profileCity;
                $requestedCountry = $profileCountry;
                session()->flash('location_limit_reached', true);
            } else {
                $user->increment('location_filter_uses');
                $locationUses++;
            }
        }

        $filterCity    = $requestedCity    ?? $profileCity;
        $filterCountry = $requestedCountry ?? $profileCountry;

        if ($filterCity !== '') {
            $query->where('profiles.city', $filterCity);
        }

        if ($filterCountry !== '') {
            $query->where('profiles.country', $filterCountry);
        }

        // ── Dealbreaker hard-filters (apply current user's dealbreakers) ──────
        $myDealbreakers = $user->profile?->dealbreakers ?? [];
        foreach ($myDealbreakers as $db) {
            switch ($db) {
                case 'no_smokers':
                    $query->where(fn($q) => $q->whereNull('profiles.smoking')->orWhere('profiles.smoking', 'never'));
                    break;
                case 'no_drinkers':
                    $query->where(fn($q) => $q->whereNull('profiles.drinking')->orWhere('profiles.drinking', 'never'));
                    break;
                case 'must_want_kids':
                    $query->where('profiles.wants_children', true);
                    break;
                case 'no_kids':
                    $query->where(fn($q) => $q->whereNull('profiles.has_children')->orWhere('profiles.has_children', false));
                    break;
            }
        }

        // Distance filter (Haversine)
        $requestKm = $request->filled('max_distance_km') ? (int) $request->input('max_distance_km') : null;
        $maxKm     = $requestKm ?? $prefs?->max_distance_km;

        if ($maxKm !== null && $lat && $lng) {
            $haversine = '( 6371 * acos( cos( radians(?) ) * cos( radians(profiles.latitude) )'
                . ' * cos( radians(profiles.longitude) - radians(?) )'
                . ' + sin( radians(?) ) * sin( radians(profiles.latitude) ) ) )';

            $query->selectRaw("users.*, {$haversine} AS distance_km", [$lat, $lng, $lat])
                  ->whereNotNull('profiles.latitude')
                  ->whereNotNull('profiles.longitude')
                  ->having('distance_km', '<=', $maxKm)
                  ->orderBy('distance_km', 'asc');
        } else {
            $query->selectRaw('users.*');
        }

        $users = $query->paginate(24)->withQueryString();

        // Eager-load profiles, photos, and interests for the paginated results
        $users->load(['profile.interests', 'primaryPhoto']);

        // Current user's interest IDs for shared-interest highlighting
        $myInterestIds = $user->profile?->interests()->pluck('interests.id')->toArray() ?? [];

        // All interests for the filter dropdown
        $allInterests = Interest::orderBy('name')->get();

        $compat = app(CompatibilityService::class);

        return view('discover.index', compact(
            'users', 'prefs', 'compat', 'minAge', 'maxAge', 'maxKm',
            'filterCity', 'filterCountry',
            'isPremium', 'locationUses', 'locationLimitReached',
            'allInterests', 'myInterestIds', 'interestFilter'
        ));
    }
}
