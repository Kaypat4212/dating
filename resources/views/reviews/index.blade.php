@extends('layouts.app')
@section('title', 'Reviews & Testimonials')

@push('styles')
<style>
/* ── Reviews page ──────────────────────────────────────────────────────────── */
.reviews-hero {
    background: linear-gradient(135deg, #f43f5e 0%, #ec4899 50%, #a855f7 100%);
    border-radius: 20px;
    color: #fff;
    padding: 2.5rem 2rem;
    margin-bottom: 2.5rem;
    text-align: center;
}
.reviews-hero h1 { font-size: 2rem; font-weight: 800; margin-bottom: .5rem; }
.reviews-hero p  { opacity: .88; margin: 0; }

/* Rating summary bar */
.rating-summary { background: var(--bs-body-bg); border: 1px solid var(--bs-border-color); border-radius: 16px; padding: 1.5rem; }
.rating-avg-num { font-size: 3.5rem; font-weight: 900; line-height: 1; color: var(--bs-body-color); }
.rating-avg-stars { font-size: 1.4rem; color: #f59e0b; letter-spacing: 2px; }
.rating-bar-row { display: flex; align-items: center; gap: 8px; margin-bottom: 5px; font-size: .82rem; }
.rating-bar-track { flex: 1; height: 8px; background: var(--bs-secondary-bg); border-radius: 99px; overflow: hidden; }
.rating-bar-fill  { height: 100%; border-radius: 99px; background: linear-gradient(90deg, #f59e0b, #fbbf24); transition: width .6s ease; }
.rating-bar-count { width: 28px; text-align: right; color: var(--bs-secondary-color); font-size: .78rem; }

/* Star input */
.star-rating-input { display: flex; flex-direction: row-reverse; justify-content: flex-end; gap: 4px; }
.star-rating-input input { display: none; }
.star-rating-input label {
    font-size: 2rem; color: #d1d5db; cursor: pointer;
    transition: color .15s, transform .1s;
}
.star-rating-input label:hover,
.star-rating-input label:hover ~ label,
.star-rating-input input:checked ~ label { color: #f59e0b; }
.star-rating-input label:hover { transform: scale(1.15); }

/* Review card */
.review-card {
    background: var(--bs-body-bg);
    border: 1px solid var(--bs-border-color);
    border-radius: 16px;
    padding: 1.4rem 1.5rem;
    margin-bottom: 1.25rem;
    transition: box-shadow .2s;
}
.review-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.07); }
.rv-stars { color: #f59e0b; font-size: 1rem; letter-spacing: 1px; }
.rv-avatar {
    width: 40px; height: 40px; border-radius: 50%;
    background: linear-gradient(135deg, #f43f5e, #a855f7);
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; color: #fff; font-size: 1rem; flex-shrink: 0;
}
.rv-helpful-btn {
    background: none; border: 1px solid var(--bs-border-color); border-radius: 99px;
    padding: 3px 12px; font-size: .78rem; color: var(--bs-secondary-color);
    cursor: pointer; display: inline-flex; align-items: center; gap: 5px; transition: all .15s;
}
.rv-helpful-btn:hover, .rv-helpful-btn.active {
    background: #fef3c7; border-color: #f59e0b; color: #92400e;
}

/* Comments */
.rv-comments  { border-top: 1px solid var(--bs-border-color); margin-top: 1rem; padding-top: .8rem; }
.rv-comment   { display: flex; gap: 10px; margin-bottom: .9rem; }
.rv-com-avatar {
    width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    display: flex; align-items: center; justify-content: center;
    font-size: .78rem; color: #fff; font-weight: 700;
}
.rv-com-bubble {
    background: var(--bs-secondary-bg); border-radius: 12px; padding: 8px 12px;
    font-size: .85rem; flex: 1;
}
.rv-com-author { font-weight: 600; font-size: .8rem; }
.rv-reply-thread { margin-left: 42px; }
.rv-com-reply-btn { background: none; border: none; font-size: .75rem; color: var(--bs-secondary-color); padding: 0; cursor: pointer; }
.rv-com-reply-btn:hover { color: #f43f5e; }
.reply-form-wrap { display: none; margin-top: 6px; }

/* Write review card */
.write-review-card {
    background: var(--bs-body-bg);
    border: 1.5px dashed var(--bs-border-color);
    border-radius: 16px; padding: 1.5rem; margin-bottom: 1.5rem;
}
.guest-fields { display: none; }
</style>
@endpush

@section('content')
@php
    $authUser = auth()->user();
@endphp
<div class="container py-4" style="max-width:860px">

    {{-- Hero ----------------------------------------------------------------}}
    <div class="reviews-hero">
        <h1>❤️ What Our Members Say</h1>
        <p>Real stories from real people who found connection here.</p>
    </div>

    {{-- Flash messages ------------------------------------------------------}}
    @if(session('review_submitted'))
    <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        <strong>Thank you!</strong> Your review has been submitted and will appear after admin approval.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('comment_posted'))
    <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
        <i class="bi bi-chat-dots-fill me-2"></i> Your comment has been posted!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-4">

        {{-- LEFT: stats + write review ────────────────────────────────────── --}}
        <div class="col-lg-4">

            {{-- Rating Summary --}}
            <div class="rating-summary mb-3">
                <div class="text-center mb-3">
                    <div class="rating-avg-num">{{ number_format($stats['avg'], 1) }}</div>
                    <div class="rating-avg-stars">
                        @for($s = 1; $s <= 5; $s++)
                            {{ $s <= round($stats['avg']) ? '★' : '☆' }}
                        @endfor
                    </div>
                    <div class="text-muted" style="font-size:.82rem">{{ $stats['total'] }} {{ Str::plural('review', $stats['total']) }}</div>
                </div>
                @foreach([5,4,3,2,1] as $star)
                @php $cnt = $stats['counts'][$star] ?? 0; $pct = $stats['total'] > 0 ? ($cnt / $stats['total']) * 100 : 0; @endphp
                <div class="rating-bar-row">
                    <span style="font-size:.78rem">{{ $star }}★</span>
                    <div class="rating-bar-track">
                        <div class="rating-bar-fill" style="width:{{ $pct }}%"></div>
                    </div>
                    <span class="rating-bar-count">{{ $cnt }}</span>
                </div>
                @endforeach
            </div>

            {{-- Write Review form --}}
            <div class="write-review-card">
                <h6 class="fw-bold mb-3"><i class="bi bi-pencil-square me-1 text-danger"></i> Leave a Review</h6>

                <form action="{{ route('reviews.store') }}" method="POST" id="reviewForm">
                    @csrf

                    {{-- Star picker --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.85rem">Your Rating *</label>
                        <div class="star-rating-input">
                            @for($i = 5; $i >= 1; $i--)
                            <input type="radio" name="rating" id="star{{ $i }}" value="{{ $i }}"
                                   {{ old('rating') == $i ? 'checked' : '' }} required>
                            <label for="star{{ $i }}" title="{{ $i }} star{{ $i > 1 ? 's' : '' }}">★</label>
                            @endfor
                        </div>
                        @error('rating')
                        <div class="text-danger" style="font-size:.78rem">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Guest fields (hidden if logged in) --}}
                    @guest
                    <div class="guest-fields" id="guestFields" style="display:block">
                        <div class="mb-2">
                            <input type="text" name="guest_name" class="form-control form-control-sm @error('guest_name') is-invalid @enderror"
                                   placeholder="Your name *" value="{{ old('guest_name') }}" required>
                            @error('guest_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-2">
                            <input type="email" name="guest_email" class="form-control form-control-sm @error('guest_email') is-invalid @enderror"
                                   placeholder="Email (private, not shown) *" value="{{ old('guest_email') }}" required>
                            @error('guest_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    @endguest

                    <div class="mb-2">
                        <input type="text" name="title" class="form-control form-control-sm @error('title') is-invalid @enderror"
                               placeholder="Headline (optional)" value="{{ old('title') }}" maxlength="160">
                        @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <textarea name="body" rows="4"
                                  class="form-control form-control-sm @error('body') is-invalid @enderror"
                                  placeholder="Share your experience... (min 20 chars)" required minlength="20" maxlength="3000">{{ old('body') }}</textarea>
                        @error('body')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-danger w-100 fw-semibold" style="border-radius:10px">
                        <i class="bi bi-send-fill me-1"></i> Submit Review
                    </button>
                </form>

                @guest
                <div class="text-center mt-2" style="font-size:.78rem; color:var(--bs-secondary-color)">
                    <i class="bi bi-info-circle me-1"></i>
                    Have an account? <a href="{{ route('login') }}">Log in</a> to review with your profile.
                </div>
                @endguest
            </div>
        </div>

        {{-- RIGHT: review list ─────────────────────────────────────────────── --}}
        <div class="col-lg-8">

            @if($reviews->isEmpty())
            <div class="text-center py-5 text-muted">
                <div style="font-size:3rem">💬</div>
                <h5 class="mt-2">No reviews yet</h5>
                <p style="font-size:.9rem">Be the first to share your experience!</p>
            </div>
            @else

            @foreach($reviews as $review)
            @php
                $initials = strtoupper(substr($review->author_name, 0, 1));
                $myVoted  = $authUser ? $review->hasBeenMarkedHelpfulBy($authUser->id) : false;
                $topComments = $review->comments->where('parent_id', null);
            @endphp
            <div class="review-card" id="review-{{ $review->id }}">
                {{-- Header --}}
                <div class="d-flex align-items-start gap-3 mb-2">
                    <div class="rv-avatar">{{ $initials }}</div>
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-1">
                            <span class="fw-bold" style="font-size:.93rem">{{ $review->author_name }}</span>
                            <span class="text-muted" style="font-size:.75rem">{{ $review->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="rv-stars">
                            @for($s = 1; $s <= 5; $s++){{ $s <= $review->rating ? '★' : '☆' }}@endfor
                        </div>
                    </div>
                </div>

                {{-- Title + body --}}
                @if($review->title)
                <div class="fw-semibold mb-1" style="font-size:.92rem">{{ $review->title }}</div>
                @endif
                <p class="mb-2" style="font-size:.88rem; color:var(--bs-body-color)">{{ $review->body }}</p>

                {{-- Helpful --}}
                <div class="d-flex align-items-center gap-2">
                    @auth
                    <button type="button"
                            class="rv-helpful-btn {{ $myVoted ? 'active' : '' }}"
                            data-review-id="{{ $review->id }}"
                            onclick="toggleHelpful(this, {{ $review->id }})">
                        👍 Helpful
                        <span class="helpful-count">{{ $review->helpful_count }}</span>
                    </button>
                    @else
                    <span class="text-muted" style="font-size:.78rem">👍 {{ $review->helpful_count }} found this helpful</span>
                    @endauth

                    <button type="button"
                            class="rv-com-reply-btn ms-auto"
                            onclick="toggleCommentForm('new-comment-{{ $review->id }}')">
                        <i class="bi bi-chat me-1"></i>
                        {{ $topComments->count() + $review->comments->sum(fn($c) => $c->replies->count()) }} comment{{ ($topComments->count() + $review->comments->sum(fn($c) => $c->replies->count())) !== 1 ? 's' : '' }}
                    </button>
                </div>

                {{-- Comments section --}}
                @if($topComments->isNotEmpty() || $authUser)
                <div class="rv-comments">

                    {{-- Existing comments --}}
                    @foreach($topComments as $comment)
                    @php $comInitials = strtoupper(substr($comment->author->name ?? 'U', 0, 1)); @endphp
                    <div class="rv-comment">
                        @if($comment->author->profile_photo_path ?? null)
                        <img src="{{ Storage::url($comment->author->profile_photo_path) }}" class="rv-com-avatar" style="object-fit:cover" alt="">
                        @else
                        <div class="rv-com-avatar">{{ $comInitials }}</div>
                        @endif
                        <div class="flex-grow-1">
                            <div class="rv-com-bubble">
                                <span class="rv-com-author">{{ $comment->author->name ?? 'User' }}</span>
                                <span class="text-muted ms-1" style="font-size:.72rem">{{ $comment->created_at->diffForHumans() }}</span>
                                <p class="mb-0 mt-1" style="font-size:.84rem">{{ $comment->body }}</p>
                            </div>
                            @auth
                            <button type="button" class="rv-com-reply-btn mt-1"
                                    onclick="toggleCommentForm('reply-{{ $comment->id }}')">
                                <i class="bi bi-reply me-1"></i>Reply
                            </button>
                            @endauth

                            {{-- Replies to this comment --}}
                            @foreach($comment->replies as $reply)
                            @php $rInitials = strtoupper(substr($reply->author->name ?? 'U', 0, 1)); @endphp
                            <div class="rv-comment rv-reply-thread mt-1">
                                @if($reply->author->profile_photo_path ?? null)
                                <img src="{{ Storage::url($reply->author->profile_photo_path) }}" class="rv-com-avatar" style="object-fit:cover;width:26px;height:26px;font-size:.68rem" alt="">
                                @else
                                <div class="rv-com-avatar" style="width:26px;height:26px;font-size:.68rem">{{ $rInitials }}</div>
                                @endif
                                <div class="flex-grow-1">
                                    <div class="rv-com-bubble">
                                        <span class="rv-com-author">{{ $reply->author->name ?? 'User' }}</span>
                                        <span class="text-muted ms-1" style="font-size:.72rem">{{ $reply->created_at->diffForHumans() }}</span>
                                        <p class="mb-0 mt-1" style="font-size:.84rem">{{ $reply->body }}</p>
                                    </div>
                                </div>
                            </div>
                            @endforeach

                            {{-- Reply form for this comment --}}
                            @auth
                            <div class="reply-form-wrap mt-1" id="reply-{{ $comment->id }}">
                                <form action="{{ route('reviews.comment.store', $review->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                    <div class="d-flex gap-2">
                                        <textarea name="body" rows="2" class="form-control form-control-sm"
                                                  placeholder="Write a reply…" required minlength="2" maxlength="2000"
                                                  style="border-radius:10px;font-size:.83rem"></textarea>
                                        <button type="submit" class="btn btn-sm btn-danger align-self-end" style="border-radius:8px">
                                            <i class="bi bi-send-fill"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                            @endauth
                        </div>
                    </div>
                    @endforeach

                    {{-- New top-level comment form --}}
                    @auth
                    <div class="reply-form-wrap mt-2" id="new-comment-{{ $review->id }}">
                        <form action="{{ route('reviews.comment.store', $review->id) }}" method="POST">
                            @csrf
                            <div class="d-flex gap-2">
                                <textarea name="body" rows="2" class="form-control form-control-sm"
                                          placeholder="Write a comment…" required minlength="2" maxlength="2000"
                                          style="border-radius:10px;font-size:.83rem"></textarea>
                                <button type="submit" class="btn btn-sm btn-primary align-self-end" style="border-radius:8px">
                                    <i class="bi bi-send-fill"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    @else
                    <div class="mt-2 text-center" style="font-size:.8rem;color:var(--bs-secondary-color)">
                        <a href="{{ route('login') }}" class="text-decoration-none">
                            <i class="bi bi-lock-fill me-1"></i>Log in to comment
                        </a>
                    </div>
                    @endauth

                </div>
                @endif
            </div>
            @endforeach

            {{-- Pagination --}}
            <div class="d-flex justify-content-center mt-3">
                {{ $reviews->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Toggle comment/reply forms
function toggleCommentForm(id) {
    const el = document.getElementById(id);
    if (!el) return;
    const isHidden = el.style.display === 'none' || !el.style.display;
    // Close any open forms
    document.querySelectorAll('.reply-form-wrap').forEach(f => f.style.display = 'none');
    el.style.display = isHidden ? 'block' : 'none';
    if (isHidden) el.querySelector('textarea')?.focus();
}

// Helpful vote toggle (AJAX)
function toggleHelpful(btn, reviewId) {
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!token) return;

    fetch(`/reviews/${reviewId}/helpful`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        btn.querySelector('.helpful-count').textContent = data.helpful_count;
        btn.classList.toggle('active', data.voted);
    })
    .catch(() => {});
}
</script>
@endpush
