@extends('layouts.app')
@section('title', 'New Topic - ' . $category->name)
@section('content')
<div class="container py-4" style="max-width:720px;">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('forum.index') }}">Forum</a></li>
            <li class="breadcrumb-item"><a href="{{ route('forum.category', $category->slug) }}">{{ $category->name }}</a></li>
            <li class="breadcrumb-item active">New Topic</li>
        </ol>
    </nav>

    <div class="card border-0 shadow-sm">
        <div class="card-header fw-semibold"><i class="bi bi-pencil-square me-2"></i>Create New Topic</div>
        <div class="card-body">
            <form action="{{ route('forum.store-topic', $category->slug) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Topic Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                           value="{{ old('title') }}" placeholder="Ask a question or start a discussion..." required minlength="5" maxlength="200">
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Content <span class="text-danger">*</span></label>
                    <textarea name="content" class="form-control @error('content') is-invalid @enderror"
                              rows="8" placeholder="Share your thoughts, questions, or discussion..." required minlength="20" maxlength="10000">{{ old('content') }}</textarea>
                    @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tags <span class="text-muted fw-normal">(comma-separated, optional)</span></label>
                    <input type="text" name="tags" class="form-control" value="{{ old('tags') }}" placeholder="e.g. dating, advice, first-date">
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send me-1"></i>Post Topic
                    </button>
                    <a href="{{ route('forum.category', $category->slug) }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
