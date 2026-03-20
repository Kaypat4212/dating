@extends('layouts.app')
@section('title', 'Create Profile — Step 2')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            @include('setup._progress', ['current' => 2])
            <div class="card border-0 shadow p-4">
                <h4 class="fw-bold mb-1">Your profile</h4>
                <p class="text-muted small mb-4">Step 2: What makes you, you</p>
                <form method="POST" action="{{ route('setup.store', 2) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tagline <span class="text-muted fw-normal small">(headline)</span></label>
                        <input type="text" name="tagline" class="form-control" value="{{ old('tagline', session('setup.tagline')) }}" maxlength="120" placeholder="e.g. Coffee addict & dog lover ☕🐶">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">About Me</label>
                        <textarea name="about" class="form-control" rows="4" maxlength="2000" placeholder="Tell others a bit about yourself...">{{ old('about', session('setup.about')) }}</textarea>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Relationship Goal</label>
                            <select name="relationship_goal" class="form-select">
                                <option value="">Not specified</option>
                                @foreach(['casual','long_term','marriage','friendship','unsure'] as $g)
                                <option value="{{ $g }}" {{ old('relationship_goal', session('setup.relationship_goal')) === $g ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$g)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Education</label>
                            <select name="education_level" class="form-select">
                                <option value="">Not specified</option>
                                @foreach(['high_school','some_college','bachelors','masters','phd','trade_school'] as $e)
                                <option value="{{ $e }}" {{ old('education_level', session('setup.education_level')) === $e ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$e)) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Occupation</label>
                        <input type="text" name="occupation" class="form-control" value="{{ old('occupation', session('setup.occupation')) }}" maxlength="100">
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('setup.step', 1) }}" class="btn btn-outline-secondary flex-fill"><i class="bi bi-arrow-left me-2"></i>Back</a>
                        <button type="submit" class="btn btn-primary flex-fill fw-bold">Continue <i class="bi bi-arrow-right ms-2"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
