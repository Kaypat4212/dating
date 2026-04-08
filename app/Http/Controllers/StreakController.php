<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StreakController extends Controller
{
    /**
     * Record a daily check-in.  Called automatically on each page load
     * via the streak middleware, or manually via POST /checkin.
     */
    public function checkin(Request $request)
    {
        $user  = $request->user();
        $today = now()->toDateString();

        if (! $user->last_checkin_date) {
            // First ever check-in
            $user->update(['login_streak' => 1, 'last_checkin_date' => $today]);
            return response()->json(['streak' => 1, 'new_record' => true]);
        }

        $last = \Carbon\Carbon::parse($user->last_checkin_date);

        // Already checked in today
        if ($last->toDateString() === $today) {
            return response()->json(['streak' => $user->login_streak, 'new_record' => false]);
        }

        $daysDiff = $last->diffInDays(now());

        if ($daysDiff === 1) {
            // Consecutive day — extend streak
            $user->increment('login_streak');
            $user->update(['last_checkin_date' => $today]);
        } elseif ($daysDiff === 2 && $user->streak_freeze_count > 0) {
            // Missed one day but has a streak freeze — use it
            $user->decrement('streak_freeze_count');
            $user->increment('login_streak');
            $user->update(['last_checkin_date' => $today]);
        } else {
            // Streak broken
            $user->update(['login_streak' => 1, 'last_checkin_date' => $today]);
        }

        return response()->json(['streak' => $user->fresh()->login_streak, 'new_record' => false]);
    }
}
