<x-filament-panels::page>
@php
    $grouped = \App\Filament\Pages\ArtisanRunner::groupedCommands();
    $allCommands = \App\Filament\Pages\ArtisanRunner::allowedCommands();
    $filteredCommands = $allCommands;
    if (!empty($this->searchQuery)) {
        $q = strtolower($this->searchQuery);
        $filteredCommands = array_filter($allCommands, fn($def, $key) =>
            str_contains(strtolower($key), $q) ||
            str_contains(strtolower($def['label']), $q) ||
            str_contains(strtolower($def['desc']), $q) ||
            str_contains(strtolower($def['group']), $q),
        ARRAY_FILTER_USE_BOTH);
    }
@endphp

<style>
/* â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
   ARTISAN RUNNER â€” Complete Design System
   All colours hardcoded (theme() only works in compiled CSS)
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• */

/* â”€â”€ Token map â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.ar {
    /* surfaces */
    --s0: #ffffff;          /* card bg       */
    --s1: #f8fafc;          /* page bg       */
    --s2: #f1f5f9;          /* input / code  */
    --s3: #e2e8f0;          /* border        */
    /* text */
    --t1: #0f172a;          /* primary text  */
    --t2: #475569;          /* secondary     */
    --t3: #94a3b8;          /* muted         */
    /* accent */
    --a1: #6366f1;          /* indigo-500    */
    --a2: #4f46e5;          /* indigo-600    */
    --a-glow: rgba(99,102,241,.14);
    /* status */
    --ok: #16a34a;
    --err: #dc2626;
    /* shadows */
    --sh:  0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
    --shm: 0 4px 16px rgba(0,0,0,.08), 0 2px 6px rgba(0,0,0,.05);
    --shl: 0 10px 32px rgba(0,0,0,.1), 0 4px 12px rgba(0,0,0,.06);
}

/* Dark mode (Filament sets .dark on <html>) */
.dark .ar {
    --s0: #161b27;
    --s1: #0d1117;
    --s2: #1e2536;
    --s3: #2a3349;
    --t1: #f1f5f9;
    --t2: #94a3b8;
    --t3: #4b5563;
    --a1: #818cf8;
    --a2: #6366f1;
    --a-glow: rgba(129,140,248,.12);
    --ok: #22c55e;
    --err: #f87171;
    --sh:  0 1px 3px rgba(0,0,0,.3),  0 1px 2px rgba(0,0,0,.2);
    --shm: 0 4px 16px rgba(0,0,0,.35), 0 2px 6px rgba(0,0,0,.25);
    --shl: 0 10px 32px rgba(0,0,0,.45), 0 4px 12px rgba(0,0,0,.3);
}

