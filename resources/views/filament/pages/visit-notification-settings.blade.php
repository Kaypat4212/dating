<x-filament-panels::page>
@php
    $rl      = $this->rateLimitStatus;
    $enabled = filter_var(\App\Models\SiteSetting::get('telegram_visit_notifications_enabled', '1'), FILTER_VALIDATE_BOOLEAN);
    $bots    = filter_var(\App\Models\SiteSetting::get('telegram_visit_filter_bots', '1'),           FILTER_VALIDATE_BOOLEAN);
    $dc      = filter_var(\App\Models\SiteSetting::get('telegram_visit_filter_datacenter', '1'),     FILTER_VALIDATE_BOOLEAN);
    $hourly  = (int) \App\Models\SiteSetting::get('telegram_visit_hourly_limit', 30);
    $cooldown= (int) \App\Models\SiteSetting::get('telegram_visit_per_ip_cooldown', 5);
@endphp

<style>
.vns {
    --bg0: #fff; --bg1: #f8fafc; --bg2: #f1f5f9;
    --border: #e2e8f0; --border2: #cbd5e1;
    --txt: #0f172a; --txt2: #475569; --txt3: #94a3b8;
    --green: #16a34a; --gbg: #f0fdf4; --gbrd: #bbf7d0;
    --red:   #dc2626; --rbg: #fef2f2; --rbrd: #fecaca;
    --amber: #d97706; --abg: #fffbeb; --abrd: #fde68a;
    --blue:  #2563eb; --bbg: #eff6ff; --bbrd: #bfdbfe;
    --sh: 0 1px 3px rgba(0,0,0,.07);
    --shm: 0 4px 12px rgba(0,0,0,.09);
}
.dark .vns {
    --bg0:#1e293b; --bg1:#0f172a; --bg2:#1e293b;
    --border:#334155; --border2:#475569;
    --txt:#f1f5f9; --txt2:#94a3b8; --txt3:#64748b;
    --green:#4ade80; --gbg:rgba(22,163,74,.14); --gbrd:rgba(74,222,128,.25);
    --red:#f87171;   --rbg:rgba(220,38,38,.14);  --rbrd:rgba(248,113,113,.25);
    --amber:#fbbf24; --abg:rgba(217,119,6,.14);  --abrd:rgba(251,191,36,.25);
    --blue:#60a5fa;  --bbg:rgba(37,99,235,.14);  --bbrd:rgba(96,165,250,.25);
}
.vns * { box-sizing:border-box; }
.vns-page { display:flex; flex-direction:column; gap:1.25rem; }

/* Status chips */
.vns-chip {
    display:inline-flex; align-items:center; gap:5px;
    padding:3px 10px; border-radius:9999px; font-size:.7rem; font-weight:700;
    border:1px solid;
}
.vns-chip.on    { background:var(--gbg); border-color:var(--gbrd); color:var(--green); }
.vns-chip.off   { background:var(--rbg); border-color:var(--rbrd); color:var(--red); }
.vns-chip.warn  { background:var(--abg); border-color:var(--abrd); color:var(--amber); }

/* Status banner */
.vns-banner {
    display:flex; align-items:center; gap:10px;
    padding:12px 18px; border-radius:10px;
    border:1.5px solid; font-size:.875rem; font-weight:600;
}
.vns-banner.on  { background:var(--gbg); border-color:var(--gbrd); color:var(--green); }
.vns-banner.off { background:var(--rbg); border-color:var(--rbrd); color:var(--red); }
.vns-banner svg { width:18px; height:18px; flex-shrink:0; }

/* Cards */
.vns-card {
    background:var(--bg0); border:1px solid var(--border);
    border-radius:10px; overflow:hidden; box-shadow:var(--sh);
}
.vns-card-head {
    display:flex; align-items:center; gap:8px;
    padding:10px 16px; border-bottom:1px solid var(--border);
    background:var(--bg1);
    font-size:.75rem; font-weight:700; color:var(--txt2);
    text-transform:uppercase; letter-spacing:.05em;
}
.vns-card-head svg { width:14px; height:14px; color:var(--txt3); }

/* Rate limit meter */
.vns-meter-wrap { padding:16px 20px; }
.vns-meter-top {
    display:flex; justify-content:space-between; align-items:baseline;
    margin-bottom:8px;
}
.vns-meter-label { font-size:.8125rem; font-weight:600; color:var(--txt2); }
.vns-meter-numbers { font-size:.75rem; color:var(--txt3); }
.vns-meter-bar {
    height:8px; border-radius:9999px;
    background:var(--bg2); overflow:hidden;
}
.vns-meter-fill {
    height:100%; border-radius:9999px; transition:width .4s ease;
}
.vns-meter-fill.ok   { background:var(--green); }
.vns-meter-fill.warn { background:var(--amber); }
.vns-meter-fill.full { background:var(--red); }
.vns-meter-status {
    margin-top:8px; font-size:.75rem;
    display:flex; align-items:center; gap:6px;
}
.vns-meter-dot {
    width:8px; height:8px; border-radius:50%; flex-shrink:0;
}
.vns-meter-dot.ok   { background:var(--green); }
.vns-meter-dot.warn { background:var(--amber); }
.vns-meter-dot.full { background:var(--red); box-shadow:0 0 0 3px var(--rbg); }

