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

    /* ── Hero ──────────────────────────────────────────────────── */
    .hero-section {
        position: relative;
        min-height: 92vh;
        display: flex;
        align-items: center;
        background: radial-gradient(ellipse at 18% 18%, #3a0a4a 0%, #1d0635 38%, #0d0118 100%);
        overflow: hidden;
        padding: 5rem 0;
    }

    /* Floating particles */
    #heroParticles {
        position: absolute;
        inset: 0;
        pointer-events: none;
        z-index: 0;
        overflow: hidden;
    }
    .hp {
        position: absolute;
        bottom: -40px;
        animation: hp-float linear infinite;
        opacity: 0;
        user-select: none;
        line-height: 1;
    }
    @keyframes hp-float {
        0%   { transform: translateY(0) scale(1) rotate(0deg);   opacity: 0; }
        8%   { opacity: 1; }
        85%  { opacity: 0.5; }
        100% { transform: translateY(-110vh) scale(0.4) rotate(220deg); opacity: 0; }
    }

    /* Radial overlay glow */
    .hero-section::before {
        content: '';
        position: absolute;
        top: -20%;
        right: -10%;
        width: 700px;
        height: 700px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(194,24,91,0.18) 0%, transparent 65%);
        pointer-events: none;
        z-index: 0;
    }
    .hero-section::after {
        content: '';
        position: absolute;
        bottom: -15%;
        left: -5%;
        width: 500px;
        height: 500px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(123,31,162,0.14) 0%, transparent 65%);
        pointer-events: none;
        z-index: 0;
    }

    /* ── Hero text ─────────────────────────────────────────────── */
    .hero-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(244,143,177,0.12);
        border: 1px solid rgba(244,143,177,0.25);
        border-radius: 2rem;
        padding: 0.35rem 1rem;
        font-size: 0.78rem;
        font-weight: 600;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        color: #f48fb1;
        margin-bottom: 1.5rem;
    }
    .hero-heading {
        font-family: 'Playfair Display', 'Georgia', serif;
        font-size: clamp(2.4rem, 6vw, 4.2rem);
        font-weight: 900;
        line-height: 1.12;
        letter-spacing: -1px;
        background: linear-gradient(135deg, #fff 0%, #f9a8d4 55%, #c084fc 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 1.25rem;
    }
    .hero-subtext {
        font-size: 1.1rem;
        color: rgba(255,255,255,0.55);
        line-height: 1.7;
        margin-bottom: 2.25rem;
        max-width: 460px;
    }

    /* ── Hero buttons ──────────────────────────────────────────── */
    .hero-btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: linear-gradient(135deg, #c2185b 0%, #7b1fa2 100%);
        color: #fff;
        font-weight: 700;
        font-size: 1rem;
        padding: 0.85rem 2rem;
        border-radius: 0.9rem;
        border: none;
        text-decoration: none;
        box-shadow: 0 8px 30px rgba(194,24,91,0.42);
        transition: transform 0.15s, box-shadow 0.2s;
    }
    .hero-btn-primary:hover {
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 14px 40px rgba(194,24,91,0.58);
    }
    .hero-btn-secondary {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.18);
        color: rgba(255,255,255,0.85);
        font-weight: 600;
        font-size: 1rem;
        padding: 0.85rem 2rem;
        border-radius: 0.9rem;
        text-decoration: none;
        transition: background 0.15s, border-color 0.15s, transform 0.15s;
    }
    .hero-btn-secondary:hover {
        color: #fff;
        background: rgba(255,255,255,0.14);
        border-color: rgba(255,255,255,0.32);
        transform: translateY(-2px);
    }

    /* ── Trust badges ──────────────────────────────────────────── */
    .trust-row {
        display: flex;
        flex-wrap: wrap;
        gap: 1.25rem;
        margin-top: 2.25rem;
    }
    .trust-badge {
        display: flex;
        align-items: center;
        gap: 0.45rem;
        color: rgba(255,255,255,0.38);
        font-size: 0.8rem;
    }
    .trust-badge i { color: #4ade80; }

    /* ── Stats pills ───────────────────────────────────────────── */
    .hero-stats {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        margin-top: 2.5rem;
        padding-top: 2rem;
        border-top: 1px solid rgba(255,255,255,0.07);
    }
    .hero-stat-item {
        text-align: center;
    }
    .hero-stat-num {
        font-family: 'Playfair Display', serif;
        font-size: 1.6rem;
        font-weight: 900;
        background: linear-gradient(135deg, #f48fb1, #ce93d8);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1;
        display: block;
    }
    .hero-stat-label {
        color: rgba(255,255,255,0.35);
        font-size: 0.72rem;
        letter-spacing: 0.5px;
        margin-top: 0.25rem;
    }

    /* ── Profile cards ─────────────────────────────────────────── */
    .profile-cards-wrap {
        position: relative;
        display: flex;
        justify-content: center;
        gap: 1.5rem;
        align-items: flex-start;
        padding-top: 2rem;
    }
    .hero-profile-card {
        border-radius: 1.25rem;
        overflow: hidden;
        border: none;
        box-shadow:
            0 25px 60px rgba(0,0,0,0.55),
            0 0 0 1px rgba(255,255,255,0.08) inset;
        background: rgba(255,255,255,0.06);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        transition: transform 0.3s;
        flex-shrink: 0;
    }
    .hero-profile-card:hover { transform: translateY(-6px) !important; }
    .hero-profile-card .card-img-top {
        object-fit: cover;
        display: block;
    }
    .card-info {
        padding: 0.75rem 1rem;
        background: rgba(15,5,25,0.75);
    }
    .card-name {
        font-weight: 700;
        font-size: 0.9rem;
        color: #fff;
        margin: 0;
    }
    .card-location {
        font-size: 0.75rem;
        color: rgba(255,255,255,0.45);
    }
    .card-match-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        background: linear-gradient(135deg, rgba(194,24,91,0.85), rgba(123,31,162,0.85));
        color: #fff;
        font-size: 0.68rem;
        font-weight: 700;
        padding: 0.2rem 0.6rem;
        border-radius: 2rem;
        margin-top: 0.35rem;
    }

    /* Floating match notification pill */
    .match-pill {
        position: absolute;
        bottom: 1.5rem;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(15,5,25,0.88);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(244,143,177,0.3);
        border-radius: 2rem;
        padding: 0.5rem 1.2rem;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        white-space: nowrap;
        animation: pill-float 3s ease-in-out infinite;
        box-shadow: 0 10px 30px rgba(0,0,0,0.4);
        font-size: 0.82rem;
        color: #fff;
    }
    @keyframes pill-float {
        0%, 100% { transform: translateX(-50%) translateY(0); }
        50%       { transform: translateX(-50%) translateY(-6px); }
    }
    .pill-heart {
        color: #f48fb1;
        animation: pulse-heart 1.2s ease-in-out infinite;
    }
    @keyframes pulse-heart {
        0%, 100% { transform: scale(1); }
        50%       { transform: scale(1.25); }
    }

    @media (max-width: 991.98px) {
        .hero-section { min-height: auto; padding: 4rem 0 3rem; }
        .profile-cards-wrap { padding-top: 1rem; }
    }
    @media (max-width: 575.98px) {
        .hero-subtext { max-width: 100%; }
        .hero-stats { gap: 1rem; }
    }
</style>
@endpush

@section('content')

{{-- Hero --}}
<section class="hero-section">
    <div id="heroParticles"></div>
    <div class="container position-relative" style="z-index:1">
        <div class="row align-items-center gy-5">

            {{-- Left: copy --}}
            <div class="col-lg-6" data-aos="fade-right">
                <div class="hero-eyebrow">
                    <i class="bi bi-hearts"></i>
                    {{ SS::get('hero_eyebrow', 'Trusted by 50,000+ singles') }}
                </div>

                <h1 class="hero-heading">
                    {!! nl2br(e(SS::get('hero_heading', 'Find Love\nThat Lasts'))) !!}
                </h1>

                <p class="hero-subtext">
                    {{ SS::get('hero_subtext', 'Join thousands of singles finding meaningful connections. Browse profiles, swipe, match, and start chatting — for free.') }}
                </p>

                <div class="d-flex gap-3 flex-wrap">
                    @auth
                    <a href="{{ route('dashboard') }}" class="hero-btn-primary">
                        <i class="bi bi-house-heart"></i> Go to Dashboard
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline m-0">
                        @csrf
                        <button type="submit" class="hero-btn-secondary">
                            <i class="bi bi-box-arrow-right"></i> Log Out
                        </button>
                    </form>
                    @else
                    <a href="{{ route('register') }}" class="hero-btn-primary">
                        <i class="bi bi-hearts"></i> {{ SS::get('hero_btn_primary', 'Join Free Today') }}
                    </a>
                    <a href="{{ route('login') }}" class="hero-btn-secondary">
                        {{ SS::get('hero_btn_secondary', 'Sign In') }}
                    </a>
                    @endauth
                </div>

                <div class="trust-row">
                    @php $badge = SS::get('hero_badge_text', 'No credit card needed to get started'); @endphp
                    @if($badge)
                    <span class="trust-badge"><i class="bi bi-shield-check"></i> {{ $badge }}</span>
                    @endif
                    <span class="trust-badge"><i class="bi bi-lock-fill"></i> 100% Private &amp; Secure</span>
                    <span class="trust-badge"><i class="bi bi-patch-check-fill" style="color:#60a5fa"></i> Verified Profiles</span>
                </div>

                <div class="hero-stats">
                    <div class="hero-stat-item">
                        <span class="hero-stat-num">{{ SS::get('stat1_number', '50K+') }}</span>
                        <span class="hero-stat-label">{{ SS::get('stat1_label', 'Active Members') }}</span>
                    </div>
                    <div class="hero-stat-item">
                        <span class="hero-stat-num">{{ SS::get('stat2_number', '12K+') }}</span>
                        <span class="hero-stat-label">{{ SS::get('stat2_label', 'Matches Made') }}</span>
                    </div>
                    <div class="hero-stat-item">
                        <span class="hero-stat-num">{{ SS::get('stat3_number', '98%') }}</span>
                        <span class="hero-stat-label">{{ SS::get('stat3_label', 'Safety Rating') }}</span>
                    </div>
                </div>
            </div>

            {{-- Right: profile cards --}}
            <div class="col-lg-6 d-none d-lg-block" data-aos="fade-left" data-aos-delay="150">
                <div class="profile-cards-wrap">
                    @php
                        $c1img = SS::get('hero_card1_image');
                        $c1img = is_array($c1img) ? reset($c1img) : $c1img;
                        $c1src = $c1img ? asset('storage/' . ltrim($c1img, '/')) : 'https://picsum.photos/seed/d1/220/290';
                    @endphp
                    <div class="hero-profile-card" style="width:195px;transform:rotate(-5deg);margin-top:40px">
                        <img src="{{ $c1src }}" class="card-img-top" style="height:270px;" alt="">
                        <div class="card-info">
                            <p class="card-name">{{ SS::get('hero_card1_name', 'Emily, 26') }}</p>
                            <span class="card-location"><i class="bi bi-geo-alt-fill me-1" style="color:#f48fb1;font-size:0.6rem"></i>{{ SS::get('hero_card1_location', 'NYC') }}</span>
                            <div class="card-match-badge"><i class="bi bi-hearts" style="font-size:0.65rem"></i> 97% Match</div>
                        </div>
                    </div>

                    @php
                        $c2img = SS::get('hero_card2_image');
                        $c2img = is_array($c2img) ? reset($c2img) : $c2img;
                        $c2src = $c2img ? asset('storage/' . ltrim($c2img, '/')) : 'https://picsum.photos/seed/d3/220/290';
                    @endphp
                    <div class="hero-profile-card" style="width:195px;transform:rotate(4deg)">
                        <img src="{{ $c2src }}" class="card-img-top" style="height:270px;" alt="">
                        <div class="card-info">
                            <p class="card-name">{{ SS::get('hero_card2_name', 'James, 29') }}</p>
                            <span class="card-location"><i class="bi bi-geo-alt-fill me-1" style="color:#f48fb1;font-size:0.6rem"></i>{{ SS::get('hero_card2_location', 'Chicago') }}</span>
                            <div class="card-match-badge"><i class="bi bi-hearts" style="font-size:0.65rem"></i> 94% Match</div>
                        </div>
                    </div>

                    {{-- Floating match pill --}}
                    <div class="match-pill">
                        <i class="bi bi-hearts pill-heart fs-5"></i>
                        <span><strong>It's a Match!</strong> <span style="color:rgba(255,255,255,0.5)">Start chatting now</span></span>
                    </div>
                </div>
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

@push('scripts')
<script>
(function () {
    const wrap    = document.getElementById('heroParticles');
    if (!wrap) return;
    const symbols = ['♥','♡','✦','✧','✩','❣','💕','✨','☆','⋆'];
    const colors  = ['#f48fb1','#ce93d8','#ffd54f','#ef9a9a','#b39ddb','rgba(255,255,255,0.6)'];
    for (let i = 0; i < 30; i++) {
        const el   = document.createElement('span');
        el.className = 'hp';
        el.textContent = symbols[Math.floor(Math.random() * symbols.length)];
        const size  = (Math.random() * 18 + 8).toFixed(1);
        const left  = (Math.random() * 100).toFixed(2);
        const delay = (Math.random() * 20).toFixed(2);
        const dur   = (Math.random() * 16 + 14).toFixed(2);
        const blur  = Math.random() < 0.3 ? '1px' : '0';
        el.style.cssText = `left:${left}%;font-size:${size}px;color:${colors[Math.floor(Math.random()*colors.length)]};animation-duration:${dur}s;animation-delay:-${delay}s;filter:blur(${blur});`;
        wrap.appendChild(el);
    }
})();
</script>
@endpush

@endsection