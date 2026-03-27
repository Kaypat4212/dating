@extends('layouts.app')
@section('title', 'Community Forum')
@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="bi bi-people me-2 text-primary"></i>Community Forum</h2>
            <p class="text-muted mb-0 small">Connect, share, and discuss with the community</p>
        </div>
    </div>

    {{-- Recent Activity --}}
    @if($recentTopics->isNotEmpty())
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header fw-semibold"><i class="bi bi-clock-history me-2"></i>Recent Discussions</div>
        <div class="list-group list-group-flush">
            @foreach($recentTopics->take(5) as $topic)
            <a href="{{ route('forum.topic', [$topic->category->slug, $topic->slug]) }}"
               class="list-group-item list-group-item-action py-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1 me-3">
                        <div class="fw-semibold small">{{ $topic->title }}</div>
                        <small class="text-muted">
                            {{ $topic->author->name }} in
                            <span class="text-primary">{{ $topic->category->name }}</span>
                        </small>
                    </div>
                    <small class="text-muted text-nowrap">{{ $topic->last_reply_at?->diffForHumans() }}</small>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Categories --}}
    <div class="row g-3">
        @foreach($categories as $category)
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:48px;height:48px;background:{{ $category->color ?? '#0d6efd' }}20;color:{{ $category->color ?? '#0d6efd' }};">
                            <i class="{{ $category->icon ?? 'bi bi-chat-dots' }} fs-4"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <h5 class="fw-bold mb-1">
                                <a href="{{ route('forum.category', $category->slug) }}" class="text-decoration-none text-dark stretched-link">
                                    {{ $category->name }}
                                </a>
                            </h5>
                            <p class="text-muted small mb-2">{{ $category->description }}</p>
                            <div class="d-flex gap-3 align-items-center">
                                <small class="text-muted"><i class="bi bi-chat-left me-1"></i>{{ $category->topics_count }} topics</small>
                                @if($category->topics->isNotEmpty())
                                <small class="text-muted text-truncate">
                                    Latest: {{ $category->topics->first()?->title }}
                                </small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($categories->isEmpty())
    <div class="text-center py-5 text-muted">
        <i class="bi bi-people fs-1"></i>
        <p class="mt-2">Forum categories are being set up. Check back soon!</p>
    </div>
    @endif
</div>
@endsection
