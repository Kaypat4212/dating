<?php

namespace App\Http\Controllers;

use App\Events\CallStatusChangedEvent;
use App\Events\IncomingCallEvent;
use App\Models\Conversation;
use App\Models\SiteSetting;
use App\Models\VoiceCall;
use App\Services\DailyCoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class VoiceCallController extends Controller
{
    public function __construct(private readonly DailyCoService $daily) {}

    /**
     * Initiate a call from the current user to the other participant in a conversation.
     */
    public function initiate(Request $request, Conversation $conversation): JsonResponse
    {
        // ── Feature gate ──────────────────────────────────────────────────
        if (! filter_var(SiteSetting::get('voice_calls_enabled', '1'), FILTER_VALIDATE_BOOLEAN)) {
            return response()->json(['error' => 'Voice calls are currently disabled by the administrator.'], 503);
        }

        try {
            $caller = $request->user();

            // ── Daily call limit ──────────────────────────────────────────
            $dailyLimit = (int) SiteSetting::get('voice_call_daily_limit', 0);
            if ($dailyLimit > 0) {
                $usedToday = VoiceCall::where('caller_id', $caller->id)
                    ->whereDate('created_at', today())
                    ->count();
                if ($usedToday >= $dailyLimit) {
                    return response()->json([
                        'error' => "You've reached your daily call limit ({$dailyLimit} calls). Try again tomorrow.",
                    ], 429);
                }
            }
            $match  = $conversation->match;

            // Authorise: caller must be in this conversation
            abort_unless(
                $match->user1_id === $caller->id || $match->user2_id === $caller->id,
                403
            );

            $callee   = $match->getOtherUser($caller->id);
            $callType = in_array($request->input('call_type'), ['voice', 'video']) ? $request->input('call_type') : 'voice';

            // Cancel any pre-existing ringing call for this conversation
            VoiceCall::where('conversation_id', $conversation->id)
                ->where('status', 'ringing')
                ->update(['status' => 'missed', 'ended_at' => now()]);

            // Daily.co room names: max 23 chars, lowercase alphanumeric + hyphens
            // Use first 8 chars of md5(convId+timestamp) to stay well within limit
            $roomName = 'hc-' . substr(md5($conversation->id . time()), 0, 19); // 3+19 = 22 chars
            $tokenExpire = (int) SiteSetting::get('voice_call_token_expire', 3600);

            // Create Daily.co room (falls back to Jitsi Meet if no API key)
            $room        = $this->daily->createRoom($roomName, $tokenExpire);
            $callerToken = $this->daily->createToken($roomName, $caller->id, true, $tokenExpire);

            $callData = [
                'conversation_id' => $conversation->id,
                'caller_id'       => $caller->id,
                'callee_id'       => $callee->id,
                'channel_name'    => $roomName,
                'status'          => 'ringing',
                'call_type'       => $callType,
            ];
            if (\Illuminate\Support\Facades\Schema::hasColumn('voice_calls', 'room_url')) {
                $callData['room_url'] = $room['url'];
            }
            $call = VoiceCall::create($callData);

            // Notify the callee via Reverb (non-fatal — call works even if Reverb is down)
            try {
                broadcast(new IncomingCallEvent($call));
            } catch (\Throwable $broadcastErr) {
                \Illuminate\Support\Facades\Log::warning('IncomingCallEvent broadcast failed: ' . $broadcastErr->getMessage());
            }

            return response()->json([
                'call_id'   => $call->id,
                'room_url'  => $room['url'],
                'token'     => $callerToken,
                'call_type' => $callType,
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('VoiceCall initiate failed', [
                'conversation_id' => $conversation->id,
                'error'           => $e->getMessage(),
                'file'            => $e->getFile(),
                'line'            => $e->getLine(),
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Callee answers the call — returns their Daily.co room URL + token.
     */
    public function answer(Request $request, VoiceCall $call): JsonResponse
    {
        $user = $request->user();

        abort_unless($call->callee_id === $user->id, 403);
        abort_unless($call->status === 'ringing', 422, 'Call is no longer available.');

        $call->update(['status' => 'active', 'started_at' => now()]);

        $tokenExpire = (int) SiteSetting::get('voice_call_token_expire', 3600);
        $token       = $this->daily->createToken($call->channel_name, $user->id, false, $tokenExpire);
        
        // Get room URL from voice_calls table (set during initiate)
        $roomUrl = $call->room_url ?? throw new \Exception('Room URL not found for call #' . $call->id);

        // Tell the caller their call was answered (non-fatal)
        try {
            broadcast(new CallStatusChangedEvent($call, $call->caller_id));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('CallStatusChangedEvent broadcast failed: ' . $e->getMessage());
        }

        return response()->json([
            'call_id'   => $call->id,
            'room_url'  => $roomUrl,
            'token'     => $token,
            'call_type' => $call->call_type ?? 'voice',
        ]);
    }

    /**
     * Reject an incoming call.
     */
    public function reject(Request $request, VoiceCall $call): JsonResponse
    {
        $user = $request->user();

        abort_unless($call->callee_id === $user->id, 403);

        $call->update(['status' => 'rejected', 'ended_at' => now()]);

        try {
            broadcast(new CallStatusChangedEvent($call, $call->caller_id));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('CallStatusChangedEvent broadcast failed: ' . $e->getMessage());
        }

        return response()->json(['status' => 'rejected']);
    }

    /**
     * End an active or ringing call (either participant can end it).
     */
    public function end(Request $request, VoiceCall $call): JsonResponse
    {
        $user = $request->user();

        abort_unless(
            $call->caller_id === $user->id || $call->callee_id === $user->id,
            403
        );

        $newStatus = $call->status === 'ringing' ? 'missed' : 'ended';
        $call->update(['status' => $newStatus, 'ended_at' => now()]);

        // Clean up Daily.co room asynchronously (non-fatal)
        try { $this->daily->deleteRoom($call->channel_name); } catch (\Throwable) {}

        // Notify the other participant (non-fatal)
        $otherUserId = $call->caller_id === $user->id ? $call->callee_id : $call->caller_id;
        try {
            broadcast(new CallStatusChangedEvent($call, $otherUserId));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('CallStatusChangedEvent broadcast failed: ' . $e->getMessage());
        }

        return response()->json(['status' => $newStatus]);
    }

    /**
     * Call history page — all calls the authenticated user participated in.
     */
    public function history(Request $request): View
    {
        $user = $request->user();

        $calls = VoiceCall::where('caller_id', $user->id)
            ->orWhere('callee_id', $user->id)
            ->with(['caller.primaryPhoto', 'callee.primaryPhoto', 'conversation'])
            ->orderByDesc('created_at')
            ->paginate(30);

        // Mark all missed calls as "seen" so the badge resets (only if column exists)
        if (\Illuminate\Support\Facades\Schema::hasColumn('voice_calls', 'seen_at')) {
            VoiceCall::where('callee_id', $user->id)
                ->where('status', 'missed')
                ->whereNull('seen_at')
                ->update(['seen_at' => now()]);
        }

        return view('calls.history', compact('calls'));
    }

    /**
     * Return the count of unseen missed calls — used by the nav badge.
     * Returns 0 gracefully if the voice_calls table or seen_at column doesn't exist yet.
     */
    public function missedCount(Request $request): JsonResponse
    {
        try {
            // Check the table and column exist before querying
            if (! \Illuminate\Support\Facades\Schema::hasTable('voice_calls')) {
                return response()->json(['count' => 0]);
            }

            $query = VoiceCall::where('callee_id', $request->user()->id)
                ->where('status', 'missed');

            // Only filter by seen_at if the column exists (migration may not be run yet)
            if (\Illuminate\Support\Facades\Schema::hasColumn('voice_calls', 'seen_at')) {
                $query->whereNull('seen_at');
            }

            return response()->json(['count' => $query->count()]);
        } catch (\Throwable $e) {
            return response()->json(['count' => 0]);
        }
    }
}
