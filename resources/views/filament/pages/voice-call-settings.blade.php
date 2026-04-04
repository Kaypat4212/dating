<x-filament-panels::page>
@php
    $stats   = $this->getStatsProperty();
    $enabled = filter_var(\App\Models\SiteSetting::get('voice_calls_enabled', '1'), FILTER_VALIDATE_BOOLEAN);
    $timeout = (int) \App\Models\SiteSetting::get('voice_call_timeout', 30);
    $maxDur  = (int) \App\Models\SiteSetting::get('voice_call_max_duration', 0);
    $daily   = (int) \App\Models\SiteSetting::get('voice_call_daily_limit', 0);
    $expire  = (int) \App\Models\SiteSetting::get('voice_call_token_expire', 3600);
    $reqMatch = filter_var(\App\Models\SiteSetting::get('voice_call_require_match', '1'), FILTER_VALIDATE_BOOLEAN);
@endphp

<style>
/* =========================================================
   VOICE CALL SETTINGS — Design System
   All colours hardcoded so Tailwind purge never strips them
========================================================= */
.vc {
    --bg:       #ffffff;
    --bg2:      #f8fafc;
    --bg3:      #f1f5f9;
    --border:   #e2e8f0;
    --border2:  #cbd5e1;
    --txt:      #0f172a;
    --txt2:     #475569;
    --txt3:     #94a3b8;
    --green:    #16a34a;
    --green-bg: #f0fdf4;
    --green-bd: #bbf7d0;
    --green-lt: #dcfce7;
    --red:      #dc2626;
    --red-bg:   #fef2f2;
    --red-bd:   #fecaca;
    --red-lt:   #fee2e2;
    --amber:    #d97706;
    --amber-bg: #fffbeb;
    --amber-bd: #fde68a;
    --blue:     #2563eb;
    --blue-bg:  #eff6ff;
    --blue-bd:  #bfdbfe;
    --purple:   #7c3aed;
    --purple-bg:#f5f3ff;
    --purple-bd:#ddd6fe;
    --radius:   12px;
    --radius-sm:8px;
    --shadow:   0 1px 3px rgba(0,0,0,.08), 0 1px 2px rgba(0,0,0,.04);
    --shadow-md:0 4px 12px rgba(0,0,0,.10), 0 2px 6px rgba(0,0,0,.06);
}
@media (prefers-color-scheme:dark) {
    .vc {
        --bg:#1e293b; --bg2:#0f172a; --bg3:#1e293b;
        --border:#334155; --border2:#475569;
        --txt:#f1f5f9; --txt2:#94a3b8; --txt3:#64748b;
        --green:#4ade80; --green-bg:rgba(22,163,74,.15); --green-bd:rgba(74,222,128,.25); --green-lt:rgba(22,163,74,.12);
        --red:#f87171; --red-bg:rgba(220,38,38,.15); --red-bd:rgba(248,113,113,.25); --red-lt:rgba(220,38,38,.12);
        --amber:#fbbf24; --amber-bg:rgba(217,119,6,.15); --amber-bd:rgba(251,191,36,.25);
        --blue:#60a5fa; --blue-bg:rgba(37,99,235,.15); --blue-bd:rgba(96,165,250,.25);
        --purple:#a78bfa; --purple-bg:rgba(124,58,237,.15); --purple-bd:rgba(167,139,250,.25);
    }
}
.vc * { box-sizing:border-box; }

/* ── Status banner ─────────────────────────────────── */
.vc-banner {
    display:flex; align-items:center; gap:12px;
    padding:14px 20px;
    border-radius:var(--radius);
    border:1.5px solid;
    font-size:.875rem; font-weight:600;
    margin-bottom:24px; line-height:1.4;
}
.vc-banner.enabled  { background:var(--green-bg); border-color:var(--green-bd); color:var(--green); }
.vc-banner.disabled { background:var(--red-bg);   border-color:var(--red-bd);   color:var(--red);   }
.vc-banner svg { width:20px; height:20px; flex-shrink:0; }
.vc-banner strong { font-weight:800; }

