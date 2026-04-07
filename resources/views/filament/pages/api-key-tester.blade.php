<x-filament-panels::page>

@php
$services = [
    ['id'=>'agora',      'label'=>'Agora',       'subtitle'=>'Video & Voice Calls',  'icon'=>'heroicon-o-video-camera',      'method'=>'testAgora',     'env_keys'=>['AGORA_APP_ID','AGORA_APP_CERTIFICATE']],
    ['id'=>'groq',       'label'=>'Groq AI',     'subtitle'=>'AI Smart Match',       'icon'=>'heroicon-o-cpu-chip',          'method'=>'testGroq',      'env_keys'=>['GROQ_API_KEY']],
    ['id'=>'iphub',      'label'=>'IPHub',        'subtitle'=>'VPN Detection',        'icon'=>'heroicon-o-shield-check',      'method'=>'testIpHub',     'env_keys'=>['IPHUB_API_KEY']],
    ['id'=>'proxycheck', 'label'=>'ProxyCheck',   'subtitle'=>'VPN Detection',        'icon'=>'heroicon-o-shield-exclamation','method'=>'testProxyCheck','env_keys'=>['PROXYCHECK_API_KEY']],
    ['id'=>'telegram',   'label'=>'Telegram',     'subtitle'=>'Admin Notifications',  'icon'=>'heroicon-o-paper-airplane',    'method'=>'testTelegram',  'env_keys'=>['TELEGRAM_BOT_TOKEN','TELEGRAM_CHAT_ID']],
    ['id'=>'mail',       'label'=>'Mail / SMTP',  'subtitle'=>'Email Delivery',       'icon'=>'heroicon-o-envelope',          'method'=>'testMailSmtp',  'env_keys'=>['MAIL_MAILER','MAIL_HOST','MAIL_PORT']],
    ['id'=>'reverb',     'label'=>'Reverb',       'subtitle'=>'WebSocket Server',     'icon'=>'heroicon-o-signal',            'method'=>'testReverb',    'env_keys'=>['REVERB_PORT','REVERB_APP_KEY']],
    ['id'=>'database',   'label'=>'Database',     'subtitle'=>'MySQL Connection',     'icon'=>'heroicon-o-circle-stack',      'method'=>'testDatabase',  'env_keys'=>['DB_HOST','DB_DATABASE']],
];

$results   = $this->results;
$hasResult = count($results) > 0;
$pass      = collect($results)->where('status','pass')->count();
$fail      = collect($results)->where('status','fail')->count();
$warn      = collect($results)->where('status','warn')->count();
$total     = count($results);
$allPassed = $hasResult && $fail === 0 && $warn === 0;
$allFailed = $hasResult && $pass === 0 && $warn === 0;
@endphp

<style>
.akt {
    --bg0:#fff; --bg1:#f8fafc; --bg2:#f1f5f9;
    --border:#e2e8f0; --border2:#cbd5e1;
    --txt:#0f172a; --txt2:#475569; --txt3:#94a3b8;
    --green:#16a34a; --gbg:#f0fdf4; --gbrd:#bbf7d0;
    --red:#dc2626;   --rbg:#fef2f2; --rbrd:#fecaca;
    --amber:#d97706; --abg:#fffbeb; --abrd:#fde68a;
    --blue:#2563eb;
    --sh:0 1px 3px rgba(0,0,0,.07);
    --shm:0 4px 16px rgba(0,0,0,.10);
}
.dark .akt {
    --bg0:#1e293b; --bg1:#0f172a; --bg2:#1e293b;
    --border:#334155; --border2:#475569;
    --txt:#f1f5f9; --txt2:#94a3b8; --txt3:#64748b;
    --green:#4ade80;  --gbg:rgba(22,163,74,.15);  --gbrd:rgba(74,222,128,.25);
    --red:#f87171;    --rbg:rgba(220,38,38,.15);   --rbrd:rgba(248,113,113,.25);
    --amber:#fbbf24;  --abg:rgba(217,119,6,.15);   --abrd:rgba(251,191,36,.25);
    --blue:#60a5fa;
}
.akt * { box-sizing:border-box; }

.akt-topbar {
    display:flex; align-items:center; justify-content:space-between;
    gap:1rem; flex-wrap:wrap; margin-bottom:1.5rem;
}
.akt-desc { font-size:.875rem; color:var(--txt2); max-width:520px; line-height:1.5; }

.akt-run-btn {
    display:inline-flex; align-items:center; gap:.5rem;
    padding:.6rem 1.25rem; border-radius:10px; font-size:.875rem;
    font-weight:700; border:none; cursor:pointer; transition:all .15s;
    background:var(--blue); color:#fff; box-shadow:0 2px 8px rgba(37,99,235,.35);
    white-space:nowrap;
}
.akt-run-btn:hover    { opacity:.88; transform:translateY(-1px); }
.akt-run-btn:disabled { opacity:.55; cursor:not-allowed; transform:none; }
.akt-run-btn svg { width:15px; height:15px; }

