<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class DailyMatchController extends Controller
{
    /**
     * Return today's curated daily match for the authenticated user.
     * Uses a seeded random based on user-id + today's date for consistency.
     * Prioritises same country + state, then falls back to same country only.
     */
    public function show(Request $request)
    {
        $user    = $request->user();
        $profile = $user->profile;

        $userCountry = $profile->country ?? null;
        $userState   = $profile->state   ?? null;

        // Seed random with user id + date so it stays stable for 24h
        $seed = (int) ($user->id . now()->format('Ymd'));
        mt_srand($seed % PHP_INT_MAX);

        $baseQuery = fn () => User::where('id', '!=', $user->id)
            ->where('is_banned', false)
            ->where('profile_complete', true)
            ->whereNotNull('email_verified_at')
            ->whereNotNull('username')
            ->when($user->seeking !== 'everyone', function ($q) use ($user) {
                $q->where('gender', $user->seeking);
            })
            ->join('profiles', 'profiles.user_id', '=', 'users.id')
            ->with('profile', 'primaryPhoto')
            ->select('users.*');

        $match = null;

        // 1️⃣ Try: same country + same state (most local)
        if ($userCountry && $userState) {
            $match = $baseQuery()
                ->where('profiles.country', $userCountry)
                ->where('profiles.state',   $userState)
                ->orderByRaw('RAND(' . $seed . ')')
                ->first();
        }

        // 2️⃣ Fallback: same country only
        if (! $match && $userCountry) {
            $match = $baseQuery()
                ->where('profiles.country', $userCountry)
                ->orderByRaw('RAND(' . $seed . ')')
                ->first();
        }

        // 3️⃣ Last resort: any user (original behaviour, no location filter)
        if (! $match) {
            $match = $baseQuery()
                ->orderByRaw('RAND(' . $seed . ')')
                ->first();
        }

        return response()->json([
            'match' => $match ? [
                'id'       => $match->id,
                'name'     => $match->name,
                'age'      => $match->age,
                'city'     => optional($match->profile)->city,
                'country'  => optional($match->profile)->country,
                'state'    => optional($match->profile)->state,
                'headline' => optional($match->profile)->headline,
                'photo'    => optional($match->primaryPhoto)->thumbnail_url,
                'username' => $match->username,
            ] : null,
        ]);
    }
}
