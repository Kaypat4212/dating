@extends('layouts.app')
@section('title', 'Blog')
@section('content')
<div class="container py-4">

    {{-- Hero / Featured --}}
    @if($featured)
    <div class="card mb-4 border-0 shadow overflow-hidden">
        @if($featured->featured_image)
        <img src="{{ Storage::url($featured->featured_image) }}" alt="{{ $featured->title }}"
             class="card-img-top" style="max-height:340px;object-fit:cover;">
        @endif
        <div class="card-body">
            <span class="badge bg-danger mb-2">Featured</span>
            <h2 class="card-title fw-bold">
                <a href="{{ route('blog.show', $featured->slug) }}" class="text-decoration-none text-dark stretched-link">
                    {{ $featured->title }}
                </a>
            </h2>
            <p class="card-text text-muted">{{ $featured->excerpt }}</p>
            <small class="text-muted">
                By {{ $featured->author->name }} &bull;
                {{ $featured->published_at->diffForHumans() }} &bull;
                <i class="bi bi-eye"></i> {{ number_format($featured->views_count) }}
            </small>
        </div>
    </div>
    @endif

    <div class="row g-4">
        {{-- Sidebar --}}
        <div class="col-lg-3 order-lg-last">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header fw-semibold"><i class="bi bi-grid me-2"></i>Categories</div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('blog.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ !request()->routeIs('blog.category') ? 'active' : '' }}">
                        All Posts
                    </a>
                    @foreach($categories as $cat)
                    <a href="{{ route('blog.category', $cat->slug) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        @if($cat->icon)<i class="{{ $cat->icon }} me-2"></i>@endif
                        {{ $cat->name }}
                        <span class="badge bg-secondary rounded-pill">{{ $cat->posts_count }}</span>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Posts grid --}}
        <div class="col-lg-9">
            @if($posts->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-journal-x fs-1"></i>
                <p class="mt-2">No posts yet. Check back soon!</p>
            </div>
            @else
            <div class="row g-4">
                @foreach($posts as $post)
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 border-0 shadow-sm">
                        @if($post->featured_image)
                        <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}"
                             class="card-img-top" style="height:160px;object-fit:cover;">
                        @endif
                        <div class="card-body d-flex flex-column">
                            @if($post->category)
                            <span class="badge mb-2" style="background:{{ $post->category->color ?? '#6c757d' }}">
                                {{ $post->category->name }}
                            </span>
                            @endif
                            <h6 class="card-title fw-bold">
                                <a href="{{ route('blog.show', $post->slug) }}" class="text-decoration-none text-dark stretched-link">
                                    {{ $post->title }}
                                </a>
                            </h6>
                            <p class="card-text text-muted small flex-grow-1">{{ Str::limit($post->excerpt, 100) }}</p>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <small class="text-muted">{{ $post->published_at->format('M j, Y') }}</small>
                                <small class="text-muted">
                                    <i class="bi bi-eye me-1"></i>{{ number_format($post->views_count) }}
                                    <i class="bi bi-chat ms-2 me-1"></i>{{ $post->comments_count }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-4 d-flex justify-content-center">
                {{ $posts->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
