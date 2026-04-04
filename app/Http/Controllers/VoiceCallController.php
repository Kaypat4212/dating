<?php

namespace App\Http\Controllers;

use App\Events\CallStatusChangedEvent;
use App\Events\IncomingCallEvent;
use App\Models\Conversation;
use App\Models\VoiceCall;
use App\Services\AgoraTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VoiceCallController extends Controller
{
    public function __construct(private readonly AgoraTokenService $agora) {}

    /**
     * Initiate a call from the current user to the other participant in a conversation.
     */
    public function initiate(Request $request, Conversation $conversation): JsonResponse
    {
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

        // Notify the callee via Reverb
        broadcast(new IncomingCallEvent($call));

        return response()->json([
            'call_id'      => $call->id,
            'channel_name' => $channelName,
            'token'        => $callerToken,
            'app_id'       => config('services.agora.app_id'),
            'uid'          => $caller->id,
        ]);
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

        // Tell the caller their call was answered
        broadcast(new CallStatusChangedEvent($call, $call->caller_id));

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

        broadcast(new CallStatusChangedEvent($call, $call->caller_id));

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

        // Notify the other participant
        $otherUserId = $call->caller_id === $user->id ? $call->callee_id : $call->caller_id;
        broadcast(new CallStatusChangedEvent($call, $otherUserId));

        return response()->json(['status' => $newStatus]);
    }
}
