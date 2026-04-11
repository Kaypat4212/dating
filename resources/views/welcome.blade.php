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

{{-- ═══════════════════════════════════════════════════════════
     HOW IT WORKS — 3-step visual flow
     ═══════════════════════════════════════════════════════════ --}}
<section class="py-5" style="background:var(--bs-body-bg)">
    <div class="container py-4">
        <div class="text-center mb-5" data-aos="fade-up">
            <span style="display:inline-block;background:linear-gradient(135deg,#c2185b22,#7b1fa222);border:1px solid #c2185b44;border-radius:2rem;padding:.35rem 1.1rem;font-size:.78rem;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:#c2185b;" class="mb-3">How It Works</span>
            <h2 class="fw-bold mb-2" style="font-family:'Playfair Display',serif;font-size:clamp(1.8rem,4vw,2.8rem)">Three Steps to Your Match</h2>
            <p class="text-muted mx-auto" style="max-width:480px">Create a profile, discover compatible people, and start meaningful conversations — it's that simple.</p>
        </div>

        <div class="row g-0 align-items-stretch justify-content-center">
            @foreach([
                ['1','bi-person-plus-fill','Create Your Profile','Add your photos, answer a few personality questions, and let our algorithm do the hard work.','#c2185b'],
                ['2','bi-hearts-fill','Browse & Match','Swipe, explore the grid, or let AI surface your top picks. Like someone? If they like you back — it\'s a match!','#7b1fa2'],
                ['3','bi-chat-heart-fill','Chat & Connect','Start chatting instantly. Plan a date, share snaps, make voice calls — all in one safe place.','#f97316'],
            ] as [$num, $icon, $title, $desc, $color])
            <div class="col-md-4 p-3" data-aos="fade-up" data-aos-delay="{{ ($loop->index) * 120 }}">
                <div class="h-100 rounded-4 p-4 text-center" style="border:1.5px solid {{ $color }}22;background:linear-gradient(160deg,{{ $color }}0d 0%,transparent 100%);">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                         style="width:64px;height:64px;background:linear-gradient(135deg,{{ $color }},{{ $color }}88);box-shadow:0 8px 24px {{ $color }}44;">
                        <i class="bi {{ $icon }} text-white fs-4"></i>
                    </div>
                    <div class="fw-black mb-2" style="font-size:2.5rem;line-height:1;color:{{ $color }}1a;font-family:'Playfair Display',serif;-webkit-text-stroke:2px {{ $color }}55;">{{ $num }}</div>
                    <h5 class="fw-bold mb-2">{{ $title }}</h5>
                    <p class="text-muted small mb-0">{{ $desc }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     APP PREVIEW — dashboard mockup screenshot
     ═══════════════════════════════════════════════════════════ --}}
