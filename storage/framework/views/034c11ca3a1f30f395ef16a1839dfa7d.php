
<?php $__env->startSection('title', $category->name . ' - Blog'); ?>
<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('blog.index')); ?>">Blog</a></li>
            <li class="breadcrumb-item active"><?php echo e($category->name); ?></li>
        </ol>
    </nav>

    <div class="d-flex align-items-center mb-4 gap-3">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($category->icon): ?>
        <i class="<?php echo e($category->icon); ?> fs-2" style="color:<?php echo e($category->color ?? '#0d6efd'); ?>"></i>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <div>
            <h2 class="fw-bold mb-0"><?php echo e($category->name); ?></h2>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($category->description): ?><p class="text-muted mb-0"><?php echo e($category->description); ?></p><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-3 order-lg-last">
            <div class="card border-0 shadow-sm">
                <div class="card-header fw-semibold"><i class="bi bi-grid me-2"></i>Categories</div>
                <div class="list-group list-group-flush">
                    <a href="<?php echo e(route('blog.index')); ?>" class="list-group-item list-group-item-action">All Posts</a>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('blog.category', $cat->slug)); ?>"
                       class="list-group-item list-group-item-action d-flex justify-content-between <?php echo e($cat->id === $category->id ? 'active' : ''); ?>">
                        <?php echo e($cat->name); ?>

                        <span class="badge bg-secondary rounded-pill"><?php echo e($cat->posts_count); ?></span>
                    </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($posts->isEmpty()): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-journal-x fs-1"></i>
                <p class="mt-2">No posts in this category yet.</p>
            </div>
            <?php else: ?>
            <div class="row g-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($post->featured_image): ?>
                        <img src="<?php echo e(Storage::url($post->featured_image)); ?>" class="card-img-top" style="height:160px;object-fit:cover;" alt="">
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h6 class="fw-bold">
                                <a href="<?php echo e(route('blog.show', $post->slug)); ?>" class="text-decoration-none text-dark stretched-link">
                                    <?php echo e($post->title); ?>

                                </a>
                            </h6>
                            <p class="text-muted small flex-grow-1"><?php echo e(Str::limit($post->excerpt, 120)); ?></p>
                            <small class="text-muted">
                                By <?php echo e($post->author->name); ?> &bull; <?php echo e($post->published_at->format('M j, Y')); ?>

                            </small>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div class="mt-4 d-flex justify-content-center"><?php echo e($posts->links()); ?></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\dating\resources\views\blog\category.blade.php ENDPATH**/ ?>