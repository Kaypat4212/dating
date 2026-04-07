<x-filament-panels::page>
@php
    $stats = $this->stats;
    $users = $this->incompleteUsers;
    $filterOptions = [
        'all'         => 'All Incomplete',
        'eligible'    => 'Eligible for Reminder',
        'no_photo'    => 'Missing Photo',
        'no_bio'      => 'Missing Bio',
        'no_location' => 'Missing Location',
    ];
    $sortIcon = fn(string $f) => $this->sortField === $f
        ? ($this->sortDir === 'asc' ? '↑' : '↓')
        : '↕';
@endphp

<style>
.or {
    --bg0: #fff; --bg1: #f8fafc; --bg2: #f1f5f9;
    --border: #e2e8f0; --border2: #cbd5e1;
    --txt: #0f172a; --txt2: #475569; --txt3: #94a3b8;
    --green: #16a34a; --gbg: #f0fdf4; --gbrd: #bbf7d0;
    --red:   #dc2626; --rbg: #fef2f2; --rbrd: #fecaca;
    --amber: #d97706; --abg: #fffbeb; --abrd: #fde68a;
    --blue:  #2563eb; --bbg: #eff6ff; --bbrd: #bfdbfe;
    --purple:#7c3aed; --pbg: #f5f3ff; --pbrd: #ddd6fe;
    --sh: 0 1px 3px rgba(0,0,0,.07);
    --shm: 0 4px 12px rgba(0,0,0,.10);
}
.dark .or {
    --bg0:#1e293b; --bg1:#0f172a; --bg2:#1e293b;
    --border:#334155; --border2:#475569;
    --txt:#f1f5f9; --txt2:#94a3b8; --txt3:#64748b;
    --green:#4ade80;  --gbg:rgba(22,163,74,.15);  --gbrd:rgba(74,222,128,.25);
    --red:#f87171;    --rbg:rgba(220,38,38,.15);   --rbrd:rgba(248,113,113,.25);
    --amber:#fbbf24;  --abg:rgba(217,119,6,.15);   --abrd:rgba(251,191,36,.25);
    --blue:#60a5fa;   --bbg:rgba(37,99,235,.15);   --bbrd:rgba(96,165,250,.25);
    --purple:#a78bfa; --pbg:rgba(124,58,237,.15);  --pbrd:rgba(167,139,250,.25);
}
.or * { box-sizing:border-box; }
.or-page { display:flex; flex-direction:column; gap:1.5rem; }

/* ── Stats grid ── */
.or-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; }
@media(max-width:768px){ .or-stats { grid-template-columns:repeat(2,1fr); } }
@media(max-width:480px){ .or-stats { grid-template-columns:1fr; } }

.or-stat {
    background:var(--bg0); border:1px solid var(--border);
    border-radius:12px; padding:1.25rem 1.5rem;
    box-shadow:var(--sh); display:flex; flex-direction:column; gap:.35rem;
}
.or-stat-label { font-size:.72rem; font-weight:600; text-transform:uppercase;
    letter-spacing:.08em; color:var(--txt3); }
.or-stat-value { font-size:2rem; font-weight:800; line-height:1; color:var(--txt); }
.or-stat-sub   { font-size:.75rem; color:var(--txt3); }
.or-stat.green .or-stat-value { color:var(--green); }
.or-stat.amber .or-stat-value { color:var(--amber); }
.or-stat.red   .or-stat-value { color:var(--red);   }
.or-stat.blue  .or-stat-value { color:var(--blue);  }
.or-stat-icon  { font-size:1.5rem; margin-bottom:.2rem; }

/* ── Card ── */
.or-card {
    background:var(--bg0); border:1px solid var(--border);
    border-radius:14px; overflow:hidden; box-shadow:var(--sh);
}
.or-card-header {
    padding:1rem 1.5rem; border-bottom:1px solid var(--border);
    display:flex; align-items:center; justify-content:space-between; gap:1rem;
    flex-wrap:wrap;
}
.or-card-title { font-size:1rem; font-weight:700; color:var(--txt); display:flex; align-items:center; gap:.5rem; }
.or-card-body  { padding:1.5rem; }

