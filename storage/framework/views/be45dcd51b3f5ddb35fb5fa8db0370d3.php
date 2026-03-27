
<?php /** @var \Illuminate\Support\ViewErrorBag $errors */ ?>
<?php $__env->startSection('title', 'Feature Requests & Bug Reports'); ?>
<?php $__env->startSection('content'); ?>
<div class="container py-5" style="max-width:720px">

    <div class="text-center mb-5">
        <div class="display-5 mb-2">💡</div>
        <h1 class="fw-bold h2">Feature Requests &amp; Bug Reports</h1>
        <p class="text-muted">Got an idea to make the app better? Found a bug? Let us know — we read every submission.</p>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
    <div class="alert alert-success rounded-3 d-flex align-items-start gap-2 mb-4">
        <i class="bi bi-check-circle-fill fs-5 mt-1 text-success"></i>
        <div><?php echo e(session('success')); ?></div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
    <div class="alert alert-danger rounded-3 mb-4">
        <ul class="mb-0 ps-3">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </ul>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">

        
        <div class="mb-4">
            <label class="form-label fw-semibold">What are you submitting?</label>
            <div class="d-flex gap-2 flex-wrap" id="type-toggle">
                <input type="hidden" name="type" id="type-value" form="fr-form" value="<?php echo e(old('type', 'feature')); ?>">
                <button type="button" class="btn pill-btn <?php if(old('type','feature')==='feature'): ?> active <?php endif; ?>" data-value="feature">
                    <i class="bi bi-lightbulb me-1"></i>Feature Request
                </button>
                <button type="button" class="btn pill-btn <?php if(old('type')==='bug'): ?> active <?php endif; ?>" data-value="bug">
                    <i class="bi bi-bug me-1"></i>Bug Report
                </button>
            </div>
        </div>

        <form method="POST" action="<?php echo e(route('pages.feature-request.store')); ?>" id="fr-form">
            <?php echo csrf_field(); ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->guest()): ?>
            <div class="row g-3 mb-4">
                <div class="col-sm-6">
                    <label for="fr-name" class="form-label fw-semibold">Your Name</label>
                    <input type="text" id="fr-name" name="name" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                           placeholder="Jane Doe" value="<?php echo e(old('name')); ?>" required maxlength="100">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="col-sm-6">
                    <label for="fr-email" class="form-label fw-semibold">Email Address</label>
                    <input type="email" id="fr-email" name="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                           placeholder="you@example.com" value="<?php echo e(old('email')); ?>" required maxlength="200">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
            
            <input type="hidden" name="name"  value="<?php echo e(auth()->user()->name ?? auth()->user()->username); ?>">
            <input type="hidden" name="email" value="<?php echo e(auth()->user()->email); ?>">
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <div class="mb-3">
                <label for="fr-title" class="form-label fw-semibold">Title <span class="text-muted fw-normal small">(brief summary)</span></label>
                <input type="text" id="fr-title" name="title" class="form-control <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                       placeholder="e.g. Add voice messages in chat" value="<?php echo e(old('title')); ?>"
                       required maxlength="200">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <div class="mb-4">
                <label for="fr-body" class="form-label fw-semibold">Details</label>
                <textarea id="fr-body" name="body" rows="6"
                          class="form-control <?php $__errorArgs = ['body'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                          placeholder="Describe the feature or bug in detail. For bugs: what happened, what you expected, and how to reproduce it."
                          required maxlength="5000"><?php echo e(old('body')); ?></textarea>
                <div class="form-text d-flex justify-content-between">
                    <span id="fr-type-hint" class="text-muted">Be as specific as possible — it helps us prioritise.</span>
                    <span id="char-count" class="text-muted">0 / 5000</span>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['body'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <p class="text-muted small mb-0">
                    <i class="bi bi-shield-check me-1"></i>We'll reply to your email once we review your submission.
                </p>
                <button type="submit" class="btn btn-primary px-4 fw-semibold">
                    <i class="bi bi-send me-1"></i>Submit
                </button>
            </div>
        </form>
    </div>

    
    <div class="card border-0 rounded-4 mt-4 p-4" style="background:rgba(var(--bs-primary-rgb),.06)">
        <div class="d-flex gap-3 align-items-start">
            <div class="fs-3">🏆</div>
            <div>
                <h5 class="fw-bold mb-1">Bug Bounty Program</h5>
                <p class="text-muted mb-0 small">
                    Found a security vulnerability? We take security seriously. Submit a detailed bug report using the form above (type: <strong>Bug Report</strong>), and if your report leads to a confirmed security fix, we'll recognise your contribution. Critical vulnerabilities may qualify for a reward at admin discretion.
                </p>
            </div>
        </div>
    </div>
</div>

<style>
.pill-btn {
    border: 2px solid #dee2e6;
    border-radius: 50px;
    font-size: .875rem;
    padding: .35rem 1.1rem;
    color: #6c757d;
    background: transparent;
    transition: all .15s;
}
.pill-btn.active, .pill-btn:focus {
    border-color: var(--bs-primary);
    background: var(--bs-primary);
    color: #fff;
}
</style>

<script>
const typeButtons = document.querySelectorAll('#type-toggle .pill-btn');
const typeValue   = document.getElementById('type-value');
const hint        = document.getElementById('fr-type-hint');
const body        = document.getElementById('fr-body');
const charCount   = document.getElementById('char-count');

typeButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        typeButtons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        typeValue.value = btn.dataset.value;
        hint.textContent = btn.dataset.value === 'bug'
            ? 'For bugs: describe what happened, what you expected, and steps to reproduce.'
            : 'Be as specific as possible — it helps us prioritise.';
    });
});

body.addEventListener('input', () => {
    charCount.textContent = body.value.length + ' / 5000';
});
charCount.textContent = body.value.length + ' / 5000';
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\dating\resources\views\pages\feature-request.blade.php ENDPATH**/ ?>