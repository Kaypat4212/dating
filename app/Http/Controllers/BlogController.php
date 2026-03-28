<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use App\Models\BlogComment;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        $posts = BlogPost::published()
            ->with(['author', 'category'])
            ->orderByDesc('published_at')
            ->paginate(12);

        $categories = BlogCategory::where('is_active', true)
            ->withCount(['posts' => fn($q) => $q->published()])
            ->orderBy('order')
            ->get();

        $featured = BlogPost::published()
            ->where('is_featured', true)
            ->with(['author', 'category'])
            ->orderByDesc('published_at')
            ->first();

        return view('blog.index', compact('posts', 'categories', 'featured'));
    }

    public function category(BlogCategory $category): View
    {
        $posts = $category->posts()
            ->published()
            ->with(['author'])
            ->orderByDesc('published_at')
            ->paginate(12);

        $categories = BlogCategory::where('is_active', true)
            ->withCount(['posts' => fn($q) => $q->published()])
            ->orderBy('order')
            ->get();

        return view('blog.category', compact('category', 'posts', 'categories'));
    }

    public function show(BlogPost $post): View
    {
        abort_unless($post->status === 'published', 404);

        // Increment view count
        $post->increment('views_count');

        $comments = $post->comments()
            ->whereNull('parent_id')
            ->where('is_approved', true)
            ->with(['author', 'replies.author'])
            ->orderBy('created_at')
            ->get();

        $related = BlogPost::published()
            ->where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        return view('blog.show', compact('post', 'comments', 'related'));
    }

    public function create(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_unless($user->hasAnyRole(['admin', 'blogger']), 403);

        $categories = BlogCategory::where('is_active', true)->orderBy('order')->get();

        return view('blog.create', compact('categories'));
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_unless($user->hasAnyRole(['admin', 'blogger']), 403);

        $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:blog_categories,id'],
            'excerpt'     => ['nullable', 'string', 'max:500'],
            'content'     => ['required', 'string', 'min:50'],
            'status'      => ['required', 'in:draft,published'],
        ]);

        $slug = Str::slug($request->title) . '-' . time();

        $post = BlogPost::create([
            'author_id'     => $user->id,
            'category_id'   => $request->category_id,
            'title'         => $request->title,
            'slug'          => $slug,
            'excerpt'       => $request->excerpt,
            'content'       => $this->sanitizeHtml($request->input('content')),
            'status'        => $request->status,
            'published_at'  => $request->status === 'published' ? now() : null,
            'allow_comments'=> true,
        ]);

        return redirect()->route('blog.show', $post->slug)
            ->with('success', 'Post published!');
    }

    public function edit(BlogPost $post)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_unless($user->hasRole('admin') || ($user->hasRole('blogger') && $post->author_id === $user->id), 403);

        $categories = BlogCategory::where('is_active', true)->orderBy('order')->get();

        return view('blog.edit', compact('post', 'categories'));
    }

    public function update(Request $request, BlogPost $post)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_unless($user->hasRole('admin') || ($user->hasRole('blogger') && $post->author_id === $user->id), 403);

        $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:blog_categories,id'],
            'excerpt'     => ['nullable', 'string', 'max:500'],
            'content'     => ['required', 'string', 'min:50'],
            'status'      => ['required', 'in:draft,published'],
        ]);

        $post->update([
            'category_id'  => $request->category_id,
            'title'        => $request->title,
            'excerpt'      => $request->excerpt,
            'content'      => $this->sanitizeHtml($request->input('content')),
            'status'       => $request->status,
            'published_at' => $request->status === 'published' && !$post->published_at ? now() : $post->published_at,
        ]);

        return redirect()->route('blog.show', $post->slug)
            ->with('success', 'Post updated!');
    }

    public function destroy(BlogPost $post)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_unless($user->hasRole('admin') || ($user->hasRole('blogger') && $post->author_id === $user->id), 403);

        $post->delete();

        return redirect()->route('blog.index')
            ->with('success', 'Post deleted.');
    }

    public function storeComment(Request $request, BlogPost $post)
    {
        abort_unless($post->allow_comments, 403);

        $request->validate([
            'content'   => ['required', 'string', 'max:2000'],
            'parent_id' => ['nullable', 'exists:blog_comments,id'],
        ]);

        $post->comments()->create([
            'user_id'     => Auth::id(),
            'parent_id'   => $request->parent_id,
            'content'     => $request->input('content'),
            'is_approved' => true,
        ]);

        $post->increment('comments_count');

        return back()->with('success', 'Comment posted!');
    }

    /**
     * Strip all HTML except safe formatting tags.
     */
    private function sanitizeHtml(string $html): string
    {
        return strip_tags($html, '<p><br><strong><em><u><s><ul><ol><li><a><h1><h2><h3><h4><blockquote><pre><code><span>');
    }
}
