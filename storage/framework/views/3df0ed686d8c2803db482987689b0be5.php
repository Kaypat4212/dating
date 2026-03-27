<?php $__env->startComponent('mail::message'); ?>
# New Message from <?php echo e($sender->name); ?> 💬

Hi **<?php echo e($user->name); ?>**, you have a new message waiting!

<?php $__env->startComponent('mail::panel'); ?>
**<?php echo e($sender->name); ?> says:**

*"<?php echo e($preview); ?>"*
<?php echo $__env->renderComponent(); ?>

Don't leave them waiting — log in and reply now!

<?php $__env->startComponent('mail::button', ['url' => $conversationUrl, 'color' => 'red']); ?>
Read & Reply
<?php echo $__env->renderComponent(); ?>

Happy chatting,
**The <?php echo e($appName); ?> Team**

---
<small>You're receiving this because you have email notifications enabled. Manage your preferences in [account settings](<?php echo e($appUrl); ?>/account/settings).</small>
<?php echo $__env->renderComponent(); ?>
<?php /**PATH C:\xampp\htdocs\dating\resources\views\emails\new-message.blade.php ENDPATH**/ ?>