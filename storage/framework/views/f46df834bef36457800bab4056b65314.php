
<?php $__env->startSection('title', 'Community Forum'); ?>
<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="bi bi-people me-2 text-primary"></i>Community Forum</h2>
            <p class="text-muted mb-0 small">Connect, share, and discuss with the community</p>
        </div>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($recentTopics->isNotEmpty()): ?>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header fw-semibold"><i class="bi bi-clock-history me-2"></i>Recent Discussions</div>
        <div class="list-group list-group-flush">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $recentTopics->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $topic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('forum.topic', [$topic->category->slug, $topic->slug])); ?>"
               class="list-group-item list-group-item-action py-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1 me-3">
                        <div class="fw-semibold small"><?php echo e($topic->title); ?></div>
                        <small class="text-muted">
                            <?php echo e($topic->author->name); ?> in
                            <span class="text-primary"><?php echo e($topic->category->name); ?></span>
                        </small>
                    </div>
                    <small class="text-muted text-nowrap"><?php echo e($topic->last_reply_at?->diffForHumans()); ?></small>
                </div>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="row g-3">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:48px;height:48px;background:<?php echo e($category->color ?? '#0d6efd'); ?>20;color:<?php echo e($category->color ?? '#0d6efd'); ?>;">
                            <i class="<?php echo e($category->icon ?? 'bi bi-chat-dots'); ?> fs-4"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <h5 class="fw-bold mb-1">
                                <a href="<?php echo e(route('forum.category', $category->slug)); ?>" class="text-decoration-none text-dark stretched-link">
                                    <?php echo e($category->name); ?>

                                </a>
                            </h5>
                            <p class="text-muted small mb-2"><?php echo e($category->description); ?></p>
                            <div class="d-flex gap-3 align-items-center">
                                <small class="text-muted"><i class="bi bi-chat-left me-1"></i><?php echo e($category->topics_count); ?> topics</small>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($category->topics->isNotEmpty()): ?>
                                <small class="text-muted text-truncate">
                                    Latest: <?php echo e($category->topics->first()?->title); ?>

                                </small>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($categories->isEmpty()): ?>
    <div class="text-center py-5 text-muted">
        <i class="bi bi-people fs-1"></i>
        <p class="mt-2">Forum categories are being set up. Check back soon!</p>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\dating\resources\views\forum\index.blade.php ENDPATH**/ ?>