/* ── Stat cards ────────────────────────────────────── */
.vc-stats-grid {
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:12px; margin-bottom:20px;
}
@media(min-width:640px)  { .vc-stats-grid { grid-template-columns:repeat(3,1fr); } }
@media(min-width:1024px) { .vc-stats-grid { grid-template-columns:repeat(5,1fr); } }

.vc-stat {
    background:var(--bg);
    border:1px solid var(--border);
    border-radius:var(--radius);
    padding:16px;
    display:flex; flex-direction:column; gap:4px;
    box-shadow:var(--shadow);
    transition:box-shadow .15s;
}
.vc-stat:hover { box-shadow:var(--shadow-md); }
.vc-stat-val  { font-size:1.75rem; font-weight:800; line-height:1; }
.vc-stat-lbl  { font-size:.75rem; font-weight:600; color:var(--txt); margin-top:2px; }
.vc-stat-sub  { font-size:.65rem; color:var(--txt3); }
.vc-stat.green  { border-left:4px solid var(--green);  }
.vc-stat.amber  { border-left:4px solid var(--amber);  }
.vc-stat.blue   { border-left:4px solid var(--blue);   }
.vc-stat.red    { border-left:4px solid var(--red);    }
.vc-stat.purple { border-left:4px solid var(--purple); }
.vc-stat.green  .vc-stat-val { color:var(--green);  }
.vc-stat.amber  .vc-stat-val { color:var(--amber);  }
.vc-stat.blue   .vc-stat-val { color:var(--blue);   }
.vc-stat.red    .vc-stat-val { color:var(--red);    }
.vc-stat.purple .vc-stat-val { color:var(--purple); }

/* ── All-time totals card ──────────────────────────── */
.vc-alltime {
    background:var(--bg);
    border:1px solid var(--border);
    border-radius:var(--radius);
    overflow:hidden;
    margin-bottom:24px;
    box-shadow:var(--shadow);
}
.vc-alltime-head {
    display:flex; align-items:center; gap:8px;
    padding:12px 20px;
    border-bottom:1px solid var(--border);
    font-size:.8125rem; font-weight:600; color:var(--txt2);
}
.vc-alltime-head svg { width:16px; height:16px; }
.vc-alltime-row {
    display:grid;
    grid-template-columns:repeat(2,1fr);
}
@media(min-width:640px) { .vc-alltime-row { grid-template-columns:repeat(4,1fr); } }
.vc-alltime-cell {
    padding:20px 16px; text-align:center;
    border-right:1px solid var(--border);
}
.vc-alltime-cell:last-child { border-right:none; }
.vc-alltime-num  { font-size:1.375rem; font-weight:800; color:var(--txt); }
.vc-alltime-desc { font-size:.75rem; color:var(--txt2); margin-top:4px; }

/* ── Config Snapshot ───────────────────────────────── */
.vc-config {
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:0;
    border:1px solid var(--border);
    border-radius:var(--radius);
    overflow:hidden;
    background:var(--bg);
    margin-top:24px;
    box-shadow:var(--shadow);
}
@media(min-width:640px){ .vc-config { grid-template-columns:repeat(3,1fr); } }
.vc-config-head {
    grid-column:1/-1;
    display:flex; align-items:center; gap:8px;
    padding:12px 20px;
    border-bottom:1px solid var(--border);
    font-size:.8125rem; font-weight:600; color:var(--txt2);
}
.vc-config-item {
    padding:14px 20px;
    border-right:1px solid var(--border);
    border-bottom:1px solid var(--border);
}
.vc-config-item:nth-child(2n) { border-right:none; }
@media(min-width:640px){
    .vc-config-item:nth-child(2n)  { border-right:1px solid var(--border); }
    .vc-config-item:nth-child(3n)  { border-right:none; }
}
.vc-config-item:nth-last-child(-n+3) { border-bottom:none; }
.vc-config-lbl { font-size:.7rem; text-transform:uppercase; letter-spacing:.05em; color:var(--txt3); margin-bottom:4px; }
.vc-config-val { font-size:.875rem; font-weight:700; color:var(--txt); }
.vc-config-val.ok  { color:var(--green); }
.vc-config-val.bad { color:var(--red); }

