<x-filament-panels::page>

<style>
/* Action badge colours */
.al-badge { display:inline-flex;align-items:center;gap:.3rem;padding:.18rem .55rem;border-radius:9999px;font-size:.68rem;font-weight:700;letter-spacing:.03em;white-space:nowrap }
.al-badge-login        { background:rgba(59,130,246,.15);border:1px solid rgba(59,130,246,.3);color:#93c5fd }
.al-badge-like         { background:rgba(244,63,94,.15);border:1px solid rgba(244,63,94,.3);color:#fda4af }
.al-badge-message      { background:rgba(168,85,247,.15);border:1px solid rgba(168,85,247,.3);color:#d8b4fe }
.al-badge-match        { background:rgba(236,72,153,.15);border:1px solid rgba(236,72,153,.3);color:#f9a8d4 }
.al-badge-report       { background:rgba(239,68,68,.18);border:1px solid rgba(239,68,68,.35);color:#fca5a5 }
.al-badge-photo        { background:rgba(20,184,166,.15);border:1px solid rgba(20,184,166,.3);color:#5eead4 }
.al-badge-profile_view { background:rgba(250,204,21,.12);border:1px solid rgba(250,204,21,.25);color:#fde68a }
.al-badge-premium      { background:rgba(245,158,11,.18);border:1px solid rgba(245,158,11,.35);color:#fcd34d }
.al-badge-block        { background:rgba(107,114,128,.2);border:1px solid rgba(107,114,128,.35);color:#d1d5db }
.al-badge-wave         { background:rgba(99,102,241,.15);border:1px solid rgba(99,102,241,.3);color:#a5b4fc }
.al-badge-other        { background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.12);color:rgba(255,255,255,.5) }

.al-flag-suspicious  { background:rgba(239,68,68,.18);border:1px solid rgba(239,68,68,.35);color:#fca5a5 }
.al-flag-spam        { background:rgba(234,179,8,.18);border:1px solid rgba(234,179,8,.35);color:#fde047 }

.al-row { border-bottom:1px solid rgba(255,255,255,.05);transition:background .12s }
.al-row:hover { background:rgba(255,255,255,.03) }
.al-stat-card {
    border-radius:1rem;padding:1rem 1.25rem;
    background:linear-gradient(135deg,rgba(30,10,46,.8),rgba(45,16,80,.5));
    border:1px solid rgba(255,255,255,.07);
}
</style>

@php
    $stats      = $this->getStats();
    $activities = $this->getActivities();
    $actionTypes = $this->getActionTypes();
@endphp

{{-- ── Stats Row ──────────────────────────────────────────────────────────── --}}
<div class="mb-6 grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-6">

    @php
    $statCards = [
        ['label' => 'Actions Today',    'value' => $stats['total_today'],    'color' => '#a855f7', 'icon' => '📊'],
        ['label' => 'Logins Today',     'value' => $stats['logins_today'],   'color' => '#3b82f6', 'icon' => '🔑'],
        ['label' => 'Messages Today',   'value' => $stats['messages_today'], 'color' => '#d946ef', 'icon' => '💬'],
        ['label' => 'Reports Today',    'value' => $stats['reports_today'],  'color' => '#ef4444', 'icon' => '🚩'],
        ['label' => 'Suspicious Today', 'value' => $stats['suspicious'],     'color' => '#f59e0b', 'icon' => '⚠️'],
        ['label' => 'Flagged Users',    'value' => $stats['flagged_users'],  'color' => '#ec4899', 'icon' => '🛡️'],
    ];
    @endphp

    @foreach($statCards as $card)
        <div class="al-stat-card">
            <div class="mb-1 flex items-center gap-1.5">
                <span class="text-lg leading-none">{{ $card['icon'] }}</span>
                <span class="text-[11px] font-semibold uppercase tracking-wider" style="color:rgba(255,255,255,.4)">{{ $card['label'] }}</span>
            </div>
            <div class="text-2xl font-black" style="color:{{ $card['color'] }}">{{ number_format($card['value']) }}</div>
        </div>
    @endforeach
</div>

{{-- ── Filters ─────────────────────────────────────────────────────────────── --}}
<div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center"
     style="background:rgba(30,10,46,.6);border:1px solid rgba(255,255,255,.08);border-radius:1rem;padding:1rem 1.25rem">

    {{-- Search --}}
    <div class="flex-1">
        <div class="relative">
            <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2" style="color:rgba(255,255,255,.3)"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text"
                   wire:model.live.debounce.400ms="search"
                   placeholder="Search by name or email…"
                   class="w-full rounded-xl border-0 pl-9 pr-4 py-2 text-sm text-white placeholder-white/30 outline-none ring-1 ring-white/10 focus:ring-purple-500/50"
                   style="background:rgba(255,255,255,.05)" />
        </div>
    </div>

    {{-- Action type filter --}}
    <select wire:model.live="filterAction"
            class="rounded-xl border-0 px-3 py-2 text-sm text-white outline-none ring-1 ring-white/10 focus:ring-purple-500/50"
            style="background:rgba(255,255,255,.07);min-width:170px">
        <option value="">All Actions</option>
        @foreach($actionTypes as $type)
            <option value="{{ $type }}">{{ ucwords(str_replace('_', ' ', $type)) }}</option>
        @endforeach
    </select>

    {{-- Flag filter --}}
    <select wire:model.live="filterFlag"
            class="rounded-xl border-0 px-3 py-2 text-sm text-white outline-none ring-1 ring-white/10 focus:ring-purple-500/50"
            style="background:rgba(255,255,255,.07);min-width:150px">
        <option value="">All Flags</option>
        <option value="suspicious">⚠️ Suspicious</option>
        <option value="spam">🔴 Spam</option>
    </select>

    {{-- Per page --}}
    <select wire:model.live="perPage"
            class="rounded-xl border-0 px-3 py-2 text-sm text-white outline-none ring-1 ring-white/10 focus:ring-purple-500/50"
            style="background:rgba(255,255,255,.07);min-width:100px">
        <option value="25">25 / page</option>
        <option value="50">50 / page</option>
        <option value="100">100 / page</option>
    </select>

    {{-- Clear --}}
    @if($search || $filterAction || $filterFlag)
        <button wire:click="$set('search', ''); $set('filterAction', ''); $set('filterFlag', '')"
                class="rounded-xl px-3 py-2 text-xs font-semibold text-white/50 ring-1 ring-white/10 transition hover:text-white hover:ring-white/25"
                style="background:rgba(255,255,255,.04)">
            ✕ Clear
        </button>
    @endif
</div>

{{-- ── Table ─────────────────────────────────────────────────────────────── --}}
@php
    $badgeMap = [
        'login'        => ['class' => 'al-badge-login',        'icon' => '🔑'],
        'like'         => ['class' => 'al-badge-like',         'icon' => '❤️'],
        'message'      => ['class' => 'al-badge-message',      'icon' => '💬'],
        'match'        => ['class' => 'al-badge-match',        'icon' => '💞'],
        'report'       => ['class' => 'al-badge-report',       'icon' => '🚩'],
        'photo'        => ['class' => 'al-badge-photo',        'icon' => '📷'],
        'profile'      => ['class' => 'al-badge-profile_view', 'icon' => '👁️'],
        'premium'      => ['class' => 'al-badge-premium',      'icon' => '⭐'],
        'block'        => ['class' => 'al-badge-block',        'icon' => '🚫'],
        'wave'         => ['class' => 'al-badge-wave',         'icon' => '👋'],
    ];
    $getBadge = function(string $action) use ($badgeMap): array {
        foreach ($badgeMap as $key => $val) {
            if (str_contains($action, $key)) return $val;
        }
        return ['class' => 'al-badge-other', 'icon' => '⚡'];
    };
@endphp

<div class="overflow-hidden rounded-2xl"
     style="background:rgba(20,8,36,.5);border:1px solid rgba(255,255,255,.07)">

    {{-- Table header --}}
    <div class="grid items-center gap-2 px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest"
         style="grid-template-columns:2fr 1.5fr 1.2fr 1fr 1.5fr 1fr;border-bottom:1px solid rgba(255,255,255,.06);color:rgba(255,255,255,.3)">
        <span>User</span>
        <span>Action</span>
        <span>IP Address</span>
        <span>Flag</span>
        <span>Meta</span>
        <span class="text-right">Time</span>
    </div>

    {{-- Rows --}}
    @forelse($activities as $log)
        @php
            $user    = $log->user;
            $meta    = $log->meta ?? [];
            $metaStr = '';
            if (!empty($meta)) {
                $parts = [];
                foreach(array_slice($meta, 0, 3, true) as $k => $v) {
                    if (is_scalar($v)) $parts[] = str_replace('_', ' ', $k) . ': ' . $v;
                }
                $metaStr = implode(' · ', $parts);
            }
        @endphp
        <div class="al-row grid items-center gap-2 px-4 py-2.5"
             style="grid-template-columns:2fr 1.5fr 1.2fr 1fr 1.5fr 1fr">

            {{-- User --}}
            <div class="min-w-0">
                @if($user)
                    <div class="flex items-center gap-2">
                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-black text-white"
                             style="background:linear-gradient(135deg,{{ $user->is_suspicious ? '#ef4444' : '#7c3aed' }},{{ $user->is_suspicious ? '#b91c1c' : '#a855f7' }})">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <a href="{{ route('filament.admin.resources.users.edit', $user->id) }}"
                               class="truncate block text-xs font-semibold text-white/80 hover:text-white">
                                {{ $user->name }}
                            </a>
                            <p class="truncate text-[10px]" style="color:rgba(255,255,255,.3)">{{ $user->email }}</p>
                        </div>
                    </div>
                @else
                    <span class="text-xs" style="color:rgba(255,255,255,.2)">Deleted user</span>
                @endif
            </div>

            {{-- Action badge --}}
            <div>
                @php $badge = $getBadge($log->action); @endphp
                <span class="al-badge {{ $badge['class'] }}">
                    <span>{{ $badge['icon'] }}</span>
                    {{ ucwords(str_replace('_', ' ', $log->action)) }}
                </span>
            </div>

            {{-- IP --}}
            <div class="text-xs font-mono" style="color:rgba(255,255,255,.4)">
                {{ $log->ip_address ?? '—' }}
            </div>

            {{-- Flag --}}
            <div>
                @if($log->flag)
                    <span class="al-badge al-flag-{{ $log->flag }}">
                        {{ $log->flag === 'suspicious' ? '⚠️' : '🔴' }}
                        {{ ucfirst($log->flag) }}
                    </span>
                @else
                    <span class="text-[10px]" style="color:rgba(255,255,255,.18)">—</span>
                @endif
            </div>

            {{-- Meta --}}
            <div class="min-w-0">
                @if($metaStr)
                    <p class="truncate text-[10px]" style="color:rgba(255,255,255,.35)"
                       title="{{ json_encode($meta, JSON_PRETTY_PRINT) }}">
                        {{ $metaStr }}
                    </p>
                @else
                    <span class="text-[10px]" style="color:rgba(255,255,255,.18)">—</span>
                @endif
            </div>

            {{-- Time --}}
            <div class="text-right">
                <p class="text-xs" style="color:rgba(255,255,255,.45)" title="{{ $log->created_at->format('d M Y H:i:s') }}">
                    {{ $log->created_at->diffForHumans() }}
                </p>
                <p class="text-[10px]" style="color:rgba(255,255,255,.22)">{{ $log->created_at->format('d M, H:i') }}</p>
            </div>
        </div>
    @empty
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <div class="mb-3 text-5xl">📋</div>
            <p class="text-sm" style="color:rgba(255,255,255,.35)">No activity logs found.</p>
            @if($search || $filterAction || $filterFlag)
                <p class="mt-1 text-xs" style="color:rgba(255,255,255,.2)">Try changing your filters.</p>
            @endif
        </div>
    @endforelse
</div>

{{-- ── Pagination ──────────────────────────────────────────────────────────── --}}
@if($activities->hasPages())
    <div class="mt-4 flex items-center justify-between text-xs" style="color:rgba(255,255,255,.35)">
        <span>
            Showing {{ $activities->firstItem() }}–{{ $activities->lastItem() }}
            of {{ number_format($activities->total()) }} entries
        </span>
        <div class="flex items-center gap-1">
            {{-- Prev --}}
            @if($activities->onFirstPage())
                <span class="cursor-not-allowed rounded-lg px-3 py-1.5" style="background:rgba(255,255,255,.04);color:rgba(255,255,255,.2)">← Prev</span>
            @else
                <button wire:click="previousPage" wire:loading.attr="disabled"
                        class="rounded-lg px-3 py-1.5 transition hover:text-white"
                        style="background:rgba(255,255,255,.06)">← Prev</button>
            @endif

            {{-- Page numbers --}}
            @foreach($activities->getUrlRange(max(1, $activities->currentPage()-2), min($activities->lastPage(), $activities->currentPage()+2)) as $page => $url)
                @if($page === $activities->currentPage())
                    <span class="rounded-lg px-3 py-1.5 font-bold"
                          style="background:linear-gradient(135deg,#f43f5e,#a855f7);color:#fff">{{ $page }}</span>
                @else
                    <button wire:click="gotoPage({{ $page }})" wire:loading.attr="disabled"
                            class="rounded-lg px-3 py-1.5 transition hover:text-white"
                            style="background:rgba(255,255,255,.05)">{{ $page }}</button>
                @endif
            @endforeach

            {{-- Next --}}
            @if($activities->hasMorePages())
                <button wire:click="nextPage" wire:loading.attr="disabled"
                        class="rounded-lg px-3 py-1.5 transition hover:text-white"
                        style="background:rgba(255,255,255,.06)">Next →</button>
            @else
                <span class="cursor-not-allowed rounded-lg px-3 py-1.5" style="background:rgba(255,255,255,.04);color:rgba(255,255,255,.2)">Next →</span>
            @endif
        </div>
    </div>
@endif

</x-filament-panels::page>
