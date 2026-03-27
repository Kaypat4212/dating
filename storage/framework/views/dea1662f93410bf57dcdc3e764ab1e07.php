
<?php $__env->startSection('title', 'Browse Profiles'); ?>

<?php $__env->startPush('styles'); ?>
<style>
/* ── Discover page ──────────────────────────────────────────── */
.discover-filters {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,.07);
    padding: 1.25rem 1.5rem;
    margin-bottom: 1.5rem;
}
.discover-filters .form-control,
.discover-filters .form-select {
    border-radius: 10px;
    font-size: .85rem;
    border-color: #e5e7eb;
    background: #f9fafb;
    transition: border-color .2s, box-shadow .2s;
}
.discover-filters .form-control:focus,
.discover-filters .form-select:focus {
    border-color: #e11d74;
    box-shadow: 0 0 0 3px rgba(225,29,116,.1);
}

/* ── Profile Cards ──────────────────────────────────────────── */
.profile-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}
@media (min-width: 576px)  { .profile-grid { grid-template-columns: repeat(2, 1fr); gap: 1.1rem; } }
@media (min-width: 768px)  { .profile-grid { grid-template-columns: repeat(3, 1fr); gap: 1.25rem; } }
@media (min-width: 992px)  { .profile-grid { grid-template-columns: repeat(4, 1fr); gap: 1.4rem; } }
@media (min-width: 1200px) { .profile-grid { grid-template-columns: repeat(5, 1fr); gap: 1.5rem; } }

/* Staggered entrance */
.profile-card-wrap {
    opacity: 0;
    transform: translateY(22px);
    animation: cardFadeIn .45s ease forwards;
}
@keyframes cardFadeIn {
    to { opacity: 1; transform: translateY(0); }
}

.pc {                           /* profile card */
    position: relative;
    border-radius: 18px;
    overflow: hidden;
    background: #1a1a2e;
    box-shadow: 0 4px 18px rgba(0,0,0,.13);
    aspect-ratio: 3 / 4;
    display: flex;
    flex-direction: column;
    cursor: pointer;
    transition: transform .3s cubic-bezier(.34,1.56,.64,1),
                box-shadow .3s ease;
    -webkit-tap-highlight-color: transparent;
}
.pc:hover {
    transform: translateY(-6px) scale(1.02);
    box-shadow: 0 14px 36px rgba(0,0,0,.22);
}

/* Photo area */
.pc__photo {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center top;
    transition: transform .5s ease;
}
.pc:hover .pc__photo { transform: scale(1.06); }

.pc__avatar-fallback {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    font-size: 3rem;
    font-weight: 700;
    letter-spacing: -.02em;
    user-select: none;
}
.pc__avatar-fallback small {
    font-size: .75rem;
    font-weight: 400;
    opacity: .7;
    margin-top: .3rem;
}

/* Gradient overlay */
.pc__overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(
        to bottom,
        transparent 30%,
        rgba(0,0,0,.12) 55%,
        rgba(0,0,0,.75) 80%,
        rgba(0,0,0,.92) 100%
    );
    pointer-events: none;
}

