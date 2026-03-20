<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\UserMatch;
use App\Models\Like;
use App\Models\ProfileView;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user()->load(['profile', 'primaryPhoto']);

        // Match count
        $matchCount = UserMatch::where(function ($q) use ($user) {
            $q->where('user1_id', $user->id)->orWhere('user2_id', $user->id);
        })->where('is_active', true)->count();

        // New likes (last 30 days)
        $newLikes = Like::where('receiver_id', $user->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        // Profile views (last 7 days)
        $recentViews = ProfileView::where('viewed_id', $user->id)
            ->where('viewed_at', '>=', now()->subDays(7))
            ->count();

        // Unread messages (via matches → conversations → messages)
        $unreadMessages = \App\Models\Message::whereHas('conversation.match', function ($q) use ($user) {
                $q->where('user1_id', $user->id)->orWhere('user2_id', $user->id);
            })
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->count();

        $stats = [
            'matchCount'     => $matchCount,
            'newLikes'       => $newLikes,
            'recentViews'    => $recentViews,
            'unreadMessages' => $unreadMessages,
        ];

        // Recent matches — kept as UserMatch objects so view can call getOtherUser()
        $recentMatches = UserMatch::where(function ($q) use ($user) {
            $q->where('user1_id', $user->id)->orWhere('user2_id', $user->id);
        })->where('is_active', true)
          ->latest('matched_at')
          ->limit(6)
          ->with(['user1.primaryPhoto', 'user2.primaryPhoto', 'conversation'])
          ->get();

        return view('dashboard', compact('stats', 'recentMatches'));
    }
}
