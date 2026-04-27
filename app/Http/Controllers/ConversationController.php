<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\UserMatch;
use App\Models\VoiceCall;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConversationController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $conversations = Conversation::whereHas('match', function ($q) use ($user) {
            $q->where('user1_id', $user->id)->orWhere('user2_id', $user->id);
        })
        ->where(function ($query) use ($user) {
            // Filter out conversations hidden for this user
            $query->where(function ($q) use ($user) {
                $q->whereHas('match', fn($m) => $m->where('user1_id', $user->id))
                  ->where('hidden_for_user1', false);
            })->orWhere(function ($q) use ($user) {
                $q->whereHas('match', fn($m) => $m->where('user2_id', $user->id))
                  ->where('hidden_for_user2', false);
            });
        })
        ->with([
            'match.user1.primaryPhoto',
            'match.user2.primaryPhoto',
            'messages' => fn($q) => $q->latest('created_at')->limit(1),
        ])->get()
          ->sortByDesc(function ($c) use ($user) {
              // Pinned conversations at top, then by latest message
              $isPinned = $c->isPinnedFor($user->id);
              $latestTime = $c->messages->first()?->created_at?->timestamp ?? 0;
              return ($isPinned ? 9999999999 : 0) + $latestTime;
          })
          ->values();

        return view('conversations.index', compact('conversations'));
    }

    public function show(Request $request, Conversation $conversation): View
    {
        $user  = $request->user();

        // Eager-load the match and both users' photos in one go
        $conversation->load('match.user1.primaryPhoto', 'match.user2.primaryPhoto', 'match.travelPlan');
        $match = $conversation->match;

        // Authorize: only matched users can see conversation
        abort_unless(
            $match->user1_id === $user->id || $match->user2_id === $user->id,
            403,
            'You do not have access to this conversation.'
        );

        // Mark all messages from the other user as read and broadcast "seen" event
        $unreadCount = $conversation->messages()
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->count();

        if ($unreadCount > 0) {
            $conversation->messages()
                ->where('sender_id', '!=', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            // Broadcast real-time "seen" to the sender — only if user allows read receipts
            if ($user->read_receipts_enabled !== false) {
                try {
                    broadcast(new \App\Events\MessagesRead(
                        $conversation->id,
                        $user->id,
                        now()->toIso8601String()
                    ));
                } catch (\Throwable) {}
            }
        }

        $messages = $conversation->messages()
            ->notExpired()
            ->with('sender.primaryPhoto', 'reactions')
            ->orderBy('created_at')
            ->get();

        // Load voice calls for this conversation to show inline call events
        $voiceCalls = VoiceCall::where('conversation_id', $conversation->id)
            ->with(['caller', 'callee'])
            ->orderBy('created_at')
            ->get()
            ->keyBy('id');

        $other = $match->getOtherUser($user->id);

        $giftPrices = \App\Models\SiteSetting::allAsArray();
        $disappearAfter = $conversation->disappear_after ?? 'off';

        return view('conversations.show', compact('conversation', 'messages', 'other', 'match', 'giftPrices', 'voiceCalls', 'disappearAfter'));
    }

    public function setDisappearTimer(Request $request, Conversation $conversation): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        $match = $conversation->match;

        abort_unless(
            $match && ($match->user1_id === $user->id || $match->user2_id === $user->id),
            403
        );

        $mode = $request->input('mode', 'off');
        abort_unless(in_array($mode, ['off', '1h', '24h', '7d']), 422);

        $conversation->update(['disappear_after' => $mode]);

        return response()->json(['disappear_after' => $mode]);
    }

    /** Pin or unpin a conversation */
    public function togglePin(Request $request, Conversation $conversation): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        $match = $conversation->match;

        abort_unless(
            $match && ($match->user1_id === $user->id || $match->user2_id === $user->id),
            403
        );

        $conversation->togglePinFor($user->id);

        return response()->json([
            'pinned' => $conversation->fresh()->isPinnedFor($user->id),
            'message' => 'Conversation ' . ($conversation->isPinnedFor($user->id) ? 'pinned' : 'unpinned')
        ]);
    }

    /** Clear all messages in a conversation */
    public function clearMessages(Request $request, Conversation $conversation): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        $match = $conversation->match;

        abort_unless(
            $match && ($match->user1_id === $user->id || $match->user2_id === $user->id),
            403
        );

        // Delete all messages in this conversation
        $conversation->messages()->delete();

        return response()->json([
            'message' => 'All messages cleared successfully'
        ]);
    }

    /** Hide conversation from the list */
    public function hide(Request $request, Conversation $conversation): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        $match = $conversation->match;

        abort_unless(
            $match && ($match->user1_id === $user->id || $match->user2_id === $user->id),
            403
        );

        $conversation->hideFor($user->id);

        return response()->json([
            'message' => 'Conversation hidden successfully'
        ]);
    }
}
