<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\ReviewComment;
use App\Models\ReviewHelpfulVote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReviewController extends Controller
{
    // ── Public listing ────────────────────────────────────────────────────────

    public function index(): View
    {
        $reviews = Review::approved()
            ->with(['user', 'comments' => fn($q) => $q->where('is_approved', true)->whereNull('parent_id')->with('replies.author', 'author')])
            ->orderByDesc('helpful_count')
            ->orderByDesc('created_at')
            ->paginate(10);

        $stats = [
            'total'   => Review::approved()->count(),
            'avg'     => round(Review::approved()->avg('rating') ?? 0, 1),
            'counts'  => Review::approved()
                ->selectRaw('rating, count(*) as total')
                ->groupBy('rating')
                ->orderByDesc('rating')
                ->pluck('total', 'rating')
                ->toArray(),
        ];

        return view('reviews.index', compact('reviews', 'stats'));
    }

    // ── Submit a review (guests + auth users) ─────────────────────────────────

    public function store(Request $request): RedirectResponse
    {
        /** @var ?\App\Models\User $user */
        $user = Auth::user();

        $rules = [
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title'  => ['nullable', 'string', 'max:160'],
            'body'   => ['required', 'string', 'min:20', 'max:3000'],
        ];

        if (! $user) {
            $rules['guest_name']  = ['required', 'string', 'max:100'];
            $rules['guest_email'] = ['required', 'email', 'max:180'];
        }

        $data = $request->validate($rules);

        Review::create([
            'user_id'     => $user?->id,
            'guest_name'  => $user ? null : $data['guest_name'],
            'guest_email' => $user ? null : ($data['guest_email'] ?? null),
            'rating'      => $data['rating'],
            'title'       => $data['title'] ?? null,
            'body'        => strip_tags($data['body']),
            'status'      => 'pending',
        ]);

        return back()->with('review_submitted', true);
    }

    // ── Post a comment on a review (auth required) ────────────────────────────

    public function storeComment(Request $request, Review $review): RedirectResponse
    {
        abort_if(! Auth::check(), 401);
        abort_unless($review->status === 'approved', 403, 'Cannot comment on an unapproved review.');

        $data = $request->validate([
            'body'      => ['required', 'string', 'min:2', 'max:2000'],
            'parent_id' => ['nullable', 'exists:review_comments,id'],
        ]);

        // Ensure parent belongs to this review
        if (! empty($data['parent_id'])) {
            abort_unless(
                ReviewComment::where('id', $data['parent_id'])->where('review_id', $review->id)->exists(),
                422,
                'Invalid parent comment.'
            );
        }

        ReviewComment::create([
            'review_id' => $review->id,
            'user_id'   => Auth::id(),
            'parent_id' => $data['parent_id'] ?? null,
            'body'      => strip_tags($data['body']),
            'is_approved' => true,
        ]);

        return back()->with('comment_posted', true);
    }

    // ── Mark a review as helpful (auth required) ──────────────────────────────

    public function helpful(Request $request, Review $review)
    {
        abort_if(! Auth::check(), 401);
        abort_unless($review->status === 'approved', 403);

        $userId = Auth::id();

        $exists = ReviewHelpfulVote::where('review_id', $review->id)
            ->where('user_id', $userId)
            ->exists();

        if ($exists) {
            ReviewHelpfulVote::where('review_id', $review->id)
                ->where('user_id', $userId)
                ->delete();
            $review->decrement('helpful_count');
            $voted = false;
        } else {
            ReviewHelpfulVote::create([
                'review_id' => $review->id,
                'user_id'   => $userId,
            ]);
            $review->increment('helpful_count');
            $voted = true;
        }

        if ($request->expectsJson()) {
            return response()->json([
                'helpful_count' => $review->fresh()->helpful_count,
                'voted'         => $voted,
            ]);
        }

        return back();
    }
}
