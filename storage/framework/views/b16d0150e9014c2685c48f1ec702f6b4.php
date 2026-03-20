<?php $__env->startComponent('mail::message'); ?>
# 💳 Wallet Credited — <?php echo e($credits); ?> Credits Added!

Hi **<?php echo e($user->name); ?>**,

Great news! Your deposit request has been **approved** and your wallet has been credited.

<?php $__env->startComponent('mail::panel'); ?>
| | |
|---|---|
| **Credits Added** | <?php echo e(number_format($credits)); ?> credits |
| **Transaction ID** | `<?php echo e($txid ?: 'N/A'); ?>` |
| **New Balance** | <?php echo e(number_format($user->credit_balance)); ?> credits |
<?php echo $__env->renderComponent(); ?>

Your credits are available immediately. Use them to send gifts, tip other members, or unlock premium features.

<?php $__env->startComponent('mail::button', ['url' => $walletUrl, 'color' => 'green']); ?>
View My Wallet →
<?php echo $__env->renderComponent(); ?>

Thank you for topping up,
**The <?php echo e($appName); ?> Team**

---
<small>If you did not submit this deposit request, please [contact support](<?php echo e($appUrl); ?>).</small>
<?php echo $__env->renderComponent(); ?>
<?php /**PATH C:\xampp\htdocs\dating\resources\views\emails\wallet-funded.blade.php ENDPATH**/ ?>