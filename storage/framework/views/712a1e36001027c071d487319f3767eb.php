<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" data-bs-theme="light">
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
    <link rel="manifest" href="<?php echo e(route('pwa.manifest')); ?>">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="theme-color" content="#e91e63">
    <?php
        $appName = \App\Models\SiteSetting::get('site_name', config('app.name'));
        $fallbackTitle = \App\Models\SiteSetting::get('seo_default_title') ?: ($appName . ' — Find Your Match');
    ?>
    <title><?php if (! empty(trim($__env->yieldContent('title')))): ?><?php echo $__env->yieldContent('title'); ?> &mdash; <?php echo e($appName); ?><?php else: ?><?php echo e($fallbackTitle); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></title>
    <?php echo $__env->make('partials.seo-meta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <script>
        (function(){
            var t = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', t);
        })();
    </script>

    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css">

    <?php echo app('Illuminate\Foundation\Vite')(['resources/scss/app.scss', 'resources/js/app.js']); ?>
    <?php echo $__env->yieldPushContent('head'); ?>
    <?php echo $__env->yieldPushContent('styles'); ?>
    <style>
        /* Mobile: dropdown flows in-place below the button, full width */
        @media (max-width: 991.98px) {
            .navbar-dropdown-menu {
                position: static !important;
                width: 100% !important;
                margin-top: 6px !important;
                transform: none !important;
                box-shadow: none !important;
            }
        }
        /* Desktop: absolute right-aligned under the button */
        @media (min-width: 992px) {
            .dropdown .navbar-dropdown-menu {
                position: absolute !important;
                right: 0 !important;
                left: auto !important;
                top: 100% !important;
                transform: none !important;
                min-width: 260px;
                z-index: 1050;
            }
        }
        /* Unread message blinking dot on bottom nav */
        @keyframes msgPulse {
            0%, 100% { opacity:1; transform:scale(1); }
            50%       { opacity:.5; transform:scale(1.4); }
        }
        .bnav-msg-dot {
            position: absolute;
            top: 3px;
            right: calc(50% - 14px);
            width: 9px;
            height: 9px;
            background: #ef4444;
            border-radius: 50%;
            border: 2px solid var(--bs-body-bg, #fff);
            animation: msgPulse 1.1s ease-in-out infinite;
            display: block;
        }
        .bnav-msg-dot.d-none { display: none !important; }
    </style>
</head>
<body>


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('impersonating_id')): ?>
<div class="alert alert-warning alert-dismissible mb-0 rounded-0 border-0 border-bottom d-flex align-items-center justify-content-center gap-3 py-2" style="position:sticky;top:0;z-index:2000;">
    <i class="bi bi-person-fill-gear fs-5"></i>
    <span>You are currently <strong>logged in as <?php echo e(auth()->user()->name); ?></strong> (impersonating). All actions affect this real user.</span>
    <a href="<?php echo e(route('impersonate.leave')); ?>" class="btn btn-sm btn-dark ms-2">
        <i class="bi bi-arrow-left-circle me-1"></i>Return to Admin
    </a>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


<?php
    $unreadCount    = auth()->check() ? auth()->user()->unreadNotifications()->count() : 0;
    $unreadMsgCount = 0;
    if (auth()->check()) {
        $uid = auth()->id();
        $unreadMsgCount = \App\Models\Message::whereHas('conversation.match', function ($q) use ($uid) {
            $q->where('user1_id', $uid)->orWhere('user2_id', $uid);
        })->where('sender_id', '!=', $uid)->whereNull('read_at')->count();
    }
    $navPhoto = auth()->check() ? auth()->user()->primaryPhoto : null;
?>
<nav class="navbar navbar-expand-lg sticky-top shadow-sm" id="mainNav">
    <div class="container">

        
        <a class="navbar-brand fw-bold d-flex align-items-center text-decoration-none" href="<?php echo e(route('home')); ?>">
            <?php if (isset($component)) { $__componentOriginal0e3e854f1972cb532cc8b5bc0ace80b0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0e3e854f1972cb532cc8b5bc0ace80b0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.site-logo','data' => ['size' => 'md']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('site-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 'md']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0e3e854f1972cb532cc8b5bc0ace80b0)): ?>
