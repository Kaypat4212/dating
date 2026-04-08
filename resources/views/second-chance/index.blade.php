@extends('layouts.app')
@section('title', 'Second Chance Queue')

@push('head')
<style>
    .sc-hero {
        background: linear-gradient(135deg, #f59e0b 0%, #ef4444 60%, #ec4899 100%);
        border-radius: 1.25rem;
        color: #fff;
        padding: 2rem 1.5rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }
    .sc-hero::before {
        content: '🔄';
        font-size: 7rem;
        position: absolute;
        right: 1rem; top: -.5rem;
        opacity: .15; pointer-events: none;
    }
    .sc-card {
        border: none;
        border-radius: 1rem;
        overflow: hidden;
        transition: transform .2s, box-shadow .2s;
        box-shadow: 0 2px 12px rgba(0,0,0,.08);
    }
    .sc-card:hover { transform: translateY(-4px); box-shadow: 0 8px 28px rgba(239,68,68,.18); }
    .sc-card .photo-wrap {
        position: relative; aspect-ratio: 1/1;
        overflow: hidden; background: #f0f0f0;
    }
    .sc-card .photo-wrap img {
        width: 100%; height: 100%; object-fit: cover;
        transition: transform .35s;
    }
    .sc-card:hover .photo-wrap img { transform: scale(1.06); }
    .empty-state { padding: 4rem 1rem; text-align: center; }
    .empty-icon { font-size: 4.5rem; margin-bottom: 1rem; }
</style>
@endpush

@section('content')
<div class="container py-4">

    <div class="sc-hero">
        <h3 class="fw-bold mb-1"><i class="bi bi-arrow-repeat me-2"></i>Second Chance Queue</h3>
        <p class="mb-0 opacity-80">Profiles you passed over 30 days ago — maybe it's time for a second look?</p>
    </div>

    @if($passes->isEmpty())
    <div class="empty-state">
        <div class="empty-icon">🔄</div>
        <h5 class="fw-semibold mb-1">No second chances yet</h5>
        <p class="text-muted mb-4">Profiles you pass will resurface here after 30 days. Keep swiping!</p>
        <a href="{{ route('discover.index') }}" class="btn btn-primary rounded-pill px-5">
            <i class="bi bi-search-heart me-1"></i>Discover People
        </a>
    </div>
    @else
    <div class="row g-3 mb-4">
        @foreach($passes as $pass)
        @php $person = $pass->passed; @endphp
        @if(!$person) @continue @endif
        <div class="col-6 col-md-4 col-lg-3">
            <div class="sc-card card">
                <div class="photo-wrap">
                    @if($person->primaryPhoto)
                    <img src="{{ $person->primaryPhoto->url }}" alt="{{ $person->name }}">
                    @else
                    <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light">
                        <i class="bi bi-person-circle text-muted" style="font-size:3rem"></i>
                    </div>
                    @endif
                </div>
                <div class="card-body p-3">
                    <div class="fw-semibold mb-1">{{ $person->name }}, {{ $person->age }}</div>
                    @if($person->profile?->city)
                    <div class="text-muted small mb-2"><i class="bi bi-geo-alt me-1"></i>{{ $person->profile->city }}</div>
                    @endif
                    <div class="d-flex gap-2">
                        <form method="POST" action="{{ route('like.store', $person->id) }}" class="flex-grow-1">
                            @csrf
                            <button class="btn btn-primary btn-sm w-100 rounded-pill">
                                <i class="bi bi-heart me-1"></i>Like
                            </button>
                        </form>
                        <a href="{{ route('profile.show', $person->username) }}" class="btn btn-outline-secondary btn-sm rounded-pill" title="View profile">
                            <i class="bi bi-eye"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{ $passes->links() }}
    @endif

</div>
@endsection
