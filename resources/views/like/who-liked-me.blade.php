@extends('layouts.app')
@section('title', 'Who Liked You')

@push('head')
<style>
    .like-hero {
        background: linear-gradient(135deg, #ff6b9d 0%, #c44ee0 50%, #7b2ff7 100%);
        border-radius: 1.25rem;
        color: #fff;
        padding: 2rem 1.5rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }
    .like-hero::before {
        content: '❤️';
        font-size: 8rem;
        position: absolute;
        right: -1rem;
        top: -1.5rem;
        opacity: .12;
        pointer-events: none;
    }
    .liker-card {
        border: none;
        border-radius: 1rem;
        overflow: hidden;
        transition: transform .2s, box-shadow .2s;
        box-shadow: 0 2px 12px rgba(0,0,0,.08);
    }
    .liker-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 28px rgba(196,78,224,.2);
    }
    .liker-card .photo-wrap {
        position: relative;
        aspect-ratio: 1/1;
        overflow: hidden;
        background: #f0f0f0;
    }
    .liker-card .photo-wrap img {
        width: 100%; height: 100%;
        object-fit: cover;
        transition: transform .35s;
    }
    .liker-card:hover .photo-wrap img {
        transform: scale(1.06);
    }
    .liker-card .photo-wrap .super-badge {
        position: absolute;
        top: .5rem; left: .5rem;
        background: linear-gradient(135deg,#ffd700,#ff9900);
        color: #fff;
        font-size: .65rem;
        font-weight: 700;
        padding: .22rem .55rem;
        border-radius: 2rem;
        letter-spacing: .04em;
        box-shadow: 0 2px 6px rgba(0,0,0,.2);
    }
    .liker-card .photo-wrap .time-badge {
        position: absolute;
        bottom: .5rem; right: .5rem;
        background: rgba(0,0,0,.55);
        color: #fff;
        font-size: .65rem;
        padding: .2rem .45rem;
        border-radius: .75rem;
        backdrop-filter: blur(4px);
    }
    .blur-premium {
        filter: blur(14px);
        transform: scale(1.05);
        pointer-events: none;
    }
    .premium-overlay {
        position: absolute; inset: 0;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        gap: .5rem;
        background: rgba(123,47,247,.18);
        backdrop-filter: blur(2px);
    }
    .empty-state { padding: 4rem 1rem; text-align: center; }
    .empty-icon { font-size: 4.5rem; margin-bottom: 1rem; animation: heartbeat 2s infinite; }
    @keyframes heartbeat {
        0%,100% { transform: scale(1); }
        15% { transform: scale(1.15); }
        30% { transform: scale(1); }
        45% { transform: scale(1.1); }
        60% { transform: scale(1); }
    }
</style>
@endpush

@section('content')
<div class="container py-4">

    {{-- Hero banner --}}
    <div class="like-hero">
        <h3 class="fw-bold mb-1">
            <i class="bi bi-heart-fill me-2"></i>Who Liked You
        </h3>
        <p class="mb-0 opacity-75 fs-6">
            @if($likers->total() > 0)
                <strong>{{ $likers->total() }}</strong> {{ Str::plural('person', $likers->total()) }} have expressed interest in you.
            @else
                Nobody yet — keep discovering new people!
            @endif
        </p>
        @if(!$isPremium)
        <a href="{{ route('premium.show') }}"
           class="btn btn-warning btn-sm fw-semibold mt-3 rounded-pill px-4 shadow-sm">
            <i class="bi bi-star-fill me-1"></i>Upgrade to see who liked you
        </a>
        @endif
    </div>

    @if($likers->isEmpty())
        {{-- Empty state --}}
        <div class="empty-state">
            <div class="empty-icon">💝</div>
            <h5 class="fw-semibold mb-1">No likes yet</h5>
            <p class="text-muted mb-4">Complete your profile and keep swiping — someone special will find you!</p>
            <a href="{{ route('discover.index') }}" class="btn btn-primary rounded-pill px-5">
                <i class="bi bi-search-heart me-1"></i>Discover People
            </a>
        </div>
    @else
        <div class="row g-3">
            @foreach($likers as $like)
                @php $liker = $like->sender @endphp
                @if(!$liker || !$liker->username) @continue @endif

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card liker-card h-100">

                        {{-- Photo --}}
                        <div class="photo-wrap">
                            @if($liker->primaryPhoto)
                                <img
                                    src="{{ $liker->primaryPhoto->thumbnail_url }}"
                                    alt="{{ $isPremium ? $liker->name : 'Hidden' }}"
                                    class="{{ $isPremium ? '' : 'blur-premium' }}"
                                    loading="lazy">
                            @else
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light" style="aspect-ratio:1/1">
                                    <i class="bi bi-person-circle display-4 text-muted"></i>
                                </div>
                            @endif

                            @if($like->is_super_like)
                                <span class="super-badge"><i class="bi bi-star-fill me-1"></i>Super Like</span>
                            @endif

                            @if($isPremium)
                                <span class="time-badge">{{ $like->created_at->diffForHumans() }}</span>
                            @else
                                <div class="premium-overlay">
                                    <i class="bi bi-lock-fill text-white fs-3"></i>
                                    <a href="{{ route('premium.show') }}"
                                       class="btn btn-warning btn-sm fw-bold rounded-pill px-3"
                                       style="font-size:.7rem">Unlock</a>
                                </div>
                            @endif
                        </div>

                        {{-- Body --}}
                        <div class="card-body p-2 pb-1">
                            @if($isPremium)
                                <div class="fw-semibold text-truncate" style="font-size:.9rem">
                                    {{ $liker->name }}@if($liker->age), {{ $liker->age }}@endif
                                </div>
                                @if($liker->profile && $liker->profile->city)
                                <div class="text-muted" style="font-size:.7rem">
                                    <i class="bi bi-geo-alt"></i> {{ $liker->profile->city }}
                                </div>
                                @endif
                            @else
                                <div class="fw-semibold text-muted text-truncate" style="font-size:.9rem">
                                    ✨ Premium member
                                </div>
                                <div class="text-muted" style="font-size:.7rem">Upgrade to reveal</div>
                            @endif
                        </div>

                        {{-- Footer --}}
                        <div class="card-footer bg-transparent p-2 pt-0 d-flex gap-1">
                            @if($isPremium)
                                @if($liker->username)
                                <a href="{{ route('profile.show', $liker->username) }}"
                                   class="btn btn-outline-secondary btn-sm flex-fill">
                                    <i class="bi bi-person"></i>
                                </a>
                                @endif
                                <form method="POST" action="{{ route('like.store', $liker->id) }}" class="flex-fill">
                                    @csrf
                                    <button class="btn btn-primary btn-sm w-100">
                                        <i class="bi bi-heart-fill"></i>
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('premium.show') }}"
                                   class="btn btn-warning btn-sm w-100 fw-bold">
                                    <i class="bi bi-star-fill me-1"></i>See Who
                                </a>
                            @endif
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($likers->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $likers->links('pagination::bootstrap-5') }}
        </div>
        @endif
    @endif

</div>
@endsection
