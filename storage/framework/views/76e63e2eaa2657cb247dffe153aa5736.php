<?php
    use App\Models\SiteSetting as SS;

    $footerBlurb = SS::get('footer_blurb', 'A safe, inclusive space to find meaningful connections and lasting love. Every heart deserves to be found.');
    $supportEmail = SS::get('footer_support_email') ?: ('support@' . (parse_url(config('app.url'), PHP_URL_HOST) ?? 'heartsconnect.com'));
    $copyrightText = SS::get('footer_copyright_text', 'All rights reserved. Made with love for love seekers worldwide.');
    $appBadgeText = SS::get('footer_app_badge_text', 'Coming Soon');
    $socialLinks = [
        ['title' => 'Facebook', 'icon' => 'bi bi-facebook', 'url' => SS::get('footer_facebook_url')],
        ['title' => 'Instagram', 'icon' => 'bi bi-instagram', 'url' => SS::get('footer_instagram_url')],
        ['title' => 'Twitter / X', 'icon' => 'bi bi-twitter-x', 'url' => SS::get('footer_twitter_url')],
        ['title' => 'TikTok', 'icon' => 'bi bi-tiktok', 'url' => SS::get('footer_tiktok_url')],
    ];
    $appLinks = [
        [
            'title' => 'App Store',
            'icon' => 'bi bi-apple',
            'url' => SS::get('footer_app_store_url'),
            'prefix' => 'Download on the',
        ],
        [
            'title' => 'Google Play',
            'icon' => 'bi bi-google-play',
            'url' => SS::get('footer_google_play_url'),
            'prefix' => 'Get it on',
        ],
    ];
    $supportLinks = [
        ['label' => 'Contact Us',        'url' => SS::get('footer_contact_url')          ?: route('pages.contact')],
        ['label' => 'Help Center',       'url' => SS::get('footer_help_center_url')       ?: route('pages.help-center')],
        ['label' => 'Safety Tips',       'url' => SS::get('footer_safety_tips_url')       ?: route('pages.safety-tips')],
        ['label' => 'Report Abuse',      'url' => SS::get('footer_report_abuse_url')      ?: route('pages.report-abuse')],
        ['label' => 'Feature Requests',  'url' => route('pages.feature-request')],
        ['label' => 'Cookie Settings',   'url' => SS::get('footer_cookie_settings_url')   ?: route('pages.cookie-settings')],
    ];
?>

