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

    public function createTopic(ForumCategory $category): View
    {
        return view('forum.create-topic', compact('category'));
    }

    public function storeTopic(Request $request, ForumCategory $category)
    {
        abort_unless($category->is_active, 404);

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
            'content'        => $request->input('content'),
            'tags'           => $tags,
            'last_reply_at'  => now(),
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
            'content'   => $request->input('content'),
        ]);

        $topic->increment('replies_count');
        $topic->update([
            'last_reply_user_id' => Auth::id(),
            'last_reply_at'      => now(),
        ]);

        return back()->with('success', 'Reply posted!');
    }
}
