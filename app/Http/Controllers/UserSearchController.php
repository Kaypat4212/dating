<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserSearchController extends Controller
{
    /**
     * Show the search page.
     */
    public function index()
    {
        return view('users.search');
    }

    /**
     * Live AJAX search — returns a partial view or JSON depending on request type.
     */
    public function search(Request $request)
    {
        $q = trim($request->input('q', ''));

        if (strlen($q) < 2) {
            return response()->json(['users' => [], 'total' => 0]);
        }

        $me            = Auth::id();
        $blockedByMe   = \App\Models\Block::where('blocker_id', $me)->pluck('blocked_id');
        $whoBlockedMe  = \App\Models\Block::where('blocked_id', $me)->pluck('blocker_id');
        $excluded      = $blockedByMe->merge($whoBlockedMe)->push($me)->unique();

        $users = User::with(['primaryPhoto', 'profile'])
            ->whereNotIn('id', $excluded)
            ->where('is_banned', false)
            ->where(function ($query) use ($q) {
                $query->where('name',     'like', "%{$q}%")
                      ->orWhere('username', 'like', "%{$q}%")
                      ->orWhere('email',    'like', "%{$q}%");
            })
            ->orderByRaw('CASE WHEN username = ? THEN 0 WHEN name = ? THEN 1 ELSE 2 END', [$q, $q])
            ->limit(24)
            ->get();

        // Decorate each user with their like state relative to current auth user
        $likedIds = Like::where('sender_id', $me)
            ->whereIn('receiver_id', $users->pluck('id'))
            ->pluck('receiver_id')
            ->flip();

        $results = $users->map(function (User $user) use ($likedIds) {
            $photo = $user->primaryPhoto?->thumbnail_url
                  ?? $user->primaryPhoto?->url
                  ?? null;

            return [
                'id'          => $user->id,
                'name'        => $user->name,
                'username'    => $user->username,
                'photo'       => $photo,
                'age'         => $user->date_of_birth
                    ? (int) $user->date_of_birth->age
                    : null,
                'city'        => $user->profile?->city,
                'is_premium'  => (bool) $user->is_premium,
                'is_verified' => (bool) $user->is_verified,
                'profile_url' => route('profile.show', $user->username ?? $user->id),
                'like_url'    => route('like.store', $user->id),
                'liked'       => isset($likedIds[$user->id]),
            ];
        });

        return response()->json([
            'users' => $results,
            'total' => $results->count(),
        ]);
    }
}