<section class="py-5 overflow-hidden" style="background:radial-gradient(ellipse at 50% 100%,#1d063588 0%,#0d0118 60%,#0d0118 100%);">
    <div class="container py-4">
        <div class="row align-items-center gy-5">
            <div class="col-lg-5" data-aos="fade-right">
                <span style="display:inline-block;background:rgba(244,143,177,.12);border:1px solid rgba(244,143,177,.25);border-radius:2rem;padding:.35rem 1.1rem;font-size:.78rem;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:#f48fb1;" class="mb-3">Built for Real Connections</span>
                <h2 class="fw-bold text-white mb-3" style="font-family:'Playfair Display',serif;font-size:clamp(1.8rem,4vw,2.6rem)">
                    Everything in<br>One Beautiful App
                </h2>
                <p style="color:rgba(255,255,255,.55);line-height:1.75;margin-bottom:1.75rem">
                    Your personalised dashboard shows your top matches, active chats, daily streak, and who's viewed your profile — all at a glance.
                </p>
                <div class="d-flex flex-column gap-3">
                    @foreach([
                        ['bi-layout-wtf','Smart Dashboard','See matches, messages, and activity in one place'],
                        ['bi-fire','Daily Streak','Keep the conversation alive with Snapchat‑style streaks'],
                        ['bi-camera-video-fill','Voice & Video Calls','Free voice and video calls once you match'],
                        ['bi-camera-fill','Snaps','Send disappearing photos and videos to your matches'],
                    ] as [$icon, $label, $sub])
                    <div class="d-flex align-items-start gap-3">
                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-3"
                             style="width:40px;height:40px;background:rgba(194,24,91,.2);">
                            <i class="bi {{ $icon }}" style="color:#f48fb1"></i>
                        </div>
                        <div>
                            <div class="fw-semibold text-white" style="font-size:.9rem">{{ $label }}</div>
                            <div style="color:rgba(255,255,255,.4);font-size:.8rem">{{ $sub }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>

                @guest
                <a href="{{ route('register') }}" class="hero-btn-primary mt-4 d-inline-flex">
                    <i class="bi bi-hearts"></i> Try it Free
                </a>
                @endguest
            </div>

            <div class="col-lg-7" data-aos="fade-left" data-aos-delay="100">
                {{-- Browser chrome mockup --}}
                <div class="position-relative mx-auto" style="max-width:640px">
                    <div class="rounded-4 overflow-hidden" style="border:1px solid rgba(255,255,255,.12);box-shadow:0 40px 100px rgba(0,0,0,.7);">
                        {{-- Browser bar --}}
                        <div class="px-3 py-2 d-flex align-items-center gap-2" style="background:#1c1c1e;border-bottom:1px solid rgba(255,255,255,.07);">
                            <span style="width:11px;height:11px;border-radius:50%;background:#ff5f57;display:inline-block"></span>
                            <span style="width:11px;height:11px;border-radius:50%;background:#febc2e;display:inline-block"></span>
                            <span style="width:11px;height:11px;border-radius:50%;background:#28c840;display:inline-block"></span>
                            <div class="flex-grow-1 mx-2 rounded-2 px-3 py-1 text-center" style="background:#2c2c2e;font-size:.7rem;color:rgba(255,255,255,.4);">
                                <i class="bi bi-lock-fill me-1" style="font-size:.6rem"></i>heartsconnect.cc/dashboard
                            </div>
                        </div>
                        {{-- Dashboard preview --}}
                        <div style="background:#0f0118;min-height:340px;padding:1.25rem;font-family:system-ui,sans-serif;">
                            {{-- mini navbar --}}
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2" style="border-bottom:1px solid rgba(255,255,255,.07);">
                                <span style="font-weight:800;font-size:1rem;background:linear-gradient(135deg,#f48fb1,#ce93d8);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">💜 HeartsConnect</span>
                                <div class="d-flex gap-2">
                                    @foreach(['bi-house-heart-fill','bi-people-fill','bi-chat-dots-fill','bi-person-circle'] as $ic)
                                    <span style="width:28px;height:28px;border-radius:8px;background:rgba(255,255,255,.06);display:inline-flex;align-items:center;justify-content:center;"><i class="bi {{ $ic }}" style="color:rgba(255,255,255,.4);font-size:.7rem"></i></span>
                                    @endforeach
                                </div>
                            </div>

                            {{-- greeting + stats row --}}
                            <p style="color:rgba(255,255,255,.55);font-size:.78rem;margin-bottom:.75rem">Good morning 👋 — <strong style="color:#fff">3 new matches</strong> are waiting</p>

                            <div class="d-flex gap-2 mb-3 flex-wrap">
                                @foreach([['❤️','12','Matches'],['💬','5','Unread'],['👁️','41','Views'],['🔥','7','Streak']] as [$ic,$n,$lb])
                                <div class="flex-fill text-center rounded-3 py-2 px-1" style="background:rgba(255,255,255,.05);min-width:60px;">
                                    <div style="font-size:1rem">{{ $ic }}</div>
                                    <div style="font-weight:800;color:#fff;font-size:.95rem">{{ $n }}</div>
                                    <div style="font-size:.6rem;color:rgba(255,255,255,.35)">{{ $lb }}</div>
                                </div>
                                @endforeach
                            </div>

                            {{-- match cards row --}}
                            <p style="color:rgba(255,255,255,.4);font-size:.72rem;text-transform:uppercase;letter-spacing:.8px;margin-bottom:.6rem">Your Top Matches</p>
                            <div class="d-flex gap-2 overflow-hidden">
                                @foreach([
                                    ['https://picsum.photos/seed/p1/80/100','Aisha, 24','99%'],
                                    ['https://picsum.photos/seed/p2/80/100','Sarah, 27','96%'],
                                    ['https://picsum.photos/seed/p3/80/100','Temi, 23','94%'],
                                    ['https://picsum.photos/seed/p4/80/100','Lola, 25','91%'],
                                ] as [$img,$name,$pct])
                                <div class="text-center flex-shrink-0" style="width:72px">
                                    <div class="position-relative mx-auto mb-1" style="width:54px;height:68px;border-radius:10px;overflow:hidden;border:1.5px solid rgba(194,24,91,.5);">
                                        <img src="{{ $img }}" style="width:100%;height:100%;object-fit:cover" alt="">
                                        <span style="position:absolute;bottom:2px;left:50%;transform:translateX(-50%);background:linear-gradient(135deg,#c2185b,#7b1fa2);color:#fff;font-size:.5rem;font-weight:700;padding:1px 4px;border-radius:10px;white-space:nowrap;">{{ $pct }}</span>
                                    </div>
                                    <div style="font-size:.6rem;color:rgba(255,255,255,.55);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $name }}</div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    {{-- Glow --}}
                    <div style="position:absolute;inset:-1px;border-radius:1rem;background:linear-gradient(135deg,#c2185b33,#7b1fa233);pointer-events:none;z-index:-1;filter:blur(30px);transform:scale(1.05)"></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     TESTIMONIALS / SUCCESS STORIES
     ═══════════════════════════════════════════════════════════ --}}
