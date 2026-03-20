
<?php $__env->startSection('title', 'Waves Received'); ?>
<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="d-flex align-items-center gap-2 mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-hand-wave text-warning me-2"></i>Waves Received</h4>
        <span class="badge bg-primary"><?php echo e($waves->total()); ?></span>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?><div class="alert alert-success alert-dismissible fade show" role="alert"><?php echo e(session('success')); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($waves->isEmpty()): ?>
    <div class="card border-0 shadow-sm text-center py-5">
        <div class="display-1 mb-3">👋</div>
        <h5>No waves yet</h5>
        <p class="text-muted">Browse profiles and someone will wave back!</p>
        <a href="<?php echo e(route('discover.index')); ?>" class="btn btn-primary mx-auto" style="width:fit-content">Browse Profiles</a>
    </div>
    <?php else: ?>
    <div class="row g-3">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $waves; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wave): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $sender = $wave->sender; ?>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm h-100 <?php echo e(!$wave->seen ? 'border-start border-3 border-warning' : ''); ?>">
                <div class="ratio ratio-1x1">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sender->primaryPhoto): ?>
                    <img src="<?php echo e($sender->primaryPhoto->thumbnail_url); ?>" class="card-img-top object-fit-cover" alt="<?php echo e($sender->name); ?>">
                    <?php else: ?>
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center"><i class="bi bi-person-circle display-4 text-muted"></i></div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="card-body p-2">
                    <div class="fw-semibold"><?php echo e($sender->name); ?> <span class="fs-5"><?php echo e($wave->emoji); ?></span></div>
                    <div class="text-muted" style="font-size:.75rem"><?php echo e($wave->created_at->diffForHumans()); ?></div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$wave->seen): ?><span class="badge bg-warning text-dark">New</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="mt-2 d-flex gap-1">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sender && $sender->username): ?>
                        <a href="<?php echo e(route('profile.show', $sender->username)); ?>" class="btn btn-sm btn-outline-primary flex-fill">View</a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        
                        <button class="btn btn-sm btn-warning wave-back-btn" data-user="<?php echo e($sender->id); ?>" title="Wave back 👋">👋</button>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <div class="mt-4"><?php echo e($waves->links()); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Mark all waves as seen
fetch('<?php echo e(route('wave.seen')); ?>', {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
});

// Wave back buttons
document.querySelectorAll('.wave-back-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
        const userId = btn.dataset.user;
        const csrf   = document.querySelector('meta[name="csrf-token"]').content;
        const res    = await fetch(`<?php echo e(url('wave')); ?>/${userId}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' },
            body: JSON.stringify({ emoji: '👋' })
        });
        if (res.ok) {
            btn.textContent = '✅';
            btn.disabled = true;
        }
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\dating\resources\views\waves\received.blade.php ENDPATH**/ ?>