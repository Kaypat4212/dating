@extends('layouts.app')
@section('title', 'Get Verified')
@section('content')
<div class="container py-4" style="max-width:720px">

    {{-- ── Page header ──────────────────────────────────────────────────────── --}}
    <div class="text-center mb-5">
        <div class="mb-3">
            <span style="font-size:3.5rem">✅</span>
        </div>
        <h2 class="fw-bold">Identity Verification</h2>
        <p class="text-muted lead">Earn the <strong>Verified badge</strong> next to your name so everyone knows you're the real deal.</p>
    </div>

    {{-- ── Current status ───────────────────────────────────────────────────── --}}
    @if($user->is_verified)
    <div class="alert border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center gap-3"
         style="background:linear-gradient(135deg,#d1fae5,#a7f3d0)">
        <span style="font-size:2rem">✅</span>
        <div>
            <div class="fw-bold text-success fs-5">You're verified!</div>
            <div class="text-success-emphasis small">Your profile carries the <strong>Verified</strong> badge. Other members can see it while swiping and on your profile.</div>
        </div>
    </div>

    @elseif($verification && $verification->isPending())
    <div class="alert border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center gap-3"
         style="background:linear-gradient(135deg,#fef9c3,#fde68a)">
        <span style="font-size:2rem">⏳</span>
        <div>
            <div class="fw-bold text-warning-emphasis fs-5">Under Review</div>
            <div class="text-warning-emphasis small">Your documents were submitted on <strong>{{ $verification->created_at->format('M j, Y') }}</strong>. Our team usually responds within 24–48 hours. You'll receive a notification once reviewed.</div>
        </div>
    </div>

    @elseif($verification && $verification->isRejected())
    <div class="alert border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center gap-3"
         style="background:linear-gradient(135deg,#fee2e2,#fecaca)">
        <span style="font-size:2rem">❌</span>
        <div>
            <div class="fw-bold text-danger fs-5">Verification Not Approved</div>
            @if($verification->admin_notes)
            <div class="text-danger-emphasis small">Reason: <em>{{ $verification->admin_notes }}</em></div>
            @endif
            <div class="text-danger-emphasis small mt-1">Please re-submit with clearer, better-lit photos. Make sure the document text is fully readable.</div>
        </div>
    </div>
    @endif

    {{-- ── How it works ─────────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
        <h5 class="fw-bold mb-3"><i class="bi bi-question-circle me-2 text-primary"></i>How verification works</h5>
        <div class="row g-3">
            <div class="col-md-4 text-center">
                <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-2"
                     style="width:56px;height:56px;font-size:1.6rem">📸</div>
                <div class="fw-semibold small">Step 1 — Selfie</div>
                <div class="text-muted" style="font-size:.8rem">Take a clear, front-facing selfie in good lighting. No filters, sunglasses, or hats.</div>
            </div>
            <div class="col-md-4 text-center">
                <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-2"
                     style="width:56px;height:56px;font-size:1.6rem">🪪</div>
                <div class="fw-semibold small">Step 2 — ID Document</div>
                <div class="text-muted" style="font-size:.8rem">Upload a government-issued ID (passport, driver's licence, or national ID). All text must be readable.</div>
            </div>
            <div class="col-md-4 text-center">
                <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-2"
                     style="width:56px;height:56px;font-size:1.6rem">✅</div>
                <div class="fw-semibold small">Step 3 — Badge Granted</div>
                <div class="text-muted" style="font-size:.8rem">Our moderation team reviews submissions within 24–48 hours. Once approved, your badge goes live instantly.</div>
            </div>
        </div>
    </div>

    {{-- ── Benefits ──────────────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
        <h5 class="fw-bold mb-3"><i class="bi bi-star me-2 text-warning"></i>Why get verified?</h5>
        <ul class="list-unstyled mb-0">
            @foreach([
                ['bi-patch-check-fill text-info',    'Blue verified badge shown next to your name everywhere'],
                ['bi-shield-check text-success',     'Other members feel safer connecting with you'],
                ['bi-graph-up-arrow text-primary',   'Verified profiles appear higher in discovery'],
                ['bi-heart-fill text-danger',        'Get more likes and conversations'],
                ['bi-lock-fill text-secondary',      'Your documents are stored encrypted and never shared publicly'],
            ] as [$icon, $text])
            <li class="d-flex align-items-center gap-2 mb-2">
                <i class="bi {{ $icon }}"></i>
                <span class="small">{{ $text }}</span>
            </li>
            @endforeach
        </ul>
    </div>

    {{-- ── Privacy notice ───────────────────────────────────────────────────── --}}
    <div class="alert alert-secondary rounded-4 border-0 small mb-4">
        <i class="bi bi-lock me-2"></i><strong>Privacy:</strong> Your ID document is stored on a private, encrypted disk and is <strong>never visible</strong> to other users. It is only accessed by our moderation team for verification purposes and deleted after 90 days.
    </div>

    {{-- ── Upload form (only if not verified/pending) ───────────────────────── --}}
    @if(!$user->is_verified && (!$verification || $verification->isRejected()))
    <div class="card border-0 shadow-sm rounded-4 p-4">
        <h5 class="fw-bold mb-4"><i class="bi bi-upload me-2 text-primary"></i>Submit Your Verification</h5>

        <form method="POST" action="{{ route('verify.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label class="form-label fw-semibold">Your Selfie <span class="text-danger">*</span></label>
                <input type="file" name="selfie" class="form-control @error('selfie') is-invalid @enderror"
                       accept="image/jpeg,image/png,image/webp" required>
                <div class="form-text">JPG, PNG, or WEBP · max 5 MB · No sunglasses, filters, or hats</div>
                @error('selfie')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Government-Issued ID <span class="text-danger">*</span></label>
                <input type="file" name="id_document" class="form-control @error('id_document') is-invalid @enderror"
                       accept="image/jpeg,image/png,image/webp,application/pdf" required>
                <div class="form-text">Passport, driver's licence, or national ID · JPG, PNG, WEBP, or PDF · max 8 MB</div>
                @error('id_document')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" id="privacyConsent" required>
                <label class="form-check-label small" for="privacyConsent">
                    I consent to HeartsConnect storing my documents securely for identity verification purposes. I understand they will not be shared publicly and will be deleted after 90 days.
                </label>
            </div>

            <button type="submit" class="btn btn-primary rounded-3 px-4">
                <i class="bi bi-send me-2"></i>Submit for Verification
            </button>
        </form>
    </div>
    @endif

</div>
@endsection
