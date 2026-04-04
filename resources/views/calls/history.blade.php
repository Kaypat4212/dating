@extends('layouts.app')
@section('title', 'Call History')

@push('head')
<style>
.calls-page { max-width: 680px; margin: 0 auto; padding: 1.5rem 1rem 5rem; }

/* ── stat cards ──────────────────────────────────────────────────────── */
.call-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: .75rem; margin-bottom: 1.5rem; }
.stat-card {
    background: #fff;
    border-radius: 16px;
    padding: 1rem .75rem;
    text-align: center;
    box-shadow: 0 1px 6px rgba(0,0,0,.06);
    border: 1px solid var(--bs-border-color);
}
[data-bs-theme="dark"] .stat-card { background: var(--bs-dark); }
.stat-icon { font-size: 1.6rem; line-height: 1; margin-bottom: .35rem; }
.stat-value { font-size: 1.4rem; font-weight: 700; line-height: 1; }
.stat-label { font-size: .7rem; text-transform: uppercase; letter-spacing: .06em; color: var(--bs-secondary-color); margin-top: .2rem; }

/* ── filter tabs ─────────────────────────────────────────────────────── */
.filter-tabs { display: flex; gap: .4rem; margin-bottom: 1rem; flex-wrap: wrap; }
.filter-tab {
    padding: .3rem .85rem;
    border-radius: 20px;
    font-size: .8rem;
    font-weight: 500;
    border: 1px solid var(--bs-border-color);
    background: transparent;
    color: var(--bs-body-color);
    cursor: pointer;
    transition: background .15s, color .15s, border-color .15s;
}
.filter-tab.active, .filter-tab:hover {
    background: var(--bs-primary);
    color: #fff;
    border-color: var(--bs-primary);
}

/* ── call items ──────────────────────────────────────────────────────── */
.call-list { display: flex; flex-direction: column; gap: .5rem; }
.call-item {
    display: flex;
    align-items: center;
    gap: .85rem;
    background: #fff;
    border-radius: 16px;
    padding: .85rem 1rem;
    text-decoration: none;
    color: var(--bs-body-color);
    border: 1px solid var(--bs-border-color);
    box-shadow: 0 1px 4px rgba(0,0,0,.04);
    transition: box-shadow .15s, transform .15s;
}
.call-item:hover { box-shadow: 0 4px 16px rgba(0,0,0,.10); transform: translateY(-1px); color: inherit; }
[data-bs-theme="dark"] .call-item { background: var(--bs-dark); }

