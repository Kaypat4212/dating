@extends('layouts.app')
@section('title', 'Create Profile — Step 3')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            @include('setup._progress', ['current' => 3])
            <div class="card border-0 shadow p-4">
                <h4 class="fw-bold mb-1">Add Photos</h4>
                <p class="text-muted small mb-4">Step 3: Show your best self (at least 1 required)</p>
                <form method="POST" action="{{ route('setup.store', 3) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <input type="file" name="photos[]" class="form-control @error('photos') is-invalid @enderror @error('photos.*') is-invalid @enderror" accept="image/*" multiple required>
                        <div class="form-text">JPEG/PNG/WebP, max 5 MB each. Upload up to 6 photos.</div>
                        @error('photos')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @error('photos.*')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('setup.step', 2) }}" class="btn btn-outline-secondary flex-fill"><i class="bi bi-arrow-left me-2"></i>Back</a>
                        <button type="submit" class="btn btn-primary flex-fill fw-bold">Continue <i class="bi bi-arrow-right ms-2"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
