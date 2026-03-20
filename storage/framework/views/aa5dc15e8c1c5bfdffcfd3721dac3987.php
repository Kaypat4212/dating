
<?php $__env->startSection('title', $isBlocked ? 'Profile Unavailable' : $profileUser->name); ?>
<?php $__env->startSection('content'); ?>
<?php
    $photosPrivate = $profileUser->profile?->private_photos && !$isMatched && auth()->id() !== $profileUser->id;
?>


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isBlocked): ?>
<div class="container py-5" style="max-width:520px">
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden text-center">

        
        <div class="py-5 px-4" style="background:linear-gradient(135deg,#1a1a2e 0%,#16213e 60%,#0f3460 100%)">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($iBlockedThem): ?>
                <div class="mb-3" style="font-size:3.5rem">🚫</div>
                <h4 class="fw-bold text-white mb-2">You've blocked <?php echo e($profileUser->name); ?></h4>
                <p class="mb-0" style="color:rgba(255,255,255,.6);font-size:.95rem">
                    This person cannot see your profile, like you, or send you messages while they are blocked.
                </p>
            <?php else: ?>
                <div class="mb-3" style="font-size:3.5rem">🔒</div>
                <h4 class="fw-bold text-white mb-2">Profile not available</h4>
                <p class="mb-0" style="color:rgba(255,255,255,.6);font-size:.95rem">
                    This profile isn't available to you right now.
                </p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <div class="card-body px-4 py-4">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($iBlockedThem): ?>
                <p class="text-muted mb-4" style="font-size:.9rem">
                    You blocked <strong><?php echo e($profileUser->name); ?></strong>. You can unblock them at any time from your blocked
                    users list or by clicking below.
                </p>
                <form method="POST" action="<?php echo e(route('block.destroy', $profileUser->id)); ?>"
                      onsubmit="return confirm('Unblock <?php echo e($profileUser->name); ?>?')">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button class="btn btn-danger px-4 mb-3 w-100" style="border-radius:.75rem">
                        <i class="bi bi-slash-circle me-2"></i>Unblock <?php echo e($profileUser->name); ?>

                    </button>
                </form>
                <a href="<?php echo e(route('account.blocked')); ?>" class="btn btn-outline-secondary w-100" style="border-radius:.75rem">
                    <i class="bi bi-list-ul me-2"></i>Manage blocked users
                </a>
            <?php else: ?>
                <p class="text-muted mb-4" style="font-size:.9rem">
                    This might be because the account has been deactivated, removed, or your access to it has been restricted.
                </p>
                <a href="<?php echo e(route('discover.index')); ?>" class="btn btn-primary px-4 w-100 mb-3" style="border-radius:.75rem">
                    <i class="bi bi-compass me-2"></i>Discover other profiles
                </a>
                <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-outline-secondary w-100" style="border-radius:.75rem">
                    <i class="bi bi-house me-2"></i>Go to dashboard
                </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</div>
