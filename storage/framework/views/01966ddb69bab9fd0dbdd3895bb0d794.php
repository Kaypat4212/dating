
<?php
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
?>

<div class="space-y-6 p-2">

    
    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex items-center gap-2">
            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-envelope'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5 text-blue-500']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
            <span class="font-semibold text-sm text-gray-700 dark:text-gray-200">Effective Mail Configuration (Runtime)</span>
        </div>
        <div class="p-4 grid grid-cols-2 gap-3 text-sm">
            <div>
                <span class="text-gray-500 dark:text-gray-400">Driver</span>
                <div class="font-mono font-semibold <?php echo e($driver === 'log' ? 'text-yellow-600' : 'text-green-600'); ?>"><?php echo e($driver); ?></div>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Details</span>
                <div class="font-mono text-gray-800 dark:text-gray-200 break-all"><?php echo e($driverDetail); ?></div>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">From Address</span>
                <div class="font-mono text-gray-800 dark:text-gray-200"><?php echo e($fromAddress); ?></div>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">From Name</span>
                <div class="font-mono text-gray-800 dark:text-gray-200"><?php echo e($fromName); ?></div>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Queue Connection</span>
                <div class="font-mono font-semibold <?php echo e($queueMode === 'sync' ? 'text-green-600' : 'text-orange-500'); ?>">
                    <?php echo e($queueMode); ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($queueMode !== 'sync'): ?>
                        <span class="text-xs text-orange-500 font-normal ml-1">(requires queue:work to be running)</span>
                    <?php else: ?>
                        <span class="text-xs text-green-600 font-normal ml-1">(emails sent immediately)</span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">PHP Mail Function</span>
                <div class="font-mono <?php echo e(function_exists('mail') ? 'text-green-600' : 'text-red-600'); ?>">
                    <?php echo e(function_exists('mail') ? 'Available' : 'Disabled'); ?>

                </div>
            </div>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($driver === 'log'): ?>
        <div class="px-4 pb-3">
            <div class="rounded-lg bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 px-3 py-2 text-sm text-yellow-700 dark:text-yellow-400">
                ⚠️ Driver is set to <strong>log</strong> — emails are written to <code>storage/logs/laravel.log</code> and NOT sent to users. Change to SMTP or Sendmail in the <strong>Mail Transport</strong> tab.
            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex items-center gap-2">
            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-queue-list'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5 text-purple-500']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
            <span class="font-semibold text-sm text-gray-700 dark:text-gray-200">Queue Status</span>
        </div>
        <div class="p-4 grid grid-cols-2 gap-4 text-sm">
            <div class="rounded-lg bg-gray-50 dark:bg-gray-900 p-3 text-center">
                <div class="text-2xl font-bold <?php echo e(is_int($pendingJobs) && $pendingJobs > 0 ? 'text-orange-500' : 'text-green-600'); ?>">
                    <?php echo e($pendingJobs); ?>

                </div>
                <div class="text-gray-500 dark:text-gray-400 text-xs mt-1">Pending Jobs</div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(is_int($pendingJobs) && $pendingJobs > 0 && $queueMode !== 'sync'): ?>
                    <div class="text-xs text-orange-500 mt-1">Click "Process Queued Emails Now" to send them</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div class="rounded-lg bg-gray-50 dark:bg-gray-900 p-3 text-center">
                <div class="text-2xl font-bold <?php echo e($failedJobs->count() > 0 ? 'text-red-500' : 'text-green-600'); ?>">
                    <?php echo e($failedJobs->count()); ?>

                </div>
                <div class="text-gray-500 dark:text-gray-400 text-xs mt-1">Failed Jobs (last 10)</div>
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($failedJobs->count() > 0): ?>
        <div class="px-4 pb-4">
            <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">Recent Failed Jobs</div>
            <div class="space-y-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $failedJobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $job): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="rounded-lg border border-red-100 dark:border-red-900 bg-red-50 dark:bg-red-900/20 p-2 text-xs">
                    <div class="font-mono text-red-700 dark:text-red-400 break-all"><?php echo e(Str::limit($job->payload, 120)); ?></div>
                    <div class="text-gray-400 mt-1">
                        <?php echo e($job->queue); ?> • Failed at: <?php echo e($job->failed_at); ?>

                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(property_exists($job, 'exception') && $job->exception): ?>
                    <details class="mt-1">
                        <summary class="cursor-pointer text-red-600">Exception ▶</summary>
                        <pre class="mt-1 text-xs text-red-700 dark:text-red-400 overflow-x-auto"><?php echo e(Str::limit($job->exception, 500)); ?></pre>
                    </details>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasNotifTable && $recentNotifs->count() > 0): ?>
    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex items-center gap-2">
            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-bell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5 text-indigo-500']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
            <span class="font-semibold text-sm text-gray-700 dark:text-gray-200">Recent Notifications (last 10)</span>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $recentNotifs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notif): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="px-4 py-2 text-xs flex items-start justify-between gap-4">
                <div>
                    <span class="font-mono text-indigo-600 dark:text-indigo-400"><?php echo e(class_basename($notif->type)); ?></span>
                    <span class="text-gray-400 ml-2">→ user #<?php echo e($notif->notifiable_id); ?></span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($notif->read_at): ?>
                        <span class="ml-2 text-green-500">✓ read</span>
                    <?php else: ?>
                        <span class="ml-2 text-gray-400">unread</span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <span class="text-gray-400 whitespace-nowrap"><?php echo e(\Carbon\Carbon::parse($notif->created_at)->diffForHumans()); ?></span>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex items-center gap-2">
            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-document-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5 text-gray-500']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
            <span class="font-semibold text-sm text-gray-700 dark:text-gray-200">Laravel Log — Mail/Queue/Error Lines (last 40)</span>
        </div>
        <div class="p-4">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($logLines)): ?>
                <div class="text-sm text-gray-400 italic">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!file_exists($logPath)): ?>
                        Log file not found at <code><?php echo e($logPath); ?></code>
                    <?php else: ?>
                        No mail/queue-related log entries found. Emails may be sending without errors, or no emails have been triggered yet.
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php else: ?>
            <pre class="text-xs font-mono bg-gray-900 text-green-300 rounded-lg p-3 overflow-x-auto max-h-80 overflow-y-auto leading-5 whitespace-pre-wrap break-all"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $logLines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php echo e($line); ?>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></pre>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex items-center gap-2">
            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-check-circle'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5 text-green-500']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
            <span class="font-semibold text-sm text-gray-700 dark:text-gray-200">Environment Checks</span>
        </div>
        <div class="p-4 grid grid-cols-2 gap-2 text-sm">
            <?php
                $checks = [
                    ['PHP mail()', function_exists('mail'), 'Required for sendmail/PHP mail driver'],
                    ['OpenSSL', extension_loaded('openssl'), 'Required for TLS/SSL SMTP connections'],
                    ['cURL', extension_loaded('curl'), 'Required for Mailgun, SES, Postmark, Resend APIs'],
                    ['IMAP', extension_loaded('imap'), 'Optional — for email reading features'],
                    ['sockets', extension_loaded('sockets'), 'Optional — improves SMTP reliability'],
                    ['Storage writable', is_writable(storage_path('logs')), 'Required to write laravel.log'],
                ];
            ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $checks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label, $ok, $note]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="flex items-start gap-2">
                <span class="<?php echo e($ok ? 'text-green-500' : 'text-red-500'); ?> mt-0.5"><?php echo e($ok ? '✓' : '✗'); ?></span>
                <div>
                    <div class="font-medium <?php echo e($ok ? 'text-gray-700 dark:text-gray-200' : 'text-red-600'); ?>"><?php echo e($label); ?></div>
                    <div class="text-xs text-gray-400"><?php echo e($note); ?></div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

</div>
<?php /**PATH C:\xampp\htdocs\dating\resources\views\filament\pages\email-diagnostics.blade.php ENDPATH**/ ?>