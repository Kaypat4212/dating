@extends('layouts.app')
@section('title', 'Write a New Post')

@push('head')
{{-- Quill rich-text editor --}}
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
<style>
    #quill-editor { min-height: 300px; font-size: 1rem; }
    .ql-toolbar.ql-snow { border-radius: 0.375rem 0.375rem 0 0; }
    .ql-container.ql-snow { border-radius: 0 0 0.375rem 0.375rem; }
</style>
@endpush

@section('content')
<div class="container py-4" style="max-width: 860px;">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('blog.index') }}">Blog</a></li>
            <li class="breadcrumb-item active">Write a Post</li>
        </ol>
    </nav>

    <div class="card border-0 shadow-sm">
        <div class="card-header fw-semibold">
            <i class="bi bi-pencil-square me-2"></i>Write a New Blog Post
        </div>
        <div class="card-body">

            @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            @endif

            <form action="{{ route('blog.store') }}" method="POST" id="blog-form">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                           value="{{ old('title') }}" required maxlength="255" placeholder="An engaging title…">
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                            <option value="">Select a category…</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="draft" @selected(old('status','draft') === 'draft')>Save as Draft</option>
                            <option value="published" @selected(old('status') === 'published')>Publish Now</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Excerpt <small class="text-muted">(optional — shown in listings)</small></label>
                    <textarea name="excerpt" class="form-control" rows="2" maxlength="500">{{ old('excerpt') }}</textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Content <span class="text-danger">*</span></label>
                    {{-- Quill editor --}}
                    <div id="quill-editor">{!! old('content') !!}</div>
                    {{-- Hidden input submitted with the form --}}
                    <input type="hidden" name="content" id="content-input">
                    @error('content')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send me-1"></i>Save Post
                    </button>
                    <a href="{{ route('blog.index') }}" class="btn btn-outline-secondary">Cancel</a>
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
    placeholder: 'Write your post here…',
});

document.getElementById('blog-form').addEventListener('submit', function () {
    document.getElementById('content-input').value = quill.root.innerHTML;
});
</script>
@endpush
