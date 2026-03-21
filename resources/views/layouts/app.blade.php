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
            }
        }
        /* Desktop: absolute right-aligned under the button */
        @media (min-width: 992px) {
            .dropdown .navbar-dropdown-menu {
                position: absolute !important;
                right: 0 !important;
                left: auto !important;
                top: 100% !important;
                transform: none !important;
                min-width: 260px;
                z-index: 1050;
            }
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
    </style>
</head>
<body>

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
    if (auth()->check()) {
        $uid = auth()->id();
        $unreadMsgCount = \App\Models\Message::whereHas('conversation.match', function ($q) use ($uid) {
            $q->where('user1_id', $uid)->orWhere('user2_id', $uid);
        })->where('sender_id', '!=', $uid)->whereNull('read_at')->count();
    }
    $navPhoto = auth()->check() ? auth()->user()->primaryPhoto : null;
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
            <ul class="navbar-nav me-auto mt-2 mt-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active fw-semibold' : '' }}" href="{{ route('dashboard') }}">
                        <i class="bi bi-house-heart me-1"></i>Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('swipe.*') ? 'active fw-semibold' : '' }}" href="{{ route('swipe.deck') }}">
                        <i class="bi bi-fire me-1"></i>Swipe
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('discover.*') ? 'active fw-semibold' : '' }}" href="{{ route('discover.index') }}">
                        <i class="bi bi-search-heart me-1"></i>Browse
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('matches.*') ? 'active fw-semibold' : '' }}" href="{{ route('matches.index') }}">
                        <i class="bi bi-hearts me-1"></i>Matches
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('stories.*') ? 'active fw-semibold' : '' }}" href="{{ route('stories.index') }}">
                        <i class="bi bi-camera-video me-1"></i>Stories
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('wave.*') ? 'active fw-semibold' : '' }}" href="{{ route('wave.received') }}">
                        <i class="bi bi-hand-wave me-1"></i>Waves
                    </a>
                </li>
            </ul>

            {{-- Right-side actions --}}
            <div class="d-flex align-items-center gap-2 mt-2 mt-lg-0">
                {{-- Notifications (desktop only — mobile already shown above) --}}
                <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-outline-secondary position-relative d-none d-lg-inline-flex" title="Notifications">
                    <i class="bi bi-bell"></i>
                    @if($unreadCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.6rem">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                    @endif
                </a>
                <a href="{{ route('conversations.index') }}" class="btn btn-sm btn-outline-secondary d-none d-lg-inline-flex position-relative" title="Messages" id="navChatDesktop">
                    <i class="bi bi-chat-heart"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger{{ $unreadMsgCount > 0 ? '' : ' d-none' }}" id="msgBadgeDesktop" style="font-size:.6rem">{{ $unreadMsgCount > 99 ? '99+' : max($unreadMsgCount,1) }}</span>
                </a>
                {{-- Wallet balance (desktop) --}}
                <a href="{{ route('wallet.index') }}" class="btn btn-sm btn-outline-success d-none d-lg-inline-flex align-items-center gap-1 fw-semibold" title="Wallet Credits">
                    <i class="bi bi-coin"></i>
                    {{ number_format(auth()->user()->credit_balance) }} credits
                </a>

                <button class="btn btn-sm btn-outline-secondary" data-theme-toggle title="Toggle dark/light mode" aria-label="Toggle theme">
                    <i class="bi bi-moon-stars-fill" data-theme-icon></i>
                </button>

                {{-- User dropdown --}}
                <div class="dropdown" style="position:relative">
                    <button class="btn btn-sm btn-primary dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                        @if($navPhoto)
                            <img src="{{ $navPhoto->thumbnail_url }}" class="rounded-circle" width="26" height="26" alt="avatar" style="object-fit:cover">
                        @else
                            <i class="bi bi-person-circle fs-5"></i>
                        @endif
                        <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 navbar-dropdown-menu">
                        <li class="px-3 py-2 text-muted small">{{ auth()->user()->email }}</li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Profile</a></li>
                        <li><a class="dropdown-item" href="{{ route('account.show') }}"><i class="bi bi-gear me-2 text-secondary"></i>Settings</a></li>
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
    <a href="{{ route('matches.index') }}" class="{{ request()->routeIs('matches.*') ? 'active' : '' }}">
        <i class="bi bi-hearts"></i><span>Matches</span>
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
    }
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
            navigator.serviceWorker.register('{{ asset('sw.js') }}', {
                scope: '{{ rtrim(request()->getBasePath(), '/') }}/'
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
</body>
</html>
