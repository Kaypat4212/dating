@extends('layouts.app')
@section('title', 'Create Your Profile — Step 1')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            @include('setup._progress', ['current' => 1])
            <div class="card border-0 shadow p-4">
                <h4 class="fw-bold mb-1">Tell us about you</h4>
                <p class="text-muted small mb-4">Step 1: Basic identity</p>
                <form method="POST" action="{{ route('setup.store', 1) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">I am a...</label>
                        <div class="d-flex gap-2 flex-wrap">
                            @foreach(['man','woman','non-binary','other'] as $g)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gender" id="g{{ $g }}" value="{{ $g }}" {{ old('gender', session('setup.gender')) === $g ? 'checked' : '' }} required>
                                <label class="form-check-label" for="g{{ $g }}">{{ ucfirst($g) }}</label>
                            </div>
                            @endforeach
                        </div>
                        @error('gender')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Looking for...</label>
                        <div class="d-flex gap-2 flex-wrap">
                            @foreach(['men','women','everyone'] as $s)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="seeking" id="s{{ $s }}" value="{{ $s }}" {{ old('seeking', session('setup.seeking')) === $s ? 'checked' : '' }} required>
                                <label class="form-check-label" for="s{{ $s }}">{{ ucfirst($s) }}</label>
                            </div>
                            @endforeach
                        </div>
                        @error('seeking')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth', session('setup.date_of_birth')) }}" required>
                        @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold">Continue <i class="bi bi-arrow-right ms-2"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
