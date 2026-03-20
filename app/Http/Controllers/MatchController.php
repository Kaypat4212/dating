<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\UserMatch;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MatchController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $matches = UserMatch::where(function ($q) use ($user) {
            $q->where('user1_id', $user->id)->orWhere('user2_id', $user->id);
        })->where('is_active', true)
          ->with([
              'user1.primaryPhoto',
              'user2.primaryPhoto',
              'conversation.messages' => fn($q) => $q->latest('created_at')->limit(1),
          ])
          ->orderByDesc('matched_at')
          ->paginate(20);

        return view('matches.index', compact('matches'));
    }
}
