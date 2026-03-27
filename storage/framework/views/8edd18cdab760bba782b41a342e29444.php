
<?php $__env->startSection('title', 'Setup — Step 1 of 5'); ?>
<?php $__env->startSection('content'); ?>
<div class="container py-5" style="max-width:640px">

    
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="small fw-semibold text-muted">Step <?php echo e($step); ?> of <?php echo e($total); ?></span>
            <span class="small text-muted"><?php echo e(round(($step/$total)*100)); ?>% complete</span>
        </div>
        <div class="progress" style="height:6px;border-radius:10px">
            <div class="progress-bar bg-primary" style="width:<?php echo e(round(($step/$total)*100)); ?>%"></div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
        <div class="text-center mb-4">
            <div class="display-5 mb-2">👋</div>
            <h3 class="fw-bold">Tell us about yourself</h3>
            <p class="text-muted">This helps us find your best matches.</p>
        </div>

        <form method="POST" action="<?php echo e(route('setup.store', ['step' => 1])); ?>">
            <?php echo csrf_field(); ?>

            
            <div class="mb-4">
                <label class="form-label fw-semibold">I am a</label>
                <div class="d-flex flex-wrap gap-2">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['male' => '👨 Man', 'female' => '👩 Woman', 'non_binary' => '🧑 Non-binary', 'other' => '🌈 Other']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label class="flex-fill text-center px-3 py-2 rounded-3 border cursor-pointer gender-opt <?php echo e(old('gender', $user->gender) === $val ? 'border-primary bg-primary bg-opacity-10' : ''); ?>" style="cursor:pointer">
                        <input type="radio" name="gender" value="<?php echo e($val); ?>" class="d-none gender-radio" <?php echo e(old('gender', $user->gender) === $val ? 'checked' : ''); ?>>
                        <?php echo e($label); ?>

                    </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['gender'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <div class="mb-4">
                <label class="form-label fw-semibold">Looking for</label>
                <div class="d-flex flex-wrap gap-2">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['male' => '👨 Men', 'female' => '👩 Women', 'everyone' => '❤️ Everyone']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label class="flex-fill text-center px-3 py-2 rounded-3 border cursor-pointer seek-opt <?php echo e(old('seeking', $user->seeking) === $val ? 'border-primary bg-primary bg-opacity-10' : ''); ?>" style="cursor:pointer">
                        <input type="radio" name="seeking" value="<?php echo e($val); ?>" class="d-none seek-radio" <?php echo e(old('seeking', $user->seeking) === $val ? 'checked' : ''); ?>>
                        <?php echo e($label); ?>

                    </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['seeking'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <div class="mb-4">
                <label for="date_of_birth" class="form-label fw-semibold">Date of Birth <span class="text-muted fw-normal">(must be 18+)</span></label>
                <input type="date" name="date_of_birth" id="date_of_birth" class="form-control <?php $__errorArgs = ['date_of_birth'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                    value="<?php echo e(old('date_of_birth', optional($user->date_of_birth)->format('Y-m-d'))); ?>"
                    max="<?php echo e(now()->subYears(18)->format('Y-m-d')); ?>" required>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['date_of_birth'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                Continue <i class="bi bi-arrow-right ms-1"></i>
            </button>
        </form>
    </div>
</div>

<script>
document.querySelectorAll('.gender-radio').forEach(r => r.addEventListener('change', () => {
    document.querySelectorAll('.gender-opt').forEach(l => l.classList.remove('border-primary','bg-primary','bg-opacity-10'));
    r.closest('.gender-opt').classList.add('border-primary','bg-primary','bg-opacity-10');
}));
document.querySelectorAll('.seek-radio').forEach(r => r.addEventListener('change', () => {
    document.querySelectorAll('.seek-opt').forEach(l => l.classList.remove('border-primary','bg-primary','bg-opacity-10'));
    r.closest('.seek-opt').classList.add('border-primary','bg-primary','bg-opacity-10');
}));
</script>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('just_registered')): ?>

<div class="modal fade" id="welcomeModal" tabindex="-1" aria-labelledby="welcomeModalLabel" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 text-center p-4 p-md-5">
            <div style="font-size:4rem;line-height:1">🎉</div>
            <h4 class="fw-bold mt-3 mb-2" id="welcomeModalLabel">Welcome to HeartsConnect!</h4>
            <p class="text-muted mb-4">
                Your account has been created successfully.<br>
                Let's set up your profile to find your perfect match!
            </p>
            <button type="button" class="btn btn-primary px-5 py-2 fw-bold rounded-pill mx-auto" data-bs-dismiss="modal">
                <i class="bi bi-hearts me-2"></i>Get Started
            </button>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var welcomeModal = new bootstrap.Modal(document.getElementById('welcomeModal'), { backdrop: 'static', keyboard: false });
    welcomeModal.show();
});
</script>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\dating\resources\views\onboarding\step1.blade.php ENDPATH**/ ?>