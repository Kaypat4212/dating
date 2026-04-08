<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use Illuminate\Http\Request;

class BadgeController extends Controller
{
    /** Show all badges a user has earned (from profile page) */
    public function index(Request $request)
    {
        $user   = $request->user();
        $earned = $user->badges()->get()->keyBy('id');
        $all    = Badge::where('is_active', true)->orderBy('category')->orderBy('name')->get();

        return view('badges.index', compact('user', 'earned', 'all'));
    }

    /** Toggle pin status of a badge (shows on profile card) */
    public function togglePin(Request $request, Badge $badge)
    {
        $user = $request->user();

        $pivot = $user->badges()->where('badge_id', $badge->id)->first();
        if (! $pivot) {
            return back()->withErrors(['badge' => 'You have not earned this badge.']);
        }

        $currentPin = (bool) $pivot->pivot->is_pinned;
        $user->badges()->updateExistingPivot($badge->id, ['is_pinned' => ! $currentPin]);

        return back()->with('success', ! $currentPin ? 'Badge pinned to your profile.' : 'Badge unpinned.');
    }
}
