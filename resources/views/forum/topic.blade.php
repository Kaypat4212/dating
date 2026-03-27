@extends('layouts.app')
@section('title', $topic->title)
@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('forum.index') }}">Forum</a></li>
            <li class="breadcrumb-item"><a href="{{ route('forum.category', $category->slug) }}">{{ $category->name }}</a></li>
            <li class="breadcrumb-item active text-truncate" style="max-width:200px;">{{ $topic->title }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-lg-8">
            {{-- Original Post --}}
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:44px;height:44px;">
                            {{ strtoupper(substr($topic->author->name, 0, 1)) }}
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">{{ $topic->title }}</h5>
                            <small class="text-muted">
                                by {{ $topic->author->name }} &bull; {{ $topic->created_at->format('M j, Y') }}
                                &bull; <i class="bi bi-eye me-1"></i>{{ $topic->views_count }} views
                            </small>
                        </div>
                    </div>
                    <p class="lh-lg">{{ $topic->content }}</p>
                    @if($topic->tags)
                    <div class="mt-2">
                        @foreach($topic->tags as $tag)
                        <span class="badge bg-light text-dark border me-1">{{ $tag }}</span>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            {{-- Replies --}}
            @if($replies->isNotEmpty())
            <h6 class="fw-semibold mb-3">{{ $topic->replies_count }} {{ Str::plural('Reply', $topic->replies_count) }}</h6>
            @foreach($replies as $reply)
            <div class="card border-0 shadow-sm mb-3 {{ $reply->is_best_answer ? 'border-success border' : '' }}" id="reply-{{ $reply->id }}">
                @if($reply->is_best_answer)
                <div class="card-header bg-success text-white small fw-semibold py-1">
                    <i class="bi bi-check-circle me-1"></i>Best Answer
                </div>
                @endif
                <div class="card-body">
                    <div class="d-flex gap-3">
                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:36px;height:36px;font-size:0.8rem;">
                            {{ strtoupper(substr($reply->author->name, 0, 1)) }}
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between mb-1">
                                <strong class="small">{{ $reply->author->name }}</strong>
                                <small class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-0">{{ $reply->content }}</p>
                        </div>
                    </div>
                    {{-- Nested replies --}}
                    @foreach($reply->children as $child)
                    <div class="d-flex gap-3 mt-3 ms-5 ps-2 border-start">
                        <div class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:30px;height:30px;font-size:0.75rem;">
                            {{ strtoupper(substr($child->author->name, 0, 1)) }}
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between mb-1">
                                <strong class="small">{{ $child->author->name }}</strong>
                                <small class="text-muted">{{ $child->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-0 small">{{ $child->content }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
            <div class="d-flex justify-content-center">{{ $replies->links() }}</div>
            @endif

            {{-- Reply form --}}
            @if(!$topic->is_locked)
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header fw-semibold"><i class="bi bi-reply me-2"></i>Post a Reply</div>
                <div class="card-body">
                    <form action="{{ route('forum.reply', [$category->slug, $topic->slug]) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <textarea name="content" class="form-control @error('content') is-invalid @enderror"
                                rows="5" placeholder="Share your thoughts..." required minlength="5" maxlength="5000">{{ old('content') }}</textarea>
                            @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-send me-1"></i>Post Reply
                        </button>
                    </form>
                </div>
            </div>
            @else
            <div class="alert alert-warning mt-4"><i class="bi bi-lock me-2"></i>This topic is locked and no longer accepts replies.</div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header fw-semibold">Topic Stats</div>
                <div class="card-body">
                    <div class="row g-2 text-center">
                        <div class="col-4">
                            <div class="fw-bold text-primary">{{ $topic->views_count }}</div>
                            <small class="text-muted">Views</small>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold text-success">{{ $topic->replies_count }}</div>
                            <small class="text-muted">Replies</small>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold text-warning">{{ $topic->likes_count }}</div>
                            <small class="text-muted">Likes</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card border-0 shadow-sm">
                <div class="card-header fw-semibold">Forum Rules</div>
                <div class="card-body small text-muted">
                    <ul class="mb-0 ps-3">
                        <li>Be respectful and kind</li>
                        <li>Stay on topic</li>
                        <li>No spam or self-promotion</li>
                        <li>Protect privacy (yours & others)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
