<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\UserMatch;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConversationController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $conversations = Conversation::whereHas('match', function ($q) use ($user) {
            $q->where('user1_id', $user->id)->orWhere('user2_id', $user->id);
        })->with([
            'match.user1.primaryPhoto',
            'match.user2.primaryPhoto',
            'messages' => fn($q) => $q->latest('created_at')->limit(1),
        ])->get()
          ->sortByDesc(fn($c) => $c->messages->first()?->created_at)
          ->values();

        return view('conversations.index', compact('conversations'));
    }

    public function show(Request $request, Conversation $conversation): View
    {
        $user  = $request->user();

        // Eager-load the match and both users' photos in one go
        $conversation->load('match.user1.primaryPhoto', 'match.user2.primaryPhoto');
        $match = $conversation->match;

        // Authorize: only matched users can see conversation
        abort_unless(
            $match->user1_id === $user->id || $match->user2_id === $user->id,
            403
        );

        // Mark all messages from the other user as read
        $conversation->messages()
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = $conversation->messages()
            ->with('sender.primaryPhoto', 'reactions')
            ->orderBy('created_at')
            ->get();

        $other = $match->getOtherUser($user->id);

        $giftPrices = \App\Models\SiteSetting::allAsArray();

        return view('conversations.show', compact('conversation', 'messages', 'other', 'match', 'giftPrices'));
    }
}
