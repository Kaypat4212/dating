<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageReaction;
use Illuminate\Http\Request;

class MessageReactionController extends Controller
{
    private const ALLOWED_EMOJIS = ['❤️', '😂', '👍', '😮', '😢', '🔥'];

    /** Toggle a reaction on a message (add if missing, remove if same). */
    public function toggle(Request $request, Message $message)
    {
        $request->validate(['emoji' => 'required|string|max:10']);

        if (! in_array($request->emoji, self::ALLOWED_EMOJIS, true)) {
            return response()->json(['message' => 'Invalid emoji'], 422);
        }

        $user = $request->user();
        $existing = MessageReaction::where('message_id', $message->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            if ($existing->emoji === $request->emoji) {
                $existing->delete();
                $action = 'removed';
            } else {
                $existing->update(['emoji' => $request->emoji]);
                $action = 'changed';
            }
        } else {
            MessageReaction::create([
                'message_id' => $message->id,
                'user_id'    => $user->id,
                'emoji'      => $request->emoji,
            ]);
            $action = 'added';
        }

        $reactions = MessageReaction::where('message_id', $message->id)
            ->selectRaw('emoji, count(*) as count')
            ->groupBy('emoji')
            ->get();

        return response()->json(['action' => $action, 'reactions' => $reactions]);
    }
}
