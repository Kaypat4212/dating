@extends('layouts.app')
@section('title', 'Question of the Day')

@push('head')
<style>
    .qotd-hero {
        background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 60%, #8b5cf6 100%);
        border-radius: 1.25rem;
        color: #fff;
        padding: 2rem 1.5rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }
    .qotd-hero::before {
        content: '💬';
        font-size: 7rem;
        position: absolute;
        right: 1rem; top: -.5rem;
        opacity: .15; pointer-events: none;
    }
    .match-qotd-card {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 2px 12px rgba(0,0,0,.07);
        overflow: hidden;
    }
    .answer-area {
        background: #f8f9fa;
        border-radius: .75rem;
        padding: 1rem;
        font-style: italic;
        color: #374151;
    }
    .partner-answer {
        background: linear-gradient(135deg,#f0fdf4,#dcfce7);
        border-left: 3px solid #22c55e;
        border-radius: .75rem;
        padding: 1rem;
        font-style: italic;
    }
    .empty-state { padding: 4rem 1rem; text-align: center; }
    .empty-icon { font-size: 4.5rem; margin-bottom: 1rem; }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="qotd-hero">
                <h3 class="fw-bold mb-1"><i class="bi bi-calendar-heart me-2"></i>Question of the Day</h3>
                <p class="mb-0 opacity-80">Answer today's prompt with each of your matches — see if you think alike!</p>
            </div>

            @if(!$question)
            <div class="empty-state">
                <div class="empty-icon">🤔</div>
                <h5 class="fw-semibold">No questions available yet</h5>
                <p class="text-muted">Check back soon!</p>
            </div>
            @else

            {{-- Today's question --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body p-4">
                    <div class="text-muted small mb-2"><i class="bi bi-calendar3 me-1"></i>{{ now()->format('F j, Y') }}</div>
                    <h5 class="fw-bold mb-0">"{{ $question->question }}"</h5>
                </div>
            </div>

            @if($matches->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">💞</div>
                <h5 class="fw-semibold mb-1">No matches yet</h5>
                <p class="text-muted mb-4">Match with someone to start answering the daily question together!</p>
                <a href="{{ route('discover.index') }}" class="btn btn-primary rounded-pill px-5">
                    <i class="bi bi-compass me-1"></i>Discover People
                </a>
            </div>
            @else

            @foreach($matches as $match)
            @php $partner = $match->getOtherUser(auth()->id()); @endphp
            <div class="match-qotd-card card mb-4">
                <div class="card-header bg-transparent d-flex align-items-center gap-3 py-3">
                    @if($partner->primaryPhoto)
                    <img src="{{ $partner->primaryPhoto->url }}" class="rounded-circle" width="42" height="42" style="object-fit:cover" alt="{{ $partner->name }}">
                    @else
                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width:42px;height:42px"><i class="bi bi-person text-muted fs-5"></i></div>
                    @endif
                    <div>
                        <div class="fw-semibold">{{ $partner->name }}</div>
                        <div class="text-muted small">Matched {{ $match->matched_at?->diffForHumans() }}</div>
                    </div>
                </div>
                <div class="card-body p-4">
                    @php
                        $myAnswer      = $myAnswers[$match->id] ?? null;
                        $partnerAnswer = $partnerAnswers[$match->id] ?? null;
                    @endphp

                    {{-- My answer --}}
                    @if($myAnswer)
                    <div class="mb-3">
                        <div class="text-muted small mb-1"><i class="bi bi-person me-1"></i>Your answer:</div>
                        <div class="answer-area">"{{ $myAnswer }}"</div>
                    </div>
                    @else
                    <form class="qotd-form mb-3" data-match="{{ $match->id }}" data-question="{{ $question->id }}">
                        @csrf
                        <label class="form-label small text-muted">Your answer:</label>
                        <textarea name="answer" class="form-control mb-2" rows="2" maxlength="500" placeholder="Type your answer…" required></textarea>
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3">
                            <i class="bi bi-send me-1"></i>Submit Answer
                        </button>
                    </form>
                    @endif

                    {{-- Partner's answer --}}
                    @if($partnerAnswer)
                    <div>
                        <div class="text-muted small mb-1"><i class="bi bi-person-heart me-1"></i>{{ $partner->name }}'s answer:</div>
                        <div class="partner-answer">"{{ $partnerAnswer }}"</div>
                    </div>
                    @elseif($myAnswer)
                    <div class="text-muted small fst-italic">
                        <i class="bi bi-hourglass-split me-1"></i>Waiting for {{ $partner->name }}'s answer…
                    </div>
                    @endif
                </div>
            </div>
            @endforeach

            @endif
            @endif

        </div>
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('.qotd-form').forEach(form => {
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = this.querySelector('button[type="submit"]');
        btn.disabled = true;

        const resp = await fetch('{{ route('match-question.answer') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                match_id:    this.dataset.match,
                question_id: this.dataset.question,
                answer:      this.querySelector('textarea').value,
            }),
        });

        if (resp.ok) {
            const answer = this.querySelector('textarea').value;
            const wrapper = document.createElement('div');
            wrapper.className = 'mb-3';
            wrapper.innerHTML = `
                <div class="text-muted small mb-1"><i class="bi bi-person me-1"></i>Your answer:</div>
                <div class="answer-area">"${answer}"</div>
            `;
            this.replaceWith(wrapper);
        } else {
            btn.disabled = false;
            alert('Failed to save — please try again.');
        }
    });
});
</script>
@endpush
@endsection
