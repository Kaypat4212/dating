@extends('layouts.app')
@section('title', 'Setup — Step 1 of 5')
@section('content')
<div class="container py-5" style="max-width:640px">

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
            <div class="display-5 mb-2">👋</div>
            <h3 class="fw-bold">Tell us about yourself</h3>
            <p class="text-muted">This helps us find your best matches.</p>
        </div>

        <form method="POST" action="{{ route('setup.store', ['step' => 1]) }}">
            @csrf

            {{-- Gender --}}
            <div class="mb-4">
                <label class="form-label fw-semibold">I am a</label>
                <div class="d-flex flex-wrap gap-2">
                    @foreach(['male' => '👨 Man', 'female' => '👩 Woman', 'non_binary' => '🧑 Non-binary', 'other' => '🌈 Other'] as $val => $label)
                    <label class="flex-fill text-center px-3 py-2 rounded-3 border cursor-pointer gender-opt {{ old('gender', $user->gender) === $val ? 'border-primary bg-primary bg-opacity-10' : '' }}" style="cursor:pointer">
                        <input type="radio" name="gender" value="{{ $val }}" class="d-none gender-radio" {{ old('gender', $user->gender) === $val ? 'checked' : '' }}>
                        {{ $label }}
                    </label>
                    @endforeach
                </div>
                @error('gender')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>

            {{-- Seeking --}}
            <div class="mb-4">
                <label class="form-label fw-semibold">Looking for</label>
                <div class="d-flex flex-wrap gap-2">
                    @foreach(['male' => '👨 Men', 'female' => '👩 Women', 'everyone' => '❤️ Everyone'] as $val => $label)
                    <label class="flex-fill text-center px-3 py-2 rounded-3 border cursor-pointer seek-opt {{ old('seeking', $user->seeking) === $val ? 'border-primary bg-primary bg-opacity-10' : '' }}" style="cursor:pointer">
                        <input type="radio" name="seeking" value="{{ $val }}" class="d-none seek-radio" {{ old('seeking', $user->seeking) === $val ? 'checked' : '' }}>
                        {{ $label }}
                    </label>
                    @endforeach
                </div>
                @error('seeking')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>

            {{-- Date of Birth --}}
            <div class="mb-4">
                <label for="date_of_birth" class="form-label fw-semibold">Date of Birth <span class="text-muted fw-normal">(must be 18+)</span></label>
                <input type="date" name="date_of_birth" id="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror"
                    value="{{ old('date_of_birth', optional($user->date_of_birth)->format('Y-m-d')) }}"
                    max="{{ now()->subYears(18)->format('Y-m-d') }}" required>
                @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                Continue <i class="bi bi-arrow-right ms-1"></i>
            </button>
        </form>
    </div>
</div>

<script>
document.querySelectorAll('.gender-radio').forEach(r => r.addEventListener('change', () => {
    document.querySelectorAll('.gender-opt').forEach(l => l.classList.remove('border-primary','bg-primary','bg-opacity-10'));
    r.closest('.gender-opt').classList.add('border-primary','bg-primary','bg-opacity-10');
}));
document.querySelectorAll('.seek-radio').forEach(r => r.addEventListener('change', () => {
    document.querySelectorAll('.seek-opt').forEach(l => l.classList.remove('border-primary','bg-primary','bg-opacity-10'));
    r.closest('.seek-opt').classList.add('border-primary','bg-primary','bg-opacity-10');
}));
</script>

@if(session('just_registered'))
{{-- Welcome Modal --}}
<div class="modal fade" id="welcomeModal" tabindex="-1" aria-labelledby="welcomeModalLabel" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 text-center p-4 p-md-5">
            <div style="font-size:4rem;line-height:1">🎉</div>
            <h4 class="fw-bold mt-3 mb-2" id="welcomeModalLabel">Welcome to HeartsConnect!</h4>
            <p class="text-muted mb-4">
                Your account has been created successfully.<br>
                Let's set up your profile to find your perfect match!
            </p>
            <button type="button" class="btn btn-primary px-5 py-2 fw-bold rounded-pill mx-auto" data-bs-dismiss="modal">
                <i class="bi bi-hearts me-2"></i>Get Started
            </button>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var welcomeModal = new bootstrap.Modal(document.getElementById('welcomeModal'), { backdrop: 'static', keyboard: false });
    welcomeModal.show();
});
</script>
@endif

@endsection
