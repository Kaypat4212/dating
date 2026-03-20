<?php $__env->startComponent('mail::message'); ?>
# New Login Detected

Hi **<?php echo e($user->name); ?>**, we noticed a new sign-in to your <?php echo e($appName); ?> account.

<?php $__env->startComponent('mail::panel'); ?>
**Time:** <?php echo e($loginTime); ?>

**IP Address:** <?php echo e($ip); ?>

**Device / Browser:** <?php echo e($device); ?>

<?php echo $__env->renderComponent(); ?>

If this was you, no action is needed — enjoy the app! 💕

If you don't recognise this login, please **secure your account immediately** by changing your password.

<?php $__env->startComponent('mail::button', ['url' => $appUrl . '/profile/settings', 'color' => 'red']); ?>
Secure My Account
<?php echo $__env->renderComponent(); ?>

Stay safe,
**The <?php echo e($appName); ?> Team**

---
<small>You're receiving this email because login alerts are enabled for your account. You can turn them off from your account notification settings.</small>
<?php echo $__env->renderComponent(); ?>
<?php /**PATH C:\xampp\htdocs\dating\resources\views\emails\login-alert.blade.php ENDPATH**/ ?>