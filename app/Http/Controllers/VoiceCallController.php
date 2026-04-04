<?php

namespace App\Http\Controllers;

use App\Events\CallStatusChangedEvent;
use App\Events\IncomingCallEvent;
use App\Models\Conversation;
use App\Models\VoiceCall;
use App\Services\AgoraTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class VoiceCallController extends Controller
{
    public function __construct(private readonly AgoraTokenService $agora) {}

    /**
     * Initiate a call from the current user to the other participant in a conversation.
     */
    public function initiate(Request $request, Conversation $conversation): JsonResponse
    {
        try {
            $caller = $request->user();
            $match  = $conversation->match;

            // Authorise: caller must be in this conversation
            abort_unless(
                $match->user1_id === $caller->id || $match->user2_id === $caller->id,
                403
            );

            $callee = $match->getOtherUser($caller->id);

            // Cancel any pre-existing ringing call for this conversation
            VoiceCall::where('conversation_id', $conversation->id)
                ->where('status', 'ringing')
                ->update(['status' => 'missed', 'ended_at' => now()]);

            $channelName = 'call-' . $conversation->id . '-' . time();

            $call = VoiceCall::create([
                'conversation_id' => $conversation->id,
                'caller_id'       => $caller->id,
                'callee_id'       => $callee->id,
                'channel_name'    => $channelName,
                'status'          => 'ringing',
            ]);

            // Generate token for the caller
            $callerToken = $this->agora->generateRtcToken($channelName, $caller->id);

            // Notify the callee via Reverb (non-fatal — call works even if Reverb is down)
            try {
                broadcast(new IncomingCallEvent($call));
            } catch (\Throwable $broadcastErr) {
                \Illuminate\Support\Facades\Log::warning('IncomingCallEvent broadcast failed: ' . $broadcastErr->getMessage());
            }

            return response()->json([
                'call_id'      => $call->id,
                'channel_name' => $channelName,
                'token'        => $callerToken,
                'app_id'       => config('services.agora.app_id'),
                'uid'          => $caller->id,
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
     * Callee answers the call — returns their Agora token.
     */
    public function answer(Request $request, VoiceCall $call): JsonResponse
    {
        $user = $request->user();

        abort_unless($call->callee_id === $user->id, 403);
        abort_unless($call->status === 'ringing', 422, 'Call is no longer available.');

        $call->update(['status' => 'active', 'started_at' => now()]);

        $token = $this->agora->generateRtcToken($call->channel_name, $user->id);

        // Tell the caller their call was answered (non-fatal)
        try {
            broadcast(new CallStatusChangedEvent($call, $call->caller_id));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('CallStatusChangedEvent broadcast failed: ' . $e->getMessage());
        }

        return response()->json([
            'call_id'      => $call->id,
            'channel_name' => $call->channel_name,
            'token'        => $token,
            'app_id'       => config('services.agora.app_id'),
            'uid'          => $user->id,
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
