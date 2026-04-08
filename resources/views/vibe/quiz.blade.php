@extends('layouts.app')
@section('title', 'Vibe Check Quiz')

@push('head')
<style>
    .vibe-hero {
        background: linear-gradient(135deg, #7b2ff7 0%, #c44ee0 60%, #ff6b9d 100%);
        border-radius: 1.25rem;
        color: #fff;
        padding: 2rem 1.5rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }
    .vibe-hero::before {
        content: '✨';
        font-size: 7rem;
        position: absolute;
        right: 1rem; top: -1rem;
        opacity: .15; pointer-events: none;
    }
    .quiz-card {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 2px 12px rgba(0,0,0,.07);
    }
    .answer-btn {
        width: 100%;
        text-align: left;
        padding: .85rem 1.25rem;
        border: 2px solid #dee2e6;
        border-radius: .75rem;
        background: #fff;
        font-size: .95rem;
        transition: border-color .2s, background .2s, transform .15s;
        cursor: pointer;
    }
    .answer-btn:hover, .answer-btn.selected {
        border-color: #7b2ff7;
        background: #f5f0ff;
        transform: translateX(3px);
    }
    .answer-btn.selected {
        font-weight: 600;
        color: #5b21b6;
    }
    .question-step { display: none; }
    .question-step.active { display: block; }
    .progress-dots { display: flex; gap: .5rem; justify-content: center; margin-bottom: 1.5rem; }
    .progress-dots span {
        width: 10px; height: 10px; border-radius: 50%;
        background: #dee2e6; transition: background .3s;
    }
    .progress-dots span.done { background: #7b2ff7; }
    .vibe-result-card {
        border: none;
        border-radius: 1.25rem;
        background: linear-gradient(135deg, #f5f0ff 0%, #fdf4ff 100%);
        padding: 2rem;
        text-align: center;
        box-shadow: 0 4px 24px rgba(123,47,247,.12);
    }
    .vibe-emoji { font-size: 4rem; }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-7">

            <div class="vibe-hero">
                <h3 class="fw-bold mb-1"><i class="bi bi-stars me-2"></i>Vibe Check Quiz</h3>
                <p class="mb-0 opacity-80">5 quick questions to discover your relationship vibe — shown on your profile!</p>
            </div>

            {{-- Flash result --}}
            @if(session('vibe_result'))
            @php
                $r = session('vibe_result');
                $meta = $vibeMeta[$r] ?? ['emoji' => '🌟', 'label' => ucfirst($r), 'desc' => ''];
            @endphp
            <div class="vibe-result-card mb-4">
                <div class="vibe-emoji mb-2">{{ $meta['emoji'] }}</div>
                <h4 class="fw-bold mb-1">Your Vibe: {{ $meta['label'] }}</h4>
                <p class="text-muted mb-3">{{ $meta['desc'] }}</p>
                <a href="{{ route('profile.edit') }}" class="btn btn-primary rounded-pill px-4">
                    <i class="bi bi-person-check me-1"></i>View on Your Profile
                </a>
            </div>
            @endif

            {{-- Current vibe badge if exists --}}
            @if($existing && !session('vibe_result'))
            @php $m = $vibeMeta[$existing] ?? ['emoji' => '🌟', 'label' => ucfirst($existing), 'desc' => '']; @endphp
            <div class="alert alert-light border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center gap-3">
                <span style="font-size:2rem">{{ $m['emoji'] }}</span>
                <div>
                    <div class="fw-semibold">Your current vibe: <strong>{{ $m['label'] }}</strong></div>
                    <div class="small text-muted">Retake the quiz to update your vibe anytime.</div>
                </div>
            </div>
            @endif

            {{-- Quiz form --}}
            <div class="quiz-card card mb-4">
                <div class="card-body p-4">
                    <div class="progress-dots">
                        @foreach($questions as $i => $q)
                        <span id="dot-{{ $i }}" class="{{ $i === 0 ? 'done' : '' }}"></span>
                        @endforeach
                    </div>

                    <form id="vibeForm" method="POST" action="{{ route('vibe.submit') }}">
                        @csrf
                        @foreach($questions as $i => $q)
                        <div class="question-step {{ $i === 0 ? 'active' : '' }}" id="step-{{ $i }}">
                            <p class="fw-semibold fs-5 mb-3">Q{{ $i + 1 }}. {{ $q['text'] }}</p>
                            <div class="d-flex flex-column gap-2">
                                @foreach($q['answers'] as $key => $ans)
                                <button type="button"
                                    class="answer-btn"
                                    data-step="{{ $i }}"
                                    data-answer="{{ $key }}">
                                    {{ $ans['label'] }}
                                    <input type="hidden" name="answers[{{ $i }}]" value="" disabled>
                                </button>
                                @endforeach
                            </div>
                        </div>
                        @endforeach

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" id="prevBtn" class="btn btn-outline-secondary rounded-pill px-4" style="display:none" onclick="changeStep(-1)">
                                <i class="bi bi-arrow-left me-1"></i> Back
                            </button>
                            <button type="button" id="nextBtn" class="btn btn-primary rounded-pill px-4 ms-auto" onclick="changeStep(1)" disabled>
                                Next <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                            <button type="submit" id="submitBtn" class="btn btn-success rounded-pill px-4 ms-auto" style="display:none" disabled>
                                <i class="bi bi-check-circle me-1"></i>Get My Vibe!
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
let currentStep = 0;
const totalSteps = {{ count($questions) }};
const answers = {};

document.querySelectorAll('.answer-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const step = parseInt(this.dataset.step);
        const answer = this.dataset.answer;

        // Deselect all in this step
        document.querySelectorAll(`.answer-btn[data-step="${step}"]`).forEach(b => b.classList.remove('selected'));
        this.classList.add('selected');

        // Store answer in hidden inputs
        answers[step] = answer;
        document.querySelectorAll(`#step-${step} input[type="hidden"]`).forEach(inp => inp.value = answer);
        document.querySelectorAll(`#step-${step} input[type="hidden"]`).forEach(inp => inp.disabled = false);

        // Enable next/submit
        if (currentStep < totalSteps - 1) {
            document.getElementById('nextBtn').disabled = false;
        } else {
            document.getElementById('submitBtn').disabled = false;
        }
    });
});

function changeStep(dir) {
    const steps = document.querySelectorAll('.question-step');
    const dots  = document.querySelectorAll('.progress-dots span');

    steps[currentStep].classList.remove('active');
    currentStep += dir;
    steps[currentStep].classList.add('active');

    // Update dots
    dots.forEach((d, i) => d.classList.toggle('done', i <= currentStep));

    // Restore button state for this step
    const hasAnswer = answers[currentStep] !== undefined;
    document.getElementById('nextBtn').disabled = !hasAnswer;
    document.getElementById('submitBtn').disabled = !hasAnswer;

    // Toggle buttons
    document.getElementById('prevBtn').style.display = currentStep > 0 ? '' : 'none';
    document.getElementById('nextBtn').style.display = currentStep < totalSteps - 1 ? '' : 'none';
    document.getElementById('submitBtn').style.display = currentStep === totalSteps - 1 ? '' : 'none';
}
</script>
@endpush
@endsection
