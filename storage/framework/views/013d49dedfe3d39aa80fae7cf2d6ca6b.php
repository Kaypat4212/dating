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


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
:root {
    --primary-gradient: linear-gradient(135deg, #f43f5e 0%, #a855f7 100%);
    --card-bg: #1a1625;
}

body {
    background: #0f0a1a;
}

.server-card {
    background: linear-gradient(135deg, rgba(30, 10, 46, 0.9), rgba(45, 16, 80, 0.7));
    border: 1px solid rgba(244, 63, 94, 0.15);
    border-radius: 20px;
    padding: 2rem;
    transition: all 0.3s ease;
}

.server-card:hover {
    border-color: rgba(244, 63, 94, 0.4);
    box-shadow: 0 8px 24px rgba(244, 63, 94, 0.15);
}

.status-badge {
    padding: 0.75rem 1.5rem;
    border-radius: 50px;
    font-weight: 700;
    font-size: 0.9rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.status-running {
    background: rgba(16, 185, 129, 0.15);
    border: 2px solid rgba(16, 185, 129, 0.4);
    color: #10b981;
}

.status-stopped {
    background: rgba(239, 68, 68, 0.15);
    border: 2px solid rgba(239, 68, 68, 0.4);
    color: #ef4444;
}

.status-unknown {
    background: rgba(156, 163, 175, 0.15);
    border: 2px solid rgba(156, 163, 175, 0.4);
    color: #9ca3af;
}

.status-unavailable {
    background: rgba(245, 158, 11, 0.15);
    border: 2px solid rgba(245, 158, 11, 0.4);
    color: #f59e0b;
}

@keyframes pulse-ring {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.1); opacity: 0.8; }
}

.pulse-indicator {
    animation: pulse-ring 2s ease-in-out infinite;
}

.control-btn {
    padding: 0.75rem 2rem;
    border-radius: 12px;
    font-weight: 600;
    border: none;
    transition: all 0.3s ease;
}

.btn-start {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.btn-start:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
}

.btn-stop {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
}

.btn-stop:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
}

.btn-restart {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
}

.btn-restart:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
}

.output-console {
    background: #0f0a1a;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 1.5rem;
    font-family: 'Consolas', 'Monaco', monospace;
    font-size: 0.9rem;
    color: #10b981;
    min-height: 200px;
    max-height: 400px;
    overflow-y: auto;
    white-space: pre-wrap;
    word-wrap: break-word;
}

.info-box {
    background: rgba(59, 130, 246, 0.1);
    border: 1px solid rgba(59, 130, 246, 0.3);
    border-radius: 12px;
    padding: 1.25rem;
}

