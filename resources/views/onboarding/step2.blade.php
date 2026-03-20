@extends('layouts.app')
@section('title', 'Setup — Step 2 of 5')
@section('content')
<div class="container py-5" style="max-width:700px">

    {{-- Progress --}}
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="small fw-semibold text-muted">Step {{ $step }} of {{ $total }}</span>
            <span class="small text-muted">{{ round(($step/$total)*100) }}% complete</span>
        </div>
        <div class="progress" style="height:6px;border-radius:10px">
            <div class="progress-bar bg-primary" style="width:{{ round(($step/$total)*100) }}%"></div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
        <div class="text-center mb-4">
            <div class="display-5 mb-2">✍️</div>
            <h3 class="fw-bold">About You</h3>
            <p class="text-muted">Let people know what makes you unique.</p>
        </div>

        <form method="POST" action="{{ route('setup.store', ['step' => 2]) }}">
            @csrf

            {{-- Headline --}}
            <div class="mb-3">
                <label for="headline" class="form-label fw-semibold">Headline <span class="text-muted fw-normal">(max 120 chars)</span></label>
                <input type="text" name="headline" id="headline"
                    class="form-control @error('headline') is-invalid @enderror"
                    maxlength="120"
                    placeholder="e.g. Adventurer at heart, coffee addict"
                    value="{{ old('headline', $profile?->headline ?? '') }}">
                @error('headline')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Bio --}}
            <div class="mb-3">
                <label for="bio" class="form-label fw-semibold">Bio <span class="text-muted fw-normal">(max 1000 chars)</span></label>
                <textarea name="bio" id="bio" rows="4"
                    class="form-control @error('bio') is-invalid @enderror"
                    maxlength="1000"
                    placeholder="Tell potential matches a bit about yourself…">{{ old('bio', $profile?->bio ?? '') }}</textarea>
                @error('bio')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row g-3 mb-3">
                {{-- Height --}}
                <div class="col-sm-6">
                    <label for="height_cm" class="form-label fw-semibold">Height (cm)</label>
                    <input type="number" name="height_cm" id="height_cm"
                        class="form-control @error('height_cm') is-invalid @enderror"
                        min="100" max="250" step="1" placeholder="170"
                        value="{{ old('height_cm', $profile?->height_cm ?? '') }}">
                    @error('height_cm')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Body Type --}}
                <div class="col-sm-6">
                    <label for="body_type" class="form-label fw-semibold">Body Type</label>
                    <select name="body_type" id="body_type" class="form-select @error('body_type') is-invalid @enderror">
                        <option value="">Select…</option>
                        @foreach(['slim'=>'Slim','athletic'=>'Athletic','average'=>'Average','curvy'=>'Curvy','large'=>'Large','prefer_not_say'=>'Prefer not to say'] as $v=>$l)
                        <option value="{{ $v }}" {{ old('body_type', $profile?->body_type ?? '') === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                    @error('body_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Relationship Goal --}}
                <div class="col-sm-6">
                    <label for="relationship_goal" class="form-label fw-semibold">Relationship Goal</label>
                    <select name="relationship_goal" id="relationship_goal" class="form-select @error('relationship_goal') is-invalid @enderror">
                        <option value="">Select…</option>
                        @foreach(['casual'=>'Casual','serious'=>'Serious relationship','friendship'=>'Friendship','marriage'=>'Marriage','open'=>'Open relationship'] as $v=>$l)
                        <option value="{{ $v }}" {{ old('relationship_goal', $profile?->relationship_goal ?? '') === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                    @error('relationship_goal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Education --}}
                <div class="col-sm-6">
                    <label for="education" class="form-label fw-semibold">Education</label>
                    <select name="education" id="education" class="form-select @error('education') is-invalid @enderror">
                        <option value="">Select…</option>
                        @foreach(['high_school'=>'High School','some_college'=>'Some College','bachelors'=>"Bachelor's",'masters'=>"Master's",'phd'=>'PhD','trade_school'=>'Trade School','other'=>'Other'] as $v=>$l)
                        <option value="{{ $v }}" {{ old('education', $profile?->education ?? '') === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                    @error('education')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Occupation --}}
                <div class="col-sm-6">
                    <label for="occupation" class="form-label fw-semibold">Occupation</label>
                    <input type="text" name="occupation" id="occupation"
                        class="form-control @error('occupation') is-invalid @enderror"
                        placeholder="Software Engineer, Teacher…"
                        value="{{ old('occupation', $profile?->occupation ?? '') }}">
                    @error('occupation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Ethnicity --}}
                <div class="col-sm-6">
                    <label for="ethnicity" class="form-label fw-semibold">Ethnicity <span class="text-muted fw-normal">(optional)</span></label>
                    <input type="text" name="ethnicity" id="ethnicity"
                        class="form-control @error('ethnicity') is-invalid @enderror"
                        placeholder="Optional"
                        value="{{ old('ethnicity', $profile?->ethnicity ?? '') }}">
                    @error('ethnicity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Religion --}}
                <div class="col-sm-6">
                    <label for="religion" class="form-label fw-semibold">Religion <span class="text-muted fw-normal">(optional)</span></label>
                    <input type="text" name="religion" id="religion"
                        class="form-control @error('religion') is-invalid @enderror"
                        placeholder="Optional"
                        value="{{ old('religion', $profile?->religion ?? '') }}">
                    @error('religion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Smoking --}}
                <div class="col-sm-6">
                    <label for="smoking" class="form-label fw-semibold">Smoking</label>
                    <select name="smoking" id="smoking" class="form-select @error('smoking') is-invalid @enderror">
                        <option value="">Select…</option>
                        @foreach(['never'=>'Never','occasionally'=>'Occasionally','regularly'=>'Regularly','trying_to_quit'=>'Trying to quit'] as $v=>$l)
                        <option value="{{ $v }}" {{ old('smoking', $profile?->smoking ?? '') === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                    @error('smoking')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Drinking --}}
                <div class="col-sm-6">
                    <label for="drinking" class="form-label fw-semibold">Drinking</label>
                    <select name="drinking" id="drinking" class="form-select @error('drinking') is-invalid @enderror">
                        <option value="">Select…</option>
                        @foreach(['never'=>'Never','socially'=>'Socially','regularly'=>'Regularly'] as $v=>$l)
                        <option value="{{ $v }}" {{ old('drinking', $profile?->drinking ?? '') === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                    @error('drinking')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Has Children --}}
                <div class="col-sm-6">
                    <label class="form-label fw-semibold d-block">Have children?</label>
                    @php $hasChildrenVal = (string) old('has_children', $profile?->has_children ?? ''); @endphp
                    <div class="d-flex gap-2">
                        <input type="radio" class="btn-check" name="has_children" id="hc_yes" value="1" autocomplete="off"
                            {{ $hasChildrenVal === '1' ? 'checked' : '' }}>
                        <label class="btn btn-outline-primary px-4" for="hc_yes">Yes</label>

                        <input type="radio" class="btn-check" name="has_children" id="hc_no" value="0" autocomplete="off"
                            {{ $hasChildrenVal === '0' ? 'checked' : '' }}>
                        <label class="btn btn-outline-primary px-4" for="hc_no">No</label>
                    </div>
                    @error('has_children')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                {{-- Wants Children --}}
                <div class="col-sm-6">
                    <label for="wants_children" class="form-label fw-semibold">Want children?</label>
                    <select name="wants_children" id="wants_children" class="form-select @error('wants_children') is-invalid @enderror">
                        <option value="">Select…</option>
                        @foreach(['yes'=>'Yes','no'=>'No','open'=>'Open','not_sure'=>'Not sure'] as $v=>$l)
                        <option value="{{ $v }}" {{ old('wants_children', $profile?->wants_children ?? '') === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                    @error('wants_children')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>{{-- end row --}}

            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('setup.step', 1) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
                <button type="submit" class="btn btn-primary px-4 fw-bold">
                    Continue <i class="bi bi-arrow-right ms-1"></i>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
