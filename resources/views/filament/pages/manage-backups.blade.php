<x-filament-panels::page>
@php
    $backups      = $this->getBackups();
    $backupCount  = count($backups);
    $totalBytes   = 0;
    foreach ($backups as $b) {
        preg_match('/[\d.]+/', $b['size'], $m);
        $val = floatval($m[0] ?? 0);
        if (str_contains($b['size'], 'GB')) $val *= 1024;
        $totalBytes += $val;
    }
    $totalSize    = $totalBytes > 0 ? round($totalBytes, 1) . ' MB' : '—';
    $latestBackup = $backupCount > 0 ? $backups[0]['created_at'] : null;
@endphp

<style>
.bk {
    --bg0:#fff; --bg1:#f8fafc; --bg2:#f1f5f9;
    --border:#e2e8f0; --border2:#cbd5e1;
    --txt:#0f172a; --txt2:#475569; --txt3:#94a3b8; --txt4:#64748b;
    --green:#16a34a; --gbg:#f0fdf4; --gbrd:#bbf7d0;
    --amber:#d97706; --abg:#fffbeb; --abrd:#fde68a;
    --blue:#2563eb; --bbg:rgba(37,99,235,.07); --bbrd:rgba(37,99,235,.2);
    --red:#dc2626; --rbg:#fef2f2; --rbrd:#fecaca;
    --rose:#e11d48; --indigo:#4f46e5; --emerald:#059669;
    --sh:0 1px 3px rgba(0,0,0,.07);
    font-family:inherit;
}
.dark .bk {
    --bg0:#1e293b; --bg1:#0f172a; --bg2:#1e293b;
    --border:#334155; --border2:#475569;
    --txt:#f1f5f9; --txt2:#94a3b8; --txt3:#64748b; --txt4:#475569;
    --green:#4ade80;  --gbg:rgba(22,163,74,.12);   --gbrd:rgba(74,222,128,.22);
    --amber:#fbbf24;  --abg:rgba(217,119,6,.12);   --abrd:rgba(251,191,36,.22);
    --blue:#60a5fa;   --bbg:rgba(96,165,250,.07);  --bbrd:rgba(96,165,250,.2);
    --red:#f87171;    --rbg:rgba(220,38,38,.12);   --rbrd:rgba(248,113,113,.22);
    --rose:#fb7185;   --indigo:#818cf8; --emerald:#34d399;
}
.bk * { box-sizing:border-box; }
.bk-layout { display:flex; flex-direction:column; gap:.875rem; }

