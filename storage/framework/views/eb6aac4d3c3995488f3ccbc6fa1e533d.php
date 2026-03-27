<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <?php
        $faviconPath = \App\Models\SiteSetting::get('site_favicon');
        $faviconUrl  = $faviconPath ? asset('storage/' . $faviconPath) : asset('favicon.svg');
        $faviconMime = str_ends_with($faviconUrl, '.svg') ? 'image/svg+xml' : 'image/png';
        $touchPath   = \App\Models\SiteSetting::get('site_apple_touch_icon');
        $touchUrl    = $touchPath ? asset('storage/' . $touchPath) : $faviconUrl;
    ?>
    <link rel="icon" href="<?php echo e($faviconUrl); ?>" type="<?php echo e($faviconMime); ?>">
    <link rel="shortcut icon" href="<?php echo e($faviconUrl); ?>">
    <link rel="apple-touch-icon" href="<?php echo e($touchUrl); ?>">
    <title><?php echo e(config('app.name', 'Admin')); ?></title>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

    <?php echo app('Illuminate\Foundation\Vite')(['resources/scss/app.scss', 'resources/js/app.js']); ?>
    <?php echo $__env->yieldPushContent('head'); ?>
</head>
<body class="filament-body">
    <?php echo e($slot); ?>

    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\dating\resources\views\vendor\filament-panels\layouts\app.blade.php ENDPATH**/ ?>