<?php $attributes = $__attributesOriginal0e3e854f1972cb532cc8b5bc0ace80b0; ?>
<?php unset($__attributesOriginal0e3e854f1972cb532cc8b5bc0ace80b0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0e3e854f1972cb532cc8b5bc0ace80b0)): ?>
<?php $component = $__componentOriginal0e3e854f1972cb532cc8b5bc0ace80b0; ?>
<?php unset($__componentOriginal0e3e854f1972cb532cc8b5bc0ace80b0); ?>
<?php endif; ?>
        </a>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
        
        <div class="d-flex d-lg-none align-items-center gap-1 ms-auto me-2">
            <a href="<?php echo e(route('notifications.index')); ?>" class="btn btn-sm btn-outline-secondary position-relative">
                <i class="bi bi-bell"></i>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unreadCount > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.6rem"><?php echo e($unreadCount > 99 ? '99+' : $unreadCount); ?></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </a>
            <a href="<?php echo e(route('conversations.index')); ?>" class="btn btn-sm btn-outline-secondary position-relative" id="navChatMobile">
                <i class="bi bi-chat-heart"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger<?php echo e($unreadMsgCount > 0 ? '' : ' d-none'); ?>" id="msgBadgeMobile" style="font-size:.6rem"><?php echo e($unreadMsgCount > 99 ? '99+' : max($unreadMsgCount,1)); ?></span>
            </a>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <button class="navbar-toggler border-0" type="button"
            data-bs-toggle="collapse" data-bs-target="#navbarMain"
            aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        
        <div class="collapse navbar-collapse" id="navbarMain">

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
            
            <ul class="navbar-nav me-auto mt-2 mt-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('dashboard') ? 'active fw-semibold' : ''); ?>" href="<?php echo e(route('dashboard')); ?>">
                        <i class="bi bi-house-heart me-1"></i>Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('swipe.*') ? 'active fw-semibold' : ''); ?>" href="<?php echo e(route('swipe.deck')); ?>">
                        <i class="bi bi-fire me-1"></i>Swipe
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('discover.*') ? 'active fw-semibold' : ''); ?>" href="<?php echo e(route('discover.index')); ?>">
                        <i class="bi bi-search-heart me-1"></i>Browse
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('matches.*') ? 'active fw-semibold' : ''); ?>" href="<?php echo e(route('matches.index')); ?>">
                        <i class="bi bi-hearts me-1"></i>Matches
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('stories.*') ? 'active fw-semibold' : ''); ?>" href="<?php echo e(route('stories.index')); ?>">
                        <i class="bi bi-camera-video me-1"></i>Stories
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('wave.*') ? 'active fw-semibold' : ''); ?>" href="<?php echo e(route('wave.received')); ?>">
                        <i class="bi bi-hand-wave me-1"></i>Waves
                    </a>
                </li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo e(request()->routeIs('blog.*') || request()->routeIs('forum.*') || request()->routeIs('chat-rooms.*') || request()->routeIs('travel.*') ? 'active fw-semibold' : ''); ?>"
                       href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-globe-americas me-1"></i>Community
                    </a>
                    <ul class="dropdown-menu shadow border-0">
                        <li><a class="dropdown-item" href="<?php echo e(route('blog.index')); ?>"><i class="bi bi-journal-richtext me-2 text-primary"></i>Blog</a></li>
                        <li><a class="dropdown-item" href="<?php echo e(route('forum.index')); ?>"><i class="bi bi-people-fill me-2 text-success"></i>Forum</a></li>
                        <li><a class="dropdown-item" href="<?php echo e(route('chat-rooms.index')); ?>"><i class="bi bi-chat-dots me-2 text-info"></i>Chat Rooms</a></li>
                        <li><a class="dropdown-item" href="<?php echo e(route('travel.index')); ?>"><i class="bi bi-airplane me-2 text-warning"></i>Travel Buddy</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?php echo e(route('icebreaker.index')); ?>"><i class="bi bi-snow2 me-2 text-primary"></i>Icebreakers</a></li>
                        <li><a class="dropdown-item" href="<?php echo e(route('extras.pets')); ?>"><i class="bi bi-heart-fill me-2 text-danger"></i>My Pets</a></li>
                        <li><a class="dropdown-item" href="<?php echo e(route('extras.voice')); ?>"><i class="bi bi-mic-fill me-2 text-danger"></i>Voice Prompts</a></li>
                    </ul>
                </li>
            </ul>

            
            <div class="d-flex align-items-center gap-2 mt-2 mt-lg-0">
                
                <a href="<?php echo e(route('notifications.index')); ?>" class="btn btn-sm btn-outline-secondary position-relative d-none d-lg-inline-flex" title="Notifications">
                    <i class="bi bi-bell"></i>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unreadCount > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.6rem"><?php echo e($unreadCount > 99 ? '99+' : $unreadCount); ?></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </a>
                <a href="<?php echo e(route('conversations.index')); ?>" class="btn btn-sm btn-outline-secondary d-none d-lg-inline-flex position-relative" title="Messages" id="navChatDesktop">
                    <i class="bi bi-chat-heart"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger<?php echo e($unreadMsgCount > 0 ? '' : ' d-none'); ?>" id="msgBadgeDesktop" style="font-size:.6rem"><?php echo e($unreadMsgCount > 99 ? '99+' : max($unreadMsgCount,1)); ?></span>
                </a>
                
                <a href="<?php echo e(route('wallet.index')); ?>" class="btn btn-sm btn-outline-success d-none d-lg-inline-flex align-items-center gap-1 fw-semibold" title="Wallet Credits">
                    <i class="bi bi-coin"></i>
                    <?php echo e(number_format(auth()->user()->credit_balance)); ?> credits
                </a>

                <button class="btn btn-sm btn-outline-secondary" data-theme-toggle title="Toggle dark/light mode" aria-label="Toggle theme">
                    <i class="bi bi-moon-stars-fill" data-theme-icon></i>
                </button>

                
                <div class="dropdown" style="position:relative">
                    <button class="btn btn-sm btn-primary dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($navPhoto): ?>
                            <img src="<?php echo e($navPhoto->thumbnail_url); ?>" class="rounded-circle" width="26" height="26" alt="avatar" style="object-fit:cover">
                        <?php else: ?>
                            <i class="bi bi-person-circle fs-5"></i>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <span class="d-none d-md-inline"><?php echo e(auth()->user()->name); ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 navbar-dropdown-menu">
                        <li class="px-3 py-2 text-muted small"><?php echo e(auth()->user()->email); ?></li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li><a class="dropdown-item" href="<?php echo e(route('profile.edit')); ?>"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Profile</a></li>
                        <li><a class="dropdown-item" href="<?php echo e(route('account.show')); ?>"><i class="bi bi-gear me-2 text-secondary"></i>Settings</a></li>
                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->is_verified): ?>
                        <li><span class="dropdown-item-text small text-success"><i class="bi bi-patch-check-fill me-2"></i>Verified ✅</span></li>
                        <?php else: ?>
                        <li><a class="dropdown-item fw-semibold text-info" href="<?php echo e(route('verify.show')); ?>"><i class="bi bi-patch-check me-2"></i>Get Verified ✅</a></li>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        
                        <li>
                            <a class="dropdown-item d-flex align-items-center justify-content-between" href="<?php echo e(route('wallet.index')); ?>">
                                <span><i class="bi bi-wallet2 me-2 text-success"></i>Wallet</span>
                                <span class="badge bg-success rounded-pill"><?php echo e(number_format(auth()->user()->credit_balance)); ?> cr</span>
                            </a>
                        </li>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! auth()->user()->isPremiumActive()): ?>
                        <li><a class="dropdown-item fw-semibold text-warning" href="<?php echo e(route('premium.show')); ?>"><i class="bi bi-star-fill me-2"></i>Go Premium</a></li>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <form method="POST" action="<?php echo e(route('logout')); ?>">
                                <?php echo csrf_field(); ?>
                                <button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Sign Out</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
            <?php else: ?>
            
            <div class="ms-auto d-flex gap-2 mt-2 mt-lg-0">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-theme-toggle title="Toggle dark/light mode" aria-label="Toggle theme">
                    <i class="bi bi-moon-stars-fill" data-theme-icon></i>
                </button>
                <a href="<?php echo e(route('login')); ?>" class="btn btn-outline-primary btn-sm">Sign In</a>
                <a href="<?php echo e(route('register')); ?>" class="btn btn-primary btn-sm">
                    <i class="bi bi-hearts me-1"></i>Join Free
                </a>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</nav>


