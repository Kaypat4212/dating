<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        use App\Models\SiteSetting as SS;

        $supportEmail = SS::get('legal_support_email')
            ?: SS::get('footer_support_email')
            ?: ('support@' . (parse_url(config('app.url'), PHP_URL_HOST) ?? 'heartsconnect.com'));
        $safetyEmail = SS::get('legal_safety_email') ?: 'safety@heartsconnect.com';
        $billingEmail = SS::get('legal_billing_email') ?: 'billing@heartsconnect.com';
        $dmcaEmail = SS::get('legal_dmca_email') ?: 'dmca@heartsconnect.com';
    @endphp
    <title>Terms &amp; Conditions — {{ SS::get('site_name', config('app.name')) }}</title>
    @php($seoTitle = 'Terms & Conditions')
    @include('partials.seo-meta')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    <style>
        body { background: #0d0118; color: rgba(255,255,255,.85); }
        .legal-hero {
            background: linear-gradient(135deg, #1a0533 0%, #3a0a4a 50%, #1a0533 100%);
            border-bottom: 1px solid rgba(255,255,255,.07);
            padding: 4rem 0 3rem;
        }
        .legal-body { max-width: 820px; margin: 0 auto; }
        .legal-nav {
            position: sticky;
            top: 70px;
            background: rgba(26,5,51,.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 1rem;
        }
        .legal-nav a {
            display: block;
            padding: .45rem .75rem;
            color: rgba(255,255,255,.45);
            text-decoration: none;
            font-size: .8rem;
            border-radius: .5rem;
            transition: all .2s;
        }
        .legal-nav a:hover, .legal-nav a.active { background: rgba(244,143,177,.12); color: #f48fb1; }
        .section-badge {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            background: rgba(244,143,177,.1);
            color: #f48fb1;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            padding: .25rem .7rem;
            border-radius: 2rem;
            border: 1px solid rgba(244,143,177,.2);
            margin-bottom: 1rem;
        }
        .legal-section {
            border-bottom: 1px solid rgba(255,255,255,.05);
            padding: 2.5rem 0;
        }
        .legal-section:last-of-type { border-bottom: none; }
        .legal-section h2 {
            font-size: 1.35rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: .8rem;
        }
        .legal-section p, .legal-section li {
            color: rgba(255,255,255,.6);
            line-height: 1.85;
            font-size: .9375rem;
        }
        .legal-section ul, .legal-section ol {
            padding-left: 1.4rem;
        }
        .legal-section li { margin-bottom: .4rem; }
        .highlight-box {
            background: rgba(244,143,177,.06);
            border-left: 3px solid #f48fb1;
            border-radius: 0 .75rem .75rem 0;
            padding: 1rem 1.25rem;
            margin: 1.25rem 0;
        }
        .highlight-box p { margin: 0; color: rgba(255,255,255,.7); }
        .info-box {
            background: rgba(100,149,237,.06);
            border: 1px solid rgba(100,149,237,.15);
            border-radius: .75rem;
            padding: 1rem 1.25rem;
            margin: 1.25rem 0;
        }
        .warn-box {
            background: rgba(255,193,7,.05);
            border: 1px solid rgba(255,193,7,.2);
            border-radius: .75rem;
            padding: 1rem 1.25rem;
            margin: 1.25rem 0;
        }
        .warn-box p { color: rgba(255,255,255,.65); margin: 0; }
        .toc-chip {
            display: inline-block;
            background: rgba(255,255,255,.05);
            border: 1px solid rgba(255,255,255,.1);
            border-radius: .5rem;
            padding: .3rem .75rem;
            font-size: .8rem;
            color: rgba(255,255,255,.5);
            margin: .2rem;
            text-decoration: none;
            transition: all .2s;
        }
        .toc-chip:hover { background: rgba(244,143,177,.12); border-color: #f48fb1; color: #f48fb1; }
    </style>
</head>
<body>

{{-- Minimal top bar --}}
<nav class="navbar navbar-expand sticky-top shadow-sm" style="background:rgba(13,1,24,.92);backdrop-filter:blur(16px);border-bottom:1px solid rgba(255,255,255,.06)">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2 text-decoration-none" href="{{ auth()->check() ? route('dashboard') : url('/') }}">
            <x-site-logo size="md" />
        </a>
        <div class="d-flex gap-2">
            @auth
            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-light rounded-pill px-3">
                <i class="bi bi-house me-1"></i>Dashboard
            </a>
            @else
            <a href="{{ route('login') }}" class="btn btn-sm btn-outline-light rounded-pill px-3">Sign In</a>
            <a href="{{ route('register') }}" class="btn btn-sm rounded-pill px-3" style="background:linear-gradient(135deg,#c2185b,#7b1fa2);border:none;color:#fff">Join Free</a>
            @endauth
        </div>
    </div>
</nav>

{{-- Hero --}}
<div class="legal-hero">
    <div class="container text-center">
        <div class="section-badge mx-auto"><i class="bi bi-file-earmark-text"></i> Legal</div>
        <h1 style="font-family:'Playfair Display',serif;font-weight:900;font-size:clamp(2rem,5vw,3rem)" class="mb-2">Terms &amp; Conditions</h1>
        <p style="color:rgba(255,255,255,.45);font-size:.9rem" class="mb-4">Last updated: <strong style="color:rgba(255,255,255,.7)">{{ \Carbon\Carbon::now()->format('F j, Y') }}</strong> &nbsp;·&nbsp; Effective immediately</p>
        {{-- Quick nav chips --}}
        <div class="mt-3">
            <a href="#eligibility"   class="toc-chip">Eligibility</a>
            <a href="#account"       class="toc-chip">Account</a>
            <a href="#conduct"       class="toc-chip">Conduct</a>
            <a href="#content"       class="toc-chip">Content</a>
            <a href="#premium"       class="toc-chip">Premium & Payments</a>
            <a href="#privacy"       class="toc-chip">Privacy</a>
            <a href="#safety"        class="toc-chip">Safety</a>
            <a href="#cookies"       class="toc-chip">Cookies</a>
            <a href="#ip"            class="toc-chip">Intellectual Property</a>
            <a href="#disclaimers"   class="toc-chip">Disclaimers</a>
            <a href="#liability"     class="toc-chip">Liability</a>
            <a href="#community"     class="toc-chip">Community Rules</a>
            <a href="#termination"   class="toc-chip">Termination</a>
            <a href="#dmca"          class="toc-chip">DMCA</a>
            <a href="#governing-law" class="toc-chip">Governing Law</a>
            <a href="#contact"       class="toc-chip">Contact</a>
        </div>
    </div>
</div>

{{-- Main content --}}
<div class="container py-5">
    <div class="legal-body">

        {{-- Intro --}}
        <div class="highlight-box mb-4">
            <p>Please read these Terms &amp; Conditions carefully before using <strong>{{ config('app.name') }}</strong>. By accessing or using our platform, you agree to be bound by these terms. If you do not agree, please do not use the service.</p>
        </div>

        {{-- 1. Eligibility --}}
        <div class="legal-section" id="eligibility">
            <div class="section-badge"><i class="bi bi-person-check"></i> Section 1</div>
            <h2>Eligibility</h2>
            <p>To use {{ config('app.name') }}, you must:</p>
            <ul>
                <li>Be at least <strong>18 years of age</strong>. We do not knowingly permit minors to register or use the service.</li>
                <li>Be a human — automated accounts, bots, and scripts are strictly prohibited.</li>
                <li>Not be a convicted sex offender or otherwise legally prohibited from accessing such services.</li>
                <li>Have the legal capacity to enter into a binding agreement under applicable law.</li>
                <li>Not have a previously terminated {{ config('app.name') }} account due to violations of these Terms.</li>
            </ul>
            <p>By registering, you represent and warrant that you meet all eligibility requirements above. We reserve the right to verify eligibility at any time and suspend accounts that do not comply.</p>
        </div>

        {{-- 2. Account --}}
        <div class="legal-section" id="account">
            <div class="section-badge"><i class="bi bi-shield-person"></i> Section 2</div>
            <h2>Account Registration &amp; Security</h2>
            <p>When creating an account on {{ config('app.name') }}, you agree to:</p>
            <ul>
                <li>Provide accurate, current, and complete information during registration.</li>
                <li>Maintain and promptly update your account information to keep it accurate.</li>
                <li>Keep your password confidential and not share it with any third party.</li>
                <li>Notify us immediately at <a href="mailto:{{ $supportEmail }}" style="color:#f48fb1">{{ $supportEmail }}</a> if you suspect unauthorised access to your account.</li>
                <li>Accept responsibility for all activity that occurs under your account.</li>
            </ul>
            <div class="info-box">
                <p><i class="bi bi-info-circle me-2" style="color:#6495ed"></i><strong style="color:#9ab4f5">One account per person.</strong> Creating multiple accounts to circumvent bans or limitations is a violation of these Terms and will result in permanent suspension of all associated accounts.</p>
            </div>
            <p>You may not transfer, sell, or sublicense your account to any other person. Account access is personal and non-transferable.</p>
        </div>

        {{-- 3. Conduct --}}
        <div class="legal-section" id="conduct">
            <div class="section-badge"><i class="bi bi-hand-thumbs-up"></i> Section 3</div>
            <h2>User Conduct &amp; Prohibited Activities</h2>
            <p>{{ config('app.name') }} is a platform for genuine human connection. You agree <strong>not</strong> to:</p>
            <ul>
                <li>Harass, bully, threaten, stalk, intimidate, or abuse any other user.</li>
                <li>Post or transmit fraudulent, deceptive, misleading, or false information.</li>
                <li>Impersonate any person or entity, or misrepresent your affiliation with any person or entity.</li>
                <li>Solicit money, financial information, or other personal information from other users.</li>
                <li>Send unsolicited commercial messages (spam) or engage in pyramid schemes.</li>
                <li>Use the platform for prostitution, escort services, or any illegal sexual activity.</li>
                <li>Upload viruses, malicious code, or interfere with the platform's infrastructure.</li>
                <li>Scrape, crawl, or use automated tools to collect data from the platform.</li>
                <li>Circumvent, disable, or interfere with security features or access controls.</li>
                <li>Post content that is racist, sexist, hateful, discriminatory, or promotes violence.</li>
                <li>Share explicit sexual content unless explicitly permitted in designated areas.</li>
                <li>Violate any applicable local, national, or international law or regulation.</li>
            </ul>
            <div class="warn-box">
                <p><i class="bi bi-exclamation-triangle me-2" style="color:#ffc107"></i><strong style="color:#ffd54f">Violation of these conduct rules may result in immediate account suspension</strong>, content removal, reporting to law enforcement, and/or legal action where appropriate.</p>
            </div>
        </div>

        {{-- 4. Content --}}
        <div class="legal-section" id="content">
            <div class="section-badge"><i class="bi bi-images"></i> Section 4</div>
            <h2>User-Generated Content</h2>
            <p>You are solely responsible for all content — photos, messages, profile text, and other materials — that you post or transmit through {{ config('app.name') }}.</p>
            <p>By submitting content, you grant {{ config('app.name') }} a worldwide, royalty-free, non-exclusive, sublicensable licence to:</p>
            <ul>
                <li>Host, store, display, and distribute your content solely to operate and improve the service.</li>
                <li>Use anonymised or aggregated data derived from your content for analytical purposes.</li>
                <li>Moderate, remove, or restrict access to content that violates these Terms.</li>
            </ul>
            <p>You represent and warrant that:</p>
            <ul>
                <li>You own or have the rights to all content you submit.</li>
                <li>Your content does not infringe the intellectual property, privacy, or other rights of any third party.</li>
                <li>Your content is not illegal, harmful, or prohibited under these Terms.</li>
                <li>All persons depicted in photos you upload have consented to such use.</li>
            </ul>
            <div class="highlight-box">
                <p><i class="bi bi-camera me-2" style="color:#f48fb1"></i><strong>Profile photos</strong> must be real photos of yourself. Photos depicting minors, nudity, graphic violence, or drug use are strictly prohibited and will be removed immediately.</p>
            </div>
        </div>

        {{-- 5. Premium --}}
        <div class="legal-section" id="premium">
            <div class="section-badge"><i class="bi bi-gem"></i> Section 5</div>
            <h2>Premium Memberships &amp; Payments</h2>
            <p>{{ config('app.name') }} offers optional paid premium features ("Premium"). By subscribing to Premium, you agree to the following:</p>
            <h6 class="mt-3 mb-2" style="color:rgba(255,255,255,.8)">Billing</h6>
            <ul>
                <li>Subscription fees are billed in advance on a recurring basis (monthly or annually, as selected).</li>
                <li>Payment is processed through our third-party payment processors. You authorise us to charge your selected payment method.</li>
                <li>All prices are displayed inclusive of applicable taxes where required by law.</li>
            </ul>
            <h6 class="mt-3 mb-2" style="color:rgba(255,255,255,.8)">Cancellation &amp; Refunds</h6>
            <ul>
                <li>You may cancel your subscription at any time through your account settings. Cancellation takes effect at the end of the current billing period.</li>
                <li>We do not provide refunds for partial subscription periods unless required by applicable consumer law.</li>
                <li>If you believe a charge was made in error, contact us within 30 days at <a href="mailto:{{ $billingEmail }}" style="color:#f48fb1">{{ $billingEmail }}</a>.</li>
            </ul>
            <h6 class="mt-3 mb-2" style="color:rgba(255,255,255,.8)">Virtual Credits &amp; Boosts</h6>
            <ul>
                <li>Virtual credits and boost packs are non-refundable and expire as stated at the time of purchase.</li>
                <li>Virtual items have no monetary value and cannot be transferred or redeemed for cash.</li>
            </ul>
            <h6 class="mt-3 mb-2" style="color:rgba(255,255,255,.8)">Price Changes</h6>
            <p>We reserve the right to change Premium pricing at any time. Existing subscribers will be given at least 30 days' notice of any price increase before it applies to their account.</p>
        </div>

        {{-- 6. Privacy --}}
        <div class="legal-section" id="privacy">
            <div class="section-badge"><i class="bi bi-shield-lock"></i> Section 6</div>
            <h2>Privacy &amp; Data</h2>
            <p>Your privacy matters to us. Our full <a href="{{ route('legal.privacy') }}" style="color:#f48fb1">Privacy Policy</a> explains in detail how we collect, use, and protect your personal data. Key points:</p>
            <ul>
                <li>We collect only the data necessary to operate the service and improve your experience.</li>
                <li>We do not sell your personal data to third parties for their marketing purposes.</li>
                <li>Location data is used solely to show nearby profiles and is never shared with other users at precise resolution.</li>
                <li>You can request deletion of your account and associated data at any time by visiting Settings → Account → Delete Account.</li>
                <li>We comply with applicable data protection laws including GDPR (for EU residents) and similar frameworks.</li>
                <li>We use industry-standard encryption (TLS/SSL) to protect data in transit and at rest.</li>
            </ul>
            <div class="info-box">
                <p><i class="bi bi-info-circle me-2" style="color:#6495ed"></i>By using {{ config('app.name') }}, you consent to the collection and use of your data as described in our <a href="{{ route('legal.privacy') }}" style="color:#9ab4f5">Privacy Policy</a>.</p>
            </div>
        </div>

        {{-- 7. Safety --}}
        <div class="legal-section" id="safety">
            <div class="section-badge"><i class="bi bi-heart-pulse"></i> Section 7</div>
            <h2>Safety &amp; Reporting</h2>
            <p>{{ config('app.name') }} is committed to your safety. However, we cannot guarantee the behaviour of other users and are not responsible for offline interactions.</p>
            <ul>
                <li>Always meet new people in public places for the first time.</li>
                <li>Never share financial information, home address, or workplace details with matches you have not thoroughly vetted.</li>
                <li>Use the in-app reporting and blocking tools if you encounter inappropriate behaviour.</li>
                <li>We review all reports within 24–72 hours and take appropriate action, which may include warnings, suspension, or permanent bans.</li>
                <li>In cases involving imminent danger, please contact local emergency services first.</li>
            </ul>
            <p>We maintain a dedicated Safety Team and partner with organisations combating online exploitation. To report serious concerns, email <a href="mailto:{{ $safetyEmail }}" style="color:#f48fb1">{{ $safetyEmail }}</a>.</p>
        </div>

        {{-- 8. Cookies --}}
        <div class="legal-section" id="cookies">
            <div class="section-badge"><i class="bi bi-cookie"></i> Section 8</div>
            <h2>Cookie Policy</h2>
            <p>{{ config('app.name') }} uses cookies and similar tracking technologies to enhance your experience. We use the following types of cookies:</p>
            <ul>
                <li><strong style="color:rgba(255,255,255,.8)">Strictly Necessary:</strong> Required for the platform to function (session management, authentication, CSRF protection). Cannot be disabled.</li>
                <li><strong style="color:rgba(255,255,255,.8)">Functional:</strong> Remember your preferences, language settings, and theme choices.</li>
                <li><strong style="color:rgba(255,255,255,.8)">Analytics:</strong> Help us understand how users interact with the platform so we can improve it. Data is anonymised.</li>
                <li><strong style="color:rgba(255,255,255,.8)">Marketing:</strong> Used only if you opt in. Allow us to show relevant promotions within the platform.</li>
            </ul>
            <p>You can manage your cookie preferences at any time through your browser settings or by clicking "Cookie Settings" in the footer. Disabling functional or analytics cookies may affect your experience.</p>
        </div>

        {{-- 9. Intellectual Property --}}
        <div class="legal-section" id="ip">
            <div class="section-badge"><i class="bi bi-c-circle"></i> Section 9</div>
            <h2>Intellectual Property</h2>
            <p>All content and materials on {{ config('app.name') }} — including but not limited to logos, design, code, text, graphics, and user interface elements — are owned by or licenced to {{ config('app.name') }} and are protected by applicable intellectual property laws.</p>
            <ul>
                <li>You may not copy, reproduce, distribute, or create derivative works of our proprietary content without explicit written permission.</li>
                <li>The {{ config('app.name') }} name, logo, and related marks are trademarks. Unauthorised use is prohibited.</li>
                <li>You retain ownership of content you upload, subject to the licence granted in Section 4.</li>
            </ul>
        </div>

        {{-- 10. Disclaimers --}}
        <div class="legal-section" id="disclaimers">
            <div class="section-badge"><i class="bi bi-exclamation-circle"></i> Section 10</div>
            <h2>Disclaimers</h2>
            <p>{{ config('app.name') }} is provided on an <strong>"as is"</strong> and <strong>"as available"</strong> basis, without warranties of any kind, either express or implied. We expressly disclaim:</p>
            <ul>
                <li>Any warranty of merchantability, fitness for a particular purpose, or non-infringement.</li>
                <li>Any warranty that the service will be uninterrupted, error-free, or free of viruses or harmful components.</li>
                <li>Any warranty as to the accuracy, reliability, or completeness of any content on the platform, including user-generated content.</li>
                <li>Any responsibility for the conduct, online or offline, of any user of the platform.</li>
            </ul>
            <div class="warn-box">
                <p><i class="bi bi-exclamation-triangle me-2" style="color:#ffc107"></i>{{ config('app.name') }} is a platform for meeting people, not a matchmaking service. We make no guarantee of romantic success, compatibility, or the authenticity of any user's identity.</p>
            </div>
        </div>

        {{-- 11. Liability --}}
        <div class="legal-section" id="liability">
            <div class="section-badge"><i class="bi bi-balance-scale" style="font-family:serif"></i> Section 11</div>
            <h2>Limitation of Liability</h2>
            <p>To the maximum extent permitted by applicable law, {{ config('app.name') }} and its officers, directors, employees, and partners shall not be liable for:</p>
            <ul>
                <li>Any indirect, incidental, special, consequential, or punitive damages.</li>
                <li>Loss of profits, data, goodwill, or other intangible losses.</li>
                <li>Any damages arising from your use of or inability to use the service.</li>
                <li>Any conduct or content of any third party or other user on the service.</li>
                <li>Any unauthorised access to or alteration of your transmissions or data.</li>
            </ul>
            <p>Our total aggregate liability to you for any claim arising out of or relating to these Terms or the service shall not exceed the greater of: (a) the amount you paid us in the 12 months preceding the claim, or (b) <strong>$100 USD</strong>.</p>
        </div>

        {{-- 12. Community Rules --}}
        <div class="legal-section" id="community">
            <div class="section-badge"><i class="bi bi-people"></i> Section 12</div>
            <h2>Community Rules</h2>
            <p>Beyond the conduct rules in Section 3, we ask that all members of {{ config('app.name') }} uphold these community standards to help us build a positive, safe environment:</p>
            <ul>
                <li><strong style="color:rgba(255,255,255,.8)">Be yourself:</strong> Use real photos and honest descriptions. Authenticity builds trust.</li>
                <li><strong style="color:rgba(255,255,255,.8)">Respect rejection:</strong> If someone isn't interested, respect their decision gracefully.</li>
                <li><strong style="color:rgba(255,255,255,.8)">Be kind:</strong> Treat all users with dignity, regardless of their background, appearance, or orientation.</li>
                <li><strong style="color:rgba(255,255,255,.8)">No ghosting abuse:</strong> While unmatching is allowed, repeatedly baiting users into conversations and ghosting may trigger a review of your account.</li>
                <li><strong style="color:rgba(255,255,255,.8)">Inclusive language:</strong> Discriminatory language based on race, ethnicity, religion, gender, sexual orientation, disability, or nationality is not tolerated.</li>
                <li><strong style="color:rgba(255,255,255,.8)">Protect your safety:</strong> Never share passwords, financial details, or sensitive personal information with matches.</li>
            </ul>
        </div>

        {{-- 13. Termination --}}
        <div class="legal-section" id="termination">
            <div class="section-badge"><i class="bi bi-door-closed"></i> Section 13</div>
            <h2>Termination &amp; Suspension</h2>
            <p>We reserve the right to suspend or permanently terminate your account, with or without notice, for any reason including but not limited to:</p>
            <ul>
                <li>Violation of any provision of these Terms.</li>
                <li>Behaviour that we determine, in our sole discretion, to be harmful to other users or the platform.</li>
                <li>Extended periods of inactivity (accounts inactive for more than 24 months may be removed).</li>
                <li>Requests by law enforcement or government agencies.</li>
            </ul>
            <p>Upon termination:</p>
            <ul>
                <li>Your right to access the service ceases immediately.</li>
                <li>Your profile and content will be removed from public view.</li>
                <li>Any unused Premium subscription time or virtual credits are forfeited, except where refunds are required by law.</li>
                <li>Provisions of these Terms that by their nature should survive termination (including Sections 4, 8, 9, 10, 11, and 15) shall remain in effect.</li>
            </ul>
            <p>You may also delete your own account at any time via Settings → Account → Delete Account.</p>
        </div>

        {{-- 14. DMCA --}}
        <div class="legal-section" id="dmca">
            <div class="section-badge"><i class="bi bi-file-lock"></i> Section 14</div>
            <h2>DMCA &amp; Copyright Takedowns</h2>
            <p>If you believe content on {{ config('app.name') }} infringes your copyright, you may submit a takedown notice under the Digital Millennium Copyright Act (DMCA) by sending the following information to our Designated Copyright Agent:</p>
            <ol>
                <li>A description of the copyrighted work you claim has been infringed.</li>
                <li>A description of where the allegedly infringing material is located on the platform (URL or description).</li>
                <li>Your contact information (name, address, phone, email).</li>
                <li>A statement that you have a good faith belief that the use is not authorised by the copyright owner, its agent, or the law.</li>
                <li>A statement, made under penalty of perjury, that the information in your notice is accurate and that you are the copyright owner or authorised to act on the owner's behalf.</li>
                <li>Your physical or electronic signature.</li>
            </ol>
            <p>Send notices to: <a href="mailto:{{ $dmcaEmail }}" style="color:#f48fb1">{{ $dmcaEmail }}</a></p>
            <p>We will respond to valid DMCA notices promptly and remove infringing content where required. Repeat infringers will have their accounts terminated.</p>
        </div>

        {{-- 15. Governing Law --}}
        <div class="legal-section" id="governing-law">
            <div class="section-badge"><i class="bi bi-globe2"></i> Section 15</div>
            <h2>Governing Law &amp; Dispute Resolution</h2>
            <p>These Terms shall be governed by and construed in accordance with applicable law. Any dispute arising out of or relating to these Terms or your use of {{ config('app.name') }} shall first be attempted to be resolved through good-faith negotiation.</p>
            <p>If negotiation fails, disputes shall be resolved through binding arbitration, except that either party may seek injunctive or other equitable relief in a court of competent jurisdiction to prevent irreparable harm.</p>
            <p><strong style="color:rgba(255,255,255,.8)">Class Action Waiver:</strong> You agree that any dispute resolution proceedings will be conducted only on an individual basis and not in a class, consolidated, or representative action.</p>
            <p>We reserve the right to update these Terms at any time. We will notify users of material changes via email or an in-app notification at least 14 days before the changes take effect. Continued use of the service after that date constitutes acceptance of the updated Terms.</p>
        </div>

        {{-- 16. Contact --}}
        <div class="legal-section" id="contact">
            <div class="section-badge"><i class="bi bi-envelope-heart"></i> Section 16</div>
            <h2>Contact Us</h2>
            <p>Questions about these Terms? We're here to help:</p>
            <div class="row g-3 mt-1">
                <div class="col-12 col-sm-4">
                    <div class="p-3 rounded-3 text-center" style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.07)">
                        <i class="bi bi-envelope mb-2 d-block" style="color:#f48fb1;font-size:1.4rem"></i>
                        <div style="font-size:.78rem;color:rgba(255,255,255,.45)" class="mb-1">General Support</div>
                        <a href="mailto:{{ $supportEmail }}" style="color:#f48fb1;font-size:.85rem">{{ $supportEmail }}</a>
                    </div>
                </div>
                <div class="col-12 col-sm-4">
                    <div class="p-3 rounded-3 text-center" style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.07)">
                        <i class="bi bi-shield-check mb-2 d-block" style="color:#f48fb1;font-size:1.4rem"></i>
                        <div style="font-size:.78rem;color:rgba(255,255,255,.45)" class="mb-1">Safety Team</div>
                        <a href="mailto:{{ $safetyEmail }}" style="color:#f48fb1;font-size:.85rem">{{ $safetyEmail }}</a>
                    </div>
                </div>
                <div class="col-12 col-sm-4">
                    <div class="p-3 rounded-3 text-center" style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.07)">
                        <i class="bi bi-credit-card mb-2 d-block" style="color:#f48fb1;font-size:1.4rem"></i>
                        <div style="font-size:.78rem;color:rgba(255,255,255,.45)" class="mb-1">Billing</div>
                        <a href="mailto:{{ $billingEmail }}" style="color:#f48fb1;font-size:.85rem">{{ $billingEmail }}</a>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- /.legal-body --}}
</div>

@include('partials.footer')

{{-- Smooth scroll + active nav --}}
<script>
document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
        const target = document.querySelector(a.getAttribute('href'));
        if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
    });
});
// Highlight active section
const sections = document.querySelectorAll('.legal-section[id]');
const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            document.querySelectorAll('.toc-chip').forEach(c => c.classList.remove('active'));
            const active = document.querySelector(`.toc-chip[href="#${entry.target.id}"]`);
            if (active) active.classList.add('active');
        }
    });
}, { threshold: 0.35 });
sections.forEach(s => observer.observe(s));
</script>
</body>
</html>
