## Plan: Full-Featured Dating Site (Laravel 12 + Bootstrap 5)

**TL;DR** — Build a full dating site from scratch in the empty `c:\xampp\htdocs\dating` workspace using Laravel 12, Blade + Alpine.js, Bootstrap 5.3, and Laravel Reverb for real-time chat. Discovery uses both a swipe-deck mode and a browse-grid mode. Premium upgrades are manual — crypto wallet addresses are managed via the admin, users pay and submit a proof, admins approve and flip the `is_premium` flag. Admin panel powered by **Filament 3** (most advanced Laravel admin option). No Stripe.

---

### Phase 1 — Project Scaffolding

1. Run `composer create-project laravel/laravel . "^12.0"` inside `c:\xampp\htdocs\dating`
2. Configure `.env` — MySQL DB, APP_URL, queue driver (`database` for dev), broadcasting driver (`reverb`)
3. Install **Laravel Breeze** (Blade + Alpine.js variant) for auth scaffolding (login, register, email verify, password reset)
4. Run `php artisan install:broadcasting` to scaffold Reverb + `channels.php`
5. Install NPM deps: `bootstrap`, `@popperjs/core`, `bootstrap-icons`, `alpinejs`
6. Configure Vite to compile Bootstrap 5 via `resources/css/app.scss` + `resources/js/app.js`

---

### Phase 2 — Database & Models

**13 migration files** created in order:

1. Extend `users` table: add `gender`, `seeking`, `date_of_birth`, `is_premium`, `is_banned`, `banned_reason`, `last_active_at`, `profile_complete`
2. `profiles` — headline, bio, height, body type, ethnicity, religion, education, occupation, relationship goal, smoking, drinking, children fields, latitude, longitude (DECIMAL 10,7), city, country, views_count
3. `photos` — user_id, path, thumbnail_path, is_primary, is_approved, sort_order
4. `interests` + `profile_interest` pivot
5. `likes` — sender_id, receiver_id, is_super_like | unique constraint (sender, receiver)
6. `matches` — user1_id, user2_id, matched_at | unique constraint
7. `conversations` + `messages` — conversation_id, sender_id, body, read_at
8. `blocks` + `reports`
9. `profile_views` — viewer_id, viewed_id, viewed_at
10. `user_preferences` — min_age, max_age, max_distance_km, seeking_gender, body_types (JSON), online_only
11. `compatibility_questions` + `user_answers`
12. `premium_payments` — user_id, crypto_currency, amount, wallet_address, tx_hash (proof), status (pending/approved/rejected), notes, approved_by, approved_at
13. `crypto_wallets` — currency (BTC/ETH/USDT etc.), address, is_active (managed by admin)

---

### Phase 3 — Auth & Onboarding

- Breeze handles login/register/email verification/password reset
- After registration, `EnsureProfileComplete` middleware redirects to a **multi-step onboarding wizard** (5 steps: basic info → about you → photos → preferences → interests)
- `ProfileSetupController` — progress tracked in session; can resume if interrupted
- Social login via **Laravel Socialite** (Google + Facebook) — optional but wired up
- `UpdateLastActive` middleware — updates `last_active_at` via cache throttle (once per 5 min) on every authenticated request

---

### Phase 4 — Profile System

- `ProfileController` — view own profile, edit, upload photos (up to 6)
- `PhotoController` — handles uploads through `intervention/image-laravel`; generates two sizes: `400×400` card thumbnail + `800×800` full view; saves both paths; marks photo `is_approved = false` (admin queue)
- `storage:link` sets up public disk; all photos served from `storage/`
- Profile completeness percentage shown on dashboard
- "Who viewed me" feed from `profile_views`
- Compatibility score calculated on-the-fly (weighted shared interests + matching relationship goals + preference alignment) — cached per user pair for 24h

---

### Phase 5 — Discovery & Matching

**Browse Grid (POF style)**
- `DiscoverController@index` — filtered, paginated grid of profiles
- Haversine distance query in a `NearbyScope` on the `Profile` model using raw MySQL
- Filters: age range, distance, gender, body type, relationship goal, online status, "new members"
- User preferences loaded from `user_preferences` and pre-populate filter form
- Exclude: own profile, blocked users, already-matched users (optionally show them greyed)
- Show compatibility % badge on each card

**Swipe Deck (Tinder style)**
- `SwipeController@deck` — returns 10 profiles as an Alpine.js-driven card stack
- Cards rendered server-side in a Blade partial; Alpine.js drives swipe animation (CSS transforms) with drag support via Pointer Events API (no heavy library needed)
- AJAX `POST /like` and `POST /pass` endpoints consume cards from the deck
- When mutual like detected → `MatchCreated` event fired → notification sent to both users → real-time toast via Reverb

---

### Phase 6 — Likes, Matches & Notifications

- `LikeController@store` — creates `likes` record; checks for reverse like → creates `matches` row + dispatches `MatchCreated` event
- "Who Liked Me" page: free users see blurred thumbnails (count only); premium users see full grid
- `MatchController@index` — lists all matches with last message preview
- Laravel **Notifications** system used for all alerts:
  - `NewMatchNotification` (DB + mail + broadcast)
  - `NewMessageNotification` (DB + broadcast)
  - `ProfileLikedNotification` (DB — premium only visible)
  - `ProfileViewedNotification` (DB)
- In-app notification bell updates in real-time via `Echo.private('user.{id}').notification(...)` pipe

---

