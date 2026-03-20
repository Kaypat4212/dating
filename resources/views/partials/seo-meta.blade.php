@php
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
@endphp

<meta name="description" content="{{ $metaDescription }}">
<meta name="keywords" content="{{ $metaKeywords }}">
<meta name="robots" content="{{ $metaRobots }}">
<link rel="canonical" href="{{ $canonical }}">
@if($googleSiteVerification !== '')
<meta name="google-site-verification" content="{{ $googleSiteVerification }}">
@endif

<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:type" content="{{ $ogType }}">
<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:url" content="{{ $canonical }}">
@if($ogImage)
<meta property="og:image" content="{{ $ogImage }}">
@endif

<meta name="twitter:card" content="{{ $ogImage ? 'summary_large_image' : 'summary' }}">
<meta name="twitter:title" content="{{ $metaTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
@if($ogImage)
<meta name="twitter:image" content="{{ $ogImage }}">
@endif
@if($twitterHandle !== '')
<meta name="twitter:site" content="{{ $twitterHandle }}">
@endif

<script type="application/ld+json">{!! json_encode($organizationSchema, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
<script type="application/ld+json">{!! json_encode($websiteSchema, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>

@if($adsenseAutoAds && $adsensePublisherId !== '')
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ $adsensePublisherId }}" crossorigin="anonymous"></script>
@endif

@stack('seo')