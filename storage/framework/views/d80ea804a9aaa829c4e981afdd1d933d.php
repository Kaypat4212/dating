
<?php $__env->startSection('title', 'Setup — Step 5 of 5'); ?>
<?php $__env->startSection('content'); ?>
<div class="container py-5" style="max-width:680px">

    
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="small fw-semibold text-muted">Step <?php echo e($step); ?> of <?php echo e($total); ?></span>
            <span class="small text-muted">Almost there!</span>
        </div>
        <div class="progress" style="height:6px;border-radius:10px">
            <div class="progress-bar bg-success" style="width:100%"></div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
        <div class="text-center mb-4">
            <div class="display-5 mb-2">🎯</div>
            <h3 class="fw-bold">Your Interests</h3>
            <p class="text-muted">Pick up to <strong>20 interests</strong> to match with like-minded people.</p>
            <div class="badge bg-secondary rounded-pill" id="count-badge">0 / 20 selected</div>
        </div>

        <form method="POST" action="<?php echo e(route('setup.store', ['step' => 5])); ?>" id="interests-form">
            <?php echo csrf_field(); ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['interests'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="alert alert-danger py-2"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="d-flex flex-wrap gap-2 mb-4" id="interests-grid">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $interests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $interest): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $checked = in_array($interest->id, old('interests', $selected ?? [])); ?>
                <label class="interest-chip badge rounded-pill border px-3 py-2 <?php echo e($checked ? 'bg-primary text-white border-primary' : 'bg-light text-dark'); ?>"
                    style="cursor:pointer;font-size:.9rem;user-select:none">
                    <input type="checkbox" name="interests[]" value="<?php echo e($interest->id); ?>"
                        class="d-none interest-check"
                        <?php echo e($checked ? 'checked' : ''); ?>>
                    <?php echo e($interest->icon ?? ''); ?> <?php echo e($interest->name); ?>

                </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <a href="<?php echo e(route('setup.step', 4)); ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
                <button type="submit" class="btn btn-success px-4 fw-bold" id="btn-finish">
                    🎉 Finish &amp; Find Matches
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const MAX = 20;
const badge = document.getElementById('count-badge');
const chips = document.querySelectorAll('.interest-chip');

function updateCount() {
    const checked = document.querySelectorAll('.interest-check:checked').length;
    badge.textContent = `${checked} / ${MAX} selected`;
    badge.className = checked >= MAX
        ? 'badge bg-warning text-dark rounded-pill'
        : 'badge bg-secondary rounded-pill';
}

chips.forEach(chip => {
    chip.addEventListener('click', () => {
        const cb = chip.querySelector('.interest-check');
        const checked = document.querySelectorAll('.interest-check:checked').length;

        if (!cb.checked && checked >= MAX) {
            badge.classList.add('animate__animated','animate__headShake');
            setTimeout(() => badge.classList.remove('animate__animated','animate__headShake'), 800);
            return;
        }

        cb.checked = !cb.checked;
        if (cb.checked) {
            chip.classList.replace('bg-light', 'bg-primary');
            chip.classList.replace('text-dark', 'text-white');
            chip.classList.add('border-primary');
        } else {
            chip.classList.replace('bg-primary', 'bg-light');
            chip.classList.replace('text-white', 'text-dark');
            chip.classList.remove('border-primary');
        }
        updateCount();
    });
});

// Init count on page load
updateCount();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\dating\resources\views\onboarding\step5.blade.php ENDPATH**/ ?>