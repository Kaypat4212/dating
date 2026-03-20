
<?php $__env->startSection('title', 'Create Profile — Step 5'); ?>
<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <?php echo $__env->make('setup._progress', ['current' => 5], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <div class="card border-0 shadow p-4">
                <h4 class="fw-bold mb-1">Your Interests</h4>
                <p class="text-muted small mb-4">Step 5: Pick at least 3 interests to help us find great matches</p>
                <form method="POST" action="<?php echo e(route('setup.store', 5)); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $interests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $interest): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div>
                            <input type="checkbox" class="btn-check" name="interests[]" id="int<?php echo e($interest->id); ?>" value="<?php echo e($interest->id); ?>"
                                <?php echo e(in_array($interest->id, old('interests', session('setup.interests', []))) ? 'checked' : ''); ?>>
                            <label class="btn btn-outline-primary btn-sm rounded-pill" for="int<?php echo e($interest->id); ?>"><?php echo e($interest->name); ?></label>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="<?php echo e(route('setup.step', 4)); ?>" class="btn btn-outline-secondary flex-fill"><i class="bi bi-arrow-left me-2"></i>Back</a>
                        <button type="submit" class="btn btn-success flex-fill fw-bold"><i class="bi bi-check-circle me-2"></i>Finish Setup!</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\dating\resources\views\setup\step5.blade.php ENDPATH**/ ?>