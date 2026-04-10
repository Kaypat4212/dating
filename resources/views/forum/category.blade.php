@extends('layouts.app')
@section('title', $category->name . ' - Forum')
@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('forum.index') }}">Forum</a></li>
            <li class="breadcrumb-item active">{{ $category->name }}</li>
        </ol>
    </nav>

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center"
                 style="width:48px;height:48px;background:{{ $category->color ?? '#0d6efd' }}20;color:{{ $category->color ?? '#0d6efd' }};">
                <i class="{{ $category->icon ?? 'bi bi-chat-dots' }} fs-4"></i>
            </div>
            <div>
                <h2 class="fw-bold mb-0">{{ $category->name }}</h2>
                <p class="text-muted small mb-0">{{ $category->description }}</p>
            </div>
        </div>
        <a href="{{ route('forum.create-topic', $category->slug) }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>New Topic
        </a>
        <button type="button" class="btn btn-sm btn-outline-secondary"
                onclick="navigator.clipboard.writeText('{{ route('forum.category', $category->slug) }}').then(() => { this.innerHTML='<i class=\'bi bi-check-lg me-1\'></i>Copied!'; setTimeout(() => { this.innerHTML='<i class=\'bi bi-share me-1\'></i>Share'; }, 2000); })"
                title="Copy link to this category">
            <i class="bi bi-share me-1"></i>Share
        </button>
    </div>

    @if($topics->isEmpty())
    <div class="text-center py-5 text-muted">
        <i class="bi bi-chat-square fs-1"></i>
        <p class="mt-2">No topics yet. <a href="{{ route('forum.create-topic', $category->slug) }}">Start the first discussion!</a></p>
    </div>
    @else
    <div class="card border-0 shadow-sm">
        <div class="list-group list-group-flush">
            @foreach($topics as $topic)
            <a href="{{ route('forum.topic', [$category->slug, $topic->slug]) }}"
               class="list-group-item list-group-item-action py-3">
                <div class="d-flex align-items-start gap-3">
                    <div class="flex-shrink-0 mt-1">
                        @if($topic->is_pinned)
                        <i class="bi bi-pin-angle-fill text-warning" title="Pinned"></i>
                        @elseif($topic->is_locked)
                        <i class="bi bi-lock-fill text-secondary" title="Locked"></i>
                        @else
                        <i class="bi bi-chat-left-text text-primary"></i>
                        @endif
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="fw-semibold">{{ $topic->title }}</div>
                        <small class="text-muted">
                            by {{ $topic->author->name }} &bull;
                            {{ $topic->created_at->format('M j, Y') }}
                        </small>
                    </div>
                    <div class="text-end text-muted small flex-shrink-0">
                        <div><i class="bi bi-reply me-1"></i>{{ $topic->replies_count }}</div>
                        <div><i class="bi bi-eye me-1"></i>{{ $topic->views_count }}</div>
                        @if($topic->last_reply_at)
                        <div class="text-nowrap">{{ $topic->last_reply_at->diffForHumans() }}</div>
                        @endif
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    <div class="mt-4 d-flex justify-content-center">{{ $topics->links() }}</div>
    @endif
</div>
@endsection
