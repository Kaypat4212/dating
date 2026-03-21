<x-filament-panels::page>

{{-- ── Styles ────────────────────────────────────────────────────────────── --}}
<style>
/* ── Page shell ──────────────────────────────────────────────────────── */
.sm-page {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

/* ── Two-column layout ───────────────────────────────────────────────── */
.sm-layout {
    display: flex;
    gap: 20px;
    align-items: flex-start;
}

/* ── Sidebar ─────────────────────────────────────────────────────────── */
.sm-sidebar {
    width: 280px;
    flex-shrink: 0;
    position: sticky;
    top: 20px;
}

/* ── Main panel ──────────────────────────────────────────────────────── */
.sm-main {
    flex: 1;
    min-width: 0;
}

/* Mobile: sidebar collapses */
.sm-sidebar-inner {
    display: none;
}
.sm-sidebar-inner.is-open {
    display: block;
}

/* On desktop-and-up: always show sidebar, hide toggle */
@media (min-width: 1024px) {
    .sm-sidebar-inner  { display: block !important; }
    .sm-mobile-toggle  { display: none !important; }
    .sm-layout         { flex-direction: row !important; }
}

/* On tablet and below: stack vertically, sidebar full width */
@media (max-width: 1023px) {
    .sm-sidebar { width: 100%; position: static; }
    .sm-layout  { flex-direction: column; }
}

/* ── Stat pills ──────────────────────────────────────────────────────── */
.sm-stat-pill {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 14px;
    border-radius: 12px;
    background: rgba(255,255,255,.04);
    border: 1px solid rgba(255,255,255,.08);
    flex: 1;
    min-width: 0;
}

/* ── Sidebar card ────────────────────────────────────────────────────── */
.sm-sidebar-card {
    background: linear-gradient(160deg,#1a0a2e 0%,#2d1050 60%,#1a0a2e 100%);
    border: 1px solid rgba(244,63,94,.2);
    border-radius: 16px;
    overflow: hidden;
}

/* ── Member list scrollable ──────────────────────────────────────────── */
.sm-member-list {
    max-height: 60vh;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: rgba(244,63,94,.3) transparent;
}
@media (min-width: 1024px) {
    .sm-member-list {
        max-height: calc(100vh - 260px);
    }
}
.sm-member-list::-webkit-scrollbar { width: 4px; }
.sm-member-list::-webkit-scrollbar-track { background: transparent; }
.sm-member-list::-webkit-scrollbar-thumb { background: rgba(244,63,94,.3); border-radius: 4px; }

/* ── Member row ──────────────────────────────────────────────────────── */
.sm-member-row {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    width: 100%;
    text-align: left;
    border: none;
    background: transparent;
    cursor: pointer;
    transition: background .15s;
    border-bottom: 1px solid rgba(255,255,255,.04);
}
.sm-member-row:hover  { background: rgba(255,255,255,.05); }
.sm-member-row.active {
    background: linear-gradient(135deg,rgba(244,63,94,.14),rgba(168,85,247,.08));
    border-left: 2.5px solid #f43f5e;
    padding-left: 11.5px;
}

/* ── Candidate cards ─────────────────────────────────────────────────── */
.sm-card {
    display: flex;
    flex-direction: column;
    border-radius: 18px;
    overflow: hidden;
    transition: transform .18s, box-shadow .18s;
}
.sm-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 48px rgba(0,0,0,.55) !important;
}

/* ── Score ring ──────────────────────────────────────────────────────── */
@keyframes ring-draw { from { stroke-dashoffset: 113; } }
.sm-ring-arc { animation: ring-draw .8s ease forwards; }

/* ── Animations ──────────────────────────────────────────────────────── */
@keyframes pulse-dot {
    0%,100% { opacity:1; box-shadow: 0 0 0 0 rgba(34,197,94,.5); }
    50%      { opacity:.7; box-shadow: 0 0 0 4px rgba(34,197,94,0); }
}
.sm-online { animation: pulse-dot 2s infinite; }

@keyframes shimmer {
    0%   { background-position:-400px 0 }
    100% { background-position: 400px 0 }
}
.sm-skeleton {
    background: linear-gradient(90deg,rgba(255,255,255,.04) 25%,rgba(255,255,255,.09) 50%,rgba(255,255,255,.04) 75%);
    background-size: 400px 100%;
    animation: shimmer 1.4s infinite;
    border-radius: 6px;
}

/* ── Tag chip ────────────────────────────────────────────────────────── */
.sm-chip {
    display: inline-flex;
    align-items: center;
    padding: 2px 9px;
    border-radius: 20px;
    font-size: 10px;
    font-weight: 500;
}
.sm-chip-purple {
    background: rgba(168,85,247,.14);
    border: 1px solid rgba(168,85,247,.25);
    color: #c4b5fd;
}
.sm-chip-teal {
    background: rgba(20,184,166,.14);
    border: 1px solid rgba(20,184,166,.25);
    color: #5eead4;
}

/* ── Match button ────────────────────────────────────────────────────── */
.sm-match-btn {
    display: block;
    width: 100%;
    padding: 9px 14px;
    border-radius: 12px;
    border: none;
    background: linear-gradient(135deg,#f43f5e,#a855f7);
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    text-align: center;
    cursor: pointer;
    transition: box-shadow .15s, opacity .15s;
    box-shadow: 0 4px 16px rgba(244,63,94,.3);
}
.sm-match-btn:hover   { box-shadow: 0 8px 24px rgba(244,63,94,.55); }
.sm-match-btn:active  { opacity: .88; }
.sm-match-btn:disabled{ opacity: .5; cursor: not-allowed; }

/* ── Grid ────────────────────────────────────────────────────────────── */
.sm-grid {
    display: grid;
    gap: 16px;
    grid-template-columns: 1fr;
}
@media (min-width: 580px)  { .sm-grid { grid-template-columns: repeat(2,1fr); } }
@media (min-width: 1280px) { .sm-grid { grid-template-columns: repeat(3,1fr); } }
</style>

@php $newUsers = $this->getNewUsers(); @endphp

<div class="sm-page">

    {{-- ── Top stats bar ─────────────────────────────────────────────── --}}
    <div class="flex flex-wrap gap-3">
        <div class="sm-stat-pill">
            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg"
                 style="background:linear-gradient(135deg,#f43f5e,#a855f7)">
                <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-3-3H5a3 3 0 00-3 3v2h5M9 11a4 4 0 110-8 4 4 0 010 8zm6 0a4 4 0 110-8 4 4 0 010 8z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-semibold text-white/40 uppercase tracking-wider leading-none mb-0.5">Members</p>
                <p class="text-sm font-black text-white leading-none">{{ $newUsers->count() }} recent</p>
            </div>
        </div>
        <div class="sm-stat-pill">
            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg"
                 style="background:linear-gradient(135deg,#10b981,#0d9488)">
                <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-semibold text-white/40 uppercase tracking-wider leading-none mb-0.5">AI Scoring</p>
                <p class="text-sm font-black text-white leading-none">Top 10 ranked</p>
            </div>
        </div>
        <div class="sm-stat-pill">
            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg"
                 style="background:linear-gradient(135deg,#3b82f6,#2563eb)">
                <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-semibold text-white/40 uppercase tracking-wider leading-none mb-0.5">Admin Match</p>
                <p class="text-sm font-black text-white leading-none">Force &amp; notify</p>
            </div>
        </div>
    </div>

    {{-- ── Mobile member toggle ────────────────────────────────────────── --}}
    <button
        class="sm-mobile-toggle flex w-full items-center justify-between rounded-2xl px-4 py-3"
        style="background:linear-gradient(135deg,rgba(244,63,94,.12),rgba(168,85,247,.08));border:1px solid rgba(244,63,94,.25);color:#fff"
        onclick="document.getElementById('sm-sidebar-inner').classList.toggle('is-open');
                 this.querySelector('.sm-chevron').style.transform = document.getElementById('sm-sidebar-inner').classList.contains('is-open') ? 'rotate(180deg)' : 'rotate(0deg)'"
    >
        <div class="flex items-center gap-3">
            <span class="flex h-7 w-7 items-center justify-center rounded-lg"
                  style="background:linear-gradient(135deg,#f43f5e,#a855f7)">
                <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-3-3H5a3 3 0 00-3 3v2h5M9 11a4 4 0 110-8 4 4 0 010 8zm6 0a4 4 0 110-8 4 4 0 010 8z"/>
                </svg>
            </span>
            <span class="font-bold text-sm">Recent Members</span>
            <span class="flex h-5 min-w-[1.25rem] items-center justify-center rounded-full px-1 text-[10px] font-black"
                  style="background:linear-gradient(135deg,#f43f5e,#a855f7);color:#fff">
                {{ $newUsers->count() }}
            </span>
        </div>
        <svg class="sm-chevron h-4 w-4 text-white/40 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    {{-- ── Main two-column layout ──────────────────────────────────────── --}}
    <div class="sm-layout">

        {{-- ──────────────────────── SIDEBAR ──────────────────────────── --}}
        <aside class="sm-sidebar">
            <div id="sm-sidebar-inner" class="sm-sidebar-inner sm-sidebar-card">

                {{-- Sidebar header --}}
                <div class="flex items-center justify-between px-4 py-3"
                     style="border-bottom:1px solid rgba(255,255,255,.07)">
                    <div>
                        <p class="text-xs font-black text-white/80">New Members</p>
                        <p class="text-[10px] text-white/30">{{ $newUsers->count() }} most recent complete profiles</p>
                    </div>
                    <span class="flex h-5 min-w-[1.25rem] items-center justify-center rounded-full px-1 text-[10px] font-black"
                          style="background:linear-gradient(135deg,#f43f5e,#a855f7);color:#fff">
                        {{ $newUsers->count() }}
                    </span>
                </div>

                {{-- Member list --}}
                @if($newUsers->isEmpty())
                    <div class="flex flex-col items-center justify-center px-4 py-10 text-center">
                        <span class="mb-2 text-3xl">👥</span>
                        <p class="text-xs text-white/30">No members found.</p>
                    </div>
                @else
                    <ul class="sm-member-list" role="listbox">
                        @foreach($newUsers as $nu)
                            @php
                                $photoUrl = $nu->primaryPhoto?->thumbnail_url;
                                $active   = $focusUserId === $nu->id;
                            @endphp
                            <li role="option" aria-selected="{{ $active ? 'true' : 'false' }}">
                                <button
                                    wire:click="selectUser({{ $nu->id }})"
                                    wire:loading.class="opacity-50 pointer-events-none"
                                    wire:target="selectUser({{ $nu->id }})"
                                    class="sm-member-row {{ $active ? 'active' : '' }}"
                                >
                                    {{-- Avatar --}}
                                    <div class="relative shrink-0">
                                        @if($photoUrl)
                                            <img src="{{ $photoUrl }}" alt="{{ $nu->name }}"
                                                 class="h-9 w-9 rounded-full object-cover"
                                                 style="box-shadow:0 0 0 2px {{ $active ? '#f43f5e' : 'rgba(255,255,255,.1)' }}" />
                                        @else
                                            <div class="flex h-9 w-9 items-center justify-center rounded-full text-sm font-black text-white"
                                                 style="background:linear-gradient(135deg,#2d1050,#4a0e6e);box-shadow:0 0 0 2px {{ $active ? '#f43f5e' : 'rgba(255,255,255,.1)' }}">
                                                {{ strtoupper(substr($nu->name,0,1)) }}
                                            </div>
                                        @endif
                                        @if(isset($nu->last_active_at) && \Carbon\Carbon::parse($nu->last_active_at)->diffInMinutes() < 30)
                                            <span class="sm-online absolute -bottom-0.5 -right-0.5 h-2.5 w-2.5 rounded-full border-2"
                                                  style="background:#22c55e;border-color:#1a0a2e"></span>
                                        @endif
                                    </div>

                                    {{-- Info --}}
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-1.5">
                                            <p class="truncate text-xs font-semibold {{ $active ? '' : 'text-white/75' }}"
                                               style="{{ $active ? 'color:#fb7185' : '' }}">{{ $nu->name }}</p>
                                            @if($nu->is_premium)
                                                <span class="shrink-0 rounded px-1 text-[9px] font-black" style="background:#f59e0b;color:#000">★</span>
                                            @endif
                                            @if($nu->is_verified)
                                                <span class="shrink-0 rounded px-1 text-[9px] font-bold" style="background:#0ea5e9;color:#fff">✓</span>
                                            @endif
                                        </div>
                                        <p class="text-[10px] text-white/30">
                                            {{ ucfirst($nu->gender ?? '—') }}{{ $nu->age ? ' · '.$nu->age.' yrs' : '' }}
                                        </p>
                                        <p class="text-[10px] text-white/20">{{ $nu->created_at->diffForHumans() }}</p>
                                    </div>

                                    @if($active)
                                        <svg class="h-4 w-4 shrink-0 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    @endif
                                </button>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </aside>

        {{-- ────────────────────────── MAIN PANEL ─────────────────────── --}}
        <div class="sm-main">

            @if(! $focusUserId)
                {{-- ── Empty / landing state ──────────────────────────── --}}
                <div class="flex flex-col items-center justify-center rounded-2xl py-16 text-center"
                     style="background:linear-gradient(160deg,rgba(30,10,46,.6),rgba(45,16,80,.4));border:1.5px dashed rgba(244,63,94,.2);min-height:320px">
                    <div class="mb-5 flex h-20 w-20 items-center justify-center rounded-full"
                         style="background:radial-gradient(circle,rgba(244,63,94,.15),rgba(168,85,247,.08));border:1.5px solid rgba(244,63,94,.2)">
                        <svg class="h-10 w-10" style="color:rgba(244,63,94,.5)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                    </div>
                    <h3 class="mb-2 text-base font-bold text-white/70">Select a member to begin</h3>
                    <p class="text-sm text-white/35 max-w-xs">
                        Choose any member from the list to see their AI-powered compatibility suggestions ranked by score.
                    </p>
                    <div class="mt-8 flex flex-wrap items-center justify-center gap-4 text-[11px] text-white/25">
                        <span class="flex items-center gap-1.5">
                            <span class="flex h-5 w-5 items-center justify-center rounded-full text-[9px]"
                                  style="background:rgba(244,63,94,.15)">🧠</span>AI scoring
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="flex h-5 w-5 items-center justify-center rounded-full text-[9px]"
                                  style="background:rgba(168,85,247,.15)">💞</span>Auto-match
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="flex h-5 w-5 items-center justify-center rounded-full text-[9px]"
                                  style="background:rgba(16,185,129,.15)">⚡</span>Instant notification
                        </span>
                    </div>
                </div>

            @else
                @php
                    $focusUser  = \App\Models\User::with(['profile.interests','primaryPhoto'])->find($focusUserId);
                    $focusPhoto = $focusUser?->primaryPhoto;
                @endphp

                @if(! $focusUser)
                    <div class="rounded-xl px-4 py-5 text-center text-sm"
                         style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#fca5a5">
                        ⚠ User not found.
                    </div>
                @else

                    {{-- ── Focus user hero ─────────────────────────────── --}}
                    <div class="mb-5 overflow-hidden rounded-2xl"
                         style="background:linear-gradient(140deg,#1e0a2e,#2d1050,#1a0a2e);border:1px solid rgba(244,63,94,.3);box-shadow:0 8px 32px rgba(244,63,94,.1)">
                        <div class="h-0.5 w-full" style="background:linear-gradient(90deg,#f43f5e,#a855f7,#3b82f6)"></div>
                        <div class="flex flex-col gap-4 p-4 sm:flex-row sm:items-center sm:gap-5 sm:p-5">

                            {{-- Avatar --}}
                            <div class="relative shrink-0 self-start sm:self-center">
                                @if($focusPhoto)
                                    <img src="{{ $focusPhoto->thumbnail_url }}" alt="{{ $focusUser->name }}"
                                         class="h-16 w-16 rounded-2xl object-cover"
                                         style="box-shadow:0 0 0 3px #f43f5e,0 8px 24px rgba(244,63,94,.35)" />
                                @else
                                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl text-2xl font-black text-white"
                                         style="background:linear-gradient(135deg,#f43f5e,#a855f7);box-shadow:0 8px 24px rgba(244,63,94,.35)">
                                        {{ strtoupper(substr($focusUser->name,0,1)) }}
                                    </div>
                                @endif
                                <span class="absolute -bottom-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full text-[10px]"
                                      style="background:linear-gradient(135deg,#f43f5e,#a855f7);box-shadow:0 2px 8px rgba(244,63,94,.5)">✦</span>
                            </div>

                            {{-- Details --}}
                            <div class="flex-1 min-w-0">
                                <div class="mb-1.5 flex flex-wrap items-center gap-2">
                                    <h2 class="text-lg font-black text-white leading-none">{{ $focusUser->name }}</h2>
                                    @if($focusUser->is_premium)
                                        <span class="rounded-full px-2 py-0.5 text-[10px] font-black"
                                              style="background:linear-gradient(90deg,#f59e0b,#f97316);color:#fff">★ PREMIUM</span>
                                    @endif
                                    @if($focusUser->is_verified)
                                        <span class="rounded-full px-2 py-0.5 text-[10px] font-bold"
                                              style="background:rgba(14,165,233,.2);border:1px solid rgba(14,165,233,.4);color:#7dd3fc">✓ VERIFIED</span>
                                    @endif
                                </div>
                                <div class="flex flex-wrap gap-x-3 gap-y-1 text-xs text-white/45">
                                    <span>{{ ucfirst($focusUser->gender ?? 'Unknown') }}</span>
                                    @if($focusUser->age)<span>· {{ $focusUser->age }} yrs</span>@endif
                                    <span>· Seeking <strong class="text-white/65">{{ ucfirst($focusUser->seeking ?? 'everyone') }}</strong></span>
                                    @if($focusUser->profile?->city)
                                        <span>· 📍 {{ $focusUser->profile->city }}{{ $focusUser->profile->country ? ', '.$focusUser->profile->country : '' }}</span>
                                    @endif
                                </div>
                                @if($focusUser->profile?->headline)
                                    <p class="mt-1.5 text-xs italic text-white/30">"{{ Str::limit($focusUser->profile->headline,80) }}"</p>
                                @endif
                            </div>

                            {{-- Joined --}}
                            <div class="shrink-0 sm:text-right">
                                <p class="text-[10px] font-semibold uppercase tracking-widest text-white/25">Joined</p>
                                <p class="text-sm font-bold text-white/60">{{ $focusUser->created_at->format('d M Y') }}</p>
                                <p class="text-[10px] text-white/25">{{ $focusUser->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- ── Suggestions ─────────────────────────────────── --}}
                    @if($suggestions->isEmpty())
                        <div class="flex flex-col items-center justify-center rounded-2xl py-14 text-center"
                             style="background:rgba(255,255,255,.02);border:1.5px dashed rgba(255,255,255,.08)">
                            <span class="mb-3 text-4xl">🤷</span>
                            <p class="text-sm font-semibold text-white/45">No compatible candidates found</p>
                            <p class="mt-1 text-xs text-white/25">All suitable users may already be matched, or this user's profile lacks preference data.</p>
                        </div>
                    @else

                        {{-- Count + legend header --}}
                        <div class="mb-4 flex flex-wrap items-center gap-2">
                            <p class="text-sm font-semibold text-white/55">
                                <span class="font-black text-white">{{ $suggestions->count() }}</span> compatibility {{ Str::plural('match', $suggestions->count()) }} found
                            </p>
                            <div class="h-px flex-1" style="background:linear-gradient(90deg,rgba(244,63,94,.25),transparent);min-width:20px"></div>
                            <div class="flex flex-wrap items-center gap-3 text-[10px] text-white/30">
                                <span class="flex items-center gap-1">
                                    <span class="h-2 w-2 rounded-full bg-emerald-400 inline-block"></span>≥70% good
                                </span>
                                <span class="flex items-center gap-1">
                                    <span class="h-2 w-2 rounded-full bg-amber-400 inline-block"></span>≥40% fair
                                </span>
                                <span class="flex items-center gap-1">
                                    <span class="h-2 w-2 rounded-full inline-block" style="background:#6b7280"></span>&lt;40% low
                                </span>
                            </div>
                        </div>

                        {{-- Cards grid --}}
                        <div class="sm-grid">
                            @foreach($suggestions as $rank => $item)
                                @php
                                    $candidate = $item['user'];
                                    $score     = $item['score'];
                                    $candPhoto = $candidate->primaryPhoto;
                                    $interests = $candidate->profile?->interests ?? collect();

                                    [$barColor, $cardBg, $scoreColor] = match(true) {
                                        $score >= 70 => [
                                            '#10b981',
                                            'background:linear-gradient(160deg,rgba(16,185,129,.06),rgba(5,150,105,.03));border:1px solid rgba(16,185,129,.22)',
                                            '#34d399',
                                        ],
                                        $score >= 40 => [
                                            '#f59e0b',
                                            'background:linear-gradient(160deg,rgba(245,158,11,.06),rgba(217,119,6,.03));border:1px solid rgba(245,158,11,.18)',
                                            '#fbbf24',
                                        ],
                                        default => [
                                            '#6b7280',
                                            'background:rgba(255,255,255,.025);border:1px solid rgba(255,255,255,.07)',
                                            '#9ca3af',
                                        ],
                                    };

                                    $sharedInterests = array_intersect(
                                        $focusUser->profile?->interests->pluck('name')->toArray() ?? [],
                                        $interests->pluck('name')->toArray()
                                    );
                                    $tags = array_values(array_filter([
                                        $candidate->profile?->relationship_goal ?? null,
                                        $candidate->profile?->religion ?? null,
                                        $candidate->profile?->education ?? null,
                                    ]));
                                @endphp

                                <div class="sm-card" style="{{ $cardBg }};box-shadow:0 4px 20px rgba(0,0,0,.3)">

                                    {{-- Photo banner --}}
                                    @if($candPhoto)
                                        <div class="relative overflow-hidden" style="height:130px">
                                            <img src="{{ $candPhoto->thumbnail_url }}" alt="{{ $candidate->name }}"
                                                 class="h-full w-full object-cover"
                                                 style="filter:brightness(.72)" />
                                            <div class="absolute inset-0"
                                                 style="background:linear-gradient(to top,rgba(0,0,0,.88) 0%,rgba(0,0,0,.1) 55%,transparent 100%)"></div>
                                            {{-- Rank badge --}}
                                            <span class="absolute left-3 top-3 flex h-6 w-6 items-center justify-center rounded-full text-[10px] font-black text-white"
                                                  style="background:rgba(0,0,0,.55);backdrop-filter:blur(6px);border:1px solid rgba(255,255,255,.18)">#{{ $rank + 1 }}</span>
                                            {{-- Score ring --}}
                                            <div class="absolute right-3 top-2.5">
                                                <svg width="46" height="46" viewBox="0 0 46 46">
                                                    <circle cx="23" cy="23" r="18" fill="rgba(0,0,0,.4)" stroke="rgba(255,255,255,.1)" stroke-width="3"/>
                                                    <circle cx="23" cy="23" r="18" fill="none" stroke="{{ $barColor }}" stroke-width="3"
                                                            stroke-dasharray="113" stroke-dashoffset="{{ max(0, 113 - ($score / 100 * 113)) }}"
                                                            stroke-linecap="round" transform="rotate(-90 23 23)"
                                                            class="sm-ring-arc"/>
                                                    <text x="23" y="27.5" text-anchor="middle" font-size="9" font-weight="900" fill="{{ $barColor }}">{{ $score }}%</text>
                                                </svg>
                                            </div>
                                            {{-- Name over photo --}}
                                            <div class="absolute bottom-2.5 left-3 right-3">
                                                <p class="truncate text-sm font-bold text-white leading-tight">{{ $candidate->name }}</p>
                                                <p class="truncate text-[10px] text-white/50">
                                                    {{ ucfirst($candidate->gender ?? '?') }}{{ $candidate->age ? ' · '.$candidate->age.' yrs' : '' }}
                                                </p>
                                            </div>
                                        </div>
                                    @else
                                        {{-- Gradient placeholder banner --}}
                                        <div class="relative flex items-center justify-center overflow-hidden" style="height:90px;background:linear-gradient(135deg,#2d1050,#4a0e6e)">
                                            <span class="select-none text-[3.5rem] font-black text-white/10">{{ strtoupper(substr($candidate->name,0,1)) }}</span>
                                            <span class="absolute left-3 top-2.5 flex h-6 w-6 items-center justify-center rounded-full text-[10px] font-black text-white"
                                                  style="background:rgba(0,0,0,.5);border:1px solid rgba(255,255,255,.18)">#{{ $rank + 1 }}</span>
                                            <div class="absolute right-3 top-2">
                                                <svg width="46" height="46" viewBox="0 0 46 46">
                                                    <circle cx="23" cy="23" r="18" fill="rgba(0,0,0,.3)" stroke="rgba(255,255,255,.1)" stroke-width="3"/>
                                                    <circle cx="23" cy="23" r="18" fill="none" stroke="{{ $barColor }}" stroke-width="3"
                                                            stroke-dasharray="113" stroke-dashoffset="{{ max(0, 113 - ($score / 100 * 113)) }}"
                                                            stroke-linecap="round" transform="rotate(-90 23 23)"
                                                            class="sm-ring-arc"/>
                                                    <text x="23" y="27.5" text-anchor="middle" font-size="9" font-weight="900" fill="{{ $barColor }}">{{ $score }}%</text>
                                                </svg>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Card body --}}
                                    <div class="flex flex-1 flex-col gap-2.5 px-3.5 pt-3 pb-2">

                                        {{-- Name (only when no photo) + meta --}}
                                        @if(! $candPhoto)
                                            <div>
                                                <p class="text-sm font-bold text-white">{{ $candidate->name }}</p>
                                                <p class="text-[10px] text-white/40">
                                                    {{ ucfirst($candidate->gender ?? '?') }}{{ $candidate->age ? ' · '.$candidate->age.' yrs' : '' }}
                                                    @if($candidate->is_premium)
                                                        <span class="ml-1 rounded px-1 text-[9px] font-black" style="background:#f59e0b;color:#000">★</span>
                                                    @endif
                                                </p>
                                            </div>
                                        @else
                                            @if($candidate->is_premium)
                                                <span class="self-start rounded px-1.5 py-0.5 text-[9px] font-black" style="background:#f59e0b;color:#000">★ PREMIUM</span>
                                            @endif
                                        @endif

                                        {{-- Location --}}
                                        @if($candidate->profile?->city)
                                            <p class="truncate text-[10px] text-white/30">
                                                📍 {{ $candidate->profile->city }}{{ $candidate->profile->country ? ', '.$candidate->profile->country : '' }}
                                            </p>
                                        @endif

                                        {{-- Headline --}}
                                        @if($candidate->profile?->headline)
                                            <p class="text-[10px] italic text-white/28 leading-relaxed">"{{ Str::limit($candidate->profile->headline,60) }}"</p>
                                        @endif

                                        {{-- Profile tags --}}
                                        @if(count($tags))
                                            <div class="flex flex-wrap gap-1">
                                                @foreach(array_slice($tags, 0, 3) as $tag)
                                                    <span class="sm-chip sm-chip-purple capitalize">{{ $tag }}</span>
                                                @endforeach
                                            </div>
                                        @endif

                                        {{-- Shared interests --}}
                                        @if(count($sharedInterests) > 0)
                                            <div>
                                                <p class="mb-1.5 text-[9px] font-semibold uppercase tracking-wider text-white/20">Shared interests</p>
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach(array_slice($sharedInterests, 0, 4) as $int)
                                                        <span class="sm-chip sm-chip-teal">{{ $int }}</span>
                                                    @endforeach
                                                    @if(count($sharedInterests) > 4)
                                                        <span class="sm-chip" style="background:rgba(255,255,255,.06);color:rgba(255,255,255,.3)">
                                                            +{{ count($sharedInterests) - 4 }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Footer: progress bar + action --}}
                                    <div class="px-3.5 pb-3.5 pt-1">
                                        {{-- Compat bar --}}
                                        <div class="mb-3">
                                            <div class="mb-1.5 flex items-center justify-between text-[10px] text-white/30">
                                                <span>Compatibility score</span>
                                                <span class="font-bold" style="color:{{ $scoreColor }}">{{ $score }}%</span>
                                            </div>
                                            <div class="h-1.5 w-full overflow-hidden rounded-full" style="background:rgba(255,255,255,.07)">
                                                <div class="h-full rounded-full transition-all duration-700"
                                                     style="width:{{ $score }}%;background:{{ $barColor }}"></div>
                                            </div>
                                        </div>

                                        {{-- Force match button --}}
                                        <button
                                            wire:click="forceMatch({{ $focusUserId }}, {{ $candidate->id }})"
                                            wire:loading.attr="disabled"
                                            class="sm-match-btn"
                                        >
                                            <span wire:loading.remove wire:target="forceMatch({{ $focusUserId }}, {{ $candidate->id }})">
                                                💞 Force Match
                                            </span>
                                            <span wire:loading wire:target="forceMatch({{ $focusUserId }}, {{ $candidate->id }})"
                                                  class="flex items-center justify-center gap-1.5">
                                                <svg class="h-3 w-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                                </svg>
                                                Matching…
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endif
            @endif
        </div>{{-- /sm-main --}}
    </div>{{-- /sm-layout --}}