<section class="py-5" style="background:var(--bs-body-bg)">
    <div class="container py-4">
        <div class="text-center mb-5" data-aos="fade-up">
            <span style="display:inline-block;background:linear-gradient(135deg,#f4900022,#f4430022);border:1px solid #f9731644;border-radius:2rem;padding:.35rem 1.1rem;font-size:.78rem;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:#f97316;" class="mb-3">❤️ Real Stories</span>
            <h2 class="fw-bold mb-2" style="font-family:'Playfair Display',serif;font-size:clamp(1.8rem,4vw,2.8rem)">Love Stories That Started Here</h2>
            <p class="text-muted mx-auto" style="max-width:440px">Thousands of real couples found each other on {{ config('app.name') }}.</p>
        </div>

        <div class="row g-4">
            @foreach([
                ['https://picsum.photos/seed/t1/56/56','https://picsum.photos/seed/t2/56/56','Chisom & Emeka','Lagos, Nigeria','We matched on a Tuesday and went on our first date that Friday. One year later and we just got engaged! 💍 HeartsConnect changed our lives.',5],
                ['https://picsum.photos/seed/t3/56/56','https://picsum.photos/seed/t4/56/56','Priya & Daniel','London, UK','I wasn\'t sure about online dating but the compatibility score really works. We had 96% — and honestly that\'s exactly how it feels every day.',5],
                ['https://picsum.photos/seed/t5/56/56','https://picsum.photos/seed/t6/56/56','Fatima & Khalid','Dubai, UAE','The chat features are amazing — we kept each other\'s streaks going for 3 months before meeting. Best decision of my life to join.',5],
            ] as [$img1, $img2, $names, $location, $quote, $stars])
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                <div class="h-100 rounded-4 p-4" style="border:1px solid var(--bs-border-color);background:var(--bs-tertiary-bg, #f8f9fa);position:relative;overflow:hidden;">
                    <div style="position:absolute;top:-1px;left:0;right:0;height:3px;background:linear-gradient(90deg,#c2185b,#7b1fa2,#f97316);"></div>

                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="position-relative">
                            <img src="{{ $img1 }}" class="rounded-circle border border-2 border-white" style="width:40px;height:40px;object-fit:cover" alt="">
                            <img src="{{ $img2 }}" class="rounded-circle border border-2 border-white position-absolute" style="width:40px;height:40px;object-fit:cover;left:24px;top:0" alt="">
                        </div>
                        <div style="margin-left:28px">
                            <div class="fw-bold" style="font-size:.9rem">{{ $names }}</div>
                            <div class="text-muted" style="font-size:.75rem"><i class="bi bi-geo-alt-fill me-1" style="color:#c2185b;font-size:.6rem"></i>{{ $location }}</div>
                        </div>
                    </div>

                    <div class="mb-3" style="color:#f59e0b;font-size:.85rem">
                        @for($s=0;$s<$stars;$s++)<i class="bi bi-star-fill"></i>@endfor
                    </div>

                    <p class="mb-0" style="font-size:.88rem;line-height:1.65;color:var(--bs-body-color);font-style:italic;">"{{ $quote }}"</p>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Social proof bar --}}
        <div class="mt-5 rounded-4 p-4 text-center" style="background:linear-gradient(135deg,#c2185b0d,#7b1fa20d);border:1px solid rgba(194,24,91,.15);" data-aos="fade-up">
            <div class="d-flex justify-content-center align-items-center flex-wrap gap-4">
                @foreach([['50K+','Happy Members'],['12K+','Matches Made'],['4.9★','App Rating'],['98%','Safety Score'],['150+','Countries']] as [$n,$l])
                <div class="text-center px-3">
                    <div class="fw-black" style="font-size:1.5rem;background:linear-gradient(135deg,#c2185b,#7b1fa2);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">{{ $n }}</div>
                    <div class="text-muted" style="font-size:.75rem">{{ $l }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     FEATURES DEEP-DIVE — icon grid with hover cards
     ═══════════════════════════════════════════════════════════ --}}
