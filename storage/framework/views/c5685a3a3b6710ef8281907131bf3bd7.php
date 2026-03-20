
<?php $__env->startSection('title', 'Edit Profile'); ?>
<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h4 class="fw-bold mb-4"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Your Profile</h4>

            
            <?php $completion = $profile?->completion_percent ?? 0; ?>
            <div class="card border-0 shadow-sm mb-4 <?php echo e($completion >= 80 ? 'border-success' : ''); ?>">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-semibold small"><i class="bi bi-bar-chart-fill me-1 text-primary"></i>Profile Strength</span>
                        <span class="fw-bold <?php echo e($completion >= 80 ? 'text-success' : ($completion >= 50 ? 'text-warning' : 'text-danger')); ?>">
                            <?php echo e($completion); ?>%
                        </span>
                    </div>
                    <div class="progress mb-2" style="height:10px;border-radius:6px">
                        <div class="progress-bar <?php echo e($completion >= 80 ? 'bg-success' : ($completion >= 50 ? 'bg-warning' : 'bg-danger')); ?>"
                             style="width:<?php echo e($completion); ?>%;transition:width .5s ease"></div>
                    </div>
                    <p class="small text-muted mb-0">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($completion < 40): ?> Add a headline, bio, and lifestyle details to stand out!
                        <?php elseif($completion < 80): ?> Looking good! Adding more details can get you 3× more matches.
                        <?php else: ?> 🌟 Great profile! You're showing up at your best.
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </p>
                </div>
            </div>

            <form method="POST" action="<?php echo e(route('profile.update')); ?>">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent fw-semibold">Basic Info</div>
                    <div class="card-body row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Display Name</label>
                            <input type="text" name="name" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('name', $user->name)); ?>" required>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Username</label>
                            <div class="input-group">
                                <span class="input-group-text">@</span>
                                <input type="text" name="username" id="usernameInput"
                                       class="form-control <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       value="<?php echo e(old('username', $user->username)); ?>"
                                       autocomplete="off"
                                       required>
                                <button type="button" id="aiUsernameBtn" class="btn btn-outline-secondary" title="AI username ideas">
                                    ✨
                                </button>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <div id="username-status" class="mt-1"></div>
                            <div id="username-suggestions" class="mt-2 d-none">
                                <div class="text-muted small mb-1"><i class="bi bi-lightbulb me-1"></i>Suggestions — tap to use:</div>
                                <div id="username-suggestions-list" class="d-flex flex-wrap gap-1"></div>
                                <div id="username-suggestions-spinner" class="d-none">
                                    <span class="spinner-border spinner-border-sm text-secondary me-1"></span>
                                    <span class="text-muted small">Generating ideas…</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Tagline <span class="text-muted small">(headline people see first)</span></label>
                            <input type="text" name="tagline" class="form-control" value="<?php echo e(old('tagline', $profile?->headline)); ?>" maxlength="120" placeholder="e.g. Adventure seeker looking for my co-pilot ✈️">
                        </div>
                        <div class="col-12">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label class="form-label mb-0">About Me</label>
                                <button type="button" id="aiBioBtn"
                                        class="btn btn-sm btn-outline-secondary"
                                        style="font-size:.78rem;padding:2px 8px;border-color:#f0d0e0;color:#c2185b">
                                    ✨ AI suggest
                                </button>
                            </div>
                            <textarea name="about" id="aboutTextarea" class="form-control" rows="4" maxlength="2000" placeholder="Tell potential matches about yourself..."><?php echo e(old('about', $profile?->bio)); ?></textarea>
                            <div id="aibioPanell" class="mt-2 d-none">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="small fw-semibold" style="color:#c2185b">✨ AI Bio Ideas</span>
                                    <span id="aiBioSpinner" class="d-none"><span class="spinner-border spinner-border-sm text-danger" role="status"></span></span>
                                    <button type="button" id="aiBioClose" class="btn-close ms-auto" style="width:.6rem;height:.6rem"></button>
                                </div>
                                <div id="aiBioList"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent fw-semibold">Appearance & Lifestyle</div>
                    <div class="card-body row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Height (cm)</label>
                            <input type="number" name="height_cm" class="form-control" value="<?php echo e(old('height_cm', $profile?->height_cm)); ?>" min="100" max="250">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Body Type</label>
                            <select name="body_type" class="form-select">
                                <option value="">Prefer not to say</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['slim','athletic','average','curvy','plus_size','muscular']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($t); ?>" <?php echo e(old('body_type', $profile?->body_type) === $t ? 'selected' : ''); ?>><?php echo e(ucfirst(str_replace('_',' ',$t))); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Ethnicity</label>
                            <input type="text" name="ethnicity" class="form-control" value="<?php echo e(old('ethnicity', $profile?->ethnicity)); ?>" maxlength="80">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Religion</label>
                            <input type="text" name="religion" class="form-control" value="<?php echo e(old('religion', $profile?->religion)); ?>" maxlength="80">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Education</label>
                            <select name="education_level" class="form-select">
                                <option value="">Prefer not to say</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['high_school','some_college','bachelors','masters','phd','trade_school','other']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($e); ?>" <?php echo e(old('education_level', $profile?->education_level) === $e ? 'selected' : ''); ?>><?php echo e(ucfirst(str_replace('_',' ',$e))); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Occupation</label>
                            <input type="text" name="occupation" class="form-control" value="<?php echo e(old('occupation', $profile?->occupation)); ?>" maxlength="100">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Smoking</label>
                            <select name="smoking_habit" class="form-select">
                                <option value="">Prefer not to say</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['never','sometimes','regularly','trying_to_quit']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($s); ?>" <?php echo e(old('smoking_habit', $profile?->smoking_habit) === $s ? 'selected' : ''); ?>><?php echo e(ucfirst(str_replace('_',' ',$s))); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Drinking</label>
                            <select name="drinking_habit" class="form-select">
                                <option value="">Prefer not to say</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['never','socially','regularly']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($d); ?>" <?php echo e(old('drinking_habit', $profile?->drinking_habit) === $d ? 'selected' : ''); ?>><?php echo e(ucfirst($d)); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Relationship Goal</label>
                            <select name="relationship_goal" class="form-select">
                                <option value="">Not specified</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['casual','long_term','marriage','friendship','unsure']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($g); ?>" <?php echo e(old('relationship_goal', $profile?->relationship_goal) === $g ? 'selected' : ''); ?>><?php echo e(ucfirst(str_replace('_',' ',$g))); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="wants_children" id="wantsKids" value="1" <?php echo e(old('wants_children', $profile?->wants_children) ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="wantsKids">Open to having children</label>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent fw-semibold">Location</div>
                    <div class="card-body row g-3">
                        <?php
                            $currentCountry = old('country', $profile?->country ?? '');
                            $currentState   = old('state',   $profile?->state   ?? '');
                        ?>
                        <div class="col-md-6">
                            <label class="form-label">City</label>
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
                                   value="<?php echo e(old('city', $profile?->city ?? '')); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Country</label>
                            <select name="country" id="country"
                                    class="form-select <?php $__errorArgs = ['country'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <option value="">Select country…</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = \App\Helpers\CountryHelper::list(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cName => $cCode): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($cName); ?>"
                                        <?php echo e($currentCountry === $cName ? 'selected' : ''); ?>>
                                        <?php echo e($cName); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['country'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="col-md-6" id="stateWrapper">
                            <label class="form-label">State / Province</label>
                            <select name="state" id="stateSelect"
                                    class="form-select <?php $__errorArgs = ['state'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <option value="">Select state…</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = \App\Helpers\StateHelper::forCountry($currentCountry); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($s); ?>" <?php echo e($currentState === $s ? 'selected' : ''); ?>><?php echo e($s); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                            <input type="text" name="state" id="stateText"
                                   class="form-control mt-1 d-none <?php $__errorArgs = ['state'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   placeholder="State / Province"
                                   value="<?php echo e($currentState); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['state'];
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
                            <button type="button" id="detect-location" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-geo-alt me-1"></i> Detect my location
                            </button>
                            <small id="geo-status" class="text-muted ms-2"></small>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary px-5 fw-bold"><i class="bi bi-check-lg me-2"></i>Save Changes</button>
            </form>

            
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent fw-semibold">Photos <span class="badge bg-secondary"><?php echo e($user->photos->count()); ?>/6</span></div>
                <div class="card-body">
                    <div class="row g-2 mb-3">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $user->photos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-4 col-md-2 position-relative">
                            <div class="ratio ratio-1x1"><img src="<?php echo e($photo->thumbnail_url); ?>" class="object-fit-cover rounded-3 w-100 h-100" alt=""></div>
                            <div class="position-absolute top-0 end-0 m-1 d-flex flex-column gap-1">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $photo->is_primary): ?>
                                <form method="POST" action="<?php echo e(route('photos.primary', $photo->id)); ?>"><?php echo csrf_field(); ?><button class="btn btn-xs btn-warning rounded-circle p-1" title="Set primary" style="width:22px;height:22px;font-size:.6rem"><i class="bi bi-star-fill"></i></button></form>
                                <?php else: ?>
                                <span class="badge bg-warning p-1" title="Primary photo"><i class="bi bi-star-fill"></i></span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <form method="POST" action="<?php echo e(route('photos.destroy', $photo->id)); ?>"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="btn btn-xs btn-danger rounded-circle p-1" style="width:22px;height:22px;font-size:.6rem"><i class="bi bi-trash-fill"></i></button></form>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->photos->count() < 6): ?>
                    <form method="POST" action="<?php echo e(route('photos.store')); ?>" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <label class="form-label">Add Photo</label>
                        <input type="file" name="photo" class="form-control mb-2" accept="image/jpeg,image/png,image/webp">
                        <div class="form-text mb-2">JPEG/PNG/WebP, max 8 MB. Up to <?php echo e(6 - $user->photos->count()); ?> more.</div>
                        <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-upload me-1"></i>Upload Photo</button>
                    </form>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    const detectBtn = document.getElementById('detect-location');
    if (!detectBtn) return;
    detectBtn.addEventListener('click', function () {
        const btn      = this;
        const status   = document.getElementById('geo-status');
        const isSecure = location.protocol === 'https:'
            || location.hostname === 'localhost'
            || location.hostname === '127.0.0.1';

        btn.disabled  = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Detecting…';
        status.textContent = '';

        function fillFields(city, state, country, lat, lng) {
            if (city)    document.getElementById('city').value    = city;
            if (state) {
                const stEl = document.getElementById('stateText') || document.getElementById('stateSelect');
                if (stEl) stEl.value = state;
            }
            if (country) {
                const sel = document.getElementById('country');
                for (let i = 0; i < sel.options.length; i++) {
                    if (sel.options[i].value.toLowerCase() === country.toLowerCase()) {
                        sel.value = sel.options[i].value; break;
                    }
                }
                sel.dispatchEvent(new Event('change'));
            }
            if (lat) document.getElementById('latitude').value  = lat;
            if (lng) document.getElementById('longitude').value = lng;
            const parts = [city, state, country].filter(Boolean);
            status.innerHTML = '<span class="text-success"><i class="bi bi-check-circle-fill me-1"></i>Detected: ' + parts.join(', ') + '</span>';
            btn.innerHTML = '<i class="bi bi-geo-alt-fill me-1"></i> Location detected!';
            btn.disabled  = false;
        }

        async function ipFallback() {
            status.innerHTML = '<span class="text-muted"><i class="bi bi-globe me-1"></i> GPS unavailable — detecting via network…</span>';
            try {
                const res  = await fetch('https://ipapi.co/json/');
                const data = await res.json();
                if (data && data.city) {
                    fillFields(data.city || '', data.region || '', data.country_name || '', data.latitude || null, data.longitude || null);
                } else { throw new Error('empty'); }
            } catch (_) {
                status.innerHTML = '<span class="text-warning">⚠️ Could not auto-detect. Please type your city and country manually.</span>';
                btn.innerHTML = '<i class="bi bi-geo-alt me-1"></i> Detect my location';
                btn.disabled  = false;
            }
        }

        if (!isSecure) { ipFallback(); return; }
        if (!navigator.geolocation) { ipFallback(); return; }

        navigator.geolocation.getCurrentPosition(
            async function (pos) {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                document.getElementById('latitude').value  = lat;
                document.getElementById('longitude').value = lng;
                try {
                    const res  = await fetch(
                        'https://nominatim.openstreetmap.org/reverse?lat=' + lat + '&lon=' + lng + '&format=json&accept-language=en',
                        { headers: { 'Accept': 'application/json' } }
                    );
                    const data = await res.json();
                    const addr    = data.address ?? {};
                    const city    = addr.city || addr.town || addr.village || addr.municipality || addr.county || '';
                    const state   = addr.state || addr.state_district || '';
                    const country = addr.country || '';
                    fillFields(city, state, country, lat, lng);
                } catch (_) {
                    status.innerHTML = '<span class="text-warning">✓ Coordinates saved — please confirm your city &amp; country above.</span>';
                    btn.innerHTML = '<i class="bi bi-geo-alt me-1"></i> Detect my location';
                    btn.disabled  = false;
                }
            },
            async function (err) {
                if (err.code === 1 || err.code === 2) {
                    await ipFallback();
                } else {
                    btn.disabled  = false;
                    btn.innerHTML = '<i class="bi bi-geo-alt me-1"></i> Detect my location';
                    status.innerHTML = '<span class="text-warning">⚠️ Request timed out — please try again.</span>';
                }
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 300000 }
        );
    });
})();
</script>