<?php else: ?>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow">
                
                <div class="position-relative bg-light" style="height:320px;overflow:hidden">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($photos->isNotEmpty()): ?>
                    <div id="photoCarousel" class="carousel slide h-100" data-bs-ride="carousel">
                        <div class="carousel-inner h-100">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $photos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="carousel-item h-100 <?php echo e($i === 0 ? 'active' : ''); ?>">
                                <img src="<?php echo e($photo->url); ?>" class="d-block w-100 h-100 object-fit-cover <?php echo e($photosPrivate ? 'blur-premium' : ''); ?>" alt="<?php echo e($profileUser->name); ?>">
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($photos->count() > 1): ?>
                        <button class="carousel-control-prev" data-bs-target="#photoCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
                        <button class="carousel-control-next" data-bs-target="#photoCarousel" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <?php else: ?>
                    <div class="w-100 h-100 d-flex align-items-center justify-content-center"><i class="bi bi-person-circle display-1 text-muted"></i></div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($photosPrivate): ?>
                    <div class="position-absolute top-0 start-0 end-0 bottom-0 d-flex align-items-center justify-content-center flex-column gap-2"
                         style="background:rgba(0,0,0,.45);z-index:10">
                        <i class="bi bi-lock-fill text-white" style="font-size:2.5rem"></i>
                        <span class="text-white fw-semibold small text-center px-3">Photos are private — match to unlock</span>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                        <div>
                            <h2 class="fw-bold mb-1"><?php echo e($profileUser->name); ?>, <?php echo e($profileUser->age); ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($profileUser->is_verified): ?>
                                <span title="Verified profile" style="font-size:1.2rem;vertical-align:middle;color:#1d9bf0">&#9989;</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </h2>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($profile && $profile->city): ?>
                            <p class="text-muted mb-1"><i class="bi bi-geo-alt me-1"></i><?php echo e($profile->city); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($profile->country): ?>, <?php echo e($profile->country); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($compatibility): ?>
                            <span class="badge bg-primary fs-6"><i class="bi bi-magic me-1"></i><?php echo e($compatibility); ?>% Compatibility</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->id() !== $profileUser->id): ?>
                        <div class="d-flex gap-2 flex-wrap">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isMatched): ?>
                                <a href="<?php echo e(route('conversations.show', $conversationId)); ?>" class="btn btn-success"><i class="bi bi-chat-heart me-2"></i>Message</a>
                            <?php elseif($hasLiked): ?>
                                <form method="POST" action="<?php echo e(route('like.destroy', $profileUser->id)); ?>"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="btn btn-outline-secondary"><i class="bi bi-heart-fill text-danger me-2"></i>Liked</button></form>
                            <?php else: ?>
                                <form method="POST" action="<?php echo e(route('like.store', $profileUser->id)); ?>"><?php echo csrf_field(); ?><button class="btn btn-primary"><i class="bi bi-heart me-2"></i>Like</button></form>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            
                            <button class="btn btn-outline-warning wave-btn" data-user="<?php echo e($profileUser->id); ?>" title="Send a wave">👋 Wave</button>
                            
                            <button class="btn btn-outline-success tip-btn" data-user="<?php echo e($profileUser->id); ?>" data-name="<?php echo e($profileUser->name); ?>" title="Send a tip"><i class="bi bi-coin me-1"></i>Tip</button>
                            <form method="POST" action="<?php echo e(route('report.store', $profileUser->id)); ?>" onsubmit="return confirm('Report this user?')"><?php echo csrf_field(); ?><button class="btn btn-outline-secondary btn-sm"><i class="bi bi-flag"></i></button></form>
                            <button type="button" class="btn btn-outline-danger btn-sm block-btn" data-user="<?php echo e($profileUser->id); ?>" data-name="<?php echo e($profileUser->name); ?>" title="Block user"><i class="bi bi-slash-circle"></i></button>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($profile && $profile->tagline): ?>
                    <blockquote class="blockquote mt-3 mb-0 fs-5 fst-italic text-muted border-start border-primary border-3 ps-3">
                        "<?php echo e($profile->tagline); ?>"
                    </blockquote>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($profile && $profile->about): ?>
                    <div class="mt-4">
                        <h6 class="fw-bold">About <?php echo e($profileUser->name); ?></h6>
                        <p class="mb-0"><?php echo e($profile->about); ?></p>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($profile): ?>
                    <div class="mt-4">
                        <h6 class="fw-bold mb-3">Profile Details</h6>
                        <div class="row g-2 text-center">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($profile->height_cm): ?><div class="col-4 col-md-3"><div class="card border-0 bg-light p-2 rounded-3"><div class="fw-semibold"><?php echo e($profile->height_cm); ?> cm</div><div class="text-muted" style="font-size:.7rem">Height</div></div></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($profile->body_type): ?><div class="col-4 col-md-3"><div class="card border-0 bg-light p-2 rounded-3"><div class="fw-semibold text-capitalize"><?php echo e($profile->body_type); ?></div><div class="text-muted" style="font-size:.7rem">Body Type</div></div></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($profile->relationship_goal): ?><div class="col-4 col-md-3"><div class="card border-0 bg-light p-2 rounded-3"><div class="fw-semibold text-capitalize"><?php echo e(str_replace('_', ' ', $profile->relationship_goal)); ?></div><div class="text-muted" style="font-size:.7rem">Looking for</div></div></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($profile->wants_children !== null): ?><div class="col-4 col-md-3"><div class="card border-0 bg-light p-2 rounded-3"><div class="fw-semibold"><?php echo e($profile->wants_children ? 'Yes' : 'No'); ?></div><div class="text-muted" style="font-size:.7rem">Wants kids</div></div></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($profile->education_level): ?><div class="col-4 col-md-3"><div class="card border-0 bg-light p-2 rounded-3"><div class="fw-semibold text-capitalize"><?php echo e($profile->education_level); ?></div><div class="text-muted" style="font-size:.7rem">Education</div></div></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($profile->smoking_habit): ?><div class="col-4 col-md-3"><div class="card border-0 bg-light p-2 rounded-3"><div class="fw-semibold text-capitalize"><?php echo e($profile->smoking_habit); ?></div><div class="text-muted" style="font-size:.7rem">Smoking</div></div></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($profile->drinking_habit): ?><div class="col-4 col-md-3"><div class="card border-0 bg-light p-2 rounded-3"><div class="fw-semibold text-capitalize"><?php echo e($profile->drinking_habit); ?></div><div class="text-muted" style="font-size:.7rem">Drinking</div></div></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($profile && $profile->mood_status): ?>
                    <div class="mb-3">
                        <span class="badge bg-warning-subtle text-warning-emphasis px-3 py-2 rounded-pill fs-6">
                            <i class="bi bi-emoji-smile me-1"></i><?php echo e($profile->mood_status); ?>

                        </span>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($profile && $profile->interests->isNotEmpty()): ?>
                    <div class="mt-4">
                        <h6 class="fw-bold mb-2">Interests</h6>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $profile->interests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $interest): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="badge bg-primary-subtle text-primary-emphasis me-1 mb-1 px-3 py-2 rounded-pill" style="font-size:.8rem"><?php echo e($interest->name); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