<section class="py-5 overflow-hidden" style="background:radial-gradient(ellipse at 80% 50%,#1d063533 0%,var(--bs-body-bg) 60%);">
    <div class="container py-4">
        <div class="row align-items-center gy-5">
            <div class="col-lg-5" data-aos="fade-right">
                <span style="display:inline-block;background:rgba(123,31,162,.1);border:1px solid rgba(123,31,162,.3);border-radius:2rem;padding:.35rem 1.1rem;font-size:.78rem;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:#7b1fa2;" class="mb-3">Packed with Features</span>
                <h2 class="fw-bold mb-3" style="font-family:'Playfair Display',serif;font-size:clamp(1.8rem,4vw,2.6rem)">
                    Not Just Another<br>Dating App
                </h2>
                <p class="text-muted mb-4" style="line-height:1.75">We built every feature with real connections in mind. From AI-powered matching to safe-date check-ins, we've thought of everything.</p>
                @guest
                <a href="{{ route('register') }}" class="btn btn-lg fw-bold rounded-3 px-4" style="background:linear-gradient(135deg,#c2185b,#7b1fa2);color:#fff;box-shadow:0 8px 24px rgba(194,24,91,.35)">
                    <i class="bi bi-hearts me-2"></i>Explore the App
                </a>
                @endguest
            </div>
            <div class="col-lg-7" data-aos="fade-left">
                <div class="row g-3">
                    @foreach([
                        ['bi-cpu-fill','AI Matching','Smart compatibility algorithm learns your preferences over time','#7c3aed'],
                        ['bi-camera-fill','Snaps','Snapchat-style disappearing photos — fun, flirty, safe','#c2185b'],
                        ['bi-telephone-fill','Voice & Video','Free calls once matched — no need to share your number','#059669'],
                        ['bi-fire','Streaks','Daily interaction streaks keep you connected and engaged','#f97316'],
                        ['bi-shield-check-fill','Safe Dating','Safe-date check-in, block, report, photo verification','#2563eb'],
                        ['bi-trophy-fill','Badges & XP','Earn badges and level up your profile as you connect','#d97706'],
                        ['bi-megaphone-fill','Announcements','Stay up to date with new features and promotions','#0891b2'],
                        ['bi-globe','Country Forums','Local community forums to meet people in your area','#9333ea'],
                    ] as [$icon, $title, $desc, $color])
                    <div class="col-sm-6" data-aos="zoom-in" data-aos-delay="{{ $loop->index * 50 }}">
                        <div class="rounded-3 p-3 d-flex gap-3 align-items-start h-100" style="border:1px solid var(--bs-border-color);transition:all .2s;cursor:default" onmouseenter="this.style.borderColor='{{ $color }}55';this.style.background='{{ $color }}0a'" onmouseleave="this.style.borderColor='';this.style.background=''">
                            <div class="flex-shrink-0 rounded-2 d-flex align-items-center justify-content-center"
                                 style="width:38px;height:38px;background:{{ $color }}1a;">
                                <i class="bi {{ $icon }}" style="color:{{ $color }};font-size:1rem"></i>
                            </div>
                            <div>
                                <div class="fw-semibold" style="font-size:.88rem">{{ $title }}</div>
                                <div class="text-muted" style="font-size:.75rem;line-height:1.5">{{ $desc }}</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     PREMIUM PLANS TEASER
     ═══════════════════════════════════════════════════════════ --}}