.akt-summary {
    display:flex; align-items:center; gap:1.5rem; flex-wrap:wrap;
    padding:.875rem 1.25rem; border-radius:12px; border:1.5px solid;
    font-size:.85rem; font-weight:600; margin-bottom:1.5rem;
}
.akt-summary.ok    { background:var(--gbg); border-color:var(--gbrd); color:var(--green); }
.akt-summary.bad   { background:var(--rbg); border-color:var(--rbrd); color:var(--red);   }
.akt-summary.mixed { background:var(--abg); border-color:var(--abrd); color:var(--amber); }
.akt-sum-chip   { display:inline-flex; align-items:center; gap:.3rem; }
.akt-sum-chip svg { width:14px; height:14px; }
.akt-sum-lbl { color:var(--txt3); font-weight:500; margin-right:.25rem; }

.akt-grid {
    display:grid; grid-template-columns:repeat(4,1fr); gap:1rem;
}
@media(max-width:1100px){ .akt-grid { grid-template-columns:repeat(2,1fr); } }
@media(max-width:600px) { .akt-grid { grid-template-columns:1fr; } }

.akt-card {
    display:flex; flex-direction:column;
    background:var(--bg0); border:1.5px solid var(--border);
    border-radius:14px; overflow:hidden;
    box-shadow:var(--sh); transition:box-shadow .2s, border-color .2s;
}
.akt-card:hover { box-shadow:var(--shm); }
.akt-card.pass  { border-color:var(--gbrd); background:var(--gbg); }
.akt-card.fail  { border-color:var(--rbrd); background:var(--rbg); }
.akt-card.warn  { border-color:var(--abrd); background:var(--abg); }

.akt-stripe { height:3px; background:var(--border2); }
.akt-card.pass .akt-stripe { background:var(--green); }
.akt-card.fail .akt-stripe { background:var(--red);   }
.akt-card.warn .akt-stripe { background:var(--amber); }

.akt-body { padding:1.1rem 1.1rem .85rem; display:flex; flex-direction:column; flex:1; gap:.8rem; }

.akt-head { display:flex; align-items:flex-start; justify-content:space-between; gap:.5rem; }
.akt-meta { display:flex; align-items:center; gap:.65rem; }
.akt-icon {
    width:36px; height:36px; border-radius:9px; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    background:var(--bg2); border:1px solid var(--border);
}
.akt-card.pass .akt-icon { background:rgba(22,163,74,.12); border-color:var(--gbrd); }
.akt-card.fail .akt-icon { background:rgba(220,38,38,.10); border-color:var(--rbrd); }
.akt-card.warn .akt-icon { background:rgba(217,119,6,.10); border-color:var(--abrd); }
.akt-icon svg { width:16px; height:16px; color:var(--txt3); }
.akt-card.pass .akt-icon svg { color:var(--green); }
.akt-card.fail .akt-icon svg { color:var(--red);   }
.akt-card.warn .akt-icon svg { color:var(--amber); }
.akt-name { font-size:.9rem; font-weight:700; color:var(--txt); line-height:1.2; }
.akt-sub  { font-size:.72rem; color:var(--txt3); margin-top:1px; }

.akt-badge {
    display:inline-flex; align-items:center; gap:.3rem;
    padding:3px 9px; border-radius:20px; font-size:.67rem;
    font-weight:700; border:1px solid; white-space:nowrap; flex-shrink:0;
}
.akt-badge svg { width:9px; height:9px; }
.akt-badge.pass { background:var(--gbg); border-color:var(--gbrd); color:var(--green); }
.akt-badge.fail { background:var(--rbg); border-color:var(--rbrd); color:var(--red);   }
.akt-badge.warn { background:var(--abg); border-color:var(--abrd); color:var(--amber); }
.akt-badge.idle { background:var(--bg2); border-color:var(--border2); color:var(--txt3); }

.akt-envs { display:flex; flex-wrap:wrap; gap:4px; }
.akt-env  {
    font-family:ui-monospace,monospace; font-size:.67rem;
    background:var(--bg1); border:1px solid var(--border2);
    color:var(--txt3); padding:2px 7px; border-radius:5px;
}
.akt-card.pass .akt-env,
.akt-card.fail .akt-env,
.akt-card.warn .akt-env { background:rgba(0,0,0,.05); }

.akt-result { display:flex; flex-direction:column; gap:4px; }
.akt-result-msg { font-size:.8rem; font-weight:600; color:var(--txt); }
.akt-result-det { font-size:.73rem; color:var(--txt2); word-break:break-all; line-height:1.4; }
.akt-result-ms  {
    display:inline-flex; align-items:center; gap:3px;
    font-size:.67rem; color:var(--txt3); font-family:ui-monospace,monospace;
}
.akt-result-ms svg { width:10px; height:10px; }

.akt-btn {
    margin-top:auto;
    display:flex; align-items:center; justify-content:center; gap:.4rem;
    width:100%; padding:.5rem; border-radius:8px;
    font-size:.78rem; font-weight:600;
    border:1px solid var(--border2); background:var(--bg1); color:var(--txt2);
    cursor:pointer; transition:all .15s;
}
.akt-btn:hover    { background:var(--bg2); color:var(--txt); border-color:var(--blue); }
.akt-btn:disabled { opacity:.5; cursor:not-allowed; }
.akt-btn svg { width:13px; height:13px; }