<footer class="site-footer mt-auto py-5" style="background:linear-gradient(180deg,#0d0118 0%,#1a0533 100%);border-top:1px solid rgba(255,255,255,.07)">
    <div class="container">
        <div class="row g-4 mb-4">

            
            <div class="col-12 col-md-4">
                <a href="<?php echo e(auth()->check() ? route('dashboard') : url('/')); ?>" class="d-inline-flex align-items-center gap-2 text-decoration-none mb-3">
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
                <p class="mb-3" style="color:rgba(255,255,255,.45);font-size:.875rem;max-width:280px;line-height:1.7">
                    <?php echo e($footerBlurb); ?>

                </p>
                <div class="d-flex gap-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $socialLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $social): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($social['url']): ?>
                            <a href="<?php echo e($social['url']); ?>" class="footer-social" title="<?php echo e($social['title']); ?>" target="_blank" rel="noopener noreferrer"><i class="<?php echo e($social['icon']); ?>"></i></a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <div class="col-6 col-md-2">
                <h6 class="footer-heading mb-3">Explore</h6>
                <ul class="footer-links">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                    <li><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
                    <li><a href="<?php echo e(route('swipe.deck')); ?>">Swipe</a></li>
                    <li><a href="<?php echo e(route('discover.index')); ?>">Browse</a></li>
                    <li><a href="<?php echo e(route('matches.index')); ?>">Matches</a></li>
                    <li><a href="<?php echo e(route('conversations.index')); ?>">Messages</a></li>
                    <?php else: ?>
                    <li><a href="<?php echo e(route('login')); ?>">Sign In</a></li>
                    <li><a href="<?php echo e(route('register')); ?>">Join Free</a></li>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </ul>
            </div>

            
            <div class="col-6 col-md-2">
                <h6 class="footer-heading mb-3">Support</h6>
                <ul class="footer-links">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $supportLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><a href="<?php echo e($link['url']); ?>"><?php echo e($link['label']); ?></a></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </ul>
            </div>

            
            <div class="col-6 col-md-2">
                <h6 class="footer-heading mb-3">Legal</h6>
                <ul class="footer-links">
                    <li><a href="<?php echo e(route('legal.terms')); ?>">Terms &amp; Conditions</a></li>
                    <li><a href="<?php echo e(route('legal.privacy')); ?>">Privacy Policy</a></li>
                    <li><a href="<?php echo e(route('legal.terms')); ?>#cookies">Cookie Policy</a></li>
                    <li><a href="<?php echo e(route('legal.terms')); ?>#community">Community Rules</a></li>
                    <li><a href="<?php echo e(route('legal.terms')); ?>#dmca">DMCA</a></li>
                </ul>
            </div>

            
            <div class="col-6 col-md-2">
                <h6 class="footer-heading mb-3">Get the App</h6>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $appLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $appLink): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="position-relative d-inline-block <?php echo e($index === 0 ? 'mb-2' : ''); ?>" style="cursor:<?php echo e($appLink['url'] ? 'pointer' : 'not-allowed'); ?>;" title="<?php echo e($appLink['url'] ? $appLink['title'] : 'Not available yet'); ?>">
                    <a href="<?php echo e($appLink['url'] ?: '#'); ?>" class="d-flex align-items-center gap-2 footer-app-btn <?php echo e($appLink['url'] ? '' : 'pe-none'); ?>" style="opacity:<?php echo e($appLink['url'] ? '1' : '.45'); ?>;" <?php if(! $appLink['url']): ?> tabindex="-1" aria-disabled="true" <?php else: ?> target="_blank" rel="noopener noreferrer" <?php endif; ?>>
                        <i class="<?php echo e($appLink['icon']); ?>" style="font-size:1.3rem"></i>
                        <div class="lh-sm"><span style="font-size:.65rem;opacity:.7"><?php echo e($appLink['prefix']); ?></span><br><strong style="font-size:.85rem"><?php echo e($appLink['title']); ?></strong></div>
                    </a>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $appLink['url'] && $appBadgeText): ?>
                    <span style="position:absolute;top:-6px;right:-6px;background:#f48fb1;color:#fff;font-size:.55rem;font-weight:700;letter-spacing:.04em;padding:2px 5px;border-radius:30px;white-space:nowrap;line-height:1.4;pointer-events:none;"><?php echo e($appBadgeText); ?></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        
        <div style="border-top:1px solid rgba(255,255,255,.07)" class="pt-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <p class="mb-0" style="color:rgba(255,255,255,.3);font-size:.8rem">
                &copy; <?php echo e(date('Y')); ?> <?php echo e(SS::get('site_name', config('app.name'))); ?>. <?php echo e($copyrightText); ?>

            </p>
            <div class="d-flex gap-3" style="font-size:.8rem">
                <a href="<?php echo e(route('legal.terms')); ?>" class="footer-micro-link">Terms</a>
                <a href="<?php echo e(route('legal.privacy')); ?>" class="footer-micro-link">Privacy</a>
                <a href="<?php echo e(route('legal.terms')); ?>#cookies" class="footer-micro-link">Cookies</a>
            </div>
        </div>
    </div>
</footer>

<style>
.footer-heading {
    color: rgba(255,255,255,.75);
    font-size: .75rem;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
}
.footer-links {
    list-style: none;
    padding: 0;
    margin: 0;
}
.footer-links li + li { margin-top: .45rem; }
.footer-links a {
    color: rgba(255,255,255,.42);
    text-decoration: none;
    font-size: .875rem;
    transition: color .2s;
}
.footer-links a:hover { color: #f48fb1; }
.footer-social {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(255,255,255,.07);
    color: rgba(255,255,255,.5);
    font-size: 1rem;
    text-decoration: none;
    transition: background .2s, color .2s;
}
.footer-social:hover { background: #f48fb1; color: #fff; }
.footer-app-btn {
    background: rgba(255,255,255,.07);
    border: 1px solid rgba(255,255,255,.1);
    border-radius: .75rem;
    padding: .5rem .85rem;
    color: rgba(255,255,255,.75);
    text-decoration: none;
    transition: background .2s, border-color .2s;
}
.footer-app-btn:hover { background: rgba(244,143,177,.15); border-color: #f48fb1; color: #f48fb1; }
.footer-micro-link {
    color: rgba(255,255,255,.3);
    text-decoration: none;
    transition: color .2s;
}
.footer-micro-link:hover { color: #f48fb1; }
</style>
<?php /**PATH C:\xampp\htdocs\dating\resources\views\partials\footer.blade.php ENDPATH**/ ?>