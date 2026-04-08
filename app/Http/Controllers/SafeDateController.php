<?php

namespace App\Http\Controllers;

use App\Models\SafeDateCheckin;
use Illuminate\Http\Request;

class SafeDateController extends Controller
{
    public function index(Request $request): \Illuminate\View\View
    {
        $user     = $request->user();
        $active   = SafeDateCheckin::where('user_id', $user->id)
            ->whereIn('status', ['active'])
            ->latest('date_at')
            ->first();
        $recent   = SafeDateCheckin::where('user_id', $user->id)
            ->whereNotIn('status', ['active'])
            ->latest('date_at')
            ->take(5)
            ->get();

        return view('safe-date.index', compact('active', 'recent'));
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validate([
            'date_location'            => 'required|string|max:255',
            'emergency_contact_name'   => 'required|string|max:100',
            'emergency_contact_phone'  => 'nullable|string|max:30',
            'emergency_contact_email'  => 'required|email|max:150',
            'date_at'                  => 'required|date|after:now',
            'checkin_minutes'          => 'required|integer|min:30|max:480',
        ]);

        $user = $request->user();

        // Cancel any existing active check-in first
        SafeDateCheckin::where('user_id', $user->id)
            ->where('status', 'active')
            ->update(['status' => 'cancelled']);

        SafeDateCheckin::create(array_merge($data, [
            'user_id' => $user->id,
            'status'  => 'active',
        ]));

        return redirect()->route('safe-date.index')
            ->with('success', 'Safe date check-in created! We\'ll alert your emergency contact if you don\'t check in.');
    }

    public function markSafe(SafeDateCheckin $checkin, Request $request): \Illuminate\Http\RedirectResponse
    {
        abort_if($checkin->user_id !== $request->user()->id, 403);
        abort_if($checkin->status !== 'active', 404);

        $checkin->update([
            'status'        => 'safe',
            'checked_in_at' => now(),
        ]);

        return redirect()->route('safe-date.index')
            ->with('success', '✅ You\'re marked safe! Your emergency contact will not be alerted.');
    }
}
