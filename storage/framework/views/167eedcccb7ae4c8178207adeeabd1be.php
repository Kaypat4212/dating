<?php if (isset($component)) { $__componentOriginal166a02a7c5ef5a9331faf66fa665c256 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-panels::components.page.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-panels::page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<div>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
:root {
    --primary-gradient: linear-gradient(135deg, #f43f5e 0%, #a855f7 100%);
    --card-bg: #1a1625;
    --success-color: #10b981;
    --warning-color: #f59e0b;
    --danger-color: #ef4444;
}

body {
    background: #0f0a1a;
}

.security-card {
    background: linear-gradient(135deg, rgba(30, 10, 46, 0.9), rgba(45, 16, 80, 0.7));
    border: 1px solid rgba(244, 63, 94, 0.15);
    border-radius: 20px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
}

.security-card:hover {
    border-color: rgba(244, 63, 94, 0.35);
    transform: translateY(-2px);
}

.stat-card {
    background: linear-gradient(135deg, rgba(30, 10, 46, 0.9), rgba(45, 16, 80, 0.7));
    border: 1px solid rgba(244, 63, 94, 0.15);
    border-radius: 16px;
    padding: 1.5rem;
    transition: all 0.3s ease;
    height: 100%;
}

.stat-card:hover {
    border-color: rgba(244, 63, 94, 0.35);
    transform: translateY(-2px);
}

.alert-card {
    background: linear-gradient(135deg, rgba(30, 10, 46, 0.9), rgba(45, 16, 80, 0.7));
    border: 1px solid;
    border-radius: 12px;
    padding: 1rem 1.25rem;
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.alert-danger {
    border-color: rgba(239, 68, 68, 0.4);
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(185, 28, 28, 0.05));
}

.alert-warning {
    border-color: rgba(245, 158, 11, 0.4);
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(217, 119, 6, 0.05));
}

.alert-info {
    border-color: rgba(59, 130, 246, 0.4);
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(37, 99, 235, 0.05));
}

.status-badge {
    padding: 0.4rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    display: inline-block;
}

.status-active {
    background: rgba(16, 185, 129, 0.15);
    color: #10b981;
    border: 1px solid rgba(16, 185, 129, 0.3);
}

.status-inactive {
    background: rgba(239, 68, 68, 0.15);
    color: #ef4444;
    border: 1px solid rgba(239, 68, 68, 0.3);
}

.table-scroll {
    max-height: 400px;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: rgba(244, 63, 94, 0.3) transparent;
}

.table-scroll::-webkit-scrollbar {
    width: 6px;
}

.table-scroll::-webkit-scrollbar-thumb {
    background: rgba(244, 63, 94, 0.3);
    border-radius: 10px;
}
</style>

<?php
    $stats = $this->getStats();
    $alerts = $this->getSecurityAlerts();
    $recentVpn = $this->getRecentVpnDetections();
    $recentSuspicious = $this->getRecentSuspiciousActivity();
    $topProviders = $this->getTopVpnProviders();
    $topIps = $this->getTopSuspiciousIps();
?>