.call-avatar-wrap { position: relative; flex-shrink: 0; }
.call-avatar {
    width: 52px; height: 52px; border-radius: 50%; object-fit: cover;
}
.call-avatar-ph {
    width: 52px; height: 52px; border-radius: 50%;
    background: linear-gradient(135deg,#7c3aed,#a855f7);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-weight: 700; font-size: 1.2rem;
}
.call-direction-badge {
    position: absolute; bottom: -2px; right: -2px;
    width: 20px; height: 20px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: .65rem;
    border: 2px solid #fff;
}
[data-bs-theme="dark"] .call-direction-badge { border-color: var(--bs-dark); }
.badge-outgoing  { background: #10b981; color: #fff; }
.badge-incoming  { background: #6366f1; color: #fff; }
.badge-missed    { background: #ef4444; color: #fff; }
.badge-rejected  { background: #f59e0b; color: #fff; }

.call-body { flex: 1; overflow: hidden; }
.call-name { font-weight: 600; font-size: .95rem; line-height: 1.2; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.call-detail { font-size: .78rem; color: var(--bs-secondary-color); margin-top: .2rem; display: flex; align-items: center; gap: .4rem; }
.call-status-pill {
    display: inline-flex; align-items: center; gap: .25rem;
    font-size: .7rem; font-weight: 600; padding: .15rem .55rem;
    border-radius: 20px; white-space: nowrap;
}
.pill-ended    { background: #d1fae5; color: #065f46; }
.pill-missed   { background: #fee2e2; color: #991b1b; }
.pill-rejected { background: #fef3c7; color: #92400e; }
.pill-active   { background: #dbeafe; color: #1d4ed8; }
.pill-ringing  { background: #f3f4f6; color: #374151; }
[data-bs-theme="dark"] .pill-ended    { background: #064e3b; color: #6ee7b7; }
[data-bs-theme="dark"] .pill-missed   { background: #7f1d1d; color: #fca5a5; }
[data-bs-theme="dark"] .pill-rejected { background: #78350f; color: #fde68a; }
[data-bs-theme="dark"] .pill-active   { background: #1e3a8a; color: #93c5fd; }

.call-meta { flex-shrink: 0; text-align: right; }
.call-time { font-size: .75rem; color: var(--bs-secondary-color); }
.call-duration { font-size: .75rem; font-weight: 600; color: var(--bs-secondary-color); white-space: nowrap; }

/* ── empty state ─────────────────────────────────────────────────────── */
.empty-calls { text-align: center; padding: 4rem 1rem; color: var(--bs-secondary-color); }
.empty-calls .icon { font-size: 3.5rem; margin-bottom: 1rem; opacity: .4; }
</style>
@endpush

@section('content')
@php
    /** @var \App\Models\User $me */
    $me = auth()->user();

    $totalCalls    = $calls->total();
    $answeredCalls = $calls->getCollection()->filter(fn($c) => $c->status === 'ended')->count();
    $missedCalls   = $calls->getCollection()->filter(fn($c) => $c->status === 'missed')->count();

    // total duration this page (seconds)
    $totalSecs = $calls->getCollection()->sum(fn($c) => $c->durationSeconds() ?? 0);
    $totalMin  = floor($totalSecs / 60);
@endphp

<div class="calls-page">

    {{-- Header --}}
    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('conversations.index') }}" class="btn btn-sm btn-outline-secondary rounded-circle" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold mb-0"><i class="bi bi-telephone-fill text-success me-2"></i>Call History</h4>
            <div class="text-muted small">{{ $calls->total() }} call{{ $calls->total() !== 1 ? 's' : '' }} total</div>
        </div>
    </div>

    {{-- Stat cards --}}
    @if($totalCalls > 0)
    <div class="call-stats">
        <div class="stat-card">
            <div class="stat-icon">📞</div>
            <div class="stat-value">{{ $totalCalls }}</div>
            <div class="stat-label">Total</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="color:#ef4444">📵</div>
            <div class="stat-value text-danger">{{ $calls->getCollection()->filter(fn($c) => $c->status === 'missed')->count() }}</div>
            <div class="stat-label">Missed</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">⏱️</div>
            <div class="stat-value">{{ $totalMin }}<small class="fw-normal" style="font-size:.7em">m</small></div>
            <div class="stat-label">Talk time</div>
        </div>
    </div>
    @endif

    {{-- Filter tabs --}}
    <div class="filter-tabs" id="filterTabs">
        <button class="filter-tab active" data-filter="all">All</button>
        <button class="filter-tab" data-filter="outgoing">Outgoing</button>
        <button class="filter-tab" data-filter="incoming">Incoming</button>
        <button class="filter-tab" data-filter="missed">Missed</button>
    </div>

    {{-- Call list --}}
    @if($calls->isEmpty())
    <div class="empty-calls">
        <div class="icon">📞</div>
        <h5 class="fw-semibold mb-2">No calls yet</h5>
        <p class="mb-4">Start a call from any conversation to see history here.</p>
        <a href="{{ route('conversations.index') }}" class="btn btn-primary px-4 rounded-pill">
            <i class="bi bi-chat-heart me-2"></i>Open Messages
        </a>
    </div>
    @else
    <div class="call-list" id="callList">
        @foreach($calls as $call)
        @php
            $isOutgoing  = $call->caller_id === $me->id;
            $other       = $isOutgoing ? $call->callee : $call->caller;
            $direction   = $isOutgoing ? 'outgoing' : ($call->status === 'missed' ? 'missed' : ($call->status === 'rejected' ? 'rejected' : 'incoming'));
            $duration    = $call->durationSeconds();
            $convUrl     = $call->conversation_id ? route('conversations.show', $call->conversation_id) : '#';

            $dirLabel = match($direction) {
                'outgoing'  => 'Outgoing',
                'missed'    => 'Missed',
                'rejected'  => 'Declined',
                default     => 'Incoming',
            };
            $dirIcon = match($direction) {
                'outgoing'  => 'bi-telephone-outbound-fill',
                'missed'    => 'bi-telephone-missed-fill',
                'rejected'  => 'bi-telephone-x-fill',
                default     => 'bi-telephone-inbound-fill',
            };
            $badgeClass = match($direction) {
                'outgoing'  => 'badge-outgoing',
                'missed'    => 'badge-missed',
                'rejected'  => 'badge-rejected',
                default     => 'badge-incoming',
            };
            $pillClass  = match($call->status) {
                'ended'     => 'pill-ended',
                'missed'    => 'pill-missed',
                'rejected'  => 'pill-rejected',
                'active'    => 'pill-active',
                default     => 'pill-ringing',
            };
            $statusLabel = match($call->status) {
                'ended'     => 'Ended',
                'missed'    => 'Missed',
                'rejected'  => 'Rejected',
                'active'    => 'Active',
                default     => 'Ringing',
            };
        @endphp
        <a href="{{ $convUrl }}"
           class="call-item"
           data-direction="{{ $direction }}">

            {{-- Avatar --}}
            <div class="call-avatar-wrap">
                @if($other?->primaryPhoto)
                    <img src="{{ $other->primaryPhoto->thumbnail_url }}" alt="{{ $other->name }}" class="call-avatar">
                @else
                    <div class="call-avatar-ph">{{ strtoupper(mb_substr($other->name ?? '?', 0, 1)) }}</div>
                @endif
                <div class="call-direction-badge {{ $badgeClass }}">
                    <i class="bi {{ $dirIcon }}"></i>
                </div>
            </div>

            {{-- Body --}}
            <div class="call-body">
                <div class="call-name">
                    {{ $other->name ?? 'Unknown' }}
                    @if($other?->is_verified ?? false)
                        <i class="bi bi-patch-check-fill text-info ms-1" style="font-size:.7rem"></i>
                    @endif
                </div>
                <div class="call-detail">
                    <span class="call-status-pill {{ $pillClass }}">
                        <i class="bi {{ $dirIcon }}"></i>
                        {{ $dirLabel }}
                    </span>
                    @if($duration)
                        <span>·</span>
                        @php
                            $m = floor($duration / 60);
                            $s = $duration % 60;
                        @endphp
                        <span class="call-duration">
                            <i class="bi bi-clock"></i>
                            {{ $m > 0 ? $m.'m ' : '' }}{{ str_pad($s, 2, '0', STR_PAD_LEFT) }}s
                        </span>
                    @endif
                </div>
            </div>

            {{-- Meta --}}
            <div class="call-meta">
                <div class="call-time">
                    @if($call->created_at->isToday())
                        {{ $call->created_at->format('g:i A') }}
                    @elseif($call->created_at->isYesterday())
                        Yesterday
                    @else
                        {{ $call->created_at->format('M j') }}
                    @endif
                </div>
                @if($call->conversation_id)
                <div class="mt-1">
                    <span class="badge bg-secondary rounded-pill" style="font-size:.65rem">
                        <i class="bi bi-chat"></i>
                    </span>
                </div>
                @endif
            </div>
        </a>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($calls->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $calls->links() }}
    </div>
    @endif
    @endif
</div>
@endsection

@push('scripts')
<script>
(function () {
    var tabs  = document.querySelectorAll('.filter-tab');
    var items = document.querySelectorAll('#callList .call-item');

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            tabs.forEach(function (t) { t.classList.remove('active'); });
            tab.classList.add('active');
            var filter = tab.dataset.filter;
            items.forEach(function (item) {
                item.style.display = (filter === 'all' || item.dataset.direction === filter) ? '' : 'none';
            });
        });
    });
})();
</script>
@endpush
