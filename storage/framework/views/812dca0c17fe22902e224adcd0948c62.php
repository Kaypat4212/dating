<?php $__env->startComponent('mail::message'); ?>
# Re: <?php echo e($type); ?> — <?php echo e($record->title); ?>


Hi <?php echo e($record->name); ?>,

Thank you for taking the time to submit your <?php echo e(strtolower($type)); ?>. Here is our response:

<?php $__env->startComponent('mail::panel'); ?>
<?php echo e($response); ?>

<?php echo $__env->renderComponent(); ?>

**Your submission:**

> **<?php echo e($record->title); ?>**
> <?php echo e($record->body); ?>


---

<?php $__env->startComponent('mail::button', ['url' => route('pages.feature-request')]); ?>
Submit Another Request
<?php echo $__env->renderComponent(); ?>

Thanks again for helping us improve <?php echo e($siteName); ?>!

Warm regards,
**The <?php echo e($siteName); ?> Team**

---
<small>This is a reply to a <?php echo e(strtolower($type)); ?> you submitted at <?php echo e($siteName); ?>. If you did not submit this, please ignore this email.</small>
<?php echo $__env->renderComponent(); ?>
<?php /**PATH C:\xampp\htdocs\dating\resources\views\emails\feature-request-reply.blade.php ENDPATH**/ ?>