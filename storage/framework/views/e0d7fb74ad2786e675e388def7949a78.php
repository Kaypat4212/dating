<?php $__env->startComponent('mail::message'); ?>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($approved): ?>
# ✅ Withdrawal Approved — Processing Now

Hi **<?php echo e($user->name); ?>**,

Your withdrawal request has been **approved** and is being processed. Funds will be sent to your nominated wallet shortly.

<?php $__env->startComponent('mail::panel'); ?>
| | |
|---|---|
| **Credits Withdrawn** | <?php echo e(number_format($credits)); ?> credits |
| **Payout To** | `<?php echo e($destination); ?>` |
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($currency): ?>
| **Currency** | <?php echo e($currency); ?><?php echo e($network ? ' (' . $network . ')' : ''); ?> |
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
| **Status** | ✅ Approved – processing |
<?php echo $__env->renderComponent(); ?>

Payouts are typically processed within 24–48 hours. You'll receive crypto at the address listed above once it's sent.

<?php $__env->startComponent('mail::button', ['url' => $walletUrl, 'color' => 'green']); ?>
View Wallet →
<?php echo $__env->renderComponent(); ?>

<?php else: ?>
# ❌ Withdrawal Request Rejected

Hi **<?php echo e($user->name); ?>**,

Your withdrawal request of **<?php echo e(number_format($credits)); ?> credits** has been **rejected** and your credits have been **refunded** to your wallet.

<?php $__env->startComponent('mail::panel'); ?>
| | |
|---|---|
| **Credits Refunded** | <?php echo e(number_format($credits)); ?> credits |
| **Destination (unused)** | `<?php echo e($destination); ?>` |
| **Status** | ❌ Rejected – credits refunded |
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($reason): ?>
| **Reason** | <?php echo e($reason); ?> |
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php echo $__env->renderComponent(); ?>

Your credits are back in your wallet and available to use or withdraw again.

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($reason): ?>
**Admin note:** <?php echo e($reason); ?>

<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php $__env->startComponent('mail::button', ['url' => $walletUrl, 'color' => 'red']); ?>
View My Wallet →
<?php echo $__env->renderComponent(); ?>

<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

Thank you for your patience,
**The <?php echo e($appName); ?> Team**

---
<small>Questions about your withdrawal? [Contact support](<?php echo e($appUrl); ?>).</small>
<?php echo $__env->renderComponent(); ?>
<?php /**PATH C:\xampp\htdocs\dating\resources\views\emails\wallet-withdrawal-processed.blade.php ENDPATH**/ ?>