/* Top badges row */
.pc__badges-top {
    position: absolute;
    top: 10px;
    left: 10px;
    right: 10px;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    pointer-events: none;
    z-index: 4;
}
.pc__badge-online {
    display: flex;
    align-items: center;
    gap: 4px;
    background: rgba(22,163,74,.88);
    color: #fff;
    font-size: .6rem;
    font-weight: 700;
    letter-spacing: .05em;
    padding: 3px 8px;
    border-radius: 20px;
    backdrop-filter: blur(4px);
}
.pc__badge-online::before {
    content: '';
    display: inline-block;
    width: 6px; height: 6px;
    background: #86efac;
    border-radius: 50%;
    animation: pulse-dot 1.5s infinite;
}
@keyframes pulse-dot {
    0%,100% { opacity: 1; }
    50%      { opacity: .4; }
}
.pc__badge-verified {
    width: 26px; height: 26px;
    background: rgba(255,255,255,.92);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 1px 6px rgba(0,0,0,.2);
    font-size: .9rem;
    color: #1d9bf0;
}
.pc__badge-premium {
    display: flex;
    align-items: center;
    gap: 3px;
    background: linear-gradient(135deg, #f59e0b, #ef4444);
    color: #fff;
    font-size: .58rem;
    font-weight: 800;
    letter-spacing: .08em;
    padding: 3px 7px;
    border-radius: 20px;
    text-transform: uppercase;
}

/* Info block at bottom */
.pc__info {
    position: absolute;
    bottom: 0; left: 0; right: 0;
    padding: .9rem 1rem .85rem;
    z-index: 3;
    color: #fff;
}
.pc__name {
    font-size: .95rem;
    font-weight: 700;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    text-shadow: 0 1px 4px rgba(0,0,0,.5);
    margin-bottom: 2px;
}
.pc__meta {
    font-size: .7rem;
    opacity: .85;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 4px;
}
.pc__headline {
    font-size: .68rem;
    opacity: .72;
    font-style: italic;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 6px;
}
.pc__mood {
    display: inline-block;
    background: rgba(255,255,255,.15);
    backdrop-filter: blur(4px);
    border: 1px solid rgba(255,255,255,.2);
    border-radius: 20px;
    padding: 2px 8px;
    font-size: .6rem;
    font-weight: 600;
    margin-bottom: 6px;
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Action buttons that appear on hover */
.pc__actions {
    display: flex;
    gap: 6px;
    position: relative;
    z-index: 5;
    margin-top: 2px;
    opacity: 0;
    transform: translateY(8px);
    transition: opacity .25s ease, transform .25s ease;
}
.pc:hover .pc__actions,
.pc:focus-within .pc__actions {
    opacity: 1;
    transform: translateY(0);
}
/* Always show on touch devices */
@media (hover: none) {
    .pc__actions { opacity: 1; transform: translateY(0); }
}

.pc__action-btn {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    border: none;
    border-radius: 10px;
    padding: 6px 4px;
    font-size: .7rem;
    font-weight: 700;
    cursor: pointer;
    transition: transform .15s ease, filter .15s ease;
    white-space: nowrap;
    backdrop-filter: blur(6px);
}
.pc__action-btn:hover { transform: scale(1.08); filter: brightness(1.1); }
.pc__action-btn:active { transform: scale(.96); }
.pc__action-btn--wave {
    background: rgba(251,191,36,.85);
    color: #78350f;
}
.pc__action-btn--like {
    background: rgba(239,68,68,.85);
    color: #fff;
}
.pc__action-btn--view {
    background: rgba(255,255,255,.22);
    color: #fff;
    border: 1px solid rgba(255,255,255,.3);
}

/* Auto-detect location button */
#detectLocationBtn {
    flex-shrink: 0;
    font-size: .78rem;
    font-weight: 600;
    transition: all .2s;
    white-space: nowrap;
}
#detectLocationBtn .spinner-border {
    width: .85em;
    height: .85em;
    border-width: .12em;
}
#detectLocationBtn.success {
    background: #16a34a !important;
    border-color: #16a34a !important;
    color: #fff !important;
}
#detectLocationBtn.error {
    background: #dc2626 !important;
    border-color: #dc2626 !important;
    color: #fff !important;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-4">

    <?php echo $__env->make('partials.safety-banner', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <form method="GET" action="<?php echo e(route('discover.index')); ?>" class="discover-filters">

        
        <div class="row g-2 align-items-end mb-2">
            <div class="col-12 col-md-5">
                <label class="form-label small fw-semibold mb-1">
                    <i class="bi bi-geo-alt-fill text-danger me-1"></i>City / Location
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($filterCity): ?>
                        <span class="badge bg-primary ms-1" style="font-size:.65rem">Active</span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isPremium): ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($locationLimitReached): ?>
                            <span class="badge bg-danger ms-1" style="font-size:.65rem">
                                <i class="bi bi-lock-fill me-1"></i>Limit reached
                            </span>
                        <?php else: ?>
                            <span class="badge bg-secondary ms-1" style="font-size:.65rem">
                                <?php echo e(max(0, 2 - $locationUses)); ?>/2 trials left
                            </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php else: ?>
                        <span class="badge ms-1" style="background:linear-gradient(135deg,#e11d74,#f97316);font-size:.65rem">
                            <i class="bi bi-star-fill me-1"></i>Premium
                        </span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </label>
                <div class="d-flex gap-1 align-items-center">
                    <input id="cityInput" type="text" name="city" class="form-control form-control-sm flex-grow-1"
                           value="<?php echo e($filterCity); ?>"
                           placeholder="e.g. Lagos"
                           autocomplete="off"
                           <?php if($locationLimitReached): ?> disabled <?php endif; ?>>
                    <button type="button" id="detectLocationBtn"
                            class="btn btn-outline-secondary btn-sm rounded-pill px-2"
                            title="Auto-detect my current location"
                            <?php if($locationLimitReached): ?> disabled <?php endif; ?>>
                        <i class="bi bi-crosshair2 me-1" id="detectIcon"></i><span id="detectLabel">Detect</span>
                    </button>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label small fw-semibold mb-1">Country</label>
                <input id="countryInput" type="text" name="country" class="form-control form-control-sm"
                       value="<?php echo e($filterCountry); ?>"
                       placeholder="e.g. Nigeria — leave blank for any"
                       autocomplete="off"
                       <?php if($locationLimitReached): ?> disabled <?php endif; ?>>
            </div>
            <div class="col-12 col-md-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($locationLimitReached): ?>
                <div class="alert alert-warning py-2 px-3 mb-0 d-flex align-items-center gap-2" style="font-size:.8rem;border-radius:.5rem">
                    <i class="bi bi-lock-fill text-warning fs-6"></i>
                    <div>
                        <strong>Free trial used up.</strong><br>
                        <a href="<?php echo e(route('premium.show')); ?>" class="fw-semibold text-decoration-none"
                           style="color:#e11d74">Upgrade to Premium</a> to browse any city.
                    </div>
                </div>
                <?php else: ?>
                <p class="text-muted small mb-0" style="font-size:.78rem;line-height:1.3">
                    <i class="bi bi-info-circle me-1"></i>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isPremium): ?>
                        Browse any location freely — Premium benefit.
                    <?php else: ?>
                        <?php echo e(max(0, 2 - $locationUses)); ?> free trial(s) to browse other cities.
                        <a href="<?php echo e(route('premium.show')); ?>" class="fw-semibold" style="color:#e11d74">Go Premium</a> for unlimited access.
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('location_limit_reached')): ?>
        <div class="alert alert-warning alert-dismissible fade show py-2 px-3 mb-2" style="font-size:.85rem" role="alert">
            <i class="bi bi-lock-fill me-2"></i>
            <strong>Location filter trial exhausted.</strong>
            Your results are showing your home city. <a href="<?php echo e(route('premium.show')); ?>" class="fw-semibold" style="color:#e11d74">Upgrade to Premium</a> to browse any location.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <div class="row g-2 align-items-end">
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold mb-1">Min Age</label>
                <input type="number" name="min_age" class="form-control form-control-sm"
                       value="<?php echo e(request('min_age', $minAge)); ?>" min="18" max="80">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold mb-1">Max Age</label>
                <input type="number" name="max_age" class="form-control form-control-sm"
                       value="<?php echo e(request('max_age', $maxAge)); ?>" min="18" max="99">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold mb-1">Distance (km)</label>
                <input type="number" name="max_distance_km" class="form-control form-control-sm"
                       value="<?php echo e(request('max_distance_km', $maxKm && $maxKm < 9999 ? $maxKm : '')); ?>"
                       min="5" max="20000" placeholder="Any">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold mb-1">Looking for</label>
                <select name="seeking_gender" class="form-select form-select-sm">
                    <option value="">Any</option>
                    <option value="men"      <?php echo e(request('seeking_gender') === 'men'      ? 'selected' : ''); ?>>Men</option>
                    <option value="women"    <?php echo e(request('seeking_gender') === 'women'    ? 'selected' : ''); ?>>Women</option>
                    <option value="everyone" <?php echo e(request('seeking_gender') === 'everyone' ? 'selected' : ''); ?>>Everyone</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold mb-1">Online now</label>
                <select name="online_only" class="form-select form-select-sm">
                    <option value="">Any</option>
                    <option value="1" <?php echo e(request('online_only') === '1' ? 'selected' : ''); ?>>Online only</option>
                </select>
            </div>
            <div class="col-6 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1 rounded-pill fw-semibold">
                    <i class="bi bi-search me-1"></i>Filter
                </button>
                <a href="<?php echo e(route('discover.index')); ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3" title="Clear filters">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->is_verified): ?>
        <div class="d-flex align-items-center gap-2 mt-3 pt-3 border-top">
            <button type="button" id="verifiedOnlyToggle" onclick="toggleVerifiedOnly(this)"
                    class="btn btn-sm d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill fw-semibold
                           <?php echo e(request('verified_only') ? 'btn-primary' : 'btn-outline-secondary'); ?>"
                    style="font-size:.82rem;transition:all .2s">
                <i class="bi bi-patch-check-fill"
                   style="color:<?php echo e(request('verified_only') ? '#fff' : '#1d9bf0'); ?>;font-size:.95rem"></i>
                Verified Only
                <span class="badge rounded-pill ms-1 <?php echo e(request('verified_only') ? 'bg-white text-primary' : 'bg-secondary'); ?>"
                      style="font-size:.65rem">
                    <?php echo e(request('verified_only') ? 'ON' : 'OFF'); ?>

                </span>
            </button>
            <input type="hidden" name="verified_only" id="verifiedOnlyInput" value="<?php echo e(request('verified_only', 0)); ?>">
            <span class="text-muted small">Show only ID-verified members</span>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </form>

    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-search-heart text-primary fs-5"></i>
                Browse Profiles
                <span class="badge rounded-pill" style="background:linear-gradient(135deg,#e11d74,#f97316);font-size:.72rem">
                    <?php echo e(number_format($users->total())); ?>

                </span>
            </h5>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($users->total() > 0): ?>
            <p class="text-muted small mb-0 mt-1">
                Showing <?php echo e($users->firstItem()); ?>–<?php echo e($users->lastItem()); ?> of <?php echo e(number_format($users->total())); ?> members
            </p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <a href="<?php echo e(route('swipe.deck')); ?>"
           class="btn btn-sm fw-semibold rounded-pill px-3"
           style="background:linear-gradient(135deg,#e11d74,#f97316);color:#fff;border:none;box-shadow:0 2px 10px rgba(225,29,116,.35)">
            <i class="bi bi-fire me-1"></i>Swipe Mode
        </a>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($users->isEmpty()): ?>
    <div class="text-center py-5">
        <div style="font-size:4rem;line-height:1;margin-bottom:1rem">🔭</div>
        <h5 class="fw-bold">No profiles found</h5>
        <p class="text-muted">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($filterCity): ?>
                No members found in <strong><?php echo e($filterCity); ?></strong>.
                Try clearing the city filter or search a different location.
            <?php else: ?>
                Try adjusting your filters or expanding the distance.
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </p>
        <a href="<?php echo e(route('discover.index')); ?>" class="btn btn-primary rounded-pill px-4">Clear Filters</a>
    </div>
    <?php else: ?>
    <div class="profile-grid">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $photo    = $user->primaryPhoto;
            $photoUrl = $photo ? $photo->thumbnail_url : null;
            $initials = collect(explode(' ', $user->name))->map(fn($w) => strtoupper(mb_substr($w,0,1)))->take(2)->implode('');
            $age      = $user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth)->age : null;
            $isOnline = $user->last_active_at && $user->last_active_at->gt(now()->subMinutes(10));
            $delay    = ($i % 12) * 55;
        ?>
        <div class="profile-card-wrap" style="animation-delay: <?php echo e($delay); ?>ms">
            <div class="pc" tabindex="0" role="article" aria-label="<?php echo e($user->name); ?><?php echo e($age ? ', '.$age : ''); ?>">

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($photoUrl): ?>
                    <img src="<?php echo e($photoUrl); ?>"
                         alt="<?php echo e(e($user->name)); ?>"
                         class="pc__photo"
                         loading="lazy"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="pc__avatar-fallback" style="display:none">
                        <?php echo e($initials); ?><small>No photo</small>
                    </div>
                <?php else: ?>
                    <div class="pc__avatar-fallback">
                        <?php echo e($initials); ?><small><?php echo e($user->name); ?></small>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <div class="pc__overlay"></div>

                
                <div class="pc__badges-top">
                    <div class="d-flex flex-column gap-1">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isOnline): ?>
                        <span class="pc__badge-online">Online</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->isPremiumActive()): ?>
                        <span class="pc__badge-premium"><i class="bi bi-star-fill" style="font-size:.55rem"></i>PRO</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->is_verified): ?>
                    <span class="pc__badge-verified" title="ID Verified">
                        <i class="bi bi-patch-check-fill"></i>
                    </span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                
                <div class="pc__info">
                    <div class="pc__name">
                        <?php echo e($user->name); ?><?php echo e($age ? ', '.$age : ''); ?>

                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->profile?->city || isset($user->distance_km)): ?>
                    <div class="pc__meta">
                        <i class="bi bi-geo-alt-fill me-1" style="font-size:.62rem;opacity:.8"></i>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($user->distance_km)): ?><?php echo e(round($user->distance_km)); ?> km · <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php echo e($user->profile?->city); ?>

                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->profile?->headline): ?>
                    <div class="pc__headline">"<?php echo e($user->profile->headline); ?>"</div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->profile?->mood_status): ?>
                    <div class="pc__mood"><?php echo e(Str::limit($user->profile->mood_status, 30)); ?></div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <div class="pc__actions">
                        <button class="pc__action-btn pc__action-btn--wave wave-btn"
                                data-user="<?php echo e($user->id); ?>"
                                title="Wave"
                                onclick="event.preventDefault();event.stopPropagation()">
                            👋 Wave
                        </button>
                        <form method="POST" action="<?php echo e(route('like.store', $user->id)); ?>" style="flex:1;margin:0">
                            <?php echo csrf_field(); ?>
                            <button type="submit"
                                    class="pc__action-btn pc__action-btn--like w-100"
                                    onclick="event.stopPropagation()"
                                    title="Like">
                                ❤️ Like
                            </button>
                        </form>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->username): ?>
                        <a href="<?php echo e(route('profile.show', $user->username)); ?>"
                           class="pc__action-btn pc__action-btn--view text-decoration-none"
                           onclick="event.stopPropagation()"
                           title="View profile">
                            <i class="bi bi-person-fill" style="font-size:.8rem"></i>
                        </a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->username): ?>
                <a href="<?php echo e(route('profile.show', $user->username)); ?>"
                   class="stretched-link"
                   aria-label="View <?php echo e($user->name); ?>'s profile"></a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <div class="mt-5 d-flex justify-content-center">
        <?php echo e($users->withQueryString()->links('pagination::bootstrap-5')); ?>

    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Verified-only toggle
