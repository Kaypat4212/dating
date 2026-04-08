@extends('layouts.app')
@section('title', 'Badges & Achievements')

@section('content')
<div class="container py-4">
<div class="row justify-content-center">
<div class="col-lg-8">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0"><i class="bi bi-trophy-fill text-warning me-2"></i>Badges & Achievements</h4>
            <div class="text-muted small">Track your milestones — pin up to 3 badges to your profile</div>
        </div>
        <div>
            <span class="badge bg-primary fs-6">{{ $earned->count() }} / {{ $all->count() }} earned</span>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible rounded-4 small py-2 px-3 mb-3">
        {{ session('success') }}
        <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Progress bar --}}
    @php $pct = $all->count() ? round($earned->count() / $all->count() * 100) : 0; @endphp
    <div class="card border-0 shadow-sm rounded-4 mb-4 p-3">
        <div class="d-flex justify-content-between small fw-semibold mb-1">
            <span>Overall Progress</span><span>{{ $pct }}%</span>
        </div>
        <div class="progress rounded-pill" style="height:10px">
            <div class="progress-bar bg-warning" style="width:{{ $pct }}%"></div>
        </div>
    </div>

    @foreach($all->groupBy('category') as $category => $badges)
    <div class="mb-4">
        <h6 class="text-uppercase text-muted fw-bold small mb-3 ps-1">
            @if($category === 'streak') 🔥 Streak
            @elseif($category === 'social') ❤️ Social
            @elseif($category === 'profile') ✅ Profile
            @elseif($category === 'premium') 💎 Premium
            @else 🏅 General
            @endif
        </h6>
        <div class="row g-3">
        @foreach($badges as $badge)
        @php $isEarned = $earned->has($badge->id); $isPinned = $isEarned && $earned[$badge->id]->pivot->is_pinned; @endphp
        <div class="col-6 col-sm-4 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-center p-3 h-100 {{ $isEarned ? '' : 'opacity-50' }}"
                 style="{{ $isEarned ? 'background:linear-gradient(135deg,#fff9f0,#fff)' : '' }}">
                <div style="font-size:2.5rem;line-height:1.2">{{ $badge->emoji }}</div>
                <div class="fw-semibold small mt-2">{{ $badge->name }}</div>
                <div class="text-muted" style="font-size:.72rem">{{ $badge->description }}</div>
                @if($isEarned)
                    <div class="text-success small mt-1" style="font-size:.7rem">
                        <i class="bi bi-check-circle-fill me-1"></i>{{ \Carbon\Carbon::parse($earned[$badge->id]->pivot->earned_at)->format('M j, Y') }}
                    </div>
                    <form method="POST" action="{{ route('badges.pin', $badge) }}" class="mt-2">
                        @csrf
                        <button type="submit" class="btn btn-sm w-100 {{ $isPinned ? 'btn-warning' : 'btn-outline-secondary' }}"
                                style="font-size:.72rem;padding:.25rem .5rem">
                            {{ $isPinned ? '📌 Pinned' : 'Pin to Profile' }}
                        </button>
                    </form>
                @else
                    <div class="text-muted mt-2" style="font-size:.7rem"><i class="bi bi-lock-fill me-1"></i>Not earned yet</div>
                @endif
            </div>
        </div>
        @endforeach
        </div>
    </div>
    @endforeach

</div>
</div>
</div>
@endsection
