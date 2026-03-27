
<?php $__env->startSection('title', 'Premium Membership'); ?>
<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="text-center mb-5">
                <span class="badge bg-warning text-dark fs-6 px-3 py-2 mb-3"><i class="bi bi-star-fill me-2"></i>Premium</span>
                <h2 class="fw-bold">Unlock the Full Experience</h2>
                <p class="text-muted lead">See who liked you, send unlimited likes, and more.</p>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->isPremiumActive()): ?>
            <div class="alert alert-success mb-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <i class="bi bi-star-fill me-2"></i>You are currently a <strong>Premium</strong> member
                        (<?php echo e(auth()->user()->premium_plan === '365day' ? '1 Year' : (auth()->user()->premium_plan === '90day' ? '3 Months' : '1 Month')); ?> plan).
                        Expires: <strong><?php echo e(auth()->user()->premium_expires_at->format('F j, Y')); ?></strong>
                    </div>
                    <?php $lastPayment = auth()->user()->premiumPayments()->whereIn('status',['approved','pending'])->latest()->first(); ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lastPayment): ?>
                    <a href="<?php echo e(route('premium.invoice', $lastPayment)); ?>" class="btn btn-sm btn-outline-success" target="_blank">
                        <i class="bi bi-receipt me-1"></i> View Invoice
                    </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <?php elseif($pending): ?>
            <div class="alert alert-info mb-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <i class="bi bi-hourglass-split me-2"></i>Your payment is <strong>pending review</strong>. We will activate your subscription within 24 hours.
                    </div>
                    <a href="<?php echo e(route('premium.invoice', $pending)); ?>" class="btn btn-sm btn-outline-info" target="_blank">
                        <i class="bi bi-receipt me-1"></i> View Invoice
                    </a>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('upgrade_success')): ?>
            <div class="alert alert-success d-flex justify-content-between align-items-center mb-4">
                <span><i class="bi bi-check-circle-fill me-2"></i><?php echo e(session('upgrade_success')); ?></span>
                <?php $latestPending = auth()->user()->premiumPayments()->where('status','pending')->latest()->first(); ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($latestPending): ?>
                <a href="<?php echo e(route('premium.invoice', $latestPending)); ?>" class="btn btn-sm btn-outline-success" target="_blank">
                    <i class="bi bi-receipt me-1"></i> View Invoice
                </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <div class="row g-3 mb-5">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [['bi-eye-fill','See Who Liked You','Know exactly who is interested in you',null],['bi-infinity','Unlimited Likes','Never run out of likes',null],['bi-rocket-takeoff-fill','Profile Boost','Appear at the top of discovery',null],['bi-geo-alt-fill','Unlimited Location Updates','Update your location as often as you like — free users are limited to 2 updates',null],['bi-graph-up-arrow','Advanced Filters','Filter by body type, education, religion & more',null],['bi-shield-check-fill','Read Receipts','See when your messages are read',null],['bi-arrow-through-heart-fill','SuperLikes','Stand out with 5 SuperLikes per day',null],['bi-image','Share Photos & Audio','Send images and voice clips in chat',null],['bi-eye-slash-fill','Hide Last Seen','Let no one see when you were last active','365day']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$icon,$title,$desc,$planOnly]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-6 col-md-4">
                    <div class="card border-0 shadow-sm h-100 p-3 text-center <?php echo e($planOnly ? 'border border-warning border-2' : ''); ?>">
                        <i class="bi <?php echo e($icon); ?> display-5 <?php echo e($planOnly ? 'text-warning' : 'text-primary'); ?> mb-2"></i>
                        <h6 class="fw-bold mb-1"><?php echo e($title); ?></h6>
                        <p class="text-muted mb-0 small"><?php echo e($desc); ?></p>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($planOnly): ?>
                        <span class="badge bg-warning text-dark mt-2 small">1-Year Plan Only</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <h5 class="fw-bold mb-3 text-center">Choose Your Plan</h5>
            <div class="row g-3 mb-5">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-4">
                    <div class="card border-0 shadow <?php echo e($key === '90day' ? 'border border-primary border-2' : ''); ?> h-100">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($key === '90day'): ?>
                        <div class="card-header bg-primary text-white text-center fw-bold py-1 small">Most Popular</div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <div class="card-body text-center p-4">
                            <h5 class="fw-bold mb-1"><?php echo e($plan['label']); ?></h5>
                            <div class="display-5 fw-bold text-primary my-3">$<?php echo e($plan['price']); ?></div>
                            <p class="text-muted small mb-0"><?php echo e($key === '30day' ? 'Billed monthly' : ($key === '90day' ? 'Save 33%' : 'Best value, save 58%')); ?></p>
                        </div>
                        <div class="card-footer bg-transparent text-center pb-3">
                            <button class="btn <?php echo e($key === '90day' ? 'btn-primary' : 'btn-outline-primary'); ?> px-4 fw-bold select-plan"
                                data-plan="<?php echo e($key); ?>" data-price="<?php echo e($plan['price']); ?>" data-label="<?php echo e($plan['label']); ?>">
                                Select Plan
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($upgradeOptions): ?>
            <div class="card border-0 shadow mb-5" id="upgradeSection">
                <div class="card-header bg-gradient text-white fw-bold py-3" style="background:linear-gradient(135deg,#7c3aed,#db2777)">
                    <i class="bi bi-arrow-up-circle-fill me-2"></i>Upgrade Your Plan
                </div>
                <div class="card-body p-4">
                    <p class="text-muted mb-1">
                        You are on the <strong><?php echo e($upgradeOptions['current_label']); ?></strong> plan.
                        You have <strong><?php echo e($upgradeOptions['remaining_days']); ?> days</strong> remaining
                        (valued at <strong>$<?php echo e(number_format($upgradeOptions['credit'], 2)); ?></strong>).
                    </p>
                    <p class="text-muted small mb-4">This credit will be applied to your upgrade — you only pay the difference.</p>

                    <div class="row g-3 mb-4">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $upgradeOptions['options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $upgradeKey => $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-6">
                            <div class="card h-100 upgrade-plan-card <?php echo e($upgradeKey === '365day' ? 'border-primary border-2' : ''); ?>"
                                 style="cursor:pointer" data-plan="<?php echo e($upgradeKey); ?>"
                                 data-price="<?php echo e($opt['price']); ?>"
                                 data-due="<?php echo e($opt['amount_due']); ?>"
                                 data-credit="<?php echo e($opt['credit']); ?>"
                                 data-label="<?php echo e($opt['label']); ?>">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($upgradeKey === '365day'): ?>
                                <div class="card-header bg-primary text-white text-center fw-bold py-1 small">Best Value</div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <div class="card-body text-center p-3">
                                    <h5 class="fw-bold mb-1"><?php echo e($opt['label']); ?></h5>
                                    <div class="text-decoration-line-through text-muted small">$<?php echo e(number_format($opt['price'], 2)); ?> full price</div>
                                    <div class="text-success small">−$<?php echo e(number_format($opt['credit'], 2)); ?> credit</div>
                                    <div class="display-6 fw-bold text-primary my-2">$<?php echo e(number_format($opt['amount_due'], 2)); ?></div>
                                    <div class="text-muted small">you pay today</div>
                                </div>
                                <div class="card-footer bg-transparent text-center pb-3">
                                    <button type="button" class="btn btn-outline-primary px-4 fw-bold select-upgrade"
                                        data-plan="<?php echo e($upgradeKey); ?>"
                                        data-due="<?php echo e($opt['amount_due']); ?>"
                                        data-credit="<?php echo e($opt['credit']); ?>"
                                        data-label="<?php echo e($opt['label']); ?>">
                                        Upgrade to <?php echo e($opt['label']); ?>

                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    
                    <div id="upgradePaymentForm" style="display:none">
                        <hr>
                        <h6 class="fw-bold mb-1" id="upgradeFormTitle">Complete Upgrade Payment</h6>
                        <p class="text-muted small mb-3" id="upgradeFormDesc"></p>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($wallets->isEmpty()): ?>
                        <div class="alert alert-warning">No crypto wallets configured yet. Please check back soon or contact support.</div>
                        <?php else: ?>
                        <form method="POST" action="<?php echo e(route('premium.upgrade.submit')); ?>" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="plan" id="upgradeHiddenPlan">

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Choose Cryptocurrency</label>
                                <div class="row g-2">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $wallets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wallet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-auto">
                                        <input type="radio" class="btn-check" name="crypto_currency"
                                               id="uw<?php echo e($wallet->id); ?>" value="<?php echo e($wallet->currency); ?>"
                                               data-address="<?php echo e($wallet->address); ?>" required>
                                        <label class="btn btn-outline-secondary" for="uw<?php echo e($wallet->id); ?>">
                                            <?php echo e($wallet->currency); ?>

                                        </label>
                                    </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>

                            <div class="mb-3" id="upgradeWalletBox" style="display:none">
                                <label class="form-label fw-semibold">Send to this address:</label>
                                <div class="input-group">
                                    <span class="form-control font-monospace" id="upgradeWalletAddress" style="user-select:all;overflow-x:auto"></span>
                                    <button type="button" class="btn btn-outline-secondary" onclick="navigator.clipboard.writeText(document.getElementById('upgradeWalletAddress').textContent)">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                </div>
                                <input type="hidden" name="wallet_address" id="upgradeHiddenWallet">
                                <div class="form-text text-danger fw-semibold" id="upgradePayAmount"></div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Transaction Hash (TX ID) <span class="text-muted fw-normal small">— optional if you upload a screenshot</span></label>
                                <input type="text" name="tx_hash"
                                       class="form-control font-monospace <?php $__errorArgs = ['upgrade_tx_hash'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       placeholder="0x..." value="<?php echo e(old('tx_hash')); ?>">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['upgrade_tx_hash'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Payment Screenshot / Receipt <span class="text-muted fw-normal small">— optional if you provided a TX hash</span></label>
                                <input type="file" name="proof_image" id="upgrade_proof_image"
                                       accept="image/jpeg,image/png,image/webp,application/pdf"
                                       class="form-control <?php $__errorArgs = ['proof_image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['proof_image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <div class="form-text">Max 5 MB (JPG, PNG, WEBP, PDF).</div>
                                <div id="upgradeProofPreview" class="mt-2" style="display:none">
                                    <img id="upgradeProofImg" src="" alt="Preview" class="rounded border" style="max-height:160px;object-fit:contain">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success fw-bold px-5">
                                <i class="bi bi-check-circle me-2"></i>Submit Upgrade Payment for Review
                            </button>
                        </form>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! auth()->user()->isPremiumActive() && ! $pending): ?>
            <div class="card border-0 shadow p-4" id="paymentForm" style="display:none !important">
                <h5 class="fw-bold mb-1" id="payFormTitle">Complete Payment</h5>
                <p class="text-muted small mb-4">Send the exact amount to one of our wallet addresses below, then paste your transaction hash to confirm.</p>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($wallets->isEmpty()): ?>
                <div class="alert alert-warning">No crypto wallets configured yet. Please check back soon or contact support.</div>
                <?php else: ?>
                <form method="POST" action="<?php echo e(route('premium.submit')); ?>" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="plan" id="hiddenPlan">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Choose Cryptocurrency</label>
                        <div class="row g-2">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $wallets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wallet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-auto">
                                <input type="radio" class="btn-check" name="crypto_currency" id="w<?php echo e($wallet->id); ?>" value="<?php echo e($wallet->currency); ?>"
                                    data-address="<?php echo e($wallet->address); ?>" required>
                                <label class="btn btn-outline-secondary" for="w<?php echo e($wallet->id); ?>">
                                    <?php echo e($wallet->currency); ?>

                                </label>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                    <div class="mb-3" id="walletAddressBox" style="display:none">
                        <label class="form-label fw-semibold">Send to this address:</label>
                        <div class="input-group">
                            <span class="form-control font-monospace" id="walletAddress" style="user-select:all;overflow-x:auto"></span>
                            <button type="button" class="btn btn-outline-secondary" onclick="navigator.clipboard.writeText(document.getElementById('walletAddress').textContent)"><i class="bi bi-clipboard"></i></button>
                        </div>
                        <input type="hidden" name="wallet_address" id="hiddenWallet">
                        <div class="form-text text-danger fw-semibold" id="payAmount"></div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Transaction Hash (TX ID) <span class="text-muted fw-normal small">— optional if you upload a screenshot</span></label>
                        <input type="text" name="tx_hash" class="form-control font-monospace <?php $__errorArgs = ['tx_hash'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="0x..." value="<?php echo e(old('tx_hash')); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['tx_hash'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <div class="form-text">Paste the transaction hash/ID here so we can verify on-chain.</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Payment Screenshot / Receipt <span class="text-muted fw-normal small">— optional if you provided a TX hash</span></label>
                        <input type="file" name="proof_image" id="proof_image"
                            accept="image/jpeg,image/png,image/webp,application/pdf"
                            class="form-control <?php $__errorArgs = ['proof_image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['proof_image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <div class="form-text">Upload a screenshot of your transaction or a PDF receipt. Max 5 MB (JPG, PNG, WEBP, PDF).</div>
                        <div id="proofPreview" class="mt-2" style="display:none">
                            <img id="proofImg" src="" alt="Preview" class="rounded border" style="max-height:160px;object-fit:contain">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success fw-bold px-5"><i class="bi bi-check-circle me-2"></i>Submit Payment for Review</button>
                </form>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// ── Upgrade plan selection ───────────────────────────────────────────────────
document.querySelectorAll('.select-upgrade').forEach(btn => {
    btn.addEventListener('click', () => {
        const plan   = btn.dataset.plan;
        const label  = btn.dataset.label;
        const due    = parseFloat(btn.dataset.due).toFixed(2);
        const credit = parseFloat(btn.dataset.credit).toFixed(2);
        document.getElementById('upgradeHiddenPlan').value = plan;
        document.getElementById('upgradeFormTitle').textContent = `Upgrade to ${label} — Pay $${due} today`;
        document.getElementById('upgradeFormDesc').textContent =
            `$${credit} credit from your current plan has been deducted. Send exactly $${due} in crypto.`;
        const form = document.getElementById('upgradePaymentForm');
        form.style.removeProperty('display');
        form.scrollIntoView({ behavior: 'smooth' });
    });
});
// Wallet address reveal for upgrade form
document.querySelectorAll('[name="crypto_currency"]').forEach(r => {
    if (!r.closest('form[action*="upgrade"]')) return;
    r.addEventListener('change', () => {
        document.getElementById('upgradeWalletAddress').textContent = r.dataset.address;
        document.getElementById('upgradeHiddenWallet').value = r.dataset.address;
        document.getElementById('upgradeWalletBox').style.removeProperty('display');
    });
});
document.getElementById('upgrade_proof_image')?.addEventListener('change', function () {
    const file = this.files[0];
    const preview = document.getElementById('upgradeProofPreview');
    const img = document.getElementById('upgradeProofImg');
    if (file && file.type.startsWith('image/')) {
        img.src = URL.createObjectURL(file);
        preview.style.removeProperty('display');
    } else {
        preview.style.display = 'none';
    }
});
// ── New subscription plan selection ─────────────────────────────────────────
document.querySelectorAll('.select-plan').forEach(btn => {
    btn.addEventListener('click', () => {
        const plan = btn.dataset.plan;
        const label = btn.dataset.label;
        const price = btn.dataset.price;
        document.getElementById('hiddenPlan').value = plan;
        document.getElementById('payFormTitle').textContent = `Complete Payment — ${label}`;
        document.getElementById('paymentForm').style.removeProperty('display');
        document.getElementById('paymentForm').scrollIntoView({behavior:'smooth'});
    });
});
document.querySelectorAll('[name="crypto_currency"]').forEach(r => {
    r.addEventListener('change', () => {
        const addr = r.dataset.address;
        document.getElementById('walletAddress').textContent = addr;
        document.getElementById('hiddenWallet').value = addr;
        document.getElementById('walletAddressBox').style.removeProperty('display');
    });
});
document.getElementById('proof_image')?.addEventListener('change', function () {
    const file = this.files[0];
    const preview = document.getElementById('proofPreview');
    const img = document.getElementById('proofImg');
    if (file && file.type.startsWith('image/')) {
        img.src = URL.createObjectURL(file);
        preview.style.removeProperty('display');
    } else {
        preview.style.display = 'none';
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\dating\resources\views\premium\show.blade.php ENDPATH**/ ?>