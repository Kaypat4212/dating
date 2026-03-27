<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($status): ?>
<div <?php echo e($attributes->merge(['class' => ''])); ?>>
    <div class="d-flex align-items-center gap-2 p-3 rounded-3 mb-0" style="background:rgba(25,135,84,.18);border:1px solid rgba(25,135,84,.3);color:#d1fae5">
        <i class="bi bi-check-circle-fill" style="color:#4ade80;font-size:1.1rem;flex-shrink:0"></i>
        <span style="font-size:.875rem"><?php echo e($status); ?></span>
    </div>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\xampp\htdocs\dating\resources\views\components\auth-session-status.blade.php ENDPATH**/ ?>