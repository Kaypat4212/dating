<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BlockController extends Controller
{
    public function store(Request $request, User $user): JsonResponse
    {
        $me = $request->user();

        if ($me->id === $user->id) {
            return response()->json(['error' => 'Cannot block yourself.'], 422);
        }

        Block::firstOrCreate([
            'blocker_id' => $me->id,
            'blocked_id' => $user->id,
        ]);

        return response()->json(['blocked' => true]);
    }

    public function destroy(Request $request, User $user): JsonResponse|RedirectResponse
    {
        $me = $request->user();
        Block::where('blocker_id', $me->id)->where('blocked_id', $user->id)->delete();

        if ($request->expectsJson()) {
            return response()->json(['unblocked' => true]);
        }

        return redirect()->route('account.blocked')->with('success', $user->name . ' has been unblocked.');
    }
}