### Phase 7 — Real-Time Messaging

- `MessageController` + `ConversationController`
- Only matched users can initiate conversations
- Inbox page: conversation list sorted by latest message, unread count badges
- Chat view: full conversation thread, auto-scroll, read receipt (`read_at`) updated on view
- `MessageSent` event broadcast on `private-conversation.{id}` channel → Echo listener appends message to DOM in real-time
- Typing indicator: `TypingEvent` broadcast with 2s debounce from Alpine.js keypress handler
- Laravel Reverb runs as a local WebSocket server (`php artisan reverb:start`)

---

### Phase 8 — Premium & Crypto Payments

- `CryptoWallet` model seeded with admin-configured wallets (BTC, ETH, USDT, etc.)
- `PremiumController@show` — displays payment page with active wallet addresses and plan pricing
- User selects currency, shown the wallet address + QR code, enters TX hash as proof → creates `premium_payments` record with status `pending`
- Admin reviews in Filament → approves → `User::setPremium()` flips `is_premium = true` + sets `premium_expires_at` (30/90/365 day plan)
- Premium gates enforced via a `Premium` middleware and `@premium` Blade directive
- Gated features: unblurred "who liked me", unlimited daily likes, profile boosts, advanced filters, read receipts

---

### Phase 9 — Filament 3 Admin Panel

Install `filament/filament ^3.0`. Admin panel at `/admin` (separate guard). Resources:

- **UserResource** — list, edit, ban/unban, force-verify email, manually set premium
- **PhotoModerationResource** — pending photos queue with approve/reject actions + image preview
- **ReportResource** — reported users/content queue with action workflow
- **PremiumPaymentResource** — pending payment proofs, approve/reject with notes, TX hash visible
- **CryptoWalletResource** — CRUD for wallet addresses (BTC address, ETH address, etc.) + toggle active
- **ConversationResource** — view flagged/reported conversations
- **Dashboard** — stat widgets: total users, active today, new signups this week, pending photo approvals, pending payments, open reports
- **SiteSettingsPage** — single-page Filament Settings page for site name, tagline, maintenance mode, daily like limit (free), premium plan prices

---

### Phase 10 — Safety & Moderation

- `BlockController` — block user (mutual invisibility across discover, messaging)
- `ReportController` — report profile / photo / message with reason dropdown + description
- Blocked users filtered from all queries via a global `ExcludeBlockedScope`
- GDPR: `AccountController@export` (JSON of all user data), `AccountController@destroy` (full account deletion, queued cleanup job)
- Rate limiting on `POST /messages` (30/min) and `POST /like` (100/hour) via `RateLimiter`

---

### Phase 11 — Polish & UI Design

- **Master layout** `layouts/app.blade.php` — Bootstrap 5 navbar (with notification bell, profile dropdown, match count badge), responsive sidebar on desktop, bottom nav on mobile
- **Color scheme** — modern gradient hero (deep plum → rose), white card surfaces, Bootstrap 5 utility classes throughout
- **Custom SCSS** variables override Bootstrap tokens for brand colors
- **Dark mode** toggle stored in `localStorage`, applies a `data-bs-theme="dark"` attribute
- Swipe deck fully touch-friendly (CSS transform + pointer events)
- Responsive throughout — mobile-first grid layouts
- Font: **Inter** (Google Fonts) — clean, modern sans-serif
- Micro-animations: card swipe fling, match celebration modal (confetti via `canvas-confetti`), notification slide-in toast

---

### Phase 12 — Queues & Background Jobs

Jobs dispatched to the `database` queue (dev) or `redis` (prod):
- `ProcessProfilePhoto` — resizes uploaded photo to two sizes after upload
- `SendMatchEmail` — email digest for new matches
- `UpdateCompatibilityScore` — recalculated when profile answers change
- `CleanProfileViews` — prune `profile_views` older than 30 days (scheduled daily)
- `ExpirePremiumAccounts` — check `premium_expires_at` daily, revoke if expired

Scheduled in `routes/console.php` via `Schedule`.

---

### Verification

- Run `php artisan migrate --seed` to confirm all 13 migration files execute cleanly
- Seed factory data: 50 fake users with varied profiles, photos (Unsplash placeholders), likes, matches, messages → visible in browse grid and swipe deck
- Manually test: register → onboard → browse → swipe → match → chat flow end to end
- Test Reverb: open two browser tabs as different users → send message → confirm real-time delivery
- Test admin: log in as seeded admin → approve a photo → approve a premium payment → verify user becomes premium
- Test blocking: block a user → confirm they disappear from discover + cannot message
- Run `php artisan test` — Breeze ships with auth feature tests; add Feature tests for `LikeController`, match creation, message sending

---

### Key Decisions Summary

| Concern | Decision |
|---|---|
| Laravel version | **12.x** |
| PHP version | **8.3+** |
| Auth | **Breeze + Blade + Alpine.js** |
| Real-time | **Laravel Reverb** (self-hosted WebSockets) |
| Images | **Intervention Image v3** + local storage (S3-ready) |
| Admin | **Filament 3** |
| Search | **Laravel Scout + Meilisearch** |
| Billing | **Manual crypto payments** — admin approval workflow |
| Geo | **Haversine formula** in `NearbyScope` |
| CSS | **Bootstrap 5.3.x** via Vite |
| Queue | **database** driver (dev), Redis (prod) |
| Roles | **Spatie Laravel Permission** |
| Discovery | **Both** swipe deck + browse grid |
