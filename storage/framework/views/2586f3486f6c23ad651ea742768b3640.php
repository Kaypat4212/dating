
<?php $__env->startSection('title', 'Blog'); ?>
<?php $__env->startSection('content'); ?>
<div class="container py-4">

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($featured): ?>
    <div class="card mb-4 border-0 shadow overflow-hidden">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($featured->featured_image): ?>
        <img src="<?php echo e(Storage::url($featured->featured_image)); ?>" alt="<?php echo e($featured->title); ?>"
             class="card-img-top" style="max-height:340px;object-fit:cover;">
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <div class="card-body">
            <span class="badge bg-danger mb-2">Featured</span>
            <h2 class="card-title fw-bold">
                <a href="<?php echo e(route('blog.show', $featured->slug)); ?>" class="text-decoration-none text-dark stretched-link">
                    <?php echo e($featured->title); ?>

                </a>
            </h2>
            <p class="card-text text-muted"><?php echo e($featured->excerpt); ?></p>
            <small class="text-muted">
                By <?php echo e($featured->author->name); ?> &bull;
                <?php echo e($featured->published_at->diffForHumans()); ?> &bull;
                <i class="bi bi-eye"></i> <?php echo e(number_format($featured->views_count)); ?>

            </small>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="row g-4">
        
        <div class="col-lg-3 order-lg-last">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header fw-semibold"><i class="bi bi-grid me-2"></i>Categories</div>
                <div class="list-group list-group-flush">
                    <a href="<?php echo e(route('blog.index')); ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo e(!request()->routeIs('blog.category') ? 'active' : ''); ?>">
                        All Posts
                    </a>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('blog.category', $cat->slug)); ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cat->icon): ?><i class="<?php echo e($cat->icon); ?> me-2"></i><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
                <p class="mt-2">No posts yet. Check back soon!</p>
            </div>
            <?php else: ?>
            <div class="row g-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($post->featured_image): ?>
                        <img src="<?php echo e(Storage::url($post->featured_image)); ?>" alt="<?php echo e($post->title); ?>"
                             class="card-img-top" style="height:160px;object-fit:cover;">
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($post->category): ?>
                            <span class="badge mb-2" style="background:<?php echo e($post->category->color ?? '#6c757d'); ?>">
                                <?php echo e($post->category->name); ?>

                            </span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <h6 class="card-title fw-bold">
                                <a href="<?php echo e(route('blog.show', $post->slug)); ?>" class="text-decoration-none text-dark stretched-link">
                                    <?php echo e($post->title); ?>

                                </a>
                            </h6>
                            <p class="card-text text-muted small flex-grow-1"><?php echo e(Str::limit($post->excerpt, 100)); ?></p>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <small class="text-muted"><?php echo e($post->published_at->format('M j, Y')); ?></small>
                                <small class="text-muted">
                                    <i class="bi bi-eye me-1"></i><?php echo e(number_format($post->views_count)); ?>

                                    <i class="bi bi-chat ms-2 me-1"></i><?php echo e($post->comments_count); ?>

                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div class="mt-4 d-flex justify-content-center">
                <?php echo e($posts->links()); ?>

            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\dating\resources\views\blog\index.blade.php ENDPATH**/ ?>