/* What's filtered table */
.vns-filter-grid {
    display:grid; grid-template-columns:repeat(2,1fr);
    gap:0; padding:4px;
}
@media(min-width:640px){ .vns-filter-grid { grid-template-columns:repeat(3,1fr); } }
.vns-filter-item {
    display:flex; align-items:flex-start; gap:8px;
    padding:10px 12px; border-radius:8px;
    font-size:.75rem; color:var(--txt2); line-height:1.4;
}
.vns-filter-item strong { color:var(--txt); font-weight:600; display:block; }
.vns-filter-icon { font-size:.95rem; flex-shrink:0; margin-top:1px; }

/* Config snapshot */
.vns-snap {
    display:grid; grid-template-columns:repeat(2,1fr);
    padding:4px;
}
@media(min-width:768px){ .vns-snap { grid-template-columns:repeat(4,1fr); } }
.vns-snap-cell {
    padding:14px 16px;
    border-right:1px solid var(--border);
    border-bottom:1px solid var(--border);
}
.vns-snap-cell:last-child { border-right:none; }
.vns-snap-lbl { font-size:.68rem; text-transform:uppercase; letter-spacing:.05em; color:var(--txt3); margin-bottom:5px; }
.vns-snap-val { font-size:.9rem; font-weight:700; color:var(--txt); }
.vns-snap-val.ok  { color:var(--green); }
.vns-snap-val.bad { color:var(--red); }
.vns-snap-val.warn { color:var(--amber); }
</style>