function toggleVerifiedOnly(btn) {
    const input = document.getElementById('verifiedOnlyInput');
    const isOn  = input.value === '1';
    input.value = isOn ? '0' : '1';
    const badge = btn.querySelector('.badge');
    if (isOn) {
        btn.classList.replace('btn-primary', 'btn-outline-secondary');
        badge.classList.replace('bg-white', 'bg-secondary');
        badge.classList.remove('text-primary');
        badge.textContent = 'OFF';
        btn.querySelector('i').style.color = '#1d9bf0';
    } else {
        btn.classList.replace('btn-outline-secondary', 'btn-primary');
        badge.classList.replace('bg-secondary', 'bg-white');
        badge.classList.add('text-primary');
        badge.textContent = 'ON';
        btn.querySelector('i').style.color = '#fff';
    }
    btn.closest('form').submit();
}

// Wave button
document.addEventListener('DOMContentLoaded', () => {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    document.querySelectorAll('.wave-btn').forEach(btn => {
        btn.addEventListener('click', async e => {
            e.preventDefault();
            e.stopPropagation();
            if (btn.classList.contains('sent')) return;
            const userId = btn.dataset.user;
            const res = await fetch(`<?php echo e(url('wave')); ?>/${userId}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' },
                body: JSON.stringify({ emoji: '👋' })
            });
            if (res.ok) {
                btn.textContent = '✅ Sent';
                btn.classList.add('sent');
                btn.disabled = true;
                btn.style.background = 'rgba(34,197,94,.75)';
                btn.style.color = '#fff';
            }
        });
    });

    // Keyboard navigation for cards
    document.querySelectorAll('.pc').forEach(card => {
        card.addEventListener('keydown', e => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                const link = card.querySelector('.stretched-link');
                if (link) link.click();
            }
        });
    });

    // ── Auto-detect location ─────────────────────────────────────
    const detectBtn   = document.getElementById('detectLocationBtn');
    const cityInput   = document.getElementById('cityInput');
    const countryInput = document.getElementById('countryInput');
    const detectIcon  = document.getElementById('detectIcon');
    const detectLabel = document.getElementById('detectLabel');

    function setDetectState(state, msg) {
        detectBtn.disabled = (state === 'loading');
        detectBtn.classList.remove('success', 'error');
        if (state === 'loading') {
            detectIcon.className = '';
            detectLabel.innerHTML = '<span class="spinner-border" role="status"></span> Detecting…';
        } else if (state === 'success') {
            detectBtn.classList.add('success');
            detectIcon.className = 'bi bi-check-lg me-1';
            detectLabel.textContent = msg || 'Detected!';
        } else if (state === 'error') {
            detectBtn.classList.add('error');
            detectBtn.disabled = false;
            detectIcon.className = 'bi bi-exclamation-triangle me-1';
            detectLabel.textContent = msg || 'Failed';
            setTimeout(() => resetDetectBtn(), 3000);
        }
    }

    function resetDetectBtn() {
        detectBtn.disabled = false;
        detectBtn.classList.remove('success', 'error');
        detectIcon.className = 'bi bi-crosshair2 me-1';
        detectLabel.textContent = 'Detect';
    }

    if (detectBtn) {
        detectBtn.addEventListener('click', () => {
            if (!('geolocation' in navigator)) {
                setDetectState('error', 'Not supported');
                return;
            }
            setDetectState('loading');
            navigator.geolocation.getCurrentPosition(
                async (pos) => {
                    const { latitude: lat, longitude: lng } = pos.coords;
                    try {
                        const resp = await fetch(
                            `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`,
                            { headers: { 'Accept-Language': 'en' } }
                        );
                        if (!resp.ok) throw new Error('Geocode failed');
                        const data = await resp.json();
                        const addr = data.address || {};
                        // city → town → village → county → suburb fallback
                        const city = addr.city || addr.town || addr.village || addr.county || addr.suburb || '';
                        const country = addr.country || '';
                        if (cityInput)   cityInput.value   = city;
                        if (countryInput) countryInput.value = country;
                        setDetectState('success', city || 'Detected!');
                        // Auto-submit the filter form after short delay
                        setTimeout(() => detectBtn.closest('form').submit(), 800);
                    } catch {
                        setDetectState('error', 'Geocode failed');
                    }
                },
                (err) => {
                    const msgs = {
                        1: 'Permission denied',
                        2: 'Position unavailable',
                        3: 'Timed out',
                    };
                    setDetectState('error', msgs[err.code] || 'Location error');
                },
                { timeout: 10000, maximumAge: 300000 }
            );
        });
    }

    // Intersection Observer for on-scroll reveal (in addition to CSS stagger)
    if ('IntersectionObserver' in window) {
        const obs = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animationPlayState = 'running';
                }
            });
        }, { threshold: 0.05 });

        document.querySelectorAll('.profile-card-wrap').forEach(el => {
            el.style.animationPlayState = 'paused';
            obs.observe(el);
        });
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\dating\resources\views\discover\index.blade.php ENDPATH**/ ?>