<?php $__env->startComponent('mail::message'); ?>
# You're Now a Premium Member! 🌟

Hi **<?php echo e($user->name); ?>**, welcome to <?php echo e($appName); ?> Premium! Your **<?php echo e(ucfirst($plan)); ?>** plan is now active.

<?php $__env->startComponent('mail::panel'); ?>
**Your Premium benefits are unlocked:**

- 👁️ See who liked your profile — no more guessing
- 💬 Unlimited messages with all your matches
- 🚀 Weekly profile boost — appear at the top of the deck
- 🌍 Browse profiles from anywhere in the world
- 🔒 Advanced privacy controls

**Active until:** <?php echo e($expiresAt); ?>

<?php echo $__env->renderComponent(); ?>

Make the most of it — try boosting your profile today and watch your matches soar!

<?php $__env->startComponent('mail::button', ['url' => $appUrl . '/discover', 'color' => 'red']); ?>
Start Exploring →
<?php echo $__env->renderComponent(); ?>

Enjoy every perk,
**The <?php echo e($appName); ?> Team**

---
<small>Questions about your subscription? Visit your [account settings](<?php echo e($appUrl); ?>/account/settings).</small>
<?php echo $__env->renderComponent(); ?>
<?php /**PATH C:\xampp\htdocs\dating\resources\views\emails\premium.blade.php ENDPATH**/ ?>