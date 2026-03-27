
<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="row g-4">
        
        <div class="col-12">
            <h4 class="fw-bold mb-0">Welcome back, <?php echo e(auth()->user()->name); ?>! 👋</h4>
            <p class="text-muted small">Here is what is happening with your profile today</p>
        </div>

        
        <div class="col-12 d-none" id="pwaInstallNudge">
            <div class="alert alert-primary d-flex align-items-center justify-content-between flex-wrap gap-2 mb-0" role="alert">
                <div>
                    <i class="bi bi-phone me-2 fs-5"></i>
                    <strong>Get the App!</strong>
                    <span class="ms-1 d-none d-sm-inline">Install <?php echo e(\App\Models\SiteSetting::get('site_name', config('app.name'))); ?> on your device for a faster, richer experience.</span>
                </div>
                <div class="d-flex gap-2 flex-shrink-0">
                    <a href="<?php echo e(route('pwa.install')); ?>" class="btn btn-primary btn-sm">
                        <i class="bi bi-download me-1"></i>Install Now
                    </a>
                    <button type="button" class="btn-close" onclick="this.closest('#pwaInstallNudge').classList.add('d-none');localStorage.setItem('pwaNudgeDismissed','1');" aria-label="Dismiss"></button>
                </div>
            </div>
        </div>

        
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-end">
                <a href="<?php echo e(route('pwa.install')); ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-phone me-1"></i>Get the App
                </a>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 text-center p-3">
                <div class="display-5 fw-bold text-primary"><?php echo e($stats['matchCount']); ?></div>
                <div class="text-muted small"><i class="bi bi-hearts me-1"></i>Total Matches</div>
                <a href="<?php echo e(route('matches.index')); ?>" class="stretched-link"></a>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 text-center p-3">
                <div class="display-5 fw-bold text-danger"><?php echo e($stats['newLikes']); ?></div>
                <div class="text-muted small"><i class="bi bi-heart me-1"></i>New Likes</div>
                <a href="<?php echo e(route('like.who-liked-me')); ?>" class="stretched-link"></a>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 text-center p-3">
                <div class="display-5 fw-bold text-info"><?php echo e($stats['recentViews']); ?></div>
                <div class="text-muted small"><i class="bi bi-eye me-1"></i>Profile Views</div>
                <a href="<?php echo e(route('profile.who-viewed')); ?>" class="stretched-link"></a>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 text-center p-3">
                <div class="display-5 fw-bold text-success"><?php echo e($stats['unreadMessages']); ?></div>
                <div class="text-muted small"><i class="bi bi-chat-heart me-1"></i>Unread Messages</div>
                <a href="<?php echo e(route('conversations.index')); ?>" class="stretched-link"></a>
            </div>
        </div>

        
        <div class="col-12">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex flex-wrap gap-2">
                    <a href="<?php echo e(route('swipe.deck')); ?>" class="btn btn-primary"><i class="bi bi-fire me-2"></i>Swipe Profiles</a>
                    <a href="<?php echo e(route('discover.index')); ?>" class="btn btn-outline-primary"><i class="bi bi-search-heart me-2"></i>Browse All</a>
                    <a href="<?php echo e(route('matches.index')); ?>" class="btn btn-outline-secondary"><i class="bi bi-hearts me-2"></i>My Matches</a>
                    <a href="<?php echo e(route('wave.received')); ?>" class="btn btn-outline-info position-relative"><i class="bi bi-hand-wave me-2"></i>Waves</a>
                    <a href="<?php echo e(route('stories.index')); ?>" class="btn btn-outline-warning"><i class="bi bi-camera-video me-2"></i>Stories</a>
                    <a href="<?php echo e(route('wallet.index')); ?>" class="btn btn-outline-dark"><i class="bi bi-wallet2 me-2"></i>My Wallet</a>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->isPremiumActive()): ?>
                        <?php $activeBoost = auth()->user()->activeBoost(); ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeBoost): ?>
                            <form method="POST" action="<?php echo e(route('boost.destroy')); ?>" class="d-inline"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button class="btn btn-success" onclick="return confirm('Cancel your boost?')">
                                    <i class="bi bi-rocket-takeoff-fill me-1"></i>Boosted
                                    <span class="badge bg-light text-success ms-1"><?php echo e($activeBoost->ends_at->diffForHumans()); ?></span>
                                </button>
                            </form>
                        <?php else: ?>
                            <form method="POST" action="<?php echo e(route('boost.store')); ?>" class="d-inline"><?php echo csrf_field(); ?>
                                <button class="btn btn-outline-success"><i class="bi bi-rocket-takeoff me-2"></i>Boost Profile</button>
                            </form>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php else: ?>
                    <a href="<?php echo e(route('premium.show')); ?>" class="btn btn-warning fw-bold"><i class="bi bi-star-fill me-2"></i>Upgrade to Premium</a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm h-100" id="dailyMatchCard">
                <div class="card-body text-center p-4">
                    <h5 class="fw-bold mb-1"><i class="bi bi-calendar-heart text-danger me-2"></i>Today's Match</h5>
                    <p class="text-muted small mb-3">A special pick just for you — refreshes daily</p>
                    <div id="dailyMatchContent" class="py-2">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-1"><i class="bi bi-emoji-smile text-warning me-2"></i>Your Mood Status</h5>
                    <p class="text-muted small mb-3">Let others know how you're feeling</p>
                    <?php $profile = auth()->user()->profile; ?>
                    <form method="POST" action="<?php echo e(route('profile.update')); ?>" class="d-flex gap-2">
                        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                        <input type="text" name="mood_status" class="form-control"
                               placeholder="e.g. Open to dating 💕"
                               value="<?php echo e(old('mood_status', $profile->mood_status ?? '')); ?>"
                               maxlength="80">
                        <button class="btn btn-primary px-3" type="submit"><i class="bi bi-check-lg"></i></button>
                    </form>
                    <div class="mt-2 d-flex flex-wrap gap-1">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['Open to dating 💕','Feeling adventurous 🌍','Looking for something real 💎','Just here to vibe ✨','Recently single 🦋']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mood): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button type="button" class="badge bg-secondary text-decoration-none mood-preset border-0"
                                data-mood="<?php echo e($mood); ?>" style="cursor:pointer;font-size:.75rem"><?php echo e($mood); ?></button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($recentMatches->isNotEmpty()): ?>
        <div class="col-12">
            <h5 class="fw-bold mb-3"><i class="bi bi-hearts text-danger me-2"></i>Recent Matches</h5>
            <div class="row g-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $recentMatches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $match): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $other = $match->getOtherUser(auth()->id()) ?>
                <div class="col-6 col-md-3 col-lg-2">
                    <div class="card border-0 shadow-sm h-100 profile-card">
                        <div class="ratio ratio-1x1">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($other->primaryPhoto): ?>
                            <img src="<?php echo e($other->primaryPhoto->url); ?>" class="card-img-top object-fit-cover" alt="<?php echo e($other->name); ?>">
                            <?php else: ?>
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center"><i class="bi bi-person-circle display-4 text-muted"></i></div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="card-body p-2 text-center">
                            <div class="fw-semibold small"><?php echo e($other->name); ?></div>
                            <div class="text-muted" style="font-size:.7rem"><?php echo e($other->age); ?> yrs</div>
                        </div>
                        <a href="<?php echo e($match->conversation ? route('conversations.show', $match->conversation->id) : '#'); ?>" class="stretched-link"></a>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <?php else: ?>
        <div class="col-12">
            <div class="card border-0 shadow-sm text-center p-5">
                <div class="display-1 mb-3">💔</div>
                <h5>No matches yet</h5>
                <p class="text-muted">Start swiping to find your first match!</p>
                <a href="<?php echo e(route('swipe.deck')); ?>" class="btn btn-primary mx-auto" style="width:fit-content"><i class="bi bi-fire me-2"></i>Start Swiping</a>
            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Daily match loader