<section class="py-5" style="background:var(--bs-body-bg)">
    <div class="container py-4">
        <div class="text-center mb-5" data-aos="fade-up">
            <span style="display:inline-block;background:linear-gradient(135deg,#d9770622,#b4530a22);border:1px solid #f59e0b44;border-radius:2rem;padding:.35rem 1.1rem;font-size:.78rem;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:#d97706;" class="mb-3">⭐ Premium</span>
            <h2 class="fw-bold mb-2" style="font-family:'Playfair Display',serif;font-size:clamp(1.8rem,4vw,2.8rem)">Go Premium, Find Love Faster</h2>
            <p class="text-muted mx-auto" style="max-width:440px">Free gets you started. Premium removes every limit.</p>
        </div>

        <div class="row g-4 justify-content-center">
            {{-- Free --}}
            <div class="col-md-5 col-lg-4" data-aos="fade-right">
                <div class="rounded-4 p-4 h-100" style="border:1px solid var(--bs-border-color)">
                    <div class="mb-3">
                        <span class="badge rounded-pill bg-secondary-subtle text-secondary fw-semibold px-3 py-2">Free</span>
                    </div>
                    <div class="mb-3">
                        <span style="font-size:2.2rem;font-weight:900">$0</span>
                        <span class="text-muted">/forever</span>
                    </div>
                    <ul class="list-unstyled mb-4" style="font-size:.9rem">
                        @foreach(['Browse & swipe profiles','5 likes per day','Basic chat','Community forums','Safe-date check-in'] as $f)
                        <li class="d-flex align-items-center gap-2 mb-2"><i class="bi bi-check-circle-fill text-success" style="flex-shrink:0"></i> {{ $f }}</li>
                        @endforeach
                        @foreach(['See who liked you','Unlimited likes','Profile boost','Unlimited location changes','Voice & video calls (unlimited)'] as $f)
                        <li class="d-flex align-items-center gap-2 mb-2 text-muted"><i class="bi bi-x-circle" style="flex-shrink:0;opacity:.4"></i> {{ $f }}</li>
                        @endforeach
                    </ul>
                    @guest
                    <a href="{{ route('register') }}" class="btn btn-outline-secondary w-100 rounded-3 fw-semibold">Get Started Free</a>
                    @endguest
                </div>
            </div>

            {{-- Premium --}}
            <div class="col-md-5 col-lg-4" data-aos="fade-left" data-aos-delay="80">
                <div class="rounded-4 p-4 h-100 position-relative" style="background:linear-gradient(160deg,#2d0845 0%,#1a0430 100%);border:1.5px solid rgba(194,24,91,.5);box-shadow:0 20px 60px rgba(194,24,91,.2);">
                    <div style="position:absolute;top:-1px;left:0;right:0;height:3px;background:linear-gradient(90deg,#c2185b,#7b1fa2,#f97316);border-radius:4px 4px 0 0;"></div>
                    <div class="mb-3 d-flex align-items-center gap-2">
                        <span class="badge rounded-pill fw-semibold px-3 py-2" style="background:linear-gradient(135deg,#c2185b,#7b1fa2);color:#fff">Premium ⭐</span>
                        <span class="badge rounded-pill bg-warning-subtle text-warning fw-semibold px-2 py-1" style="font-size:.65rem">MOST POPULAR</span>
                    </div>
                    <div class="mb-1">
                        <span style="font-size:2.2rem;font-weight:900;color:#fff">$9.99</span>
                        <span style="color:rgba(255,255,255,.5)">/month</span>
                    </div>
                    <p style="font-size:.75rem;color:rgba(255,255,255,.35);margin-bottom:1rem">Cancel anytime · Pay with card or crypto</p>
                    <ul class="list-unstyled mb-4" style="font-size:.9rem">
                        @foreach(['Everything in Free','✨ See who liked you','💫 Unlimited likes','🚀 Profile boost (3×/month)','📍 Unlimited location changes','📞 Unlimited voice & video calls','👑 Premium badge on profile','🔍 Advanced filters'] as $f)
                        <li class="d-flex align-items-center gap-2 mb-2" style="color:rgba(255,255,255,.85)"><i class="bi bi-check-circle-fill" style="color:#4ade80;flex-shrink:0"></i> {{ $f }}</li>
                        @endforeach
                    </ul>
                    @guest
                    <a href="{{ route('register') }}" class="btn w-100 rounded-3 fw-bold py-2" style="background:linear-gradient(135deg,#c2185b,#7b1fa2);color:#fff;box-shadow:0 6px 20px rgba(194,24,91,.4)">
                        <i class="bi bi-hearts me-2"></i>Go Premium
                    </a>
                    @else
                    <a href="{{ route('premium.show') }}" class="btn w-100 rounded-3 fw-bold py-2" style="background:linear-gradient(135deg,#c2185b,#7b1fa2);color:#fff;box-shadow:0 6px 20px rgba(194,24,91,.4)">
                        <i class="bi bi-hearts me-2"></i>Upgrade Now
                    </a>
                    @endguest
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     SAFETY SECTION
     ═══════════════════════════════════════════════════════════ --}}
