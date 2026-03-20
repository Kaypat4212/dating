<?php $__env->startComponent('mail::message'); ?>
# Someone Liked Your Profile! 😍

Hi **<?php echo e($user->name); ?>**, great news — someone just liked your profile on <?php echo e($appName); ?>!

<?php $__env->startComponent('mail::panel'); ?>
**Want to know who it is?**

Upgrade to Premium to see exactly who liked you, send unlimited messages, and boost your profile to the top. 🚀
<?php echo $__env->renderComponent(); ?>

You could be one step away from your perfect match!

<?php $__env->startComponent('mail::button', ['url' => $appUrl . '/premium', 'color' => 'red']); ?>
See Who Liked You →
<?php echo $__env->renderComponent(); ?>

Don't miss your chance,
**The <?php echo e($appName); ?> Team**

---
<small>Manage your notification preferences in [account settings](<?php echo e($appUrl); ?>/account/settings).</small>
<?php echo $__env->renderComponent(); ?>
<?php /**PATH C:\xampp\htdocs\dating\resources\views\emails\profile-liked.blade.php ENDPATH**/ ?>