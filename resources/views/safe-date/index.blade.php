@extends('layouts.app')
@section('title', 'Safe Date Check-In')

@push('head')
<style>
    .sd-hero {
        background: linear-gradient(135deg, #22c55e 0%, #0ea5e9 60%, #6366f1 100%);
        border-radius: 1.25rem;
        color: #fff;
        padding: 2rem 1.5rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }
    .sd-hero::before {
        content: '🛡️';
        font-size: 7rem;
        position: absolute;
        right: 1rem; top: -.5rem;
        opacity: .15; pointer-events: none;
    }
    .status-badge-active { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
    .status-badge-safe   { background: #dbeafe; color: #1d4ed8; border: 1px solid #93c5fd; }
    .status-badge-alert  { background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; }
    .status-badge-cancelled { background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; }
    .countdown-ring { font-size: 1.1rem; font-weight: 700; }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-7">

            <div class="sd-hero">
                <h3 class="fw-bold mb-1"><i class="bi bi-shield-check me-2"></i>Safe Date Check-In</h3>
                <p class="mb-0 opacity-80">Before your date starts, set up a safety timer. If you don't check in on time, your emergency contact is alerted automatically.</p>
            </div>

            @if(session('success'))
            <div class="alert alert-success rounded-3 border-0 shadow-sm mb-4">{{ session('success') }}</div>
            @endif

            {{-- Active check-in --}}
            @if($active)
            <div class="card border-0 shadow rounded-3 mb-4" style="border-left: 4px solid #22c55e !important">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="fw-bold mb-0"><i class="bi bi-shield-fill-check text-success me-2"></i>Active Check-In</h5>
                        <span class="badge px-3 py-2 rounded-pill status-badge-active">🟢 Active</span>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <div class="text-muted small">📍 Location</div>
                            <div class="fw-semibold">{{ $active->date_location }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">⏰ Date starts</div>
                            <div class="fw-semibold">{{ $active->date_at->format('M j, Y g:i A') }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">👤 Emergency contact</div>
                            <div class="fw-semibold">{{ $active->emergency_contact_name }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">⏱️ Alert in</div>
                            <div class="fw-semibold text-warning" id="countdown">
                                {{ $active->checkin_minutes }} min after start
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('safe-date.safe', $active->id) }}" class="d-inline"
                          onsubmit="return confirm('Mark yourself as safe?')">
                        @csrf
                        <button class="btn btn-success rounded-pill px-4">
                            <i class="bi bi-check-circle me-2"></i>I'm Safe — Check In Now
                        </button>
                    </form>
                </div>
            </div>
            @else

            {{-- New check-in form --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-transparent fw-semibold py-3">
                    <i class="bi bi-plus-circle me-2 text-primary"></i>Set Up a New Check-In
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('safe-date.store') }}">
                        @csrf

                        @if($errors->any())
                        <div class="alert alert-danger rounded-3 mb-3">
                            <ul class="mb-0 ps-3">
                                @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                            </ul>
                        </div>
                        @endif

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">📍 Date Location</label>
                                <input type="text" name="date_location" class="form-control"
                                    value="{{ old('date_location') }}"
                                    placeholder="e.g. The Coffee House, Main Street" required maxlength="255">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">📅 Date & Time</label>
                                <input type="datetime-local" name="date_at" class="form-control"
                                    value="{{ old('date_at') }}" required
                                    min="{{ now()->addMinutes(5)->format('Y-m-d\TH:i') }}">
                                <div class="form-text">When does your date start?</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">⏱️ Alert After (minutes)</label>
                                <select name="checkin_minutes" class="form-select">
                                    @foreach([30, 60, 90, 120, 180, 240] as $m)
                                    @php
                                        $hours = floor($m / 60);
                                        $mins = $m % 60;
                                        $display = $hours >= 1 ? $hours . 'h' . ($mins > 0 ? ' ' . $mins . 'm' : '') : $m . 'm';
                                    @endphp
                                    <option value="{{ $m }}" {{ old('checkin_minutes', 120) == $m ? 'selected' : '' }}>{{ $m }} minutes ({{ $display }})</option>
                                    @endforeach
                                </select>
                                <div class="form-text">Alert fires if you don't check in after this long.</div>
                            </div>

                            <div class="col-12"><hr class="my-1"><p class="fw-semibold mb-0">Emergency Contact</p></div>
                            <div class="col-md-4">
                                <label class="form-label">Name</label>
                                <input type="text" name="emergency_contact_name" class="form-control"
                                    value="{{ old('emergency_contact_name') }}" required maxlength="100">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Phone (optional)</label>
                                <input type="tel" name="emergency_contact_phone" class="form-control"
                                    value="{{ old('emergency_contact_phone') }}" maxlength="30">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Email</label>
                                <input type="email" name="emergency_contact_email" class="form-control"
                                    value="{{ old('emergency_contact_email') }}" required maxlength="150">
                                <div class="form-text">Alert email is sent here if overdue.</div>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary rounded-pill px-5">
                                    <i class="bi bi-shield-check me-2"></i>Start Check-In
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            {{-- Recent check-ins --}}
            @if($recent->isNotEmpty())
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-transparent fw-semibold py-3">
                    <i class="bi bi-clock-history me-2"></i>Recent Check-Ins
                </div>
                <div class="list-group list-group-flush">
                    @foreach($recent as $c)
                    <div class="list-group-item px-4 py-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-semibold">{{ $c->date_location }}</div>
                                <div class="text-muted small">{{ $c->date_at->format('M j, Y g:i A') }}</div>
                            </div>
                            <span class="badge rounded-pill px-3 py-2 small
                                @if($c->status === 'safe') status-badge-safe
                                @elseif($c->status === 'alert_sent') status-badge-alert
                                @else status-badge-cancelled
                                @endif">
                                @if($c->status === 'safe') ✅ Safe
                                @elseif($c->status === 'alert_sent') ⚠️ Alert Sent
                                @else Cancelled
                                @endif
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