<section class="py-5 overflow-hidden" style="background:linear-gradient(160deg,#0d3349 0%,#0d0118 100%);">
    <div class="container py-4">
        <div class="row align-items-center gy-5">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="d-flex flex-wrap gap-3">
                    @foreach([
                        ['🛡️','Photo Verification','Every profile photo is reviewed by our moderation team before going live.'],
                        ['🔒','End-to-End Private','Your messages and media are only visible to you and your match.'],
                        ['📍','Safe Date Check-In','Share your date plan with a trusted contact. Auto-notify if you don\'t check in.'],
                        ['🚫','Block & Report','One tap to block or report any user — our team responds within 24 hours.'],
                    ] as [$ic,$title,$desc])
                    <div class="col-12 col-sm-12 p-3 rounded-4 d-flex gap-3 align-items-start" style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.07);" data-aos="fade-right" data-aos-delay="{{ $loop->index * 80 }}">
                        <div class="flex-shrink-0 rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(74,222,128,.12);font-size:1.3rem">{{ $ic }}</div>
                        <div>
                            <div class="fw-bold text-white mb-1" style="font-size:.9rem">{{ $title }}</div>
                            <div style="color:rgba(255,255,255,.45);font-size:.82rem;line-height:1.55">{{ $desc }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="col-lg-6 text-center text-lg-start" data-aos="fade-left">
                <span style="display:inline-block;background:rgba(74,222,128,.12);border:1px solid rgba(74,222,128,.25);border-radius:2rem;padding:.35rem 1.1rem;font-size:.78rem;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:#4ade80;" class="mb-3">Your Safety, Our Priority</span>
                <h2 class="fw-bold text-white mb-3" style="font-family:'Playfair Display',serif;font-size:clamp(1.8rem,4vw,2.6rem)">
                    Date with Confidence
                </h2>
                <p style="color:rgba(255,255,255,.55);line-height:1.75;margin-bottom:2rem">
                    We have a dedicated trust & safety team, AI photo moderation, and multiple layers of protection so you can focus on making real connections.
                </p>
                <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start">
                    <div class="text-center">
                        <div class="fw-black text-white" style="font-size:1.8rem;line-height:1">98%</div>
                        <div style="color:rgba(255,255,255,.35);font-size:.72rem">Safety Rating</div>
                    </div>
                    <div style="width:1px;background:rgba(255,255,255,.1)"></div>
                    <div class="text-center">
                        <div class="fw-black text-white" style="font-size:1.8rem;line-height:1">&lt;24h</div>
                        <div style="color:rgba(255,255,255,.35);font-size:.72rem">Report Response</div>
                    </div>
                    <div style="width:1px;background:rgba(255,255,255,.1)"></div>
                    <div class="text-center">
                        <div class="fw-black text-white" style="font-size:1.8rem;line-height:1">100%</div>
                        <div style="color:rgba(255,255,255,.35);font-size:.72rem">Verified Photos</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     FAQ ACCORDION
     ═══════════════════════════════════════════════════════════ --}}