.config-item {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.config-item:last-child {
    border-bottom: none;
}
</style>

<div class="container-fluid px-4 py-4">
    
    
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="display-6 fw-bold text-white mb-2">
                <i class="bi bi-broadcast me-2" style="color: #f43f5e;"></i>
                Reverb WebSocket Server
            </h1>
            <p class="text-white-50 mb-0">Manage real-time broadcasting server for live notifications and messaging</p>
        </div>
    </div>

    <div class="row g-4">
        
        
        <div class="col-lg-8">
            <div class="server-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="text-white fw-bold mb-0">
                        <i class="bi bi-server me-2"></i>Server Status
                    </h4>
                    <button wire:click="checkServerStatus" class="btn btn-sm btn-outline-light">
                        <i class="bi bi-arrow-clockwise"></i> Refresh
                    </button>
                </div>

                <div class="text-center mb-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($serverStatus === 'running'): ?>
                        <div class="status-badge status-running">
                            <div class="pulse-indicator">
                                <i class="bi bi-circle-fill"></i>
                            </div>
                            Server Running
                        </div>
                    <?php elseif($serverStatus === 'stopped'): ?>
                        <div class="status-badge status-stopped">
                            <i class="bi bi-x-circle-fill"></i>
                            Server Stopped
                        </div>
                    <?php elseif($serverStatus === 'unavailable'): ?>
                        <div class="status-badge status-unavailable">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            Control Unavailable
                        </div>
                    <?php else: ?>
                        <div class="status-badge status-unknown">
                            <i class="bi bi-question-circle-fill"></i>
                            Status Unknown
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                
                <div class="d-flex gap-3 justify-content-center flex-wrap mb-4">
                    <button wire:click="startServer" 
                            class="control-btn btn-start"
                            wire:loading.attr="disabled"
                            <?php echo e(($isRunning || $serverStatus === 'unavailable') ? 'disabled' : ''); ?>>
                        <i class="bi bi-play-fill me-2"></i>
                        Start Server
                    </button>

                    <button wire:click="stopServer" 
                            class="control-btn btn-stop"
                            wire:loading.attr="disabled"
                            <?php echo e((!$isRunning || $serverStatus === 'unavailable') ? 'disabled' : ''); ?>>
                        <i class="bi bi-stop-fill me-2"></i>
                        Stop Server
                    </button>

                    <button wire:click="restartServer" 
                            class="control-btn btn-restart"
                            wire:loading.attr="disabled"
                            <?php echo e($serverStatus === 'unavailable' ? 'disabled' : ''); ?>>
                        <i class="bi bi-arrow-repeat me-2"></i>
                        Restart Server
                    </button>
                </div>

                
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="text-white-50 fw-semibold small">Console Output</label>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($output): ?>
                            <button wire:click="clearOutput" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-trash"></i> Clear
                            </button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="output-console">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($output): ?>
                            <?php echo e($output); ?>

                        <?php else: ?>
                            <span class="text-white-50 fst-italic">No output yet. Start or stop the server to see logs.</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-lg-4">
            <div class="server-card mb-4">
                <h5 class="text-white fw-bold mb-3">
                    <i class="bi bi-gear me-2"></i>Configuration
                </h5>
                <div>
                    <div class="config-item">
                        <span class="text-white-50">Host</span>
                        <code class="text-white"><?php echo e(env('REVERB_HOST', 'localhost')); ?></code>
                    </div>
                    <div class="config-item">
                        <span class="text-white-50">Port</span>
                        <code class="text-white"><?php echo e(env('REVERB_PORT', 8080)); ?></code>
                    </div>
                    <div class="config-item">
                        <span class="text-white-50">Scheme</span>
                        <code class="text-white"><?php echo e(env('REVERB_SCHEME', 'http')); ?></code>
                    </div>
                    <div class="config-item">
                        <span class="text-white-50">App ID</span>
                        <code class="text-white"><?php echo e(env('REVERB_APP_ID', 'dating-app')); ?></code>
                    </div>
                </div>
            </div>

            <div class="info-box">
                <h6 class="text-white fw-bold mb-3">
                    <i class="bi bi-info-circle me-2"></i>Information
                </h6>
                <div class="small text-white-50">
                    <p><strong>What is Reverb?</strong></p>
                    <p>Laravel Reverb is a WebSocket server for real-time features like:</p>
                    <ul class="mb-3">
                        <li>Live online status</li>
                        <li>Instant notifications</li>
                        <li>Real-time messaging</li>
                        <li>Typing indicators</li>
                        <li>Live match updates</li>
                    </ul>
                    <p class="mb-1"><strong>Manual Command:</strong></p>
                    <code class="text-success d-block p-2 rounded" style="background: rgba(0,0,0,0.3);">
                        php artisan reverb:start
                    </code>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Reverb Control Panel initialized');
    
    // Auto-refresh status every 30 seconds
    setInterval(() => {
        window.Livewire.find('<?php echo e($_instance->getId()); ?>').call('checkServerStatus');
    }, 30000);
});
</script>

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
<?php /**PATH C:\xampp\htdocs\dating\resources\views\filament\pages\reverb-control.blade.php ENDPATH**/ ?>