</div>{{-- /sm-page --}}

<script>
// Auto-open sidebar on desktop; keep state across Livewire re-renders
(function () {
    function applyState() {
        const inner = document.getElementById('sm-sidebar-inner');
        if (!inner) return;
        // Always open on lg+
        if (window.innerWidth >= 1024) {
            inner.classList.add('is-open');
        }
    }
    applyState();
    window.addEventListener('resize', applyState);
    // Livewire re-render hook
    document.addEventListener('livewire:navigated', applyState);
    document.addEventListener('livewire:update', () => setTimeout(applyState, 50));
})();
</script>

{{-- ── Same-sex match confirmation modal ─────────────────────────────── --}}
@if($showSameSexModal)
<div
    style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:16px;background:rgba(0,0,0,.7);backdrop-filter:blur(4px)"
    x-data x-init="$el.style.opacity=0; setTimeout(()=>$el.style.transition='opacity .25s',0); setTimeout(()=>$el.style.opacity=1,10)"
>
    <div style="
        width:100%;max-width:420px;
        background:linear-gradient(160deg,#1e0a2e,#2d1050);
        border:1.5px solid rgba(239,68,68,.45);
        border-radius:20px;overflow:hidden;
        box-shadow:0 24px 64px rgba(239,68,68,.25),0 0 0 1px rgba(255,255,255,.05);
    ">
        {{-- Top accent bar --}}
        <div style="height:3px;background:linear-gradient(90deg,#ef4444,#f97316,#ef4444)"></div>

        <div style="padding:24px 24px 20px">
            {{-- Warning icon --}}
            <div style="display:flex;justify-content:center;margin-bottom:16px">
                <div style="
                    width:56px;height:56px;border-radius:50%;
                    background:rgba(239,68,68,.15);border:1.5px solid rgba(239,68,68,.35);
                    display:flex;align-items:center;justify-content:center;font-size:1.75rem
                ">⚠️</div>
            </div>

            {{-- Title --}}
            <h3 style="color:#fff;font-size:1.05rem;font-weight:800;text-align:center;margin:0 0 8px">
                Same-Gender Match Detected
            </h3>

            {{-- Body --}}
            <p style="color:rgba(255,255,255,.55);font-size:.8rem;text-align:center;margin:0 0 6px;line-height:1.6">
                You are about to force-match two
                <strong style="color:#fca5a5">{{ $pendingGender }}</strong> users:
            </p>
            <div style="
                background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.25);
                border-radius:12px;padding:10px 16px;text-align:center;margin-bottom:16px
            ">
                <span style="font-size:.92rem;font-weight:700;color:#fff">{{ $pendingMatchNames }}</span>
            </div>
            <p style="color:rgba(255,255,255,.4);font-size:.75rem;text-align:center;margin:0 0 20px;line-height:1.5">
                This platform is designed for opposite-gender matching. Same-sex matches should only be created if both users have explicitly requested it or your platform intentionally supports it.
            </p>

            {{-- Actions --}}
            <div style="display:flex;gap:10px">
                <button
                    wire:click="cancelSameSexMatch"
                    wire:loading.attr="disabled"
                    style="
                        flex:1;padding:10px;border-radius:12px;border:1px solid rgba(255,255,255,.12);
                        background:rgba(255,255,255,.06);color:rgba(255,255,255,.75);
                        font-size:.82rem;font-weight:600;cursor:pointer;transition:background .15s
                    "
                    onmouseover="this.style.background='rgba(255,255,255,.1)'"
                    onmouseout="this.style.background='rgba(255,255,255,.06)'"
                >
                    Cancel
                </button>
                <button
                    wire:click="confirmSameSexMatch"
                    wire:loading.attr="disabled"
                    style="
                        flex:1;padding:10px;border-radius:12px;border:none;
                        background:linear-gradient(135deg,#ef4444,#f97316);
                        color:#fff;font-size:.82rem;font-weight:700;cursor:pointer;
                        box-shadow:0 4px 16px rgba(239,68,68,.4);transition:opacity .15s
                    "
                    onmouseover="this.style.opacity='.88'"
                    onmouseout="this.style.opacity='1'"
                >
                    <span wire:loading.remove wire:target="confirmSameSexMatch">
                        ⚠️ Yes, Force Match Anyway
                    </span>
                    <span wire:loading wire:target="confirmSameSexMatch" style="display:flex;align-items:center;justify-content:center;gap:6px">
                        <svg style="width:12px;height:12px;animation:spin 1s linear infinite" fill="none" viewBox="0 0 24 24">
                            <circle style="opacity:.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path style="opacity:.75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        Matching…
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
<style>
@keyframes spin { to { transform: rotate(360deg); } }
</style>
@endif

</x-filament-panels::page>

/* Gradient glow for active user card */
.sm-user-active {
    background: linear-gradient(135deg, rgba(244,63,94,.15) 0%, rgba(168,85,247,.1) 100%) !important;
    border-left: 3px solid #f43f5e !important;
}
/* Score arc ring */
@keyframes ring-fill { from { stroke-dashoffset: 220; } }
/* Card hover lift */
.sm-candidate-card { transition: transform .18s ease, box-shadow .18s ease; }
.sm-candidate-card:hover { transform: translateY(-3px); box-shadow: 0 16px 40px rgba(0,0,0,.5); }
/* Pulse dot */
@keyframes pulse-dot { 0%,100%{opacity:1} 50%{opacity:.35} }
.sm-pulse { animation: pulse-dot 1.8s infinite; }
/* Shimmer skeleton */
@keyframes shimmer { 0%{background-position:-400px 0} 100%{background-position:400px 0} }
.sm-skeleton {
    background: linear-gradient(90deg, rgba(255,255,255,.04) 25%, rgba(255,255,255,.09) 50%, rgba(255,255,255,.04) 75%);
    background-size: 400px 100%;
    animation: shimmer 1.4s infinite;
    border-radius: 6px;
}
</style>

<div class="flex flex-col gap-5 lg:flex-row lg:items-start" style="min-height:70vh">

    {{-- ══════════════════════════════════════════════════════════
         LEFT SIDEBAR — New Members
    ══════════════════════════════════════════════════════════ --}}
    <div class="w-full shrink-0 lg:w-[280px]">
        {{-- Sidebar header --}}
        <div class="mb-3 overflow-hidden rounded-2xl"
             style="background:linear-gradient(135deg,#1e0a2e 0%,#2d1050 50%,#1a0a2e 100%);border:1px solid rgba(244,63,94,.25)">
            <div class="px-4 py-3" style="border-bottom:1px solid rgba(255,255,255,.07)">
                <div class="flex items-center gap-2">
                    <div class="flex h-7 w-7 items-center justify-center rounded-lg"
                         style="background:linear-gradient(135deg,#f43f5e,#a855f7)">
                        <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 20h5v-2a3 3 0 00-3-3H5a3 3 0 00-3 3v2h5M9 11a4 4 0 110-8 4 4 0 010 8zm6 0a4 4 0 110-8 4 4 0 010 8z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-widest" style="color:rgba(255,255,255,.5)">New Members</p>
                        <p class="text-[10px]" style="color:rgba(255,255,255,.3)">Last 7 days</p>
                    </div>
                    @php $newUsers = $this->getNewUsers(); @endphp
                    <span class="ml-auto inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full px-1 text-[10px] font-bold"
                          style="background:linear-gradient(135deg,#f43f5e,#a855f7);color:#fff">
                        {{ $newUsers->count() }}
                    </span>
                </div>
            </div>

            {{-- Member list --}}
            @if($newUsers->isEmpty())
                <div class="px-4 py-8 text-center">
                    <div class="mb-2 text-3xl">👥</div>
                    <p class="text-xs" style="color:rgba(255,255,255,.35)">No new users in the past 7 days.</p>
                </div>
            @else
                <ul class="max-h-[calc(100vh-220px)] overflow-y-auto" style="scrollbar-width:thin;scrollbar-color:rgba(244,63,94,.3) transparent">
                    @foreach($newUsers as $nu)
                        @php
                            $photo    = $nu->primaryPhoto;
                            $photoUrl = $photo ? $photo->thumbnail_url : null;
                            $active   = $focusUserId === $nu->id;
                        @endphp
                        <li style="border-bottom:1px solid rgba(255,255,255,.05)">
                            <button
                                wire:click="selectUser({{ $nu->id }})"
                                wire:loading.class="opacity-60"
                                wire:target="selectUser({{ $nu->id }})"
                                class="group flex w-full items-center gap-3 px-4 py-2.5 text-left transition-all duration-150
                                       {{ $active ? 'sm-user-active' : 'hover:bg-white/5' }}"
                            >
                                {{-- Avatar --}}
                                <div class="relative shrink-0">
                                    @if($photoUrl)
                                        <img src="{{ $photoUrl }}" alt="{{ $nu->name }}"
                                             class="h-9 w-9 rounded-full object-cover"
                                             style="box-shadow:0 0 0 2px {{ $active ? '#f43f5e' : 'rgba(255,255,255,.12)' }}" />
                                    @else
                                        <div class="flex h-9 w-9 items-center justify-center rounded-full text-sm font-bold"
                                             style="background:linear-gradient(135deg,#2d1050,#4a0e6e);color:#fff;box-shadow:0 0 0 2px {{ $active ? '#f43f5e' : 'rgba(255,255,255,.12)' }}">
                                            {{ strtoupper(substr($nu->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    {{-- Online indicator --}}
                                    @if(isset($nu->last_active_at) && \Carbon\Carbon::parse($nu->last_active_at)->diffInMinutes() < 30)
                                        <span class="absolute -bottom-0.5 -right-0.5 h-2.5 w-2.5 rounded-full border-2 sm-pulse"
                                              style="background:#22c55e;border-color:#1a0a2e"></span>
                                    @endif
                                </div>

                                {{-- Info --}}
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-1">
                                        <p class="truncate text-xs font-semibold {{ $active ? '' : 'text-white/80 group-hover:text-white' }}"
                                           style="{{ $active ? 'color:#fb7185' : '' }}">
                                            {{ $nu->name }}
                                        </p>
                                        @if($nu->is_premium)
                                            <span class="shrink-0 rounded px-1 text-[9px] font-black"
                                                  style="background:#f59e0b;color:#000">★</span>
                                        @endif
                                        @if($nu->is_verified)
                                            <span class="shrink-0 rounded px-1 text-[9px] font-black"
                                                  style="background:#0ea5e9;color:#fff">✓</span>
                                        @endif
                                    </div>
                                    <p class="text-[10px]" style="color:rgba(255,255,255,.35)">
                                        {{ ucfirst($nu->gender ?? '—') }}{{ $nu->age ? ' · '.$nu->age.' yrs' : '' }}
                                    </p>
                                    <p class="text-[10px]" style="color:rgba(255,255,255,.2)">{{ $nu->created_at->diffForHumans() }}</p>
                                </div>

                                {{-- Chevron when active --}}
                                @if($active)
                                    <svg class="h-4 w-4 shrink-0" style="color:#f43f5e" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                @endif
                            </button>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         RIGHT PANEL — Suggestions
    ══════════════════════════════════════════════════════════ --}}
    <div class="flex-1 min-w-0">

        @if(! $focusUserId)
            {{-- Empty state --}}
            <div class="flex flex-col items-center justify-center rounded-2xl py-20 text-center"
                 style="background:linear-gradient(135deg,rgba(30,10,46,.6),rgba(45,16,80,.4));border:1.5px dashed rgba(244,63,94,.2)">
                <div class="mb-5 flex h-20 w-20 items-center justify-center rounded-full"
                     style="background:linear-gradient(135deg,rgba(244,63,94,.12),rgba(168,85,247,.12));border:1.5px solid rgba(244,63,94,.2)">
                    <svg class="h-10 w-10" style="color:rgba(244,63,94,.5)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                </div>
                <h3 class="mb-1 text-base font-bold text-white/70">Select a member to begin</h3>
                <p class="text-sm" style="color:rgba(255,255,255,.3);max-width:300px">
                    Pick any new member from the sidebar to see their AI-powered compatibility suggestions.
                </p>
                <div class="mt-6 flex items-center gap-6 text-[11px]" style="color:rgba(255,255,255,.25)">
                    <span>🧠 AI scoring</span>
                    <span>💞 Auto-match</span>
                    <span>⚡ Instant notifications</span>
                </div>
            </div>

        @else
            @php
                $focusUser  = \App\Models\User::with(['profile.interests', 'primaryPhoto'])->find($focusUserId);
                $focusPhoto = $focusUser?->primaryPhoto;
            @endphp

            @if(! $focusUser)
                <div class="rounded-xl py-6 text-center text-sm"
                     style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#fca5a5">
                    User not found.
                </div>
            @else

                {{-- ── Focus user hero banner ─────────────────────────────── --}}
                <div class="mb-5 overflow-hidden rounded-2xl"
                     style="background:linear-gradient(135deg,#1e0a2e 0%,#2d1050 50%,#1a0a2e 100%);border:1px solid rgba(244,63,94,.3);box-shadow:0 8px 32px rgba(244,63,94,.12)">
                    {{-- Top gradient line --}}
                    <div class="h-0.5 w-full" style="background:linear-gradient(90deg,#f43f5e,#a855f7,#3b82f6)"></div>

                    <div class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center">
                        {{-- Avatar --}}
                        <div class="relative shrink-0 self-start sm:self-center">
                            @if($focusPhoto)
                                <img src="{{ $focusPhoto->thumbnail_url }}" alt="{{ $focusUser->name }}"
                                     class="h-16 w-16 rounded-2xl object-cover"
                                     style="box-shadow:0 0 0 3px #f43f5e,0 8px 24px rgba(244,63,94,.35)" />
                            @else
                                <div class="flex h-16 w-16 items-center justify-center rounded-2xl text-2xl font-black text-white"
                                     style="background:linear-gradient(135deg,#f43f5e,#a855f7);box-shadow:0 8px 24px rgba(244,63,94,.35)">
                                    {{ strtoupper(substr($focusUser->name, 0, 1)) }}
                                </div>
                            @endif
                            {{-- "Analysing" pulse ring --}}
                            <span class="absolute -bottom-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full text-[10px]"
                                  style="background:linear-gradient(135deg,#f43f5e,#a855f7);box-shadow:0 2px 8px rgba(244,63,94,.5)">
                                ✦
                            </span>
                        </div>

                        {{-- Details --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <h2 class="text-lg font-black text-white leading-none">{{ $focusUser->name }}</h2>
                                @if($focusUser->is_premium)
                                    <span class="rounded-full px-2 py-0.5 text-[10px] font-black"
                                          style="background:linear-gradient(90deg,#f59e0b,#f97316);color:#fff">
                                        ★ PREMIUM
                                    </span>
                                @endif
                                @if($focusUser->is_verified)
                                    <span class="rounded-full px-2 py-0.5 text-[10px] font-bold"
                                          style="background:rgba(14,165,233,.2);border:1px solid rgba(14,165,233,.4);color:#7dd3fc">
                                        ✓ VERIFIED
                                    </span>
                                @endif
                            </div>
                            <div class="flex flex-wrap gap-x-3 gap-y-1 text-xs" style="color:rgba(255,255,255,.5)">
                                <span>{{ ucfirst($focusUser->gender ?? 'Unknown') }}</span>
                                @if($focusUser->age)<span>· {{ $focusUser->age }} yrs</span>@endif
                                <span>· Seeking <strong class="text-white/70">{{ ucfirst($focusUser->seeking ?? 'everyone') }}</strong></span>
                                @if($focusUser->profile?->city)
                                    <span>· 📍 {{ $focusUser->profile->city }}{{ $focusUser->profile->country ? ', '.$focusUser->profile->country : '' }}</span>
                                @endif
                            </div>
                            @if($focusUser->profile?->headline)
                                <p class="mt-1.5 text-xs italic" style="color:rgba(255,255,255,.35)">"{{ Str::limit($focusUser->profile->headline, 80) }}"</p>
                            @endif
                        </div>

                        {{-- Joined meta --}}
                        <div class="shrink-0 text-right">
                            <p class="text-[10px] font-semibold uppercase tracking-widest" style="color:rgba(255,255,255,.25)">Joined</p>
                            <p class="text-sm font-bold text-white/60">{{ $focusUser->created_at->format('d M Y') }}</p>
                            <p class="text-[10px]" style="color:rgba(255,255,255,.25)">{{ $focusUser->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>

                {{-- ── Suggestions grid ──────────────────────────────────── --}}
                @if($suggestions->isEmpty())
                    <div class="flex flex-col items-center justify-center rounded-2xl py-14 text-center"
                         style="background:rgba(255,255,255,.025);border:1.5px dashed rgba(255,255,255,.1)">
                        <div class="mb-3 text-4xl">🤷</div>
                        <p class="text-sm text-white/50">No compatible candidates found.</p>
                        <p class="mt-1 text-xs text-white/25">All suitable users may already be matched.</p>
                    </div>
                @else
                    {{-- Count header --}}
                    <div class="mb-3 flex items-center gap-3">
                        <p class="text-sm font-semibold text-white/60">
                            <span class="font-black text-white">{{ $suggestions->count() }}</span> compatibility matches found
                        </p>
                        <div class="h-px flex-1" style="background:linear-gradient(90deg,rgba(244,63,94,.3),transparent)"></div>
                        <div class="flex items-center gap-2 text-[10px]" style="color:rgba(255,255,255,.3)">
                            <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-emerald-400"></span>≥70%</span>
                            <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-amber-400"></span>≥40%</span>
                            <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-gray-500"></span>&lt;40%</span>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach($suggestions as $rank => $item)
                            @php
                                $candidate  = $item['user'];
                                $score      = $item['score'];
                                $candPhoto  = $candidate->primaryPhoto;
                                $interests  = $candidate->profile?->interests ?? collect();

                                // Tiers
                                if ($score >= 70) {
                                    $tier      = 'emerald';
                                    $cardBg    = 'background:linear-gradient(160deg,rgba(16,185,129,.06),rgba(5,150,105,.03));border:1px solid rgba(16,185,129,.25)';
                                    $barColor  = '#10b981';
                                    $scoreText = 'color:#34d399';
                                } elseif ($score >= 40) {
                                    $tier      = 'amber';
                                    $cardBg    = 'background:linear-gradient(160deg,rgba(245,158,11,.06),rgba(217,119,6,.03));border:1px solid rgba(245,158,11,.2)';
                                    $barColor  = '#f59e0b';
                                    $scoreText = 'color:#fbbf24';
                                } else {
                                    $tier      = 'gray';
                                    $cardBg    = 'background:rgba(255,255,255,.025);border:1px solid rgba(255,255,255,.07)';
                                    $barColor  = '#6b7280';
                                    $scoreText = 'color:#9ca3af';
                                }

                                $focusInterestNames = $focusUser->profile?->interests->pluck('name')->toArray() ?? [];
                                $sharedInterests    = array_intersect($focusInterestNames, $interests->pluck('name')->toArray());
                            @endphp

                            <div class="sm-candidate-card flex flex-col rounded-2xl overflow-hidden"
                                 style="{{ $cardBg }};box-shadow:0 4px 20px rgba(0,0,0,.3)">

                                {{-- Card photo banner (if has photo) --}}
                                @if($candPhoto)
                                    <div class="relative h-28 w-full overflow-hidden">
                                        <img src="{{ $candPhoto->thumbnail_url }}"
                                             alt="{{ $candidate->name }}"
                                             class="h-full w-full object-cover"
                                             style="filter:brightness(.7)" />
                                        <div class="absolute inset-0"
                                             style="background:linear-gradient(to top,rgba(0,0,0,.85) 0%,transparent 60%)"></div>

                                        {{-- Rank badge --}}
                                        <span class="absolute left-2.5 top-2.5 flex h-6 w-6 items-center justify-center rounded-full text-[10px] font-black"
                                              style="background:rgba(0,0,0,.6);backdrop-filter:blur(6px);color:#fff;border:1px solid rgba(255,255,255,.2)">
                                            #{{ $rank + 1 }}
                                        </span>

                                        {{-- Score ring --}}
                                        <div class="absolute right-2.5 top-2" style="line-height:0">
                                            <svg width="44" height="44" viewBox="0 0 44 44">
                                                <circle cx="22" cy="22" r="18" fill="none" stroke="rgba(255,255,255,.1)" stroke-width="3"/>
                                                <circle cx="22" cy="22" r="18" fill="none" stroke="{{ $barColor }}" stroke-width="3"
                                                        stroke-dasharray="113"
                                                        stroke-dashoffset="{{ 113 - ($score / 100 * 113) }}"
                                                        stroke-linecap="round"
                                                        transform="rotate(-90 22 22)"/>
                                                <text x="22" y="26" text-anchor="middle"
                                                      font-size="9" font-weight="900" fill="{{ $barColor }}">{{ $score }}%</text>
                                            </svg>
                                        </div>

                                        {{-- Name over photo --}}
                                        <div class="absolute bottom-2 left-3 right-3">
                                            <p class="truncate text-sm font-bold text-white leading-tight">{{ $candidate->name }}</p>
                                        </div>
                                    </div>
                                @else
                                    {{-- No photo — gradient banner --}}
                                    <div class="relative flex h-20 items-center justify-center overflow-hidden"
                                         style="background:linear-gradient(135deg,#2d1050,#4a0e6e)">
                                        <span class="text-3xl font-black text-white/20 select-none">{{ strtoupper(substr($candidate->name,0,1)) }}</span>
                                        <span class="absolute left-2.5 top-2.5 flex h-6 w-6 items-center justify-center rounded-full text-[10px] font-black"
                                              style="background:rgba(0,0,0,.5);color:#fff;border:1px solid rgba(255,255,255,.2)">#{{ $rank + 1 }}</span>
                                        <div class="absolute right-2.5 top-2" style="line-height:0">
                                            <svg width="44" height="44" viewBox="0 0 44 44">
                                                <circle cx="22" cy="22" r="18" fill="none" stroke="rgba(255,255,255,.1)" stroke-width="3"/>
                                                <circle cx="22" cy="22" r="18" fill="none" stroke="{{ $barColor }}" stroke-width="3"
                                                        stroke-dasharray="113" stroke-dashoffset="{{ 113 - ($score / 100 * 113) }}"
                                                        stroke-linecap="round" transform="rotate(-90 22 22)"/>
                                                <text x="22" y="26" text-anchor="middle" font-size="9" font-weight="900" fill="{{ $barColor }}">{{ $score }}%</text>
                                            </svg>
                                        </div>
                                    </div>
                                @endif

                                {{-- Card body --}}
                                <div class="flex flex-1 flex-col gap-2 p-3">
                                    {{-- Basic info --}}
                                    <div>
                                        @if(!$candPhoto)
                                            <p class="mb-0.5 text-sm font-bold text-white">{{ $candidate->name }}</p>
                                        @endif
                                        <div class="flex flex-wrap items-center gap-1.5 text-[10px]" style="color:rgba(255,255,255,.45)">
                                            {{ ucfirst($candidate->gender ?? '?') }}
                                            @if($candidate->age)<span>· {{ $candidate->age }} yrs</span>@endif
                                            @if($candidate->is_premium)
                                                <span class="rounded px-1 text-[9px] font-black" style="background:#f59e0b;color:#000">★</span>
                                            @endif
                                        </div>
                                        @if($candidate->profile?->city)
                                            <p class="mt-0.5 truncate text-[10px]" style="color:rgba(255,255,255,.3)">
                                                📍 {{ $candidate->profile->city }}{{ $candidate->profile->country ? ', '.$candidate->profile->country : '' }}
                                            </p>
                                        @endif
                                    </div>

                                    {{-- Headline --}}
                                    @if($candidate->profile?->headline)
                                        <p class="text-[10px] italic" style="color:rgba(255,255,255,.3)">
                                            "{{ Str::limit($candidate->profile->headline, 55) }}"
                                        </p>
                                    @endif

                                    {{-- Tags --}}
                                    @php $tags = array_filter([$candidate->profile?->relationship_goal ?? null, $candidate->profile?->religion ?? null, $candidate->profile?->education ?? null]); @endphp
                                    @if(count($tags))
                                        <div class="flex flex-wrap gap-1">
                                            @foreach(array_slice($tags, 0, 3) as $tag)
                                                <span class="rounded-full px-1.5 py-0.5 text-[9px] font-medium capitalize"
                                                      style="background:rgba(168,85,247,.15);border:1px solid rgba(168,85,247,.25);color:#c4b5fd">
                                                    {{ $tag }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif

                                    {{-- Shared interests --}}
                                    @if(count($sharedInterests) > 0)
                                        <div>
                                            <p class="mb-1 text-[9px] uppercase tracking-wider" style="color:rgba(255,255,255,.2)">Shared</p>
                                            <div class="flex flex-wrap gap-1">
                                                @foreach(array_slice($sharedInterests, 0, 4) as $int)
                                                    <span class="rounded-full px-1.5 py-0.5 text-[9px]"
                                                          style="background:rgba(20,184,166,.15);border:1px solid rgba(20,184,166,.25);color:#5eead4">
                                                        {{ $int }}
                                                    </span>
                                                @endforeach
                                                @if(count($sharedInterests) > 4)
                                                    <span class="rounded-full px-1.5 py-0.5 text-[9px]"
                                                          style="background:rgba(255,255,255,.06);color:rgba(255,255,255,.3)">
                                                        +{{ count($sharedInterests) - 4 }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                {{-- Compat bar + Action --}}
                                <div class="px-3 pb-3 pt-1">
                                    <div class="mb-2">
                                        <div class="mb-1 flex items-center justify-between text-[10px]" style="color:rgba(255,255,255,.3)">
                                            <span>Compatibility</span>
                                            <span style="{{ $scoreText }}" class="font-bold">{{ $score }}%</span>
                                        </div>
                                        <div class="h-1 w-full overflow-hidden rounded-full" style="background:rgba(255,255,255,.07)">
                                            <div class="h-full rounded-full transition-all duration-700"
                                                 style="width:{{ $score }}%;background:{{ $barColor }}"></div>
                                        </div>
                                    </div>

                                    <button
                                        wire:click="forceMatch({{ $focusUserId }}, {{ $candidate->id }})"
                                        wire:confirm="Force match {{ addslashes($focusUser->name) }} ↔ {{ addslashes($candidate->name) }}?\n\nThis creates a mutual match, ensures cross-likes exist, and opens a shared conversation. Both users will be notified."
                                        wire:loading.attr="disabled"
                                        class="group w-full overflow-hidden rounded-xl px-3 py-2 text-xs font-bold text-white transition-all duration-150"
                                        style="background:linear-gradient(135deg,#f43f5e,#a855f7);box-shadow:0 4px 14px rgba(244,63,94,.3)"
                                        onmouseover="this.style.boxShadow='0 6px 20px rgba(244,63,94,.55)'"
                                        onmouseout="this.style.boxShadow='0 4px 14px rgba(244,63,94,.3)'"
                                    >
                                        <span wire:loading.remove wire:target="forceMatch({{ $focusUserId }}, {{ $candidate->id }})">
                                            💞 Force Match
                                        </span>
                                        <span wire:loading wire:target="forceMatch({{ $focusUserId }}, {{ $candidate->id }})" class="flex items-center justify-center gap-1.5">
                                            <svg class="h-3 w-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                            </svg>
                                            Matching…
                                        </span>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif
        @endif
    </div>
</div>
</x-filament-panels::page>

