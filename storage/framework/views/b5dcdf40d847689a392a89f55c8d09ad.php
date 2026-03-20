
<?php $__env->startSection('title', isset($is_edit) ? 'Location & Match Preferences' : 'Setup — Step 4 of 5'); ?>

<?php $__env->startPush('head'); ?>
<style>
/* ── Gender pill radio buttons ───────────────────────────────── */
.gender-pill input[type="radio"] { display: none; }
.gender-pill label {
    cursor: pointer;
    border: 2px solid var(--bs-border-color);
    border-radius: 0.5rem;
    padding: .5rem 1.25rem;
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    font-weight: 600;
    transition: background .15s, color .15s, border-color .15s;
    user-select: none;
}
.gender-pill input[type="radio"]:checked + label {
    background: var(--bs-primary);
    color: #fff;
    border-color: var(--bs-primary);
}

/* ── Dual range slider (age) ─────────────────────────────────── */
.dual-range-wrap {
    position: relative;
    height: 44px;
}
.dual-range-wrap input[type=range] {
    position: absolute;
    width: 100%;
    height: 4px;
    background: transparent;
    pointer-events: none;
    -webkit-appearance: none;
    appearance: none;
    outline: none;
    margin: 0;
    top: 50%;
    transform: translateY(-50%);
}
.dual-range-wrap input[type=range]::-webkit-slider-thumb {
    pointer-events: all;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: var(--bs-primary);
    border: 3px solid var(--bs-body-bg, #fff);
    box-shadow: 0 1px 4px rgba(0,0,0,.22);
    -webkit-appearance: none;
    cursor: pointer;
}
.dual-range-wrap input[type=range]::-moz-range-thumb {
    pointer-events: all;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: var(--bs-primary);
    border: 3px solid var(--bs-body-bg, #fff);
    box-shadow: 0 1px 4px rgba(0,0,0,.22);
    cursor: pointer;
}
.range-track-bg {
    position: absolute;
    height: 4px;
    border-radius: 4px;
    background: var(--bs-border-color, #dee2e6);
    width: 100%;
    top: 50%;
    transform: translateY(-50%);
    z-index: 0;
}
.range-track-fill {
    position: absolute;
    height: 4px;
    border-radius: 4px;
    background: var(--bs-primary);
    top: 50%;
    transform: translateY(-50%);
    z-index: 1;
    pointer-events: none;
}

/* ── Body type chips ─────────────────────────────────────────── */
.body-chip input[type="checkbox"] { display: none; }
.body-chip label {
    cursor: pointer;
    border: 2px solid var(--bs-border-color);
    border-radius: 999px;
    padding: .3rem .9rem;
    font-size: .85rem;
    font-weight: 600;
    color: var(--bs-body-color);
    background: transparent;
    display: inline-flex;
    align-items: center;
    transition: background .15s, color .15s, border-color .15s;
    user-select: none;
}
.body-chip input[type="checkbox"]:checked + label {
    background: var(--bs-primary);
    color: #fff;
    border-color: var(--bs-primary);
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-5" style="max-width:640px">

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show mb-4">
        <i class="bi bi-check-circle me-2"></i><?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!isset($is_edit)): ?>
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="small fw-semibold text-muted">Step <?php echo e($step); ?> of <?php echo e($total); ?></span>
            <span class="small text-muted"><?php echo e(round(($step/$total)*100)); ?>% complete</span>
        </div>
        <div class="progress" style="height:6px;border-radius:10px">
            <div class="progress-bar bg-primary" style="width:<?php echo e(round(($step/$total)*100)); ?>%"></div>
        </div>
    </div>
    <?php else: ?>
    <div class="mb-4">
        <h4 class="fw-bold mb-1">Location &amp; Match Preferences</h4>
        <p class="text-muted mb-0">Set where you are and who you'd like to meet.</p>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php
        $isPremium       = $is_premium ?? false;
        $locationUpdates = $location_updates ?? 0;
        $locationLocked  = ! $isPremium && $locationUpdates >= 2;
        $onbCountry      = old('country', $profile?->country ?? '');
        $onbState        = old('state',   $profile?->state   ?? '');
        $pref            = $preference ?? new \App\Models\UserPreference();
    ?>

    <form method="POST" action="<?php echo e(isset($is_edit) ? route('preferences.update') : route('setup.store', ['step' => 4])); ?>">
        <?php echo csrf_field(); ?>

        
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">

                
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:42px;height:42px;background:rgba(var(--bs-primary-rgb),.1);color:var(--bs-primary)">
                        <i class="bi bi-geo-alt-fill fs-5"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="fw-bold mb-0">Your Location</h5>
                        <p class="text-muted small mb-0">Base point for distance matching.</p>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($locationLocked): ?>
                        <span class="badge bg-warning text-dark"><i class="bi bi-lock-fill me-1"></i>Locked</span>
                    <?php elseif(!$isPremium): ?>
                        <span class="badge bg-light text-muted border small"><?php echo e(2 - $locationUpdates); ?> update<?php echo e(2 - $locationUpdates === 1 ? '' : 's'); ?> left</span>
                    <?php else: ?>
                        <span class="badge border border-success-subtle text-success bg-success-subtle"><i class="bi bi-infinity me-1"></i>Premium</span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['location'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="alert alert-danger rounded-3 py-2 mb-3 small">
                    <i class="bi bi-lock-fill me-2"></i><?php echo e($message); ?>

                    <a href="<?php echo e(route('premium.show')); ?>" class="alert-link fw-semibold ms-1">Upgrade →</a>
                </div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($locationLocked): ?>
                <div class="alert alert-warning rounded-3 d-flex align-items-center gap-3 mb-3">
                    <i class="bi bi-lock-fill fs-5 text-warning flex-shrink-0"></i>
                    <div class="small">
                        <strong>Location updates used (2/2).</strong>
                        <a href="<?php echo e(route('premium.show')); ?>" class="fw-semibold text-warning-emphasis ms-1">⭐ Go Premium</a>
                        for unlimited updates.
                    </div>
                </div>
                <?php elseif(!$isPremium && $locationUpdates === 1): ?>
                <div class="alert alert-info rounded-3 py-2 small mb-3">
                    <i class="bi bi-info-circle me-1"></i>
                    <strong>1 of 2 free location updates used.</strong>
                    <a href="<?php echo e(route('premium.show')); ?>" class="fw-semibold ms-1">⭐ Go Premium</a> for unlimited updates.
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="row g-3">

                    
                    <div class="col-12">
                        <label for="onbCountry" class="form-label fw-semibold small text-uppercase text-muted" style="letter-spacing:.05em">Country</label>
                        <select name="country" id="onbCountry"
                                class="form-select <?php $__errorArgs = ['country'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                <?php echo e($locationLocked ? 'disabled' : ''); ?>>
                            <option value="">Select country…</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = \App\Helpers\CountryHelper::list(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cName => $cCode): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($cName); ?>" <?php echo e($onbCountry === $cName ? 'selected' : ''); ?>><?php echo e($cName); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($locationLocked): ?>
                            <input type="hidden" name="country" value="<?php echo e($profile?->country ?? ''); ?>">
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['country'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    
                    <div class="col-md-6">
                        <label for="onbStateSelect" class="form-label fw-semibold small text-uppercase text-muted" style="letter-spacing:.05em">State / Province</label>
                        <select name="state" id="onbStateSelect"
                                class="form-select <?php $__errorArgs = ['state'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                <?php echo e($locationLocked ? 'disabled' : ''); ?>>
                            <option value="">Select state…</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = \App\Helpers\StateHelper::forCountry($onbCountry); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($s); ?>" <?php echo e($onbState === $s ? 'selected' : ''); ?>><?php echo e($s); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                        <input type="text" name="state" id="onbStateText"
                               class="form-control mt-1 <?php $__errorArgs = ['state'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                      <?php echo e(\App\Helpers\StateHelper::hasStates($onbCountry) ? 'd-none' : ''); ?>"
                               placeholder="State or region"
                               value="<?php echo e($onbState); ?>"
                               <?php echo e($locationLocked ? 'readonly' : ''); ?>>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($locationLocked): ?>
                            <input type="hidden" name="state" value="<?php echo e($onbState); ?>">
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['state'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    
                    <div class="col-md-6">
                        <label for="city" class="form-label fw-semibold small text-uppercase text-muted" style="letter-spacing:.05em">City</label>
                        <input type="text" name="city" id="city"
                               class="form-control <?php $__errorArgs = ['city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               placeholder="Your city"
                               value="<?php echo e(old('city', $profile?->city ?? '')); ?>"
                               <?php echo e($locationLocked ? 'readonly' : ''); ?>>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    
                    <div class="col-12">
                        <input type="hidden" name="latitude"  id="latitude"  value="<?php echo e(old('latitude',  $profile?->latitude  ?? '')); ?>">
                        <input type="hidden" name="longitude" id="longitude" value="<?php echo e(old('longitude', $profile?->longitude ?? '')); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $locationLocked): ?>
                        <button type="button" id="detect-location"
                                class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-2">
                            <i class="bi bi-crosshair2"></i> Auto-detect my location
                        </button>
                        <small id="geo-status" class="d-block mt-1"></small>
                        <?php else: ?>
                        <a href="<?php echo e(route('premium.show')); ?>" class="btn btn-sm btn-warning fw-semibold">
                            <i class="bi bi-star-fill me-1"></i> Unlock location updates
                        </a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                </div>
            </div>
        </div>

        
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">

                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:42px;height:42px;background:rgba(var(--bs-danger-rgb),.1);color:var(--bs-danger)">
                        <i class="bi bi-hearts fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">Who You're Looking For</h5>
                        <p class="text-muted small mb-0">We'll tailor your matches to fit these.</p>
                    </div>
                </div>

                
                <div class="mb-4">
                    <label class="form-label fw-semibold mb-2">Interested in</label>
                    <div class="d-flex gap-2 flex-wrap">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['male' => ['Men','bi-gender-male'], 'female' => ['Women','bi-gender-female'], 'everyone' => ['Everyone','bi-people-fill']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gv => [$gl, $gi]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="gender-pill">
                            <input type="radio" name="seeking_gender" id="sg_<?php echo e($gv); ?>" value="<?php echo e($gv); ?>"
                                   <?php echo e(old('seeking_gender', $pref->seeking_gender ?? 'everyone') === $gv ? 'checked' : ''); ?>>
                            <label for="sg_<?php echo e($gv); ?>">
                                <i class="bi <?php echo e($gi); ?>"></i> <?php echo e($gl); ?>

                            </label>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['seeking_gender'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <hr class="my-4">

                
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label fw-semibold mb-0">Age Range</label>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-bold px-3 py-1"
                              id="age-badge">
                            <?php echo e(old('min_age', $pref->min_age ?? 18)); ?> – <?php echo e(old('max_age', $pref->max_age ?? 50)); ?>

                        </span>
                    </div>
                    <div class="dual-range-wrap mb-1">
                        <div class="range-track-bg"></div>
                        <div class="range-track-fill" id="age-fill"></div>
                        <input type="range" id="min_age" name="min_age"
                               min="18" max="100"
                               value="<?php echo e(old('min_age', $pref->min_age ?? 18)); ?>"
                               style="z-index:2">
                        <input type="range" id="max_age" name="max_age"
                               min="18" max="100"
                               value="<?php echo e(old('max_age', $pref->max_age ?? 50)); ?>"
                               style="z-index:3">
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <small class="text-muted">18 yrs</small>
                        <small class="text-muted">100 yrs</small>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['min_age'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['max_age'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <hr class="my-4">

                
                <div class="mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <label class="form-label fw-semibold mb-0">Maximum Distance</label>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-bold px-3 py-1"
                                  id="dist-badge">
                                <span id="dist-val"><?php echo e(old('max_distance_km') !== null ? old('max_distance_km') : ($pref->max_distance_km ?? 100)); ?></span><span id="dist-unit"><?php echo e((old('max_distance_km') !== null ? old('max_distance_km') : $pref->max_distance_km) !== null ? ' km' : ''); ?></span>
                            </span>
                            <button type="button" id="reset-distance"
                                    class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1"
                                    title="Remove distance limit">
                                <i class="bi bi-x-circle"></i> Any
                            </button>
                        </div>
                    </div>
                    <input type="range" class="form-range" id="max_distance_km"
                           name="max_distance_km"
                           min="1" max="500" step="5"
                           value="<?php echo e(old('max_distance_km') !== null ? old('max_distance_km') : ($pref->max_distance_km ?? 100)); ?>">
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">1 km</small>
                        <small class="text-muted">500 km</small>
                    </div>
                    <div id="dist-reset-note"
                         class="mt-1 <?php echo e((old('max_distance_km') !== null ? old('max_distance_km') : $pref->max_distance_km) !== null ? 'd-none' : ''); ?>">
                        <small class="text-success">
                            <i class="bi bi-check-circle me-1"></i>No distance limit — showing everyone regardless of location.
                        </small>
                    </div>
                </div>

                <hr class="my-4">

                
                <div class="mb-4">
                    <label class="form-label fw-semibold">Preferred Region
                        <span class="text-muted fw-normal">(optional)</span>
                    </label>
                    <p class="text-muted small mb-2">Prioritise people from a specific state or province.</p>
                    <select name="preferred_state" id="onbPrefStateSelect"
                            class="form-select <?php echo e(!\App\Helpers\StateHelper::hasStates($onbCountry) ? 'd-none' : ''); ?>">
                        <option value="">No preference — show all</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = \App\Helpers\StateHelper::forCountry($onbCountry); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($s); ?>" <?php echo e(old('preferred_state', $pref->preferred_state ?? '') === $s ? 'selected' : ''); ?>><?php echo e($s); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </select>
                    <input type="text" name="preferred_state" id="onbPrefStateText"
                           class="form-control <?php echo e(\App\Helpers\StateHelper::hasStates($onbCountry) ? 'd-none' : ''); ?>"
                           placeholder="Preferred state (optional)"
                           value="<?php echo e(old('preferred_state', $pref->preferred_state ?? '')); ?>">
                </div>

                <hr class="my-4">

                
                <div class="mb-4">
                    <label class="form-label fw-semibold">Body Type Preference
                        <span class="text-muted fw-normal">(optional)</span>
                    </label>
                    <p class="text-muted small mb-2">Select any that apply — leave blank to match all body types.</p>
                    <div class="d-flex flex-wrap gap-2">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['slim' => 'Slim', 'athletic' => 'Athletic', 'average' => 'Average', 'curvy' => 'Curvy', 'large' => 'Large']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bv => $bl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $btChecked = in_array($bv, (array)(old('body_types', $pref->body_types ?? []))); ?>
                        <div class="body-chip">
                            <input type="checkbox" name="body_types[]" id="bt_<?php echo e($bv); ?>"
                                   value="<?php echo e($bv); ?>" <?php echo e($btChecked ? 'checked' : ''); ?>>
                            <label for="bt_<?php echo e($bv); ?>"><?php echo e($bl); ?></label>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                <hr class="my-4">

                
                <div class="d-flex align-items-center justify-content-between rounded-3 border p-3">
                    <div>
                        <div class="fw-semibold">Show online users only</div>
                        <div class="text-muted small">Only show profiles of people currently active.</div>
                    </div>
                    <div class="form-check form-switch ms-3 mb-0">
                        <input class="form-check-input" type="checkbox" role="switch"
                               name="show_online_only" id="show_online_only" value="1"
                               <?php echo e(old('show_online_only', $pref->show_online_only ?? false) ? 'checked' : ''); ?>

                               style="width:2.6em;height:1.4em">
                        <label class="form-check-label visually-hidden" for="show_online_only">Online only</label>
                    </div>
                </div>

            </div>
        </div>

        
        <div class="d-flex justify-content-between">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($is_edit)): ?>
                <a href="<?php echo e(url()->previous()); ?>" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            <?php else: ?>
                <a href="<?php echo e(route('setup.step', 2)); ?>" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold">
                <?php echo e(isset($is_edit) ? 'Save Preferences' : 'Continue'); ?>

                <i class="bi bi-<?php echo e(isset($is_edit) ? 'check-lg' : 'arrow-right'); ?> ms-1"></i>
            </button>
        </div>

    </form>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// ── Gender pill: visual sync ──────────────────────────────────────────────────
(function () {
    function syncGender() {
        document.querySelectorAll('.gender-pill input[type="radio"]').forEach(function (r) {
            var lbl = document.querySelector('label[for="' + r.id + '"]');
            if (!lbl) return;
            if (r.checked) {
                lbl.style.background  = 'var(--bs-primary)';
                lbl.style.color       = '#fff';
                lbl.style.borderColor = 'var(--bs-primary)';
            } else {
                lbl.style.background  = '';
                lbl.style.color       = '';
                lbl.style.borderColor = '';
            }
        });
    }
    document.querySelectorAll('.gender-pill input[type="radio"]').forEach(function (r) {
        r.addEventListener('change', syncGender);
    });
    syncGender();
})();

// ── Dual age range slider ──────────────────────────────────────────────────────
(function () {
    var minEl  = document.getElementById('min_age');
    var maxEl  = document.getElementById('max_age');
    var fill   = document.getElementById('age-fill');
    var badge  = document.getElementById('age-badge');
    var ABS_MIN = 18, ABS_MAX = 100;

    function pct(v) { return ((v - ABS_MIN) / (ABS_MAX - ABS_MIN)) * 100; }

    function syncAge() {
        var lo = parseInt(minEl.value);
        var hi = parseInt(maxEl.value);
        if (lo > hi) { maxEl.value = lo; hi = lo; }
        fill.style.left  = pct(lo) + '%';
        fill.style.width = (pct(hi) - pct(lo)) + '%';
        badge.textContent = lo + ' \u2013 ' + hi;
    }

    minEl.addEventListener('input', syncAge);
    maxEl.addEventListener('input', syncAge);
    syncAge();
})();

// ── Distance slider ────────────────────────────────────────────────────────────
(function () {
    var dist     = document.getElementById('max_distance_km');
    var valEl    = document.getElementById('dist-val');
    var unitEl   = document.getElementById('dist-unit');
    var note     = document.getElementById('dist-reset-note');
    var resetBtn = document.getElementById('reset-distance');

    dist.addEventListener('input', function () {
        valEl.textContent  = this.value;
        unitEl.textContent = ' km';
        this.name          = 'max_distance_km';
        note.classList.add('d-none');
        var h = document.getElementById('dist-reset-input');
        if (h) h.remove();
    });

    resetBtn.addEventListener('click', function () {
        dist.name          = '';
        valEl.textContent  = 'Any';
        unitEl.textContent = '';
        note.classList.remove('d-none');
        var h = document.getElementById('dist-reset-input');
        if (!h) {
            h       = document.createElement('input');
            h.type  = 'hidden';
            h.id    = 'dist-reset-input';
            h.name  = 'max_distance_km';
            h.value = '';
            dist.closest('form').appendChild(h);
        }
    });
})();

// ── Geolocation: GPS (HTTPS) + IP-API fallback (HTTP) ────────────────────────
(function () {
    var detectBtn = document.getElementById('detect-location');
    if (!detectBtn) return;

    detectBtn.addEventListener('click', function () {
        var btn    = this;
        var status = document.getElementById('geo-status');
        var secure = location.protocol === 'https:'
            || location.hostname === 'localhost'
            || location.hostname === '127.0.0.1';

        btn.disabled  = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Detecting\u2026';
        status.textContent = '';

        function fillFields(city, state, country, lat, lng) {
            if (city) document.getElementById('city').value = city;
            if (lat)  document.getElementById('latitude').value  = lat;
            if (lng)  document.getElementById('longitude').value = lng;

            if (country) {
                var cSel = document.getElementById('onbCountry');
                if (cSel) {
                    for (var i = 0; i < cSel.options.length; i++) {
                        if (cSel.options[i].value.toLowerCase() === country.toLowerCase()) {
                            cSel.value = cSel.options[i].value;
                            cSel.dispatchEvent(new Event('change'));
                            break;
                        }
                    }
                }
            }

            if (state) {
                setTimeout(function () {
                    var sSel = document.getElementById('onbStateSelect');
                    var sTxt = document.getElementById('onbStateText');
                    if (sSel && !sSel.classList.contains('d-none')) {
                        for (var j = 0; j < sSel.options.length; j++) {
                            if (sSel.options[j].value.toLowerCase() === state.toLowerCase()) {
                                sSel.value = sSel.options[j].value;
                                break;
                            }
                        }
                    } else if (sTxt) {
                        sTxt.value = state;
                    }
                }, 700);
            }

            var parts = [city, state, country].filter(Boolean);
            status.innerHTML = '<span class="text-success"><i class="bi bi-check-circle-fill me-1"></i>Detected: ' + parts.join(', ') + '</span>';
            btn.innerHTML = '<i class="bi bi-crosshair2 me-1"></i> Location detected';
            btn.disabled  = false;
        }

        async function ipFallback() {
            status.innerHTML = '<span class="text-muted"><i class="bi bi-globe me-1"></i>GPS unavailable \u2014 detecting via network\u2026</span>';
            try {
                var res  = await fetch('https://ipapi.co/json/');
                var data = await res.json();
                if (data && data.city) {
                    fillFields(data.city || '', data.region || '', data.country_name || '', data.latitude || null, data.longitude || null);
                } else {
                    throw new Error('no city');
                }
            } catch (_) {
                status.innerHTML = '<span class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i>Could not auto-detect. Please type your location manually.</span>';
                btn.innerHTML = '<i class="bi bi-crosshair2 me-1"></i> Auto-detect my location';
                btn.disabled  = false;
            }
        }

        if (!secure)                 { ipFallback(); return; }
        if (!navigator.geolocation) { ipFallback(); return; }

        navigator.geolocation.getCurrentPosition(
            async function (pos) {
                var lat = pos.coords.latitude;
                var lng = pos.coords.longitude;
                document.getElementById('latitude').value  = lat;
                document.getElementById('longitude').value = lng;
                try {
                    var res  = await fetch(
                        'https://nominatim.openstreetmap.org/reverse?lat=' + lat + '&lon=' + lng + '&format=json&accept-language=en',
                        { headers: { 'Accept': 'application/json' } }
                    );
                    var data = await res.json();
                    var addr = data.address || {};
                    fillFields(
                        addr.city || addr.town || addr.village || addr.municipality || addr.county || '',
                        addr.state || addr.state_district || '',
                        addr.country || '',
                        lat, lng
                    );
                } catch (_) {
                    status.innerHTML = '<span class="text-warning"><i class="bi bi-check-circle me-1"></i>Coordinates saved \u2014 please confirm city, state &amp; country above.</span>';
                    btn.innerHTML = '<i class="bi bi-crosshair2 me-1"></i> Auto-detect my location';
                    btn.disabled  = false;
                }
            },
            async function (err) {
                if (err.code === 1 || err.code === 2) {
                    await ipFallback();
                } else {
                    btn.disabled  = false;
                    btn.innerHTML = '<i class="bi bi-crosshair2 me-1"></i> Auto-detect my location';
                    status.innerHTML = '<span class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i>Request timed out \u2014 please try again.</span>';
                }
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 300000 }
        );
    });
})();

// ── State cascade (country → state + preferred region dropdowns) ──────────────
(function () {
    var countryEl = document.getElementById('onbCountry');
    var stateEl   = document.getElementById('onbStateSelect');
    var stateText = document.getElementById('onbStateText');
    var prefEl    = document.getElementById('onbPrefStateSelect');
    var prefText  = document.getElementById('onbPrefStateText');
    if (!countryEl) return;

    async function loadStates(country, stateVal, prefVal) {
        try {
            var res    = await fetch('<?php echo e(route('api.states')); ?>?country=' + encodeURIComponent(country));
            var states = await res.json();

            function populate(sel, txt, selected, anyLabel) {
                if (!sel) return;
                if (states.length) {
                    sel.innerHTML = '<option value="">' + anyLabel + '</option>' +
                        states.map(function (s) {
                            return '<option value="' + s + '"' + (s === selected ? ' selected' : '') + '>' + s + '</option>';
                        }).join('');
                    sel.classList.remove('d-none');
                    if (txt) { txt.classList.add('d-none'); txt.removeAttribute('name'); }
                } else {
                    sel.innerHTML = '';
                    sel.classList.add('d-none');
                    if (txt) { txt.classList.remove('d-none'); txt.value = selected || ''; }
                }
            }

            populate(stateEl, stateText, stateVal, 'Select state\u2026');
            if (prefEl) populate(prefEl, prefText, prefVal, 'No preference \u2014 show all');
        } catch (_) {}
    }

    countryEl.addEventListener('change', function () {
        loadStates(this.value, '', '');
    });

    var init = countryEl.value;
    if (init && stateEl && stateEl.options.length <= 1) {
        loadStates(
            init,
            '<?php echo e(addslashes(old('state', $profile?->state ?? ''))); ?>',
            '<?php echo e(addslashes(old('preferred_state', $pref->preferred_state ?? ''))); ?>'
        );
    }
})();
</script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\dating\resources\views\onboarding\step4.blade.php ENDPATH**/ ?>