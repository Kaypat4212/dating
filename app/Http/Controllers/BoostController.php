<?php

namespace App\Http\Controllers;

use App\Models\Boost;
use Illuminate\Http\Request;

class BoostController extends Controller
{
    /** Activate a 30-minute boost for the authenticated user (premium only). */
    public function store(Request $request)
    {
        $user = $request->user();

        if (! $user->isPremiumActive()) {
            return back()->with('error', 'Boost is a premium feature. Upgrade to use it.');
        }

        // Check if already boosted
        if ($user->activeBoost()) {
            return back()->with('error', 'You already have an active boost running.');
        }

        Boost::create([
            'user_id'    => $user->id,
            'started_at' => now(),
            'ends_at'    => now()->addMinutes(30),
            'active'     => true,
        ]);

        return back()->with('success', 'Your profile has been boosted for 30 minutes!');
    }

    /** Deactivate any running boost. */
    public function destroy(Request $request)
    {
        Boost::where('user_id', $request->user()->id)
            ->where('active', true)
            ->update(['active' => false]);

        return back()->with('success', 'Boost cancelled.');
    }
}
