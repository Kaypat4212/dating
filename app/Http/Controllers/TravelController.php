<?php

namespace App\Http\Controllers;

use App\Models\TravelInterest;
use App\Models\TravelPlan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TravelController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();

        $query = TravelPlan::where('is_visible', true)
            ->where('is_active', true)
            ->where('user_id', '!=', $user->id)
            ->where('travel_to', '>=', today())
            ->with(['user.profile', 'user.primaryPhoto', 'travelInterests']);

        // Destination search filter
        if ($request->filled('destination')) {
            $dest = $request->input('destination');
            $query->where(function ($q) use ($dest) {
                $q->where('destination', 'like', "%{$dest}%")
                  ->orWhere('destination_country', 'like', "%{$dest}%");
            });
        }

        // Travel type filter
        if ($request->filled('travel_type')) {
            $query->where('travel_type', $request->input('travel_type'));
        }

        // Month filter
        if ($request->filled('month')) {
            $query->whereMonth('travel_from', $request->integer('month'));
        }

        // Accommodation filter
        if ($request->filled('accommodation')) {
            $query->where('accommodation', $request->input('accommodation'));
        }

        $plans = $query->orderByDesc('created_at')->paginate(12)->withQueryString();

        $myPlans = TravelPlan::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->withCount('travelInterests')
            ->get();

        // IDs the current user already expressed interest in
        $myInterestPlanIds = TravelInterest::where('user_id', $user->id)
            ->pluck('plan_id')
            ->toArray();

        // Interests expressed on my plans (for the "received interests" section)
        $receivedInterests = TravelInterest::with(['user.profile', 'user.primaryPhoto', 'plan'])
            ->whereHas('plan', fn ($q) => $q->where('user_id', $user->id))
            ->where('status', 'pending')
            ->orderByDesc('expressed_at')
            ->get();

        return view('travel.index', compact(
            'plans', 'myPlans', 'myInterestPlanIds', 'receivedInterests'
        ));
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'destination'         => ['required', 'string', 'max:150'],
            'destination_country' => ['required', 'string', 'max:100'],
            'travel_from'         => ['required', 'date', 'after_or_equal:today'],
            'travel_to'           => ['required', 'date', 'after_or_equal:travel_from'],
            'travel_type'         => ['required', 'in:solo,with_friends,seeking_companion'],
            'accommodation'       => ['nullable', 'in:hotel,hostel,airbnb,camping,flexible'],
            'description'         => ['nullable', 'string', 'max:1000'],
        ]);

        TravelPlan::create([
            'user_id'             => auth()->id(),
            'destination'         => $request->destination,
            'destination_country' => $request->destination_country,
            'travel_from'         => $request->travel_from,
            'travel_to'           => $request->travel_to,
            'travel_type'         => $request->travel_type,
            'accommodation'       => $request->accommodation,
            'description'         => $request->description,
            'is_active'           => true,
            'is_visible'          => true,
        ]);

        return back()->with('success', 'Travel plan added! Others can now find and connect with you.');
    }

    public function destroy(TravelPlan $travelPlan): \Illuminate\Http\RedirectResponse
    {
        abort_unless($travelPlan->user_id === auth()->id(), 403);
        $travelPlan->delete();
        return back()->with('success', 'Plan removed.');
    }

    public function expressInterest(TravelPlan $travelPlan): \Illuminate\Http\RedirectResponse
    {
        abort_if($travelPlan->user_id === auth()->id(), 403);

        $already = TravelInterest::where('user_id', auth()->id())
            ->where('plan_id', $travelPlan->id)
            ->first();

        if ($already) {
            return back()->with('info', 'You already expressed interest in this plan.');
        }

        TravelInterest::create([
            'user_id'      => auth()->id(),
            'plan_id'      => $travelPlan->id,
            'expressed_at' => now(),
            'status'       => 'pending',
        ]);

        return back()->with('success', 'Interest expressed! The trip owner will be notified.');
    }

    public function respondInterest(TravelInterest $travelInterest, string $action): \Illuminate\Http\RedirectResponse
    {
        // Only the plan owner can respond
        abort_unless($travelInterest->plan->user_id === auth()->id(), 403);
        abort_unless(in_array($action, ['accepted', 'declined']), 400);

        $travelInterest->update(['status' => $action]);

        $msg = $action === 'accepted'
            ? 'Connection accepted! You can now message them.'
            : 'Interest declined.';

        return back()->with('success', $msg);
    }
}
