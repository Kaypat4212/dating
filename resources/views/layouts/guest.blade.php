<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
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
    @php
        $appName = \App\Models\SiteSetting::get('site_name', config('app.name', 'HeartsConnect'));
        $fallbackTitle = \App\Models\SiteSetting::get('seo_default_title') ?: $appName;
    @endphp
    <title>@hasSection('title')@yield('title') &mdash; {{ $appName }}@else{{ $fallbackTitle }}@endif</title>
    @include('partials.seo-meta')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @vite(['resources/scss/app.scss', 'resources/js/app.js'])

    <style>
        /* ── Page background ──────────────────────────────────── */
        body.auth-page {
            background: radial-gradient(ellipse at 20% 20%, #3a0a4a 0%, #1a0533 40%, #0d0118 100%);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        /* ── Floating particles ───────────────────────────────── */
        #loveParticles {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }
        .lp {
            position: absolute;
            bottom: -40px;
            animation: lp-float linear infinite;
            opacity: 0;
            user-select: none;
            line-height: 1;
        }
        @keyframes lp-float {
            0%   { transform: translateY(0) scale(1) rotate(0deg);   opacity: 0; }
            8%   { opacity: 1; }
            85%  { opacity: 0.55; }
            100% { transform: translateY(-115vh) scale(0.4) rotate(220deg); opacity: 0; }
        }

        /* ── Auth card ────────────────────────────────────────── */
        .auth-card {
            background: rgba(255, 255, 255, 0.055);
            backdrop-filter: blur(28px);
            -webkit-backdrop-filter: blur(28px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 1.75rem;
            box-shadow:
                0 30px 80px rgba(0, 0, 0, 0.6),
                0 0 0 1px rgba(255, 255, 255, 0.04) inset,
                0 0 60px rgba(194, 24, 91, 0.07) inset;
        }

        /* ── Brand ────────────────────────────────────────────── */
        .auth-brand-name {
            font-family: 'Playfair Display', serif;
            font-weight: 900;
            font-size: 2.1rem;
            background: linear-gradient(135deg, #f48fb1 0%, #ce93d8 55%, #ffd54f 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.5px;
        }
        .auth-brand-icon {
            color: #f48fb1;
            font-size: 1.9rem;
            vertical-align: middle;
            -webkit-text-fill-color: #f48fb1;
        }
        .auth-tagline {
            color: rgba(255,255,255,0.38);
            font-size: 0.8rem;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        /* ── Form ─────────────────────────────────────────────── */
        .auth-heading { color: #fff; font-weight: 700; }
        .auth-subtitle { color: rgba(255,255,255,0.45); font-size: 0.875rem; }

        .auth-label {
            color: rgba(255,255,255,0.65);
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.4px;
            text-transform: uppercase;
        }

        .auth-input {
            background: rgba(255,255,255,0.07) !important;
            border: 1px solid rgba(255,255,255,0.12) !important;
            color: #fff !important;
            border-radius: 0.85rem !important;
            padding: 0.75rem 1rem !important;
            font-size: 0.95rem;
            transition: border-color .2s, box-shadow .2s, background .2s;
        }
        .auth-input::placeholder { color: rgba(255,255,255,0.22) !important; }
        .auth-input:focus {
            background: rgba(255,255,255,0.11) !important;
            border-color: #e91e63 !important;
            box-shadow: 0 0 0 0.2rem rgba(233,30,99,0.2) !important;
            color: #fff !important;
        }
        .auth-input:disabled {
            background: rgba(255,255,255,0.04) !important;
        }

        /* ── Submit button ────────────────────────────────────── */
        .auth-btn {
            background: linear-gradient(135deg, #c2185b 0%, #7b1fa2 100%) !important;
            border: none !important;
            border-radius: 0.85rem !important;
            font-weight: 700;
            font-size: 1rem;
            padding: 0.8rem;
            letter-spacing: 0.3px;
            box-shadow: 0 6px 28px rgba(194, 24, 91, 0.42) !important;
            transition: transform .15s, box-shadow .2s, opacity .15s !important;
            position: relative;
            overflow: hidden;
        }
        .auth-btn::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.12), transparent);
        }
        .auth-btn:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 12px 36px rgba(194, 24, 91, 0.55) !important;
        }
        .auth-btn:active { transform: translateY(0) !important; }

        /* ── Misc ─────────────────────────────────────────────── */
        .auth-link { color: #f48fb1 !important; font-weight: 600; }
        .auth-link:hover { color: #ffaed3 !important; }
        .auth-card .text-muted { color: rgba(255,255,255,0.38) !important; }
        .auth-card .form-check-input {
            background-color: rgba(255,255,255,0.1);
            border-color: rgba(255,255,255,0.25);
        }
        .auth-card .form-check-input:checked {
            background-color: #e91e63;
            border-color: #e91e63;
        }
        .auth-card .form-check-label { color: rgba(255,255,255,0.55); font-size: 0.85rem; }
        .auth-divider {
            border-color: rgba(255,255,255,0.1) !important;
        }
        .auth-card small.text-muted a,
        .auth-card p a { color: #f48fb1 !important; }
        .auth-card small.text-muted a:hover,
        .auth-card p a:hover { color: #ffaed3 !important; }
    </style>
</head>
<body class="auth-page">

    {{-- Floating love particles container --}}
    <div id="loveParticles"></div>

    <div class="container d-flex justify-content-center align-items-center position-relative" style="min-height:100vh;z-index:1">
        <div class="col-sm-11 col-md-8 col-lg-5 col-xl-4 py-5">

            {{-- Brand --}}
            <div class="text-center mb-4">
                <a href="{{ route('home') }}" class="text-decoration-none d-inline-block">
                    <div class="mb-1">
                        <x-site-logo size="lg" />
                    </div>
                    <p class="auth-tagline mb-0">Find your forever person</p>
                </a>
            </div>

            {{-- Flash messages (success / error) --}}
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3 rounded-4 border-0 shadow-sm" role="alert" style="background:rgba(25,135,84,.18);border:1px solid rgba(25,135,84,.3) !important;color:#d1fae5">
                <i class="bi bi-check-circle-fill fs-5" style="color:#4ade80"></i>
                <div class="flex-grow-1">{{ session('success') }}</div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-3 rounded-4 border-0 shadow-sm" role="alert" style="background:rgba(220,38,38,.18);border:1px solid rgba(220,38,38,.3) !important;color:#fee2e2">
                <i class="bi bi-exclamation-circle-fill fs-5" style="color:#f87171"></i>
                <div class="flex-grow-1">{{ session('error') }}</div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
            @endif
            @if(session('status'))
            <div class="alert alert-info alert-dismissible fade show d-flex align-items-center gap-2 mb-3 rounded-4 border-0 shadow-sm" role="alert" style="background:rgba(59,130,246,.18);border:1px solid rgba(59,130,246,.3) !important;color:#dbeafe">
                <i class="bi bi-info-circle-fill fs-5" style="color:#60a5fa"></i>
                <div class="flex-grow-1">{{ session('status') }}</div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
            @endif

            {{-- Card --}}
            <div class="auth-card p-4 p-sm-5">
                {{ $slot }}
            </div>

            {{-- Footer note --}}
            <p class="text-center mt-4" style="color:rgba(255,255,255,.22);font-size:.75rem">
                By joining you agree to our <a href="{{ route('legal.terms') }}" class="auth-link" style="font-size:.75rem">Terms</a> &amp; <a href="{{ route('legal.privacy') }}" class="auth-link" style="font-size:.75rem">Privacy Policy</a>
            </p>
        </div>
    </div>

    {{-- Toast container --}}
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index:1090" id="toastContainer"></div>

    {{-- Particle generator script --}}
    <script>
    (function () {
        const wrap    = document.getElementById('loveParticles');
        const symbols = ['♥','♡','✦','✧','✩','❣','💕','✨','☆','⋆'];
        const colors  = ['#f48fb1','#ce93d8','#ffd54f','#ef9a9a','#b39ddb','rgba(255,255,255,0.7)'];
        for (let i = 0; i < 35; i++) {
            const el  = document.createElement('span');
            el.className = 'lp';
            el.textContent = symbols[Math.floor(Math.random() * symbols.length)];
            const size  = (Math.random() * 16 + 9).toFixed(1);
            const left  = (Math.random() * 100).toFixed(2);
            const delay = (Math.random() * 18).toFixed(2);
            const dur   = (Math.random() * 14 + 12).toFixed(2);
            const blur  = Math.random() < 0.35 ? '1px' : '0';
            el.style.cssText = `left:${left}%;font-size:${size}px;color:${colors[Math.floor(Math.random()*colors.length)]};animation-duration:${dur}s;animation-delay:-${delay}s;filter:blur(${blur});`;
            wrap.appendChild(el);
        }
    })();
    </script>
    {{-- Toast auto-trigger from session --}}
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
            @if(session('status'))
            { msg: @json(session('status')), type: 'info', icon: 'bi-info-circle-fill' },
            @endif
        ];
        var container = document.getElementById('toastContainer');
        toasts.forEach(function (t) {
            var el = document.createElement('div');
            el.className = 'toast align-items-center text-bg-' + t.type + ' border-0 show';
            el.setAttribute('role', 'alert');
            el.setAttribute('aria-live', 'assertive');
            el.setAttribute('aria-atomic', 'true');
            el.setAttribute('data-bs-autohide', 'true');
            el.setAttribute('data-bs-delay', '4000');
            el.innerHTML = '<div class="d-flex"><div class="toast-body"><i class="bi ' + t.icon + ' me-2"></i>' + t.msg + '</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>';
            container.appendChild(el);
            if (window.bootstrap && bootstrap.Toast) {
                new bootstrap.Toast(el).show();
            }
        });
    })();
    </script>
</body>
</html>

