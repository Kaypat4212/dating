
<?php $__env->startSection('title', 'My Matches'); ?>
<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <h4 class="fw-bold mb-4"><i class="bi bi-hearts text-danger me-2"></i>My Matches <span class="badge bg-primary"><?php echo e($matches->total()); ?></span></h4>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($matches->isEmpty()): ?>
    <div class="text-center py-5">
        <div class="display-1 mb-3">💔</div>
        <h5>No matches yet</h5>
        <p class="text-muted">Start liking profiles to find your matches!</p>
        <a href="<?php echo e(route('swipe.deck')); ?>" class="btn btn-primary"><i class="bi bi-fire me-2"></i>Start Swiping</a>
    </div>
    <?php else: ?>
    <div class="row g-3">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $matches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $match): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $other = $match->getOtherUser(auth()->id()) ?>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm h-100 profile-card">
                <div class="ratio ratio-1x1 overflow-hidden">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($other->primaryPhoto): ?>
                    <img src="<?php echo e($other->primaryPhoto->thumbnail_url); ?>" class="object-fit-cover w-100 h-100" alt="<?php echo e($other->name); ?>">
                    <?php else: ?>
                    <div class="bg-light d-flex align-items-center justify-content-center"><i class="bi bi-person-circle display-3 text-muted"></i></div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="card-body p-2">
                    <div class="fw-semibold"><?php echo e($other->name); ?>, <?php echo e($other->age); ?></div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($match->lastMessage): ?>
                    <p class="mb-0 text-muted text-truncate" style="font-size:.75rem"><?php echo e($match->lastMessage->body); ?></p>
                    <?php else: ?>
                    <p class="mb-0 text-muted" style="font-size:.75rem"><em>No messages yet — say hi!</em></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="card-footer bg-transparent p-2 d-flex gap-1">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($other->username): ?>
                    <a href="<?php echo e(route('profile.show', $other->username)); ?>" class="btn btn-outline-secondary btn-sm flex-fill"><i class="bi bi-person"></i></a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($match->conversation): ?>
                    <a href="<?php echo e(route('conversations.show', $match->conversation->id)); ?>" class="btn btn-primary btn-sm flex-fill"><i class="bi bi-chat-heart"></i></a>
                    <?php else: ?>
                    <a href="<?php echo e(route('conversations.index')); ?>" class="btn btn-primary btn-sm flex-fill"><i class="bi bi-chat-heart"></i></a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <div class="mt-4 d-flex justify-content-center"><?php echo e($matches->links('pagination::bootstrap-5')); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\dating\resources\views\matches\index.blade.php ENDPATH**/ ?>