
<?php $__env->startSection('title', 'Who Liked You'); ?>

<?php $__env->startPush('head'); ?>
<style>
    .like-hero {
        background: linear-gradient(135deg, #ff6b9d 0%, #c44ee0 50%, #7b2ff7 100%);
        border-radius: 1.25rem;
        color: #fff;
        padding: 2rem 1.5rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }
    .like-hero::before {
        content: '❤️';
        font-size: 8rem;
        position: absolute;
        right: -1rem;
        top: -1.5rem;
        opacity: .12;
        pointer-events: none;
    }
    .liker-card {
        border: none;
        border-radius: 1rem;
        overflow: hidden;
        transition: transform .2s, box-shadow .2s;
        box-shadow: 0 2px 12px rgba(0,0,0,.08);
    }
    .liker-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 28px rgba(196,78,224,.2);
    }
    .liker-card .photo-wrap {
        position: relative;
        aspect-ratio: 1/1;
        overflow: hidden;
        background: #f0f0f0;
    }
    .liker-card .photo-wrap img {
        width: 100%; height: 100%;
        object-fit: cover;
        transition: transform .35s;
    }
    .liker-card:hover .photo-wrap img {
        transform: scale(1.06);
    }
    .liker-card .photo-wrap .super-badge {
        position: absolute;
        top: .5rem; left: .5rem;
        background: linear-gradient(135deg,#ffd700,#ff9900);
        color: #fff;
        font-size: .65rem;
        font-weight: 700;
        padding: .22rem .55rem;
        border-radius: 2rem;
        letter-spacing: .04em;
        box-shadow: 0 2px 6px rgba(0,0,0,.2);
    }
    .liker-card .photo-wrap .time-badge {
        position: absolute;
        bottom: .5rem; right: .5rem;
        background: rgba(0,0,0,.55);
        color: #fff;
        font-size: .65rem;
        padding: .2rem .45rem;
        border-radius: .75rem;
        backdrop-filter: blur(4px);
    }
    .blur-premium {
        filter: blur(14px);
        transform: scale(1.05);
        pointer-events: none;
    }
    .premium-overlay {
        position: absolute; inset: 0;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        gap: .5rem;
        background: rgba(123,47,247,.18);
        backdrop-filter: blur(2px);
    }
    .empty-state { padding: 4rem 1rem; text-align: center; }
    .empty-icon { font-size: 4.5rem; margin-bottom: 1rem; animation: heartbeat 2s infinite; }
    @keyframes heartbeat {
        0%,100% { transform: scale(1); }
        15% { transform: scale(1.15); }
        30% { transform: scale(1); }
        45% { transform: scale(1.1); }
        60% { transform: scale(1); }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-4">

    
    <div class="like-hero">
        <h3 class="fw-bold mb-1">
            <i class="bi bi-heart-fill me-2"></i>Who Liked You
        </h3>
        <p class="mb-0 opacity-75 fs-6">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($likers->total() > 0): ?>
                <strong><?php echo e($likers->total()); ?></strong> <?php echo e(Str::plural('person', $likers->total())); ?> have expressed interest in you.
            <?php else: ?>
                Nobody yet — keep discovering new people!
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </p>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isPremium): ?>
        <a href="<?php echo e(route('premium.show')); ?>"
           class="btn btn-warning btn-sm fw-semibold mt-3 rounded-pill px-4 shadow-sm">
            <i class="bi bi-star-fill me-1"></i>Upgrade to see who liked you
        </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($likers->isEmpty()): ?>
        
        <div class="empty-state">
            <div class="empty-icon">💝</div>
            <h5 class="fw-semibold mb-1">No likes yet</h5>
            <p class="text-muted mb-4">Complete your profile and keep swiping — someone special will find you!</p>
            <a href="<?php echo e(route('discover.index')); ?>" class="btn btn-primary rounded-pill px-5">
                <i class="bi bi-search-heart me-1"></i>Discover People
            </a>
        </div>
    <?php else: ?>
        <div class="row g-3">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $likers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $like): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $liker = $like->sender ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$liker || !$liker->username): ?> <?php continue; ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card liker-card h-100">

                        
                        <div class="photo-wrap">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($liker->primaryPhoto): ?>
                                <img
                                    src="<?php echo e($liker->primaryPhoto->thumbnail_url); ?>"
                                    alt="<?php echo e($isPremium ? $liker->name : 'Hidden'); ?>"
                                    class="<?php echo e($isPremium ? '' : 'blur-premium'); ?>"
                                    loading="lazy">
                            <?php else: ?>
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light" style="aspect-ratio:1/1">
                                    <i class="bi bi-person-circle display-4 text-muted"></i>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($like->is_super_like): ?>
                                <span class="super-badge"><i class="bi bi-star-fill me-1"></i>Super Like</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isPremium): ?>
                                <span class="time-badge"><?php echo e($like->created_at->diffForHumans()); ?></span>
                            <?php else: ?>
                                <div class="premium-overlay">
                                    <i class="bi bi-lock-fill text-white fs-3"></i>
                                    <a href="<?php echo e(route('premium.show')); ?>"
                                       class="btn btn-warning btn-sm fw-bold rounded-pill px-3"
                                       style="font-size:.7rem">Unlock</a>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        
                        <div class="card-body p-2 pb-1">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isPremium): ?>
                                <div class="fw-semibold text-truncate" style="font-size:.9rem">
                                    <?php echo e($liker->name); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($liker->age): ?>, <?php echo e($liker->age); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($liker->profile && $liker->profile->city): ?>
                                <div class="text-muted" style="font-size:.7rem">
                                    <i class="bi bi-geo-alt"></i> <?php echo e($liker->profile->city); ?>

                                </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php else: ?>
                                <div class="fw-semibold text-muted text-truncate" style="font-size:.9rem">
                                    ✨ Premium member
                                </div>
                                <div class="text-muted" style="font-size:.7rem">Upgrade to reveal</div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        
                        <div class="card-footer bg-transparent p-2 pt-0 d-flex gap-1">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isPremium): ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($liker->username): ?>
                                <a href="<?php echo e(route('profile.show', $liker->username)); ?>"
                                   class="btn btn-outline-secondary btn-sm flex-fill">
                                    <i class="bi bi-person"></i>
                                </a>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <form method="POST" action="<?php echo e(route('like.store', $liker->id)); ?>" class="flex-fill">
                                    <?php echo csrf_field(); ?>
                                    <button class="btn btn-primary btn-sm w-100">
                                        <i class="bi bi-heart-fill"></i>
                                    </button>
                                </form>
                            <?php else: ?>
                                <a href="<?php echo e(route('premium.show')); ?>"
                                   class="btn btn-warning btn-sm w-100 fw-bold">
                                    <i class="bi bi-star-fill me-1"></i>See Who
                                </a>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($likers->hasPages()): ?>
        <div class="mt-4 d-flex justify-content-center">
            <?php echo e($likers->links('pagination::bootstrap-5')); ?>

        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\dating\resources\views\like\who-liked-me.blade.php ENDPATH**/ ?>