/* ── Form card wrapper ─────────────────────────────── */
.vc-form-card {
    background:var(--bg);
    border:1px solid var(--border);
    border-radius:var(--radius);
    padding:24px;
    margin-top:24px;
    box-shadow:var(--shadow);
}
.vc-form-head {
    display:flex; align-items:center; gap:8px;
    font-size:.9375rem; font-weight:700; color:var(--txt);
    margin-bottom:20px; padding-bottom:14px;
    border-bottom:1px solid var(--border);
}
.vc-form-head svg { width:20px; height:20px; color:var(--txt2); }

/* ── Save row ──────────────────────────────────────── */
.vc-save-row {
    display:flex; justify-content:flex-end;
    margin-top:20px; padding-top:16px;
    border-top:1px solid var(--border);
}
.vc-btn-save {
    display:inline-flex; align-items:center; gap:8px;
    padding:10px 24px; border-radius:var(--radius-sm);
    font-size:.875rem; font-weight:600;
    background:#2563eb; color:#fff; border:none; cursor:pointer;
    transition:background .15s, box-shadow .15s;
    box-shadow:0 1px 4px rgba(37,99,235,.35);
}
.vc-btn-save:hover { background:#1d4ed8; box-shadow:0 2px 8px rgba(37,99,235,.45); }
.vc-btn-save:disabled { opacity:.55; cursor:not-allowed; }

/* ── Danger zone ───────────────────────────────────── */
.vc-danger {
    margin-top:24px;
    border:1.5px solid var(--red-bd);
    border-radius:var(--radius);
    background:var(--red-bg);
    overflow:hidden;
}
.vc-danger-head {
    display:flex; align-items:center; gap:8px;
    padding:12px 20px;
    border-bottom:1px solid var(--red-bd);
    font-size:.8125rem; font-weight:700; color:var(--red);
}
.vc-danger-head svg { width:16px; height:16px; }
.vc-danger-body { display:flex; flex-direction:column; gap:12px; padding:16px; }
@media(min-width:640px){ .vc-danger-body { flex-direction:row; } }

.vc-danger-card {
    flex:1; background:var(--bg);
    border:1px solid var(--border);
    border-radius:var(--radius-sm);
    padding:16px; display:flex; flex-direction:column; gap:12px;
    box-shadow:var(--shadow);
}
.vc-danger-card h4 { font-size:.875rem; font-weight:700; color:var(--txt); margin:0; }
.vc-danger-card p  { font-size:.75rem; color:var(--txt2); margin:0; line-height:1.5; }

.vc-btn-danger {
    display:inline-flex; align-items:center; gap:6px;
    padding:8px 16px; border-radius:var(--radius-sm);
    font-size:.75rem; font-weight:600;
    border:none; cursor:pointer; transition:background .15s;
    align-self:flex-start;
}
.vc-btn-end   { background:var(--red);   color:#fff; }
.vc-btn-end:hover   { background:#b91c1c; }
.vc-btn-clear { background:#374151; color:#fff; }
.vc-btn-clear:hover { background:#111827; }
.vc-btn-danger:disabled { opacity:.55; cursor:not-allowed; }
.vc-btn-danger svg { width:14px; height:14px; }

/* ── Spinner ───────────────────────────────────────── */
.vc-spin { animation:vc-spin .7s linear infinite; width:14px; height:14px; }
@keyframes vc-spin { to { transform:rotate(360deg); } }
</style>

<div class="vc">

    {{-- ════════════════════════════════════════════════════ --}}
    {{-- Status Banner                                        --}}
    {{-- ════════════════════════════════════════════════════ --}}
    <div class="vc-banner {{ $enabled ? 'enabled' : 'disabled' }}">
        @if($enabled)
            <svg fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd"/></svg>
            Voice calls are <strong>ENABLED</strong> — users can make and receive calls right now.
        @else
            <svg fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-1.72 6.97a.75.75 0 10-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 101.06 1.06L12 13.06l1.72 1.72a.75.75 0 101.06-1.06L13.06 12l1.72-1.72a.75.75 0 10-1.06-1.06L12 10.94l-1.72-1.72z" clip-rule="evenodd"/></svg>
            Voice calls are <strong>DISABLED</strong> — the call button is hidden for all users.
        @endif
    </div>

    {{-- ════════════════════════════════════════════════════ --}}
    {{-- Today's Live Stats                                   --}}
    {{-- ════════════════════════════════════════════════════ --}}
    <div class="vc-stats-grid">
        <div class="vc-stat green">
            <span class="vc-stat-val">{{ $stats['active_now'] }}</span>
            <span class="vc-stat-lbl">Active Now</span>
            <span class="vc-stat-sub">live &amp; connected</span>
        </div>
        <div class="vc-stat amber">
            <span class="vc-stat-val">{{ $stats['ringing_now'] }}</span>
            <span class="vc-stat-lbl">Ringing Now</span>
            <span class="vc-stat-sub">waiting for answer</span>
        </div>
        <div class="vc-stat blue">
            <span class="vc-stat-val">{{ $stats['total_today'] }}</span>
            <span class="vc-stat-lbl">Calls Today</span>
            <span class="vc-stat-sub">initiated today</span>
        </div>
        <div class="vc-stat red">
            <span class="vc-stat-val">{{ $stats['missed_today'] }}</span>
            <span class="vc-stat-lbl">Missed Today</span>
            <span class="vc-stat-sub">unanswered calls</span>
        </div>
        <div class="vc-stat purple">
            <span class="vc-stat-val">{{ $this->formatSeconds($stats['avg_duration_today']) }}</span>
            <span class="vc-stat-lbl">Avg Duration</span>
            <span class="vc-stat-sub">completed calls today</span>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════ --}}
    {{-- All-time Totals                                      --}}
    {{-- ════════════════════════════════════════════════════ --}}
    <div class="vc-alltime">
        <div class="vc-alltime-head">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
            All-time Statistics
        </div>
        <div class="vc-alltime-row">
            <div class="vc-alltime-cell">
                <div class="vc-alltime-num">{{ number_format($stats['total_all']) }}</div>
                <div class="vc-alltime-desc">Total Calls</div>
            </div>
            <div class="vc-alltime-cell">
                <div class="vc-alltime-num" style="color:var(--green)">{{ number_format($stats['total_ended']) }}</div>
                <div class="vc-alltime-desc">Completed</div>
            </div>
            <div class="vc-alltime-cell">
                <div class="vc-alltime-num" style="color:var(--red)">{{ number_format($stats['total_missed']) }}</div>
                <div class="vc-alltime-desc">Missed</div>
            </div>
            <div class="vc-alltime-cell">
                <div class="vc-alltime-num" style="color:var(--purple)">{{ $this->formatSeconds($stats['avg_duration_all']) }}</div>
                <div class="vc-alltime-desc">Avg Duration (all time)</div>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════ --}}
    {{-- Settings Form                                        --}}
    {{-- ════════════════════════════════════════════════════ --}}
    <div class="vc-form-card">
        <div class="vc-form-head">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Call Configuration
        </div>

        {{ $this->form }}

        <div class="vc-save-row">
            <button type="button" class="vc-btn-save" wire:click="save" wire:loading.attr="disabled" wire:target="save">
                <span wire:loading.remove wire:target="save">
                    <svg style="width:16px;height:16px;display:inline;vertical-align:-3px" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                    Save Settings
                </span>
                <span wire:loading wire:target="save" style="display:flex;align-items:center;gap:6px">
                    <svg class="vc-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" opacity=".25"/><path fill="currentColor" opacity=".75" d="M4 12a8 8 0 018-8v8z"/></svg>
                    Saving…
                </span>
            </button>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════ --}}
    {{-- Current Config Snapshot                              --}}
    {{-- ════════════════════════════════════════════════════ --}}
    <div class="vc-config">
        <div class="vc-config-head">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="width:16px;height:16px"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
            Currently Active Settings (live, reflects last save)
        </div>
        <div class="vc-config-item">
            <div class="vc-config-lbl">Feature Status</div>
            <div class="vc-config-val {{ $enabled ? 'ok' : 'bad' }}">{{ $enabled ? '✓ Enabled' : '✗ Disabled' }}</div>
        </div>
        <div class="vc-config-item">
            <div class="vc-config-lbl">Ring Timeout</div>
            <div class="vc-config-val">{{ $timeout }}s</div>
        </div>
        <div class="vc-config-item">
            <div class="vc-config-lbl">Max Duration</div>
            <div class="vc-config-val">{{ $maxDur > 0 ? $maxDur . ' min' : 'Unlimited' }}</div>
        </div>
        <div class="vc-config-item">
            <div class="vc-config-lbl">Daily Limit</div>
            <div class="vc-config-val">{{ $daily > 0 ? $daily . ' calls / user / day' : 'Unlimited' }}</div>
        </div>
        <div class="vc-config-item">
            <div class="vc-config-lbl">Token Expiry</div>
            <div class="vc-config-val">{{ $expire / 60 >= 60 ? ($expire / 3600) . 'h' : ($expire / 60) . ' min' }}</div>
        </div>
        <div class="vc-config-item">
            <div class="vc-config-lbl">Require Match</div>
            <div class="vc-config-val {{ $reqMatch ? 'ok' : 'bad' }}">{{ $reqMatch ? 'Yes' : 'No' }}</div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════ --}}
    {{-- Danger Zone                                          --}}
    {{-- ════════════════════════════════════════════════════ --}}
    <div class="vc-danger">
        <div class="vc-danger-head">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
            Danger Zone
        </div>
        <div class="vc-danger-body">

            <div class="vc-danger-card">
                <h4>⚡ End All Active Calls</h4>
                <p>Immediately force-terminates every call that is currently ringing or connected
                    <strong>({{ $stats['active_now'] + $stats['ringing_now'] }} right now)</strong>.
                    Use after deploying breaking changes or in an emergency.</p>
                <button class="vc-btn-danger vc-btn-end"
                    wire:click="endAllActiveCalls"
                    wire:loading.attr="disabled"
                    wire:confirm="Are you sure? This will immediately end ALL active and ringing calls.">
                    <span wire:loading.remove wire:target="endAllActiveCalls">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        End All Calls
                    </span>
                    <span wire:loading wire:target="endAllActiveCalls" style="display:flex;align-items:center;gap:6px">
                        <svg class="vc-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" opacity=".25"/><path fill="currentColor" opacity=".75" d="M4 12a8 8 0 018-8v8z"/></svg>
                        Ending…
                    </span>
                </button>
            </div>

            <div class="vc-danger-card">
                <h4>🗑 Clear Call History</h4>
                <p>Permanently deletes all <strong>{{ number_format($stats['total_all']) }} call records</strong> from the database.
                    This action is <strong>irreversible</strong> — only use on dev / staging environments.</p>
                <button class="vc-btn-danger vc-btn-clear"
                    wire:click="clearCallHistory"
                    wire:loading.attr="disabled"
                    wire:confirm="WARNING: This permanently deletes ALL call history. This cannot be undone. Are you absolutely sure?">
                    <span wire:loading.remove wire:target="clearCallHistory">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                        Clear History
                    </span>
                    <span wire:loading wire:target="clearCallHistory" style="display:flex;align-items:center;gap:6px">
                        <svg class="vc-spin" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" opacity=".25"/><path fill="currentColor" opacity=".75" d="M4 12a8 8 0 018-8v8z"/></svg>
                        Deleting…
                    </span>
                </button>
            </div>

        </div>
    </div>

</div>{{-- .vc --}}
</x-filament-panels::page>