/* Stat cards */
.bk-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:.875rem; }
@media(max-width:700px){ .bk-stats { grid-template-columns:1fr; } }
.bk-stat {
    background:var(--bg0); border:1.5px solid var(--border);
    border-radius:14px; padding:1.1rem 1.25rem;
    box-shadow:var(--sh); display:flex; flex-direction:column; gap:.55rem;
}
.bk-stat-head { display:flex; align-items:center; justify-content:space-between; }
.bk-stat-icon {
    width:32px; height:32px; border-radius:8px; flex-shrink:0;
    display:flex; align-items:center; justify-content:center; border:1px solid;
}
.bk-stat-icon svg { width:14px; height:14px; }
.bk-stat-icon.rose    { background:rgba(225,29,72,.1);  border-color:rgba(225,29,72,.25); }
.bk-stat-icon.rose svg { color:#e11d48; }
.bk-stat-icon.indigo  { background:rgba(79,70,229,.1);  border-color:rgba(79,70,229,.25); }
.bk-stat-icon.indigo svg { color:#4f46e5; }
.bk-stat-icon.emerald { background:rgba(5,150,105,.1);  border-color:rgba(5,150,105,.25); }
.bk-stat-icon.emerald svg { color:#059669; }
.dark .bk-stat-icon.rose svg    { color:#fb7185; }
.dark .bk-stat-icon.indigo svg  { color:#818cf8; }
.dark .bk-stat-icon.emerald svg { color:#34d399; }
.bk-stat-label { font-size:.67rem; font-weight:700; letter-spacing:.09em; text-transform:uppercase; color:var(--txt3); }
.bk-stat-val   { font-size:1.55rem; font-weight:800; color:var(--txt); line-height:1.1; }
.bk-stat-val.sm { font-size:.95rem; font-weight:700; line-height:1.35; }
.bk-stat-sub   { font-size:.68rem; color:var(--txt3); }
.bk-badge {
    display:inline-flex; align-items:center; gap:.35rem; width:fit-content;
    font-size:.67rem; font-weight:600; padding:3px 9px;
    border-radius:20px; border:1px solid;
}
.bk-badge.ok   { color:var(--green); background:var(--gbg); border-color:var(--gbrd); }
.bk-badge.warn { color:var(--amber); background:var(--abg); border-color:var(--abrd); }
.bk-dot { width:6px; height:6px; border-radius:50%; background:currentColor; }
.bk-badge.ok .bk-dot { animation:bkp 2s infinite; }
@keyframes bkp { 0%,100%{opacity:1} 50%{opacity:.35} }

/* Info panel */
.bk-info {
    background:var(--bbg); border:1.5px solid var(--bbrd);
    border-radius:12px; padding:.875rem 1.1rem;
    display:flex; align-items:flex-start; gap:.75rem;
}
.bk-info svg.icon { width:14px; height:14px; color:var(--blue); flex-shrink:0; margin-top:1px; }
.bk-info-title { font-size:.78rem; font-weight:700; color:var(--txt); margin-bottom:.4rem; }
.bk-chips { display:flex; flex-wrap:wrap; gap:.35rem; }
.bk-chip {
    display:inline-flex; align-items:center; gap:.3rem;
    font-size:.69rem; color:var(--txt2);
    background:var(--bg1); border:1px solid var(--border2);
    padding:3px 8px; border-radius:5px;
}
.bk-chip svg { width:11px; height:11px; color:var(--blue); }
.bk-note { margin-top:.45rem; font-size:.67rem; color:var(--txt3); }
.bk-note code { font-family:ui-monospace,monospace; color:var(--txt4); }

/* Table card */
.bk-card {
    background:var(--bg0); border:1.5px solid var(--border);
    border-radius:14px; overflow:hidden; box-shadow:var(--sh);
}
.bk-card-head {
    display:flex; align-items:center; justify-content:space-between;
    padding:.7rem 1.1rem; border-bottom:1px solid var(--border); background:var(--bg1);
}
.bk-card-title { display:flex; align-items:center; gap:.45rem; font-size:.78rem; font-weight:700; color:var(--txt); }
.bk-card-title svg { width:13px; height:13px; color:var(--rose); }
.bk-cbadge {
    background:rgba(225,29,72,.1); border:1px solid rgba(225,29,72,.2);
    color:var(--rose); font-size:.63rem; font-weight:800; padding:2px 7px; border-radius:20px;
}
.bk-card-meta { display:flex; align-items:center; gap:.3rem; font-size:.67rem; color:var(--txt3); }
.bk-card-meta svg { width:11px; height:11px; }

/* Empty */
.bk-empty { display:flex; flex-direction:column; align-items:center; gap:.7rem; padding:3rem 1.5rem; text-align:center; }
.bk-empty-ico { width:48px; height:48px; border-radius:12px; display:flex; align-items:center; justify-content:center; background:var(--bg2); border:1.5px solid var(--border); }
.bk-empty-ico svg { width:20px; height:20px; color:var(--txt3); }
.bk-empty-t { font-size:.83rem; font-weight:600; color:var(--txt2); }
.bk-empty-s { font-size:.76rem; color:var(--txt3); margin-top:2px; }

/* Table */
.bk-tbl { width:100%; border-collapse:collapse; font-size:.79rem; }
.bk-tbl th {
    padding:.55rem 1rem; text-align:left;
    font-size:.63rem; font-weight:700; letter-spacing:.09em; text-transform:uppercase;
    color:var(--txt3); border-bottom:1px solid var(--border); background:var(--bg1);
}
.bk-tbl th:last-child { text-align:right; }
.bk-tbl td { padding:.7rem 1rem; border-bottom:1px solid var(--border); vertical-align:middle; }
.bk-tbl tr:last-child td { border-bottom:none; }
.bk-tbl tr:hover td { background:var(--bg1); }

.bk-file { display:flex; align-items:center; gap:.6rem; }
.bk-file-ico {
    width:28px; height:28px; border-radius:7px; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    background:rgba(79,70,229,.09); border:1px solid rgba(79,70,229,.2);
}
.bk-file-ico svg { width:12px; height:12px; color:var(--indigo); }
.dark .bk-file-ico { background:rgba(129,140,248,.09); border-color:rgba(129,140,248,.2); }
.dark .bk-file-ico svg { color:#818cf8; }
.bk-fname { font-family:ui-monospace,monospace; font-size:.71rem; font-weight:500; color:var(--txt); max-width:240px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.bk-szchip { display:inline-flex; align-items:center; font-size:.69rem; color:var(--txt2); background:var(--bg2); border:1px solid var(--border2); padding:2px 7px; border-radius:5px; font-family:ui-monospace,monospace; }
.bk-drow { display:flex; align-items:center; gap:.35rem; font-size:.71rem; color:var(--txt2); }
.bk-drow svg { width:11px; height:11px; color:var(--txt3); }
.bk-acts { display:flex; align-items:center; justify-content:flex-end; gap:.4rem; }
.bk-btn {
    display:inline-flex; align-items:center; gap:.3rem;
    padding:.28rem .7rem; border-radius:7px; font-size:.71rem; font-weight:600;
    border:1.5px solid; cursor:pointer; text-decoration:none;
    transition:all .15s; white-space:nowrap; line-height:1;
}
.bk-btn svg { width:11px; height:11px; flex-shrink:0; }
.bk-dl { color:var(--indigo); background:rgba(79,70,229,.07); border-color:rgba(79,70,229,.25); }
.bk-dl:hover { background:rgba(79,70,229,.14); border-color:var(--indigo); }
.dark .bk-dl { color:#818cf8; background:rgba(129,140,248,.07); border-color:rgba(129,140,248,.25); }
.dark .bk-dl:hover { background:rgba(129,140,248,.14); }
.bk-del { color:var(--red); background:rgba(220,38,38,.06); border-color:rgba(220,38,38,.2); }
.bk-del:hover { background:rgba(220,38,38,.12); border-color:var(--red); }
.bk-del:disabled { opacity:.4; cursor:not-allowed; }
.bk-tfoot { padding:.5rem 1rem; border-top:1px solid var(--border); font-size:.67rem; color:var(--txt3); background:var(--bg1); }

/* Restore */
.bk-rest { border:1.5px solid var(--border); border-radius:12px; overflow:hidden; background:var(--bg0); }
.bk-rest summary {
    display:flex; align-items:center; gap:.6rem; list-style:none;
    padding:.75rem 1.1rem; cursor:pointer; user-select:none;
    font-size:.78rem; font-weight:700; color:var(--txt);
}
.bk-rest summary:hover { background:var(--bg1); }
.bk-rest summary .ricon { width:13px; height:13px; color:var(--amber); }
.bk-rest summary .chev  { width:12px; height:12px; color:var(--txt3); margin-left:auto; transition:transform .2s; }
details[open].bk-rest summary .chev { transform:rotate(180deg); }
.bk-rest-body { border-top:1px solid var(--border); padding:.85rem 1.1rem; }
.bk-steps { display:flex; flex-direction:column; gap:.55rem; }
.bk-step { display:flex; align-items:flex-start; gap:.6rem; font-size:.77rem; color:var(--txt2); line-height:1.55; }
.bk-num {
    width:20px; height:20px; border-radius:50%; flex-shrink:0; margin-top:1px;
    display:flex; align-items:center; justify-content:center;
    background:rgba(217,119,6,.1); border:1px solid rgba(217,119,6,.25);
    font-size:.62rem; font-weight:800; color:var(--amber);
}
.bk-step code { font-family:ui-monospace,monospace; font-size:.69rem; background:var(--bg2); border:1px solid var(--border2); color:var(--txt); padding:1px 5px; border-radius:4px; }

@media(max-width:640px) { .bk-hism { display:none !important; } }
@media(max-width:800px) { .bk-himd { display:none !important; } }
</style>

<div class="bk">
<div class="bk-layout">

{{-- Stats --}}
<div class="bk-stats">
    <div class="bk-stat">
        <div class="bk-stat-head">
            <span class="bk-stat-label">Total Backups</span>
            <div class="bk-stat-icon rose">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M2 3a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H2Z"/><path fill-rule="evenodd" d="M2 7.5h16l-.811 7.71a2 2 0 0 1-1.99 1.79H4.802a2 2 0 0 1-1.99-1.79L2 7.5ZM7 11a1 1 0 0 1 1-1h4a1 1 0 1 1 0 2H8a1 1 0 0 1-1-1Z" clip-rule="evenodd"/></svg>
            </div>
        </div>
        <div class="bk-stat-val">{{ $backupCount }}</div>
        <div class="bk-stat-sub">snapshot{{ $backupCount !== 1 ? 's' : '' }} on disk</div>
    </div>
    <div class="bk-stat">
        <div class="bk-stat-head">
            <span class="bk-stat-label">Storage Used</span>
            <div class="bk-stat-icon indigo">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M3.505 2.365A41.369 41.369 0 0 1 9 2c1.863 0 3.697.124 5.495.365 1.247.167 2.18 1.108 2.435 2.268a4.45 4.45 0 0 0-.577-.069 43.141 43.141 0 0 0-4.706 0C9.229 4.696 7.5 6.727 7.5 8.998v2.24c0 1.413.67 2.735 1.76 3.562l-2.98 2.98A.75.75 0 0 1 5 17.25v-3.443c-.501-.048-1-.106-1.495-.172C2.033 13.438 1 12.162 1 10.72V5.28c0-1.441 1.033-2.717 2.505-2.914Z"/><path d="M14 6c-.762 0-1.52.02-2.271.062C10.157 6.148 9 7.472 9 8.998v2.24c0 1.519 1.141 2.841 2.705 2.97.288.024.578.05.869.08.433.044.86.098 1.29.162.745.113 1.136.95.81 1.627L13.56 18h3.19a.75.75 0 0 0 .75-.75 41.32 41.32 0 0 0 .61-5 43.141 43.141 0 0 0-4.11.256V6Z"/></svg>
            </div>
        </div>
        <div class="bk-stat-val">{{ $totalSize }}</div>
        <div class="bk-stat-sub">in <code style="font-family:ui-monospace,monospace;font-size:.64rem">storage/app/backups/</code></div>
    </div>
    <div class="bk-stat">
        <div class="bk-stat-head">
            <span class="bk-stat-label">Last Backup</span>
            <div class="bk-stat-icon emerald">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm.75-13a.75.75 0 0 0-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 0 0 0-1.5h-3.25V5Z" clip-rule="evenodd"/></svg>
            </div>
        </div>
        <div class="bk-stat-val sm">{{ $latestBackup ?? 'No backups yet' }}</div>
        <div>
            @if($latestBackup)
                <span class="bk-badge ok"><span class="bk-dot"></span>Up to date</span>
            @else
                <span class="bk-badge warn"><span class="bk-dot"></span>None yet</span>
            @endif
        </div>
    </div>
</div>

{{-- Info panel --}}
<div class="bk-info">
    <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z" clip-rule="evenodd"/></svg>
    <div>
        <div class="bk-info-title">What gets backed up</div>
        <div class="bk-chips">
            <span class="bk-chip">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M3.505 2.365A41.369 41.369 0 0 1 9 2c1.863 0 3.697.124 5.495.365 1.247.167 2.18 1.108 2.435 2.268a4.45 4.45 0 0 0-.577-.069 43.141 43.141 0 0 0-4.706 0C9.229 4.696 7.5 6.727 7.5 8.998v2.24c0 1.413.67 2.735 1.76 3.562l-2.98 2.98A.75.75 0 0 1 5 17.25v-3.443c-.501-.048-1-.106-1.495-.172C2.033 13.438 1 12.162 1 10.72V5.28c0-1.441 1.033-2.717 2.505-2.914Z"/><path d="M14 6c-.762 0-1.52.02-2.271.062C10.157 6.148 9 7.472 9 8.998v2.24c0 1.519 1.141 2.841 2.705 2.97.288.024.578.05.869.08.433.044.86.098 1.29.162.745.113 1.136.95.81 1.627L13.56 18h3.19a.75.75 0 0 0 .75-.75 41.32 41.32 0 0 0 .61-5 43.141 43.141 0 0 0-4.11.256V6Z"/></svg>
                Full database SQL dump
            </span>
            <span class="bk-chip">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M3.75 3A1.75 1.75 0 0 0 2 4.75v3.26a3.235 3.235 0 0 1 1.75-.51h12.5c.644 0 1.245.188 1.75.51V6.75A1.75 1.75 0 0 0 16.25 5h-4.836a.25.25 0 0 1-.177-.073L9.823 3.513A1.75 1.75 0 0 0 8.586 3H3.75ZM3.75 9A1.75 1.75 0 0 0 2 10.75v4.5c0 .966.784 1.75 1.75 1.75h12.5A1.75 1.75 0 0 0 18 15.25v-4.5A1.75 1.75 0 0 0 16.25 9H3.75Z"/></svg>
                Code, config &amp; resources
            </span>
            <span class="bk-chip">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M1 5.25A2.25 2.25 0 0 1 3.25 3h13.5A2.25 2.25 0 0 1 19 5.25v9.5A2.25 2.25 0 0 1 16.75 17H3.25A2.25 2.25 0 0 1 1 14.75v-9.5Zm1.5 5.81v3.69c0 .414.336.75.75.75h13.5a.75.75 0 0 0 .75-.75v-2.69l-2.22-2.219a.75.75 0 0 0-1.06 0l-1.91 1.909.47.47a.75.75 0 1 1-1.06 1.06L6.53 8.091a.75.75 0 0 0-1.06 0l-2.97 2.97ZM12 7a1 1 0 1 1-2 0 1 1 0 0 1 2 0Z" clip-rule="evenodd"/></svg>
                User uploads
            </span>
        </div>
        <p class="bk-note">Excluded: <code>vendor/</code> <code>node_modules/</code> <code>storage/framework/</code> <code>storage/logs/</code></p>
    </div>
</div>

{{-- Backup list --}}
<div class="bk-card">
    <div class="bk-card-head">
        <div class="bk-card-title">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M2 3a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H2Z"/><path fill-rule="evenodd" d="M2 7.5h16l-.811 7.71a2 2 0 0 1-1.99 1.79H4.802a2 2 0 0 1-1.99-1.79L2 7.5ZM7 11a1 1 0 0 1 1-1h4a1 1 0 1 1 0 2H8a1 1 0 0 1-1-1Z" clip-rule="evenodd"/></svg>
            Backup Files
            @if($backupCount > 0)
                <span class="bk-cbadge">{{ $backupCount }}</span>
            @endif
        </div>
        <div class="bk-card-meta">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 0 0-4.5 4.5V9H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-.5V5.5A4.5 4.5 0 0 0 10 1Zm3 8V5.5a3 3 0 1 0-6 0V9h6Z" clip-rule="evenodd"/></svg>
            Private storage
        </div>
    </div>

    @if(empty($backups))
    <div class="bk-empty">
        <div class="bk-empty-ico">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M2 3a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H2Z"/><path fill-rule="evenodd" d="M2 7.5h16l-.811 7.71a2 2 0 0 1-1.99 1.79H4.802a2 2 0 0 1-1.99-1.79L2 7.5ZM7 11a1 1 0 0 1 1-1h4a1 1 0 1 1 0 2H8a1 1 0 0 1-1-1Z" clip-rule="evenodd"/></svg>
        </div>
        <div>
            <div class="bk-empty-t">No backups yet</div>
            <div class="bk-empty-s">Click <strong>Create Backup</strong> at the top right to take your first snapshot.</div>
        </div>
    </div>
    @else
    <div style="overflow-x:auto">
        <table class="bk-tbl">
            <thead>
                <tr>
                    <th>Filename</th>
                    <th class="bk-hism">Size</th>
                    <th class="bk-himd">Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($backups as $backup)
                <tr>
                    <td>
                        <div class="bk-file">
                            <div class="bk-file-ico">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 2.75a.75.75 0 0 0-1.5 0v8.614L6.295 8.235a.75.75 0 1 0-1.09 1.03l4.25 4.5a.75.75 0 0 0 1.09 0l4.25-4.5a.75.75 0 0 0-1.09-1.03l-2.955 3.129V2.75Z"/><path d="M3.5 12.75a.75.75 0 0 0-1.5 0v2.5A2.75 2.75 0 0 0 4.75 18h10.5A2.75 2.75 0 0 0 18 15.25v-2.5a.75.75 0 0 0-1.5 0v2.5c0 .69-.56 1.25-1.25 1.25H4.75c-.69 0-1.25-.56-1.25-1.25v-2.5Z"/></svg>
                            </div>
                            <span class="bk-fname">{{ $backup['filename'] }}</span>
                        </div>
                    </td>
                    <td class="bk-hism">
                        <span class="bk-szchip">{{ $backup['size'] }}</span>
                    </td>
                    <td class="bk-himd">
                        <div class="bk-drow">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.75 2a.75.75 0 0 1 .75.75V4h7V2.75a.75.75 0 0 1 1.5 0V4h.25A2.75 2.75 0 0 1 18 6.75v8.5A2.75 2.75 0 0 1 15.25 18H4.75A2.75 2.75 0 0 1 2 15.25v-8.5A2.75 2.75 0 0 1 4.75 4H5V2.75A.75.75 0 0 1 5.75 2Zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75Z" clip-rule="evenodd"/></svg>
                            {{ $backup['created_at'] }}
                        </div>
                    </td>
                    <td>
                        <div class="bk-acts">
                            <a href="{{ route('admin.backup.download', ['filename' => $backup['filename']]) }}"
                               class="bk-btn bk-dl" title="Download">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 2.75a.75.75 0 0 0-1.5 0v8.614L6.295 8.235a.75.75 0 1 0-1.09 1.03l4.25 4.5a.75.75 0 0 0 1.09 0l4.25-4.5a.75.75 0 0 0-1.09-1.03l-2.955 3.129V2.75Z"/><path d="M3.5 12.75a.75.75 0 0 0-1.5 0v2.5A2.75 2.75 0 0 0 4.75 18h10.5A2.75 2.75 0 0 0 18 15.25v-2.5a.75.75 0 0 0-1.5 0v2.5c0 .69-.56 1.25-1.25 1.25H4.75c-.69 0-1.25-.56-1.25-1.25v-2.5Z"/></svg>
                                Download
                            </a>
                            <button wire:click="deleteBackup('{{ $backup['filename'] }}')"
                                    wire:confirm="Delete {{ $backup['filename'] }}? This cannot be undone."
                                    wire:loading.attr="disabled"
                                    class="bk-btn bk-del" title="Delete">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.149-.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.807a2.75 2.75 0 0 0 2.742-2.53l.841-10.52.149.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4ZM8.58 7.72a.75.75 0 0 0-1.5.06l.3 7.5a.75.75 0 1 0 1.5-.06l-.3-7.5Zm4.34.06a.75.75 0 1 0-1.5-.06l-.3 7.5a.75.75 0 1 0 1.5.06l.3-7.5Z" clip-rule="evenodd"/></svg>
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="bk-tfoot">{{ $backupCount }} {{ $backupCount === 1 ? 'backup' : 'backups' }} &middot; {{ $totalSize }} total</div>
    @endif
</div>

{{-- Restore instructions --}}
<details class="bk-rest">
    <summary>
        <svg class="ricon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M14.5 10a4.5 4.5 0 0 0 4.284-5.882c-.105-.324-.51-.391-.752-.15L15.34 6.66a.454.454 0 0 1-.493.11 3.01 3.01 0 0 1-1.618-1.616.455.455 0 0 1 .11-.494l2.694-2.692c.24-.241.174-.647-.15-.752a4.5 4.5 0 0 0-5.873 4.575c.055.873-.128 1.808-.8 2.368l-7.23 6.024a2.724 2.724 0 1 0 3.837 3.837l6.024-7.23c.56-.672 1.495-.855 2.368-.8.096.007.193.01.291.01ZM5 16a1 1 0 1 1-2 0 1 1 0 0 1 2 0Z" clip-rule="evenodd"/></svg>
        How to restore a backup
        <svg class="chev" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/></svg>
    </summary>
    <div class="bk-rest-body">
        <div class="bk-steps">
            <div class="bk-step">
                <span class="bk-num">1</span>
                <span>Download the backup ZIP file using the <strong>Download</strong> button above.</span>
            </div>
            <div class="bk-step">
                <span class="bk-num">2</span>
                <span>Extract the ZIP and locate <code>database.sql</code>. Import it with: <code>mysql -u root -p heartsconnect &lt; database.sql</code></span>
            </div>
            <div class="bk-step">
                <span class="bk-num">3</span>
                <span>Copy <code>storage/</code> contents back to <code>storage/app/public/</code>. Run <code>php artisan storage:link</code> if needed.</span>
            </div>
            <div class="bk-step">
                <span class="bk-num">4</span>
                <span>Clear caches: <code>php artisan config:clear</code> then <code>php artisan cache:clear</code> and verify the site loads.</span>
            </div>
        </div>
    </div>
</details>

</div>
</div>

</x-filament-panels::page>