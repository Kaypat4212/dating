@extends('layouts.app')
@section('title', 'Icebreakers')
@section('content')
<div class="container py-4" style="max-width:900px;">
    <div class="mb-4">
        <h2 class="fw-bold mb-0"><i class="bi bi-snow2 text-primary me-2"></i>Icebreakers</h2>
        <p class="text-muted small">Answer fun questions that show on your profile and help start conversations.</p>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    @php
        $types = [
            'would_you_rather' => ['label' => 'Would You Rather?', 'icon' => 'bi-shuffle', 'color' => 'text-primary'],
            'two_truths_lie'   => ['label' => 'Two Truths & a Lie', 'icon' => 'bi-shield-exclamation', 'color' => 'text-warning'],
            'this_or_that'     => ['label' => 'This or That', 'icon' => 'bi-toggles', 'color' => 'text-success'],
            'open_ended'       => ['label' => 'Open-Ended', 'icon' => 'bi-pencil', 'color' => 'text-info'],
        ];
    @endphp

    @foreach($types as $type => $meta)
    @php $typeQuestions = $questions->where('type', $type); @endphp
    @if($typeQuestions->isNotEmpty())
    <div class="mb-4">
        <h6 class="fw-semibold text-uppercase small text-muted mb-3">
            <i class="bi {{ $meta['icon'] }} {{ $meta['color'] }} me-2"></i>{{ $meta['label'] }}
        </h6>
        <div class="row g-3">
            @foreach($typeQuestions as $q)
            @php $ans = $myAnswers[$q->id] ?? null; @endphp
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100 {{ $ans ? 'border-start border-success border-3' : '' }}">
                    <div class="card-body">
                        <p class="fw-semibold mb-3">{{ $q->question }}</p>

                        @if(in_array($type, ['would_you_rather', 'this_or_that']) && $q->option_a && $q->option_b)
                        <form action="{{ route('icebreaker.answer') }}" method="POST" class="d-flex gap-2">
                            @csrf
                            <input type="hidden" name="question_id" value="{{ $q->id }}">
                            <button type="submit" name="choice" value="a"
                                    class="btn btn-sm flex-grow-1 {{ $ans?->choice === 'a' ? 'btn-primary' : 'btn-outline-primary' }}">
                                {{ $q->option_a }}
                            </button>
                            <span class="text-muted align-self-center fw-bold">or</span>
                            <button type="submit" name="choice" value="b"
                                    class="btn btn-sm flex-grow-1 {{ $ans?->choice === 'b' ? 'btn-danger' : 'btn-outline-danger' }}">
                                {{ $q->option_b }}
                            </button>
                        </form>

                        @elseif($type === 'two_truths_lie')
                        <form action="{{ route('icebreaker.answer') }}" method="POST">
                            @csrf
                            <input type="hidden" name="question_id" value="{{ $q->id }}">
                            <textarea name="answer" class="form-control form-control-sm mb-2" rows="3"
                                      placeholder="Write your 2 truths and 1 lie (label which is the lie!)..." maxlength="500">{{ $ans?->answer }}</textarea>
                            <button type="submit" class="btn btn-sm btn-primary w-100">Save</button>
                        </form>

                        @else
                        <form action="{{ route('icebreaker.answer') }}" method="POST">
                            @csrf
                            <input type="hidden" name="question_id" value="{{ $q->id }}">
                            <textarea name="answer" class="form-control form-control-sm mb-2" rows="2"
                                      placeholder="Your answer..." maxlength="500">{{ $ans?->answer }}</textarea>
                            <button type="submit" class="btn btn-sm btn-primary w-100">Save</button>
                        </form>
                        @endif

                        @if($ans)
                        <div class="d-flex align-items-center justify-content-between mt-2">
                            <small class="text-success"><i class="bi bi-check-circle me-1"></i>Answered</small>
                            <form action="{{ route('icebreaker.answer.destroy', $ans->id) }}" method="POST" style="display:inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" style="font-size:.72rem"
                                        onclick="return confirm('Remove this answer from your profile?')">
                                    <i class="bi bi-trash me-1"></i>Remove
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
    @endforeach

    @if($questions->isEmpty())
    <div class="text-center py-5 text-muted">
        <i class="bi bi-snow2 fs-1"></i>
        <p class="mt-2">Icebreaker questions are being prepared. Check back soon!</p>
    </div>
    @endif
</div>
@endsection