<div class="container-fluid px-4 py-4">
    
    
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-6 fw-bold text-white mb-2">
                        <i class="bi bi-shield-check me-2" style="color: #f43f5e;"></i>
                        Security Dashboard
                    </h1>
                    <p class="text-white-50 mb-0">Real-time security monitoring and threat detection</p>
                </div>
                <div class="d-flex gap-2">
                    <span class="status-badge <?php echo e($stats['vpn_detection_enabled'] ? 'status-active' : 'status-inactive'); ?>">
                        <i class="bi bi-shield-<?php echo e($stats['vpn_detection_enabled'] ? 'check' : 'x'); ?>"></i>
                        VPN Detection <?php echo e($stats['vpn_detection_enabled'] ? 'Active' : 'Inactive'); ?>

                    </span>
                    <span class="status-badge <?php echo e($stats['telegram_enabled'] ? 'status-active' : 'status-inactive'); ?>">
                        <i class="bi bi-send"></i>
                        Telegram <?php echo e($stats['telegram_enabled'] ? 'Active' : 'Inactive'); ?>

                    </span>
                </div>
            </div>
        </div>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($alerts)): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="security-card">
                <h5 class="text-white fw-bold mb-3">
                    <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
                    Security Alerts
                </h5>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $alerts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="alert-card alert-<?php echo e($alert['type']); ?>">
                        <i class="bi bi-<?php echo e($alert['icon']); ?> fs-4 text-<?php echo e($alert['type'] == 'danger' ? 'danger' : ($alert['type'] == 'warning' ? 'warning' : 'info')); ?>"></i>
                        <div class="flex-grow-1">
                            <h6 class="text-white mb-1 fw-semibold"><?php echo e($alert['title']); ?></h6>
                            <p class="text-white-50 small mb-1"><?php echo e($alert['message']); ?></p>
                            <small class="text-<?php echo e($alert['type'] == 'danger' ? 'danger' : ($alert['type'] == 'warning' ? 'warning' : 'info')); ?>">
                                🔧 <?php echo e($alert['action']); ?>

                            </small>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="text-center">
                    <div class="mb-2">
                        <i class="bi bi-shield-exclamation fs-1" style="color: #f43f5e;"></i>
                    </div>
                    <p class="text-white-50 text-uppercase small mb-1 fw-semibold">VPN Detected Today</p>
                    <h3 class="text-white fw-bold mb-0"><?php echo e(number_format($stats['vpn_today'])); ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card">
                <div class="text-center">
                    <div class="mb-2">
                        <i class="bi bi-shield-slash fs-1" style="color: #ef4444;"></i>
                    </div>
                    <p class="text-white-50 text-uppercase small mb-1 fw-semibold">Blocked Today</p>
                    <h3 class="text-white fw-bold mb-0"><?php echo e(number_format($stats['vpn_blocked_today'])); ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card">
                <div class="text-center">
                    <div class="mb-2">
                        <i class="bi bi-flag-fill fs-1" style="color: #f59e0b;"></i>
                    </div>
                    <p class="text-white-50 text-uppercase small mb-1 fw-semibold">Suspicious Today</p>
                    <h3 class="text-white fw-bold mb-0"><?php echo e(number_format($stats['suspicious_today'])); ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card">
                <div class="text-center">
                    <div class="mb-2">
                        <i class="bi bi-key fs-1" style="color: #3b82f6;"></i>
                    </div>
                    <p class="text-white-50 text-uppercase small mb-1 fw-semibold">Failed Logins</p>
                    <h3 class="text-white fw-bold mb-0"><?php echo e(number_format($stats['failed_logins_today'])); ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card">
                <div class="text-center">
                    <div class="mb-2">
                        <i class="bi bi-activity fs-1" style="color: #a855f7;"></i>
                    </div>
                    <p class="text-white-50 text-uppercase small mb-1 fw-semibold">VPN This Week</p>
                    <h3 class="text-white fw-bold mb-0"><?php echo e(number_format($stats['vpn_week'])); ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card">
                <div class="text-center">
                    <div class="mb-2">
                        <i class="bi bi-exclamation-circle fs-1" style="color: #ec4899;"></i>
                    </div>
                    <p class="text-white-50 text-uppercase small mb-1 fw-semibold">High Confidence</p>
                    <h3 class="text-white fw-bold mb-0"><?php echo e(number_format($stats['vpn_high_confidence'])); ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card">
                <div class="text-center">
                    <div class="mb-2">
                        <i class="bi bi-person-x fs-1" style="color: #dc2626;"></i>
                    </div>
                    <p class="text-white-50 text-uppercase small mb-1 fw-semibold">Banned Users</p>
                    <h3 class="text-white fw-bold mb-0"><?php echo e(number_format($stats['banned_users'])); ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card">
                <div class="text-center">
                    <div class="mb-2">
                        <i class="bi bi-person-exclamation fs-1" style="color: #f59e0b;"></i>
                    </div>
                    <p class="text-white-50 text-uppercase small mb-1 fw-semibold">Flagged Users</p>
                    <h3 class="text-white fw-bold mb-0"><?php echo e(number_format($stats['flagged_users'])); ?></h3>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row g-4">
        
        <div class="col-md-6">
            <div class="security-card">
                <h5 class="text-white fw-bold mb-3">
                    <i class="bi bi-shield-exclamation text-danger me-2"></i>
                    Recent VPN Detections
                </h5>
                <div class="table-scroll">
                    <table class="table table-dark table-hover">
                        <thead>
                            <tr>
                                <th class="text-white-50 small">User</th>
                                <th class="text-white-50 small">IP</th>
                                <th class="text-white-50 small">Confidence</th>
                                <th class="text-white-50 small">Provider</th>
                                <th class="text-white-50 small">Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $recentVpn; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vpn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><small class="text-white"><?php echo e($vpn['user']); ?></small></td>
                                    <td><code class="small text-info"><?php echo e($vpn['ip']); ?></code></td>
                                    <td>
                                        <span class="badge <?php echo e($vpn['confidence'] >= 80 ? 'bg-danger' : 'bg-warning'); ?>">
                                            <?php echo e($vpn['confidence']); ?>%
                                        </span>
                                    </td>
                                    <td><small class="text-white-50"><?php echo e($vpn['provider']); ?></small></td>
                                    <td><small class="text-white-50" title="<?php echo e($vpn['timestamp']); ?>"><?php echo e($vpn['time']); ?></small></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-white-50 py-4">
                                        <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                                        No VPN detections yet
                                    </td>
                                </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        
        <div class="col-md-6">
            <div class="security-card">
                <h5 class="text-white fw-bold mb-3">
                    <i class="bi bi-flag-fill text-warning me-2"></i>
                    Recent Suspicious Activity
                </h5>
                <div class="table-scroll">
                    <table class="table table-dark table-hover">
                        <thead>
                            <tr>
                                <th class="text-white-50 small">User</th>
                                <th class="text-white-50 small">Action</th>
                                <th class="text-white-50 small">IP</th>
                                <th class="text-white-50 small">Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $recentSuspicious; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><small class="text-white"><?php echo e($activity['user']); ?></small></td>
                                    <td><small class="text-danger"><?php echo e(ucwords(str_replace('_', ' ', $activity['action']))); ?></small></td>
                                    <td><code class="small text-info"><?php echo e($activity['ip']); ?></code></td>
                                    <td><small class="text-white-50" title="<?php echo e($activity['timestamp']); ?>"><?php echo e($activity['time']); ?></small></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-white-50 py-4">
                                        <i class="bi bi-shield-check fs-4 d-block mb-2"></i>
                                        No suspicious activity detected
                                    </td>
                                </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        
        <div class="col-md-6">
            <div class="security-card">
                <h5 class="text-white fw-bold mb-3">
                    <i class="bi bi-bar-chart-fill text-primary me-2"></i>
                    Top VPN Providers (30 Days)
                </h5>
                <div class="table-scroll">
                    <table class="table table-dark table-hover">
                        <thead>
                            <tr>
                                <th class="text-white-50 small">Provider</th>
                                <th class="text-white-50 small text-end">Detections</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $topProviders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $provider): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><small class="text-white"><?php echo e($provider['provider']); ?></small></td>
                                    <td class="text-end">
                                        <span class="badge bg-primary"><?php echo e(number_format($provider['count'])); ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="2" class="text-center text-white-50 py-4">
                                        <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                                        No data available
                                    </td>
                                </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        
        <div class="col-md-6">
            <div class="security-card">
                <h5 class="text-white fw-bold mb-3">
                    <i class="bi bi-router text-danger me-2"></i>
                    Top Suspicious IPs (7 Days)
                </h5>
                <div class="table-scroll">
                    <table class="table table-dark table-hover">
                        <thead>
                            <tr>
                                <th class="text-white-50 small">IP Address</th>
                                <th class="text-white-50 small text-end">Attempts</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $topIps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><code class="small text-info"><?php echo e($ip['ip']); ?></code></td>
                                    <td class="text-end">
                                        <span class="badge bg-danger"><?php echo e(number_format($ip['count'])); ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="2" class="text-center text-white-50 py-4">
                                        <i class="bi bi-shield-check fs-4 d-block mb-2"></i>
                                        No suspicious IPs detected
                                    </td>
                                </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $attributes = $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $component = $__componentOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\dating\resources\views\filament\pages\security-dashboard.blade.php ENDPATH**/ ?>