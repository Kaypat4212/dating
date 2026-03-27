
<?php $__env->startSection('title', 'Blocked Users'); ?>
<?php $__env->startSection('content'); ?>
<div class="container py-4" style="max-width:760px">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="<?php echo e(route('account.show')); ?>" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
        <h4 class="fw-bold mb-0"><i class="bi bi-slash-circle text-danger me-2"></i>Blocked Users</h4>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show"><?php echo e(session('success')); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($blocks->isEmpty()): ?>
        <div class="card border-0 shadow-sm text-center py-5">
            <div class="display-1 mb-3">🚫</div>
            <h5>No blocked users</h5>
            <p class="text-muted">Users you block will appear here. You can unblock them any time.</p>
        </div>
    <?php else: ?>
        <p class="text-muted small mb-3"><?php echo e($blocks->total()); ?> <?php echo e(Str::plural('user', $blocks->total())); ?> blocked. Blocked users cannot like, message, or see your profile.</p>
        <div class="row g-3">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $blocks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $block): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $blocked = $block->blocked; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$blocked): ?> <?php continue; ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <div class="col-6 col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="ratio ratio-1x1 overflow-hidden bg-light">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($blocked->primaryPhoto): ?>
                            <img src="<?php echo e($blocked->primaryPhoto->thumbnail_url); ?>"
                                 class="object-fit-cover w-100 h-100" alt="<?php echo e($blocked->name); ?>">
                        <?php else: ?>
                            <div class="d-flex align-items-center justify-content-center h-100">
                                <i class="bi bi-person-circle display-3 text-muted"></i>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="card-body p-2">
                        <div class="fw-semibold text-truncate"><?php echo e($blocked->name); ?></div>
                        <div class="text-muted small">@<span><?php echo e($blocked->username); ?></span></div>
                    </div>
                    <div class="card-footer bg-transparent border-0 p-2 pt-0">
                        <form method="POST" action="<?php echo e(route('block.destroy', $blocked->id)); ?>"
                              onsubmit="return confirm('Unblock <?php echo e($blocked->name); ?>?')">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button class="btn btn-sm btn-outline-danger w-100">
                                <i class="bi bi-slash-circle me-1"></i>Unblock
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <div class="mt-4 d-flex justify-content-center">
            <?php echo e($blocks->links('pagination::bootstrap-5')); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\dating\resources\views\account\blocked.blade.php ENDPATH**/ ?>