<script>
// ── State Cascade ─────────────────────────────────────────────────────────────
(function () {
    const countryEl = document.getElementById('country');
    const stateEl   = document.getElementById('stateSelect');
    const stateText = document.getElementById('stateText');
    if (!countryEl || !stateEl) return;

    async function loadStates(country, selectedState) {
        stateEl.innerHTML = '<option value="">Loading…</option>';
        try {
            const res    = await fetch(`<?php echo e(route('api.states')); ?>?country=${encodeURIComponent(country)}`);
            const states = await res.json();
            if (states.length) {
                stateEl.innerHTML = '<option value="">Select state…</option>' +
                    states.map(s => `<option value="${s}"${s === selectedState ? ' selected' : ''}>${s}</option>`).join('');
                stateEl.closest('.col-md-6').style.display = '';
                stateEl.classList.remove('d-none');
                stateText.classList.add('d-none');
                stateText.removeAttribute('name');
                stateEl.name = 'state';
            } else {
                // No state data — use free-text
                stateEl.innerHTML = '';
                stateEl.classList.add('d-none');
                stateEl.removeAttribute('name');
                stateText.classList.remove('d-none');
                stateText.name = 'state';
                stateText.value = selectedState || '';
                stateEl.closest('.col-md-6').style.display = '';
            }
        } catch (_) {}
    }

    countryEl.addEventListener('change', function () {
        loadStates(this.value, '');
    });

    // On page load, if a country is already selected, states may be pre-rendered server-side.
    // If not, reload them client-side too.
    const initialCountry = countryEl.value;
    if (initialCountry && stateEl.options.length <= 1) {
        loadStates(initialCountry, '<?php echo e(old('state', $profile?->state ?? '')); ?>');
    }
})();
</script>