(function () {
    const el = document.getElementById('dailyMatchContent');
    const noMatchHtml = `
        <div class="py-2">
            <div class="display-6 mb-2">💔</div>
            <p class="text-muted mb-1 fw-semibold">Sorry, no match available today.</p>
            <p class="text-muted small">Check back tomorrow — new suggestions refresh daily!</p>
            <a href="<?php echo e(route('discover.index')); ?>" class="btn btn-sm btn-outline-primary mt-1">
                <i class="bi bi-search me-1"></i>Browse Profiles
            </a>
        </div>`;

    fetch('<?php echo e(route('daily.match')); ?>')
        .then(function (r) {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        })
        .then(function (data) {
            if (!data.match) {
                el.innerHTML = noMatchHtml;
                return;
            }
            const m = data.match;
            const photo = m.photo
                ? `<img src="${m.photo}" class="rounded-circle object-fit-cover mb-2" width="80" height="80" alt="${m.name}">`
                : `<div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-2" style="width:80px;height:80px"><i class="bi bi-person-circle fs-2 text-muted"></i></div>`;

            const locationParts = [m.city, m.state || m.country].filter(Boolean);
            const locationLine  = locationParts.length ? locationParts.join(', ') : '';

            el.innerHTML = `
                ${photo}
                <div class="fw-semibold">${m.name}, ${m.age}</div>
                ${locationLine ? `<div class="text-muted small"><i class="bi bi-geo-alt-fill text-danger me-1"></i>${locationLine}</div>` : ''}
                <div class="text-muted small fst-italic mt-1">${m.headline || ''}</div>
                <a href="<?php echo e(url('/profile')); ?>/${m.username}" class="btn btn-sm btn-primary mt-2">View Profile</a>
            `;
        })
        .catch(function () {
            el.innerHTML = noMatchHtml;
        });
})();

// Mood preset quick-fill
document.querySelectorAll('.mood-preset').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelector('input[name="mood_status"]').value = btn.dataset.mood;
    });
});

// Suppress PWA nudge if user already dismissed it
if (localStorage.getItem('pwaNudgeDismissed') === '1' ||
    window.matchMedia('(display-mode: standalone)').matches) {
    var n = document.getElementById('pwaInstallNudge');
    if (n) n.classList.add('d-none');
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\dating\resources\views\dashboard.blade.php ENDPATH**/ ?>