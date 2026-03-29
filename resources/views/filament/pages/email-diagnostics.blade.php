{{-- Email Diagnostics panel — rendered inside the Diagnostics tab of ManageEmailSettings --}}
@php
    use App\Models\SiteSetting;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Config;

    // ── Effective mail config (what's actually loaded at runtime) ─────────────
    $driver       = Config::get('mail.default', '?');
    $fromAddress  = Config::get('mail.from.address', '?');
    $fromName     = Config::get('mail.from.name', '?');
    $queueMode    = Config::get('queue.default', '?');

    $driverDetail = match ($driver) {
        'smtp'     => 'Host: ' . (Config::get('mail.mailers.smtp.host') ?? '?') . '  Port: ' . (Config::get('mail.mailers.smtp.port') ?? '?'),
        'sendmail' => 'Path: ' . (Config::get('mail.mailers.sendmail.path') ?? '?'),
        'mailgun'  => 'Domain: ' . (Config::get('services.mailgun.domain') ?? '?'),
        'ses'      => 'Region: ' . (Config::get('services.ses.region') ?? '?'),
        'postmark' => 'Token: ' . (Config::get('services.postmark.token') ? '***set***' : '— not set —'),
        'resend'   => 'Key: ' . (Config::get('services.resend.key') ? '***set***' : '— not set —'),
        'log'      => 'Writes to: storage/logs/laravel.log',
        default    => '—',
    };

    // ── Queue stats ───────────────────────────────────────────────────────────
    try {
        $pendingJobs = DB::table('jobs')->count();
        $failedJobs  = DB::table('failed_jobs')->latest()->limit(10)->get();
    } catch (\Throwable) {
        $pendingJobs = '(table not found — run migrations)';
        $failedJobs  = collect();
    }

    // ── notifications table (recent emails dispatched) ────────────────────────
    $hasNotifTable = false;
    try {
        DB::table('notifications')->limit(1)->get();
        $hasNotifTable = true;
        $recentNotifs  = DB::table('notifications')
            ->where('type', 'like', '%Notification')
            ->latest()
            ->limit(10)
            ->get();
    } catch (\Throwable) {
        $recentNotifs = collect();
    }

    // ── Log tail (last 40 lines related to mail/notifications) ───────────────
    $logPath  = storage_path('logs/laravel.log');
    $logLines = [];
    if (file_exists($logPath)) {
        $all      = file($logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $relevant = array_filter($all, fn($l) => stripos($l, 'mail') !== false
            || stripos($l, 'smtp') !== false
            || stripos($l, 'notification') !== false
            || stripos($l, 'queue') !== false
            || stripos($l, 'swift') !== false
            || str_contains($l, '[ERROR]')
            || str_contains($l, '[CRITICAL]'));
        $logLines = array_slice(array_values($relevant), -40);
    }
@endphp

<style>
    .email-diagnostics {
        display: grid;
        gap: 1rem;
        padding: .25rem;
    }

    .email-diag-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(15, 23, 42, .06);
    }

    .dark .email-diag-card {
        background: #111827;
        border-color: #374151;
        box-shadow: none;
    }

    .email-diag-head {
        display: flex;
        align-items: center;
        gap: .625rem;
        padding: .95rem 1rem;
        border-bottom: 1px solid #eef2f7;
        background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
        color: #0f172a;
        font-weight: 700;
        font-size: .95rem;
    }

    .dark .email-diag-head {
        background: linear-gradient(180deg, #111827 0%, #0f172a 100%);
        border-color: #374151;
        color: #f9fafb;
    }

    .email-diag-body {
        padding: 1rem;
    }

    .email-diag-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .875rem;
    }

    .email-diag-stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: .875rem;
    }

    .email-diag-item,
    .email-diag-stat,
    .email-diag-check,
    .email-diag-row {
        border: 1px solid #e5e7eb;
        border-radius: .875rem;
        background: #f8fafc;
    }

    .dark .email-diag-item,
    .dark .email-diag-stat,
    .dark .email-diag-check,
    .dark .email-diag-row {
        background: #0f172a;
        border-color: #374151;
    }

    .email-diag-item {
        padding: .9rem 1rem;
    }

    .email-diag-label {
        display: block;
        margin-bottom: .35rem;
        color: #64748b;
        font-size: .78rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .03em;
    }

    .dark .email-diag-label {
        color: #94a3b8;
    }

    .email-diag-value {
        color: #0f172a;
        font-size: .94rem;
        font-weight: 700;
        line-height: 1.45;
        word-break: break-word;
    }

    .dark .email-diag-value {
        color: #f8fafc;
    }

    .email-diag-mono {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, Liberation Mono, Courier New, monospace;
    }

    .email-diag-subtle {
        font-size: .78rem;
        color: #64748b;
        margin-top: .25rem;
    }

    .dark .email-diag-subtle {
        color: #94a3b8;
    }

    .email-diag-badge {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .3rem .62rem;
        border-radius: 999px;
        font-size: .78rem;
        font-weight: 700;
    }

    .email-diag-badge--green { background: #dcfce7; color: #166534; }
    .email-diag-badge--yellow { background: #fef3c7; color: #92400e; }
    .email-diag-badge--red { background: #fee2e2; color: #991b1b; }
    .email-diag-badge--blue { background: #dbeafe; color: #1d4ed8; }
    .email-diag-badge--gray { background: #e5e7eb; color: #374151; }

    .dark .email-diag-badge--green { background: rgba(34, 197, 94, .16); color: #86efac; }
    .dark .email-diag-badge--yellow { background: rgba(245, 158, 11, .16); color: #fcd34d; }
    .dark .email-diag-badge--red { background: rgba(239, 68, 68, .16); color: #fca5a5; }
    .dark .email-diag-badge--blue { background: rgba(59, 130, 246, .16); color: #93c5fd; }
    .dark .email-diag-badge--gray { background: rgba(148, 163, 184, .16); color: #cbd5e1; }

    .email-diag-note {
        margin-top: .9rem;
        padding: .85rem 1rem;
        border-radius: .875rem;
        border: 1px solid #fde68a;
        background: #fffbeb;
        color: #92400e;
        font-size: .88rem;
        line-height: 1.5;
    }

    .dark .email-diag-note {
        background: rgba(245, 158, 11, .08);
        border-color: rgba(245, 158, 11, .3);
        color: #fcd34d;
    }

    .email-diag-list {
        display: grid;
        gap: .75rem;
    }

    .email-diag-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        padding: .85rem 1rem;
    }

    .email-diag-row-main {
        min-width: 0;
    }

    .email-diag-log {
        margin: 0;
        padding: 1rem;
        border-radius: .875rem;
        background: #0f172a;
        color: #bbf7d0;
        overflow: auto;
        max-height: 22rem;
        font-size: .78rem;
        line-height: 1.55;
        white-space: pre-wrap;
        word-break: break-word;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, Liberation Mono, Courier New, monospace;
    }

    .email-diag-check-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: .75rem;
    }

    .email-diag-check {
        display: flex;
        gap: .75rem;
        padding: .9rem 1rem;
    }

    .email-diag-check-icon {
        font-weight: 800;
        font-size: 1rem;
        line-height: 1;
        margin-top: .1rem;
    }

    .email-diag-check-icon--ok { color: #16a34a; }
    .email-diag-check-icon--bad { color: #dc2626; }

    .email-diag-empty {
        color: #64748b;
        font-size: .9rem;
        font-style: italic;
    }

    .dark .email-diag-empty {
        color: #94a3b8;
    }

    @media (max-width: 768px) {
        .email-diag-grid,
        .email-diag-row,
        .email-diag-check-grid {
            grid-template-columns: 1fr;
        }

        .email-diag-row {
            flex-direction: column;
        }
    }
</style>

<div class="email-diagnostics">

    {{-- ── Runtime Mail Config ───────────────────────────────────── --}}
    <div class="email-diag-card">
        <div class="email-diag-head">
            <x-heroicon-o-envelope class="w-5 h-5 text-blue-500"/>
            <span>Effective Mail Configuration (Runtime)</span>
        </div>
        <div class="email-diag-body">
            <div class="email-diag-grid">
                <div class="email-diag-item">
                    <span class="email-diag-label">Driver</span>
                    <div class="email-diag-value email-diag-mono">
                        <span class="email-diag-badge {{ $driver === 'log' ? 'email-diag-badge--yellow' : ($driver === 'cpanel' || $driver === 'sendmail' || $driver === 'smtp' ? 'email-diag-badge--green' : 'email-diag-badge--blue') }}">{{ $driver }}</span>
                    </div>
                </div>
                <div class="email-diag-item">
                    <span class="email-diag-label">Details</span>
                    <div class="email-diag-value email-diag-mono">{{ $driverDetail }}</div>
                </div>
                <div class="email-diag-item">
                    <span class="email-diag-label">From Address</span>
                    <div class="email-diag-value email-diag-mono">{{ $fromAddress }}</div>
                </div>
                <div class="email-diag-item">
                    <span class="email-diag-label">From Name</span>
                    <div class="email-diag-value">{{ $fromName }}</div>
                </div>
                <div class="email-diag-item">
                    <span class="email-diag-label">Queue Connection</span>
                    <div class="email-diag-value email-diag-mono">
                        <span class="email-diag-badge {{ $queueMode === 'sync' ? 'email-diag-badge--green' : 'email-diag-badge--yellow' }}">{{ $queueMode }}</span>
                    </div>
                    <div class="email-diag-subtle">
                        @if ($queueMode !== 'sync')
                            Requires <span class="email-diag-mono">queue:work</span> to be running.
                        @else
                            Emails are sent immediately.
                        @endif
                    </div>
                </div>
                <div class="email-diag-item">
                    <span class="email-diag-label">PHP Mail Function</span>
                    <div class="email-diag-value">
                        <span class="email-diag-badge {{ function_exists('mail') ? 'email-diag-badge--green' : 'email-diag-badge--red' }}">{{ function_exists('mail') ? 'Available' : 'Disabled' }}</span>
                    </div>
                </div>
            </div>
            @if ($driver === 'log')
            <div class="email-diag-note">
                ⚠️ Driver is set to <strong>log</strong> — emails are written to <code>storage/logs/laravel.log</code> and NOT sent to users. Change to SMTP or Sendmail in the <strong>Mail Transport</strong> tab.
            </div>
            @endif
        </div>
    </div>

    {{-- ── Queue Stats ─────────────────────────────────────────────── --}}
    <div class="email-diag-card">
        <div class="email-diag-head">
            <x-heroicon-o-queue-list class="w-5 h-5 text-purple-500"/>
            <span>Queue Status</span>
        </div>
        <div class="email-diag-body">
            <div class="email-diag-stat-grid">
                <div class="email-diag-stat" style="padding: 1rem; text-align: center;">
                    <div class="email-diag-value" style="font-size: 1.8rem; color: {{ is_int($pendingJobs) && $pendingJobs > 0 ? '#f97316' : '#16a34a' }};">
                    {{ $pendingJobs }}
                    </div>
                    <div class="email-diag-subtle">Pending Jobs</div>
                @if (is_int($pendingJobs) && $pendingJobs > 0 && $queueMode !== 'sync')
                    <div class="email-diag-subtle" style="color:#f97316;">Click "Process Queued Emails Now" to send them</div>
                @endif
                </div>
                <div class="email-diag-stat" style="padding: 1rem; text-align: center;">
                    <div class="email-diag-value" style="font-size: 1.8rem; color: {{ $failedJobs->count() > 0 ? '#ef4444' : '#16a34a' }};">
                    {{ $failedJobs->count() }}
                    </div>
                    <div class="email-diag-subtle">Failed Jobs (last 10)</div>
                </div>
            </div>

        @if ($failedJobs->count() > 0)
            <div style="margin-top: 1rem;">
            <div class="email-diag-label" style="margin-bottom:.55rem;">Recent Failed Jobs</div>
            <div class="email-diag-list">
                @foreach ($failedJobs as $job)
                <div class="email-diag-row" style="background: rgba(239, 68, 68, .06); border-color: rgba(239, 68, 68, .18);">
                    <div class="email-diag-row-main">
                    <div class="email-diag-value email-diag-mono" style="font-size:.82rem; color:#dc2626;">{{ Str::limit($job->payload, 120) }}</div>
                    <div class="email-diag-subtle">
                        {{ $job->queue }} • Failed at: {{ $job->failed_at }}
                    </div>
                    @if (property_exists($job, 'exception') && $job->exception)
                    <details class="mt-1">
                        <summary style="cursor:pointer; color:#dc2626; font-size:.8rem; font-weight:700;">Exception</summary>
                        <pre class="email-diag-log" style="margin-top:.6rem; max-height:12rem; color:#fca5a5;">{{ Str::limit($job->exception, 500) }}</pre>
                    </details>
                    @endif
                    </div>
                </div>
                @endforeach
            </div>
            </div>
        @endif
        </div>
    </div>

    {{-- ── Recent Notifications ──────────────────────────────────── --}}
    @if ($hasNotifTable && $recentNotifs->count() > 0)
    <div class="email-diag-card">
        <div class="email-diag-head">
            <x-heroicon-o-bell class="w-5 h-5 text-indigo-500"/>
            <span>Recent Notifications (last 10)</span>
        </div>
        <div class="email-diag-body email-diag-list">
            @foreach ($recentNotifs as $notif)
            <div class="email-diag-row">
                <div class="email-diag-row-main">
                    <span class="email-diag-badge email-diag-badge--blue email-diag-mono">{{ class_basename($notif->type) }}</span>
                    <span class="email-diag-subtle" style="display:block; margin-top:.45rem;">Target user #{{ $notif->notifiable_id }}</span>
                    @if ($notif->read_at)
                        <span class="email-diag-subtle" style="color:#16a34a; display:block;">✓ Read</span>
                    @else
                        <span class="email-diag-subtle" style="display:block;">Unread</span>
                    @endif
                </div>
                <span class="email-diag-subtle" style="white-space:nowrap; margin-top:0;">{{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Log Tail ──────────────────────────────────────────────── --}}
    <div class="email-diag-card">
        <div class="email-diag-head">
            <x-heroicon-o-document-text class="w-5 h-5 text-gray-500"/>
            <span>Laravel Log — Mail/Queue/Error Lines (last 40)</span>
        </div>
        <div class="email-diag-body">
            @if (empty($logLines))
                <div class="email-diag-empty">
                    @if (!file_exists($logPath))
                        Log file not found at <code>{{ $logPath }}</code>
                    @else
                        No mail/queue-related log entries found. Emails may be sending without errors, or no emails have been triggered yet.
                    @endif
                </div>
            @else
            <pre class="email-diag-log">@foreach($logLines as $line){{ $line }}
@endforeach</pre>
            @endif
        </div>
    </div>

    {{-- ── Quick Checks ──────────────────────────────────────────── --}}
    <div class="email-diag-card">
        <div class="email-diag-head">
            <x-heroicon-o-check-circle class="w-5 h-5 text-green-500"/>
            <span>Environment Checks</span>
        </div>
        <div class="email-diag-body">
            @php
                $checks = [
                    ['PHP mail()', function_exists('mail'), 'Required for sendmail/PHP mail driver'],
                    ['OpenSSL', extension_loaded('openssl'), 'Required for TLS/SSL SMTP connections'],
                    ['cURL', extension_loaded('curl'), 'Required for Mailgun, SES, Postmark, Resend APIs'],
                    ['IMAP', extension_loaded('imap'), 'Optional — for email reading features'],
                    ['sockets', extension_loaded('sockets'), 'Optional — improves SMTP reliability'],
                    ['Storage writable', is_writable(storage_path('logs')), 'Required to write laravel.log'],
                ];
            @endphp
            <div class="email-diag-check-grid">
            @foreach ($checks as [$label, $ok, $note])
            <div class="email-diag-check">
                <span class="email-diag-check-icon {{ $ok ? 'email-diag-check-icon--ok' : 'email-diag-check-icon--bad' }}">{{ $ok ? '✓' : '✗' }}</span>
                <div>
                    <div class="email-diag-value" style="font-size:.92rem; color: {{ $ok ? 'inherit' : '#dc2626' }};">{{ $label }}</div>
                    <div class="email-diag-subtle">{{ $note }}</div>
                </div>
            </div>
            @endforeach
            </div>
        </div>
    </div>

</div>
