<?php

namespace App\Http\Controllers;

use App\Models\Story;
use App\Models\UserMatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoryController extends Controller
{
    /** Show stories from matches and the user themselves. */
    public function index(Request $request)
    {
        $user = $request->user();

        // Get matched user IDs
        $matchedIds = UserMatch::where('user1_id', $user->id)->pluck('user2_id')
            ->merge(UserMatch::where('user2_id', $user->id)->pluck('user1_id'))
            ->push($user->id)
            ->unique();

        $stories = Story::with('user.primaryPhoto')
            ->whereIn('user_id', $matchedIds)
            ->active()
            ->orderBy('user_id')
            ->orderBy('created_at')
            ->get()
            ->groupBy('user_id');

        return view('stories.index', compact('stories'));
    }

    /** Upload a new story (image only for now). */
    public function store(Request $request)
    {
        $request->validate([
            'media'   => 'required|file|mimes:jpeg,png,gif,webp,mp4|max:20480',
            'caption' => 'nullable|string|max:120',
        ]);

        $file      = $request->file('media');
        $mediaType = str_starts_with($file->getMimeType(), 'video') ? 'video' : 'image';
        $path      = $file->store('stories', 'public');

        Story::create([
            'user_id'    => $request->user()->id,
            'media_path' => $path,
            'media_type' => $mediaType,
            'caption'    => $request->input('caption'),
            'expires_at' => now()->addHours(24),
        ]);

        return back()->with('success', 'Story posted! It will disappear in 24 hours.');
    }

    /** Delete a story owned by the authenticated user. */
    public function destroy(Request $request, Story $story)
    {
        abort_unless($story->user_id === $request->user()->id, 403);
        Storage::disk('public')->delete($story->media_path);
        $story->delete();
        return back()->with('success', 'Story deleted.');
    }
}
