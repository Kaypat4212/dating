
<?php $__env->startSection('title', 'Who Viewed Me'); ?>
<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <h4 class="fw-bold mb-4"><i class="bi bi-eye text-info me-2"></i>Who Viewed Your Profile</h4>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($views->isEmpty()): ?>
    <div class="text-center py-5"><div class="display-1 mb-3">👀</div><h5>No views yet</h5><p class="text-muted">Complete your profile and add photos to get more visits!</p></div>
    <?php else: ?>
    <div class="row g-3">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $views; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $view): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $viewer = $view->viewer ?>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm h-100 profile-card <?php echo e(! auth()->user()->isPremiumActive() ? 'position-relative' : ''); ?>">
                <div class="ratio ratio-1x1 overflow-hidden">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($viewer->primaryPhoto): ?>
                    <img src="<?php echo e($viewer->primaryPhoto->thumbnail_url); ?>"
                         class="object-fit-cover w-100 h-100 <?php echo e(! auth()->user()->isPremiumActive() ? 'blur-premium' : ''); ?>" alt="">
                    <?php else: ?>
                    <div class="bg-light d-flex align-items-center justify-content-center"><i class="bi bi-person-circle display-3 text-muted"></i></div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="card-body p-2">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->isPremiumActive()): ?>
                    <div class="fw-semibold"><?php echo e($viewer->name); ?>, <?php echo e($viewer->age); ?></div>
                    <div class="text-muted" style="font-size:.72rem"><?php echo e($view->viewed_at->diffForHumans()); ?></div>
                    <?php else: ?>
                    <div class="fw-semibold text-muted">Premium Required</div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->isPremiumActive() && $viewer && $viewer->username): ?>
                <a href="<?php echo e(route('profile.show', $viewer->username)); ?>" class="stretched-link"></a>
                <?php elseif(! auth()->user()->isPremiumActive()): ?>
                <div class="position-absolute top-0 start-0 end-0 bottom-0 d-flex align-items-center justify-content-center rounded" style="background:rgba(255,255,255,.6)">
                    <a href="<?php echo e(route('premium.show')); ?>" class="btn btn-warning btn-sm fw-bold shadow"><i class="bi bi-star-fill me-1"></i>Unlock</a>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <div class="mt-4 d-flex justify-content-center"><?php echo e($views->links('pagination::bootstrap-5')); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\dating\resources\views\profile\who-viewed.blade.php ENDPATH**/ ?>