<?php /** @var \Illuminate\Support\ViewErrorBag $errors */ ?>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success') || session('error') || $errors->any()): ?>
<div class="container mt-3">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i><?php echo e(session('success')); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-circle me-2"></i><?php echo e(session('error')); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <ul class="mb-0 ps-3"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


<main class="<?php if(auth()->guard()->check()): ?> pb-5 <?php endif; ?>">
    <?php echo $__env->yieldContent('content'); ?>
</main>


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
<nav class="bottom-nav d-lg-none">
    <a href="<?php echo e(route('dashboard')); ?>" class="<?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
        <i class="bi bi-house-heart-fill"></i><span>Home</span>
    </a>
    <a href="<?php echo e(route('swipe.deck')); ?>" class="<?php echo e(request()->routeIs('swipe.*') ? 'active' : ''); ?>">
        <i class="bi bi-fire"></i><span>Swipe</span>
    </a>
    <a href="<?php echo e(route('discover.index')); ?>" class="<?php echo e(request()->routeIs('discover.*') ? 'active' : ''); ?>">
        <i class="bi bi-search-heart"></i><span>Browse</span>
    </a>
    <a href="<?php echo e(route('matches.index')); ?>" class="<?php echo e(request()->routeIs('matches.*') ? 'active' : ''); ?>">
        <i class="bi bi-hearts"></i><span>Matches</span>
    </a>
    <a href="<?php echo e(route('conversations.index')); ?>" class="<?php echo e(request()->routeIs('conversations.*') ? 'active' : ''); ?>" style="position:relative">
        <i class="bi bi-chat-heart-fill"></i>
        <span class="bnav-msg-dot<?php echo e($unreadMsgCount > 0 ? '' : ' d-none'); ?>" id="msgDotBottom"></span>
        <span>Chat</span>
    </a>
