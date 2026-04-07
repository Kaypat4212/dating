<?php

namespace App\Http\Controllers;

use App\Models\SpeedDatingMessage;
use App\Models\SpeedDatingRoom;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SpeedDatingController extends Controller
{
    /** Landing page — shows queue status or active room. */
    public function index(Request $request)
    {
        $user = $request->user();

        // Check for an active room
        $room = SpeedDatingRoom::where('status', 'active')
            ->where(function ($q) use ($user) {
                $q->where('user1_id', $user->id)->orWhere('user2_id', $user->id);
            })
            ->with(['user1.primaryPhoto', 'user2.primaryPhoto'])
            ->first();

        // Check if in queue
        $inQueue = DB::table('speed_dating_queue')
            ->where('user_id', $user->id)
            ->where('status', 'waiting')
            ->exists();

        // Recent ended rooms for "connect?" prompts
        $recentEnded = SpeedDatingRoom::where('status', 'ended')
            ->where(function ($q) use ($user) {
                $q->where('user1_id', $user->id)->orWhere('user2_id', $user->id);
            })
            ->where('ended_at', '>=', now()->subMinutes(10))
            ->with(['user1.primaryPhoto', 'user2.primaryPhoto'])
            ->first();

        return view('speed-dating.index', compact('room', 'inQueue', 'recentEnded', 'user'));
    }

    /** Join the speed dating queue. */
    public function join(Request $request): JsonResponse
    {
        $user = $request->user();

        // Already in queue?
        $exists = DB::table('speed_dating_queue')
            ->where('user_id', $user->id)
            ->where('status', 'waiting')
            ->exists();

        if ($exists) {
            return response()->json(['queued' => true]);
        }

        DB::table('speed_dating_queue')->insert([
            'user_id'    => $user->id,
            'status'     => 'waiting',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['queued' => true]);
    }

    /** Leave the queue. */
    public function leave(Request $request): JsonResponse
    {
        $user = $request->user();

        DB::table('speed_dating_queue')
            ->where('user_id', $user->id)
            ->where('status', 'waiting')
            ->delete();

        return response()->json(['left' => true]);
    }

    /** Poll endpoint — returns current queue/room status. */
    public function status(Request $request): JsonResponse
    {
        $user = $request->user();

        // Check active room
        $room = SpeedDatingRoom::where('status', 'active')
            ->where(function ($q) use ($user) {
                $q->where('user1_id', $user->id)->orWhere('user2_id', $user->id);
            })
            ->with('user1.primaryPhoto', 'user2.primaryPhoto')
            ->first();

        if ($room) {
            $other = $room->getOtherUser($user->id);

            // Auto-end if time has run out
            if ($room->isExpired()) {
                $room->update(['status' => 'ended', 'ended_at' => now()]);
                return response()->json(['state' => 'ended', 'room_id' => $room->id]);
            }

            return response()->json([
                'state'            => 'active',
                'room_id'          => $room->id,
                'seconds_remaining'=> $room->secondsRemaining(),
                'other'            => [
                    'id'    => $other->id,
                    'name'  => $other->name,
                    'photo' => $other->primaryPhoto?->thumbnail_url,
                ],
            ]);
        }

        // Check queue
        $inQueue = DB::table('speed_dating_queue')
            ->where('user_id', $user->id)
            ->where('status', 'waiting')
            ->exists();

        return response()->json(['state' => $inQueue ? 'waiting' : 'idle']);
    }

    /** Show a specific room's chat (messages for polling). */
    public function messages(Request $request, SpeedDatingRoom $room): JsonResponse
    {
        $user = $request->user();
        abort_unless($room->user1_id === $user->id || $room->user2_id === $user->id, 403);

        $since = $request->integer('since', 0);

        $messages = SpeedDatingMessage::where('room_id', $room->id)
            ->where('id', '>', $since)
            ->with('sender.primaryPhoto')
            ->get()
            ->map(fn($m) => [
                'id'         => $m->id,
                'body'       => $m->body,
                'sender_id'  => $m->sender_id,
                'is_me'      => $m->sender_id === $user->id,
                'name'       => $m->sender->name,
                'created_at' => $m->created_at->format('g:i A'),
            ]);

        return response()->json(['messages' => $messages]);
    }

    /** Send a message in a speed dating room. */
    public function sendMessage(Request $request, SpeedDatingRoom $room): JsonResponse
    {
        $user = $request->user();
        abort_unless($room->user1_id === $user->id || $room->user2_id === $user->id, 403);
        abort_if($room->status !== 'active' || $room->isExpired(), 422, 'Room is no longer active.');

        $request->validate(['body' => 'required|string|max:500']);

        $msg = SpeedDatingMessage::create([
            'room_id'   => $room->id,
            'sender_id' => $user->id,
            'body'      => $request->input('body'),
        ]);

        return response()->json([
            'id'         => $msg->id,
            'body'       => $msg->body,
            'is_me'      => true,
            'name'       => $user->name,
            'created_at' => $msg->created_at->format('g:i A'),
        ]);
    }

    /** Mark "I want to connect" after a session ends. */
    public function connect(Request $request, SpeedDatingRoom $room): JsonResponse
    {
        $user = $request->user();
        abort_unless($room->user1_id === $user->id || $room->user2_id === $user->id, 403);

        $field = $room->user1_id === $user->id ? 'connect_user1' : 'connect_user2';
        $room->update([$field => true]);
        $room->refresh();

        $matched = false;
        if ($room->bothWantToConnect()) {
            // Create a real match/conversation if they don't already have one
            [$u1, $u2] = $room->user1_id < $room->user2_id
                ? [$room->user1_id, $room->user2_id]
                : [$room->user2_id, $room->user1_id];

            $match = \App\Models\UserMatch::firstOrCreate(
                ['user1_id' => $u1, 'user2_id' => $u2],
                ['matched_at' => now(), 'is_active' => true]
            );
            if ($match->wasRecentlyCreated) {
                $match->conversation()->create();
            }
            $match->loadMissing('conversation');
            $matched = true;

            return response()->json([
                'matched'          => true,
                'conversation_url' => route('conversations.show', $match->conversation->id),
            ]);
        }

        return response()->json(['matched' => false, 'waiting_for_other' => true]);
    }
}
