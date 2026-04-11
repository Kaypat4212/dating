<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $faviconPath = \App\Models\SiteSetting::get('site_favicon');
        $faviconUrl  = $faviconPath ? asset('storage/' . $faviconPath) : asset('favicon.svg');
        $faviconMime = str_ends_with($faviconUrl, '.svg') ? 'image/svg+xml' : 'image/png';
        $touchPath   = \App\Models\SiteSetting::get('site_apple_touch_icon');
        $touchUrl    = $touchPath ? asset('storage/' . $touchPath) : $faviconUrl;
    @endphp
    <link rel="icon" href="{{ $faviconUrl }}" type="{{ $faviconMime }}">
    <link rel="shortcut icon" href="{{ $faviconUrl }}">
    <link rel="apple-touch-icon" href="{{ $touchUrl }}">
    <link rel="manifest" href="{{ route('pwa.manifest') }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="theme-color" content="#e91e63">
    @php
        $appName = \App\Models\SiteSetting::get('site_name', config('app.name'));
        $fallbackTitle = \App\Models\SiteSetting::get('seo_default_title') ?: ($appName . ' — Find Your Match');
    @endphp
    <title>@hasSection('title')@yield('title') &mdash; {{ $appName }}@else{{ $fallbackTitle }}@endif</title>
    @include('partials.seo-meta')

    {{-- Apply saved theme BEFORE any CSS loads — prevents flash of wrong theme --}}
    <script>
        (function(){
            var t = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', t);
        })();
    </script>

    {{-- Google Fonts: Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Bootstrap Icons CDN (fallback for XAMPP font-path issues) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- AOS (Animate On Scroll) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css">

    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    @stack('head')
    @stack('styles')
    <style>
        /* Mobile: dropdown flows in-place below the button, full width */
        @media (max-width: 991.98px) {
            .navbar-dropdown-menu {
                position: static !important;
                width: 100% !important;
                margin-top: 6px !important;
                transform: none !important;
                box-shadow: none !important;
                max-height: 65vh;
                overflow-y: auto;
            }
        }
        /* Desktop: absolute right-aligned under the button, never goes off-screen */
        @media (min-width: 992px) {
            .dropdown .navbar-dropdown-menu {
                position: absolute !important;
                right: 0 !important;
                left: auto !important;
                top: 100% !important;
                transform: none !important;
                min-width: 260px;
                z-index: 1050;
                max-height: calc(100vh - 80px);
                overflow-y: auto;
            }
        }
        /* Mobile navbar collapse — scrollable when content overflows */
        @media (max-width: 991.98px) {
            .navbar-collapse.show,
            .navbar-collapse.collapsing {
                max-height: 70vh;
                overflow-y: auto;
                overflow-x: hidden;
            }
            /* Scrollable inline dropdowns inside the mobile collapse */
            .navbar-collapse .dropdown-menu {
                max-height: 45vh;
                overflow-y: auto;
            }
        }
        /* All dropdown menus — cap height so they never overflow the viewport */
        .dropdown-menu {
            max-height: calc(100vh - 100px);
            overflow-y: auto;
        }
        /* Thin custom scrollbar for navbar collapse and dropdowns */
        .navbar-collapse::-webkit-scrollbar,
        .dropdown-menu::-webkit-scrollbar,
        .navbar-dropdown-menu::-webkit-scrollbar {
            width: 4px;
        }
        .navbar-collapse::-webkit-scrollbar-track,
        .dropdown-menu::-webkit-scrollbar-track,
        .navbar-dropdown-menu::-webkit-scrollbar-track {
            background: transparent;
        }
        .navbar-collapse::-webkit-scrollbar-thumb,
        .dropdown-menu::-webkit-scrollbar-thumb,
        .navbar-dropdown-menu::-webkit-scrollbar-thumb {
            background: rgba(var(--bs-secondary-rgb), .3);
            border-radius: 4px;
        }
        /* Unread message blinking dot on bottom nav */
        @keyframes msgPulse {
            0%, 100% { opacity:1; transform:scale(1); }
            50%       { opacity:.5; transform:scale(1.4); }
        }
        .bnav-msg-dot {
            position: absolute;
            top: 3px;
            right: calc(50% - 14px);
            width: 9px;
            height: 9px;
            background: #ef4444;
            border-radius: 50%;
            border: 2px solid var(--bs-body-bg, #fff);
            animation: msgPulse 1.1s ease-in-out infinite;
            display: block;
        }
        .bnav-msg-dot.d-none { display: none !important; }

        /* ── Desktop Navbar Redesign ───────────────────────────────────────── */
        @media (min-width: 992px) {
            /* Active nav-link: pink underline indicator */
            #mainNav .nav-link {
                position: relative;
                padding-bottom: .4rem;
                font-size: .88rem;
                font-weight: 500;
                letter-spacing: .01em;
            }
            #mainNav .nav-link::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 50%;
                transform: translateX(-50%);
                width: 0;
                height: 2px;
                border-radius: 2px;
                background: #e91e63;
                transition: width .2s ease;
            }
            #mainNav .nav-link:hover::after,
            #mainNav .nav-link.active::after,
            #mainNav .nav-link.show::after {
                width: 70%;
            }
            #mainNav .nav-link.active {
                color: #e91e63 !important;
            }
            /* Vertical separator between left nav and right icons */
            #mainNav .nav-divider {
                width: 1px;
                height: 24px;
                background: var(--bs-border-color);
                opacity: .6;
                align-self: center;
                margin: 0 .25rem;
            }
        }
        /* Icon-only nav buttons (bell, chat, theme) */
        .nav-icon-btn {
            border: none !important;
            background: transparent !important;
            color: rgba(255, 255, 255, 0.85) !important;
            padding: .3rem .45rem !important;
            border-radius: 50% !important;
            line-height: 1;
            transition: background .15s ease, color .15s ease;
        }
        .nav-icon-btn:hover {
            background: rgba(var(--bs-secondary-rgb), .12) !important;
            color: #fff !important;
        }
        .nav-icon-btn .bi { font-size: 1rem; }
        
        /* Mobile navbar buttons - ensure visibility on dark navbar */
        #mainNav .btn-outline-secondary {
            color: rgba(255, 255, 255, 0.85);
            border-color: rgba(255, 255, 255, 0.3);
        }
        #mainNav .btn-outline-secondary:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.5);
        }
        
        /* ── Luxury Preloader ─────────────────────────────────────────────── */
        #hc-preloader {
            position: fixed;
            inset: 0;
            z-index: 99999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #080010;
            overflow: hidden;
            transition: opacity .6s ease, visibility .6s ease;
        }
        #hc-preloader.hc-fade-out {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }
        /* Ambient glow blobs */
        .hc-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: .35;
            animation: hcBlobFloat 8s ease-in-out infinite alternate;
        }
        .hc-blob-1 {
            width: 420px; height: 420px;
            background: radial-gradient(circle, #c44ee0, transparent 70%);
            top: -80px; left: -100px;
            animation-delay: 0s;
        }
        .hc-blob-2 {
            width: 380px; height: 380px;
            background: radial-gradient(circle, #7b2ff7, transparent 70%);
            bottom: -60px; right: -80px;
            animation-delay: -4s;
        }
        .hc-blob-3 {
            width: 260px; height: 260px;
            background: radial-gradient(circle, #ff6b9d, transparent 70%);
            top: 40%; left: 60%;
            animation-delay: -2s;
        }
        @keyframes hcBlobFloat {
            0%   { transform: translate(0, 0) scale(1); }
            100% { transform: translate(30px, 20px) scale(1.08); }
        }
        /* Grain overlay for luxury texture */
        #hc-preloader::before {
            content: '';
            position: absolute;
            inset: 0;
            opacity: .04;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
            background-size: 180px;
            pointer-events: none;
        }
        /* Logo container */
        .hc-logo-wrap {
            position: relative;
            width: 110px;
            height: 110px;
            margin-bottom: 2.4rem;
        }
        /* Rotating ring */
        .hc-ring {
            position: absolute;
            inset: -16px;
            border-radius: 50%;
            border: 1.5px solid transparent;
            background: conic-gradient(from 0deg, #ff6b9d, #c44ee0, #7b2ff7, #ff6b9d) border-box;
            -webkit-mask: linear-gradient(#fff 0 0) padding-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: destination-out;
            mask-composite: exclude;
            animation: hcSpin 2.4s linear infinite;
        }
        /* Outer soft glow ring (static) */
        .hc-glow-ring {
            position: absolute;
            inset: -28px;
            border-radius: 50%;
            border: 1px solid rgba(196, 78, 224, .2);
            animation: hcPulseRing 2s ease-in-out infinite;
        }
        @keyframes hcSpin {
            to { transform: rotate(360deg); }
        }
        @keyframes hcPulseRing {
            0%, 100% { opacity: .2; transform: scale(1); }
            50%       { opacity: .6; transform: scale(1.04); }
        }
        /* The favicon SVG itself */
        .hc-logo-svg {
            width: 110px;
            height: 110px;
            border-radius: 26px;
            animation: hcHeartbeat 2s ease-in-out infinite;
            box-shadow:
                0 0 40px rgba(196, 78, 224, .5),
                0 0 80px rgba(123, 47, 247, .25),
                0 20px 40px rgba(0,0,0,.6);
        }
        @keyframes hcHeartbeat {
            0%, 100% { transform: scale(1); }
            14%       { transform: scale(1.06); }
            28%       { transform: scale(1); }
            42%       { transform: scale(1.04); }
            56%       { transform: scale(1); }
        }
        /* Floating sparkles */
        .hc-sparkle {
            position: absolute;
            width: 5px; height: 5px;
            border-radius: 50%;
            background: #fff;
            opacity: 0;
            animation: hcSparkle 3s ease-in-out infinite;
        }
        @keyframes hcSparkle {
            0%   { opacity: 0; transform: translateY(0) scale(0); }
            30%  { opacity: .9; transform: translateY(-18px) scale(1); }
            60%  { opacity: .4; transform: translateY(-36px) scale(.6); }
            100% { opacity: 0; transform: translateY(-54px) scale(0); }
        }
        /* Brand name */
        .hc-brand {
            font-family: 'Inter', 'Georgia', serif;
            font-size: 1.55rem;
            font-weight: 300;
            letter-spacing: .28em;
            text-transform: uppercase;
            color: #fff;
            margin-bottom: .45rem;
            background: linear-gradient(90deg, #ff6b9d, #e0aaff, #7b2ff7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: hcShimmer 3s ease-in-out infinite;
            background-size: 200% auto;
        }
        @keyframes hcShimmer {
            0%   { background-position: 0% center; }
            50%  { background-position: 100% center; }
            100% { background-position: 0% center; }
        }
        .hc-tagline {
            font-family: 'Inter', sans-serif;
            font-size: .72rem;
            font-weight: 400;
            letter-spacing: .22em;
            text-transform: uppercase;
            color: rgba(255,255,255,.38);
            margin-bottom: 2.8rem;
        }
        /* Progress bar */
        .hc-progress-track {
            width: 160px;
            height: 1px;
            background: rgba(255,255,255,.1);
            border-radius: 1px;
            overflow: hidden;
            position: relative;
        }
        .hc-progress-fill {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, #ff6b9d, #c44ee0, #7b2ff7);
            border-radius: 1px;
            animation: hcProgress 1.8s cubic-bezier(.4,0,.2,1) forwards;
            box-shadow: 0 0 8px rgba(196,78,224,.8);
        }
        @keyframes hcProgress {
            0%   { width: 0%; }
            60%  { width: 75%; }
            100% { width: 100%; }
        }
    </style>
</head>
<body>

{{-- ── Luxury Preloader ────────────────────────────────────────────────────────--}}
<div id="hc-preloader" aria-hidden="true" role="presentation">
    {{-- Ambient blobs --}}
    <div class="hc-blob hc-blob-1"></div>
    <div class="hc-blob hc-blob-2"></div>
    <div class="hc-blob hc-blob-3"></div>

    {{-- Logo --}}
    <div class="hc-logo-wrap">
        <div class="hc-glow-ring"></div>
        <div class="hc-ring"></div>

        {{-- Sparkles --}}
        <div class="hc-sparkle" style="top:0;left:50%;animation-delay:0s"></div>
        <div class="hc-sparkle" style="top:20%;right:-8px;animation-delay:.6s;width:3px;height:3px"></div>
        <div class="hc-sparkle" style="top:10%;left:0;animation-delay:1.2s;width:4px;height:4px"></div>
        <div class="hc-sparkle" style="top:70%;right:-4px;animation-delay:1.8s;width:3px;height:3px"></div>
        <div class="hc-sparkle" style="bottom:0;left:30%;animation-delay:2.4s;width:4px;height:4px"></div>

        <svg class="hc-logo-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">
            <defs>
                <linearGradient id="hcbg" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0%"   stop-color="#ff6b9d"/>
                    <stop offset="55%"  stop-color="#c44ee0"/>
                    <stop offset="100%" stop-color="#7b2ff7"/>
                </linearGradient>
            </defs>
            <rect width="64" height="64" rx="14" ry="14" fill="url(#hcbg)"/>
            <path fill="white" opacity="0.96"
                d="M32,52 C28,48 8,37 8,22 C8,14.3 14.3,8 22,8 C26,8 29.7,9.8 32,12.7
                   C34.3,9.8 38,8 42,8 C49.7,8 56,14.3 56,22 C56,37 36,48 32,52 Z"/>
        </svg>
    </div>

    {{-- Brand --}}
    @php $preloaderName = \App\Models\SiteSetting::get('site_name', config('app.name', 'HeartsConnect')); @endphp
    <div class="hc-brand">{{ $preloaderName }}</div>
    <div class="hc-tagline">Find Your Match</div>

    {{-- Progress bar --}}
    <div class="hc-progress-track">
        <div class="hc-progress-fill"></div>
    </div>
</div>

<script>
(function () {
    var el = document.getElementById('hc-preloader');
    function hide() {
        if (!el) return;
        // Small delay so the progress bar visually completes
        setTimeout(function () {
            el.classList.add('hc-fade-out');
            setTimeout(function () { el.remove(); }, 650);
        }, 200);
    }
    if (document.readyState === 'complete') {
        hide();
    } else {
        window.addEventListener('load', hide);
        // Hard cap: never block UI longer than 4 s
        setTimeout(hide, 4000);
    }
}());
</script>

{{-- ── Impersonation Banner ────────────────────────────────────────────────────--}}
@if(session('impersonating_id'))
<div class="alert alert-warning alert-dismissible mb-0 rounded-0 border-0 border-bottom d-flex align-items-center justify-content-center gap-3 py-2" style="position:sticky;top:0;z-index:2000;">
    <i class="bi bi-person-fill-gear fs-5"></i>
    <span>You are currently <strong>logged in as {{ auth()->user()->name }}</strong> (impersonating). All actions affect this real user.</span>
    <a href="{{ route('impersonate.leave') }}" class="btn btn-sm btn-dark ms-2">
        <i class="bi bi-arrow-left-circle me-1"></i>Return to Admin
    </a>
</div>
@endif

{{-- ── Navbar ─────────────────────────────────────────────────────────────────--}}
@php
    $unreadCount    = auth()->check() ? auth()->user()->unreadNotifications()->count() : 0;
    $unreadMsgCount = 0;
    $wnUnreadCount  = 0;
    if (auth()->check()) {
        $uid = auth()->id();
        $unreadMsgCount = \App\Models\Message::whereHas('conversation.match', function ($q) use ($uid) {
            $q->where('user1_id', $uid)->orWhere('user2_id', $uid);
        })->where('sender_id', '!=', $uid)->whereNull('read_at')->count();
        $wnUnreadCount = \App\Models\Announcement::published()->forUser($uid)
            ->whereDoesntHave('reads', fn($q) => $q->where('user_id', $uid))
            ->count();
    }
    $navPhoto = auth()->check() ? auth()->user()->primaryPhoto : null;
    $callsEnabled = (bool) \App\Models\SiteSetting::get('voice_calls_enabled', true);
@endphp
<nav class="navbar navbar-expand-lg sticky-top shadow-sm" id="mainNav">
    <div class="container">

        {{-- Brand --}}
        <a class="navbar-brand fw-bold d-flex align-items-center text-decoration-none" href="{{ route('home') }}">
            <x-site-logo size="md" />
        </a>

        @auth
        {{-- Mobile: quick-action icons always visible beside toggler --}}
        <div class="d-flex d-lg-none align-items-center gap-1 ms-auto me-2">
            <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-outline-secondary position-relative">
                <i class="bi bi-bell"></i>
                @if($unreadCount > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.6rem">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                @endif
            </a>
            {{-- What's New (mobile) --}}
            <button type="button" class="btn btn-sm btn-outline-secondary position-relative" data-bs-toggle="modal" data-bs-target="#whatsNewModal" title="What's New">
                🎉
                <span id="wnNavBadgeMobile" data-count="{{ $wnUnreadCount }}"
                      class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-purple{{ $wnUnreadCount > 0 ? '' : ' d-none' }}"
                      style="font-size:.6rem;background:#7c3aed">{{ $wnUnreadCount > 99 ? '99+' : $wnUnreadCount }}</span>
            </button>
            <a href="{{ route('conversations.index') }}" class="btn btn-sm btn-outline-secondary position-relative" id="navChatMobile">
                <i class="bi bi-chat-heart"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger{{ $unreadMsgCount > 0 ? '' : ' d-none' }}" id="msgBadgeMobile" style="font-size:.6rem">{{ $unreadMsgCount > 99 ? '99+' : max($unreadMsgCount,1) }}</span>
            </a>
        </div>
        @endauth

        {{-- Hamburger toggler --}}
        <button class="navbar-toggler border-0" type="button"
            data-bs-toggle="collapse" data-bs-target="#navbarMain"
            aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- Collapsible section --}}
        <div class="collapse navbar-collapse" id="navbarMain">

            @auth
            {{-- Main nav links --}}
            <ul class="navbar-nav me-auto mt-2 mt-lg-0 align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active fw-semibold' : '' }}" href="{{ route('dashboard') }}">
                        <i class="bi bi-house-heart me-1"></i>Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('swipe.*') ? 'active fw-semibold' : '' }}" href="{{ route('swipe.deck') }}">
                        <i class="bi bi-fire me-1"></i>Swipe
                    </a>
                </l

                {{-- Discover dropdown --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('discover.*') || request()->routeIs('users.search*') ? 'active fw-semibold' : '' }}"
                       href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-search-heart me-1"></i>Discover
                    </a>
                    <ul class="dropdown-menu shadow border-0">
                        <li><h6 class="dropdown-header small text-uppercase tracking-wide">Find People</h6></li>
                        <li><a class="dropdown-item" href="{{ route('discover.index') }}"><i class="bi bi-grid-3x3-gap me-2 text-primary"></i>Browse Profiles</a></li>
                        <li><a class="dropdown-item" href="{{ route('users.search') }}"><i class="bi bi-person-search me-2 text-info"></i>Advanced Search</a></li>
                    </ul>
                </li>

                {{-- Activity dropdown --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('matches.*') || request()->routeIs('stories.*') || request()->routeIs('feed.*') || request()->routeIs('badges.*') || request()->routeIs('like.*') || request()->routeIs('wave.*') ? 'active fw-semibold' : '' }}"
                       href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-activity me-1"></i>Activity
                        @php
                            $newBadgeCount = auth()->user()?->badges()->wherePivot('earned_at', '>=', now()->subHours(24))->count() ?? 0;
                        @endphp
                        @if($newBadgeCount > 0)
                            <span class="badge bg-warning text-dark ms-1" style="font-size:.6rem">{{ $newBadgeCount }}</span>
                        @endif
                    </a>
                    <ul class="dropdown-menu shadow border-0">
                        <li><h6 class="dropdown-header small text-uppercase">Connections</h6></li>
                        <li><a class="dropdown-item" href="{{ route('matches.index') }}"><i class="bi bi-hearts me-2 text-danger"></i>My Matches</a></li>
                        <li><a class="dropdown-item" href="{{ route('like.who-liked-me') }}"><i class="bi bi-heart-fill me-2 text-danger"></i>Liked You</a></li>
                        <li><a class="dropdown-item" href="{{ route('wave.received') }}"><i class="bi bi-hand-wave me-2 text-warning"></i>Waves</a></li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li><h6 class="dropdown-header small text-uppercase">Content</h6></li>
                        <li><a class="dropdown-item" href="{{ route('stories.index') }}"><i class="bi bi-camera-video me-2 text-purple" style="color:#7b2ff7"></i>Stories</a></li>
                        <li><a class="dropdown-item" href="{{ route('feed.index') }}"><i class="bi bi-grid-1x2 me-2 text-info"></i>Feed</a></li>
                        <li><a class="dropdown-item" href="{{ route('badges.index') }}">
                            <i class="bi bi-trophy me-2 text-warning"></i>Badges
                            @if($newBadgeCount > 0)<span class="badge bg-warning text-dark ms-1" style="font-size:.6rem">{{ $newBadgeCount }} new</span>@endif
                        </a></li>
                    </ul>
                </li>

                {{-- Community dropdown --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('blog.*') || request()->routeIs('forum.*') || request()->routeIs('chat-rooms.*') || request()->routeIs('travel.*') ? 'active fw-semibold' : '' }}"
                       href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-globe-americas me-1"></i>Community
                    </a>
                    <ul class="dropdown-menu shadow border-0">
                        <li><a class="dropdown-item" href="{{ route('blog.index') }}"><i class="bi bi-journal-richtext me-2 text-primary"></i>Blog</a></li>
                        <li><a class="dropdown-item" href="{{ route('forum.index') }}"><i class="bi bi-people-fill me-2 text-success"></i>Forum</a></li>
                        <li><a class="dropdown-item" href="{{ route('chat-rooms.index') }}"><i class="bi bi-chat-dots me-2 text-info"></i>Chat Rooms</a></li>
                        <li><a class="dropdown-item" href="{{ route('travel.index') }}"><i class="bi bi-airplane me-2 text-warning"></i>Travel Buddy</a></li>
                        <li><a class="dropdown-item" href="{{ route('speed-dating.index') }}"><i class="bi bi-lightning-charge me-2 text-danger"></i>Coffee Break ☕</a></li>
                    </ul>
                </li>

                {{-- Features dropdown --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('vibe.*') || request()->routeIs('second-chance.*') || request()->routeIs('match-question.*') || request()->routeIs('safe-date.*') ? 'active fw-semibold' : '' }}"
                       href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-stars me-1"></i>Features
                    </a>
                    <ul class="dropdown-menu shadow border-0">
                        <li><a class="dropdown-item" href="{{ route('vibe.quiz') }}"><i class="bi bi-stars me-2 text-purple" style="color:#7b2ff7"></i>Vibe Check Quiz</a></li>
                        <li><a class="dropdown-item" href="{{ route('match-question.index') }}"><i class="bi bi-calendar-heart me-2 text-info"></i>Question of the Day</a></li>
                        <li><a class="dropdown-item" href="{{ route('second-chance.index') }}"><i class="bi bi-arrow-repeat me-2 text-warning"></i>Second Chance Queue</a></li>
                        <li><a class="dropdown-item" href="{{ route('safe-date.index') }}"><i class="bi bi-shield-check me-2 text-success"></i>Safe Date Check-In</a></li>
                    </ul>
                </li>
            </ul>

            {{-- Right-side actions --}}
            <div class="d-flex align-items-center gap-2 mt-2 mt-lg-0">
                <span class="nav-divider d-none d-lg-block"></span>

                {{-- What's New (desktop) --}}
                <button type="button" class="nav-icon-btn position-relative d-none d-lg-inline-flex" data-bs-toggle="modal" data-bs-target="#whatsNewModal" title="What's New">
                    🎉
                    <span id="wnNavBadge" data-count="{{ $wnUnreadCount }}"
                          class="position-absolute top-0 start-100 translate-middle badge rounded-pill{{ $wnUnreadCount > 0 ? '' : ' d-none' }}"
                          style="font-size:.6rem;background:#7c3aed">{{ $wnUnreadCount > 99 ? '99+' : $wnUnreadCount }}</span>
                </button>

                {{-- Notifications --}}
                <a href="{{ route('notifications.index') }}" class="nav-icon-btn position-relative d-none d-lg-inline-flex" title="Notifications">
                    <i class="bi bi-bell"></i>
                    @if($unreadCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.6rem">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                    @endif
                </a>

                {{-- Messages --}}
                <a href="{{ route('conversations.index') }}" class="nav-icon-btn d-none d-lg-inline-flex position-relative" title="Messages" id="navChatDesktop">
                    <i class="bi bi-chat-heart"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger{{ $unreadMsgCount > 0 ? '' : ' d-none' }}" id="msgBadgeDesktop" style="font-size:.6rem">{{ $unreadMsgCount > 99 ? '99+' : max($unreadMsgCount,1) }}</span>
                </a>

                {{-- Wallet balance --}}
                <a href="{{ route('wallet.index') }}" class="btn btn-sm btn-outline-success d-none d-lg-inline-flex align-items-center gap-1 fw-semibold rounded-pill px-3" title="Wallet Credits" style="font-size:.8rem">
                    <i class="bi bi-coin"></i>{{ number_format(auth()->user()->credit_balance) }}
                </a>

                {{-- Login Streak --}}
                @if(auth()->user()->login_streak > 0)
                <span class="d-none d-lg-inline-flex align-items-center gap-1 fw-semibold px-2 py-1 text-warning" title="Daily check-in streak" id="streakBadge" style="cursor:default;font-size:.85rem">
                    🔥 {{ auth()->user()->login_streak }}
                </span>
                @endif

                {{-- Theme toggle --}}
                <button class="nav-icon-btn" data-theme-toggle title="Toggle dark/light mode" aria-label="Toggle theme">
                    <i class="bi bi-moon-stars-fill" data-theme-icon></i>
                </button>

                {{-- User dropdown --}}
                <div class="dropdown ms-1" style="position:relative">
                    <button class="btn btn-sm btn-primary dropdown-toggle d-flex align-items-center gap-2 rounded-pill" type="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false" style="padding-left:.7rem;padding-right:.9rem">
                        @if($navPhoto)
                            <img src="{{ $navPhoto->thumbnail_url }}" class="rounded-circle" width="24" height="24" alt="avatar" style="object-fit:cover">
                        @else
                            <i class="bi bi-person-circle"></i>
                        @endif
                        <span class="d-none d-md-inline fw-semibold" style="font-size:.85rem">{{ auth()->user()->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 navbar-dropdown-menu">
                        <li class="px-3 py-2 text-muted small">{{ auth()->user()->email }}</li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Profile</a></li>
                        <li><a class="dropdown-item" href="{{ route('extras.voice') }}"><i class="bi bi-mic-fill me-2 text-danger"></i>Voice Prompts</a></li>
                        <li><a class="dropdown-item" href="{{ route('extras.pets') }}"><i class="bi bi-heart-fill me-2 text-warning"></i>My Pets</a></li>
                        <li><a class="dropdown-item" href="{{ route('icebreaker.index') }}"><i class="bi bi-snow2 me-2 text-info"></i>Icebreakers</a></li>
                        <li><a class="dropdown-item" href="{{ route('account.show') }}"><i class="bi bi-gear me-2 text-secondary"></i>Settings</a></li>
                        <li><a class="dropdown-item" href="{{ route('invite.index') }}"><i class="bi bi-gift me-2 text-danger"></i>Invite Friends</a></li>
                        @if($callsEnabled)
                        <li>
                            <a class="dropdown-item d-flex align-items-center justify-content-between" href="{{ route('calls.history') }}" id="navCallHistoryLink">
                                <span><i class="bi bi-telephone-fill me-2 text-success"></i>Call History</span>
                                <span class="badge bg-danger rounded-pill d-none" id="missedCallBadgeNav" style="font-size:.65rem"></span>
                            </a>
                        </li>
                        @endif
                        {{-- Verification link --}}
                        @if(auth()->user()->is_verified)
                        <li><span class="dropdown-item-text small text-success"><i class="bi bi-patch-check-fill me-2"></i>Verified ✅</span></li>
                        @else
                        <li><a class="dropdown-item fw-semibold text-info" href="{{ route('verify.show') }}"><i class="bi bi-patch-check me-2"></i>Get Verified ✅</a></li>
                        @endif
                        {{-- Wallet balance (shown in dropdown on mobile) --}}
                        <li>
                            <a class="dropdown-item d-flex align-items-center justify-content-between" href="{{ route('wallet.index') }}">
                                <span><i class="bi bi-wallet2 me-2 text-success"></i>Wallet</span>
                                <span class="badge bg-success rounded-pill">{{ number_format(auth()->user()->credit_balance) }} cr</span>
                            </a>
                        </li>
                        @if(! auth()->user()->isPremiumActive())
                        <li><a class="dropdown-item fw-semibold text-warning" href="{{ route('premium.show') }}"><i class="bi bi-star-fill me-2"></i>Go Premium</a></li>
                        @endif
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Sign Out</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
            @else
            {{-- Guest links --}}
            <div class="ms-auto d-flex gap-2 mt-2 mt-lg-0">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-theme-toggle title="Toggle dark/light mode" aria-label="Toggle theme">
                    <i class="bi bi-moon-stars-fill" data-theme-icon></i>
                </button>
                <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm">Sign In</a>
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-hearts me-1"></i>Join Free
                </a>
            </div>
            @endauth
        </div>{{-- /.navbar-collapse --}}
    </div>
</nav>

{{-- ── Flash Messages ─────────────────────────────────────────────────────── --}}
@php /** @var \Illuminate\Support\ViewErrorBag $errors */ @endphp
@if(session('success') || session('error') || $errors->any())
<div class="container mt-3">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
</div>
@endif

{{-- ── Main Content ───────────────────────────────────────────────────────── --}}
<main class="@auth pb-5 @endauth">
    @yield('content')
</main>

{{-- ── Mobile Bottom Nav ──────────────────────────────────────────────────── --}}
@auth
<nav class="bottom-nav d-lg-none">
    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="bi bi-house-heart-fill"></i><span>Home</span>
    </a>
    <a href="{{ route('swipe.deck') }}" class="{{ request()->routeIs('swipe.*') ? 'active' : '' }}">
        <i class="bi bi-fire"></i><span>Swipe</span>
    </a>
    <a href="{{ route('discover.index') }}" class="{{ request()->routeIs('discover.*') ? 'active' : '' }}">
        <i class="bi bi-search-heart"></i><span>Browse</span>
    </a>
    <a href="{{ route('users.search') }}" class="{{ request()->routeIs('users.search*') ? 'active' : '' }}">
        <i class="bi bi-compass-fill"></i><span>Find</span>
    </a>
    <a href="{{ route('matches.index') }}" class="{{ request()->routeIs('matches.*') ? 'active' : '' }}">
        <i class="bi bi-hearts"></i><span>Matches</span>
    </a>
    <a href="{{ route('feed.index') }}" class="{{ request()->routeIs('feed.*') ? 'active' : '' }}">
        <i class="bi bi-grid-1x2-fill"></i><span>Feed</span>
    </a>
    <a href="{{ route('conversations.index') }}" class="{{ request()->routeIs('conversations.*') ? 'active' : '' }}" style="position:relative">
        <i class="bi bi-chat-heart-fill"></i>
        <span class="bnav-msg-dot{{ $unreadMsgCount > 0 ? '' : ' d-none' }}" id="msgDotBottom"></span>
        <span>Chat</span>
    </a>
</nav>
@endauth

@include('partials.footer')

<div class="position-fixed bottom-0 end-0 p-3" style="z-index:1040;padding-bottom:calc(.75rem + 62px) !important" id="toastContainer"></div>

{{-- Auto-show toasts from session flash data --}}
<script>
(function () {
    var toasts = [
        @if(session('success'))
        { msg: @json(session('success')), type: 'success', icon: 'bi-check-circle-fill' },
        @endif
        @if(session('error'))
        { msg: @json(session('error')), type: 'danger', icon: 'bi-exclamation-circle-fill' },
        @endif
        @if(session('warning'))
        { msg: @json(session('warning')), type: 'warning', icon: 'bi-exclamation-triangle-fill' },
        @endif
        @if(session('info'))
        { msg: @json(session('info')), type: 'info', icon: 'bi-info-circle-fill' },
        @endif
    ];
    var container = document.getElementById('toastContainer');
    if (!container) return;
    toasts.forEach(function (t) {
        var el = document.createElement('div');
        el.className = 'toast align-items-center text-bg-' + t.type + ' border-0';
        el.setAttribute('role', 'alert');
        el.setAttribute('aria-live', 'assertive');
        el.setAttribute('aria-atomic', 'true');
        el.setAttribute('data-bs-autohide', 'true');
        el.setAttribute('data-bs-delay', '5000');
        el.innerHTML = '<div class="d-flex"><div class="toast-body fw-semibold"><i class="bi ' + t.icon + ' me-2"></i>' + t.msg + '</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>';
        container.appendChild(el);
        if (window.bootstrap && bootstrap.Toast) {
            new bootstrap.Toast(el).show();
        }
    });
})();
</script>

@stack('scripts')

{{-- ── Real-time unread message indicator ─────────────────────────────────── --}}
@auth
<script>
(function () {
    var userId = {{ auth()->id() }};

    function setMsgCount(n) {
        var label  = n > 99 ? '99+' : n;
        var bd     = document.getElementById('msgBadgeDesktop');
        var bm     = document.getElementById('msgBadgeMobile');
        var dot    = document.getElementById('msgDotBottom');
        if (n > 0) {
            if (bd)  { bd.textContent = label; bd.classList.remove('d-none'); }
            if (bm)  { bm.textContent = label; bm.classList.remove('d-none'); }
            if (dot) { dot.classList.remove('d-none'); }
        } else {
            if (bd)  { bd.classList.add('d-none'); }
            if (bm)  { bm.classList.add('d-none'); }
            if (dot) { dot.classList.add('d-none'); }
        }
    }

    function currentCount() {
        var bd = document.getElementById('msgBadgeDesktop');
        if (!bd || bd.classList.contains('d-none')) return 0;
        return parseInt(bd.textContent) || 0;
    }

    function showToast(senderName, preview) {
        var container = document.getElementById('toastContainer');
        if (!container) return;
        var el = document.createElement('div');
        el.className = 'toast align-items-center text-bg-primary border-0';
        el.setAttribute('role', 'alert');
        el.setAttribute('aria-live', 'assertive');
        el.setAttribute('aria-atomic', 'true');
        el.setAttribute('data-bs-autohide', 'true');
        el.setAttribute('data-bs-delay', '5000');
        var safePreview = preview ? preview.replace(/</g,'&lt;').replace(/>/g,'&gt;') : '';
        el.innerHTML = '<div class="d-flex"><div class="toast-body fw-semibold">'
            + '<i class="bi bi-chat-heart-fill me-2"></i>'
            + '<strong>' + senderName.replace(/</g,'&lt;') + '</strong>'
            + (safePreview ? ': ' + safePreview : ' sent you a message')
            + '</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>';
        container.appendChild(el);
        if (window.bootstrap && bootstrap.Toast) { new bootstrap.Toast(el).show(); }
    }

    if (typeof Echo !== 'undefined') {
        Echo.private('App.Models.User.' + userId)
            .notification(function (n) {
                if (n.type === 'App\\Notifications\\NewMessageNotification') {
                    // Don't show dot if already on this conversation
                    var onConv = window.location.pathname.indexOf('/conversations/') !== -1;
                    var convId = n.conversation_id ? String(n.conversation_id) : '';
                    var onThisConv = onConv && window.location.pathname.indexOf('/' + convId) !== -1;
                    if (!onThisConv) {
                        setMsgCount(currentCount() + 1);
                        showToast(n.sender_name || 'Someone', n.preview || '');
                    }
                }
            });

        @if($callsEnabled)
        // Listen for missed calls via private channel (call-status-changed)
        Echo.private('user.' + userId)
            .listen('.call-status-changed', function (e) {
                if (e.status === 'missed' || e.status === 'rejected') {
                    updateMissedCallBadge();
                    if (e.status === 'missed') {
                        showCallToast('Missed call', '📵 You missed a call');
                    }
                }
            });
        @endif
    }

    @if($callsEnabled)
    // ── Missed call badge ─────────────────────────────────────────────────────
    function updateMissedCallBadge() {
        fetch('/calls/missed-count', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json(); })
            .then(function (d) {
                var badge = document.getElementById('missedCallBadgeNav');
                if (!badge) return;
                if (d.count > 0) {
                    badge.textContent = d.count > 9 ? '9+' : d.count;
                    badge.classList.remove('d-none');
                } else {
                    badge.classList.add('d-none');
                }
            })
            .catch(function () {});
    }

    function showCallToast(title, body) {
        var container = document.getElementById('toastContainer');
        if (!container) return;
        var el = document.createElement('div');
        el.className = 'toast align-items-center border-0';
        el.style.background = '#ef4444';
        el.style.color = '#fff';
        el.setAttribute('role', 'alert');
        el.setAttribute('aria-live', 'assertive');
        el.setAttribute('aria-atomic', 'true');
        el.setAttribute('data-bs-autohide', 'true');
        el.setAttribute('data-bs-delay', '7000');
        el.innerHTML = '<div class="d-flex"><div class="toast-body fw-semibold">' + body.replace(/</g,'&lt;')
            + ' — <a href="/calls" class="text-white fw-bold" style="text-decoration:underline">View history</a>'
            + '</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>';
        container.appendChild(el);
        if (window.bootstrap && bootstrap.Toast) { new bootstrap.Toast(el).show(); }
    }

    // Poll once on page load
    updateMissedCallBadge();
    @endif
})();
</script>
@endauth

{{-- ── Match Celebration Modal ─────────────────────────────────────────────── --}}
@if(session('like_matched'))
<div class="modal fade" id="matchModal" tabindex="-1" aria-labelledby="matchModalLabel" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 overflow-hidden text-center"
             style="background:linear-gradient(135deg,#ff6b9d 0%,#c44ee0 55%,#7b2ff7 100%)">
            <div class="modal-body p-5 text-white">
                <div style="font-size:4rem;animation:heartbeat 1.6s infinite">🎉</div>
                <h3 class="fw-bold mt-2 mb-1">It's a Match!</h3>
                <p class="opacity-85 mb-4">You and <strong>{{ session('success') ? '' : 'them' }}</strong> both liked each other!</p>
                <div class="d-flex gap-2 justify-content-center flex-wrap">
                    <a href="{{ route('conversations.index') }}"
                       class="btn btn-light fw-bold rounded-pill px-4"
                       data-bs-dismiss="modal">
                        <i class="bi bi-chat-heart-fill me-1 text-danger"></i>Send Message
                    </a>
                    <button type="button" class="btn btn-outline-light rounded-pill px-4" data-bs-dismiss="modal">
                        Keep Swiping
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var el = document.getElementById('matchModal');
        if (el && window.bootstrap) { new bootstrap.Modal(el).show(); }
    });
</script>
@endif

{{-- AOS init --}}
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>AOS.init({ duration: 700, once: true, offset: 60 });</script>

{{-- Dark / Light mode toggle --}}
<script>
(function () {
    var html = document.documentElement;
    var buttons = document.querySelectorAll('[data-theme-toggle]');
    var icons = document.querySelectorAll('[data-theme-icon]');

    function applyTheme(t) {
        html.setAttribute('data-bs-theme', t);
        localStorage.setItem('theme', t);
        icons.forEach(function (icon) {
            icon.className = t === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-stars-fill';
        });
        buttons.forEach(function (btn) {
            btn.title = t === 'dark' ? 'Switch to light mode' : 'Switch to dark mode';
        });
    }

    // Sync icon on load
    applyTheme(localStorage.getItem('theme') || 'light');

    buttons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            applyTheme(html.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark');
        });
    });
})();
</script>
<script>
(function () {
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/sw.js', {
                scope: '/'
            }).catch(function (err) {
                console.warn('Service Worker registration failed:', err);
            });
        });
    }
    // Capture the browser's install prompt so the install page can trigger it
    window.addEventListener('beforeinstallprompt', function (e) {
        e.preventDefault();
        window._pwaInstallPrompt = e;
        if (!window.matchMedia('(display-mode: standalone)').matches) {
            var banner = document.getElementById('pwaInstallNudge');
            if (banner) banner.classList.remove('d-none');
        }
    });
})();
</script>

@auth
{{-- PWA Badge API for message notifications --}}
<meta name="user-id" content="{{ auth()->id() }}">
<script src="{{ asset('js/pwa-badge.js') }}"></script>
@endauth

{{-- ── What's New Modal ───────────────────────────────────────────────────── --}}
<x-whats-new-modal />

{{-- ── Daily Streak auto-checkin ─────────────────────────────────────────── --}}
@auth
<script>
(function () {
    var today = new Date().toDateString();
    if (sessionStorage.getItem('streak_checked') === today) return;
    sessionStorage.setItem('streak_checked', today);
    fetch('{{ route("streak.checkin") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
    }).then(r => r.json()).then(data => {
        var el = document.getElementById('streakBadge');
        if (el) el.innerHTML = '🔥 ' + data.streak;
    }).catch(() => {});
})();
</script>
@endauth

</body>
</html>