<div class="modal fade" id="tipModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-coin text-warning me-2"></i>Send a Tip</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="tipForm">
        <?php echo csrf_field(); ?>
        <input type="hidden" id="tip-recipient-id" name="recipient_id">
        <div class="modal-body">
          <p class="text-muted small mb-3">Send credits to <strong id="tip-recipient-name"></strong> as a token of appreciation.</p>
          <div class="mb-3">
            <label class="form-label fw-semibold">Amount (credits)</label>
            <input type="number" class="form-control" id="tip-amount" name="amount" min="1" placeholder="e.g. 5" required>
            <div class="form-text">Your balance: <strong id="tip-my-balance">…</strong> credits</div>
          </div>
          <div class="mb-2">
            <label class="form-label fw-semibold">Message <span class="text-muted fw-normal">(optional)</span></label>
            <input type="text" class="form-control" id="tip-message" name="message" maxlength="255" placeholder="e.g. You're amazing!">
          </div>
          <div id="tip-alert" class="d-none"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success" id="tip-submit-btn">
            <span id="tip-spinner" class="spinner-border spinner-border-sm d-none me-1"></span>
            <i class="bi bi-send me-1"></i>Send Tip
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// ── Wave ──────────────────────────────────────────────────────────────────────
document.querySelectorAll('.wave-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
        const userId = btn.dataset.user;
        const csrf   = document.querySelector('meta[name="csrf-token"]').content;
        btn.disabled = true;
        try {
            const res = await fetch('<?php echo e(route("wave.store", ":u")); ?>'.replace(':u', userId), {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' },
                body: JSON.stringify({ emoji: '👋' })
            });
            if (res.ok) {
                btn.textContent = '✅ Waved!';
                btn.classList.remove('btn-outline-warning');
                btn.classList.add('btn-success');
            } else {
                btn.disabled = false;
            }
        } catch {
            btn.disabled = false;
        }
    });
});

// ── Tip modal ─────────────────────────────────────────────────────────────────
const tipModal = new bootstrap.Modal(document.getElementById('tipModal'));
let _tipCurrentBalance = 0;
let _tipBalanceLoaded  = false;

function tipAlert(type, html) {
    const el = document.getElementById('tip-alert');
    el.className = type ? 'alert alert-' + type : 'd-none';
    el.innerHTML = html || '';
}

document.querySelectorAll('.tip-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        // Reset form state
        document.getElementById('tipForm').reset();
        document.getElementById('tip-recipient-id').value = btn.dataset.user;
        document.getElementById('tip-recipient-name').textContent = btn.dataset.name;
        tipAlert(null, '');
        _tipBalanceLoaded = false;

        const submitBtn = document.getElementById('tip-submit-btn');
        const balEl     = document.getElementById('tip-my-balance');
        submitBtn.disabled = true;
        balEl.innerHTML    = '<span class="spinner-border spinner-border-sm" style="width:.9rem;height:.9rem"></span>';

        // Fetch current balance
        fetch('<?php echo e(route("wallet.balance")); ?>')
            .then(r => r.json())
            .then(d => {
                _tipCurrentBalance = parseInt(d.balance ?? 0, 10);
                _tipBalanceLoaded  = true;
                balEl.textContent  = _tipCurrentBalance;
                if (_tipCurrentBalance <= 0) {
                    tipAlert('warning',
                        '<i class="bi bi-exclamation-triangle me-1"></i>' +
                        'You have no credits. <a href="<?php echo e(route("wallet.index")); ?>" class="alert-link fw-semibold">Fund your wallet</a> first.');
                    submitBtn.disabled = true;
                } else {
                    submitBtn.disabled = false;
                }
            })
            .catch(() => {
                _tipBalanceLoaded  = true;
                _tipCurrentBalance = 0;
                balEl.textContent  = '?';
                tipAlert('warning', '<i class="bi bi-exclamation-triangle me-1"></i>Could not load balance. Try refreshing.');
            });

        tipModal.show();
    });
});