/* â”€â”€ Keyframes â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
@keyframes ar-in     { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
@keyframes ar-in-r   { from{opacity:0;transform:translateX(14px)} to{opacity:1;transform:translateX(0)} }
@keyframes ar-spin   { to{transform:rotate(360deg)} }
@keyframes ar-pulse  { 0%,100%{opacity:.9} 50%{opacity:.3} }
@keyframes ar-glow   { 0%,100%{box-shadow:0 0 0 0 rgba(99,102,241,0)} 50%{box-shadow:0 0 22px 4px rgba(99,102,241,.2)} }
@keyframes ar-out-dn { from{opacity:0;transform:translateY(-10px)} to{opacity:1;transform:translateY(0)} }
@keyframes ar-shimmer{
    0%  { background-position:-200% center }
    100%{ background-position: 200% center }
}

/* â”€â”€ Animation helpers â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.ar-in    { animation: ar-in   .45s cubic-bezier(.16,1,.3,1) both }
.ar-in-r  { animation: ar-in-r .45s cubic-bezier(.16,1,.3,1) both }
.ar-d1 { animation-delay:.05s }
.ar-d2 { animation-delay:.12s }
.ar-d3 { animation-delay:.19s }
.ar-d4 { animation-delay:.26s }
.ar-spinner { animation: ar-spin .85s linear infinite }

/* â”€â”€ Reset â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.ar *,
.ar *::before,
.ar *::after { box-sizing:border-box; margin:0; padding:0 }

/* â”€â”€ Page background â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.ar { background:var(--s1) }

/* â”€â”€ Stat cards â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.ar-stat {
    display:flex; align-items:center; gap:1rem;
    background:var(--s0);
    border:1px solid var(--s3);
    border-radius:16px;
    padding:1.25rem 1.5rem;
    box-shadow:var(--sh);
    position:relative; overflow:hidden;
    transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
}
.ar-stat::after {
    content:''; position:absolute; inset:0;
    background:linear-gradient(135deg,rgba(255,255,255,.05) 0%,transparent 55%);
    pointer-events:none;
}
.ar-stat:hover { transform:translateY(-3px); box-shadow:var(--shm); border-color:var(--a1) }
.ar-stat-ico {
    width:3rem; height:3rem; border-radius:14px;
    display:flex; align-items:center; justify-content:center; flex-shrink:0;
}
.ar-stat-ico svg { width:1.375rem; height:1.375rem }
.ar-stat-v { font-size:1.75rem; font-weight:800; letter-spacing:-.04em; line-height:1; color:var(--t1) }
.ar-stat-l { font-size:.75rem; font-weight:500; color:var(--t2); margin-top:.3rem; text-transform:uppercase; letter-spacing:.04em }
.ar-dot {
    position:absolute; right:1.25rem; top:1.25rem;
    width:.55rem; height:.55rem; border-radius:9999px;
    animation: ar-pulse 2.2s ease-in-out infinite;
}

/* â”€â”€ Cards â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.ar-card {
    background:var(--s0);
    border:1px solid var(--s3);
    border-radius:16px;
    box-shadow:var(--sh);
    overflow:hidden;
    transition: box-shadow .25s ease, border-color .25s ease;
}
.ar-card-hd {
    display:flex; align-items:center; gap:.625rem;
    padding:.95rem 1.25rem;
    border-bottom:1px solid var(--s3);
}
.ar-card-hd svg { width:1.125rem; height:1.125rem; flex-shrink:0 }
.ar-card-hd-title { font-size:.9rem; font-weight:700; color:var(--t1); flex:1 }
.ar-card-bd { padding:1.125rem 1.25rem }

/* group header */
.ar-group-hd {
    display:flex; align-items:center; gap:.625rem;
    padding:.875rem 1.25rem;
    border-bottom:1px solid var(--s3);
    background:linear-gradient(90deg,rgba(99,102,241,.04) 0%,transparent 60%);
}
.dark .ar-group-hd { background:linear-gradient(90deg,rgba(129,140,248,.05) 0%,transparent 60%) }
.ar-group-hd svg { width:1.125rem; height:1.125rem; flex-shrink:0 }
.ar-group-hd-name { font-size:.875rem; font-weight:700; color:var(--t1); flex:1 }
.ar-group-cnt {
    font-size:.68rem; font-weight:700; padding:.15rem .55rem;
    border-radius:9999px; background:var(--s2); color:var(--t2);
    border:1px solid var(--s3); letter-spacing:.03em;
}

