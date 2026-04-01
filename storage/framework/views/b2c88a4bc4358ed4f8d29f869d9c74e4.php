<?php
    use App\Models\SiteSetting as SS;
    use Illuminate\Support\Str;

    $siteName = SS::get('site_name', config('app.name', 'HeartsConnect'));
    $pageTitle = isset($seoTitle) ? trim((string) $seoTitle) : trim($__env->yieldContent('title'));
    $defaultTitle = SS::get('seo_default_title') ?: $siteName;
    $metaTitle = $pageTitle !== '' ? ($pageTitle . ' — ' . $siteName) : $defaultTitle;
    $metaDescription = SS::get('seo_meta_description', 'Find meaningful connections and lasting love. Join, match, chat, and build real relationships.');
    $metaKeywords = SS::get('seo_meta_keywords', 'dating, relationships, singles, matchmaking, chat');
    $metaRobots = SS::get('seo_robots', 'index,follow');
    $canonical = url()->current();
    $ogType = $__env->yieldContent('og_type') ?: 'website';
    $googleSiteVerification = trim((string) SS::get('seo_google_site_verification', ''));
    $adsensePublisherId = trim((string) SS::get('seo_google_adsense_publisher_id', ''));
    $adsenseAutoAds = (bool) SS::get('seo_google_adsense_auto_ads', false);

    $ogImagePath = SS::get('seo_og_image');
    $ogImagePath = is_array($ogImagePath) ? reset($ogImagePath) : $ogImagePath;
    $ogImage = null;
    if ($ogImagePath) {
        $ogImage = asset('storage/' . ltrim($ogImagePath, '/'));
        if (! Str::startsWith($ogImage, ['http://', 'https://'])) {
            $ogImage = url($ogImage);
        }
    }

    $twitterHandle = trim((string) SS::get('seo_twitter_handle', ''));
    if ($twitterHandle !== '' && ! Str::startsWith($twitterHandle, '@')) {
        $twitterHandle = '@' . ltrim($twitterHandle, '@');
    }

    $organizationSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => $siteName,
        'url' => config('app.url'),
    ];

    $websiteSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => $siteName,
        'url' => config('app.url'),
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => rtrim(config('app.url'), '/') . '/discover?search={search_term_string}',
            'query-input' => 'required name=search_term_string',
        ],
    ];
?>

<meta name="description" content="<?php echo e($metaDescription); ?>">
<meta name="keywords" content="<?php echo e($metaKeywords); ?>">
<meta name="robots" content="<?php echo e($metaRobots); ?>">
<link rel="canonical" href="<?php echo e($canonical); ?>">
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($googleSiteVerification !== ''): ?>
<meta name="google-site-verification" content="<?php echo e($googleSiteVerification); ?>">
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<meta property="og:site_name" content="<?php echo e($siteName); ?>">
<meta property="og:type" content="<?php echo e($ogType); ?>">
<meta property="og:title" content="<?php echo e($metaTitle); ?>">
<meta property="og:description" content="<?php echo e($metaDescription); ?>">
<meta property="og:url" content="<?php echo e($canonical); ?>">
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ogImage): ?>
<meta property="og:image" content="<?php echo e($ogImage); ?>">
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<meta name="twitter:card" content="<?php echo e($ogImage ? 'summary_large_image' : 'summary'); ?>">
<meta name="twitter:title" content="<?php echo e($metaTitle); ?>">
<meta name="twitter:description" content="<?php echo e($metaDescription); ?>">
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ogImage): ?>
<meta name="twitter:image" content="<?php echo e($ogImage); ?>">
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($twitterHandle !== ''): ?>
<meta name="twitter:site" content="<?php echo e($twitterHandle); ?>">
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<script type="application/ld+json"><?php echo json_encode($organizationSchema, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE); ?></script>
<script type="application/ld+json"><?php echo json_encode($websiteSchema, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE); ?></script>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($adsenseAutoAds && $adsensePublisherId !== ''): ?>
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=<?php echo e($adsensePublisherId); ?>" crossorigin="anonymous"></script>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php echo $__env->yieldPushContent('seo'); ?><?php /**PATH C:\xampp\htdocs\dating\resources\views/partials/seo-meta.blade.php ENDPATH**/ ?>