
<?php $__env->startSection('title', 'Browse Profiles'); ?>
<?php $__env->startSection('content'); ?>
<div class="container py-4">

    <?php echo $__env->make('partials.safety-banner', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    
    <form method="GET" action="<?php echo e(route('discover.index')); ?>" class="card border-0 shadow-sm mb-4 p-3">
        <div class="row g-2 align-items-end">
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold">Min Age</label>
                <input type="number" name="min_age" class="form-control form-control-sm" value="<?php echo e(request('min_age', $minAge)); ?>" min="18" max="80">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold">Max Age</label>
                <input type="number" name="max_age" class="form-control form-control-sm" value="<?php echo e(request('max_age', $maxAge)); ?>" min="18" max="99">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold">Distance (km)</label>
                <input type="number" name="max_distance_km" class="form-control form-control-sm" value="<?php echo e(request('max_distance_km', $maxKm < 9999 ? $maxKm : '')); ?>" min="5" max="20000" placeholder="Any distance">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold">Looking for</label>
                <select name="seeking_gender" class="form-select form-select-sm">
                    <option value="">Any</option>
                    <option value="men" <?php echo e(request('seeking_gender') === 'men' ? 'selected' : ''); ?>>Men</option>
                    <option value="women" <?php echo e(request('seeking_gender') === 'women' ? 'selected' : ''); ?>>Women</option>
                    <option value="everyone" <?php echo e(request('seeking_gender') === 'everyone' ? 'selected' : ''); ?>>Everyone</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold">Online now</label>
                <select name="online_only" class="form-select form-select-sm">
                    <option value="">Any</option>
                    <option value="1" <?php echo e(request('online_only') === '1' ? 'selected' : ''); ?>>Online only</option>
                </select>
            </div>
            <div class="col-6 col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1"><i class="bi bi-search me-1"></i>Filter</button>
                <a href="<?php echo e(route('discover.index')); ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x-circle"></i></a>
            </div>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->is_verified): ?>
        <div class="d-flex align-items-center gap-2 mt-3 pt-3 border-top" style="border-color:rgba(0,0,0,.07)!important">
            <button type="button"
                    id="verifiedOnlyToggle"
                    onclick="toggleVerifiedOnly(this)"
                    class="btn btn-sm d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill fw-semibold
                           <?php echo e(request('verified_only') ? 'btn-primary' : 'btn-outline-secondary'); ?>"
                    style="font-size:.82rem;transition:all .2s">
                <i class="bi bi-patch-check-fill" style="color:<?php echo e(request('verified_only') ? '#fff' : '#1d9bf0'); ?>;font-size:.95rem"></i>
                Verified Only
                <span class="badge rounded-pill ms-1 <?php echo e(request('verified_only') ? 'bg-white text-primary' : 'bg-secondary'); ?>" style="font-size:.65rem">
                    <?php echo e(request('verified_only') ? 'ON' : 'OFF'); ?>

                </span>
            </button>
            <input type="hidden" name="verified_only" id="verifiedOnlyInput" value="<?php echo e(request('verified_only', 0)); ?>">
            <span class="text-muted small">Show only ID-verified members</span>
        </div>
        <script>
        function toggleVerifiedOnly(btn) {
            const input = document.getElementById('verifiedOnlyInput');
            const isOn  = input.value === '1';
            if (isOn) {
                input.value = '0';
                btn.className = btn.className.replace('btn-primary','btn-outline-secondary');
                btn.querySelector('.badge').className = btn.querySelector('.badge').className.replace('bg-white text-primary','bg-secondary');
                btn.querySelector('.badge').textContent = 'OFF';
                btn.querySelector('i').style.color = '#1d9bf0';
            } else {
                input.value = '1';
                btn.className = btn.className.replace('btn-outline-secondary','btn-primary');
                btn.querySelector('.badge').className = btn.querySelector('.badge').className.replace('bg-secondary','bg-white text-primary');
                btn.querySelector('.badge').textContent = 'ON';
                btn.querySelector('i').style.color = '#fff';
            }
            btn.closest('form').submit();
        }
        </script>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </form>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0"><i class="bi bi-search-heart me-2 text-primary"></i>Browse Profiles <span class="badge bg-primary ms-2"><?php echo e($users->total()); ?></span></h5>
        <a href="<?php echo e(route('swipe.deck')); ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-fire me-1"></i>Swipe Mode</a>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($users->isEmpty()): ?>
    <div class="text-center py-5">
        <div class="display-1 mb-3">🔭</div>
        <h5>No profiles found</h5>
        <p class="text-muted">Try adjusting your filters or expanding the distance.</p>
    </div>
    <?php else: ?>
    <div class="row g-3">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm h-100 profile-card position-relative">
                <div class="ratio ratio-4x5 overflow-hidden">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->primaryPhoto): ?>
                    <img src="<?php echo e($user->primaryPhoto->url); ?>" class="object-fit-cover w-100 h-100" alt="<?php echo e($user->name); ?>">
                    <?php else: ?>
                    <div class="bg-light d-flex align-items-center justify-content-center"><i class="bi bi-person-circle display-3 text-muted"></i></div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->last_active_at && $user->last_active_at->gt(now()->subMinutes(10))): ?>
                <span class="position-absolute top-0 end-0 m-2 badge rounded-pill bg-success d-flex align-items-center gap-1" style="font-size:.65rem">
                    <span style="width:6px;height:6px;background:#fff;border-radius:50%;display:inline-block"></span>Online
                </span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->is_verified): ?>
                <span class="position-absolute top-0 start-0 m-2" title="ID Verified"
                      style="width:26px;height:26px;background:rgba(255,255,255,.92);border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 1px 4px rgba(0,0,0,.18)">
                    <i class="bi bi-patch-check-fill" style="color:#1d9bf0;font-size:.9rem"></i>
                </span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <div class="card-body p-2">
                    <div class="d-flex align-items-center gap-1 fw-semibold lh-sm">
                        <span class="text-truncate"><?php echo e($user->name); ?><?php echo e($user->date_of_birth ? ', ' . \Carbon\Carbon::parse($user->date_of_birth)->age : ''); ?></span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->is_verified): ?>
                        <span title="ID Verified" class="flex-shrink-0" style="line-height:1">
                            <i class="bi bi-patch-check-fill" style="color:#1d9bf0;font-size:.9rem"></i>
                        </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->isPremiumActive()): ?>
                        <span title="Premium" class="flex-shrink-0" style="line-height:1">
                            <i class="bi bi-star-fill" style="color:#f59e0b;font-size:.75rem"></i>
                        </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="text-muted" style="font-size:.72rem">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($user->distance_km)): ?>
                        <i class="bi bi-geo-alt me-1"></i><?php echo e(round($user->distance_km)); ?> km
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->profile?->city): ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($user->distance_km)): ?> · <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php echo e($user->profile->city); ?>

                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->profile?->headline): ?>
                    <p class="mb-0 mt-1 text-truncate" style="font-size:.72rem;color:var(--bs-secondary-color)"><?php echo e($user->profile->headline); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->profile?->mood_status): ?>
                    <div class="mt-1"><span class="badge bg-warning-subtle text-warning-emphasis rounded-pill" style="font-size:.65rem;font-weight:500"><?php echo e(Str::limit($user->profile->mood_status, 28)); ?></span></div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="d-flex gap-1 mt-2" style="position:relative;z-index:5">
                        <button class="btn btn-outline-warning btn-sm wave-btn px-2 py-0" data-user="<?php echo e($user->id); ?>" title="Wave 👋" style="font-size:.8rem">👋</button>
                        <form method="POST" action="<?php echo e(route('like.store', $user->id)); ?>" style="margin:0">
                            <?php echo csrf_field(); ?>
                            <button class="btn btn-outline-danger btn-sm px-2 py-0" style="font-size:.8rem">❤️</button>
                        </form>
                    </div>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->username): ?>
                <a href="<?php echo e(route('profile.show', $user->username)); ?>" class="stretched-link"></a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <div class="mt-4 d-flex justify-content-center">
        <?php echo e($users->withQueryString()->links('pagination::bootstrap-5')); ?>

    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.querySelectorAll('.wave-btn').forEach(btn => {
    btn.addEventListener('click', async e => {
        e.preventDefault(); e.stopPropagation();
        const userId = btn.dataset.user;
        const csrf   = document.querySelector('meta[name="csrf-token"]').content;
        const res = await fetch(`<?php echo e(url('wave')); ?>/${userId}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' },
            body: JSON.stringify({ emoji: '👋' })
        });
        if (res.ok) { btn.textContent = '✅'; btn.disabled = true; }
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\dating\resources\views\discover\index.blade.php ENDPATH**/ ?>