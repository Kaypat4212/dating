
<?php $__env->startSection('title', 'Create Profile — Step 2'); ?>
<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <?php echo $__env->make('setup._progress', ['current' => 2], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <div class="card border-0 shadow p-4">
                <h4 class="fw-bold mb-1">Your profile</h4>
                <p class="text-muted small mb-4">Step 2: What makes you, you</p>
                <form method="POST" action="<?php echo e(route('setup.store', 2)); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tagline <span class="text-muted fw-normal small">(headline)</span></label>
                        <input type="text" name="tagline" class="form-control" value="<?php echo e(old('tagline', session('setup.tagline'))); ?>" maxlength="120" placeholder="e.g. Coffee addict & dog lover ☕🐶">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">About Me</label>
                        <textarea name="about" class="form-control" rows="4" maxlength="2000" placeholder="Tell others a bit about yourself..."><?php echo e(old('about', session('setup.about'))); ?></textarea>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Relationship Goal</label>
                            <select name="relationship_goal" class="form-select">
                                <option value="">Not specified</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['casual','long_term','marriage','friendship','unsure']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($g); ?>" <?php echo e(old('relationship_goal', session('setup.relationship_goal')) === $g ? 'selected' : ''); ?>><?php echo e(ucfirst(str_replace('_',' ',$g))); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Education</label>
                            <select name="education_level" class="form-select">
                                <option value="">Not specified</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['high_school','some_college','bachelors','masters','phd','trade_school']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($e); ?>" <?php echo e(old('education_level', session('setup.education_level')) === $e ? 'selected' : ''); ?>><?php echo e(ucfirst(str_replace('_',' ',$e))); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Occupation</label>
                        <input type="text" name="occupation" class="form-control" value="<?php echo e(old('occupation', session('setup.occupation'))); ?>" maxlength="100">
                    </div>
                    <div class="d-flex gap-2">
                        <a href="<?php echo e(route('setup.step', 1)); ?>" class="btn btn-outline-secondary flex-fill"><i class="bi bi-arrow-left me-2"></i>Back</a>
                        <button type="submit" class="btn btn-primary flex-fill fw-bold">Continue <i class="bi bi-arrow-right ms-2"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\dating\resources\views\setup\step2.blade.php ENDPATH**/ ?>