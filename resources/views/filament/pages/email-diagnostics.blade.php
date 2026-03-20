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

<div class="space-y-6 p-2">

    {{-- ── Runtime Mail Config ───────────────────────────────────── --}}
    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex items-center gap-2">
            <x-heroicon-o-envelope class="w-5 h-5 text-blue-500"/>
            <span class="font-semibold text-sm text-gray-700 dark:text-gray-200">Effective Mail Configuration (Runtime)</span>
        </div>
        <div class="p-4 grid grid-cols-2 gap-3 text-sm">
            <div>
                <span class="text-gray-500 dark:text-gray-400">Driver</span>
                <div class="font-mono font-semibold {{ $driver === 'log' ? 'text-yellow-600' : 'text-green-600' }}">{{ $driver }}</div>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Details</span>
                <div class="font-mono text-gray-800 dark:text-gray-200 break-all">{{ $driverDetail }}</div>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">From Address</span>
                <div class="font-mono text-gray-800 dark:text-gray-200">{{ $fromAddress }}</div>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">From Name</span>
                <div class="font-mono text-gray-800 dark:text-gray-200">{{ $fromName }}</div>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Queue Connection</span>
                <div class="font-mono font-semibold {{ $queueMode === 'sync' ? 'text-green-600' : 'text-orange-500' }}">
                    {{ $queueMode }}
                    @if ($queueMode !== 'sync')
                        <span class="text-xs text-orange-500 font-normal ml-1">(requires queue:work to be running)</span>
                    @else
                        <span class="text-xs text-green-600 font-normal ml-1">(emails sent immediately)</span>
                    @endif
                </div>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">PHP Mail Function</span>
                <div class="font-mono {{ function_exists('mail') ? 'text-green-600' : 'text-red-600' }}">
                    {{ function_exists('mail') ? 'Available' : 'Disabled' }}
                </div>
            </div>
        </div>
        @if ($driver === 'log')
        <div class="px-4 pb-3">
            <div class="rounded-lg bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 px-3 py-2 text-sm text-yellow-700 dark:text-yellow-400">
                ⚠️ Driver is set to <strong>log</strong> — emails are written to <code>storage/logs/laravel.log</code> and NOT sent to users. Change to SMTP or Sendmail in the <strong>Mail Transport</strong> tab.
            </div>
        </div>
        @endif
    </div>

    {{-- ── Queue Stats ─────────────────────────────────────────────── --}}
    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex items-center gap-2">
            <x-heroicon-o-queue-list class="w-5 h-5 text-purple-500"/>
            <span class="font-semibold text-sm text-gray-700 dark:text-gray-200">Queue Status</span>
        </div>
        <div class="p-4 grid grid-cols-2 gap-4 text-sm">
            <div class="rounded-lg bg-gray-50 dark:bg-gray-900 p-3 text-center">
                <div class="text-2xl font-bold {{ is_int($pendingJobs) && $pendingJobs > 0 ? 'text-orange-500' : 'text-green-600' }}">
                    {{ $pendingJobs }}
                </div>
                <div class="text-gray-500 dark:text-gray-400 text-xs mt-1">Pending Jobs</div>
                @if (is_int($pendingJobs) && $pendingJobs > 0 && $queueMode !== 'sync')
                    <div class="text-xs text-orange-500 mt-1">Click "Process Queued Emails Now" to send them</div>
                @endif
            </div>
            <div class="rounded-lg bg-gray-50 dark:bg-gray-900 p-3 text-center">
                <div class="text-2xl font-bold {{ $failedJobs->count() > 0 ? 'text-red-500' : 'text-green-600' }}">
                    {{ $failedJobs->count() }}
                </div>
                <div class="text-gray-500 dark:text-gray-400 text-xs mt-1">Failed Jobs (last 10)</div>
            </div>
        </div>

        @if ($failedJobs->count() > 0)
        <div class="px-4 pb-4">
            <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">Recent Failed Jobs</div>
            <div class="space-y-2">
                @foreach ($failedJobs as $job)
                <div class="rounded-lg border border-red-100 dark:border-red-900 bg-red-50 dark:bg-red-900/20 p-2 text-xs">
                    <div class="font-mono text-red-700 dark:text-red-400 break-all">{{ Str::limit($job->payload, 120) }}</div>
                    <div class="text-gray-400 mt-1">
                        {{ $job->queue }} • Failed at: {{ $job->failed_at }}
                    </div>
                    @if (property_exists($job, 'exception') && $job->exception)
                    <details class="mt-1">
                        <summary class="cursor-pointer text-red-600">Exception ▶</summary>
                        <pre class="mt-1 text-xs text-red-700 dark:text-red-400 overflow-x-auto">{{ Str::limit($job->exception, 500) }}</pre>
                    </details>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- ── Recent Notifications ──────────────────────────────────── --}}
    @if ($hasNotifTable && $recentNotifs->count() > 0)
    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex items-center gap-2">
            <x-heroicon-o-bell class="w-5 h-5 text-indigo-500"/>
            <span class="font-semibold text-sm text-gray-700 dark:text-gray-200">Recent Notifications (last 10)</span>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @foreach ($recentNotifs as $notif)
            <div class="px-4 py-2 text-xs flex items-start justify-between gap-4">
                <div>
                    <span class="font-mono text-indigo-600 dark:text-indigo-400">{{ class_basename($notif->type) }}</span>
                    <span class="text-gray-400 ml-2">→ user #{{ $notif->notifiable_id }}</span>
                    @if ($notif->read_at)
                        <span class="ml-2 text-green-500">✓ read</span>
                    @else
                        <span class="ml-2 text-gray-400">unread</span>
                    @endif
                </div>
                <span class="text-gray-400 whitespace-nowrap">{{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Log Tail ──────────────────────────────────────────────── --}}
    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex items-center gap-2">
            <x-heroicon-o-document-text class="w-5 h-5 text-gray-500"/>
            <span class="font-semibold text-sm text-gray-700 dark:text-gray-200">Laravel Log — Mail/Queue/Error Lines (last 40)</span>
        </div>
        <div class="p-4">
            @if (empty($logLines))
                <div class="text-sm text-gray-400 italic">
                    @if (!file_exists($logPath))
                        Log file not found at <code>{{ $logPath }}</code>
                    @else
                        No mail/queue-related log entries found. Emails may be sending without errors, or no emails have been triggered yet.
                    @endif
                </div>
            @else
            <pre class="text-xs font-mono bg-gray-900 text-green-300 rounded-lg p-3 overflow-x-auto max-h-80 overflow-y-auto leading-5 whitespace-pre-wrap break-all">@foreach($logLines as $line){{ $line }}
@endforeach</pre>
            @endif
        </div>
    </div>

    {{-- ── Quick Checks ──────────────────────────────────────────── --}}
    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex items-center gap-2">
            <x-heroicon-o-check-circle class="w-5 h-5 text-green-500"/>
            <span class="font-semibold text-sm text-gray-700 dark:text-gray-200">Environment Checks</span>
        </div>
        <div class="p-4 grid grid-cols-2 gap-2 text-sm">
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
            @foreach ($checks as [$label, $ok, $note])
            <div class="flex items-start gap-2">
                <span class="{{ $ok ? 'text-green-500' : 'text-red-500' }} mt-0.5">{{ $ok ? '✓' : '✗' }}</span>
                <div>
                    <div class="font-medium {{ $ok ? 'text-gray-700 dark:text-gray-200' : 'text-red-600' }}">{{ $label }}</div>
                    <div class="text-xs text-gray-400">{{ $note }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

</div>
