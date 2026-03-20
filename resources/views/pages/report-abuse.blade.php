<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $siteName    = \App\Models\SiteSetting::get('site_name', config('app.name', 'HeartsConnect'));
        $safetyEmail = \App\Models\SiteSetting::get('legal_safety_email') ?: 'safety@heartsconnect.com';
        $supportEmail = \App\Models\SiteSetting::get('legal_support_email')
            ?: \App\Models\SiteSetting::get('footer_support_email')
            ?: ('support@' . (parse_url(config('app.url'), PHP_URL_HOST) ?? 'heartsconnect.com'));
        $faviconPath = \App\Models\SiteSetting::get('site_favicon');
        $faviconUrl  = $faviconPath ? asset('storage/' . $faviconPath) : asset('favicon.svg');
    @endphp
    <title>Report Abuse — {{ $siteName }}</title>
    <link rel="icon" href="{{ $faviconUrl }}" type="{{ str_ends_with($faviconUrl, '.svg') ? 'image/svg+xml' : 'image/png' }}">
    @php($seoTitle = 'Report Abuse')
    @include('partials.seo-meta')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    <style>
        body { background: #0d0118; color: rgba(255,255,255,.85); }
        .page-hero {
            background: linear-gradient(135deg, #2a0a0a 0%, #4a1a1a 50%, #2a0a0a 100%);
            border-bottom: 1px solid rgba(255,87,87,.12);
            padding: 4rem 0 3rem;
        }
        .report-card {
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 1.25rem;
            padding: 1.75rem;
            transition: border-color .2s, transform .2s;
        }
        .report-card:hover { border-color: rgba(255,87,87,.3); transform: translateY(-2px); }
        .report-icon {
            width: 48px; height: 48px;
            display: flex; align-items: center; justify-content: center;
            border-radius: .9rem;
            font-size: 1.3rem;
            margin-bottom: 1rem;
        }
        .report-card h5 { color: #fff; font-weight: 700; font-size: .95rem; margin-bottom: .5rem; }
        .report-card p { color: rgba(255,255,255,.5); font-size: .85rem; line-height: 1.75; margin: 0; }
        .step-circle {
            width: 36px; height: 36px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg,#ff6b9d,#c44ee0);
            border-radius: 50%;
            font-size: .8rem;
            font-weight: 700;
            color: #fff;
        }
        .step-item { display: flex; gap: 1rem; align-items: flex-start; }
        .step-item + .step-item { margin-top: 1.25rem; }
        .contact-btn {
            display: inline-flex;
            align-items: center;
            gap: .6rem;
            padding: .75rem 1.5rem;
            border-radius: .9rem;
            font-weight: 600;
            font-size: .9rem;
            text-decoration: none;
            transition: opacity .2s, transform .15s;
        }
        .contact-btn:hover { opacity: .88; transform: translateY(-1px); }
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
        <div class="display-5 mb-3">🚨</div>
        <h1 class="fw-bold mb-2">Report Abuse</h1>
        <p class="opacity-75 mb-0" style="max-width:560px;margin:0 auto">We have zero tolerance for harassment, scams, and harmful behaviour. All reports are reviewed by our safety team.</p>
    </div>
</section>

<div class="container py-5" style="max-width:960px">

    {{-- Emergency banner --}}
    <div class="d-flex align-items-start gap-3 p-3 mb-5 rounded-3" style="background:rgba(255,87,87,.08);border:1px solid rgba(255,87,87,.25)">
        <i class="bi bi-exclamation-octagon-fill fs-4 flex-shrink-0 mt-1" style="color:#ff5757"></i>
        <div>
            <strong style="color:#ff5757">In immediate danger?</strong>
            <span style="color:rgba(255,255,255,.65);font-size:.9rem"> Please call your local emergency services (911, 999, 112, etc.) first. Then contact us at <a href="mailto:{{ $safetyEmail }}" style="color:#ff9090">{{ $safetyEmail }}</a>.</span>
        </div>
    </div>

    <div class="row g-4">

        {{-- What to report --}}
        <div class="col-lg-7">
            <h4 class="fw-bold mb-4">What can I report?</h4>
            <div class="row g-3">
                @foreach([
                    ['icon'=>'bi bi-person-fill-slash','color'=>'rgba(255,87,87,.12)','ic'=>'#ff5757','title'=>'Fake Profiles','desc'=>'Catfishing, stolen photos, impersonation, or profiles pretending to be someone else.'],
                    ['icon'=>'bi bi-exclamation-diamond','color'=>'rgba(255,193,7,.1)','ic'=>'#ffc107','title'=>'Harassment & Threats','desc'=>'Bullying, threatening messages, hate speech, or any form of intimidation.'],
                    ['icon'=>'bi bi-currency-dollar','color'=>'rgba(255,87,87,.12)','ic'=>'#ff5757','title'=>'Scam / Romance Fraud','desc'=>'Money requests, investment scams, fake emergencies, or gift card requests.'],
                    ['icon'=>'bi bi-explicit','color'=>'rgba(255,87,87,.12)','ic'=>'#ff5757','title'=>'Inappropriate Content','desc'=>'Unsolicited explicit images, nudity, or sexual content sent without consent.'],
                    ['icon'=>'bi bi-person-fill-down','color'=>'rgba(100,149,237,.12)','ic'=>'#6495ed','title'=>'Underage Users','desc'=>'If you suspect a user is under 18 years old, report them immediately for urgent review.'],
                    ['icon'=>'bi bi-bandaid','color'=>'rgba(72,199,142,.1)','ic'=>'#48c78e','title'=>'Self-Harm Concerns','desc'=>'If a user expresses thoughts of self-harm or suicide, please let us know so we can help connect them to resources.'],
                ] as $item)
                <div class="col-sm-6">
                    <div class="report-card">
                        <div class="report-icon" style="background:{{ $item['color'] }};color:{{ $item['ic'] }}"><i class="{{ $item['icon'] }}"></i></div>
                        <h5>{{ $item['title'] }}</h5>
                        <p>{{ $item['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- How to report --}}
        <div class="col-lg-5">
            <h4 class="fw-bold mb-4">How to report</h4>

            <div class="mb-4">
                <div class="step-item">
                    <div class="step-circle">1</div>
                    <div>
                        <div class="fw-semibold mb-1" style="font-size:.95rem">Use the in-app report button</div>
                        <p style="color:rgba(255,255,255,.5);font-size:.85rem;margin:0">Go to any profile → tap the <em>⋮ menu</em> → <strong>Report</strong>. This is the fastest way to flag someone and our team is notified immediately.</p>
                    </div>
                </div>
                <div style="width:1px;height:24px;background:rgba(255,255,255,.1);margin-left:17px;margin-top:.35rem;margin-bottom:.35rem"></div>
                <div class="step-item">
                    <div class="step-circle">2</div>
                    <div>
                        <div class="fw-semibold mb-1" style="font-size:.95rem">Block the user</div>
                        <p style="color:rgba(255,255,255,.5);font-size:.85rem;margin:0">After reporting, block them so they can no longer contact you or view your profile.</p>
                    </div>
                </div>
                <div style="width:1px;height:24px;background:rgba(255,255,255,.1);margin-left:17px;margin-top:.35rem;margin-bottom:.35rem"></div>
                <div class="step-item">
                    <div class="step-circle">3</div>
                    <div>
                        <div class="fw-semibold mb-1" style="font-size:.95rem">Contact us directly</div>
                        <p style="color:rgba(255,255,255,.5);font-size:.85rem;margin:0">For urgent or complex matters, email our safety team directly with any screenshots or evidence.</p>
                    </div>
                </div>
            </div>

            <h6 class="fw-bold mb-3" style="color:rgba(255,255,255,.75)">Direct Contact</h6>
            <div class="d-flex flex-column gap-2">
                <a href="mailto:{{ $safetyEmail }}" class="contact-btn" style="background:rgba(255,87,87,.15);border:1px solid rgba(255,87,87,.3);color:#ff9090">
                    <i class="bi bi-shield-exclamation"></i>
                    Safety Team — {{ $safetyEmail }}
                </a>
                <a href="{{ route('pages.contact') }}" class="contact-btn" style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);color:rgba(255,255,255,.65)">
                    <i class="bi bi-envelope"></i>
                    General Support Form
                </a>
            </div>

            <div class="mt-4 p-3 rounded-3" style="background:rgba(72,199,142,.06);border:1px solid rgba(72,199,142,.15)">
                <p class="mb-0" style="color:rgba(255,255,255,.55);font-size:.8rem">
                    <i class="bi bi-info-circle me-1" style="color:#48c78e"></i>
                    All reports are <strong style="color:rgba(255,255,255,.75)">strictly confidential</strong>. The reported user is never told who filed the report. We review every submission, usually within 24 hours.
                </p>
            </div>
        </div>

    </div>
</div>

@include('partials.footer')
</body>
</html>
