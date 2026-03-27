<?php $__env->startComponent('mail::message'); ?>
# ❌ Deposit Request Rejected

Hi **<?php echo e($user->name); ?>**,

Unfortunately, your deposit request of **<?php echo e(number_format($credits)); ?> credits** could not be approved.

<?php $__env->startComponent('mail::panel'); ?>
| | |
|---|---|
| **Credits Requested** | <?php echo e(number_format($credits)); ?> credits |
| **Transaction ID** | `<?php echo e($txid ?: 'N/A'); ?>` |
| **Status** | ❌ Rejected |
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($reason): ?>
| **Reason** | <?php echo e($reason); ?> |
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php echo $__env->renderComponent(); ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($reason): ?>
**Admin note:** <?php echo e($reason); ?>

<?php else: ?>
Your request could not be verified. This may be due to an unconfirmed transaction, incorrect TXID, or an unreadable proof image.
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

**What you can do:**
- Double-check your TXID and resubmit with a clear screenshot of the confirmed transaction
- Ensure the transaction has enough confirmations on the blockchain
- [Contact support](<?php echo e($appUrl); ?>) if you believe this was an error

<?php $__env->startComponent('mail::button', ['url' => $walletUrl, 'color' => 'red']); ?>
Submit a New Request →
<?php echo $__env->renderComponent(); ?>

We're happy to help,
**The <?php echo e($appName); ?> Team**
<?php echo $__env->renderComponent(); ?>
<?php /**PATH C:\xampp\htdocs\dating\resources\views\emails\wallet-deposit-rejected.blade.php ENDPATH**/ ?>