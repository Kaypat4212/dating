<?php

namespace App\Http\Controllers;

use App\Services\BadgeService;
use Illuminate\Http\Request;

class StreakController extends Controller
{
    public function checkin(Request $request)
    {
        $user  = $request->user();
        $today = now()->toDateString();

        if (! $user->last_checkin_date) {
            $user->update(['login_streak' => 1, 'last_checkin_date' => $today]);
            BadgeService::checkStreakBadges($user->fresh());
            return response()->json(['streak' => 1, 'new_record' => true]);
        }

        $last = \Carbon\Carbon::parse($user->last_checkin_date);

        if ($last->toDateString() === $today) {
            return response()->json(['streak' => $user->login_streak, 'new_record' => false]);
        }

        $daysDiff = $last->diffInDays(now());

        if ($daysDiff === 1) {
            $user->increment('login_streak');
            $user->update(['last_checkin_date' => $today]);
        } elseif ($daysDiff === 2 && $user->streak_freeze_count > 0) {
            $user->decrement('streak_freeze_count');
            $user->increment('login_streak');
            $user->update(['last_checkin_date' => $today]);
        } else {
            $user->update(['login_streak' => 1, 'last_checkin_date' => $today]);
        }

        $fresh = $user->fresh();
        BadgeService::checkStreakBadges($fresh);

        return response()->json(['streak' => $fresh->login_streak, 'new_record' => false]);
    }
}

