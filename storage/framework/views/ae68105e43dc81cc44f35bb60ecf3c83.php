<div class="d-flex justify-content-between mb-4">
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 1; $i <= 5; $i++): ?>
<div class="flex-fill mx-1">
    <div class="rounded-pill" style="height:6px;background:<?php echo e($i <= $current ? 'var(--bs-primary)' : '#dee2e6'); ?>"></div>
    <div class="text-center mt-1" style="font-size:.65rem;color:<?php echo e($i <= $current ? 'var(--bs-primary)' : '#999'); ?>"><?php echo e($i); ?></div>
</div>
<?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\xampp\htdocs\dating\resources\views\setup\_progress.blade.php ENDPATH**/ ?>