<div class="vns">
<div class="vns-page">

    {{-- ── Master status ──────────────────────────────────────────────── --}}
    <div class="vns-banner {{ $enabled ? 'on' : 'off' }}">
        @if($enabled)
            <svg fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M5.25 9a6.75 6.75 0 0113.5 0v.75c0 2.123.8 4.057 2.118 5.52a.75.75 0 01-.297 1.206c-1.544.57-3.16.99-4.831 1.243a3.75 3.75 0 11-7.48 0 24.585 24.585 0 01-4.831-1.244.75.75 0 01-.298-1.205A8.217 8.217 0 005.25 9.75V9zm4.502 8.9a2.25 2.25 0 104.496 0 25.057 25.057 0 01-4.496 0z" clip-rule="evenodd"/></svg>
            Visit notifications to Telegram are <strong style="margin:0 .2rem">ENABLED</strong>
            with {{ $bots ? 'bot filtering' : 'no bot filtering' }}
            and {{ $hourly > 0 ? "a limit of {$hourly}/hour" : 'no hourly cap' }}.
        @else
            <svg fill="currentColor" viewBox="0 0 24 24"><path d="M17.25 9.75L19.5 12m0 0l2.25 2.25M19.5 12l2.25-2.25M19.5 12l-2.25 2.25m-10.5-6l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75z"/></svg>
            Visit notifications to Telegram are <strong style="margin:0 .2rem">DISABLED</strong> — all visits are silent.
        @endif
    </div>

    {{-- ── Rate limit meter ────────────────────────────────────────────── --}}
    @php
        $pct       = ($rl['limit'] > 0 && $rl['used'] > 0) ? min(100, round($rl['used'] / $rl['limit'] * 100)) : 0;
        $fillClass = $pct >= 100 ? 'full' : ($pct >= 75 ? 'warn' : 'ok');
        $dotClass  = $fillClass;
    @endphp
    <div class="vns-card">
        <div class="vns-card-head">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Live Rate Limit Status (hourly window)
        </div>
        <div class="vns-meter-wrap">
            <div class="vns-meter-top">
                <span class="vns-meter-label">
                    @if($rl['limit'] <= 0)
                        No hourly cap configured
                    @elseif($rl['isThrottled'])
                        🔴 Hourly cap reached — notifications paused
                    @else
                        Notifications sent this hour
                    @endif
                </span>
                @if($rl['limit'] > 0)
                <span class="vns-meter-numbers">{{ $rl['used'] }} / {{ $rl['limit'] }}</span>
                @endif
            </div>

            @if($rl['limit'] > 0)
            <div class="vns-meter-bar">
                <div class="vns-meter-fill {{ $fillClass }}" style="width:{{ $pct }}%"></div>
            </div>
            <div class="vns-meter-status">
                <span class="vns-meter-dot {{ $dotClass }}"></span>
                @if($rl['isThrottled'])
                    <span style="color:var(--red);font-weight:600">Cap hit · resets in {{ ceil($rl['retryAfter'] / 60) }} min · click "Reset Hourly Counter" in the header to unblock now</span>
                @else
                    <span style="color:var(--txt2)">{{ $rl['remaining'] }} slots remaining · resets automatically each hour</span>
                @endif
            </div>
            @else
            <div class="vns-meter-status">
                <span class="vns-meter-dot ok"></span>
                <span style="color:var(--txt2)">Unlimited — set a limit above to reduce Telegram spam</span>
            </div>
            @endif
        </div>
    </div>

    {{-- ── Filters that are applied ────────────────────────────────────── --}}
    <div class="vns-card">
        <div class="vns-card-head">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z"/></svg>
            What Gets Filtered
        </div>
        <div class="vns-filter-grid">
            <div class="vns-filter-item" style="{{ $bots ? '' : 'opacity:.4' }}">
                <span class="vns-filter-icon">🤖</span>
                <div>
                    <strong>Search engine bots</strong>
                    Googlebot, Bingbot, Yandex, DuckDuck
                </div>
            </div>
            <div class="vns-filter-item" style="{{ $bots ? '' : 'opacity:.4' }}">
                <span class="vns-filter-icon">📊</span>
                <div>
                    <strong>SEO crawlers</strong>
                    Ahrefs, SEMrush, Moz, Majestic
                </div>
            </div>
            <div class="vns-filter-item" style="{{ $bots ? '' : 'opacity:.4' }}">
                <span class="vns-filter-icon">🧠</span>
                <div>
                    <strong>AI training bots</strong>
                    GPTBot, CCBot, Anthropic, Cohere
                </div>
            </div>
            <div class="vns-filter-item" style="{{ $bots ? '' : 'opacity:.4' }}">
                <span class="vns-filter-icon">💻</span>
                <div>
                    <strong>HTTP libraries</strong>
                    curl, Python-requests, wget, Go, Java
                </div>
            </div>
            <div class="vns-filter-item" style="{{ $bots ? '' : 'opacity:.4' }}">
                <span class="vns-filter-icon">📱</span>
                <div>
                    <strong>Social preview bots</strong>
                    Facebookbot, Twitterbot, Slackbot
                </div>
            </div>
            <div class="vns-filter-item" style="{{ $bots ? '' : 'opacity:.4' }}">
                <span class="vns-filter-icon">🔍</span>
                <div>
                    <strong>No browser fingerprint</strong>
                    Any UA without Mozilla/Gecko/WebKit
                </div>
            </div>
            <div class="vns-filter-item" style="{{ $dc ? '' : 'opacity:.4' }}">
                <span class="vns-filter-icon">🏢</span>
                <div>
                    <strong>Datacenter IPs</strong>
                    AWS, GCP, Azure, VPS, hosting ranges
                </div>
            </div>
            <div class="vns-filter-item">
                <span class="vns-filter-icon">⏱</span>
                <div>
                    <strong>Repeat IPs</strong>
                    Same IP silenced for {{ $cooldown }} min
                </div>
            </div>
            <div class="vns-filter-item" style="{{ $hourly > 0 ? '' : 'opacity:.4' }}">
                <span class="vns-filter-icon">🚦</span>
                <div>
                    <strong>Hourly cap</strong>
                    Max {{ $hourly > 0 ? $hourly : '∞' }} notifications/hour
                </div>
            </div>
        </div>
    </div>

    {{-- ── Settings form ───────────────────────────────────────────────── --}}
    {{ $this->form }}

    {{-- ── Config snapshot ────────────────────────────────────────────── --}}
    <div class="vns-card" style="overflow:visible">
        <div class="vns-card-head">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
            Effective Settings Summary
        </div>
        <div class="vns-snap">
            <div class="vns-snap-cell">
                <div class="vns-snap-lbl">Notifications</div>
                <div class="vns-snap-val {{ $enabled ? 'ok' : 'bad' }}">{{ $enabled ? 'Enabled' : 'Disabled' }}</div>
            </div>
            <div class="vns-snap-cell">
                <div class="vns-snap-lbl">Bot filter</div>
                <div class="vns-snap-val {{ $bots ? 'ok' : 'bad' }}">{{ $bots ? 'Active' : 'Off' }}</div>
            </div>
            <div class="vns-snap-cell">
                <div class="vns-snap-lbl">Datacenter filter</div>
                <div class="vns-snap-val {{ $dc ? 'ok' : 'bad' }}">{{ $dc ? 'Active' : 'Off' }}</div>
            </div>
            <div class="vns-snap-cell" style="border-right:none">
                <div class="vns-snap-lbl">Hourly cap</div>
                <div class="vns-snap-val {{ $hourly > 0 ? 'ok' : 'warn' }}">
                    {{ $hourly > 0 ? $hourly . ' / hr' : 'Unlimited' }}
                </div>
            </div>
        </div>
    </div>

</div>{{-- .vns-page --}}
</div>{{-- .vns --}}

</x-filament-panels::page>