</nav>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php echo $__env->make('partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="position-fixed bottom-0 end-0 p-3" style="z-index:1040;padding-bottom:calc(.75rem + 62px) !important" id="toastContainer"></div>


<script>
(function () {
    var toasts = [
        <?php if(session('success')): ?>
        { msg: <?php echo json_encode(session('success'), 15, 512) ?>, type: 'success', icon: 'bi-check-circle-fill' },
        <?php endif; ?>
        <?php if(session('error')): ?>
        { msg: <?php echo json_encode(session('error'), 15, 512) ?>, type: 'danger', icon: 'bi-exclamation-circle-fill' },
        <?php endif; ?>
        <?php if(session('warning')): ?>
        { msg: <?php echo json_encode(session('warning'), 15, 512) ?>, type: 'warning', icon: 'bi-exclamation-triangle-fill' },
        <?php endif; ?>
        <?php if(session('info')): ?>
        { msg: <?php echo json_encode(session('info'), 15, 512) ?>, type: 'info', icon: 'bi-info-circle-fill' },
        <?php endif; ?>
    ];
    var container = document.getElementById('toastContainer');
    if (!container) return;
    toasts.forEach(function (t) {
        var el = document.createElement('div');
        el.className = 'toast align-items-center text-bg-' + t.type + ' border-0';
        el.setAttribute('role', 'alert');
        el.setAttribute('aria-live', 'assertive');
        el.setAttribute('aria-atomic', 'true');
        el.setAttribute('data-bs-autohide', 'true');
        el.setAttribute('data-bs-delay', '5000');
        el.innerHTML = '<div class="d-flex"><div class="toast-body fw-semibold"><i class="bi ' + t.icon + ' me-2"></i>' + t.msg + '</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>';
        container.appendChild(el);
        if (window.bootstrap && bootstrap.Toast) {
            new bootstrap.Toast(el).show();
        }
    });
})();
</script>

<?php echo $__env->yieldPushContent('scripts'); ?>


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
<script>
(function () {
    var userId = <?php echo e(auth()->id()); ?>;

    function setMsgCount(n) {
        var label  = n > 99 ? '99+' : n;
        var bd     = document.getElementById('msgBadgeDesktop');
        var bm     = document.getElementById('msgBadgeMobile');
        var dot    = document.getElementById('msgDotBottom');
        if (n > 0) {
            if (bd)  { bd.textContent = label; bd.classList.remove('d-none'); }
            if (bm)  { bm.textContent = label; bm.classList.remove('d-none'); }
            if (dot) { dot.classList.remove('d-none'); }
        } else {
            if (bd)  { bd.classList.add('d-none'); }
            if (bm)  { bm.classList.add('d-none'); }
            if (dot) { dot.classList.add('d-none'); }
        }
    }

    function currentCount() {
        var bd = document.getElementById('msgBadgeDesktop');
        if (!bd || bd.classList.contains('d-none')) return 0;
        return parseInt(bd.textContent) || 0;
    }

    function showToast(senderName, preview) {
        var container = document.getElementById('toastContainer');
        if (!container) return;
        var el = document.createElement('div');
        el.className = 'toast align-items-center text-bg-primary border-0';
        el.setAttribute('role', 'alert');
        el.setAttribute('aria-live', 'assertive');
        el.setAttribute('aria-atomic', 'true');
        el.setAttribute('data-bs-autohide', 'true');
        el.setAttribute('data-bs-delay', '5000');
        var safePreview = preview ? preview.replace(/</g,'&lt;').replace(/>/g,'&gt;') : '';
        el.innerHTML = '<div class="d-flex"><div class="toast-body fw-semibold">'
            + '<i class="bi bi-chat-heart-fill me-2"></i>'
            + '<strong>' + senderName.replace(/</g,'&lt;') + '</strong>'
            + (safePreview ? ': ' + safePreview : ' sent you a message')
            + '</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>';
        container.appendChild(el);
        if (window.bootstrap && bootstrap.Toast) { new bootstrap.Toast(el).show(); }
    }

    if (typeof Echo !== 'undefined') {
        Echo.private('App.Models.User.' + userId)
            .notification(function (n) {
                if (n.type === 'App\\Notifications\\NewMessageNotification') {
                    // Don't show dot if already on this conversation
                    var onConv = window.location.pathname.indexOf('/conversations/') !== -1;
                    var convId = n.conversation_id ? String(n.conversation_id) : '';
                    var onThisConv = onConv && window.location.pathname.indexOf('/' + convId) !== -1;
                    if (!onThisConv) {
                        setMsgCount(currentCount() + 1);
                        showToast(n.sender_name || 'Someone', n.preview || '');
                    }
                }
            });
    }
})();
</script>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('like_matched')): ?>
<div class="modal fade" id="matchModal" tabindex="-1" aria-labelledby="matchModalLabel" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 overflow-hidden text-center"
             style="background:linear-gradient(135deg,#ff6b9d 0%,#c44ee0 55%,#7b2ff7 100%)">
            <div class="modal-body p-5 text-white">
                <div style="font-size:4rem;animation:heartbeat 1.6s infinite">🎉</div>
                <h3 class="fw-bold mt-2 mb-1">It's a Match!</h3>
                <p class="opacity-85 mb-4">You and <strong><?php echo e(session('success') ? '' : 'them'); ?></strong> both liked each other!</p>
                <div class="d-flex gap-2 justify-content-center flex-wrap">
                    <a href="<?php echo e(route('conversations.index')); ?>"
                       class="btn btn-light fw-bold rounded-pill px-4"
                       data-bs-dismiss="modal">
                        <i class="bi bi-chat-heart-fill me-1 text-danger"></i>Send Message
                    </a>
                    <button type="button" class="btn btn-outline-light rounded-pill px-4" data-bs-dismiss="modal">
                        Keep Swiping
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var el = document.getElementById('matchModal');
        if (el && window.bootstrap) { new bootstrap.Modal(el).show(); }
    });
