<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
        $siteName    = \App\Models\SiteSetting::get('site_name', config('app.name', 'HeartsConnect'));
        $supportEmail = \App\Models\SiteSetting::get('legal_support_email')
            ?: \App\Models\SiteSetting::get('footer_support_email')
            ?: ('support@' . (parse_url(config('app.url'), PHP_URL_HOST) ?? 'heartsconnect.com'));
        $faviconPath = \App\Models\SiteSetting::get('site_favicon');
        $faviconUrl  = $faviconPath ? asset('storage/' . $faviconPath) : asset('favicon.svg');
    ?>
    <title>Contact Us — <?php echo e($siteName); ?></title>
    <link rel="icon" href="<?php echo e($faviconUrl); ?>" type="<?php echo e(str_ends_with($faviconUrl, '.svg') ? 'image/svg+xml' : 'image/png'); ?>">
    <?php ($seoTitle = 'Contact Us'); ?>
    <?php echo $__env->make('partials.seo-meta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/scss/app.scss', 'resources/js/app.js']); ?>
    <style>
        body { background: #0d0118; color: rgba(255,255,255,.85); }
        .page-hero {
            background: linear-gradient(135deg, #1a0533 0%, #3a0a4a 50%, #1a0533 100%);
            border-bottom: 1px solid rgba(255,255,255,.07);
            padding: 4rem 0 3rem;
        }
        .contact-card {
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 1.25rem;
            padding: 2rem;
            transition: border-color .2s;
        }
        .contact-card:hover { border-color: rgba(244,143,177,.3); }
        .form-control, .form-select {
            background: rgba(255,255,255,.06) !important;
            border: 1px solid rgba(255,255,255,.12) !important;
            color: #fff !important;
            border-radius: .75rem !important;
            padding: .65rem 1rem !important;
        }
        .form-control:focus, .form-select:focus {
            background: rgba(255,255,255,.09) !important;
            border-color: #f48fb1 !important;
            box-shadow: 0 0 0 3px rgba(244,143,177,.15) !important;
        }
        .form-control::placeholder { color: rgba(255,255,255,.3) !important; }
        .form-label { color: rgba(255,255,255,.7); font-size: .875rem; font-weight: 500; margin-bottom: .4rem; }
        .btn-send {
            background: linear-gradient(135deg, #ff6b9d, #c44ee0);
            border: none;
            border-radius: .85rem;
            color: #fff;
            font-weight: 600;
            padding: .7rem 2.5rem;
            transition: opacity .2s, transform .15s;
        }
        .btn-send:hover { opacity: .9; transform: translateY(-1px); color: #fff; }
        .info-tile {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1.25rem;
            background: rgba(255,255,255,.03);
            border: 1px solid rgba(255,255,255,.07);
            border-radius: 1rem;
        }
        .info-tile-icon {
            width: 44px; height: 44px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            background: rgba(244,143,177,.12);
            border-radius: .75rem;
            color: #f48fb1;
            font-size: 1.2rem;
        }
        .invalid-feedback { color: #f48fb1 !important; font-size: .8rem; }
        textarea.form-control { resize: vertical; min-height: 130px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand sticky-top shadow-sm" style="background:rgba(13,1,24,.92);backdrop-filter:blur(16px);border-bottom:1px solid rgba(255,255,255,.06)">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2 text-decoration-none" href="<?php echo e(auth()->check() ? route('dashboard') : url('/')); ?>">
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
        <div class="ms-auto d-flex gap-2">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-sm btn-outline-light rounded-pill px-3">Dashboard</a>
            <?php else: ?>
                <a href="<?php echo e(route('login')); ?>" class="btn btn-sm btn-outline-light rounded-pill px-3">Sign In</a>
                <a href="<?php echo e(route('register')); ?>" class="btn btn-sm rounded-pill px-3 fw-semibold" style="background:linear-gradient(135deg,#ff6b9d,#c44ee0);color:#fff;border:none">Join Free</a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</nav>

<section class="page-hero text-center">
    <div class="container">
        <div class="display-5 mb-3">💌</div>
        <h1 class="fw-bold mb-2">Contact Us</h1>
        <p class="opacity-75 mb-0" style="max-width:520px;margin:0 auto">We'd love to hear from you. Send us a message and we'll respond within 24–48 hours.</p>
    </div>
</section>

<div class="container py-5" style="max-width:960px">

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
    <div class="alert d-flex align-items-center gap-3 mb-4 rounded-3" style="background:rgba(40,167,69,.12);border:1px solid rgba(40,167,69,.3);color:#6dda8d">
        <i class="bi bi-check-circle-fill fs-5 flex-shrink-0"></i>
        <span><?php echo e(session('success')); ?></span>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="row g-4">

        
        <div class="col-lg-7">
            <div class="contact-card">
                <h5 class="fw-bold mb-1">Send us a message</h5>
                <p class="mb-4" style="color:rgba(255,255,255,.45);font-size:.875rem">Fill in the form below and our team will get back to you shortly.</p>

                <form method="POST" action="<?php echo e(route('pages.contact.submit')); ?>" novalidate>
                    <?php echo csrf_field(); ?>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label">Your Name <span style="color:#f48fb1">*</span></label>
                            <input type="text" name="name" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   value="<?php echo e(old('name')); ?>" placeholder="Jane Doe" required>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Email Address <span style="color:#f48fb1">*</span></label>
                            <input type="email" name="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   value="<?php echo e(old('email')); ?>" placeholder="jane@example.com" required>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Subject <span style="color:#f48fb1">*</span></label>
                            <input type="text" name="subject" class="form-control <?php $__errorArgs = ['subject'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   value="<?php echo e(old('subject')); ?>" placeholder="How can we help?" required>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['subject'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Message <span style="color:#f48fb1">*</span></label>
                            <textarea name="message" class="form-control <?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                      placeholder="Please describe your question or issue in detail..." required><?php echo e(old('message')); ?></textarea>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="col-12 mt-2">
                            <button type="submit" class="btn btn-send w-100">
                                <i class="bi bi-send me-2"></i>Send Message
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        
        <div class="col-lg-5 d-flex flex-column gap-3">
            <div class="info-tile">
                <div class="info-tile-icon"><i class="bi bi-envelope-heart"></i></div>
                <div>
                    <div class="fw-semibold mb-1" style="font-size:.9rem">Email Support</div>
                    <a href="mailto:<?php echo e($supportEmail); ?>" style="color:#f48fb1;font-size:.875rem;word-break:break-all"><?php echo e($supportEmail); ?></a>
                    <p class="mb-0 mt-1" style="color:rgba(255,255,255,.4);font-size:.8rem">We respond within 24–48 hours on business days.</p>
                </div>
            </div>

            <div class="info-tile">
                <div class="info-tile-icon"><i class="bi bi-clock"></i></div>
                <div>
                    <div class="fw-semibold mb-1" style="font-size:.9rem">Support Hours</div>
                    <p class="mb-0" style="color:rgba(255,255,255,.55);font-size:.875rem">Monday – Friday<br>9:00 AM – 6:00 PM (UTC)</p>
                </div>
            </div>

            <div class="info-tile">
                <div class="info-tile-icon"><i class="bi bi-question-circle"></i></div>
                <div>
                    <div class="fw-semibold mb-1" style="font-size:.9rem">Self-Service</div>
                    <p class="mb-1" style="color:rgba(255,255,255,.55);font-size:.875rem">Browse our Help Center for instant answers.</p>
                    <a href="<?php echo e(route('pages.help-center')); ?>" style="color:#f48fb1;font-size:.875rem">Visit Help Center →</a>
                </div>
            </div>

            <div class="info-tile">
                <div class="info-tile-icon" style="background:rgba(255,87,87,.12);color:#ff5757"><i class="bi bi-shield-exclamation"></i></div>
                <div>
                    <div class="fw-semibold mb-1" style="font-size:.9rem">Safety Concerns</div>
                    <p class="mb-1" style="color:rgba(255,255,255,.55);font-size:.875rem">For urgent safety issues please use our dedicated report page.</p>
                    <a href="<?php echo e(route('pages.report-abuse')); ?>" style="color:#ff5757;font-size:.875rem">Report Abuse →</a>
                </div>
            </div>
        </div>

    </div>
</div>

<?php echo $__env->make('partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\dating\resources\views\pages\contact.blade.php ENDPATH**/ ?>