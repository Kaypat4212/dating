<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
        $siteName    = \App\Models\SiteSetting::get('site_name', config('app.name', 'HeartsConnect'));
        $safetyEmail = \App\Models\SiteSetting::get('legal_safety_email') ?: 'safety@heartsconnect.com';
        $faviconPath = \App\Models\SiteSetting::get('site_favicon');
        $faviconUrl  = $faviconPath ? asset('storage/' . $faviconPath) : asset('favicon.svg');
    ?>
    <title>Safety Tips — <?php echo e($siteName); ?></title>
    <link rel="icon" href="<?php echo e($faviconUrl); ?>" type="<?php echo e(str_ends_with($faviconUrl, '.svg') ? 'image/svg+xml' : 'image/png'); ?>">
    <?php ($seoTitle = 'Safety Tips'); ?>
    <?php echo $__env->make('partials.seo-meta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/scss/app.scss', 'resources/js/app.js']); ?>
    <style>
        body { background: #0d0118; color: rgba(255,255,255,.85); }
        .page-hero {
            background: linear-gradient(135deg, #0a2a1a 0%, #1a4a2a 50%, #0a2a1a 100%);
            border-bottom: 1px solid rgba(255,255,255,.07);
            padding: 4rem 0 3rem;
        }
        .tip-card {
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 1.25rem;
            padding: 1.75rem;
            height: 100%;
            transition: transform .2s, border-color .2s;
        }
        .tip-card:hover {
            transform: translateY(-3px);
            border-color: rgba(72,199,142,.3);
        }
        .tip-icon {
            width: 52px; height: 52px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            border-radius: 1rem;
            font-size: 1.4rem;
            margin-bottom: 1rem;
        }
        .tip-card h5 { color: #fff; font-weight: 700; font-size: 1rem; margin-bottom: .6rem; }
        .tip-card p { color: rgba(255,255,255,.55); font-size: .875rem; line-height: 1.75; margin: 0; }
        .section-label {
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            padding: .3rem .9rem;
            border-radius: 2rem;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            margin-bottom: 1.5rem;
        }
        .emergency-box {
            background: rgba(255,87,87,.08);
            border: 1px solid rgba(255,87,87,.25);
            border-radius: 1.25rem;
            padding: 2rem;
        }
        .do-list li, .dont-list li {
            color: rgba(255,255,255,.65);
            font-size: .9rem;
            line-height: 1.8;
            padding: .25rem 0;
        }
        .do-list li::marker { color: #48c78e; }
        .dont-list li::marker { color: #ff5757; }
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
        <div class="display-5 mb-3">🛡️</div>
        <h1 class="fw-bold mb-2">Safety Tips</h1>
        <p class="opacity-75 mb-0" style="max-width:560px;margin:0 auto">Your safety is our top priority. Follow these guidelines to have a safe and positive dating experience.</p>
    </div>
</section>

<div class="container py-5" style="max-width:1000px">

    
    <div class="section-label" style="background:rgba(100,149,237,.1);color:#6495ed"><i class="bi bi-laptop"></i>Online Safety</div>
    <div class="row g-3 mb-5">
        <div class="col-md-6 col-lg-4">
            <div class="tip-card">
                <div class="tip-icon" style="background:rgba(100,149,237,.12);color:#6495ed"><i class="bi bi-person-badge"></i></div>
                <h5>Keep Personal Info Private</h5>
                <p>Never share your full name, home address, workplace, financial details, or government ID with someone you've just met online. Move slowly.</p>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="tip-card">
                <div class="tip-icon" style="background:rgba(100,149,237,.12);color:#6495ed"><i class="bi bi-link-45deg"></i></div>
                <h5>Be Wary of Suspicious Links</h5>
                <p>Don't click links sent by matches you don't know well. Scammers use links to steal credentials or install malware. When in doubt, don't click.</p>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="tip-card">
                <div class="tip-icon" style="background:rgba(100,149,237,.12);color:#6495ed"><i class="bi bi-search"></i></div>
                <h5>Do Your Research</h5>
                <p>A quick reverse image search of someone's photos can reveal if they're using stolen pictures. Trust your instincts — if something feels off, it probably is.</p>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="tip-card">
                <div class="tip-icon" style="background:rgba(100,149,237,.12);color:#6495ed"><i class="bi bi-currency-exchange"></i></div>
                <h5>Never Send Money</h5>
                <p>Anyone asking for money or financial information — especially early in a conversation — is almost certainly a scammer. Block and report immediately.</p>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="tip-card">
                <div class="tip-icon" style="background:rgba(100,149,237,.12);color:#6495ed"><i class="bi bi-chat-square-text"></i></div>
                <h5>Keep Chats on the Platform</h5>
                <p>Use <?php echo e($siteName); ?>'s built-in messaging until you feel comfortable. Moving off-platform removes your ability to report or block the person easily.</p>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="tip-card">
                <div class="tip-icon" style="background:rgba(100,149,237,.12);color:#6495ed"><i class="bi bi-lock-fill"></i></div>
                <h5>Use a Strong Password</h5>
                <p>Secure your account with a unique password you don't use elsewhere. Enable a password manager to help. Never share your login credentials with anyone.</p>
            </div>
        </div>
    </div>

    
    <div class="section-label" style="background:rgba(255,193,7,.08);color:#ffc107"><i class="bi bi-geo-alt"></i>Meeting In Person</div>
    <div class="row g-4 mb-5">
        <div class="col-lg-6">
            <div style="background:rgba(72,199,142,.05);border:1px solid rgba(72,199,142,.15);border-radius:1.25rem;padding:1.75rem">
                <h5 class="fw-bold mb-3" style="color:#48c78e"><i class="bi bi-check-circle me-2"></i>Do</h5>
                <ul class="do-list mb-0" style="padding-left:1.2rem">
                    <li>Meet in a busy, public place for the first few dates</li>
                    <li>Tell a friend or family member where you're going</li>
                    <li>Share your date's profile/name with someone you trust</li>
                    <li>Arrange your own transport to and from the date</li>
                    <li>Keep your phone charged and with you at all times</li>
                    <li>Trust your instincts — leave if anything feels wrong</li>
                    <li>Video call before meeting to verify they are who they say</li>
                </ul>
            </div>
        </div>
        <div class="col-lg-6">
            <div style="background:rgba(255,87,87,.05);border:1px solid rgba(255,87,87,.15);border-radius:1.25rem;padding:1.75rem">
                <h5 class="fw-bold mb-3" style="color:#ff5757"><i class="bi bi-x-circle me-2"></i>Don't</h5>
                <ul class="dont-list mb-0" style="padding-left:1.2rem">
                    <li>Meet at your home, their home, or anywhere isolated</li>
                    <li>Accept a ride from someone you've just met online</li>
                    <li>Leave your drink unattended or accept open drinks</li>
                    <li>Share your location or home address too soon</li>
                    <li>Feel pressured to do anything you're not comfortable with</li>
                    <li>Ignore red flags just because you've been chatting for a while</li>
                    <li>Go alone somewhere remote on a first or early date</li>
                </ul>
            </div>
        </div>
    </div>

    
    <div class="section-label" style="background:rgba(255,87,87,.08);color:#ff5757"><i class="bi bi-exclamation-triangle"></i>Recognise Scams &amp; Red Flags</div>
    <div class="row g-3 mb-5">
        <div class="col-md-6 col-lg-4">
            <div class="tip-card">
                <div class="tip-icon" style="background:rgba(255,87,87,.12);color:#ff5757"><i class="bi bi-heart-arrow"></i></div>
                <h5>Love Bombing</h5>
                <p>Extreme flattery, declarations of love very early, or someone who seems too perfect — these can be manipulation tactics to lower your guard.</p>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="tip-card">
                <div class="tip-icon" style="background:rgba(255,87,87,.12);color:#ff5757"><i class="bi bi-camera-video-off"></i></div>
                <h5>Always Avoiding Video Calls</h5>
                <p>Scammers typically avoid video calls because their photos don't match their real appearance. If they keep making excuses, be cautious.</p>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="tip-card">
                <div class="tip-icon" style="background:rgba(255,87,87,.12);color:#ff5757"><i class="bi bi-airplane"></i></div>
                <h5>"I'm Overseas" Stories</h5>
                <p>A common romance scam involves claiming to be a soldier, oil rig worker, or doctor abroad who needs money for emergencies or to visit you.</p>
            </div>
        </div>
    </div>

    
    <div class="emergency-box">
        <div class="d-flex align-items-start gap-3">
            <i class="bi bi-exclamation-octagon-fill fs-3 flex-shrink-0" style="color:#ff5757;margin-top:.1rem"></i>
            <div>
                <h5 class="fw-bold mb-2" style="color:#ff5757">In an Emergency</h5>
                <p class="mb-3" style="color:rgba(255,255,255,.65)">If you are in immediate danger, call your local emergency services (e.g. 911 in the US, 999 in the UK, 112 in the EU). Your safety comes first — report the incident to both authorities and us.</p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="<?php echo e(route('pages.report-abuse')); ?>" class="btn btn-sm fw-semibold rounded-pill px-3" style="background:rgba(255,87,87,.2);border:1px solid rgba(255,87,87,.4);color:#ff5757">
                        <i class="bi bi-flag me-1"></i>Report Abuse
                    </a>
                    <a href="mailto:<?php echo e($safetyEmail); ?>" class="btn btn-sm fw-semibold rounded-pill px-3" style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.12);color:rgba(255,255,255,.7)">
                        <i class="bi bi-envelope me-1"></i>Email Safety Team
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>

<?php echo $__env->make('partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\dating\resources\views\pages\safety-tips.blade.php ENDPATH**/ ?>