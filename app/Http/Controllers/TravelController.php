<?php

namespace App\Http\Controllers;

use App\Models\TravelInterest;
use App\Models\TravelPlan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TravelController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $plans = TravelPlan::where('is_visible', true)
            ->where('is_active', true)
            ->where('user_id', '!=', $user->id)
            ->with(['user.profile', 'user.primaryPhoto'])
            ->orderByDesc('created_at')
            ->paginate(12);

        $myPlans = TravelPlan::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        return view('travel.index', compact('plans', 'myPlans'));
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'destination'         => ['required', 'string', 'max:150'],
            'destination_country' => ['required', 'string', 'max:100'],
            'travel_from'         => ['required', 'date', 'after_or_equal:today'],
            'travel_to'           => ['required', 'date', 'after_or_equal:travel_from'],
            'travel_type'         => ['required', 'in:solo,couple,group,open'],
            'description'         => ['nullable', 'string', 'max:1000'],
        ]);

        TravelPlan::create([
            'user_id'             => auth()->id(),
            'destination'         => $request->destination,
            'destination_country' => $request->destination_country,
            'travel_from'         => $request->travel_from,
            'travel_to'           => $request->travel_to,
            'travel_type'         => $request->travel_type,
            'description'         => $request->description,
            'is_active'           => true,
            'is_visible'          => true,
        ]);

        return back()->with('success', 'Travel plan added!');
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

        TravelInterest::firstOrCreate([
            'user_id' => auth()->id(),
            'plan_id' => $travelPlan->id,
        ], [
            'expressed_at' => now(),
            'status'       => 'pending',
        ]);

        return back()->with('success', 'Interest expressed!');
    }
}