</script>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>AOS.init({ duration: 700, once: true, offset: 60 });</script>


<script>
(function () {
    var html = document.documentElement;
    var buttons = document.querySelectorAll('[data-theme-toggle]');
    var icons = document.querySelectorAll('[data-theme-icon]');

    function applyTheme(t) {
        html.setAttribute('data-bs-theme', t);
        localStorage.setItem('theme', t);
        icons.forEach(function (icon) {
            icon.className = t === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-stars-fill';
        });
        buttons.forEach(function (btn) {
            btn.title = t === 'dark' ? 'Switch to light mode' : 'Switch to dark mode';
        });
    }

    // Sync icon on load
    applyTheme(localStorage.getItem('theme') || 'light');

    buttons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            applyTheme(html.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark');
        });
    });
})();
</script>
<script>
(function () {
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('<?php echo e(asset('sw.js')); ?>', {
                scope: '<?php echo e(rtrim(request()->getBasePath(), '/')); ?>/'
            }).catch(function (err) {
                console.warn('Service Worker registration failed:', err);
            });
        });
    }
    // Capture the browser's install prompt so the install page can trigger it
    window.addEventListener('beforeinstallprompt', function (e) {
        e.preventDefault();
        window._pwaInstallPrompt = e;
        if (!window.matchMedia('(display-mode: standalone)').matches) {
            var banner = document.getElementById('pwaInstallNudge');
            if (banner) banner.classList.remove('d-none');
        }
    });
})();
</script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\dating\resources\views\layouts\app.blade.php ENDPATH**/ ?>