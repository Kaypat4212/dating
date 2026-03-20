

<?php $__env->startSection('title', 'Verify Your Email'); ?>

<?php $__env->startSection('content'); ?>
<div class="text-center mb-4">
    <div style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#e91e8c,#9b27af);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;font-size:2rem;">
        ✉️
    </div>
    <h4 class="fw-bold mb-1">Check your inbox</h4>
    <p class="text-muted small mb-0">
        We sent a <strong>6-digit verification code</strong> to<br>
        <span class="text-white"><?php echo e(auth()->user()->email); ?></span>
    </p>
</div>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('status') === 'otp-sent'): ?>
<div class="alert alert-success small text-center py-2 mb-3">
    <i class="bi bi-check-circle me-1"></i>A new code has been sent to your email.
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
<div class="alert alert-danger small py-2 mb-3">
    <?php echo e($errors->first()); ?>

</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<form method="POST" action="<?php echo e(route('verification.otp.verify')); ?>" id="otpForm">
    <?php echo csrf_field(); ?>

    
    <div class="d-flex justify-content-center gap-2 mb-4" id="otpBoxes">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 0; $i < 6; $i++): ?>
        <input type="text"
               inputmode="numeric"
               maxlength="1"
               class="otp-box form-control text-center fw-bold fs-4"
               style="width:52px;height:60px;border-radius:12px;border:2px solid rgba(255,255,255,.2);background:rgba(255,255,255,.05);color:#fff;caret-color:#e91e8c;"
               autocomplete="off"
               data-index="<?php echo e($i); ?>">
        <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <input type="hidden" name="otp" id="otpHidden">

    <button type="submit" id="submitBtn" class="btn btn-primary w-100 fw-semibold py-2 mb-3" disabled
            style="background:linear-gradient(135deg,#e91e8c,#9b27af);border:none;border-radius:12px;">
        <i class="bi bi-shield-check me-2"></i>Verify Email
    </button>
</form>

<div class="text-center">
    <p class="text-muted small mb-2">Didn't receive a code?</p>

    <form method="POST" action="<?php echo e(route('verification.otp.resend')); ?>" id="resendForm">
        <?php echo csrf_field(); ?>
        <button type="submit" id="resendBtn" class="btn btn-link p-0 text-decoration-none"
                style="color:#e91e8c;font-size:.9rem;">
            <i class="bi bi-arrow-clockwise me-1"></i>Resend Code
        </button>
    </form>

    <p class="text-muted" style="font-size:.75rem;margin-top:.5rem" id="resendTimer" style="display:none"></p>
</div>

<hr style="border-color:rgba(255,255,255,.1);margin:1.5rem 0">

<div class="text-center">
    <form method="POST" action="<?php echo e(route('logout')); ?>">
        <?php echo csrf_field(); ?>
        <button type="submit" class="btn btn-link p-0 text-muted text-decoration-none" style="font-size:.85rem;">
            <i class="bi bi-box-arrow-left me-1"></i>Sign out
        </button>
    </form>
</div>

<script>
(function () {
    const boxes   = Array.from(document.querySelectorAll('.otp-box'));
    const hidden  = document.getElementById('otpHidden');
    const submit  = document.getElementById('submitBtn');
    const resend  = document.getElementById('resendBtn');
    const timer   = document.getElementById('resendTimer');

    // ── OTP box logic ──────────────────────────────────────────
    boxes.forEach((box, i) => {
        box.addEventListener('input', () => {
            box.value = box.value.replace(/\D/g, '').slice(-1);
            if (box.value && i < 5) boxes[i + 1].focus();
            sync();
        });
        box.addEventListener('keydown', e => {
            if (e.key === 'Backspace' && !box.value && i > 0) boxes[i - 1].focus();
        });
        box.addEventListener('paste', e => {
            e.preventDefault();
            const digits = (e.clipboardData.getData('text') || '').replace(/\D/g, '').slice(0, 6);
            digits.split('').forEach((d, j) => { if (boxes[j]) boxes[j].value = d; });
            const last = Math.min(digits.length, 5);
            boxes[last].focus();
            sync();
        });
    });

    function sync() {
        const val = boxes.map(b => b.value).join('');
        hidden.value = val;
        submit.disabled = val.length < 6;
        if (val.length === 6) submit.style.opacity = '1';
    }

    // ── Resend cooldown (60 s) ─────────────────────────────────
    <?php if(session('status') === 'otp-sent'): ?>
    startCooldown(60);
    <?php endif; ?>

    document.getElementById('resendForm').addEventListener('submit', () => startCooldown(60));

    function startCooldown(sec) {
        resend.disabled = true;
        resend.style.opacity = '.4';
        timer.style.display = 'block';
        const end = Date.now() + sec * 1000;
        const tick = setInterval(() => {
            const left = Math.ceil((end - Date.now()) / 1000);
            if (left <= 0) {
                clearInterval(tick);
                resend.disabled = false;
                resend.style.opacity = '1';
                timer.textContent = '';
                timer.style.display = 'none';
            } else {
                timer.textContent = `Wait ${left}s before requesting another code`;
            }
        }, 500);
    }

    // Focus first empty box on load
    boxes[0].focus();
})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\dating\resources\views\auth\verify-otp.blade.php ENDPATH**/ ?>