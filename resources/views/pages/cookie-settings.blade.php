<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $siteName    = \App\Models\SiteSetting::get('site_name', config('app.name', 'HeartsConnect'));
        $faviconPath = \App\Models\SiteSetting::get('site_favicon');
        $faviconUrl  = $faviconPath ? asset('storage/' . $faviconPath) : asset('favicon.svg');
    @endphp
    <title>Cookie Settings — {{ $siteName }}</title>
    <link rel="icon" href="{{ $faviconUrl }}" type="{{ str_ends_with($faviconUrl, '.svg') ? 'image/svg+xml' : 'image/png' }}">
    @php($seoTitle = 'Cookie Settings')
    @include('partials.seo-meta')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    <style>
        body { background: #0d0118; color: rgba(255,255,255,.85); }
        .page-hero {
            background: linear-gradient(135deg, #1a1a0a 0%, #2a2a10 50%, #1a1a0a 100%);
            border-bottom: 1px solid rgba(255,255,255,.07);
            padding: 4rem 0 3rem;
        }
        .cookie-card {
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 1.25rem;
            padding: 1.75rem;
            margin-bottom: 1rem;
            transition: border-color .2s;
        }
        .cookie-card.required { border-color: rgba(72,199,142,.2); }
        .cookie-icon {
            width: 44px; height: 44px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.25rem;
            border-radius: .85rem;
        }
        /* Custom toggle switch */
        .cookie-toggle {
            position: relative;
            width: 52px;
            height: 28px;
            flex-shrink: 0;
        }
        .cookie-toggle input { opacity: 0; width: 0; height: 0; }
        .cookie-toggle-slider {
            position: absolute;
            cursor: pointer;
            inset: 0;
            background: rgba(255,255,255,.12);
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 28px;
            transition: .25s;
        }
        .cookie-toggle-slider::before {
            content: "";
            position: absolute;
            height: 20px; width: 20px;
            left: 3px; bottom: 3px;
            background: rgba(255,255,255,.6);
            border-radius: 50%;
            transition: .25s;
        }
        .cookie-toggle input:checked + .cookie-toggle-slider { background: #48c78e; border-color: #48c78e; }
        .cookie-toggle input:checked + .cookie-toggle-slider::before { left: 25px; background: #fff; }
        .cookie-toggle input:disabled + .cookie-toggle-slider { opacity: .6; cursor: not-allowed; }
        .save-btn {
            background: linear-gradient(135deg,#ff6b9d,#c44ee0);
            border: none;
            border-radius: .85rem;
            color: #fff;
            font-weight: 600;
            padding: .7rem 2.5rem;
            transition: opacity .2s, transform .15s;
        }
        .save-btn:hover { opacity: .9; transform: translateY(-1px); color: #fff; }
        .info-badge {
            display: inline-block;
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: .05em;
            padding: .15rem .55rem;
            border-radius: 2rem;
            text-transform: uppercase;
        }
        .table-cookies th {
            color: rgba(255,255,255,.5);
            font-size: .75rem;
            font-weight: 600;
            letter-spacing: .04em;
            text-transform: uppercase;
            padding: .65rem 1rem;
            background: rgba(255,255,255,.03);
            border-bottom: 1px solid rgba(255,255,255,.07);
        }
        .table-cookies td {
            padding: .65rem 1rem;
            font-size: .85rem;
            color: rgba(255,255,255,.55);
            border-bottom: 1px solid rgba(255,255,255,.04);
            vertical-align: top;
        }
        #savedAlert {
            display: none;
            background: rgba(72,199,142,.12);
            border: 1px solid rgba(72,199,142,.3);
            color: #6dda8d;
            border-radius: .75rem;
            padding: .75rem 1rem;
            margin-top: 1rem;
            font-size: .9rem;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand sticky-top shadow-sm" style="background:rgba(13,1,24,.92);backdrop-filter:blur(16px);border-bottom:1px solid rgba(255,255,255,.06)">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2 text-decoration-none" href="{{ auth()->check() ? route('dashboard') : url('/') }}">
            <x-site-logo size="md" />
        </a>
        <div class="ms-auto d-flex gap-2">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-light rounded-pill px-3">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-sm btn-outline-light rounded-pill px-3">Sign In</a>
                <a href="{{ route('register') }}" class="btn btn-sm rounded-pill px-3 fw-semibold" style="background:linear-gradient(135deg,#ff6b9d,#c44ee0);color:#fff;border:none">Join Free</a>
            @endauth
        </div>
    </div>
</nav>

<section class="page-hero text-center">
    <div class="container">
        <div class="display-5 mb-3">🍪</div>
        <h1 class="fw-bold mb-2">Cookie Settings</h1>
        <p class="opacity-75 mb-0" style="max-width:560px;margin:0 auto">We use cookies to keep the site working, improve your experience, and understand how people use {{ $siteName }}. Choose what you're comfortable with.</p>
    </div>
</section>

<div class="container py-5" style="max-width:780px">

    <p class="mb-4" style="color:rgba(255,255,255,.5);font-size:.9rem">
        A cookie is a small text file placed on your device. Some are essential for the site to function; others help us improve it. You can update these settings at any time.
        For full details see our <a href="{{ route('legal.privacy') }}" style="color:#f48fb1">Privacy Policy</a> and <a href="{{ route('legal.terms') }}#cookies" style="color:#f48fb1">Cookie Policy</a>.
    </p>

    {{-- Necessary --}}
    <div class="cookie-card required">
        <div class="d-flex align-items-start gap-3">
            <div class="cookie-icon flex-shrink-0" style="background:rgba(72,199,142,.12);color:#48c78e"><i class="bi bi-shield-check"></i></div>
            <div class="flex-grow-1">
                <div class="d-flex align-items-center justify-content-between mb-2 gap-2 flex-wrap">
                    <div>
                        <span class="fw-semibold me-2">Necessary Cookies</span>
                        <span class="info-badge" style="background:rgba(72,199,142,.15);color:#48c78e">Always On</span>
                    </div>
                    <label class="cookie-toggle">
                        <input type="checkbox" id="cookieNecessary" checked disabled>
                        <span class="cookie-toggle-slider"></span>
                    </label>
                </div>
                <p style="color:rgba(255,255,255,.5);font-size:.875rem;margin:0">These cookies are required for the site to work — they handle your login session, security tokens (CSRF), and language preferences. They cannot be disabled.</p>
            </div>
        </div>
    </div>

    {{-- Analytics --}}
    <div class="cookie-card">
        <div class="d-flex align-items-start gap-3">
            <div class="cookie-icon flex-shrink-0" style="background:rgba(100,149,237,.12);color:#6495ed"><i class="bi bi-bar-chart-line"></i></div>
            <div class="flex-grow-1">
                <div class="d-flex align-items-center justify-content-between mb-2 gap-2 flex-wrap">
                    <div>
                        <span class="fw-semibold me-2">Analytics Cookies</span>
                        <span class="info-badge" style="background:rgba(255,255,255,.07);color:rgba(255,255,255,.45)">Optional</span>
                    </div>
                    <label class="cookie-toggle">
                        <input type="checkbox" id="cookieAnalytics">
                        <span class="cookie-toggle-slider"></span>
                    </label>
                </div>
                <p style="color:rgba(255,255,255,.5);font-size:.875rem;margin:0">These cookies help us understand how visitors use the site — which pages are popular, how long sessions last, and where visitors come from. All data is aggregated and anonymised.</p>
            </div>
        </div>
    </div>

    {{-- Marketing --}}
    <div class="cookie-card">
        <div class="d-flex align-items-start gap-3">
            <div class="cookie-icon flex-shrink-0" style="background:rgba(244,143,177,.1);color:#f48fb1"><i class="bi bi-megaphone"></i></div>
            <div class="flex-grow-1">
                <div class="d-flex align-items-center justify-content-between mb-2 gap-2 flex-wrap">
                    <div>
                        <span class="fw-semibold me-2">Marketing Cookies</span>
                        <span class="info-badge" style="background:rgba(255,255,255,.07);color:rgba(255,255,255,.45)">Optional</span>
                    </div>
                    <label class="cookie-toggle">
                        <input type="checkbox" id="cookieMarketing">
                        <span class="cookie-toggle-slider"></span>
                    </label>
                </div>
                <p style="color:rgba(255,255,255,.5);font-size:.875rem;margin:0">Used to show relevant ads and content based on your interests when browsing other sites. Disabling these does not remove ads — it just makes them less personalised.</p>
            </div>
        </div>
    </div>

    {{-- Preferences --}}
    <div class="cookie-card">
        <div class="d-flex align-items-start gap-3">
            <div class="cookie-icon flex-shrink-0" style="background:rgba(255,193,7,.1);color:#ffc107"><i class="bi bi-sliders"></i></div>
            <div class="flex-grow-1">
                <div class="d-flex align-items-center justify-content-between mb-2 gap-2 flex-wrap">
                    <div>
                        <span class="fw-semibold me-2">Preference Cookies</span>
                        <span class="info-badge" style="background:rgba(255,255,255,.07);color:rgba(255,255,255,.45)">Optional</span>
                    </div>
                    <label class="cookie-toggle">
                        <input type="checkbox" id="cookiePreferences" checked>
                        <span class="cookie-toggle-slider"></span>
                    </label>
                </div>
                <p style="color:rgba(255,255,255,.5);font-size:.875rem;margin:0">Remember your personalised settings such as dark/light mode, language, and UI layout choices so you don't have to reconfigure them on each visit.</p>
            </div>
        </div>
    </div>

    {{-- Buttons --}}
    <div class="d-flex flex-wrap gap-3 mt-4 align-items-center">
        <button type="button" class="save-btn btn" id="savePrefs">
            <i class="bi bi-check-lg me-2"></i>Save My Preferences
        </button>
        <button type="button" class="btn rounded-pill px-4 fw-semibold" id="acceptAll"
            style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.12);color:rgba(255,255,255,.7)">
            Accept All
        </button>
        <button type="button" class="btn rounded-pill px-4 fw-semibold" id="rejectOptional"
            style="background:transparent;border:1px solid rgba(255,255,255,.1);color:rgba(255,255,255,.4)">
            Reject Optional
        </button>
    </div>
    <div id="savedAlert"><i class="bi bi-check-circle me-2"></i>Your cookie preferences have been saved.</div>

    {{-- Cookie table --}}
    <div class="mt-5 pt-4" style="border-top:1px solid rgba(255,255,255,.06)">
        <h5 class="fw-bold mb-3">Cookies we use</h5>
        <div class="rounded-3 overflow-hidden" style="border:1px solid rgba(255,255,255,.07)">
            <table class="table table-cookies mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Purpose</th>
                        <th>Expires</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code style="color:#f48fb1">{{ config('session.cookie', 'heartsconnect_session') }}</code></td>
                        <td><span class="info-badge" style="background:rgba(72,199,142,.1);color:#48c78e">Necessary</span></td>
                        <td>Maintains your login session</td>
                        <td>Session</td>
                    </tr>
                    <tr>
                        <td><code style="color:#f48fb1">XSRF-TOKEN</code></td>
                        <td><span class="info-badge" style="background:rgba(72,199,142,.1);color:#48c78e">Necessary</span></td>
                        <td>Protects against cross-site request forgery</td>
                        <td>Session</td>
                    </tr>
                    <tr>
                        <td><code style="color:#f48fb1">app_theme</code></td>
                        <td><span class="info-badge" style="background:rgba(255,193,7,.1);color:#ffc107">Preference</span></td>
                        <td>Stores your dark/light mode preference</td>
                        <td>1 year</td>
                    </tr>
                    <tr>
                        <td><code style="color:#f48fb1">app_rtl</code></td>
                        <td><span class="info-badge" style="background:rgba(255,193,7,.1);color:#ffc107">Preference</span></td>
                        <td>Stores text direction preference</td>
                        <td>1 year</td>
                    </tr>
                    <tr>
                        <td><code style="color:#f48fb1">cc_cookie</code></td>
                        <td><span class="info-badge" style="background:rgba(72,199,142,.1);color:#48c78e">Necessary</span></td>
                        <td>Remembers your cookie consent choices</td>
                        <td>6 months</td>
                    </tr>
                    <tr>
                        <td><code style="color:#f48fb1">_ga, _gid</code></td>
                        <td><span class="info-badge" style="background:rgba(100,149,237,.1);color:#6495ed">Analytics</span></td>
                        <td>Google Analytics — anonymised usage statistics</td>
                        <td>2 years / 24 h</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

@include('partials.footer')

<script>
(function () {
    // Read cc_cookie to pre-populate toggles
    try {
        var stored = JSON.parse(decodeURIComponent(getCookie('cc_cookie') || '{}'));
        var cats = stored.categories || ['necessary'];
        document.getElementById('cookieAnalytics').checked    = cats.includes('analytics');
        document.getElementById('cookieMarketing').checked    = cats.includes('marketing');
        document.getElementById('cookiePreferences').checked  = cats.includes('preferences');
    } catch (e) {}

    function getCookie(name) {
        var v = document.cookie.match('(^|;)\\s*' + name + '\\s*=\\s*([^;]+)');
        return v ? v.pop() : '';
    }

    function savePrefs() {
        var cats = ['necessary'];
        if (document.getElementById('cookieAnalytics').checked)   cats.push('analytics');
        if (document.getElementById('cookieMarketing').checked)    cats.push('marketing');
        if (document.getElementById('cookiePreferences').checked)  cats.push('preferences');

        var payload = { categories: cats, revision: 0, data: null,
            consentTimestamp: new Date().toISOString(),
            lastConsentTimestamp: new Date().toISOString() };
        var exp = new Date();
        exp.setMonth(exp.getMonth() + 6);
        document.cookie = 'cc_cookie=' + encodeURIComponent(JSON.stringify(payload))
            + '; expires=' + exp.toUTCString() + '; path=/; SameSite=Lax';

        var alert = document.getElementById('savedAlert');
        alert.style.display = 'flex';
        alert.style.alignItems = 'center';
        setTimeout(function () { alert.style.display = 'none'; }, 4000);
    }

    document.getElementById('savePrefs').addEventListener('click', savePrefs);

    document.getElementById('acceptAll').addEventListener('click', function () {
        document.getElementById('cookieAnalytics').checked   = true;
        document.getElementById('cookieMarketing').checked   = true;
        document.getElementById('cookiePreferences').checked = true;
        savePrefs();
    });

    document.getElementById('rejectOptional').addEventListener('click', function () {
        document.getElementById('cookieAnalytics').checked   = false;
        document.getElementById('cookieMarketing').checked   = false;
        document.getElementById('cookiePreferences').checked = false;
        savePrefs();
    });
})();
</script>
</body>
</html>
