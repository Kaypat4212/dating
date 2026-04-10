<?php

namespace App\Http\Controllers;

use App\Models\ForumCategory;
use App\Models\ForumReply;
use App\Models\ForumTopic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ForumController extends Controller
{
    /**
     * If the category has a country_code, ensure the authenticated user's profile
     * matches. Redirect with an error message if not.
     */
    private function enforceLocationForCategory(ForumCategory $category): ?\Illuminate\Http\RedirectResponse
    {
        if (empty($category->country_code)) {
            return null; // no restriction
        }

        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $profile = $user->profile;

        if (!$profile || strtoupper($profile->country ?? '') !== strtoupper($category->country_code)) {
            return redirect()->route('forum.category', $category->slug)
                ->with('error', 'This forum is for users located in ' . $category->name . '. Please update your location in your profile settings to participate.');
        }

        return null;
    }

    public function index(): View
    {
        $categories = ForumCategory::where('is_active', true)
            ->withCount('topics')
            ->with(['topics' => fn($q) => $q->orderByDesc('last_reply_at')->limit(1)->with('author')])
            ->orderBy('order')
            ->get();

        $recentTopics = ForumTopic::with(['author', 'category'])
            ->orderByDesc('last_reply_at')
            ->limit(10)
            ->get();

        return view('forum.index', compact('categories', 'recentTopics'));
    }

    public function category(ForumCategory $category): View
    {
        abort_unless($category->is_active, 404);

        // Show the category listing — location info shown as notice (not hard-blocked)
        $topics = $category->topics()
            ->with(['author', 'lastReplyUser'])
            ->orderByDesc('is_pinned')
            ->orderByDesc('last_reply_at')
            ->paginate(20);

        return view('forum.category', compact('category', 'topics'));
    }

    public function topic(ForumCategory $category, ForumTopic $topic): View
    {
        abort_unless($topic->category_id === $category->id, 404);

        $topic->increment('views_count');

        $replies = $topic->replies()
            ->with(['author', 'children.author'])
            ->whereNull('parent_id')
            ->orderBy('created_at')
            ->paginate(20);

        return view('forum.topic', compact('category', 'topic', 'replies'));
    }

    public function createTopic(ForumCategory $category)
    {
        abort_unless($category->is_active, 404);

        if ($redirect = $this->enforceLocationForCategory($category)) {
            return $redirect;
        }

        return view('forum.create-topic', compact('category'));
    }

    public function storeTopic(Request $request, ForumCategory $category)
    {
        abort_unless($category->is_active, 404);

        if ($redirect = $this->enforceLocationForCategory($category)) {
            return $redirect;
        }

        $request->validate([
            'title'   => ['required', 'string', 'min:5', 'max:200'],
            'content' => ['required', 'string', 'min:20', 'max:10000'],
            'tags'    => ['nullable', 'string', 'max:200'],
        ]);

        $slug = Str::slug($request->title) . '-' . time();
        $tags = $request->tags
            ? array_map('trim', explode(',', $request->tags))
            : [];

        $topic = ForumTopic::create([
            'category_id'    => $category->id,
            'user_id'        => Auth::id(),
            'title'          => $request->title,
            'slug'           => $slug,
            'content'        => $this->sanitizeHtml($request->input('content')),
            'tags'           => $tags,
            'last_reply_at'  => now(),
            'share_token'    => bin2hex(random_bytes(8)),
        ]);

        return redirect()->route('forum.topic', [$category->slug, $topic->slug])
            ->with('success', 'Topic created!');
    }

    public function storeReply(Request $request, ForumCategory $category, ForumTopic $topic)
    {
        abort_if($topic->is_locked, 403, 'This topic is locked.');

        $request->validate([
            'content'   => ['required', 'string', 'min:5', 'max:5000'],
            'parent_id' => ['nullable', 'exists:forum_replies,id'],
        ]);

        ForumReply::create([
            'topic_id'  => $topic->id,
            'user_id'   => Auth::id(),
            'parent_id' => $request->parent_id,
            'content'   => $this->sanitizeHtml($request->input('content')),
        ]);

        $topic->increment('replies_count');
        $topic->update([
            'last_reply_user_id' => Auth::id(),
            'last_reply_at'      => now(),
        ]);

        return back()->with('success', 'Reply posted!');
    }

    /**
     * Strip all HTML except safe formatting tags. Prevents XSS from Quill output.
     */
    private function sanitizeHtml(string $html): string
    {
        return strip_tags($html, '<p><br><strong><em><u><s><ul><ol><li><a><h1><h2><h3><h4><blockquote><pre><code><span>');
    }
}
