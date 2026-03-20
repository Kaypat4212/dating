@extends('layouts.app')
@section('title', 'Create Profile — Step 5')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            @include('setup._progress', ['current' => 5])
            <div class="card border-0 shadow p-4">
                <h4 class="fw-bold mb-1">Your Interests</h4>
                <p class="text-muted small mb-4">Step 5: Pick at least 3 interests to help us find great matches</p>
                <form method="POST" action="{{ route('setup.store', 5) }}">
                    @csrf
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        @foreach($interests as $interest)
                        <div>
                            <input type="checkbox" class="btn-check" name="interests[]" id="int{{ $interest->id }}" value="{{ $interest->id }}"
                                {{ in_array($interest->id, old('interests', session('setup.interests', []))) ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary btn-sm rounded-pill" for="int{{ $interest->id }}">{{ $interest->name }}</label>
                        </div>
                        @endforeach
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('setup.step', 4) }}" class="btn btn-outline-secondary flex-fill"><i class="bi bi-arrow-left me-2"></i>Back</a>
                        <button type="submit" class="btn btn-success flex-fill fw-bold"><i class="bi bi-check-circle me-2"></i>Finish Setup!</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
