@extends('layouts.app')
@section('title', 'Edit Post — ' . $post->title)

@push('head')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
<style>
    #quill-editor { min-height: 300px; font-size: 1rem; }
    .ql-toolbar.ql-snow { border-radius: 0.375rem 0.375rem 0 0; }
    .ql-container.ql-snow { border-radius: 0 0 0.375rem 0.375rem; }
</style>
@endpush

@php /** @var \Illuminate\Support\ViewErrorBag $errors */ @endphp
@section('content')
<div class="container py-4" style="max-width: 860px;">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('blog.index') }}">Blog</a></li>
            <li class="breadcrumb-item"><a href="{{ route('blog.show', $post->slug) }}">{{ Str::limit($post->title, 40) }}</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>

    <div class="card border-0 shadow-sm">
        <div class="card-header fw-semibold d-flex align-items-center justify-content-between">
            <span><i class="bi bi-pencil me-2"></i>Edit Post</span>
            <form action="{{ route('blog.destroy', $post->slug) }}" method="POST" class="d-inline"
                  onsubmit="return confirm('Delete this post permanently?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-trash me-1"></i>Delete
                </button>
            </form>
        </div>
        <div class="card-body">

            @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            @endif

            <form action="{{ route('blog.update', $post->slug) }}" method="POST" id="blog-form">
                @csrf @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                           value="{{ old('title', $post->title) }}" required maxlength="255">
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                            <option value="">Select a category…</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(old('category_id', $post->category_id) == $cat->id)>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="draft" @selected(old('status', $post->status) === 'draft')>Draft</option>
                            <option value="published" @selected(old('status', $post->status) === 'published')>Published</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Excerpt <small class="text-muted">(optional)</small></label>
                    <textarea name="excerpt" class="form-control" rows="2" maxlength="500">{{ old('excerpt', $post->excerpt) }}</textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Content <span class="text-danger">*</span></label>
                    <div id="quill-editor">{!! old('content', $post->content) !!}</div>
                    <input type="hidden" name="content" id="content-input">
                    @error('content')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Save Changes
                    </button>
                    <a href="{{ route('blog.show', $post->slug) }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
const quill = new Quill('#quill-editor', {
    theme: 'snow',
    modules: {
        toolbar: [
            [{ header: [1, 2, 3, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            ['blockquote', 'code-block'],
            [{ list: 'ordered' }, { list: 'bullet' }],
            ['link'],
            ['clean'],
        ],
    },
});

document.getElementById('blog-form').addEventListener('submit', function () {
    document.getElementById('content-input').value = quill.root.innerHTML;
});
</script>
@endpush
