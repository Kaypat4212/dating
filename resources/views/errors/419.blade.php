<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Expired — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: radial-gradient(ellipse at 20% 20%, #3a0a4a 0%, #1a0533 40%, #0d0118 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            position: relative;
            padding: 1.5rem;
        }

        /* ── Floating love particles ──────────────────────────── */
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

        /* ── Card wrapper ─────────────────────────────────────── */
        .error-wrap {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 560px;
        }

        /* ── Brand header ─────────────────────────────────────── */
        .brand-row {
            text-align: center;
            margin-bottom: 1.75rem;
        }
        .brand-name {
            font-family: 'Playfair Display', serif;
            font-weight: 900;
            font-size: 1.9rem;
            background: linear-gradient(135deg, #f48fb1 0%, #ce93d8 55%, #ffd54f 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.5px;
        }
        .brand-tagline {
            color: rgba(255,255,255,0.35);
            font-size: 0.72rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-top: 0.25rem;
        }

        /* ── Glass card ───────────────────────────────────────── */
        .error-card {
            background: rgba(255,255,255,0.055);
            backdrop-filter: blur(28px);
            -webkit-backdrop-filter: blur(28px);
            border: 1px solid rgba(255,255,255,0.10);
            border-radius: 1.75rem;
            box-shadow:
                0 30px 80px rgba(0,0,0,0.55),
                0 0 0 1px rgba(255,255,255,0.04) inset,
                0 0 60px rgba(194,24,91,0.07) inset;
            padding: 2.5rem 2rem;
            text-align: center;
        }

        /* ── Error icon ───────────────────────────────────────── */
        .error-icon-ring {
            width: 88px;
            height: 88px;
            border-radius: 50%;
            background: linear-gradient(135deg, #c2185b 0%, #7b1fa2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 8px 32px rgba(194,24,91,0.45);
            animation: icon-beat 2.2s ease-in-out infinite;
        }
        @keyframes icon-beat {
            0%, 100% { transform: scale(1);    box-shadow: 0 8px 32px rgba(194,24,91,0.45); }
            45%       { transform: scale(1.08); box-shadow: 0 12px 42px rgba(194,24,91,0.65); }
        }
        .error-icon-ring i {
            font-size: 2.4rem;
            color: #fff;
        }

        /* ── Badge code ───────────────────────────────────────── */
        .error-badge {
            display: inline-block;
            background: linear-gradient(135deg, rgba(194,24,91,0.25), rgba(123,31,162,0.25));
            border: 1px solid rgba(194,24,91,0.4);
            color: #f48fb1;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            padding: 0.3rem 0.9rem;
            border-radius: 2rem;
            margin-bottom: 1rem;
        }

        .error-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.6rem, 5vw, 2.1rem);
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.75rem;
        }
        .error-subtitle {
            color: rgba(255,255,255,0.5);
            font-size: 0.95rem;
            line-height: 1.65;
            margin-bottom: 1.75rem;
        }

        /* ── Causes list ──────────────────────────────────────── */
        .causes-box {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 1rem;
            padding: 1.25rem 1.5rem;
            text-align: left;
            margin-bottom: 1.75rem;
        }
        .causes-box .causes-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #ce93d8;
            margin-bottom: 0.875rem;
        }
        .causes-box ul {
            padding-left: 1.25rem;
            margin: 0;
        }
        .causes-box li {
            color: rgba(255,255,255,0.55);
            font-size: 0.875rem;
            line-height: 1.55;
            margin-bottom: 0.55rem;
        }
        .causes-box li:last-child { margin-bottom: 0; }
        .causes-box li strong { color: rgba(255,255,255,0.8); }

        /* ── Countdown bar ────────────────────────────────────── */
        .countdown-wrap {
            margin-bottom: 1.6rem;
        }
        .countdown-text {
            color: rgba(255,255,255,0.38);
            font-size: 0.8rem;
            margin-bottom: 0.5rem;
        }
        .countdown-text span { color: #f48fb1; font-weight: 600; }
        .progress-track {
            height: 3px;
            background: rgba(255,255,255,0.1);
            border-radius: 99px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            width: 100%;
            background: linear-gradient(90deg, #c2185b, #ce93d8);
            border-radius: 99px;
            transform-origin: left;
            animation: shrink-bar 5s linear forwards;
        }
        @keyframes shrink-bar {
            from { transform: scaleX(1); }
            to   { transform: scaleX(0); }
        }

        /* ── Action buttons ───────────────────────────────────── */
        .action-row {
            display: flex;
            gap: 0.875rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn-refresh {
            background: linear-gradient(135deg, #c2185b 0%, #7b1fa2 100%);
            border: none;
            color: #fff;
            font-weight: 700;
            font-size: 0.9rem;
            padding: 0.75rem 1.75rem;
            border-radius: 0.85rem;
            cursor: pointer;
            letter-spacing: 0.2px;
            box-shadow: 0 6px 24px rgba(194,24,91,0.38);
            transition: transform 0.15s, box-shadow 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-refresh:hover {
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 10px 32px rgba(194,24,91,0.55);
        }
        .btn-ghost {
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.15);
            color: rgba(255,255,255,0.75);
            font-weight: 600;
            font-size: 0.9rem;
            padding: 0.75rem 1.75rem;
            border-radius: 0.85rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: background 0.15s, border-color 0.15s, transform 0.15s;
        }
        .btn-ghost:hover {
            background: rgba(255,255,255,0.12);
            border-color: rgba(255,255,255,0.28);
            color: #fff;
            transform: translateY(-2px);
        }

        /* ── Footer note ──────────────────────────────────────── */
        .safe-note {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.07);
            color: rgba(255,255,255,0.28);
            font-size: 0.78rem;
        }
        .safe-note i { color: #4ade80; }

        /* ── Responsive ───────────────────────────────────────── */
        @media (max-width: 480px) {
            body { padding: 1rem 0.875rem; }
            .error-card { padding: 2rem 1.25rem; }
            .action-row { flex-direction: column; }
            .btn-refresh, .btn-ghost { text-align: center; justify-content: center; }
        }
    </style>
</head>
<body>
    <div id="loveParticles"></div>

    <div class="error-wrap">

        {{-- Brand --}}
        <div class="brand-row">
            <div class="brand-name">
                <i class="bi bi-hearts" style="font-size:1.5rem;vertical-align:middle;-webkit-text-fill-color:#f48fb1;margin-right:0.3rem"></i>{{ config('app.name') }}
            </div>
            <p class="brand-tagline">Find your forever person</p>
        </div>

        <div class="error-card">

            {{-- Icon --}}
            <div class="error-icon-ring">
                <i class="bi bi-clock-history"></i>
            </div>

            {{-- Code + title --}}
            <div class="error-badge">Error 419</div>
            <h1 class="error-title">Session Expired</h1>
            <p class="error-subtitle">
                Your session has expired or the security token is no&nbsp;longer valid.
                This is a normal security measure to protect your account.
            </p>

            {{-- Causes --}}
            <div class="causes-box">
                <div class="causes-title">
                    <i class="bi bi-info-circle-fill"></i> Common causes
                </div>
                <ul>
                    <li><strong>Inactive too long:</strong> You've been away from the app for an extended period</li>
                    <li><strong>Multiple tabs / devices:</strong> Logging in elsewhere invalidated this session</li>
                    <li><strong>Browser back button:</strong> Navigating back after submitting a form</li>
                    <li><strong>Cleared cookies:</strong> Browser cookies or cache were recently cleared</li>
                    <li><strong>Cached page:</strong> You're viewing an outdated cached version</li>
                </ul>
            </div>

            {{-- Countdown --}}
            <div class="countdown-wrap">
                <p class="countdown-text">Auto-refreshing in <span id="cdNum">5</span>s…</p>
                <div class="progress-track"><div class="progress-fill" id="cdBar"></div></div>
            </div>

            {{-- Buttons --}}
            <div class="action-row">
                <button onclick="window.location.reload()" class="btn-refresh">
                    <i class="bi bi-arrow-clockwise"></i> Refresh Page
                </button>
                <a href="{{ route('login') }}" class="btn-ghost">
                    <i class="bi bi-box-arrow-in-right"></i> Login Again
                </a>
            </div>

            <div class="safe-note">
                <i class="bi bi-shield-check me-1"></i>
                Your data is safe — simply refresh or log in again to continue.
            </div>
        </div>
    </div>

    <script>
    // ── Floating love particles ───────────────────────────────────────
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

    // ── Countdown auto-reload ─────────────────────────────────────────
    let n = 5;
    const el = document.getElementById('cdNum');
    const t = setInterval(function () {
        n--;
        if (el) el.textContent = n;
        if (n <= 0) { clearInterval(t); window.location.reload(); }
    }, 1000);

    @auth
    if (document.referrer && document.referrer.includes('{{ url('/') }}')) {
        sessionStorage.setItem('attempted_action', document.referrer);
    }
    @endauth
    </script>
</body>
</html>