<section class="py-5" style="background:var(--bs-body-bg)">
    <div class="container py-4" style="max-width:760px">
        <div class="text-center mb-5" data-aos="fade-up">
            <span style="display:inline-block;background:rgba(37,99,235,.1);border:1px solid rgba(37,99,235,.3);border-radius:2rem;padding:.35rem 1.1rem;font-size:.78rem;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:#2563eb;" class="mb-3">FAQ</span>
            <h2 class="fw-bold mb-2" style="font-family:'Playfair Display',serif;font-size:clamp(1.8rem,4vw,2.4rem)">Common Questions</h2>
        </div>
        <div class="accordion accordion-flush rounded-4 overflow-hidden" id="faqAccordion" style="border:1px solid var(--bs-border-color)">
            @foreach([
                ['Is {{ config(\'app.name\') }} free to use?','Yes! Creating an account, browsing profiles, and basic chatting are completely free. Premium upgrades unlock extra features like unlimited likes, seeing who liked you, and profile boosts.'],
                ['How does matching work?','When two people like each other it becomes a match and the chat unlocks. You can also let our AI suggest your most compatible matches based on shared interests, values, and location.'],
                ['Is my data private?','Absolutely. We never sell personal data. Your location is only used to suggest nearby matches and is never shared with other users precisely. See our Privacy Policy for full details.'],
                ['Can I use {{config(\'app.name\')}} outside my country?','Yes — we support 150+ countries. You can update your location at any time (unlimited on Premium) to find people wherever you are.'],
                ['How do I report someone?','Tap the three-dot menu on any profile or message and select "Report". Our moderation team reviews every report within 24 hours.'],
                ['What payment methods do you accept?','We accept credit/debit cards, PayPal, and major cryptocurrencies (Bitcoin, Ethereum) for full privacy.'],
            ] as [$q, $a])
            @php $id = 'faq' . $loop->index; @endphp
            <div class="accordion-item" style="border-color:var(--bs-border-color)" data-aos="fade-up" data-aos-delay="{{ $loop->index * 60 }}">
                <h2 class="accordion-header">
                    <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }} fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $id }}" style="font-size:.92rem">
                        {{ $q }}
                    </button>
                </h2>
                <div id="{{ $id }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted" style="font-size:.88rem;line-height:1.7">{{ $a }}</div>
                </div>
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