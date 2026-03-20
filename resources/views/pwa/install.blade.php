@extends('layouts.app')
@section('title', 'Get the App')
@section('content')
@php $appName = \App\Models\SiteSetting::get('site_name', config('app.name')); @endphp

<div class="container py-5" style="max-width:680px">

    {{-- Already installed banner --}}
    <div id="alreadyInstalled" class="alert alert-success d-none mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        <strong>{{ $appName }}</strong> is already installed on your device!
        <a href="{{ route('dashboard') }}" class="alert-link ms-1">Open the app →</a>
    </div>

    <div class="text-center mb-5">
        @php $icon = \App\Models\SiteSetting::get('site_favicon'); @endphp
        @if($icon)
        <img src="{{ asset('storage/' . $icon) }}" alt="{{ $appName }}" width="80" height="80"
             class="rounded-3 shadow-sm mb-3" style="object-fit:contain">
        @else
        <div class="display-3 mb-3">💕</div>
        @endif
        <h1 class="fw-bold fs-2">Get the {{ $appName }} App</h1>
        <p class="text-muted">Install our app on your phone or computer for the best experience — works offline,
            loads instantly, and feels just like a native app.</p>
    </div>

    {{-- ─── AUTO: Chrome / Edge / Android ─────────────────────────────────── --}}
    <div id="autoInstallSection" class="card border-0 shadow-sm mb-4 d-none">
        <div class="card-body p-4 text-center">
            <div class="fs-1 mb-2">🚀</div>
            <h5 class="fw-bold mb-1">Install with one tap</h5>
            <p class="text-muted small mb-3">Your browser detected {{ $appName }} can be installed as an app.</p>
            <button id="autoInstallBtn" class="btn btn-primary btn-lg px-5 shadow-sm">
                <i class="bi bi-download me-2"></i>Install {{ $appName }}
            </button>
            <p class="text-muted small mt-3">No app store required. Installs directly to your device.</p>
        </div>
    </div>

    {{-- ─── MANUAL: Tabs for each platform ────────────────────────────────── --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-hand-index-thumb me-2 text-primary"></i>Manual Install Guide</h5>
            <ul class="nav nav-tabs" id="platformTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="android-tab" data-bs-toggle="tab"
                            data-bs-target="#android" type="button" role="tab">
                        <i class="bi bi-android2 me-1"></i>Android
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="ios-tab" data-bs-toggle="tab"
                            data-bs-target="#ios" type="button" role="tab">
                        <i class="bi bi-apple me-1"></i>iPhone / iPad
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="desktop-tab" data-bs-toggle="tab"
                            data-bs-target="#desktop" type="button" role="tab">
                        <i class="bi bi-display me-1"></i>Desktop
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body px-4 pb-4 pt-3">
            <div class="tab-content" id="platformTabContent">

                {{-- Android --}}
                <div class="tab-pane fade show active" id="android" role="tabpanel">
                    <ol class="list-group list-group-flush list-group-numbered">
                        <li class="list-group-item px-0">
                            Open <strong>{{ url('/') }}</strong> in
                            <strong>Chrome</strong> on your Android phone.
                        </li>
                        <li class="list-group-item px-0">
                            Tap the <strong>⋮ menu</strong> (three dots) in the top-right corner.
                        </li>
                        <li class="list-group-item px-0">
                            Tap <strong>"Add to Home screen"</strong> or <strong>"Install app"</strong>.
                        </li>
                        <li class="list-group-item px-0">
                            Tap <strong>"Add"</strong> in the confirmation dialog.
                        </li>
                        <li class="list-group-item px-0">
                            {{ $appName }} now appears on your home screen. Open it anytime — no browser bar! 🎉
                        </li>
                    </ol>
                </div>

                {{-- iOS --}}
                <div class="tab-pane fade" id="ios" role="tabpanel">
                    <div class="alert alert-info mb-3 small">
                        <i class="bi bi-info-circle me-1"></i>
                        On iPhone/iPad, use <strong>Safari</strong> — Chrome and other browsers on iOS do not support installation.
                    </div>
                    <ol class="list-group list-group-flush list-group-numbered">
                        <li class="list-group-item px-0">
                            Open <strong>{{ url('/') }}</strong> in <strong>Safari</strong>.
                        </li>
                        <li class="list-group-item px-0">
                            Tap the <strong>Share button</strong>
                            <span class="badge bg-secondary" style="font-size:.8rem">⎙</span>
                            at the bottom of the screen (the box with an arrow pointing up).
                        </li>
                        <li class="list-group-item px-0">
                            Scroll down in the share sheet and tap <strong>"Add to Home Screen"</strong>.
                        </li>
                        <li class="list-group-item px-0">
                            Edit the name if you like, then tap <strong>"Add"</strong> in the top-right corner.
                        </li>
                        <li class="list-group-item px-0">
                            {{ $appName }} now appears on your home screen with a full-screen experience! 🎉
                        </li>
                    </ol>
                </div>

                {{-- Desktop --}}
                <div class="tab-pane fade" id="desktop" role="tabpanel">
                    <div class="alert alert-warning mb-3 small">
                        <i class="bi bi-info-circle me-1"></i>
                        Desktop install works in <strong>Chrome</strong> and <strong>Edge</strong>. Firefox does not support PWA install.
                    </div>
                    <ol class="list-group list-group-flush list-group-numbered">
                        <li class="list-group-item px-0">
                            Open <strong>{{ url('/') }}</strong> in Chrome or Edge.
                        </li>
                        <li class="list-group-item px-0">
                            Look for the <strong>install icon</strong>
                            <span class="badge bg-secondary" style="font-size:.8rem">⊕</span>
                            in the browser's address bar (right side).
                        </li>
                        <li class="list-group-item px-0">
                            Click it and select <strong>"Install"</strong>.
                        </li>
                        <li class="list-group-item px-0">
                            <em>Alternatively:</em> Open the browser <strong>menu (⋮)</strong> → <strong>"Install {{ $appName }}"</strong>.
                        </li>
                        <li class="list-group-item px-0">
                            {{ $appName }} opens in its own window — like a native desktop app! 🎉
                        </li>
                    </ol>
                </div>

            </div>
        </div>
    </div>

    {{-- Benefits list --}}
    <div class="row g-3 text-center mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="fs-2">⚡</div>
                <div class="small fw-semibold mt-1">Instant load</div>
                <div class="text-muted" style="font-size:.72rem">No browser overhead</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="fs-2">🔔</div>
                <div class="small fw-semibold mt-1">Push alerts</div>
                <div class="text-muted" style="font-size:.72rem">Never miss a match</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="fs-2">📴</div>
                <div class="small fw-semibold mt-1">Works offline</div>
                <div class="text-muted" style="font-size:.72rem">Browse cached profiles</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="fs-2">🆓</div>
                <div class="small fw-semibold mt-1">Free forever</div>
                <div class="text-muted" style="font-size:.72rem">No app store needed</div>
            </div>
        </div>
    </div>

    <div class="text-center">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-house me-1"></i>Back to Dashboard
        </a>
    </div>

</div>
@endsection

@push('scripts')
<script>
(function () {
    // Check if already running as installed PWA
    if (window.matchMedia('(display-mode: standalone)').matches || navigator.standalone) {
        document.getElementById('alreadyInstalled').classList.remove('d-none');
        document.getElementById('autoInstallSection').classList.add('d-none');
        return;
    }

    // Auto-detect platform and activate the right tab
    var ua = navigator.userAgent;
    if (/iPhone|iPad|iPod/.test(ua)) {
        document.getElementById('ios-tab').click();
    } else if (/Android/.test(ua)) {
        document.getElementById('android-tab').click();
    }

    // If browser prompt is available, show the one-tap install button
    function checkPrompt() {
        if (window._pwaInstallPrompt) {
            document.getElementById('autoInstallSection').classList.remove('d-none');
        }
    }
    checkPrompt();
    window.addEventListener('beforeinstallprompt', checkPrompt);

    // One-tap install button handler
    document.getElementById('autoInstallBtn').addEventListener('click', function () {
        var prompt = window._pwaInstallPrompt;
        if (!prompt) return;
        prompt.prompt();
        prompt.userChoice.then(function (choice) {
            if (choice.outcome === 'accepted') {
                window._pwaInstallPrompt = null;
                document.getElementById('autoInstallSection').innerHTML =
                    '<div class="card-body p-4 text-center"><div class="fs-1 mb-2">✅</div>' +
                    '<h5 class="fw-bold">Installed successfully!</h5>' +
                    '<p class="text-muted small">Launch {{ $appName }} from your home screen anytime.</p></div>';
            }
        });
    });
})();
</script>
@endpush
