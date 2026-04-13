<x-filament-panels::page>

@php
/* ══════════════════════════════════════════════════════════════════════════
   Service definitions — add 'description' (what it does) + 'howto' (steps
   to get the key) + 'docs_url' (link to signup page).
   ══════════════════════════════════════════════════════════════════════════ */
$services = [

    // ── Voice & Video Calls ───────────────────────────────────────────────
    [
        'id'          => 'dailyco',
        'label'       => 'Daily.co',
        'subtitle'    => 'Voice & Video Calls',
        'group'       => 'Calls',
        'icon'        => 'heroicon-o-video-camera',
        'method'      => 'testDailyCo',
        'env_keys'    => ['DAILY_CO_API_KEY', 'DAILY_CO_DOMAIN'],
        'description' => '⚠️ REQUIRED: Powers real-time voice and video calls between matched users. Free tier includes 10,000 participant-minutes/month. Voice/video calling will NOT work without this key.',
        'howto'       => "1. Sign up free at https://dashboard.daily.co\n2. Go to Dashboard → Developers → API Keys\n3. Copy your API key → set DAILY_CO_API_KEY=your_key\n4. Your subdomain (e.g. 'heartsconnect') → set DAILY_CO_DOMAIN=heartsconnect\n5. Run: php artisan config:clear",
        'docs_url'    => 'https://dashboard.daily.co',
    ],
    [
        'id'          => 'agora',
        'label'       => 'Agora (Deprecated)',
        'subtitle'    => 'Voice & Video (Not Used)',
        'group'       => 'Calls',
        'icon'        => 'heroicon-o-phone',
        'method'      => 'testAgora',
        'env_keys'    => ['AGORA_APP_ID', 'AGORA_APP_CERTIFICATE'],
        'description' => '❌ DEPRECATED: App now uses Daily.co exclusively. Agora is no longer used. You can safely ignore this.',
        'howto'       => "Agora has been replaced by Daily.co. No action needed.",
        'docs_url'    => 'https://console.agora.io',
    ],

    // ── AI ────────────────────────────────────────────────────────────────
    [
        'id'          => 'groq',
        'label'       => 'Groq AI',
        'subtitle'    => 'Smart Match AI (Primary)',
        'group'       => 'AI',
        'icon'        => 'heroicon-o-cpu-chip',
        'method'      => 'testGroq',
        'env_keys'    => ['GROQ_API_KEY'],
        'description' => 'Powers AI-generated icebreakers, smart bio suggestions, match compatibility insights, and conversation starters. Groq runs Llama 3.1 at extremely fast speeds. Free tier is generous — recommended for production.',
        'howto'       => "1. Sign up at console.groq.com\n2. Go to API Keys → Create API Key\n3. Copy the key → set GROQ_API_KEY=...\n4. Free tier: 14,400 requests/day on llama-3.1-8b-instant",
        'docs_url'    => 'https://console.groq.com/keys',
    ],
    [
        'id'          => 'openai',
        'label'       => 'OpenAI',
        'subtitle'    => 'AI Fallback (GPT-4)',
        'group'       => 'AI',
        'icon'        => 'heroicon-o-sparkles',
        'method'      => 'testOpenAI',
        'env_keys'    => ['OPENAI_API_KEY'],
        'description' => 'Optional fallback AI provider using GPT-4o/GPT-3.5. The app uses Groq by default. Add this key to enable OpenAI as a premium-quality alternative for complex AI features.',
        'howto'       => "1. Sign up at platform.openai.com\n2. Go to API Keys → Create new secret key\n3. Copy the key → set OPENAI_API_KEY=...\n4. Add billing at platform.openai.com/billing\n5. Note: GPT-4 requires a paid plan",
        'docs_url'    => 'https://platform.openai.com/api-keys',
    ],

    // ── Auth & Social ─────────────────────────────────────────────────────
    [
        'id'          => 'google',
        'label'       => 'Google OAuth',
        'subtitle'    => 'Social Login',
        'group'       => 'Auth',
        'icon'        => 'heroicon-o-user-circle',
        'method'      => 'testGoogle',
        'env_keys'    => ['GOOGLE_CLIENT_ID', 'GOOGLE_CLIENT_SECRET', 'GOOGLE_REDIRECT_URI'],
        'description' => 'Enables "Sign in with Google" one-tap login. Users can register and sign in without a password. Reduces sign-up friction significantly — highly recommended.',
        'howto'       => "1. Go to console.cloud.google.com\n2. Create a project → Enable 'Google Identity' API\n3. Credentials → Create OAuth 2.0 Client ID → Web Application\n4. Add Authorized redirect URI: https://yourdomain.com/auth/google/callback\n5. Copy Client ID → GOOGLE_CLIENT_ID=...\n6. Copy Client Secret → GOOGLE_CLIENT_SECRET=...\n7. Set GOOGLE_REDIRECT_URI=https://yourdomain.com/auth/google/callback",
        'docs_url'    => 'https://console.cloud.google.com/apis/credentials',
    ],

    // ── Payments ──────────────────────────────────────────────────────────
    [
        'id'          => 'stripe',
        'label'       => 'Stripe',
        'subtitle'    => 'Premium Subscriptions',
        'group'       => 'Payments',
        'icon'        => 'heroicon-o-credit-card',
        'method'      => 'testStripe',
        'env_keys'    => ['STRIPE_KEY', 'STRIPE_SECRET', 'STRIPE_WEBHOOK_SECRET'],
        'description' => 'Handles premium membership payments, recurring subscriptions, and one-time credit purchases. Test mode (sk_test_...) is 100% free for development. Never go live without setting STRIPE_WEBHOOK_SECRET.',
        'howto'       => "1. Sign up at stripe.com\n2. Dashboard → Developers → API Keys\n3. Copy Publishable key → STRIPE_KEY=pk_test_...\n4. Copy Secret key → STRIPE_SECRET=sk_test_...\n5. Webhooks → Add endpoint → your_domain/stripe/webhook\n6. Copy Webhook signing secret → STRIPE_WEBHOOK_SECRET=whsec_...\n7. Switch to live keys when ready for production",
        'docs_url'    => 'https://dashboard.stripe.com/apikeys',
    ],

    // ── Communications ────────────────────────────────────────────────────
    [
        'id'          => 'twilio',
        'label'       => 'Twilio',
        'subtitle'    => 'SMS Phone Verification',
        'group'       => 'Communications',
        'icon'        => 'heroicon-o-device-phone-mobile',
        'method'      => 'testTwilio',
        'env_keys'    => ['TWILIO_ACCOUNT_SID', 'TWILIO_AUTH_TOKEN', 'TWILIO_FROM'],
        'description' => 'Sends SMS one-time passcodes for phone number verification — crucial for safety on dating apps. Also enables SMS alerts for important account activity. Trial accounts get a free number and ~$15 credit.',
        'howto'       => "1. Sign up at twilio.com\n2. Console Dashboard shows Account SID and Auth Token\n3. Copy Account SID → TWILIO_ACCOUNT_SID=AC...\n4. Copy Auth Token → TWILIO_AUTH_TOKEN=...\n5. Phone Numbers → Buy a Number (or use free trial number)\n6. Copy number (e.g. +15551234567) → TWILIO_FROM=+15551234567",
        'docs_url'    => 'https://console.twilio.com',
    ],
    [
        'id'          => 'telegram',
        'label'       => 'Telegram Bot',
        'subtitle'    => 'Admin Alerts',
        'group'       => 'Communications',
        'icon'        => 'heroicon-o-paper-airplane',
        'method'      => 'testTelegram',
        'env_keys'    => ['TELEGRAM_BOT_TOKEN', 'TELEGRAM_CHAT_ID'],
        'description' => 'Sends real-time admin notifications to a Telegram chat — new registrations, reports, payment failures, server errors, etc. Free and instant. Great alternative to email alerts.',
        'howto'       => "1. Open Telegram → Search @BotFather → /newbot\n2. Follow prompts → Copy the bot token → TELEGRAM_BOT_TOKEN=...\n3. Open your bot → send /start\n4. Visit: https://api.telegram.org/bot<TOKEN>/getUpdates\n5. Find 'chat.id' in the response → TELEGRAM_CHAT_ID=...\n6. For group chats: add bot to group, make it admin, use the group's chat ID (negative number)",
        'docs_url'    => 'https://t.me/BotFather',
    ],
    [
        'id'          => 'mail',
        'label'       => 'Mail / SMTP',
        'subtitle'    => 'Email Delivery',
        'group'       => 'Communications',
        'icon'        => 'heroicon-o-envelope',
        'method'      => 'testMailSmtp',
        'env_keys'    => ['MAIL_MAILER', 'MAIL_HOST', 'MAIL_PORT', 'MAIL_USERNAME', 'MAIL_PASSWORD'],
        'description' => 'Delivers transactional emails: email verification, password resets, match notifications, premium receipts. Supports SMTP, Mailgun, Postmark, Resend, SendGrid, and more via Laravel drivers.',
        'howto'       => "Option A — Brevo (free 300 emails/day):\n  1. Sign up at brevo.com → SMTP & API → SMTP\n  2. MAIL_MAILER=smtp, MAIL_HOST=smtp-relay.brevo.com, MAIL_PORT=587\n  3. MAIL_USERNAME=your_login, MAIL_PASSWORD=your_smtp_password\n\nOption B — Mailgun (free 100/day):\n  1. Sign up at mailgun.com → Sending → Domain Settings → SMTP\n  2. MAIL_MAILER=mailgun, MAILGUN_DOMAIN=..., MAILGUN_SECRET=...\n\nOption C — Gmail (dev only):\n  1. Enable 2FA → App Passwords → generate one\n  2. MAIL_HOST=smtp.gmail.com, MAIL_PORT=587, MAIL_ENCRYPTION=tls",
        'docs_url'    => 'https://www.brevo.com',
    ],

    // ── Storage ───────────────────────────────────────────────────────────
    [
        'id'          => 'cloudinary',
        'label'       => 'Cloudinary',
        'subtitle'    => 'Image CDN & Transformations',
        'group'       => 'Storage',
        'icon'        => 'heroicon-o-photo',
        'method'      => 'testCloudinary',
        'env_keys'    => ['CLOUDINARY_CLOUD_NAME', 'CLOUDINARY_API_KEY', 'CLOUDINARY_API_SECRET'],
        'description' => 'Stores and serves profile photos with on-the-fly transformations (resize, crop, face detection, blur NSFW content). Free tier: 25 GB storage, 25 GB bandwidth/month. Greatly improves image loading performance.',
        'howto'       => "1. Sign up at cloudinary.com (free)\n2. Dashboard shows Cloud Name, API Key, API Secret\n3. CLOUDINARY_CLOUD_NAME=your_cloud_name\n4. CLOUDINARY_API_KEY=123456789\n5. CLOUDINARY_API_SECRET=abc123...\n6. In your .env also set: FILESYSTEM_DISK=cloudinary\n7. Install SDK: composer require cloudinary-labs/cloudinary-laravel",
        'docs_url'    => 'https://cloudinary.com/console',
    ],
    [
        'id'          => 'awss3',
        'label'       => 'AWS S3',
        'subtitle'    => 'Object File Storage',
        'group'       => 'Storage',
        'icon'        => 'heroicon-o-server',
        'method'      => 'testAwsS3',
        'env_keys'    => ['AWS_ACCESS_KEY_ID', 'AWS_SECRET_ACCESS_KEY', 'AWS_BUCKET', 'AWS_DEFAULT_REGION'],
        'description' => 'Stores user-uploaded photos, videos, and documents in Amazon S3. Extremely reliable and scalable. Alternative to Cloudinary for raw file storage. First 12 months: 5 GB free. After that: ~$0.023/GB/month.',
        'howto'       => "1. Sign up at aws.amazon.com → Go to S3 → Create Bucket\n2. IAM → Users → Create User → Attach 'AmazonS3FullAccess'\n3. Security Credentials → Create Access Key\n4. AWS_ACCESS_KEY_ID=AKIA...\n5. AWS_SECRET_ACCESS_KEY=...\n6. AWS_BUCKET=your-bucket-name\n7. AWS_DEFAULT_REGION=us-east-1 (or your region)\n8. AWS_URL=https://your-bucket.s3.amazonaws.com (optional CDN URL)",
        'docs_url'    => 'https://s3.console.aws.amazon.com',
    ],

    // ── Push Notifications ────────────────────────────────────────────────
    [
        'id'          => 'firebase',
        'label'       => 'Firebase FCM',
        'subtitle'    => 'Push Notifications',
        'group'       => 'Push',
        'icon'        => 'heroicon-o-bell',
        'method'      => 'testFirebase',
        'env_keys'    => ['FIREBASE_API_KEY', 'FIREBASE_PROJECT_ID', 'FIREBASE_MESSAGING_SENDER_ID', 'FIREBASE_APP_ID'],
        'description' => 'Sends browser and mobile push notifications for new matches, messages, likes, and app alerts. Users receive real-time notifications even when not on your site. Free unlimited notifications. Service account already configured in storage/app/fire-base-dojo-9-38865f485255.json',
        'howto'       => "✅ API Key already set: AIzaSyAFBjyUOQ1DIhcTMyqo46fP27eWfsU38_I\n✅ Project ID: fire-base-dojo-9\n\nNext steps:\n1. Go to console.firebase.google.com/project/fire-base-dojo-9/settings/general\n2. Cloud Messaging tab → Copy Sender ID → FIREBASE_MESSAGING_SENDER_ID=...\n3. General tab → Your apps → Copy App ID → FIREBASE_APP_ID=...\n4. Cloud Messaging → Web Push certificates → Generate key pair → FIREBASE_VAPID_KEY=...\n5. Run migration: php artisan migrate\n6. Add routes in routes/web.php (see FIREBASE-QUICK-START.md)\n7. Update firebase-init.js and firebase-messaging-sw.js with sender ID & app ID\n\nSee FIREBASE-QUICK-START.md for complete setup guide.",
        'docs_url'    => 'https://console.firebase.google.com/project/fire-base-dojo-9/settings/general',
    ],

    // ── Security / VPN ────────────────────────────────────────────────────
    [
        'id'          => 'iphub',
        'label'       => 'IPHub',
        'subtitle'    => 'VPN Detection (Primary)',
        'group'       => 'Security',
        'icon'        => 'heroicon-o-shield-check',
        'method'      => 'testIpHub',
        'env_keys'    => ['IPHUB_API_KEY'],
        'description' => 'Detects VPN, proxy, and Tor connections during registration and login. Helps prevent fake accounts and location spoofing. Free tier: 1,000 requests/day. Block score 1 = hosting/VPN IP.',
        'howto'       => "1. Sign up at iphub.info\n2. Dashboard → API Key section\n3. Copy your key → IPHUB_API_KEY=...\n4. Free: 1,000 requests/day\n5. Paid plans start at \$9/month for 10,000 req/day\n6. Set VPN_ENABLE_IPHUB=true in .env to activate",
        'docs_url'    => 'https://iphub.info/api',
    ],
    [
        'id'          => 'proxycheck',
        'label'       => 'ProxyCheck',
        'subtitle'    => 'VPN Detection (Backup)',
        'group'       => 'Security',
        'icon'        => 'heroicon-o-shield-exclamation',
        'method'      => 'testProxyCheck',
        'env_keys'    => ['PROXYCHECK_API_KEY'],
        'description' => 'Secondary VPN/proxy detection alongside IPHub. Uses a different detection database for higher accuracy. Free tier: 1,000 queries/day. Supports risk scoring for more granular blocking.',
        'howto'       => "1. Sign up at proxycheck.io\n2. Dashboard → API Access → API Key\n3. Copy your key → PROXYCHECK_API_KEY=...\n4. Free: 1,000 queries/day\n5. Paid plans from \$10/month for more queries\n6. Set VPN_ENABLE_PROXYCHECK=true in .env",
        'docs_url'    => 'https://proxycheck.io/dashboard',
    ],

    // ── Infrastructure ────────────────────────────────────────────────────
    [
        'id'          => 'pusher',
        'label'       => 'Pusher',
        'subtitle'    => 'WebSocket (Alternative)',
        'group'       => 'Infrastructure',
        'icon'        => 'heroicon-o-arrow-path',
        'method'      => 'testPusher',
        'env_keys'    => ['PUSHER_APP_ID', 'PUSHER_APP_KEY', 'PUSHER_APP_SECRET', 'PUSHER_APP_CLUSTER'],
        'description' => 'A managed WebSocket service, alternative to self-hosted Reverb. Good choice if your server cannot run a persistent WebSocket process. Free tier: 100 concurrent connections, 200K messages/day.',
        'howto'       => "1. Sign up at pusher.com → Channels → Create App\n2. App Keys tab shows all 4 values\n3. PUSHER_APP_ID=123456\n4. PUSHER_APP_KEY=abc123...\n5. PUSHER_APP_SECRET=xyz789...\n6. PUSHER_APP_CLUSTER=mt1 (or your nearest region)\n7. In .env: BROADCAST_CONNECTION=pusher\n8. In config/broadcasting.php use the 'pusher' driver",
        'docs_url'    => 'https://dashboard.pusher.com',
    ],
    [
        'id'          => 'reverb',
        'label'       => 'Reverb',
        'subtitle'    => 'WebSocket Server (Built-in)',
        'group'       => 'Infrastructure',
        'icon'        => 'heroicon-o-signal',
        'method'      => 'testReverb',
        'env_keys'    => ['REVERB_PORT', 'REVERB_APP_KEY', 'REVERB_APP_ID'],
        'description' => 'Laravel\'s own WebSocket server — powers real-time messaging, typing indicators, online status, and live notifications. Runs as a background process on your server. 100% free, no third-party dependency.',
        'howto'       => "1. Already installed — run: php artisan reverb:start\n2. Or use Admin → Artisan Runner → Start Reverb Server\n3. .env values are auto-generated during laravel/reverb install\n4. REVERB_APP_ID, REVERB_APP_KEY, REVERB_APP_SECRET auto-set\n5. REVERB_HOST=0.0.0.0, REVERB_PORT=8080 (change port if needed)\n6. For production: run reverb as a supervisor daemon\n7. Nginx proxy_pass config needed for production SSL",
        'docs_url'    => 'https://laravel.com/docs/reverb',
    ],
    [
        'id'          => 'database',
        'label'       => 'Database',
        'subtitle'    => 'MySQL / MariaDB',
        'group'       => 'Infrastructure',
        'icon'        => 'heroicon-o-circle-stack',
        'method'      => 'testDatabase',
        'env_keys'    => ['DB_HOST', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'],
        'description' => 'Primary data store for all user profiles, matches, messages, and transactions. Verifies the PDO connection is live and counts registered users.',
        'howto'       => "1. DB_CONNECTION=mysql\n2. DB_HOST=127.0.0.1 (or your DB server IP)\n3. DB_PORT=3306\n4. DB_DATABASE=your_database_name\n5. DB_USERNAME=your_db_user\n6. DB_PASSWORD=your_db_password\n7. Run: php artisan migrate to set up tables\n8. cPanel: use MySQL Databases to create DB + user",
        'docs_url'    => 'https://laravel.com/docs/database',
    ],
];

$results   = $this->results;
$hasResult = count($results) > 0;
$pass      = collect($results)->where('status','pass')->count();
$fail      = collect($results)->where('status','fail')->count();
$warn      = collect($results)->where('status','warn')->count();
$total     = count($results);
$allPassed = $hasResult && $fail === 0 && $warn === 0;
$allFailed = $hasResult && $pass === 0 && $warn === 0;
@endphp

<style>
.akt {
    --bg0:#fff; --bg1:#f8fafc; --bg2:#f1f5f9;
    --border:#e2e8f0; --border2:#cbd5e1;
    --txt:#0f172a; --txt2:#475569; --txt3:#94a3b8;
    --green:#16a34a; --gbg:#f0fdf4; --gbrd:#bbf7d0;
    --red:#dc2626;   --rbg:#fef2f2; --rbrd:#fecaca;
    --amber:#d97706; --abg:#fffbeb; --abrd:#fde68a;
    --blue:#2563eb;
    --sh:0 1px 3px rgba(0,0,0,.07);
    --shm:0 4px 16px rgba(0,0,0,.10);
}
.dark .akt {
    --bg0:#1e293b; --bg1:#0f172a; --bg2:#1e293b;
    --border:#334155; --border2:#475569;
    --txt:#f1f5f9; --txt2:#94a3b8; --txt3:#64748b;
    --green:#4ade80;  --gbg:rgba(22,163,74,.15);  --gbrd:rgba(74,222,128,.25);
    --red:#f87171;    --rbg:rgba(220,38,38,.15);   --rbrd:rgba(248,113,113,.25);
    --amber:#fbbf24;  --abg:rgba(217,119,6,.15);   --abrd:rgba(251,191,36,.25);
    --blue:#60a5fa;
}
.akt * { box-sizing:border-box; }

.akt-topbar {
    display:flex; align-items:center; justify-content:space-between;
    gap:1rem; flex-wrap:wrap; margin-bottom:1.5rem;
}
.akt-desc { font-size:.875rem; color:var(--txt2); max-width:520px; line-height:1.5; }

.akt-run-btn {
    display:inline-flex; align-items:center; gap:.5rem;
    padding:.6rem 1.25rem; border-radius:10px; font-size:.875rem;
    font-weight:700; border:none; cursor:pointer; transition:all .15s;
    background:var(--blue); color:#fff; box-shadow:0 2px 8px rgba(37,99,235,.35);
    white-space:nowrap;
}
.akt-run-btn:hover    { opacity:.88; transform:translateY(-1px); }
.akt-run-btn:disabled { opacity:.55; cursor:not-allowed; transform:none; }
.akt-run-btn svg { width:15px; height:15px; }

.akt-summary {
    display:flex; align-items:center; gap:1.5rem; flex-wrap:wrap;
    padding:.875rem 1.25rem; border-radius:12px; border:1.5px solid;
    font-size:.85rem; font-weight:600; margin-bottom:1.5rem;
}
.akt-summary.ok    { background:var(--gbg); border-color:var(--gbrd); color:var(--green); }
.akt-summary.bad   { background:var(--rbg); border-color:var(--rbrd); color:var(--red);   }
.akt-summary.mixed { background:var(--abg); border-color:var(--abrd); color:var(--amber); }
.akt-sum-chip   { display:inline-flex; align-items:center; gap:.3rem; }
.akt-sum-chip svg { width:14px; height:14px; }
.akt-sum-lbl { color:var(--txt3); font-weight:500; margin-right:.25rem; }

.akt-grid {
    display:grid; grid-template-columns:repeat(3,1fr); gap:1rem;
}
@media(max-width:1200px){ .akt-grid { grid-template-columns:repeat(2,1fr); } }
@media(max-width:640px)  { .akt-grid { grid-template-columns:1fr; } }

/* ── Group section headers (span full row) ──────────────────────────── */
.akt-group-header {
    grid-column:1/-1;
    display:flex; align-items:center; gap:.75rem;
    margin-top:1.25rem; margin-bottom:.1rem;
    font-size:.68rem; font-weight:800; letter-spacing:.12em;
    text-transform:uppercase; color:var(--txt3);
}
.akt-group-header::after {
    content:''; flex:1; height:1px; background:var(--border);
}

/* ── Description text ───────────────────────────────────────────────── */
.akt-description {
    font-size:.73rem; color:var(--txt2); line-height:1.5;
    border-left:2px solid var(--border2); padding-left:.6rem;
    margin:0;
}

/* ── How-to expandable ──────────────────────────────────────────────── */
.akt-howto {
    border:1px solid var(--border); border-radius:8px; overflow:hidden;
}
.akt-howto summary {
    padding:.4rem .65rem; cursor:pointer; font-size:.7rem;
    font-weight:700; color:var(--txt2); list-style:none;
    display:flex; align-items:center; gap:.4rem;
    background:var(--bg1); user-select:none;
}
.akt-howto summary:hover { background:var(--bg2); color:var(--txt); }
.akt-howto summary::before { content:'▶'; font-size:.5rem; transition:transform .2s; }
details[open].akt-howto summary::before { transform:rotate(90deg); }
.akt-howto pre {
    margin:0; padding:.6rem .65rem;
    font-size:.67rem; line-height:1.65;
    color:var(--txt2); white-space:pre-wrap; word-break:break-word;
    background:var(--bg0); font-family:ui-monospace,monospace;
    border-top:1px solid var(--border);
}

/* ── Docs link ──────────────────────────────────────────────────────── */
.akt-docs-link {
    display:inline-flex; align-items:center; gap:.3rem;
    font-size:.68rem; font-weight:600; color:var(--blue);
    text-decoration:none;
    padding:.28rem .6rem; border-radius:6px;
    border:1px solid rgba(37,99,235,.25); background:rgba(37,99,235,.06);
    transition:all .15s; width:fit-content;
}
.akt-docs-link:hover { background:rgba(37,99,235,.14); border-color:var(--blue); }
.akt-docs-link svg { width:10px; height:10px; flex-shrink:0; }

.akt-card {
    display:flex; flex-direction:column;
    background:var(--bg0); border:1.5px solid var(--border);
    border-radius:14px; overflow:hidden;
    box-shadow:var(--sh); transition:box-shadow .2s, border-color .2s;
}
.akt-card:hover { box-shadow:var(--shm); }
.akt-card.pass  { border-color:var(--gbrd); background:var(--gbg); }
.akt-card.fail  { border-color:var(--rbrd); background:var(--rbg); }
.akt-card.warn  { border-color:var(--abrd); background:var(--abg); }

.akt-stripe { height:3px; background:var(--border2); }
.akt-card.pass .akt-stripe { background:var(--green); }
.akt-card.fail .akt-stripe { background:var(--red);   }
.akt-card.warn .akt-stripe { background:var(--amber); }

.akt-body { padding:1.1rem 1.1rem .85rem; display:flex; flex-direction:column; flex:1; gap:.8rem; }

.akt-head { display:flex; align-items:flex-start; justify-content:space-between; gap:.5rem; }
.akt-meta { display:flex; align-items:center; gap:.65rem; }
.akt-icon {
    width:36px; height:36px; border-radius:9px; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    background:var(--bg2); border:1px solid var(--border);
}
.akt-card.pass .akt-icon { background:rgba(22,163,74,.12); border-color:var(--gbrd); }
.akt-card.fail .akt-icon { background:rgba(220,38,38,.10); border-color:var(--rbrd); }
.akt-card.warn .akt-icon { background:rgba(217,119,6,.10); border-color:var(--abrd); }
.akt-icon svg { width:16px; height:16px; color:var(--txt3); }
.akt-card.pass .akt-icon svg { color:var(--green); }
.akt-card.fail .akt-icon svg { color:var(--red);   }
.akt-card.warn .akt-icon svg { color:var(--amber); }
.akt-name { font-size:.9rem; font-weight:700; color:var(--txt); line-height:1.2; }
.akt-sub  { font-size:.72rem; color:var(--txt3); margin-top:1px; }

.akt-badge {
    display:inline-flex; align-items:center; gap:.3rem;
    padding:3px 9px; border-radius:20px; font-size:.67rem;
    font-weight:700; border:1px solid; white-space:nowrap; flex-shrink:0;
}
.akt-badge svg { width:9px; height:9px; }
.akt-badge.pass { background:var(--gbg); border-color:var(--gbrd); color:var(--green); }
.akt-badge.fail { background:var(--rbg); border-color:var(--rbrd); color:var(--red);   }
.akt-badge.warn { background:var(--abg); border-color:var(--abrd); color:var(--amber); }
.akt-badge.idle { background:var(--bg2); border-color:var(--border2); color:var(--txt3); }

.akt-envs { display:flex; flex-wrap:wrap; gap:4px; }
.akt-env  {
    font-family:ui-monospace,monospace; font-size:.67rem;
    background:var(--bg1); border:1px solid var(--border2);
    color:var(--txt3); padding:2px 7px; border-radius:5px;
}
.akt-card.pass .akt-env,
.akt-card.fail .akt-env,
.akt-card.warn .akt-env { background:rgba(0,0,0,.05); }

.akt-result { display:flex; flex-direction:column; gap:4px; }
.akt-result-msg { font-size:.8rem; font-weight:600; color:var(--txt); }
.akt-result-det { font-size:.73rem; color:var(--txt2); word-break:break-all; line-height:1.4; }
.akt-result-ms  {
    display:inline-flex; align-items:center; gap:3px;
    font-size:.67rem; color:var(--txt3); font-family:ui-monospace,monospace;
}
.akt-result-ms svg { width:10px; height:10px; }

.akt-btn {
    margin-top:auto;
    display:flex; align-items:center; justify-content:center; gap:.4rem;
    width:100%; padding:.5rem; border-radius:8px;
    font-size:.78rem; font-weight:600;
    border:1px solid var(--border2); background:var(--bg1); color:var(--txt2);
    cursor:pointer; transition:all .15s;
}
.akt-btn:hover    { background:var(--bg2); color:var(--txt); border-color:var(--blue); }
.akt-btn:disabled { opacity:.5; cursor:not-allowed; }
.akt-btn svg { width:13px; height:13px; }

@keyframes akt-spin { to{ transform:rotate(360deg); } }
.akt-spin { animation:akt-spin .7s linear infinite; }
</style>

<div class="akt">

    {{-- Top bar --}}
    <div class="akt-topbar">
        <p class="akt-desc">
            Test every external service key in real-time. Each click makes a live HTTP request — results are never cached.
        </p>
        <button wire:click="testAll" wire:loading.attr="disabled" class="akt-run-btn">
            <span wire:loading.remove wire:target="testAll" style="display:inline-flex;align-items:center;gap:.4rem">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M4.5 5.653c0-1.427 1.529-2.33 2.779-1.643l11.54 6.347c1.295.712 1.295 2.573 0 3.286L7.28 19.99c-1.25.687-2.779-.217-2.779-1.643V5.653Z" clip-rule="evenodd"/></svg>
                Run All Tests
            </span>
            <span wire:loading wire:target="testAll" style="display:inline-flex;align-items:center;gap:.4rem">
                <svg class="akt-spin" fill="none" viewBox="0 0 24 24"><circle style="opacity:.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path style="opacity:.75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                Running…
            </span>
        </button>
    </div>

    {{-- Summary banner --}}
    @if($hasResult)
    <div class="akt-summary {{ $allPassed ? 'ok' : ($allFailed ? 'bad' : 'mixed') }}">
        <span class="akt-sum-lbl">{{ $total }} services tested</span>
        @if($pass)
        <span class="akt-sum-chip">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd"/></svg>
            {{ $pass }} passed
        </span>
        @endif
        @if($warn)
        <span class="akt-sum-chip" style="color:var(--amber)">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd"/></svg>
            {{ $warn }} warnings
        </span>
        @endif
        @if($fail)
        <span class="akt-sum-chip" style="color:var(--red)">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-1.72 6.97a.75.75 0 1 0-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 1 0 1.06 1.06L12 13.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L13.06 12l1.72-1.72a.75.75 0 1 0-1.06-1.06L12 10.94l-1.72-1.72Z" clip-rule="evenodd"/></svg>
            {{ $fail }} failed
        </span>
        @endif
    </div>
    @endif

    {{-- Service grid --}}
    <div class="akt-grid">
        @php $lastGroup = null; @endphp
        @foreach($services as $svc)
        @php
            $result = $results[$svc['id']] ?? null;
            $status = $result['status'] ?? 'idle';
            $group  = $svc['group'] ?? '';
        @endphp

        {{-- Group section header --}}
        @if($group && $group !== $lastGroup)
            @php $lastGroup = $group; @endphp
            <div class="akt-group-header">{{ $group }}</div>
        @endif

        <div class="akt-card {{ $status }}">
            <div class="akt-stripe"></div>
            <div class="akt-body">

                {{-- Header: icon + name + badge --}}
                <div class="akt-head">
                    <div class="akt-meta">
                        <div class="akt-icon">
                            <x-dynamic-component :component="$svc['icon']" />
                        </div>
                        <div>
                            <div class="akt-name">{{ $svc['label'] }}</div>
                            <div class="akt-sub">{{ $svc['subtitle'] }}</div>
                        </div>
                    </div>
                    <span class="akt-badge {{ $status }}">
                        @if($status === 'pass')
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/></svg>
                            PASS
                        @elseif($status === 'fail')
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"/></svg>
                            FAIL
                        @elseif($status === 'warn')
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495ZM10 5a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 5Zm0 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/></svg>
                            WARN
                        @else —
                        @endif
                    </span>
                </div>

                {{-- .env key chips --}}
                <div class="akt-envs">
                    @foreach($svc['env_keys'] as $k)
                        <span class="akt-env">{{ $k }}</span>
                    @endforeach
                </div>

                {{-- Description --}}
                @if(!empty($svc['description']))
                <p class="akt-description">{{ $svc['description'] }}</p>
                @endif

                {{-- Test result --}}
                @if($result)
                <div class="akt-result">
                    <div class="akt-result-msg">{{ $result['message'] }}</div>
                    @if(!empty($result['detail']))
                        <div class="akt-result-det">{{ $result['detail'] }}</div>
                    @endif
                    @if(!is_null($result['ms']))
                        <div class="akt-result-ms">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm.75-13a.75.75 0 0 0-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 0 0 0-1.5h-3.25V5Z" clip-rule="evenodd"/></svg>
                            {{ $result['ms'] }} ms
                        </div>
                    @endif
                </div>
                @endif

                {{-- How-to guide (collapsible) --}}
                @if(!empty($svc['howto']))
                <details class="akt-howto">
                    <summary>How to get this key</summary>
                    <pre>{{ $svc['howto'] }}</pre>
                </details>
                @endif

                {{-- Test button --}}
                <button
                    wire:click="{{ $svc['method'] }}"
                    wire:loading.attr="disabled"
                    wire:target="{{ $svc['method'] }},testAll"
                    class="akt-btn"
                >
                    <span wire:loading.remove wire:target="{{ $svc['method'] }}" style="display:inline-flex;align-items:center;gap:.35rem">
                        <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z"/></svg>
                        {{ $result ? 'Re-test' : 'Test' }}
                    </span>
                    <span wire:loading wire:target="{{ $svc['method'] }}" style="display:inline-flex;align-items:center;gap:.35rem">
                        <svg class="akt-spin" fill="none" viewBox="0 0 24 24"><circle style="opacity:.3" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path style="opacity:.75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                        Testing…
                    </span>
                </button>

                {{-- Dashboard / docs link --}}
                @if(!empty($svc['docs_url']))
                <a href="{{ $svc['docs_url'] }}" target="_blank" rel="noopener noreferrer" class="akt-docs-link">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.25 5.5a.75.75 0 0 0-.75.75v8.5c0 .414.336.75.75.75h8.5a.75.75 0 0 0 .75-.75v-4a.75.75 0 0 1 1.5 0v4A2.25 2.25 0 0 1 12.75 17h-8.5A2.25 2.25 0 0 1 2 14.75v-8.5A2.25 2.25 0 0 1 4.25 4h5a.75.75 0 0 1 0 1.5h-5Z" clip-rule="evenodd"/><path fill-rule="evenodd" d="M6.194 12.753a.75.75 0 0 0 1.06.053L16.5 4.44v2.81a.75.75 0 0 0 1.5 0v-4.5a.75.75 0 0 0-.75-.75h-4.5a.75.75 0 0 0 0 1.5h2.553l-9.056 8.194a.75.75 0 0 0-.053 1.06Z" clip-rule="evenodd"/></svg>
                    Open Dashboard
                </a>
                @endif

            </div>
        </div>
        @endforeach
    </div>

</div>

</x-filament-panels::page>
