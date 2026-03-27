
<?php $__env->startSection('title', $category->name . ' - Forum'); ?>
<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('forum.index')); ?>">Forum</a></li>
            <li class="breadcrumb-item active"><?php echo e($category->name); ?></li>
        </ol>
    </nav>

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center"
                 style="width:48px;height:48px;background:<?php echo e($category->color ?? '#0d6efd'); ?>20;color:<?php echo e($category->color ?? '#0d6efd'); ?>;">
                <i class="<?php echo e($category->icon ?? 'bi bi-chat-dots'); ?> fs-4"></i>
            </div>
            <div>
                <h2 class="fw-bold mb-0"><?php echo e($category->name); ?></h2>
                <p class="text-muted small mb-0"><?php echo e($category->description); ?></p>
            </div>
        </div>
        <a href="<?php echo e(route('forum.create-topic', $category->slug)); ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>New Topic
        </a>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($topics->isEmpty()): ?>
    <div class="text-center py-5 text-muted">
        <i class="bi bi-chat-square fs-1"></i>
        <p class="mt-2">No topics yet. <a href="<?php echo e(route('forum.create-topic', $category->slug)); ?>">Start the first discussion!</a></p>
    </div>
    <?php else: ?>
    <div class="card border-0 shadow-sm">
        <div class="list-group list-group-flush">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $topics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $topic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('forum.topic', [$category->slug, $topic->slug])); ?>"
               class="list-group-item list-group-item-action py-3">
                <div class="d-flex align-items-start gap-3">
                    <div class="flex-shrink-0 mt-1">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($topic->is_pinned): ?>
                        <i class="bi bi-pin-angle-fill text-warning" title="Pinned"></i>
                        <?php elseif($topic->is_locked): ?>
                        <i class="bi bi-lock-fill text-secondary" title="Locked"></i>
                        <?php else: ?>
                        <i class="bi bi-chat-left-text text-primary"></i>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="fw-semibold"><?php echo e($topic->title); ?></div>
                        <small class="text-muted">
                            by <?php echo e($topic->author->name); ?> &bull;
                            <?php echo e($topic->created_at->format('M j, Y')); ?>

                        </small>
                    </div>
                    <div class="text-end text-muted small flex-shrink-0">
                        <div><i class="bi bi-reply me-1"></i><?php echo e($topic->replies_count); ?></div>
                        <div><i class="bi bi-eye me-1"></i><?php echo e($topic->views_count); ?></div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($topic->last_reply_at): ?>
                        <div class="text-nowrap"><?php echo e($topic->last_reply_at->diffForHumans()); ?></div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
    <div class="mt-4 d-flex justify-content-center"><?php echo e($topics->links()); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\dating\resources\views\forum\category.blade.php ENDPATH**/ ?>