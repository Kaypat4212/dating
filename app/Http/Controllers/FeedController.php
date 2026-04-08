<?php

namespace App\Http\Controllers;

use App\Models\FeedPost;
use App\Models\FeedPostComment;
use App\Models\FeedPostLike;
use App\Models\FeedCommentLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FeedController extends Controller
{
    // ── Feed index ────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $user = $request->user();

        $posts = FeedPost::active()
            ->with([
                'user.primaryPhoto',
                'originalPost.user.primaryPhoto',
                'comments' => fn ($q) => $q->with(['author.primaryPhoto', 'replies.author.primaryPhoto'])->limit(3),
            ])
            ->withCount(['likes', 'comments', 'reposts'])
            ->latest()
            ->paginate(12);

        // Stamp which posts the current user has liked / reposted
        $likedIds    = FeedPostLike::where('user_id', $user->id)
                            ->pluck('post_id')->flip();
        $repostedIds = FeedPost::where('user_id', $user->id)
                            ->whereNotNull('original_post_id')
                            ->pluck('original_post_id')->flip();

        return view('feed.index', compact('posts', 'likedIds', 'repostedIds'));
    }

    // ── Create post ───────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $data = $request->validate([
            'body'  => 'nullable|string|max:2000',
            'media' => 'nullable|file|mimes:jpeg,png,gif,webp,mp4|max:30720',
        ]);

        if (empty($data['body']) && ! $request->hasFile('media')) {
            return back()->withErrors(['body' => 'Post must have text or a photo/video.']);
        }

        $mediaPath = null;
        $mediaType = null;
        if ($request->hasFile('media')) {
            $file      = $request->file('media');
            $mediaType = str_starts_with($file->getMimeType(), 'video') ? 'video' : 'image';
            $mediaPath = $file->store('feed', 'public');
        }

        FeedPost::create([
            'user_id'    => $request->user()->id,
            'body'       => $data['body'] ?? null,
            'media_path' => $mediaPath,
            'media_type' => $mediaType,
        ]);

        return back()->with('success', 'Post shared!');
    }

    // ── Delete own post ───────────────────────────────────────────────────────

    public function destroy(Request $request, FeedPost $post)
    {
        abort_unless($post->user_id === $request->user()->id, 403);

        if ($post->media_path) {
            Storage::disk('public')->delete($post->media_path);
        }
        $post->delete();

        return back()->with('success', 'Post deleted.');
    }

    // ── Like / unlike ─────────────────────────────────────────────────────────

    public function like(Request $request, FeedPost $post)
    {
        $userId = $request->user()->id;

        $existing = FeedPostLike::where('post_id', $post->id)->where('user_id', $userId)->first();

        if ($existing) {
            $existing->delete();
            $post->decrement('likes_count');
            $liked = false;
        } else {
            FeedPostLike::create(['post_id' => $post->id, 'user_id' => $userId]);
            $post->increment('likes_count');
            $liked = true;
        }

        return response()->json(['liked' => $liked, 'count' => $post->fresh()->likes_count]);
    }

    // ── Repost ────────────────────────────────────────────────────────────────

    public function repost(Request $request, FeedPost $post)
    {
        $userId = $request->user()->id;

        // Can't repost own post
        if ($post->user_id === $userId) {
            return response()->json(['error' => 'Cannot repost your own post.'], 422);
        }

        // Can't repost a repost (only original posts)
        $originalId = $post->original_post_id ?? $post->id;

        $existing = FeedPost::where('user_id', $userId)->where('original_post_id', $originalId)->first();

        if ($existing) {
            $existing->delete();
            FeedPost::find($originalId)?->decrement('reposts_count');
            return response()->json(['reposted' => false, 'count' => FeedPost::find($originalId)?->reposts_count ?? 0]);
        }

        FeedPost::create([
            'user_id'          => $userId,
            'body'             => null,
            'original_post_id' => $originalId,
        ]);

        FeedPost::find($originalId)?->increment('reposts_count');

        return response()->json(['reposted' => true, 'count' => FeedPost::find($originalId)?->reposts_count ?? 0]);
    }

    // ── Add comment ───────────────────────────────────────────────────────────

    public function comment(Request $request, FeedPost $post)
    {
        $data = $request->validate([
            'body'      => 'required|string|max:1000',
            'parent_id' => 'nullable|integer|exists:feed_post_comments,id',
        ]);

        $comment = FeedPostComment::create([
            'post_id'   => $post->id,
            'user_id'   => $request->user()->id,
            'parent_id' => $data['parent_id'] ?? null,
            'body'      => $data['body'],
        ]);

        $post->increment('comments_count');

        $comment->load('author.primaryPhoto');

        return response()->json([
            'ok'      => true,
            'comment' => $this->formatComment($comment, $request->user()->id),
        ]);
    }

    // ── Delete comment ────────────────────────────────────────────────────────

    public function destroyComment(Request $request, FeedPostComment $comment)
    {
        abort_unless($comment->user_id === $request->user()->id, 403);

        $comment->post->decrement('comments_count', max(1, 1 + $comment->replies()->count()));
        $comment->delete(); // cascades to replies

        return response()->json(['ok' => true]);
    }

    // ── Like a comment ────────────────────────────────────────────────────────

    public function likeComment(Request $request, FeedPostComment $comment)
    {
        $userId   = $request->user()->id;
        $existing = FeedCommentLike::where('comment_id', $comment->id)->where('user_id', $userId)->first();

        if ($existing) {
            $existing->delete();
            $comment->decrement('likes_count');
            $liked = false;
        } else {
            FeedCommentLike::create(['comment_id' => $comment->id, 'user_id' => $userId]);
            $comment->increment('likes_count');
            $liked = true;
        }

        return response()->json(['liked' => $liked, 'count' => $comment->fresh()->likes_count]);
    }

    // ── Load more comments ────────────────────────────────────────────────────

    public function comments(Request $request, FeedPost $post)
    {
        $userId   = $request->user()->id;
        $comments = $post->comments()
            ->with(['author.primaryPhoto', 'replies.author.primaryPhoto'])
            ->paginate(10);

        return response()->json([
            'comments' => $comments->map(fn ($c) => $this->formatComment($c, $userId)),
            'next'     => $comments->nextPageUrl(),
        ]);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function formatComment(FeedPostComment $c, int $userId): array
    {
        return [
            'id'       => $c->id,
            'body'     => e($c->body),
            'liked'    => $c->isLikedBy($userId),
            'likes'    => $c->likes_count,
            'author'   => [
                'id'     => $c->author->id,
                'name'   => $c->author->name,
                'photo'  => optional($c->author->primaryPhoto)->thumbnail_url,
                'url'    => route('profile.show', $c->author->username),
            ],
            'time'     => $c->created_at->diffForHumans(),
            'replies'  => $c->replies->map(fn ($r) => $this->formatComment($r, $userId))->toArray(),
            'post_id'  => $c->post_id,
            'parent_id'=> $c->parent_id,
        ];
    }
}
