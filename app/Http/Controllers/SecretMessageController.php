<?php

namespace App\Http\Controllers;

use App\Models\SecretMessage;
use App\Models\UserMatch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SecretMessageController extends Controller
{
    /** Send a secret message to a user (must not already be matched). */
    public function store(Request $request): JsonResponse
    {
        $sender   = $request->user();
        $request->validate([
            'receiver_id' => 'required|integer|exists:users,id',
            'body'        => 'required|string|min:5|max:500',
        ]);

        $receiverId = (int) $request->input('receiver_id');

        if ($receiverId === $sender->id) {
            return response()->json(['error' => 'Cannot send a secret message to yourself.'], 422);
        }

        // Don't allow if already matched (no need for secrets)
        $alreadyMatched = UserMatch::where('is_active', true)
            ->where(function ($q) use ($sender, $receiverId) {
                $q->where(fn($q) => $q->where('user1_id', $sender->id)->where('user2_id', $receiverId))
                  ->orWhere(fn($q) => $q->where('user1_id', $receiverId)->where('user2_id', $sender->id));
            })->exists();

        if ($alreadyMatched) {
            return response()->json(['error' => 'You\'re already matched! Just send them a message.'], 422);
        }

        // Upsert (replace existing unsent secret)
        SecretMessage::updateOrCreate(
            ['sender_id' => $sender->id, 'receiver_id' => $receiverId],
            ['body' => $request->input('body'), 'is_revealed' => false, 'revealed_at' => null]
        );

        return response()->json(['success' => true]);
    }

    /** Cancel a secret message before it's been revealed (sender only). */
    public function destroy(Request $request, SecretMessage $secretMessage): JsonResponse
    {
        abort_unless($secretMessage->sender_id === $request->user()->id, 403);
        abort_if($secretMessage->is_revealed, 422, 'Message already revealed.');
        $secretMessage->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Reveal all secret messages between two users (called internally on match creation).
     * Returns messages that were revealed so they can be injected into the conversation.
     */
    public static function revealBetween(int $userId1, int $userId2, int $conversationId): void
    {
        $secrets = SecretMessage::where('is_revealed', false)
            ->where(function ($q) use ($userId1, $userId2) {
                $q->where(fn($q) => $q->where('sender_id', $userId1)->where('receiver_id', $userId2))
                  ->orWhere(fn($q) => $q->where('sender_id', $userId2)->where('receiver_id', $userId1));
            })->get();

        foreach ($secrets as $secret) {
            // Create a real message in the conversation
            \App\Models\Message::create([
                'conversation_id' => $conversationId,
                'sender_id'       => $secret->sender_id,
                'body'            => $secret->body,
                'type'            => 'secret',
            ]);

            $secret->update(['is_revealed' => true, 'revealed_at' => now()]);
        }
    }
}
