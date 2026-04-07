<?php

namespace App\Http\Controllers;

use App\Models\Story;
use App\Models\StoryView;
use App\Models\UserMatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoryController extends Controller
{
    /** Show stories from matches and the user themselves. */
    public function index(Request $request)
    {
        $user = $request->user();

        $matchedIds = UserMatch::where('user1_id', $user->id)->pluck('user2_id')
            ->merge(UserMatch::where('user2_id', $user->id)->pluck('user1_id'))
            ->push($user->id)
            ->unique();

        $stories = Story::with(['user.primaryPhoto', 'views.viewer.primaryPhoto'])
            ->whereIn('user_id', $matchedIds)
            ->active()
            ->orderBy('user_id')
            ->orderBy('created_at')
            ->get()
            ->groupBy('user_id');

        return view('stories.index', compact('stories', 'user'));
    }

    /** Record a view and return viewer list for a story (owner only). */
    public function viewers(Request $request, Story $story)
    {
        abort_unless($story->user_id === $request->user()->id, 403);

        $viewers = StoryView::with('viewer.primaryPhoto')
            ->where('story_id', $story->id)
            ->latest('viewed_at')
            ->get();

        return response()->json([
            'count'   => $viewers->count(),
            'viewers' => $viewers->map(fn ($v) => [
                'id'         => $v->viewer->id,
                'name'       => $v->viewer->name,
                'username'   => $v->viewer->username,
                'photo'      => optional($v->viewer->primaryPhoto)->thumbnail_url,
                'viewed_at'  => $v->viewed_at->diffForHumans(),
            ]),
        ]);
    }

    /** Mark a story as viewed by the current user. */
    public function markViewed(Request $request, Story $story)
    {
        $user = $request->user();

        if ($story->user_id !== $user->id && $story->isExpired() === false) {
            $alreadyViewed = StoryView::where('story_id', $story->id)
                ->where('user_id', $user->id)
                ->exists();

            if (!$alreadyViewed) {
                StoryView::create([
                    'story_id'  => $story->id,
                    'user_id'   => $user->id,
                    'viewed_at' => now(),
                ]);
                $story->increment('views_count');
            }
        }

        return response()->json(['ok' => true]);
    }

    /** Upload a new story (image or video). */
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
