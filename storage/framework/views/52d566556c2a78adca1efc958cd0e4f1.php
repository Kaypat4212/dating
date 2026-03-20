<?php $__env->startComponent('mail::message'); ?>
# Your Premium Has Expired 💔

Hi **<?php echo e($user->name); ?>**, your <?php echo e($appName); ?> Premium subscription has expired and your account is now on the free plan.

<?php $__env->startComponent('mail::panel'); ?>
**You're missing out on:**

- 👁️ See who liked your profile
- 💬 Unlimited messaging
- 🚀 Profile boost
- 🌍 Global browsing
<?php echo $__env->renderComponent(); ?>

Your matches are still here — renew today and get straight back to connecting!

<?php $__env->startComponent('mail::button', ['url' => $appUrl . '/premium', 'color' => 'red']); ?>
Renew Premium →
<?php echo $__env->renderComponent(); ?>

We hope to see you back at full speed soon,
**The <?php echo e($appName); ?> Team**

---
<small>Manage your account at [account settings](<?php echo e($appUrl); ?>/account/settings).</small>
<?php echo $__env->renderComponent(); ?>
<?php /**PATH C:\xampp\htdocs\dating\resources\views\emails\premium-expired.blade.php ENDPATH**/ ?>