@keyframes akt-spin { to{ transform:rotate(360deg); } }
.akt-spin { animation:akt-spin .7s linear infinite; }
</style>

<div class="akt">

    {{-- Top bar --}}
    <div class="akt-topbar">
        <p class="akt-desc">
            Test every external service key in real-time. Each click makes a live HTTP request — results are never cached.
        </p>
        <button wire:click="testAll" wire:loading.attr="disabled" class="akt-run-btn">
            <span wire:loading.remove wire:target="testAll" style="display:inline-flex;align-items:center;gap:.4rem">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M4.5 5.653c0-1.427 1.529-2.33 2.779-1.643l11.54 6.347c1.295.712 1.295 2.573 0 3.286L7.28 19.99c-1.25.687-2.779-.217-2.779-1.643V5.653Z" clip-rule="evenodd"/></svg>
                Run All Tests
            </span>
            <span wire:loading wire:target="testAll" style="display:inline-flex;align-items:center;gap:.4rem">
                <svg class="akt-spin" fill="none" viewBox="0 0 24 24"><circle style="opacity:.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path style="opacity:.75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                Running…
            </span>
        </button>
    </div>

    {{-- Summary banner --}}
    @if($hasResult)
    <div class="akt-summary {{ $allPassed ? 'ok' : ($allFailed ? 'bad' : 'mixed') }}">
        <span class="akt-sum-lbl">{{ $total }} services tested</span>
        @if($pass)
        <span class="akt-sum-chip">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd"/></svg>
            {{ $pass }} passed
        </span>
        @endif
        @if($warn)
        <span class="akt-sum-chip" style="color:var(--amber)">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd"/></svg>
            {{ $warn }} warnings
        </span>
        @endif
        @if($fail)
        <span class="akt-sum-chip" style="color:var(--red)">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-1.72 6.97a.75.75 0 1 0-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 1 0 1.06 1.06L12 13.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L13.06 12l1.72-1.72a.75.75 0 1 0-1.06-1.06L12 10.94l-1.72-1.72Z" clip-rule="evenodd"/></svg>
            {{ $fail }} failed
        </span>
        @endif
    </div>
    @endif

    {{-- Service grid --}}
    <div class="akt-grid">
        @foreach($services as $svc)
        @php
            $result = $results[$svc['id']] ?? null;
            $status = $result['status'] ?? 'idle';
        @endphp
        <div class="akt-card {{ $status }}">
            <div class="akt-stripe"></div>
            <div class="akt-body">

                <div class="akt-head">
                    <div class="akt-meta">
                        <div class="akt-icon">
                            <x-dynamic-component :component="$svc['icon']" />
                        </div>
                        <div>
                            <div class="akt-name">{{ $svc['label'] }}</div>
                            <div class="akt-sub">{{ $svc['subtitle'] }}</div>
                        </div>
                    </div>
                    <span class="akt-badge {{ $status }}">
                        @if($status === 'pass')
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/></svg>
                            PASS
                        @elseif($status === 'fail')
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"/></svg>
                            FAIL
                        @elseif($status === 'warn')
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495ZM10 5a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 5Zm0 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/></svg>
                            WARN
                        @else —
                        @endif
                    </span>
                </div>

                <div class="akt-envs">
                    @foreach($svc['env_keys'] as $k)
                        <span class="akt-env">{{ $k }}</span>
                    @endforeach
                </div>

                @if($result)
                <div class="akt-result">
                    <div class="akt-result-msg">{{ $result['message'] }}</div>
                    @if(!empty($result['detail']))
                        <div class="akt-result-det">{{ $result['detail'] }}</div>
                    @endif
                    @if(!is_null($result['ms']))
                        <div class="akt-result-ms">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm.75-13a.75.75 0 0 0-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 0 0 0-1.5h-3.25V5Z" clip-rule="evenodd"/></svg>
                            {{ $result['ms'] }} ms
                        </div>
                    @endif
                </div>
                @endif

                <button
                    wire:click="{{ $svc['method'] }}"
                    wire:loading.attr="disabled"
                    wire:target="{{ $svc['method'] }},testAll"
                    class="akt-btn"
                >
                    <span wire:loading.remove wire:target="{{ $svc['method'] }}" style="display:inline-flex;align-items:center;gap:.35rem">
                        <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z"/></svg>
                        {{ $result ? 'Re-test' : 'Test' }}
                    </span>
                    <span wire:loading wire:target="{{ $svc['method'] }}" style="display:inline-flex;align-items:center;gap:.35rem">
                        <svg class="akt-spin" fill="none" viewBox="0 0 24 24"><circle style="opacity:.3" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path style="opacity:.75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                        Testing…
                    </span>
                </button>

            </div>
        </div>
        @endforeach
    </div>

</div>

</x-filament-panels::page>
