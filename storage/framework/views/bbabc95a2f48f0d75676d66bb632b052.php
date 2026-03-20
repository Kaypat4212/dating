
<?php $__env->startSection('title', 'Account Settings'); ?>
<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <h4 class="fw-bold mb-4"><i class="bi bi-gear me-2 text-primary"></i>Account Settings</h4>

            
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent fw-semibold"><i class="bi bi-star-fill text-warning me-2"></i>Membership</div>
                <div class="card-body">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->isPremiumActive()): ?>
                    <div class="d-flex justify-content-between align-items-center">
                        <div><span class="badge bg-warning text-dark me-2">Premium</span> Active until <?php echo e($user->premium_expires_at->format('M j, Y')); ?></div>
                        <a href="<?php echo e(route('premium.show')); ?>" class="btn btn-sm btn-outline-warning">Renew</a>
                    </div>
                    <?php else: ?>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Free account</span>
                        <a href="<?php echo e(route('premium.show')); ?>" class="btn btn-sm btn-warning fw-bold"><i class="bi bi-star-fill me-1"></i>Upgrade</a>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent fw-semibold"><i class="bi bi-pause-circle me-2"></i>Discovery Visibility</div>
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-semibold"><?php echo e($user->profile?->is_paused ? 'Profile Hidden' : 'Profile Visible'); ?></div>
                        <div class="text-muted small">When hidden, you will not appear in discovery or search results.</div>
                    </div>
                    <form method="POST" action="<?php echo e(route('account.pause')); ?>"><?php echo csrf_field(); ?><button class="btn btn-sm <?php echo e($user->profile?->is_paused ? 'btn-success' : 'btn-outline-secondary'); ?>"><?php echo e($user->profile?->is_paused ? 'Unhide' : 'Hide Profile'); ?></button></form>
                </div>
            </div>

            
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent fw-semibold">
                    <i class="bi bi-eye-slash me-2"></i>Last Seen Privacy
                    <span class="badge bg-warning text-dark ms-2 small">Premium</span>
                </div>
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-semibold"><?php echo e($user->hide_last_seen ? 'Last seen hidden' : 'Last seen visible'); ?></div>
                        <div class="text-muted small">When hidden, other users cannot see when you were last active.</div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$user->isPremiumActive()): ?>
                        <div class="text-warning small mt-1"><i class="bi bi-lock-fill me-1"></i>Requires any Premium plan</div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->isPremiumActive()): ?>
                    <form method="POST" action="<?php echo e(route('account.last-seen')); ?>"><?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-sm <?php echo e($user->hide_last_seen ? 'btn-success' : 'btn-outline-secondary'); ?>">
                            <?php echo e($user->hide_last_seen ? 'Unhide' : 'Hide'); ?>

                        </button>
                    </form>
                    <?php else: ?>
                    <a href="<?php echo e(route('premium.show')); ?>" class="btn btn-sm btn-warning">
                        <i class="bi bi-star-fill me-1"></i>Upgrade
                    </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent fw-semibold">
                    <i class="bi bi-eye-slash me-2 text-info"></i>Photo Privacy
                    <span class="badge bg-warning text-dark ms-2 small">Premium</span>
                </div>
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-semibold"><?php echo e($user->profile?->private_photos ? 'Photos private' : 'Photos public'); ?></div>
                        <div class="text-muted small">When enabled, your photos are blurred for people you haven't matched with.</div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$user->isPremiumActive()): ?>
                        <div class="text-warning small mt-1"><i class="bi bi-lock-fill me-1"></i>Requires any Premium plan</div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->isPremiumActive()): ?>
                    <form method="POST" action="<?php echo e(route('account.private-photos')); ?>"><?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-sm <?php echo e($user->profile?->private_photos ? 'btn-info text-white' : 'btn-outline-secondary'); ?>">
                            <?php echo e($user->profile?->private_photos ? 'Make Public' : 'Make Private'); ?>

                        </button>
                    </form>
                    <?php else: ?>
                    <a href="<?php echo e(route('premium.show')); ?>" class="btn btn-sm btn-warning">
                        <i class="bi bi-star-fill me-1"></i>Upgrade
                    </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent fw-semibold">
                    <i class="bi bi-rocket-takeoff me-2 text-danger"></i>Profile Boost
                    <span class="badge bg-warning text-dark ms-2 small">Premium</span>
                </div>
                <div class="card-body d-flex justify-content-between align-items-center">
                    <?php $boost = $user->activeBoost(); ?>
                    <div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($boost): ?>
                        <div class="fw-semibold text-danger">
                            <i class="bi bi-fire me-1"></i>Boost active!
                        </div>
                        <div class="text-muted small">Your profile is at the top of the deck until <?php echo e($boost->ends_at->format('g:i A')); ?></div>
                        <?php else: ?>
                        <div class="fw-semibold">Appear first in everyone's discovery deck</div>
                        <div class="text-muted small">A boost puts you at the top for 30 minutes — great for getting more matches quickly.</div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$user->isPremiumActive()): ?>
                        <div class="text-warning small mt-1"><i class="bi bi-lock-fill me-1"></i>Requires any Premium plan</div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($boost): ?>
                    <form method="POST" action="<?php echo e(route('boost.destroy')); ?>"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button class="btn btn-sm btn-outline-secondary">Cancel</button>
                    </form>
                    <?php elseif($user->isPremiumActive()): ?>
                    <form method="POST" action="<?php echo e(route('boost.store')); ?>"><?php echo csrf_field(); ?>
                        <button class="btn btn-sm btn-danger fw-bold">
                            <i class="bi bi-rocket-takeoff me-1"></i>Boost Now
                        </button>
                    </form>
                    <?php else: ?>
                    <a href="<?php echo e(route('premium.show')); ?>" class="btn btn-sm btn-warning">
                        <i class="bi bi-star-fill me-1"></i>Upgrade
                    </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent fw-semibold"><i class="bi bi-slash-circle me-2 text-danger"></i>Blocked Users</div>
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-semibold">Manage your block list</div>
                        <div class="text-muted small">View and unblock users you've blocked.</div>
                    </div>
                    <a href="<?php echo e(route('account.blocked')); ?>" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-list me-1"></i>View Blocks
                    </a>
                </div>
            </div>

            
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent fw-semibold"><i class="bi bi-key-fill text-warning me-2"></i>Password Recovery Secret Word</div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Set a personal secret word. If you ever forget your password, you can use it to reset your password without needing an email link.
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->secret_word): ?>
                        <span class="badge bg-success ms-1"><i class="bi bi-check-lg me-1"></i>Set</span>
                        <?php else: ?>
                        <span class="badge bg-secondary ms-1">Not set</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </p>
                    <form method="POST" action="<?php echo e(route('account.secret-word')); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="input-group">
                            <input type="password" name="secret_word" id="secretWordInput"
                                   class="form-control <?php $__errorArgs = ['secret_word'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   placeholder="<?php echo e($user->secret_word ? 'Enter new secret word to change it' : 'e.g. My first pet name'); ?>"
                                   minlength="3" maxlength="100" required>
                            <button type="button" class="btn btn-outline-secondary" id="toggleSecretWordBtn" title="Show/hide">
                                <i class="bi bi-eye" id="toggleSecretWordIcon"></i>
                            </button>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-floppy me-1"></i>Save</button>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['secret_word'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </form>
                </div>
            </div>

            
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent fw-semibold"><i class="bi bi-download me-2"></i>Your Data</div>
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-semibold">Download your data</div>
                        <div class="text-muted small">Get a full export of everything stored about your account (GDPR).</div>
                    </div>
                    <a href="<?php echo e(route('account.export')); ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-download me-1"></i>Export</a>
                </div>
            </div>

            
            <div class="card border-0 border-danger shadow-sm mb-4">
                <div class="card-header bg-transparent fw-semibold text-danger"><i class="bi bi-trash3-fill me-2"></i>Danger Zone</div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Permanently delete your account, profile, photos, matches, and all messages. This cannot be undone.</p>
                    <button class="btn btn-danger btn-sm" data-bs-toggle="collapse" data-bs-target="#deleteForm"><i class="bi bi-trash3-fill me-2"></i>Delete My Account</button>
                    <div class="collapse mt-3" id="deleteForm">
                        <form method="POST" action="<?php echo e(route('account.destroy')); ?>" onsubmit="return confirm('Are you absolutely sure? This CANNOT be undone.')">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <div class="mb-3">
                                <label class="form-label">Confirm your password to continue</label>
                                <input type="password" name="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <button type="submit" class="btn btn-danger fw-bold">Yes, permanently delete my account</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->startPush('scripts'); ?>
<script>
document.getElementById('toggleSecretWordBtn')?.addEventListener('click', function () {
    var inp  = document.getElementById('secretWordInput');
    var icon = document.getElementById('toggleSecretWordIcon');
    var show = inp.type === 'password';
    inp.type   = show ? 'text' : 'password';
    icon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\dating\resources\views\account\settings.blade.php ENDPATH**/ ?>