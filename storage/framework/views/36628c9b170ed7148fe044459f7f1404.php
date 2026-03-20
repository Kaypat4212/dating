<?php $__env->startComponent('mail::message'); ?>
# It's a Match! 💕

Hi **<?php echo e($user->name); ?>**, you and **<?php echo e($otherUser->name); ?>** liked each other — it's a match!

<?php $__env->startComponent('mail::panel'); ?>
This is your moment. The first message sets the tone — be genuine, be curious, be yourself. 😊
<?php echo $__env->renderComponent(); ?>

<?php $__env->startComponent('mail::button', ['url' => $conversationUrl, 'color' => 'red']); ?>
Send a Message 💬
<?php echo $__env->renderComponent(); ?>

Don't keep them waiting — your perfect match is ready to chat!

With love,
**The <?php echo e($appName); ?> Team**

---
<small>You matched via <?php echo e($appName); ?>. Manage your notification preferences in [account settings](<?php echo e($appUrl); ?>/account/settings).</small>
<?php echo $__env->renderComponent(); ?>
<?php /**PATH C:\xampp\htdocs\dating\resources\views\emails\new-match.blade.php ENDPATH**/ ?>