/* â”€â”€ Command grid â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.ar-grid {
    display:grid;
    grid-template-columns:repeat(auto-fill, minmax(260px,1fr));
    gap:.875rem;
    padding:1rem;
}
.ar-cmd {
    position:relative;
    padding:1rem;
    background:var(--s0);
    border:1.5px solid var(--s3);
    border-radius:12px;
    cursor:pointer;
    animation: ar-in .4s cubic-bezier(.16,1,.3,1) both;
    transition: border-color .18s ease, transform .18s ease, box-shadow .18s ease, background .18s ease;
}
.ar-cmd:hover {
    border-color:var(--a1);
    transform:translateY(-2px);
    box-shadow:var(--shm);
    background:var(--s2);
}
.ar-cmd.is-sel {
    border-color:var(--a1);
    background:var(--s2);
    box-shadow:var(--a-glow), var(--shm);
    animation: ar-in .4s cubic-bezier(.16,1,.3,1) both, ar-glow 2.5s ease-in-out infinite;
}
.ar-cmd.is-danger { border-left:3px solid #ef4444 }
.ar-cmd svg.ar-ico { width:1.125rem; height:1.125rem; flex-shrink:0; margin-top:1px }
.ar-cmd svg.ar-chk { width:1.125rem; height:1.125rem }
.ar-cmd-title { font-size:.8375rem; font-weight:600; color:var(--t1) }
.ar-cmd-desc  { font-size:.775rem; color:var(--t2); line-height:1.5; margin-top:.25rem }
.ar-mono {
    display:inline-block; margin-top:.5rem;
    padding:.2rem .5rem;
    background:var(--s2); border:1px solid var(--s3);
    border-radius:6px;
    font-size:.68rem; color:var(--t3);
    font-family:'Cascadia Code','Fira Code',ui-monospace,monospace;
}
.dark .ar-mono { background:#0d1117; border-color:#30363d }

/* â”€â”€ Badge â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.ar-badge {
    display:inline-flex; align-items:center;
    padding:.15rem .45rem;
    border-radius:9999px;
    font-size:.65rem; font-weight:700; letter-spacing:.04em; text-transform:uppercase; white-space:nowrap;
}
.ar-ok   { background:rgba(34,197,94,.1);  color:#15803d; border:1px solid rgba(34,197,94,.2) }
.ar-err  { background:rgba(239,68,68,.1);  color:#b91c1c; border:1px solid rgba(239,68,68,.2) }
.ar-info { background:rgba(99,102,241,.1); color:#4338ca; border:1px solid rgba(99,102,241,.2) }
.dark .ar-ok   { color:#4ade80; border-color:rgba(74,222,128,.25) }
.dark .ar-err  { color:#f87171; border-color:rgba(248,113,113,.25) }
.dark .ar-info { color:#a5b4fc; border-color:rgba(165,180,252,.25) }

/* â”€â”€ Search â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.ar-search-wrap { position:relative }
.ar-search-ico { position:absolute; left:.875rem; top:50%; transform:translateY(-50%); width:1rem; height:1rem; color:var(--t3); pointer-events:none }
.ar-search {
    width:100%;
    padding:.7rem 1rem .7rem 2.75rem;
    background:var(--s2); border:1.5px solid var(--s3);
    border-radius:10px; color:var(--t1); font-size:.875rem;
    outline:none;
    transition: border-color .2s ease, box-shadow .2s ease;
}
.ar-search::placeholder { color:var(--t3) }
.ar-search:focus { border-color:var(--a1); box-shadow:0 0 0 3px rgba(99,102,241,.12) }

/* â”€â”€ Exec panel â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.ar-exec-panel {
    background:var(--s0); border:1px solid var(--s3);
    border-radius:16px; box-shadow:var(--sh); overflow:hidden;
    animation: ar-in-r .38s cubic-bezier(.16,1,.3,1) both;
}
.ar-exec-hd {
    display:flex; align-items:center; gap:.625rem;
    padding:.95rem 1.25rem; border-bottom:1px solid var(--s3);
    background:linear-gradient(90deg,rgba(99,102,241,.06) 0%,transparent 65%);
}
.dark .ar-exec-hd { background:linear-gradient(90deg,rgba(129,140,248,.07) 0%,transparent 65%) }
.ar-exec-hd svg { width:1.125rem; height:1.125rem; flex-shrink:0 }
.ar-exec-preview {
    background:var(--s2); border:1px solid var(--s3); border-radius:10px; padding:1rem;
}
.ar-exec-title { font-weight:600; font-size:.875rem; color:var(--t1); margin-bottom:.3rem }
.ar-exec-desc  { font-size:.79rem; color:var(--t2); line-height:1.55; margin-bottom:.75rem }
.ar-terminal {
    background:#0d1117; border:1px solid #30363d; border-radius:8px;
    padding:.75rem 1rem;
    font-family:'Cascadia Code','Fira Code',ui-monospace,monospace;
    font-size:.78rem; color:#7ee787; overflow-x:auto;
}
.ar-terminal-ps { color:#79c0ff; margin-right:.25rem; user-select:none }

/* â”€â”€ Buttons â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.ar-btn {
    width:100%; display:inline-flex; align-items:center; justify-content:center; gap:.5rem;
    padding:.8rem 1.25rem; border-radius:10px;
    font-size:.875rem; font-weight:600; letter-spacing:.01em;
    border:none; cursor:pointer; color:#fff;
    position:relative; overflow:hidden;
    transition: transform .15s ease, box-shadow .15s ease, opacity .15s ease;
}
.ar-btn::after {
    content:''; position:absolute; inset:0;
    background:linear-gradient(120deg,transparent 30%,rgba(255,255,255,.22) 50%,transparent 70%);
    background-size:200% 100%; background-position:-200% center;
    transition:background-position .6s ease;
}
.ar-btn:hover::after   { background-position:200% center }
.ar-btn:hover          { transform:translateY(-2px) }
.ar-btn:active         { transform:translateY(0) }
.ar-btn:disabled       { opacity:.5; cursor:not-allowed; transform:none }
.ar-btn svg { width:1rem; height:1rem; flex-shrink:0 }
.ar-btn-primary {
    background:linear-gradient(135deg,#6366f1 0%,#4f46e5 100%);
    box-shadow:0 2px 12px rgba(99,102,241,.35);
}
.ar-btn-primary:hover  { box-shadow:0 4px 20px rgba(99,102,241,.45) }
.ar-btn-danger {
    background:linear-gradient(135deg,#ef4444 0%,#dc2626 100%);
    box-shadow:0 2px 12px rgba(239,68,68,.3);
}
.ar-btn-danger:hover   { box-shadow:0 4px 20px rgba(239,68,68,.4) }

/* â”€â”€ Warning banner â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.ar-warn {
    display:flex; gap:.625rem;
    padding:.875rem 1rem;
    background:rgba(239,68,68,.07);
    border:1px solid rgba(239,68,68,.22);
    border-radius:10px;
}
.ar-warn svg { width:1.125rem; height:1.125rem; flex-shrink:0; margin-top:1px; color:#ef4444 }
.ar-warn-t { font-size:.8375rem; font-weight:600; color:#dc2626 }
.ar-warn-b { font-size:.77rem; color:#ef4444; margin-top:.2rem; line-height:1.45 }
.dark .ar-warn-t { color:#f87171 }
.dark .ar-warn-b { color:#fca5a5 }

/* â”€â”€ Recent row â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.ar-recent {
    display:flex; align-items:center; gap:.75rem;
    padding:.625rem .75rem; border-radius:8px; cursor:pointer;
    transition:background .15s ease;
}
.ar-recent:hover { background:var(--s2) }
.ar-recent svg { width:.875rem; height:.875rem; color:var(--t3); flex-shrink:0 }
.ar-recent-l { font-size:.82rem; font-weight:500; color:var(--t1); overflow:hidden; text-overflow:ellipsis; white-space:nowrap }
.ar-recent-t { font-size:.7rem; color:var(--t3); margin-top:1px }

/* â”€â”€ Empty state â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.ar-empty { text-align:center; padding:3rem 2rem }
.ar-empty svg { width:2.5rem; height:2.5rem; color:var(--t3); margin:0 auto .875rem; display:block }
.ar-empty-t { font-size:.9375rem; font-weight:600; color:var(--t1); margin-bottom:.375rem }
.ar-empty-b { font-size:.8125rem; color:var(--t2) }

/* â”€â”€ Placeholder sidebar â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.ar-placeholder {
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    text-align:center; padding:2.5rem 1.5rem; gap:.75rem;
}
.ar-placeholder svg { width:2.5rem; height:2.5rem; color:var(--t3); display:block }
.ar-placeholder p { font-size:.8375rem; color:var(--t2); line-height:1.55 }

/* â”€â”€ Output terminal â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
.ar-output { animation: ar-out-dn .4s cubic-bezier(.16,1,.3,1) both }
.ar-pre {
    background:#0d1117; border:1px solid #30363d;
    border-radius:12px; padding:1.25rem;
    font-family:'Cascadia Code','Fira Code',ui-monospace,monospace;
    font-size:.79rem; line-height:1.75; color:#e6edf3;
    white-space:pre-wrap; word-break:break-word;
    max-height:420px; overflow-y:auto; overflow-x:auto;
}
.ar-pre.ar-ok-bg  { border-color:rgba(34,197,94,.35);  color:#7ee787 }
.ar-pre.ar-err-bg { border-color:rgba(239,68,68,.35);  color:#f87171 }
.ar-pre::-webkit-scrollbar { width:5px; height:5px }
.ar-pre::-webkit-scrollbar-track { background:transparent }
.ar-pre::-webkit-scrollbar-thumb { background:#374151; border-radius:3px }

.ar-clear {
    font-size:.78rem; padding:.35rem .75rem; border-radius:7px;
    color:var(--t2); background:transparent;
    border:1px solid var(--s3); cursor:pointer;
    transition:background .15s ease, color .15s ease, border-color .15s ease;
}
.ar-clear:hover { background:var(--s2); color:var(--t1); border-color:var(--a1) }
</style>

{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
<div class="ar space-y-5">

    {{-- â–“â–“  STAT STRIP  â–“â–“ --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        <div class="ar-stat ar-in ar-d1">
            <div class="ar-stat-ico" style="background:rgba(99,102,241,.12)">
                <x-heroicon-o-command-line style="color:#6366f1"/>
            </div>
            <div>
                <div class="ar-stat-v">{{ count($allCommands) }}</div>
                <div class="ar-stat-l">Commands</div>
            </div>
            <span class="ar-dot" style="background:#6366f1"></span>
        </div>

        <div class="ar-stat ar-in ar-d2">
            <div class="ar-stat-ico" style="background:rgba(34,197,94,.12)">
                <x-heroicon-o-check-circle style="color:#22c55e"/>
            </div>
            <div>
                <div class="ar-stat-v" style="{{ $ran ? ($exitCode===0 ? 'color:#22c55e' : 'color:#ef4444') : 'color:var(--t3)' }}">
                    {{ $ran ? ($exitCode===0 ? 'âœ“' : 'âœ—') : 'â€”' }}
                </div>
                <div class="ar-stat-l">{{ $ran ? ($exitCode===0 ? 'Succeeded' : 'Failed') : 'No run yet' }}</div>
            </div>
            @if($ran)
                <span class="ar-dot" style="background:{{ $exitCode===0 ? '#22c55e' : '#ef4444' }}"></span>
            @endif
        </div>

        <div class="ar-stat ar-in ar-d3">
            <div class="ar-stat-ico" style="background:rgba(168,85,247,.12)">
                <x-heroicon-o-clock style="color:#a855f7"/>
            </div>
            <div>
                <div class="ar-stat-v" style="font-size:{{ $lastRunAt ? '.95rem' : '1.75rem' }};letter-spacing:0;line-height:1.2">
                    {{ $lastRunAt ?: 'â€”' }}
                </div>
                <div class="ar-stat-l">Last Executed</div>
            </div>
        </div>
    </div>

    {{-- â–“â–“  MAIN GRID  â–“â–“ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- â–‘ LEFT â€” command library â–‘ --}}
        <div class="lg:col-span-2 space-y-4 ar-in ar-d2">

            {{-- Search --}}
            <div class="ar-card">
                <div class="ar-card-hd">
                    <x-heroicon-o-squares-2x2 style="color:var(--a1)"/>
                    <span class="ar-card-hd-title">Command Library</span>
                    <span class="ar-badge ar-info">{{ count($allCommands) }} available</span>
                </div>
                <div class="ar-card-bd">
                    <div class="ar-search-wrap">
                        <x-heroicon-o-magnifying-glass class="ar-search-ico"/>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="searchQuery"
                            placeholder="Search commands by name, description or groupâ€¦"
                            class="ar-search"
                        />
                    </div>
                </div>
            </div>

            {{-- Empty state --}}
            @if(empty($filteredCommands))
                <div class="ar-card ar-in">
                    <div class="ar-empty">
                        <x-heroicon-o-magnifying-glass/>
                        <div class="ar-empty-t">No commands found</div>
                        <div class="ar-empty-b">Try a different keyword, or clear the search box to see everything.</div>
                    </div>
                </div>
            @else
                @foreach($grouped as $groupName => $groupCmds)
                    @php $gc = array_intersect_key($groupCmds, $filteredCommands) @endphp
                    @if(!empty($gc))
                        <div class="ar-card ar-in">
                            {{-- group header --}}
                            <div class="ar-group-hd">
                                @switch($groupName)
                                    @case('Cache Management')
                                        <x-heroicon-o-arrow-path style="color:#6366f1"/>@break
                                    @case('Database Operations')
                                        <x-heroicon-o-circle-stack style="color:#22c55e"/>@break
                                    @case('Queue Management')
                                        <x-heroicon-o-queue-list style="color:#a855f7"/>@break
                                    @case('Application Maintenance')
                                        <x-heroicon-o-shield-exclamation style="color:#ef4444"/>@break
                                    @default
                                        <x-heroicon-o-cog-6-tooth style="color:var(--t3)"/>
                                @endswitch
                                <span class="ar-group-hd-name">{{ $groupName }}</span>
                                <span class="ar-group-cnt">{{ count($gc) }}</span>
                            </div>
                            {{-- command cards grid --}}
                            <div class="ar-grid">
                                @foreach($gc as $key => $def)
                                    <div
                                        wire:click="selectCommand('{{ $key }}')"
                                        class="ar-cmd {{ $selectedCommand===$key ? 'is-sel' : '' }} {{ $def['dangerous'] ? 'is-danger' : '' }}"
                                        style="animation-delay:{{ $loop->index * .045 }}s"
                                    >
                                        <div style="display:flex;align-items:flex-start;gap:.75rem">
                                            @if($def['icon'] ?? null)
                                                <x-dynamic-component
                                                    :component="$def['icon']"
                                                    class="ar-ico"
                                                    style="color:{{ $def['dangerous'] ? '#ef4444' : '#6366f1' }}"
                                                />
                                            @endif
                                            <div style="flex:1;min-width:0">
                                                <div style="display:flex;align-items:center;gap:.375rem;flex-wrap:wrap;margin-bottom:.3rem">
                                                    <span class="ar-cmd-title">{{ $def['label'] }}</span>
                                                    <span class="ar-badge {{ $def['dangerous'] ? 'ar-err' : 'ar-ok' }}">
                                                        {{ $def['dangerous'] ? 'Danger' : 'Safe' }}
                                                    </span>
                                                </div>
                                                <div class="ar-cmd-desc">{{ $def['desc'] }}</div>
                                                <code class="ar-mono">php artisan {{ $key }}</code>
                                            </div>
                                        </div>
                                        @if($selectedCommand === $key)
                                            <div style="position:absolute;top:.625rem;right:.625rem">
                                                <x-heroicon-s-check-circle class="ar-chk" style="color:var(--a1)"/>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            @endif
        </div>

        {{-- â–‘ RIGHT â€” run panel â–‘ --}}
        <div class="space-y-4 ar-in ar-d3">

            @if($selectedCommand)
                @php $def = $allCommands[$selectedCommand] @endphp
                <div class="ar-exec-panel">
                    <div class="ar-exec-hd">
                        <x-heroicon-o-bolt style="color:#6366f1"/>
                        <span class="ar-card-hd-title">Execute Command</span>
                    </div>
                    <div class="ar-card-bd space-y-3">
                        {{-- preview --}}
                        <div class="ar-exec-preview">
                            <div class="ar-exec-title">{{ $def['label'] }}</div>
                            <div class="ar-exec-desc">{{ $def['desc'] }}</div>
                            <div class="ar-terminal">
                                <span class="ar-terminal-ps">$</span>php artisan {{ $selectedCommand }}@foreach($def['args'] as $flag => $val) {{ is_bool($val) ? $flag : "$flag=$val" }}@endforeach
                            </div>
                        </div>

                        @if($def['dangerous'])
                            <div class="ar-warn">
                                <x-heroicon-o-exclamation-triangle/>
                                <div>
                                    <div class="ar-warn-t">Dangerous Command</div>
                                    <div class="ar-warn-b">This can affect your live application. Double-check before proceeding.</div>
                                </div>
                            </div>
                            <button
                                wire:click="runCommand"
                                wire:confirm="âš ï¸ DANGEROUS operation â€” this may affect production data. Proceed?"
                                wire:loading.attr="disabled"
                                class="ar-btn ar-btn-danger"
                            >
                                @if($isRunning)
                                    <x-heroicon-o-arrow-path class="ar-spinner"/>Executingâ€¦
                                @else
                                    <x-heroicon-o-exclamation-triangle/>Run Dangerous Command
                                @endif
                            </button>
                        @else
                            <button
                                wire:click="runCommand"
                                wire:loading.attr="disabled"
                                class="ar-btn ar-btn-primary"
                            >
                                @if($isRunning)
                                    <x-heroicon-o-arrow-path class="ar-spinner"/>Executingâ€¦
                                @else
                                    <x-heroicon-o-play/>Execute Command
                                @endif
                            </button>
                        @endif
                    </div>
                </div>
            @else
                <div class="ar-card">
                    <div class="ar-placeholder">
                        <x-heroicon-o-cursor-arrow-rays/>
                        <p>Click any command card on the left to preview and run it here.</p>
                    </div>
                </div>
            @endif

            {{-- Recent commands --}}
            @if(!empty($recentCommands))
                <div class="ar-card ar-in ar-d4">
                    <div class="ar-card-hd">
                        <x-heroicon-o-clock style="color:var(--t3)"/>
                        <span class="ar-card-hd-title">Recent Commands</span>
                    </div>
                    <div class="ar-card-bd" style="padding:.5rem .75rem">
                        @foreach($recentCommands as $recent)
                            <div wire:click="selectCommand('{{ $recent['key'] }}')" class="ar-recent">
                                <div style="flex:1;min-width:0">
                                    <div class="ar-recent-l">{{ $recent['label'] }}</div>
                                    <div class="ar-recent-t">{{ $recent['ran_at'] }}</div>
                                </div>
                                <x-heroicon-o-arrow-right/>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- â–“â–“  OUTPUT  â–“â–“ --}}
    @if($ran)
        <div class="ar-card ar-output">
            <div class="ar-card-hd" style="justify-content:space-between">
                <div style="display:flex;align-items:center;gap:.625rem">
                    <x-heroicon-o-command-line style="color:var(--t3);width:1.125rem;height:1.125rem"/>
                    <span class="ar-card-hd-title">Output</span>
                    <span class="ar-badge {{ $exitCode===0 ? 'ar-ok' : 'ar-err' }}">
                        {{ $exitCode===0 ? 'Success' : 'Error' }} &middot; exit {{ $exitCode }}
                    </span>
                </div>
                <button wire:click="clearOutput" class="ar-clear">Clear</button>
            </div>
            <div class="ar-card-bd">
                <pre class="ar-pre {{ $exitCode===0 ? 'ar-ok-bg' : 'ar-err-bg' }}">{{ $output }}</pre>
            </div>
        </div>
    @endif

</div>
{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}

</x-filament-panels::page>

