@extends('layouts.app')
@section('title', $category->name . ' - Blog')
@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('blog.index') }}">Blog</a></li>
            <li class="breadcrumb-item active">{{ $category->name }}</li>
        </ol>
    </nav>

    <div class="d-flex align-items-center mb-4 gap-3">
        @if($category->icon)
        <i class="{{ $category->icon }} fs-2" style="color:{{ $category->color ?? '#0d6efd' }}"></i>
        @endif
        <div>
            <h2 class="fw-bold mb-0">{{ $category->name }}</h2>
            @if($category->description)<p class="text-muted mb-0">{{ $category->description }}</p>@endif
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-3 order-lg-last">
            <div class="card border-0 shadow-sm">
                <div class="card-header fw-semibold"><i class="bi bi-grid me-2"></i>Categories</div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('blog.index') }}" class="list-group-item list-group-item-action">All Posts</a>
                    @foreach($categories as $cat)
                    <a href="{{ route('blog.category', $cat->slug) }}"
                       class="list-group-item list-group-item-action d-flex justify-content-between {{ $cat->id === $category->id ? 'active' : '' }}">
                        {{ $cat->name }}
                        <span class="badge bg-secondary rounded-pill">{{ $cat->posts_count }}</span>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            @if($posts->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-journal-x fs-1"></i>
                <p class="mt-2">No posts in this category yet.</p>
            </div>
            @else
            <div class="row g-4">
                @foreach($posts as $post)
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        @if($post->featured_image)
                        <img src="{{ Storage::url($post->featured_image) }}" class="card-img-top" style="height:160px;object-fit:cover;" alt="">
                        @endif
                        <div class="card-body d-flex flex-column">
                            <h6 class="fw-bold">
                                <a href="{{ route('blog.show', $post->slug) }}" class="text-decoration-none text-dark stretched-link">
                                    {{ $post->title }}
                                </a>
                            </h6>
                            <p class="text-muted small flex-grow-1">{{ Str::limit($post->excerpt, 120) }}</p>
                            <small class="text-muted">
                                By {{ $post->author->name }} &bull; {{ $post->published_at->format('M j, Y') }}
                            </small>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-4 d-flex justify-content-center">{{ $posts->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