/* ── Toolbar ── */
.or-toolbar { display:flex; align-items:center; gap:.75rem; flex-wrap:wrap; }

.or-search {
    flex:1; min-width:200px;
    display:flex; align-items:center; gap:.5rem;
    background:var(--bg1); border:1px solid var(--border2);
    border-radius:8px; padding:0 .75rem; height:36px;
}
.or-search svg { width:14px; height:14px; color:var(--txt3); flex-shrink:0; }
.or-search input {
    flex:1; border:none; background:transparent;
    font-size:.85rem; color:var(--txt); outline:none;
}
.or-search input::placeholder { color:var(--txt3); }

.or-select {
    height:36px; padding:0 .75rem;
    background:var(--bg1); border:1px solid var(--border2);
    border-radius:8px; font-size:.85rem; color:var(--txt);
    cursor:pointer; outline:none; appearance:none;
    padding-right:2rem;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='%2394a3b8' d='M4.5 6l3.5 3.5L11.5 6'/%3E%3C/svg%3E");
    background-repeat:no-repeat; background-position:right 8px center;
}

/* ── Table ── */
.or-table-wrap { overflow-x:auto; }
.or-table {
    width:100%; border-collapse:collapse; font-size:.83rem;
}
.or-table th {
    padding:.65rem 1rem; text-align:left;
    background:var(--bg1); color:var(--txt3);
    font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em;
    border-bottom:1px solid var(--border); white-space:nowrap; cursor:pointer;
    user-select:none;
}
.or-table th:hover { color:var(--txt2); }
.or-table td {
    padding:.75rem 1rem; border-bottom:1px solid var(--border);
    color:var(--txt); vertical-align:middle;
}
.or-table tr:last-child td { border-bottom:none; }
.or-table tr:hover td { background:var(--bg1); }

/* ── Badges ── */
.or-badge {
    display:inline-flex; align-items:center; gap:3px;
    padding:2px 8px; border-radius:6px; font-size:.68rem;
    font-weight:600; border:1px solid; white-space:nowrap; margin:2px;
}
.or-badge.red    { background:var(--rbg); border-color:var(--rbrd); color:var(--red); }
.or-badge.amber  { background:var(--abg); border-color:var(--abrd); color:var(--amber); }
.or-badge.green  { background:var(--gbg); border-color:var(--gbrd); color:var(--green); }
.or-badge.blue   { background:var(--bbg); border-color:var(--bbrd); color:var(--blue); }
.or-badge.purple { background:var(--pbg); border-color:var(--pbrd); color:var(--purple); }
.or-badge.gray   { background:var(--bg2); border-color:var(--border2); color:var(--txt3); }

/* ── Step pill ── */
.or-step {
    display:inline-flex; align-items:center; justify-content:center;
    width:28px; height:28px; border-radius:50%;
    font-size:.72rem; font-weight:800; border:2px solid;
}
.or-step.s0 { background:var(--rbg); border-color:var(--red);    color:var(--red); }
.or-step.s1 { background:var(--abg); border-color:var(--amber);  color:var(--amber); }
.or-step.s2 { background:var(--abg); border-color:var(--amber);  color:var(--amber); }
.or-step.s3 { background:var(--bbg); border-color:var(--blue);   color:var(--blue); }
.or-step.s4 { background:var(--bbg); border-color:var(--blue);   color:var(--blue); }

