<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
        use App\Models\SiteSetting as SS;

        $supportEmail = SS::get('legal_support_email')
            ?: SS::get('footer_support_email')
            ?: ('support@' . (parse_url(config('app.url'), PHP_URL_HOST) ?? 'heartsconnect.com'));
        $privacyEmail = SS::get('legal_privacy_email') ?: 'privacy@heartsconnect.com';
        $securityEmail = SS::get('legal_security_email') ?: 'security@heartsconnect.com';
        $safetyEmail = SS::get('legal_safety_email') ?: 'safety@heartsconnect.com';
    ?>
    <title>Privacy Policy — <?php echo e(SS::get('site_name', config('app.name'))); ?></title>
    <?php ($seoTitle = 'Privacy Policy'); ?>
    <?php echo $__env->make('partials.seo-meta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/scss/app.scss', 'resources/js/app.js']); ?>
    <style>
        body { background: #0d0118; color: rgba(255,255,255,.85); }
        .legal-hero { background: linear-gradient(135deg,#0d1a40 0%,#1a2a5a 50%,#0d1a40 100%); border-bottom: 1px solid rgba(255,255,255,.07); padding: 4rem 0 3rem; }
        .legal-body { max-width: 820px; margin: 0 auto; }
        .section-badge { display:inline-flex;align-items:center;gap:.4rem;background:rgba(100,149,237,.1);color:#6495ed;font-size:.72rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;padding:.25rem .7rem;border-radius:2rem;border:1px solid rgba(100,149,237,.2);margin-bottom:1rem; }
        .legal-section { border-bottom:1px solid rgba(255,255,255,.05);padding:2.5rem 0; }
        .legal-section:last-of-type { border-bottom:none; }
        .legal-section h2 { font-size:1.35rem;font-weight:700;color:#fff;margin-bottom:.8rem; }
        .legal-section p, .legal-section li { color:rgba(255,255,255,.6);line-height:1.85;font-size:.9375rem; }
        .legal-section ul, .legal-section ol { padding-left:1.4rem; }
        .legal-section li { margin-bottom:.4rem; }
        .highlight-box { background:rgba(100,149,237,.06);border-left:3px solid #6495ed;border-radius:0 .75rem .75rem 0;padding:1rem 1.25rem;margin:1.25rem 0; }
        .highlight-box p { margin:0;color:rgba(255,255,255,.7); }
        .info-box { background:rgba(100,149,237,.06);border:1px solid rgba(100,149,237,.15);border-radius:.75rem;padding:1rem 1.25rem;margin:1.25rem 0; }
        .toc-chip { display:inline-block;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:.5rem;padding:.3rem .75rem;font-size:.8rem;color:rgba(255,255,255,.5);margin:.2rem;text-decoration:none;transition:all .2s; }
        .toc-chip:hover,.toc-chip.active { background:rgba(100,149,237,.12);border-color:#6495ed;color:#9ab4f5; }
        table { width:100%; }
        th { color:rgba(255,255,255,.75);font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;padding:.6rem 1rem;background:rgba(255,255,255,.04);border-bottom:1px solid rgba(255,255,255,.08); }
        td { padding:.65rem 1rem;font-size:.875rem;color:rgba(255,255,255,.55);border-bottom:1px solid rgba(255,255,255,.04);vertical-align:top; }
        tr:hover td { background:rgba(255,255,255,.02); }
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
        <div class="d-flex gap-2">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
            <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-sm btn-outline-light rounded-pill px-3"><i class="bi bi-house me-1"></i>Dashboard</a>
            <?php else: ?>
            <a href="<?php echo e(route('login')); ?>" class="btn btn-sm btn-outline-light rounded-pill px-3">Sign In</a>
            <a href="<?php echo e(route('register')); ?>" class="btn btn-sm rounded-pill px-3" style="background:linear-gradient(135deg,#c2185b,#7b1fa2);border:none;color:#fff">Join Free</a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</nav>

<div class="legal-hero">
    <div class="container text-center">
        <div class="section-badge mx-auto"><i class="bi bi-shield-lock"></i> Legal</div>
        <h1 style="font-family:'Playfair Display',serif;font-weight:900;font-size:clamp(2rem,5vw,3rem)" class="mb-2">Privacy Policy</h1>
        <p style="color:rgba(255,255,255,.45);font-size:.9rem" class="mb-4">Last updated: <strong style="color:rgba(255,255,255,.7)"><?php echo e(\Carbon\Carbon::now()->format('F j, Y')); ?></strong></p>
        <div class="mt-3">
            <a href="#what-we-collect"   class="toc-chip">What We Collect</a>
            <a href="#how-we-use"        class="toc-chip">How We Use It</a>
            <a href="#sharing"           class="toc-chip">Sharing</a>
            <a href="#retention"         class="toc-chip">Retention</a>
            <a href="#security"          class="toc-chip">Security</a>
            <a href="#your-rights"       class="toc-chip">Your Rights</a>
            <a href="#children"          class="toc-chip">Children</a>
            <a href="#international"     class="toc-chip">International</a>
            <a href="#changes"           class="toc-chip">Policy Changes</a>
            <a href="#contact"           class="toc-chip">Contact</a>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="legal-body">

        <div class="highlight-box mb-4">
            <p>This Privacy Policy explains how <strong><?php echo e(config('app.name')); ?></strong> collects, uses, stores, and protects your personal data. We are committed to transparency and giving you meaningful control over your information.</p>
        </div>

        <div class="legal-section" id="what-we-collect">
            <div class="section-badge"><i class="bi bi-database"></i> Section 1</div>
            <h2>What Information We Collect</h2>
            <p>We collect information you provide directly, information generated by your use of the service, and in some cases information from third parties.</p>
            <div class="table-responsive rounded-3 overflow-hidden mt-3" style="border:1px solid rgba(255,255,255,.07)">
                <table>
                    <thead><tr><th>Category</th><th>Examples</th><th>Required?</th></tr></thead>
                    <tbody>
                        <tr><td>Identity</td><td>Name, date of birth, gender, sexual orientation</td><td>Yes</td></tr>
                        <tr><td>Contact</td><td>Email address</td><td>Yes</td></tr>
                        <tr><td>Profile</td><td>Photos, bio, headline, interests, body type, education</td><td>Optional</td></tr>
                        <tr><td>Location</td><td>City, country, approximate GPS coordinates</td><td>Optional</td></tr>
                        <tr><td>Communications</td><td>Messages sent via in-app chat</td><td>N/A</td></tr>
                        <tr><td>Usage Data</td><td>Pages visited, features used, swipe activity, session times</td><td>Automatic</td></tr>
                        <tr><td>Device</td><td>IP address, browser type, operating system, device identifiers</td><td>Automatic</td></tr>
                        <tr><td>Payment</td><td>Billing address, last 4 digits of card (full card data handled by payment processor)</td><td>Premium only</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="legal-section" id="how-we-use">
            <div class="section-badge"><i class="bi bi-gear"></i> Section 2</div>
            <h2>How We Use Your Information</h2>
            <p>We use your data only for legitimate purposes necessary to operate and improve <?php echo e(config('app.name')); ?>:</p>
            <ul>
                <li><strong style="color:rgba(255,255,255,.8)">Matching &amp; Discovery:</strong> To show you profiles that match your preferences and vice versa, using location, age, and preference filters.</li>
                <li><strong style="color:rgba(255,255,255,.8)">Account Management:</strong> To create and maintain your account, authenticate you, and provide customer support.</li>
                <li><strong style="color:rgba(255,255,255,.8)">Communications:</strong> To send transactional emails (e.g. email verification, password reset, match notifications) and, with your consent, marketing communications.</li>
                <li><strong style="color:rgba(255,255,255,.8)">Safety &amp; Security:</strong> To detect and prevent fraud, abuse, and violations of our Terms, and to verify user ages.</li>
                <li><strong style="color:rgba(255,255,255,.8)">Service Improvement:</strong> To understand how users interact with the platform and to develop new features. Analytics data is anonymised where possible.</li>
                <li><strong style="color:rgba(255,255,255,.8)">Legal Compliance:</strong> To comply with applicable laws, court orders, or valid legal processes.</li>
            </ul>
            <div class="info-box mt-3">
                <p><i class="bi bi-info-circle me-2" style="color:#6495ed"></i>We do <strong style="color:#9ab4f5">not</strong> use your data to train external AI models, sell it to data brokers, or share it with advertisers for targeted advertising outside our platform.</p>
            </div>
        </div>

        <div class="legal-section" id="sharing">
            <div class="section-badge"><i class="bi bi-share"></i> Section 3</div>
            <h2>How We Share Your Information</h2>
            <p>We do not sell your personal data. We share it only in these limited circumstances:</p>
            <ul>
                <li><strong style="color:rgba(255,255,255,.8)">Other Users:</strong> Your public profile (photos, name, age, bio, interests) is visible to other registered users. Your exact location is never shared — only approximate distance is shown.</li>
                <li><strong style="color:rgba(255,255,255,.8)">Service Providers:</strong> Trusted third-party vendors who help us operate the platform (hosting, payment processing, email delivery, fraud prevention). They are bound by data processing agreements and may not use your data for their own purposes.</li>
                <li><strong style="color:rgba(255,255,255,.8)">Legal Requirements:</strong> When required by law, court order, or to protect the rights, property, or safety of <?php echo e(config('app.name')); ?>, our users, or the public.</li>
                <li><strong style="color:rgba(255,255,255,.8)">Business Transfers:</strong> In the event of a merger, acquisition, or sale of assets, your data may be transferred to the acquiring entity, subject to the same privacy protections.</li>
                <li><strong style="color:rgba(255,255,255,.8)">With Your Consent:</strong> In any other case, only with your explicit, informed consent.</li>
            </ul>
        </div>

        <div class="legal-section" id="retention">
            <div class="section-badge"><i class="bi bi-clock-history"></i> Section 4</div>
            <h2>Data Retention</h2>
            <p>We retain your personal data for as long as your account is active or as needed to provide the service. Specific retention periods:</p>
            <ul>
                <li><strong style="color:rgba(255,255,255,.8)">Active accounts:</strong> Data is retained for the lifetime of your account.</li>
                <li><strong style="color:rgba(255,255,255,.8)">Deleted accounts:</strong> Profile data is removed within 30 days of deletion. Backup copies may persist for up to 90 days before being purged.</li>
                <li><strong style="color:rgba(255,255,255,.8)">Messages:</strong> Chat messages are stored for the duration of the match. When a match is unmatched or either user deletes their account, messages are deleted within 30 days.</li>
                <li><strong style="color:rgba(255,255,255,.8)">Financial records:</strong> Billing records are retained for 7 years as required by tax and accounting regulations.</li>
                <li><strong style="color:rgba(255,255,255,.8)">Safety records:</strong> Reports and moderation actions may be retained for up to 3 years to protect the community.</li>
                <li><strong style="color:rgba(255,255,255,.8)">Log data:</strong> Server logs are retained for 90 days.</li>
            </ul>
        </div>

        <div class="legal-section" id="security">
            <div class="section-badge"><i class="bi bi-lock"></i> Section 5</div>
            <h2>Security</h2>
            <p>We implement industry-standard technical and organisational safeguards to protect your data:</p>
            <ul>
                <li>All data in transit is encrypted using TLS 1.2 or higher.</li>
                <li>Passwords are hashed using bcrypt with a high cost factor and are never stored in plaintext.</li>
                <li>Payment data is handled exclusively by PCI-DSS compliant payment processors — we never store full card numbers.</li>
                <li>Access to production data is restricted to authorised personnel on a need-to-know basis.</li>
                <li>We conduct regular security audits and vulnerability assessments.</li>
            </ul>
            <p>No system is 100% secure. If you discover a security vulnerability, please responsibly disclose it to <a href="mailto:<?php echo e($securityEmail); ?>" style="color:#9ab4f5"><?php echo e($securityEmail); ?></a> rather than publicly disclosing it.</p>
        </div>

        <div class="legal-section" id="your-rights">
            <div class="section-badge"><i class="bi bi-person-gear"></i> Section 6</div>
            <h2>Your Rights &amp; Choices</h2>
            <p>Depending on your jurisdiction, you have the following rights regarding your personal data:</p>
            <ul>
                <li><strong style="color:rgba(255,255,255,.8)">Access:</strong> Request a copy of the personal data we hold about you.</li>
                <li><strong style="color:rgba(255,255,255,.8)">Correction:</strong> Request correction of inaccurate or incomplete data.</li>
                <li><strong style="color:rgba(255,255,255,.8)">Deletion ("Right to be Forgotten"):</strong> Request deletion of your personal data. You can do this directly via Settings → Account → Delete Account, or by contacting us.</li>
                <li><strong style="color:rgba(255,255,255,.8)">Portability:</strong> Request a machine-readable export of your data.</li>
                <li><strong style="color:rgba(255,255,255,.8)">Objection:</strong> Object to processing of your data for direct marketing purposes at any time.</li>
                <li><strong style="color:rgba(255,255,255,.8)">Restriction:</strong> Request that we restrict processing of your data in certain circumstances.</li>
                <li><strong style="color:rgba(255,255,255,.8)">Withdraw consent:</strong> Where processing is based on consent, withdraw it at any time without affecting prior processing.</li>
            </ul>
            <p>To exercise any of these rights, email us at <a href="mailto:<?php echo e($privacyEmail); ?>" style="color:#9ab4f5"><?php echo e($privacyEmail); ?></a>. We will respond within 30 days (GDPR) or the timeframe required by applicable law. We may need to verify your identity before processing your request.</p>
        </div>

        <div class="legal-section" id="children">
            <div class="section-badge"><i class="bi bi-shield-exclamation"></i> Section 7</div>
            <h2>Children's Privacy</h2>
            <p><?php echo e(config('app.name')); ?> is strictly for adults aged 18 and over. We do not knowingly collect or process personal data from anyone under the age of 18.</p>
            <p>If you believe a minor has registered on our platform or provided us with personal data, please contact us immediately at <a href="mailto:<?php echo e($safetyEmail); ?>" style="color:#9ab4f5"><?php echo e($safetyEmail); ?></a>. We will investigate and delete any such data promptly.</p>
        </div>

        <div class="legal-section" id="international">
            <div class="section-badge"><i class="bi bi-globe2"></i> Section 8</div>
            <h2>International Data Transfers</h2>
            <p><?php echo e(config('app.name')); ?> operates globally. Your data may be processed in countries other than your own, including countries that may have different data protection standards.</p>
            <p>Where we transfer data internationally, we use appropriate safeguards such as:</p>
            <ul>
                <li>Standard Contractual Clauses approved by the European Commission (for EEA data).</li>
                <li>Data processing agreements requiring equivalent protections.</li>
                <li>Reliance on adequacy decisions where applicable.</li>
            </ul>
        </div>

        <div class="legal-section" id="changes">
            <div class="section-badge"><i class="bi bi-arrow-repeat"></i> Section 9</div>
            <h2>Changes to This Policy</h2>
            <p>We may update this Privacy Policy from time to time to reflect changes in our practices, technology, legal requirements, or for other operational reasons. When we make material changes, we will:</p>
            <ul>
                <li>Update the "Last updated" date at the top of this page.</li>
                <li>Notify you via email or an in-app notification at least 14 days before changes take effect.</li>
                <li>Where required by law, seek your consent for new processing activities.</li>
            </ul>
            <p>We encourage you to review this policy periodically. Continued use of <?php echo e(config('app.name')); ?> after the effective date of changes constitutes acceptance of the updated policy.</p>
        </div>

        <div class="legal-section" id="contact">
            <div class="section-badge"><i class="bi bi-envelope-heart"></i> Section 10</div>
            <h2>Contact &amp; Data Controller</h2>
            <p><?php echo e(config('app.name')); ?> is the data controller for the personal data described in this policy. For privacy-related queries:</p>
            <div class="row g-3 mt-1">
                <div class="col-12 col-sm-6">
                    <div class="p-3 rounded-3 text-center" style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.07)">
                        <i class="bi bi-shield-lock mb-2 d-block" style="color:#9ab4f5;font-size:1.4rem"></i>
                        <div style="font-size:.78rem;color:rgba(255,255,255,.45)" class="mb-1">Privacy Requests</div>
                        <a href="mailto:<?php echo e($privacyEmail); ?>" style="color:#9ab4f5;font-size:.85rem"><?php echo e($privacyEmail); ?></a>
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <div class="p-3 rounded-3 text-center" style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.07)">
                        <i class="bi bi-envelope mb-2 d-block" style="color:#9ab4f5;font-size:1.4rem"></i>
                        <div style="font-size:.78rem;color:rgba(255,255,255,.45)" class="mb-1">General Support</div>
                        <a href="mailto:<?php echo e($supportEmail); ?>" style="color:#9ab4f5;font-size:.85rem"><?php echo e($supportEmail); ?></a>
                    </div>
                </div>
            </div>
            <p class="mt-3">Also see our <a href="<?php echo e(route('legal.terms')); ?>" style="color:#9ab4f5">Terms &amp; Conditions</a> for other legal matters.</p>
        </div>

    </div>
</div>

<?php echo $__env->make('partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<script>
document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
        const t = document.querySelector(a.getAttribute('href'));
        if (t) { e.preventDefault(); t.scrollIntoView({ behavior:'smooth', block:'start' }); }
    });
});
</script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\dating\resources\views\legal\privacy.blade.php ENDPATH**/ ?>