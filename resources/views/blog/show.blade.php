@extends('layouts.app')
@section('title', $post->title)
@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('blog.index') }}">Blog</a></li>
            @if($post->category)
            <li class="breadcrumb-item"><a href="{{ route('blog.category', $post->category->slug) }}">{{ $post->category->name }}</a></li>
            @endif
            <li class="breadcrumb-item active text-truncate" style="max-width:200px;">{{ $post->title }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-lg-8">
            {{-- Post --}}
            <article class="card border-0 shadow-sm mb-4">
                @if($post->featured_image)
                <img src="{{ Storage::url($post->featured_image) }}" class="card-img-top rounded-top" style="max-height:400px;object-fit:cover;" alt="">
                @endif
                <div class="card-body">
                    <h1 class="fw-bold h3 mb-2">{{ $post->title }}</h1>
                    <div class="d-flex align-items-center gap-3 mb-3 text-muted small flex-wrap">
                        <span><i class="bi bi-person me-1"></i>{{ $post->author->name }}</span>
                        <span><i class="bi bi-calendar me-1"></i>{{ $post->published_at->format('M j, Y') }}</span>
                        <span><i class="bi bi-eye me-1"></i>{{ number_format($post->views_count) }} views</span>
                        <span><i class="bi bi-chat me-1"></i>{{ $post->comments_count }} comments</span>
                    </div>
                    @if($post->tags)
                    <div class="mb-3">
                        @foreach($post->tags as $tag)
                        <span class="badge bg-light text-dark border me-1">{{ $tag }}</span>
                        @endforeach
                    </div>
                    @endif
                    <div class="blog-content lh-lg">
                        {!! nl2br(e($post->content)) !!}
                    </div>
                </div>
            </article>

            {{-- Comments --}}
            @if($post->allow_comments)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header fw-semibold">
                    <i class="bi bi-chat-square-text me-2"></i>Comments ({{ $post->comments_count }})
                </div>
                <div class="card-body">
                    @auth
                    <form action="{{ route('blog.comment.store', $post->slug) }}" method="POST" class="mb-4">
                        @csrf
                        <div class="mb-2">
                            <textarea name="content" class="form-control @error('content') is-invalid @enderror"
                                rows="3" placeholder="Write a comment..." required maxlength="2000"></textarea>
                            @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Post Comment</button>
                    </form>
                    @endauth

                    @forelse($comments as $comment)
                    <div class="d-flex gap-3 mb-3" id="comment-{{ $comment->id }}">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                 style="width:40px;height:40px;font-size:0.875rem;">
                                {{ strtoupper(substr($comment->author->name, 0, 1)) }}
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="bg-light rounded p-3">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <strong class="small">{{ $comment->author->name }}</strong>
                                    <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-0 small">{{ $comment->content }}</p>
                            </div>
                            {{-- Nested replies --}}
                            @foreach($comment->replies as $reply)
                            <div class="d-flex gap-2 mt-2 ms-4">
                                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width:32px;height:32px;font-size:0.75rem;">
                                    {{ strtoupper(substr($reply->author->name, 0, 1)) }}
                                </div>
                                <div class="bg-light rounded p-2 flex-grow-1">
                                    <div class="d-flex justify-content-between mb-1">
                                        <strong class="small">{{ $reply->author->name }}</strong>
                                        <small class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-0 small">{{ $reply->content }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @empty
                    <p class="text-muted text-center py-3">No comments yet. Be the first!</p>
                    @endforelse
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            @if($related->isNotEmpty())
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header fw-semibold"><i class="bi bi-journals me-2"></i>Related Posts</div>
                <div class="list-group list-group-flush">
                    @foreach($related as $r)
                    <a href="{{ route('blog.show', $r->slug) }}" class="list-group-item list-group-item-action">
                        <div class="fw-semibold small">{{ $r->title }}</div>
                        <small class="text-muted">{{ $r->published_at->format('M j, Y') }}</small>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="card border-0 shadow-sm">
                <div class="card-header fw-semibold"><i class="bi bi-lightning me-2"></i>About the Author</div>
                <div class="card-body text-center">
                    <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-2"
                         style="width:60px;height:60px;font-size:1.25rem;">
                        {{ strtoupper(substr($post->author->name, 0, 1)) }}
                    </div>
                    <h6 class="fw-bold mb-0">{{ $post->author->name }}</h6>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