/* ── User cell ── */
.or-user { display:flex; align-items:center; gap:.65rem; }
.or-avatar {
    width:36px; height:36px; border-radius:50%;
    background:linear-gradient(135deg, #f472b6, #818cf8);
    display:flex; align-items:center; justify-content:center;
    font-weight:700; font-size:.85rem; color:#fff; flex-shrink:0;
    text-transform:uppercase;
}
.or-user-info { display:flex; flex-direction:column; gap:1px; }
.or-user-name  { font-weight:600; color:var(--txt); font-size:.875rem; }
.or-user-email { font-size:.75rem; color:var(--txt3); }

/* ── Action button ── */
.or-btn {
    display:inline-flex; align-items:center; gap:.35rem;
    padding:5px 12px; border-radius:7px; font-size:.75rem; font-weight:600;
    border:none; cursor:pointer; transition:all .15s; text-decoration:none;
}
.or-btn-primary { background:var(--blue); color:#fff; }
.or-btn-primary:hover { opacity:.85; }
.or-btn-ghost   { background:var(--bg2); color:var(--txt2); border:1px solid var(--border2); }
.or-btn-ghost:hover { background:var(--bg1); color:var(--txt); }
.or-btn-sm svg  { width:13px; height:13px; }

/* ── Pagination ── */
.or-pagination { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.75rem; padding:.75rem 0 0; }
.or-pag-info { font-size:.8rem; color:var(--txt3); }
.or-pag-links { display:flex; gap:.3rem; }
.or-pag-links a, .or-pag-links span {
    display:inline-flex; align-items:center; justify-content:center;
    min-width:32px; height:32px; padding:0 .4rem;
    border-radius:7px; font-size:.8rem; font-weight:600;
    border:1px solid var(--border2); color:var(--txt2);
    text-decoration:none; cursor:pointer;
}
.or-pag-links .active { background:var(--blue); color:#fff; border-color:var(--blue); }
.or-pag-links a:hover:not(.active) { background:var(--bg2); }

/* ── Empty state ── */
.or-empty {
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    padding:3rem; gap:.75rem; color:var(--txt3);
}
.or-empty svg { width:48px; height:48px; opacity:.4; }
.or-empty-title { font-size:1.05rem; font-weight:600; color:var(--txt2); }
.or-empty-sub   { font-size:.85rem; }

/* ── Reminder counter ── */
.or-remind-dots { display:flex; gap:3px; }
.or-remind-dot  {
    width:8px; height:8px; border-radius:50%;
    background:var(--border2);
}
.or-remind-dot.sent { background:var(--blue); }
</style>

<div class="or">
<div class="or-page">

    {{-- ── Stats Row ──────────────────────────────────────────────────── --}}
    <div class="or-stats">
        <div class="or-stat red">
            <div class="or-stat-icon">
                <x-heroicon-o-user-minus class="w-6 h-6" style="color:var(--red)" />
            </div>
            <div class="or-stat-label">Incomplete Profiles</div>
            <div class="or-stat-value">{{ $stats['total'] }}</div>
            <div class="or-stat-sub">Haven't finished setup</div>
        </div>

        <div class="or-stat amber">
            <div class="or-stat-icon">
                <x-heroicon-o-paper-airplane class="w-6 h-6" style="color:var(--amber)" />
            </div>
            <div class="or-stat-label">Eligible for Reminder</div>
            <div class="or-stat-value">{{ $stats['eligible'] }}</div>
            <div class="or-stat-sub">Within limits & timing</div>
        </div>

        <div class="or-stat green">
            <div class="or-stat-icon">
                <x-heroicon-o-envelope class="w-6 h-6" style="color:var(--green)" />
            </div>
            <div class="or-stat-label">Reminded Today</div>
            <div class="or-stat-value">{{ $stats['reminded_today'] }}</div>
            <div class="or-stat-sub">Emails sent today</div>
        </div>

        <div class="or-stat blue">
            <div class="or-stat-icon">
                <x-heroicon-o-arrow-right-start-on-rectangle class="w-6 h-6" style="color:var(--blue)" />
            </div>
            <div class="or-stat-label">Never Started</div>
            <div class="or-stat-value">{{ $stats['never_started'] }}</div>
            <div class="or-stat-sub">Step 0 — untouched</div>
        </div>
    </div>

    {{-- ── Settings Form ────────────────────────────────────────────────── --}}
    <div class="or-card">
        <div class="or-card-header">
            <div class="or-card-title">
                <x-heroicon-o-cog-6-tooth class="w-5 h-5" style="color:var(--blue)" />
                Reminder Configuration
            </div>
        </div>
        <div class="or-card-body">
            {{ $this->form }}
        </div>
    </div>

    {{-- ── Incomplete Users Table ───────────────────────────────────────── --}}
    <div class="or-card">
        <div class="or-card-header">
            <div class="or-card-title">
                <x-heroicon-o-users class="w-5 h-5" style="color:var(--purple)" />
                Incomplete Users
                @if($users->total())
                    <span class="or-badge purple">{{ $users->total() }}</span>
                @endif
            </div>

            <div class="or-toolbar">
                {{-- Search --}}
                <div class="or-search">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0Z"/>
                    </svg>
                    <input
                        type="text"
                        wire:model.live.debounce.400ms="search"
                        placeholder="Search name or email..."
                    />
                </div>

                {{-- Filter --}}
                <select wire:model.live="filterBy" class="or-select">
                    @foreach($filterOptions as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="or-card-body" style="padding:0">
            @if($users->isEmpty())
                <div class="or-empty">
                    <x-heroicon-o-check-circle class="w-12 h-12" />
                    <div class="or-empty-title">
                        @if($this->search || $this->filterBy !== 'all')
                            No users match your filter
                        @else
                            All users have completed their profiles!
                        @endif
                    </div>
                    <div class="or-empty-sub">
                        @if($this->search || $this->filterBy !== 'all')
                            Try clearing the search or filter.
                        @else
                            Nothing to do here — great engagement!
                        @endif
                    </div>
                </div>
            @else
                <div class="or-table-wrap">
                    <table class="or-table">
                        <thead>
                            <tr>
                                <th wire:click="sortBy('name')" style="padding-left:1.5rem">
                                    User {{ $sortIcon('name') }}
                                </th>
                                <th wire:click="sortBy('created_at')">
                                    Joined {{ $sortIcon('created_at') }}
                                </th>
                                <th wire:click="sortBy('onboarding_step')">
                                    Step {{ $sortIcon('onboarding_step') }}
                                </th>
                                <th>Missing Items</th>
                                <th wire:click="sortBy('last_active_at')">
                                    Last Active {{ $sortIcon('last_active_at') }}
                                </th>
                                <th>Reminders</th>
                                <th style="text-align:right; padding-right:1.5rem">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                @php
                                    $missing   = \App\Filament\Pages\OnboardingReminders::getMissingItems($user);
                                    $step      = (int) $user->onboarding_step;
                                    $stepClass = 's' . min($step, 4);
                                    $stepLabel = $step === 0 ? 'Not started' : "Step {$step} done";
                                    $maxCount  = (int) \App\Models\SiteSetting::get('onboarding_reminder_max_count', 3);
                                    $minH      = (int) \App\Models\SiteSetting::get('onboarding_reminder_min_hours', 24);
                                    $intH      = (int) \App\Models\SiteSetting::get('onboarding_reminder_interval_hours', 48);
                                    $tooNew    = $user->created_at->gt(now()->subHours($minH));
                                    $tooSoon   = $user->last_reminder_at && $user->last_reminder_at->gt(now()->subHours($intH));
                                    $maxed     = $user->reminder_count >= $maxCount;
                                    $eligible  = !$tooNew && !$tooSoon && !$maxed;
                                @endphp
                                <tr>
                                    {{-- User --}}
                                    <td style="padding-left:1.5rem">
                                        <div class="or-user">
                                            <div class="or-avatar">{{ mb_substr($user->name, 0, 1) }}</div>
                                            <div class="or-user-info">
                                                <div class="or-user-name">{{ $user->name }}</div>
                                                <div class="or-user-email">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Joined --}}
                                    <td>
                                        <div style="color:var(--txt2); font-size:.8rem">
                                            {{ $user->created_at->format('M j, Y') }}
                                        </div>
                                        <div style="color:var(--txt3); font-size:.72rem">
                                            {{ $user->created_at->diffForHumans() }}
                                        </div>
                                    </td>

                                    {{-- Step --}}
                                    <td>
                                        <div style="display:flex; align-items:center; gap:.5rem;">
                                            <div class="or-step {{ $stepClass }}">
                                                @if($step === 0) 0 @else {{ $step }} @endif
                                            </div>
                                            <span style="font-size:.75rem; color:var(--txt3)">
                                                @if($step === 0) Not started
                                                @elseif($step === 1) Needs bio
                                                @elseif($step === 2) Needs photos
                                                @elseif($step === 3) Needs location
                                                @elseif($step === 4) Needs interests
                                                @else Unknown
                                                @endif
                                            </span>
                                        </div>
                                    </td>

                                    {{-- Missing items --}}
                                    <td>
                                        <div style="display:flex; flex-wrap:wrap; gap:2px; max-width:280px">
                                            @forelse($missing as $item)
                                                <span class="or-badge {{ match(true) {
                                                    str_contains($item, 'photo') => 'amber',
                                                    str_contains($item, 'Bio')   => 'blue',
                                                    str_contains($item, 'Location') => 'purple',
                                                    default => 'red'
                                                } }}">
                                                    {{ $item }}
                                                </span>
                                            @empty
                                                <span class="or-badge green">
                                                    <x-heroicon-s-check-circle class="w-3 h-3" /> Looks complete
                                                </span>
                                            @endforelse
                                        </div>
                                    </td>

                                    {{-- Last active --}}
                                    <td>
                                        @if($user->last_active_at)
                                            <div style="font-size:.8rem; color:var(--txt2)">
                                                {{ $user->last_active_at->diffForHumans() }}
                                            </div>
                                        @else
                                            <span style="color:var(--txt3); font-size:.78rem">Never</span>
                                        @endif
                                    </td>

                                    {{-- Reminders sent --}}
                                    <td>
                                        <div style="display:flex; flex-direction:column; gap:3px;">
                                            <div class="or-remind-dots">
                                                @for($i = 0; $i < $maxCount; $i++)
                                                    <div class="or-remind-dot {{ $i < $user->reminder_count ? 'sent' : '' }}"></div>
                                                @endfor
                                            </div>
                                            <div style="font-size:.7rem; color:var(--txt3)">
                                                {{ $user->reminder_count }}/{{ $maxCount }} sent
                                                @if($user->last_reminder_at)
                                                    · {{ $user->last_reminder_at->diffForHumans() }}
                                                @endif
                                            </div>
                                            @if($tooNew)
                                                <span class="or-badge gray">Too new</span>
                                            @elseif($maxed)
                                                <span class="or-badge red">Limit reached</span>
                                            @elseif($tooSoon)
                                                <span class="or-badge amber">Wait: {{ $user->last_reminder_at->addHours($intH)->diffForHumans() }}</span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Action --}}
                                    <td style="text-align:right; padding-right:1.5rem">
                                        <button
                                            wire:click="sendReminder({{ $user->id }})"
                                            wire:loading.attr="disabled"
                                            class="or-btn {{ $eligible ? 'or-btn-primary' : 'or-btn-ghost' }}"
                                            @if(!$eligible) title="{{ $maxed ? 'Reminder limit reached' : ($tooNew ? 'User too new' : 'Too soon since last reminder') }}" @endif
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="or-btn-sm">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/>
                                            </svg>
                                            {{ $eligible ? 'Send Reminder' : 'Send Anyway' }}
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($users->hasPages())
                    <div style="padding:1rem 1.5rem; border-top:1px solid var(--border)">
                        <div class="or-pagination">
                            <div class="or-pag-info">
                                Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }} users
                            </div>
                            <div class="or-pag-links">
                                {{ $users->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>

</div>
</div>

<x-filament-actions::modals />
