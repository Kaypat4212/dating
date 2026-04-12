@php
    use App\Models\SiteSetting as SS;

    $footerBlurb = SS::get('footer_blurb', 'A safe, inclusive space to find meaningful connections and lasting love. Every heart deserves to be found.');
    $supportEmail = SS::get('footer_support_email') ?: ('support@' . (parse_url(config('app.url'), PHP_URL_HOST) ?? 'heartsconnect.com'));
    $copyrightText = SS::get('footer_copyright_text', 'All rights reserved. Made with love for love seekers worldwide.');
    $appLinks = [
        [
            'title'  => 'App Store',
            'icon'   => 'bi bi-apple',
            'url'    => SS::get('footer_app_store_url'),
            'prefix' => 'Download on the',
        ],
        [
            'title'  => 'Google Play',
            'icon'   => 'bi bi-google-play',
            'url'    => SS::get('footer_google_play_url'),
            'prefix' => 'Get it on',
        ],
        [
            'title'  => 'Android APK',
            'icon'   => 'bi bi-android2',
            'url'    => SS::get('footer_apk_url'),
            'prefix' => 'Download',
        ],
        [
            'title'  => 'Windows App',
            'icon'   => 'bi bi-windows',
            'url'    => SS::get('footer_exe_url'),
            'prefix' => 'Download for',
        ],
        [
            'title'  => 'Web App',
            'icon'   => 'bi bi-globe2',
            'url'    => SS::get('footer_webapp_url'),
            'prefix' => 'Open as',
        ],
    ];
    // Only show links that have been configured
    $activeAppLinks = array_filter($appLinks, fn($l) => !empty($l['url']));
    $socialLinks = [
        ['title' => 'Facebook', 'icon' => 'bi bi-facebook', 'url' => SS::get('footer_facebook_url')],
        ['title' => 'Instagram', 'icon' => 'bi bi-instagram', 'url' => SS::get('footer_instagram_url')],
        ['title' => 'Twitter / X', 'icon' => 'bi bi-twitter-x', 'url' => SS::get('footer_twitter_url')],
        ['title' => 'TikTok', 'icon' => 'bi bi-tiktok', 'url' => SS::get('footer_tiktok_url')],
    ];

    $supportLinks = [
        ['label' => 'Contact Us',        'url' => SS::get('footer_contact_url')          ?: route('pages.contact')],
        ['label' => 'Help Center',       'url' => SS::get('footer_help_center_url')       ?: route('pages.help-center')],
        ['label' => 'Safety Tips',       'url' => SS::get('footer_safety_tips_url')       ?: route('pages.safety-tips')],
        ['label' => 'Report Abuse',      'url' => SS::get('footer_report_abuse_url')      ?: route('pages.report-abuse')],
        ['label' => 'User Reviews',      'url' => route('reviews.index')],
        ['label' => 'Feature Requests',  'url' => route('pages.feature-request')],
        ['label' => 'Cookie Settings',   'url' => SS::get('footer_cookie_settings_url')   ?: route('pages.cookie-settings')],
    ];
@endphp

<footer class="site-footer mt-auto py-5" style="background:linear-gradient(180deg,#0d0118 0%,#1a0533 100%);border-top:1px solid rgba(255,255,255,.07)">
    <div class="container">
        <div class="row g-4 mb-4">

            {{-- Brand column --}}
            <div class="col-12 col-md-4">
                <a href="{{ auth()->check() ? route('dashboard') : url('/') }}" class="d-inline-flex align-items-center gap-2 text-decoration-none mb-3">
                    <x-site-logo size="md" />
                </a>
                <p class="mb-3" style="color:rgba(255,255,255,.45);font-size:.875rem;max-width:280px;line-height:1.7">
                    {{ $footerBlurb }}
                </p>
                <div class="d-flex gap-3">
                    @foreach($socialLinks as $social)
                        @if($social['url'])
                            <a href="{{ $social['url'] }}" class="footer-social" title="{{ $social['title'] }}" target="_blank" rel="noopener noreferrer"><i class="{{ $social['icon'] }}"></i></a>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Explore --}}
            <div class="col-6 col-md-2">
                <h6 class="footer-heading mb-3">Explore</h6>
                <ul class="footer-links">
                    @auth
                    <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('swipe.deck') }}">Swipe</a></li>
                    <li><a href="{{ route('discover.index') }}">Browse</a></li>
                    <li><a href="{{ route('matches.index') }}">Matches</a></li>
                    <li><a href="{{ route('conversations.index') }}">Messages</a></li>
                    @else
                    <li><a href="{{ route('login') }}">Sign In</a></li>
                    <li><a href="{{ route('register') }}">Join Free</a></li>
                    @endauth
                </ul>
            </div>

            {{-- Support --}}
            <div class="col-6 col-md-2">
                <h6 class="footer-heading mb-3">Support</h6>
                <ul class="footer-links">
                    @foreach($supportLinks as $link)
                    <li><a href="{{ $link['url'] }}">{{ $link['label'] }}</a></li>
                    @endforeach
                </ul>
            </div>

            {{-- Legal --}}
            <div class="col-6 col-md-2">
                <h6 class="footer-heading mb-3">Legal</h6>
                <ul class="footer-links">
                    <li><a href="{{ route('legal.terms') }}">Terms &amp; Conditions</a></li>
                    <li><a href="{{ route('legal.privacy') }}">Privacy Policy</a></li>
                    <li><a href="{{ route('legal.terms') }}#cookies">Cookie Policy</a></li>
                    <li><a href="{{ route('legal.terms') }}#community">Community Rules</a></li>
                    <li><a href="{{ route('legal.terms') }}#dmca">DMCA</a></li>
                </ul>
            </div>

            {{-- App / CTA --}}
            <div class="col-6 col-md-2">
                <h6 class="footer-heading mb-3">Get the App</h6>
                @if(count($activeAppLinks) > 0)
                    @foreach($activeAppLinks as $appLink)
                    <a href="{{ $appLink['url'] }}" class="d-flex align-items-center gap-2 footer-app-btn mb-2" target="_blank" rel="noopener noreferrer" title="{{ $appLink['title'] }}">
                        <i class="{{ $appLink['icon'] }}" style="font-size:1.3rem"></i>
                        <div class="lh-sm"><span style="font-size:.65rem;opacity:.7">{{ $appLink['prefix'] }}</span><br><strong style="font-size:.85rem">{{ $appLink['title'] }}</strong></div>
                    </a>
                    @endforeach
                @else
                <p style="color:rgba(255,255,255,.3);font-size:.78rem;line-height:1.6">Apps & desktop clients<br>coming soon!</p>
                @endif
            </div>
        </div>

        {{-- Divider --}}
        <div style="border-top:1px solid rgba(255,255,255,.07)" class="pt-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <p class="mb-0" style="color:rgba(255,255,255,.3);font-size:.8rem">
                &copy; {{ date('Y') }} {{ SS::get('site_name', config('app.name')) }}. {{ $copyrightText }}
            </p>
            <div class="d-flex gap-3" style="font-size:.8rem">
                <a href="{{ route('legal.terms') }}" class="footer-micro-link">Terms</a>
                <a href="{{ route('legal.privacy') }}" class="footer-micro-link">Privacy</a>
                <a href="{{ route('legal.terms') }}#cookies" class="footer-micro-link">Cookies</a>
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