// Live amount validation
document.getElementById('tip-amount')?.addEventListener('input', function () {
    if (!_tipBalanceLoaded) return;
    const amount    = parseInt(this.value, 10);
    const submitBtn = document.getElementById('tip-submit-btn');
    if (isNaN(amount) || amount < 1) {
        tipAlert(null, '');
        submitBtn.disabled = _tipCurrentBalance <= 0;
        return;
    }
    if (amount > _tipCurrentBalance) {
        tipAlert('warning',
            '<i class="bi bi-exclamation-triangle me-1"></i>' +
            'Amount (<strong>' + amount + '</strong>) exceeds your balance of <strong>' + _tipCurrentBalance + '</strong> credits.');
        submitBtn.disabled = true;
    } else {
        tipAlert(null, '');
        submitBtn.disabled = false;
    }
});

document.getElementById('tipForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const submitBtn = document.getElementById('tip-submit-btn');
    const spinner   = document.getElementById('tip-spinner');
    const amount    = parseInt(document.getElementById('tip-amount').value, 10);

    if (isNaN(amount) || amount < 1) {
        tipAlert('danger', '<i class="bi bi-exclamation-circle me-1"></i>Please enter a valid amount (minimum 1 credit).');
        return;
    }
    if (_tipBalanceLoaded && amount > _tipCurrentBalance) {
        tipAlert('danger',
            '<i class="bi bi-exclamation-circle me-1"></i>' +
            'Insufficient balance — you only have <strong>' + _tipCurrentBalance + '</strong> credits.');
        return;
    }

    submitBtn.disabled = true;
    spinner.classList.remove('d-none');
    tipAlert(null, '');

    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    const body = new FormData(document.getElementById('tipForm'));
    try {
        const res  = await fetch('<?php echo e(route("tips.send")); ?>', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf },
            body
        });
        let data = {};
        try { data = await res.json(); } catch (_) {}

        if (res.ok && data.success) {
            tipAlert('success', '<i class="bi bi-check-circle me-1"></i>Gift sent! 🎉');
            if (data.new_balance !== undefined) {
                _tipCurrentBalance = data.new_balance;
                document.getElementById('tip-my-balance').textContent = _tipCurrentBalance;
            }
            setTimeout(() => tipModal.hide(), 1800);
        } else {
            let msg = data.error || data.message || 'Something went wrong.';
            if (res.status === 422 && data.errors) {
                msg = Object.values(data.errors).flat().join(' ');
            }
            if (res.status === 429) {
                msg = 'Too many requests — please wait a moment before trying again.';
            }
            tipAlert('danger', '<i class="bi bi-exclamation-circle me-1"></i>' + msg);
        }
    } catch {
        tipAlert('danger', '<i class="bi bi-wifi-off me-1"></i>Network error — please try again.');
    } finally {
        submitBtn.disabled = false;
        spinner.classList.add('d-none');
    }
});

// ── Block user ───────────────────────────────────────────────────────────────
function blockToast(msg, type) {
    const icons     = { success: 'bi-slash-circle-fill', danger: 'bi-exclamation-circle' };
    const container = document.getElementById('toastContainer');
    if (!container) { alert(msg); return; }
    const el = document.createElement('div');
    el.className = 'toast align-items-center text-bg-' + type + ' border-0';
    el.setAttribute('role', 'alert');
    el.setAttribute('aria-atomic', 'true');
    el.innerHTML =
        '<div class="d-flex"><div class="toast-body fw-semibold">' +
        '<i class="bi ' + (icons[type] || 'bi-info-circle') + ' me-2"></i>' + msg +
        '</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>';
    container.appendChild(el);
    if (window.bootstrap?.Toast) { new bootstrap.Toast(el, { delay: 3500 }).show(); }
}

document.querySelectorAll('.block-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const name = btn.dataset.name || 'this user';
        if (!confirm('Block ' + name + '?\n\nThey won\'t be able to contact you or see your profile.')) return;

        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        fetch('<?php echo e(route("block.store", ":u")); ?>'.replace(':u', btn.dataset.user), {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' },
        })
        .then(r => r.json())
        .then(d => {
            if (d.blocked) {
                blockToast(name + ' has been blocked.', 'success');
                // Redirect to discover after a short delay so user sees the toast
                setTimeout(() => { window.location.href = '<?php echo e(route("discover.index")); ?>'; }, 1800);
            } else {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-slash-circle"></i>';
                blockToast(d.error || 'Could not block user.', 'danger');
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-slash-circle"></i>';
            blockToast('Network error — please try again.', 'danger');
        });
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\dating\resources\views\profile\show.blade.php ENDPATH**/ ?>