<script>
// ── AI Bio Helper ────────────────────────────────────────────────────────────
(function () {
    const btn      = document.getElementById('aiBioBtn');
    const panel    = document.getElementById('aibioPanell');
    const spinner  = document.getElementById('aiBioSpinner');
    const list     = document.getElementById('aiBioList');
    const closeBtn = document.getElementById('aiBioClose');
    const textarea = document.getElementById('aboutTextarea');
    const csrf     = document.querySelector('meta[name="csrf-token"]').content;

    if (!btn) return;

    closeBtn.addEventListener('click', () => panel.classList.add('d-none'));

    btn.addEventListener('click', async () => {
        panel.classList.remove('d-none');
        list.innerHTML = '';
        spinner.classList.remove('d-none');

        try {
            const res = await fetch('<?php echo e(route('ai.suggest')); ?>', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' },
                body: JSON.stringify({ type: 'bio' }),
            });
            const data = await res.json();
            spinner.classList.add('d-none');

            (data.suggestions ?? []).forEach(s => {
                const card = document.createElement('div');
                card.style.cssText = 'background:#fff9fb;border:1px solid #f3c0d5;border-radius:10px;padding:10px 12px;margin-bottom:8px;font-size:.88rem;cursor:pointer;';
                card.innerHTML = s.replace(/</g,'&lt;').replace(/>/g,'&gt;')
                    + '<div style="font-size:.72rem;color:#c2185b;font-weight:600;margin-top:5px">Tap to use ↑</div>';
                card.addEventListener('click', () => {
                    textarea.value = s;
                    panel.classList.add('d-none');
                });
                list.appendChild(card);
            });

            if (!data.suggestions?.length) {
                list.innerHTML = '<p class="text-muted small text-center mt-1">No suggestions available.</p>';
            }
        } catch {
            spinner.classList.add('d-none');
            list.innerHTML = '<p class="text-danger small">Failed — please try again.</p>';
        }
    });
})();
// ── AI Username Helper ───────────────────────────────────────────────────────
(function () {
    const input       = document.getElementById('usernameInput');
    const statusEl    = document.getElementById('username-status');
    const suggestBox  = document.getElementById('username-suggestions');
    const suggestList = document.getElementById('username-suggestions-list');
    const spinner     = document.getElementById('username-suggestions-spinner');
    const aiBtn       = document.getElementById('aiUsernameBtn');
    const csrf        = document.querySelector('meta[name="csrf-token"]').content;
    const checkUrl    = '<?php echo e(route("ai.username-check")); ?>';

    if (!input) return;

    let debounceTimer = null;
    let lastChecked   = '';

    function pickSuggestion(val) {
        input.value = val;
        suggestBox.classList.add('d-none');
        statusEl.innerHTML = '';
        // Trigger a fresh check for the picked value
        lastChecked = '';
        input.dispatchEvent(new Event('input'));
    }

    function renderSuggestions(suggestions) {
        suggestList.innerHTML = '';
        spinner.classList.add('d-none');
        if (!suggestions.length) {
            suggestList.innerHTML = '<span class="text-muted small">No suggestions available.</span>';
            suggestBox.classList.remove('d-none');
            return;
        }
        suggestions.forEach(s => {
            const badge = document.createElement('button');
            badge.type = 'button';
            badge.className = 'badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1 small';
            badge.style.cursor = 'pointer';
            badge.textContent = '@' + s;
            badge.addEventListener('click', () => pickSuggestion(s));
            suggestList.appendChild(badge);
        });
        suggestBox.classList.remove('d-none');
    }

    async function checkUsername(username) {
        if (!username || username.length < 3) {
            statusEl.innerHTML = '';
            suggestBox.classList.add('d-none');
            return;
        }
        if (username === lastChecked) return;
        lastChecked = username;

        try {
            const res  = await fetch(checkUrl + '?username=' + encodeURIComponent(username), {
                headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            });
            const data = await res.json();

            if (data.flagged) {
                statusEl.innerHTML = '<span class="text-danger small"><i class="bi bi-x-circle me-1"></i>This username contains words that aren\'t allowed in our community.</span>';
                spinner.classList.remove('d-none');
                suggestBox.classList.remove('d-none');
                suggestList.innerHTML = '';
                renderSuggestions(data.suggestions ?? []);
            } else if (data.taken) {
                statusEl.innerHTML = '<span class="text-warning small"><i class="bi bi-exclamation-circle me-1"></i>Username already taken. Try one of these:</span>';
                spinner.classList.remove('d-none');
                suggestBox.classList.remove('d-none');
                suggestList.innerHTML = '';
                renderSuggestions(data.suggestions ?? []);
            } else {
                statusEl.innerHTML = '<span class="text-success small"><i class="bi bi-check-circle me-1"></i>Username is available!</span>';
                suggestBox.classList.add('d-none');
            }
        } catch {
            statusEl.innerHTML = '';
        }
    }

    // Debounce on input
    input.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        const val = input.value.trim();
        if (!val) { statusEl.innerHTML = ''; suggestBox.classList.add('d-none'); return; }
        debounceTimer = setTimeout(() => checkUsername(val), 600);
    });

    // Manual AI suggestions button
    aiBtn.addEventListener('click', async () => {
        suggestBox.classList.remove('d-none');
        suggestList.innerHTML = '';
        spinner.classList.remove('d-none');
        try {
            const res  = await fetch('<?php echo e(route("ai.suggest")); ?>', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ type: 'username' }),
            });
            const data = await res.json();
            renderSuggestions(data.suggestions ?? []);
        } catch {
            spinner.classList.add('d-none');
            suggestList.innerHTML = '<span class="text-danger small">Failed — please try again.</span>';
            suggestBox.classList.remove('d-none');
        }
    });

    // Check on page load if username is already set
    const initial = input.value.trim();
    if (initial) checkUsername(initial);
})();
</script>
<?php $__env->stopPush(); ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\dating\resources\views\profile\edit.blade.php ENDPATH**/ ?>