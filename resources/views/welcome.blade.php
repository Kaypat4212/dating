@extends('layouts.app')

@section('title', 'Find Your Forever Person')

@php
    use App\Models\SiteSetting as SS;
@endphp

@push('head')
<style>
    [data-bs-theme="dark"] .home-stats {
        background: var(--bs-body-bg) !important;
        border-color: var(--bs-border-color) !important;
    }

    [data-bs-theme="dark"] .home-features {
        background: var(--bs-tertiary-bg) !important;
    }

    [data-bs-theme="dark"] .hero-profile-card {
        background: var(--bs-body-bg);
        color: var(--bs-body-color);
    }
</style>
@endpush

@section('content')

{{-- Hero --}}
<section class="text-white py-5" style="min-height:85vh;display:flex;align-items:center;background:linear-gradient(135deg,#c2185b 0%,#7b1fa2 100%);">
    <div class="container py-5">
        <div class="row align-items-center gy-5">
            <div class="col-lg-6" data-aos="fade-right">
                <h1 class="display-3 fw-bold mb-3">
                    {!! nl2br(e(SS::get('hero_heading', 'Find Love That Lasts'))) !!}
                </h1>
                <p class="lead mb-4 opacity-90">
                    {{ SS::get('hero_subtext', 'Join thousands of singles finding meaningful connections. Browse profiles, swipe, match, and start chatting — for free.') }}
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-warning btn-lg fw-bold px-4 shadow"><i class="bi bi-house-heart me-2"></i>Go to Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">@csrf<button type="submit" class="btn btn-outline-light btn-lg px-4"><i class="bi bi-box-arrow-right me-2"></i>Log Out</button></form>
                    @else
                    <a href="{{ route('register') }}" class="btn btn-warning btn-lg fw-bold px-4 shadow"><i class="bi bi-hearts me-2"></i>{{ SS::get('hero_btn_primary', 'Join Free Today') }}</a>
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-4">{{ SS::get('hero_btn_secondary', 'Sign In') }}</a>
                    @endauth
                </div>
                @php $badge = SS::get('hero_badge_text', 'No credit card needed to get started'); @endphp
                @if($badge)
                <p class="mt-3 opacity-75 small"><i class="bi bi-shield-check me-1"></i> {{ $badge }}</p>
                @endif
            </div>
            <div class="col-lg-6 d-none d-lg-flex justify-content-center gap-4" data-aos="fade-left" data-aos-delay="150">
                @php
                    $c1img = SS::get('hero_card1_image');
                    $c1img = is_array($c1img) ? reset($c1img) : $c1img;
                    $c1src = $c1img ? asset('storage/' . ltrim($c1img, '/')) : 'https://picsum.photos/seed/d1/200/260';
                @endphp
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden hero-profile-card" style="width:200px;transform:rotate(-4deg);margin-top:30px">
                    <img src="{{ $c1src }}" class="card-img-top" style="height:260px;object-fit:cover;" alt="">
                    <div class="card-body p-2 small"><strong>{{ SS::get('hero_card1_name', 'Emily, 26') }}</strong> <span class="text-body-secondary">{{ SS::get('hero_card1_location', 'NYC') }}</span></div>
                </div>
                @php
                    $c2img = SS::get('hero_card2_image');
                    $c2img = is_array($c2img) ? reset($c2img) : $c2img;
                    $c2src = $c2img ? asset('storage/' . ltrim($c2img, '/')) : 'https://picsum.photos/seed/d3/200/260';
                @endphp
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden hero-profile-card" style="width:200px;transform:rotate(3deg)">
                    <img src="{{ $c2src }}" class="card-img-top" style="height:260px;object-fit:cover;" alt="">
                    <div class="card-body p-2 small"><strong>{{ SS::get('hero_card2_name', 'James, 29') }}</strong> <span class="text-body-secondary">{{ SS::get('hero_card2_location', 'Chicago') }}</span></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Stats Bar --}}
<section class="home-stats bg-white py-4 border-bottom">
    <div class="container">
        <div class="row text-center g-3">
            <div class="col-4" data-aos="zoom-in">
                <div class="display-6 fw-bold text-primary">{{ SS::get('stat1_number', '50K+') }}</div>
                <div class="text-muted small">{{ SS::get('stat1_label', 'Active Members') }}</div>
            </div>
            <div class="col-4" data-aos="zoom-in" data-aos-delay="100">
                <div class="display-6 fw-bold text-primary">{{ SS::get('stat2_number', '12K+') }}</div>
                <div class="text-muted small">{{ SS::get('stat2_label', 'Matches Made') }}</div>
            </div>
            <div class="col-4" data-aos="zoom-in" data-aos-delay="200">
                <div class="display-6 fw-bold text-primary">{{ SS::get('stat3_number', '98%') }}</div>
                <div class="text-muted small">{{ SS::get('stat3_label', 'Safety Rating') }}</div>
            </div>
        </div>
    </div>
</section>

{{-- Features --}}
<section class="home-features py-5 bg-light">
    <div class="container py-4">
        <h2 class="text-center fw-bold mb-5" data-aos="fade-up">{{ SS::get('features_heading', 'Why ' . config('app.name') . '?') }}</h2>
        <div class="row g-4">
            @foreach(range(1,6) as $i)
            @php
                $defaults = [
                    1 => ['🔥','Swipe & Browse','Discover profiles with our fun swipe deck OR browse the full grid — your choice.'],
                    2 => ['🧬','Compatibility Score','Our algorithm analyses shared interests, values, and goals to show you truly compatible matches.'],
                    3 => ['💬','Real-Time Chat','Instant messaging with read receipts and typing indicators once you both match.'],
                    4 => ['🛡️','Safe & Verified','Photo moderation, block, report, and a real human mod team keep you safe.'],
                    5 => ['⭐','Premium Features','See who liked you, unlimited likes, boost your profile, unlimited location updates — pay with crypto for full privacy.'],
                    6 => ['📍','Location Discovery','Find people near you or anywhere in the world — you control the distance range.'],
                ];
                $icon  = SS::get("feat{$i}_icon",  $defaults[$i][0]);
                $title = SS::get("feat{$i}_title", $defaults[$i][1]);
                $desc  = SS::get("feat{$i}_desc",  $defaults[$i][2]);
                $delay = (($i - 1) % 3) * 100;
            @endphp
            <div class="col-md-4 text-center" data-aos="fade-up" data-aos-delay="{{ $delay }}">
                <div class="display-4 mb-3">{{ $icon }}</div>
                <h5 class="fw-bold">{{ $title }}</h5>
                <p class="text-muted">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="py-5" style="background:linear-gradient(135deg,#7b1fa2,#c2185b);color:#fff">
    <div class="container text-center py-4" data-aos="zoom-in">
        <h2 class="fw-bold mb-3">{{ SS::get('cta_heading', 'Ready to Find Your Person?') }}</h2>
        <p class="lead mb-4 opacity-90">{{ SS::get('cta_subtext', 'It only takes 2 minutes to create your profile.') }}</p>
        <a href="{{ route('register') }}" class="btn btn-warning btn-lg fw-bold px-5 shadow"><i class="bi bi-hearts me-2"></i>{{ SS::get('cta_btn_text', 'Create Free Account') }}</a>
    </div>
</section>

@endsection