<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wave;
use App\Notifications\WaveReceivedNotification;
use Illuminate\Http\Request;

class WaveController extends Controller
{
    /** Send or update a wave to another user. */
    public function store(Request $request, User $user)
    {
        $sender = $request->user();

        if ($sender->id === $user->id) {
            return response()->json(['message' => 'Cannot wave at yourself'], 422);
        }

        $wave = Wave::updateOrCreate(
            ['sender_id' => $sender->id, 'receiver_id' => $user->id],
            ['emoji' => $request->input('emoji', '👋'), 'seen' => false]
        );

        // Notify receiver (throttle: only on a new wave, not an update)
        if ($wave->wasRecentlyCreated || !$wave->seen) {
            $user->notify(new WaveReceivedNotification(
                sender: $sender,
                emoji: $wave->emoji,
            ));
        }

        return response()->json([
            'message' => 'Wave sent!',
            'wave'    => $wave,
        ]);
    }

    /** Mark incoming waves as seen. */
    public function markSeen(Request $request)
    {
        Wave::where('receiver_id', $request->user()->id)
            ->where('seen', false)
            ->update(['seen' => true, 'seen_at' => now()]);

        return response()->json(['message' => 'Waves marked as seen']);
    }

    /** List waves received by the authenticated user. */
    public function received(Request $request)
    {
        $waves = Wave::with('sender.primaryPhoto', 'sender.profile')
            ->where('receiver_id', $request->user()->id)
            ->latest()
            ->paginate(20);

        return view('waves.received', compact('waves'));
    }
}
