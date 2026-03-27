<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
        $siteName    = \App\Models\SiteSetting::get('site_name', config('app.name', 'HeartsConnect'));
        $faviconPath = \App\Models\SiteSetting::get('site_favicon');
        $faviconUrl  = $faviconPath ? asset('storage/' . $faviconPath) : asset('favicon.svg');
    ?>
    <title>Help Center — <?php echo e($siteName); ?></title>
    <link rel="icon" href="<?php echo e($faviconUrl); ?>" type="<?php echo e(str_ends_with($faviconUrl, '.svg') ? 'image/svg+xml' : 'image/png'); ?>">
    <?php ($seoTitle = 'Help Center'); ?>
    <?php echo $__env->make('partials.seo-meta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/scss/app.scss', 'resources/js/app.js']); ?>
    <style>
        body { background: #0d0118; color: rgba(255,255,255,.85); }
        .page-hero {
            background: linear-gradient(135deg, #0d1a40 0%, #1a2a5a 50%, #0d1a40 100%);
            border-bottom: 1px solid rgba(255,255,255,.07);
            padding: 4rem 0 3rem;
        }
        .search-box {
            background: rgba(255,255,255,.07);
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 3rem;
            color: #fff;
            padding: .75rem 1.5rem;
            font-size: 1rem;
            width: 100%;
            max-width: 520px;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        .search-box:focus { border-color: #6495ed; box-shadow: 0 0 0 3px rgba(100,149,237,.15); }
        .search-box::placeholder { color: rgba(255,255,255,.35); }
        .category-chip {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .45rem 1.1rem;
            background: rgba(255,255,255,.05);
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 2rem;
            color: rgba(255,255,255,.55);
            font-size: .85rem;
            cursor: pointer;
            transition: all .2s;
            text-decoration: none;
        }
        .category-chip:hover, .category-chip.active {
            background: rgba(100,149,237,.15);
            border-color: #6495ed;
            color: #9ab4f5;
        }
        .accordion-button {
            background: rgba(255,255,255,.04) !important;
            color: rgba(255,255,255,.85) !important;
            border-radius: .75rem !important;
            font-weight: 500;
            border: none !important;
        }
        .accordion-button:not(.collapsed) {
            background: rgba(100,149,237,.1) !important;
            color: #9ab4f5 !important;
            box-shadow: none !important;
        }
        .accordion-button::after { filter: invert(1); }
        .accordion-item {
            background: transparent !important;
            border: 1px solid rgba(255,255,255,.07) !important;
            border-radius: .75rem !important;
            margin-bottom: .5rem;
            overflow: hidden;
        }
        .accordion-body {
            background: rgba(255,255,255,.02) !important;
            color: rgba(255,255,255,.6);
            line-height: 1.8;
            font-size: .9375rem;
            padding: 1rem 1.25rem 1.25rem;
        }
        .faq-category-title {
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #6495ed;
            margin-bottom: 1rem;
            margin-top: 2.5rem;
        }
        .faq-category-title:first-of-type { margin-top: 0; }
        .help-card {
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 1rem;
            padding: 1.5rem;
            text-align: center;
            transition: border-color .2s;
            text-decoration: none;
        }
        .help-card:hover { border-color: rgba(100,149,237,.4); }
        .help-card .icon { font-size: 2rem; margin-bottom: .75rem; }
        .help-card h6 { color: #fff; font-weight: 600; margin-bottom: .35rem; }
        .help-card p { color: rgba(255,255,255,.45); font-size: .8rem; margin: 0; }
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
        <div class="display-5 mb-3">🛟</div>
        <h1 class="fw-bold mb-3">Help Center</h1>
        <p class="opacity-75 mb-4">Find answers to common questions about <?php echo e($siteName); ?>.</p>
        <div class="d-flex justify-content-center">
            <div class="position-relative" style="width:100%;max-width:520px">
                <i class="bi bi-search position-absolute" style="left:1.1rem;top:50%;transform:translateY(-50%);color:rgba(255,255,255,.35)"></i>
                <input type="text" id="faqSearch" class="search-box" placeholder="Search help articles…" style="padding-left:2.8rem">
            </div>
        </div>
    </div>
</section>

<div class="container py-5" style="max-width:860px">

    
    <div class="d-flex flex-wrap gap-2 mb-5 justify-content-center">
        <a href="#getting-started"  class="category-chip"><i class="bi bi-rocket-takeoff"></i>Getting Started</a>
        <a href="#account"          class="category-chip"><i class="bi bi-person-gear"></i>Account</a>
        <a href="#matching"         class="category-chip"><i class="bi bi-hearts"></i>Matching</a>
        <a href="#messaging"        class="category-chip"><i class="bi bi-chat-dots"></i>Messaging</a>
        <a href="#premium"          class="category-chip"><i class="bi bi-star"></i>Premium</a>
        <a href="#safety"           class="category-chip"><i class="bi bi-shield-check"></i>Safety</a>
        <a href="#billing"          class="category-chip"><i class="bi bi-credit-card"></i>Billing</a>
    </div>

    
    <div class="faq-category-title" id="getting-started"><i class="bi bi-rocket-takeoff me-1"></i>Getting Started</div>
    <div class="accordion" id="faqGettingStarted">
        <div class="accordion-item faq-item">
            <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">How do I create an account?</button></h2>
            <div id="faq1" class="accordion-collapse collapse"><div class="accordion-body">Click <strong>Join Free</strong> on the homepage. Enter your name, email address, and a secure password. You'll be automatically logged in and guided through a 5-step profile setup to find your best matches.</div></div>
        </div>
        <div class="accordion-item faq-item">
            <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">Is <?php echo e($siteName); ?> free to use?</button></h2>
            <div id="faq2" class="accordion-collapse collapse"><div class="accordion-body">Yes! Creating a profile, browsing, swiping, and sending messages are all free. Premium membership unlocks extra features like seeing who liked you, unlimited likes, profile boosts, and more.</div></div>
        </div>
        <div class="accordion-item faq-item">
            <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">What information should I add to my profile?</button></h2>
            <div id="faq3" class="accordion-collapse collapse"><div class="accordion-body">We recommend adding a clear profile photo, a short bio, your interests, and location. Profiles with photos and complete information get significantly more matches. All personal details remain secure and are only shared as you choose.</div></div>
        </div>
    </div>

    
    <div class="faq-category-title" id="account"><i class="bi bi-person-gear me-1"></i>Account</div>
    <div class="accordion" id="faqAccount">
        <div class="accordion-item faq-item">
            <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">How do I change my email or password?</button></h2>
            <div id="faq4" class="accordion-collapse collapse"><div class="accordion-body">Go to <strong>Account Settings</strong> (your avatar → Account). From there you can update your email address and change your password at any time.</div></div>
        </div>
        <div class="accordion-item faq-item">
            <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">Can I pause my account without deleting it?</button></h2>
            <div id="faq5" class="accordion-collapse collapse"><div class="accordion-body">Yes. In <strong>Account Settings</strong> you'll find a <em>Pause Account</em> option. While paused, your profile is hidden from other users. You can reactivate it at any time by logging back in.</div></div>
        </div>
        <div class="accordion-item faq-item">
            <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">How do I delete my account?</button></h2>
            <div id="faq6" class="accordion-collapse collapse"><div class="accordion-body">Go to <strong>Account Settings → Delete Account</strong>. Deleting your account permanently removes all your data including matches, messages, and photos. This action cannot be undone. You may download a copy of your data first using the <em>Export My Data</em> option.</div></div>
        </div>
        <div class="accordion-item faq-item">
            <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq7">I forgot my password. How do I reset it?</button></h2>
            <div id="faq7" class="accordion-collapse collapse"><div class="accordion-body">Click <strong>Forgot password?</strong> on the Sign In page. Enter your email address and we'll send you a reset link. Check your spam folder if you don't see it within a few minutes.</div></div>
        </div>
    </div>

    
    <div class="faq-category-title" id="matching"><i class="bi bi-hearts me-1"></i>Matching &amp; Swiping</div>
    <div class="accordion" id="faqMatching">
        <div class="accordion-item faq-item">
            <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq8">How does matching work?</button></h2>
            <div id="faq8" class="accordion-collapse collapse"><div class="accordion-body">When two users both like each other, a <strong>Match</strong> is created and both are notified. You can then start chatting. You can also browse profiles in grid view and like from there.</div></div>
        </div>
        <div class="accordion-item faq-item">
            <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq9">How many likes can I send as a free user?</button></h2>
            <div id="faq9" class="accordion-collapse collapse"><div class="accordion-body">Free accounts can send up to <strong>3 likes per day</strong>. Premium members enjoy unlimited likes. The daily count resets at midnight UTC.</div></div>
        </div>
        <div class="accordion-item faq-item">
            <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq10">What is a Super Like?</button></h2>
            <div id="faq10" class="accordion-collapse collapse"><div class="accordion-body">A <strong>Super Like</strong> lets someone know you're especially interested in them. When you use a super like, the receiver gets a special notification highlighting your profile. Premium members can send super likes.</div></div>
        </div>
    </div>

    
    <div class="faq-category-title" id="messaging"><i class="bi bi-chat-dots me-1"></i>Messaging</div>
    <div class="accordion" id="faqMessaging">
        <div class="accordion-item faq-item">
            <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq11">When can I message someone?</button></h2>
            <div id="faq11" class="accordion-collapse collapse"><div class="accordion-body">You can only message users you've <strong>matched</strong> with — meaning both of you have liked each other. This protects everyone from unsolicited messages.</div></div>
        </div>
        <div class="accordion-item faq-item">
            <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq12">Can I block or unmatch someone?</button></h2>
            <div id="faq12" class="accordion-collapse collapse"><div class="accordion-body">Yes. On any profile or in a conversation, tap the <em>⋮ menu</em> and choose <strong>Block</strong> or <strong>Unmatch</strong>. A blocked user cannot see your profile, like you, or message you. You can unblock from Account Settings.</div></div>
        </div>
    </div>

    
    <div class="faq-category-title" id="premium"><i class="bi bi-star me-1"></i>Premium Membership</div>
    <div class="accordion" id="faqPremium">
        <div class="accordion-item faq-item">
            <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq13">What does Premium include?</button></h2>
            <div id="faq13" class="accordion-collapse collapse"><div class="accordion-body">Premium members enjoy: <strong>See who liked you</strong>, unlimited likes, super likes, profile boosts so more people see you, <strong>unlimited location updates</strong> (free accounts are limited to 2), and priority support — all for one flat monthly fee.</div></div>
        </div>
        <div class="accordion-item faq-item">
            <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq14">How do I pay for Premium? Is it anonymous?</button></h2>
            <div id="faq14" class="accordion-collapse collapse"><div class="accordion-body">We accept crypto-currency payments for complete privacy. See the <a href="<?php echo e(route('premium.show')); ?>" style="color:#6495ed">Premium page</a> for supported currencies. Your payment details are never linked to your profile.</div></div>
        </div>
    </div>

    
    <div class="faq-category-title" id="safety"><i class="bi bi-shield-check me-1"></i>Safety &amp; Reporting</div>
    <div class="accordion" id="faqSafety">
        <div class="accordion-item faq-item">
            <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq15">How do I report someone?</button></h2>
            <div id="faq15" class="accordion-collapse collapse"><div class="accordion-body">On any profile, tap the <em>⋮ menu</em> → <strong>Report</strong>. Select the reason and optionally add details. Our moderation team reviews every report. You can also visit our <a href="<?php echo e(route('pages.report-abuse')); ?>" style="color:#6495ed">Report Abuse</a> page to contact us directly for urgent matters.</div></div>
        </div>
        <div class="accordion-item faq-item">
            <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq16">Are profiles verified?</button></h2>
            <div id="faq16" class="accordion-collapse collapse"><div class="accordion-body">We offer optional <strong>Photo Verification</strong>. Verified users display a blue checkmark badge on their profile, giving other members extra confidence the profile is genuine. To verify, go to <em>Profile Settings → Verify Identity</em>.</div></div>
        </div>
        <div class="accordion-item faq-item">
            <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq17">What are the rules about photos?</button></h2>
            <div id="faq17" class="accordion-collapse collapse"><div class="accordion-body">All photos must show you clearly. No nudity, explicit content, minors, or images of other people without consent. Photos are reviewed by our moderation team before going live. Violations lead to immediate removal and possible account suspension.</div></div>
        </div>
    </div>

    
    <div class="faq-category-title" id="billing"><i class="bi bi-credit-card me-1"></i>Billing</div>
    <div class="accordion" id="faqBilling">
        <div class="accordion-item faq-item">
            <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq18">How do I cancel Premium?</button></h2>
            <div id="faq18" class="accordion-collapse collapse"><div class="accordion-body">Premium access automatically expires at the end of the period you paid for — there are no recurring charges. If you'd like a refund for unused time, contact our <a href="<?php echo e(route('pages.contact')); ?>" style="color:#6495ed">billing support</a>.</div></div>
        </div>
        <div class="accordion-item faq-item">
            <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq19">Can I get a refund?</button></h2>
            <div id="faq19" class="accordion-collapse collapse"><div class="accordion-body">Refund requests are handled on a case-by-case basis. Please <a href="<?php echo e(route('pages.contact')); ?>" style="color:#6495ed">contact our billing team</a> with your payment reference within 7 days of purchase and we'll review your request.</div></div>
        </div>
    </div>

    
    <div class="mt-5 pt-4 text-center" style="border-top:1px solid rgba(255,255,255,.06)">
        <p class="mb-3" style="color:rgba(255,255,255,.5)">Didn't find what you were looking for?</p>
        <a href="<?php echo e(route('pages.contact')); ?>" class="btn rounded-pill px-4 py-2 fw-semibold" style="background:linear-gradient(135deg,#ff6b9d,#c44ee0);color:#fff;border:none">
            <i class="bi bi-envelope me-2"></i>Contact Support
        </a>
    </div>

</div>

<?php echo $__env->make('partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<script>
// Live FAQ search
document.getElementById('faqSearch').addEventListener('input', function () {
    const q = this.value.toLowerCase().trim();
    document.querySelectorAll('.faq-item').forEach(function (item) {
        const text = item.innerText.toLowerCase();
        item.style.display = (!q || text.includes(q)) ? '' : 'none';
    });
});
</script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\dating\resources\views\pages\help-center.blade.php ENDPATH**/ ?>