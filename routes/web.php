<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\ImpersonateController;
use App\Http\Controllers\Admin\FundingActionController;
use App\Http\Controllers\Admin\WalletFundingActionController;
use App\Http\Controllers\AiController;
use App\Http\Controllers\BadgeController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ChatRoomController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\InviteController;
use App\Http\Controllers\IcebreakerController;
use App\Http\Controllers\ProfileExtrasController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TravelController;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\FeatureRequestController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\BoostController;
use App\Http\Controllers\DailyMatchController;
use App\Http\Controllers\MessageReactionController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\VoiceCallController;
use App\Http\Controllers\WaveController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DatingProfileController;
use App\Http\Controllers\DiscoverController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\MatchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\PremiumController;
use App\Http\Controllers\ProfileSetupController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SwipeController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\SpeedDatingController;
use App\Http\Controllers\SecretMessageController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

// ─── Public landing ──────────────────────────────────────────────────────────
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
})->name('home');

// ─── PWA manifest & install page ─────────────────────────────────────────────
Route::get('/manifest.json', function () {
    $name = \App\Models\SiteSetting::get('site_name', config('app.name'));
    return response()->json([
        'name'             => $name,
        'short_name'       => $name,
        'description'      => 'Find meaningful connections on ' . $name,
        'start_url'        => url('/dashboard'),
        'scope'            => url('/'),
        'display'          => 'standalone',
        'orientation'      => 'portrait',
        'background_color' => '#ffffff',
        'theme_color'      => '#e91e63',
        'icons'            => [
            ['src' => asset('favicon.svg'), 'sizes' => 'any', 'type' => 'image/svg+xml'],
        ],
        // Badge display support (Chromium, Android, some iOS PWAs)
        'display_override' => ['window-controls-overlay', 'standalone'],
        'categories'       => ['social', 'lifestyle'],
        'prefer_related_applications' => false,
    ])->header('Content-Type', 'application/manifest+json')
      ->header('Cache-Control', 'public, max-age=3600');
})->name('pwa.manifest');

Route::get('/install-app', function () {
    return view('pwa.install');
})->name('pwa.install');

// Short alias used in marketing links
Route::get('/install', function () {
    return redirect()->route('pwa.install');
});

// ─── Referral tracking (public — no auth required) ───────────────────────────
Route::get('/ref/{code}', [InviteController::class, 'track'])
    ->name('invite.track')
    ->where('code', '[A-Za-z0-9]{4,12}');

// ─── Google OAuth ─────────────────────────────────────────────────────────────
Route::get('/auth/google',          [\App\Http\Controllers\Auth\GoogleController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\GoogleController::class, 'callback'])->name('auth.google.callback');

// Signed one-click admin funding actions (approve/reject from email/Telegram)
Route::get('/admin/funding-actions/{payment}/{action}/{admin}', [FundingActionController::class, 'handle'])
    ->whereIn('action', ['approve', 'reject'])
    ->middleware(['signed', 'throttle:60,1'])
    ->name('admin.funding.action');

Route::get('/admin/wallet-funding-actions/{fundingRequest}/{action}/{admin}', [WalletFundingActionController::class, 'handle'])
    ->whereIn('action', ['approve', 'reject'])
    ->middleware(['signed', 'throttle:60,1'])
    ->name('admin.wallet-funding.action');

Route::get('/sitemap.xml', function () {
    $urls = [
        ['loc' => route('home'), 'changefreq' => 'daily', 'priority' => '1.0'],
        ['loc' => route('login'), 'changefreq' => 'weekly', 'priority' => '0.8'],
        ['loc' => route('register'), 'changefreq' => 'weekly', 'priority' => '0.9'],
        ['loc' => route('legal.terms'), 'changefreq' => 'monthly', 'priority' => '0.3'],
        ['loc' => route('legal.privacy'), 'changefreq' => 'monthly', 'priority' => '0.3'],
    ];

    $xml = view('sitemap', [
        'urls' => $urls,
        'lastmod' => Carbon::now()->toDateString(),
    ])->render();

    return response($xml, 200)->header('Content-Type', 'application/xml');
})->name('sitemap');

// ─── Auth routes (Breeze) ────────────────────────────────────────────────────
require __DIR__ . '/auth.php';

// ─── Tipping feature routes ──────────────────────────────────────────────────
require __DIR__ . '/tips.php';

// ─── Reviews (public read + submit; comments/helpful require auth) ────────────
Route::prefix('reviews')->name('reviews.')->group(function () {
    Route::get('/',                      [ReviewController::class, 'index'])->name('index');
    Route::post('/',                     [ReviewController::class, 'store'])->name('store');
    Route::post('/{review}/comment',     [ReviewController::class, 'storeComment'])->name('comment.store');
    Route::post('/{review}/helpful',     [ReviewController::class, 'helpful'])->name('helpful');
});

// ─── Authenticated routes ─────────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    // ── Onboarding / Profile Setup ───────────────────────────────────────────
    Route::prefix('setup')->name('setup.')->group(function () {
        Route::get('/',                         [ProfileSetupController::class, 'index'])->name('index');
        Route::get('/step/{step}',              [ProfileSetupController::class, 'show'])->name('step');
        Route::post('/step/{step}',             [ProfileSetupController::class, 'store'])->name('store');
    });
    // States AJAX — available during setup (before profile.complete)
    Route::get('/api/states', function (Request $request) {
        $country = $request->input('country', '');
        $states  = \App\Helpers\StateHelper::forCountry($country);
        return response()->json($states);
    })->name('api.states');
    // ── All routes that need a completed profile ──────────────────────────────
    Route::middleware('profile.complete')->group(function () {

        // Dashboard
        Route::get('/dashboard',                [DashboardController::class, 'index'])->name('dashboard');

        // Invite friends / referral
        Route::get('/invite',                   [InviteController::class, 'index'])->name('invite.index');

        // Match preferences (reachable by completed users)
        Route::get('/preferences',              [ProfileSetupController::class, 'editPreferences'])->name('preferences.edit');
        Route::post('/preferences',             [ProfileSetupController::class, 'updatePreferences'])->name('preferences.update');

        // Saved filter packs (Premium)
        Route::prefix('filter-packs')->name('filter-packs.')->group(function () {
            Route::get('/',             [\App\Http\Controllers\FilterPackController::class, 'index'])->name('index');
            Route::post('/',            [\App\Http\Controllers\FilterPackController::class, 'store'])->name('store');
            Route::post('/{pack}/default', [\App\Http\Controllers\FilterPackController::class, 'setDefault'])->name('default');
            Route::delete('/{pack}',    [\App\Http\Controllers\FilterPackController::class, 'destroy'])->name('destroy');
        });

        // Discover / Browse grid
        Route::get('/discover',                 [DiscoverController::class, 'index'])->name('discover.index');

        // ── User Search (find people by name / username / email) ────────────
        Route::get('/find-people',              [\App\Http\Controllers\UserSearchController::class, 'index'])->name('users.search');
        Route::get('/find-people/search',       [\App\Http\Controllers\UserSearchController::class, 'search'])->name('users.search.results');

        // Swipe deck
        Route::get('/swipe',                    [SwipeController::class, 'deck'])->name('swipe.deck');
        Route::get('/swipe/deck',               [SwipeController::class, 'fetchDeck'])->name('swipe.fetch');
        Route::get('/swipe/top-picks',          [SwipeController::class, 'topPicks'])->name('swipe.top-picks');

        // Profile viewing / editing
        Route::get('/profile/me/edit',          [DatingProfileController::class, 'editDating'])->name('profile.edit');
        Route::put('/profile/me/edit',          [DatingProfileController::class, 'updateDating'])->name('profile.update');
        Route::get('/profile/me/viewers',       [DatingProfileController::class, 'whoViewedMe'])->name('profile.who-viewed');
        Route::get('/profile/{username}',       [DatingProfileController::class, 'show'])->name('profile.show');

        // Photos
        Route::post('/photos',                  [PhotoController::class, 'store'])->name('photos.store');
        Route::post('/photos/{photo}/primary',  [PhotoController::class, 'setPrimary'])->name('photos.primary');
        Route::delete('/photos/{photo}',        [PhotoController::class, 'destroy'])->name('photos.destroy');

        // Likes
        Route::get('/like/{user}',              fn($user) => redirect()->route('profile.show', \App\Models\User::findOrFail($user)->username))->name('like.show');
        Route::post('/like/{user}',             [LikeController::class, 'store'])->name('like.store');
        Route::delete('/like/{user}',           [LikeController::class, 'destroy'])->name('like.destroy');
        Route::get('/who-liked-me',             [LikeController::class, 'whoLikedMe'])->name('like.who-liked-me');

        // Matches
        Route::get('/matches',                  [MatchController::class, 'index'])->name('matches.index');
        Route::delete('/matches/{match}',       [MatchController::class, 'unmatch'])->name('matches.unmatch');

        // Conversations & Messages
        Route::get('/messages',                 [ConversationController::class, 'index'])->name('conversations.index');
        Route::get('/messages/ai',              [AiController::class, 'chatView'])->name('ai.chat');
        Route::get('/messages/{conversation}',  [ConversationController::class, 'show'])->name('conversations.show');
        Route::post('/messages/{conversation}', [MessageController::class, 'store'])->name('messages.store');
        Route::post('/messages/{conversation}/typing', [MessageController::class, 'typing'])->name('messages.typing');

        // Voice Calls
        Route::post('/calls/{conversation}/initiate', [VoiceCallController::class, 'initiate'])->name('calls.initiate');
        Route::post('/calls/{call}/answer',           [VoiceCallController::class, 'answer'])->name('calls.answer');
        Route::post('/calls/{call}/reject',           [VoiceCallController::class, 'reject'])->name('calls.reject');
        Route::post('/calls/{call}/end',              [VoiceCallController::class, 'end'])->name('calls.end');
        Route::get('/calls',                          [VoiceCallController::class, 'history'])->name('calls.history');
        Route::get('/calls/missed-count',             [VoiceCallController::class, 'missedCount'])->name('calls.missed-count');

        // AI Assistant
        Route::post('/ai/suggest',              [AiController::class, 'suggest'])->name('ai.suggest');
        Route::get('/ai/username-check',        [AiController::class, 'usernameCheck'])->name('ai.username-check');
        Route::get('/ai/status',                [AiController::class, 'status'])->name('ai.status');
        Route::post('/ai/chat',                 [AiController::class, 'chatReply'])->name('ai.chat.reply');

        // Blocks
        Route::get('/block/{user}',             fn(\App\Models\User $user) => redirect()->route('profile.show', $user->username))->name('block.get');
        Route::post('/block/{user}',            [BlockController::class, 'store'])->name('block.store');
        Route::delete('/block/{user}',          [BlockController::class, 'destroy'])->name('block.destroy');

        // Reports
        Route::post('/report/{user}',           [ReportController::class, 'store'])->name('report.store');

        // Premium (crypto payments)
        Route::get('/premium',                  [PremiumController::class, 'show'])->name('premium.show');
        Route::post('/premium',                 [PremiumController::class, 'submit'])->name('premium.submit');
        Route::post('/premium/upgrade',         [PremiumController::class, 'submitUpgrade'])->name('premium.upgrade.submit');
        Route::get('/premium/invoice/{payment}',[PremiumController::class, 'invoice'])->name('premium.invoice');

        // Notifications
        Route::get('/notifications',            [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
        Route::post('/notifications/read-all',  [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
        Route::get('/notifications/count',      [NotificationController::class, 'unreadCount'])->name('notifications.count');

        // Account / GDPR
        Route::get('/account',                  [AccountController::class, 'show'])->name('account.show');
        // Wallet
        Route::get('/wallet',                          [\App\Http\Controllers\WalletController::class, 'show'])->name('wallet.index');
        Route::get('/wallet/balance',                  [\App\Http\Controllers\WalletController::class, 'balance'])->name('wallet.balance');
        Route::get('/wallet/received',                 [\App\Http\Controllers\WalletController::class, 'receivedTips'])->name('wallet.received');
        Route::get('/wallet/sent',                     [\App\Http\Controllers\WalletController::class, 'sentTips'])->name('wallet.sent');
        Route::get('/wallet/funding-history',          [\App\Http\Controllers\WalletController::class, 'fundingHistory'])->name('wallet.funding-history');
        Route::get('/wallet/withdrawal-history',       [\App\Http\Controllers\WalletController::class, 'withdrawalHistory'])->name('wallet.withdrawal-history');
        Route::get('/wallet/transactions',             [\App\Http\Controllers\WalletController::class, 'transactions'])->name('wallet.transactions');
        Route::post('/wallet/fund',                    [\App\Http\Controllers\WalletController::class, 'fund'])->name('wallet.fund');
        Route::post('/wallet/withdraw',                [\App\Http\Controllers\WalletController::class, 'withdraw'])->name('wallet.withdraw');
        Route::get('/account/export',           [AccountController::class, 'export'])->name('account.export');
        Route::delete('/account',               [AccountController::class, 'destroy'])->name('account.destroy');
        Route::post('/account/pause',           [AccountController::class, 'pause'])->name('account.pause');
        Route::post('/account/last-seen',       [AccountController::class, 'toggleLastSeen'])->name('account.last-seen');
        Route::post('/account/private-photos',  [AccountController::class, 'togglePrivatePhotos'])->name('account.private-photos');
        Route::post('/account/read-receipts',   [AccountController::class, 'toggleReadReceipts'])->name('account.read-receipts');
        Route::get('/account/blocked',          [AccountController::class, 'blockedUsers'])->name('account.blocked');
        Route::post('/account/secret-word',     [AccountController::class, 'saveSecretWord'])->name('account.secret-word');
        Route::post('/account/notification-preferences', [AccountController::class, 'updateNotificationPreferences'])->name('account.notification-prefs');
        Route::get('/account/2fa/setup',              [AccountController::class, 'totpSetup'])->name('account.2fa.setup');
        Route::post('/account/2fa/enable',            [AccountController::class, 'totpEnable'])->name('account.2fa.enable');
        Route::post('/account/2fa/disable',           [AccountController::class, 'totpDisable'])->name('account.2fa.disable');

        // ── Waves / Wink ──────────────────────────────────────────────────────
        Route::post('/wave/{user}',             [WaveController::class, 'store'])->name('wave.store');
        Route::get('/waves',                    [WaveController::class, 'received'])->name('wave.received');
        Route::post('/waves/seen',              [WaveController::class, 'markSeen'])->name('wave.seen');

        // ── Stories ───────────────────────────────────────────────────────────
        Route::get('/stories',                          [StoryController::class, 'index'])->name('stories.index');
        Route::post('/stories',                         [StoryController::class, 'store'])->name('stories.store');
        Route::post('/stories/{story}/view',            [StoryController::class, 'markViewed'])->name('stories.view');
        Route::get('/stories/{story}/viewers',          [StoryController::class, 'viewers'])->name('stories.viewers');
        Route::delete('/stories/{story}',               [StoryController::class, 'destroy'])->name('stories.destroy');

        // ── Boost ─────────────────────────────────────────────────────────────
        Route::post('/boost',                   [BoostController::class, 'store'])->name('boost.store');
        Route::delete('/boost',                 [BoostController::class, 'destroy'])->name('boost.destroy');

        // ── Message reactions ─────────────────────────────────────────────────
        Route::post('/messages/react/{message}', [MessageReactionController::class, 'toggle'])->name('message.react');

        // ── Daily match suggestion ────────────────────────────────────────────
        Route::get('/daily-match',              [DailyMatchController::class, 'show'])->name('daily.match');

        // ── Identity Verification ─────────────────────────────────────────────
        Route::get('/verify',                   [VerificationController::class, 'show'])->name('verify.show');
        Route::post('/verify',                  [VerificationController::class, 'store'])->name('verify.store');

        // ── API: Unread Messages Count (for PWA badge) ────────────────────────
        Route::get('/api/unread-messages-count', function (Request $request) {
            $uid = $request->user()->id;
            $count = \App\Models\Message::whereHas('conversation.match', function ($q) use ($uid) {
                $q->where('user1_id', $uid)->orWhere('user2_id', $uid);
            })->where('sender_id', '!=', $uid)->whereNull('read_at')->count();
            
            return response()->json(['count' => $count]);
        })->name('api.unread-messages');

        // ── Admin: serve private verification documents (admin only) ──────────
        Route::get('/admin-verify-doc/{verification}/{type}', function (\App\Models\UserVerification $verification, string $type) {
            $authUser = Auth::user();
            abort_unless($authUser instanceof \App\Models\User && $authUser->hasRole('admin'), 403);
            abort_unless(in_array($type, ['selfie', 'id']), 404);
            $path = $type === 'selfie' ? $verification->selfie_path : $verification->id_document_path;
            abort_if(empty($path), 404);
            abort_unless(\Illuminate\Support\Facades\Storage::disk('private')->exists($path), 404);
            return response()->file(
                \Illuminate\Support\Facades\Storage::disk('private')->path($path),
                ['Content-Disposition' => 'inline']
            );
        })->name('admin.verify.doc');

        // ── Blog ──────────────────────────────────────────────────────────────
        Route::prefix('blog')->name('blog.')->group(function () {
            Route::get('/',                           [BlogController::class, 'index'])->name('index');
            Route::get('/category/{category:slug}',   [BlogController::class, 'category'])->name('category');
            // User post creation (blogger/admin role)
            Route::get('/create',                     [BlogController::class, 'create'])->name('create');
            Route::post('/create',                    [BlogController::class, 'store'])->name('store');
            Route::get('/{post:slug}/edit',           [BlogController::class, 'edit'])->name('edit');
            Route::put('/{post:slug}',                [BlogController::class, 'update'])->name('update');
            Route::delete('/{post:slug}',             [BlogController::class, 'destroy'])->name('destroy');
            Route::get('/{post:slug}',                [BlogController::class, 'show'])->name('show');
            Route::post('/{post:slug}/comment',       [BlogController::class, 'storeComment'])->name('comment.store');
        });

        // ── Forum ─────────────────────────────────────────────────────────────
        Route::prefix('forum')->name('forum.')->group(function () {
            Route::get('/',                                           [ForumController::class, 'index'])->name('index');
            Route::get('/{category:slug}',                            [ForumController::class, 'category'])->name('category');
            Route::get('/{category:slug}/new',                        [ForumController::class, 'createTopic'])->name('create-topic');
            Route::post('/{category:slug}/new',                       [ForumController::class, 'storeTopic'])->name('store-topic');
            Route::get('/{category:slug}/{topic:slug}',               [ForumController::class, 'topic'])->name('topic');
            Route::post('/{category:slug}/{topic:slug}/reply',        [ForumController::class, 'storeReply'])->name('reply');
        });

        // ── Chat Rooms ────────────────────────────────────────────────────────
        Route::prefix('chat-rooms')->name('chat-rooms.')->group(function () {
            Route::get('/',                       [ChatRoomController::class, 'index'])->name('index');
            Route::post('/',                      [ChatRoomController::class, 'store'])->name('store');
            Route::get('/join/{token}',           [ChatRoomController::class, 'joinViaToken'])->name('join-token');
            Route::get('/{chatRoom:slug}',        [ChatRoomController::class, 'show'])->name('show');
            Route::post('/{chatRoom:slug}/send',  [ChatRoomController::class, 'sendMessage'])->name('send');
            Route::get('/{chatRoom:slug}/messages',[ChatRoomController::class, 'messages'])->name('messages');
            Route::post('/{chatRoom:slug}/join',  [ChatRoomController::class, 'join'])->name('join');
            Route::post('/{chatRoom:slug}/leave', [ChatRoomController::class, 'leave'])->name('leave');
        });

        // ── Travel Buddy ──────────────────────────────────────────────────────
        Route::prefix('travel')->name('travel.')->group(function () {
            Route::get('/',                                         [TravelController::class, 'index'])->name('index');
            Route::post('/',                                        [TravelController::class, 'store'])->name('store');
            Route::delete('/{travelPlan}',                          [TravelController::class, 'destroy'])->name('destroy');
            Route::post('/{travelPlan}/interest',                   [TravelController::class, 'expressInterest'])->name('interest');
            Route::patch('/interest/{travelInterest}/{action}',     [TravelController::class, 'respondInterest'])->name('respond');
        });

        // ── Icebreakers ───────────────────────────────────────────────────────
        Route::prefix('icebreakers')->name('icebreaker.')->group(function () {
            Route::get('/',                                    [IcebreakerController::class, 'index'])->name('index');
            Route::post('/answer',                             [IcebreakerController::class, 'answer'])->name('answer');
            Route::delete('/answer/{icebreakerAnswer}',        [IcebreakerController::class, 'destroy'])->name('answer.destroy');
            Route::get('/questions',                           [IcebreakerController::class, 'questions'])->name('questions');
        });

        // ── Profile Extras (Pets & Voice Prompts) ─────────────────────────────
        Route::prefix('profile/extras')->name('extras.')->group(function () {
            Route::get('/pets',                    [ProfileExtrasController::class, 'petsIndex'])->name('pets');
            Route::post('/pets',                   [ProfileExtrasController::class, 'storePet'])->name('pets.store');
            Route::delete('/pets/{pet}',           [ProfileExtrasController::class, 'destroyPet'])->name('pets.destroy');
            Route::get('/voice',                   [ProfileExtrasController::class, 'voiceIndex'])->name('voice');
            Route::post('/voice',                  [ProfileExtrasController::class, 'storeVoice'])->name('voice.store');
            Route::delete('/voice/{voicePrompt}',  [ProfileExtrasController::class, 'destroyVoice'])->name('voice.destroy');
            Route::get('/voice/{voicePrompt}/play',[ProfileExtrasController::class, 'playVoice'])->name('voice.play');
        });

        // ── Coffee Break Speed Dating ─────────────────────────────────────────
        Route::prefix('speed-dating')->name('speed-dating.')->group(function () {
            Route::get('/',                            [SpeedDatingController::class, 'index'])->name('index');
            Route::post('/join',                       [SpeedDatingController::class, 'join'])->name('join');
            Route::post('/leave',                      [SpeedDatingController::class, 'leave'])->name('leave');
            Route::get('/status',                      [SpeedDatingController::class, 'status'])->name('status');
            Route::get('/{room}/messages',             [SpeedDatingController::class, 'messages'])->name('messages');
            Route::post('/{room}/send',                [SpeedDatingController::class, 'sendMessage'])->name('send');
            Route::post('/{room}/connect',             [SpeedDatingController::class, 'connect'])->name('connect');
        });

        // ── Secret Messages ───────────────────────────────────────────────────
        Route::post('/secret-messages',                          [SecretMessageController::class, 'store'])->name('secret-messages.store');
        Route::delete('/secret-messages/{secretMessage}',        [SecretMessageController::class, 'destroy'])->name('secret-messages.destroy');

        // ── Disappearing message timer ────────────────────────────────────────
        Route::patch('/messages/{conversation}/disappear', [ConversationController::class, 'setDisappearTimer'])->name('conversations.disappear');

        // ── Social Feed ───────────────────────────────────────────────────────
        Route::get('/feed',                                    [\App\Http\Controllers\FeedController::class, 'index'])->name('feed.index');
        Route::post('/feed',                                   [\App\Http\Controllers\FeedController::class, 'store'])->name('feed.store');
        Route::delete('/feed/{post}',                          [\App\Http\Controllers\FeedController::class, 'destroy'])->name('feed.destroy');
        Route::post('/feed/{post}/like',                       [\App\Http\Controllers\FeedController::class, 'like'])->name('feed.like');
        Route::post('/feed/{post}/repost',                     [\App\Http\Controllers\FeedController::class, 'repost'])->name('feed.repost');
        Route::post('/feed/{post}/comment',                    [\App\Http\Controllers\FeedController::class, 'comment'])->name('feed.comment');
        Route::get('/feed/{post}/comments',                    [\App\Http\Controllers\FeedController::class, 'comments'])->name('feed.comments');
        Route::delete('/feed/comments/{comment}',              [\App\Http\Controllers\FeedController::class, 'destroyComment'])->name('feed.comment.destroy');
        Route::post('/feed/comments/{comment}/like',           [\App\Http\Controllers\FeedController::class, 'likeComment'])->name('feed.comment.like');

        // ── Daily Streak check-in ─────────────────────────────────────────────
        Route::post('/checkin',                                [\App\Http\Controllers\StreakController::class, 'checkin'])->name('streak.checkin');

        // ── Badges & Achievements ─────────────────────────────────────────────
        Route::get('/badges',                     [BadgeController::class, 'index'])->name('badges.index');
        Route::post('/badges/{badge}/pin',        [BadgeController::class, 'togglePin'])->name('badges.pin');

        // ── Vibe Check Quiz ───────────────────────────────────────────────────
        Route::get('/vibe-quiz',                  [\App\Http\Controllers\VibeQuizController::class, 'show'])->name('vibe.quiz');
        Route::post('/vibe-quiz',                 [\App\Http\Controllers\VibeQuizController::class, 'submit'])->name('vibe.submit');

        // ── Second Chance Queue ───────────────────────────────────────────────
        Route::get('/second-chance',              [\App\Http\Controllers\SecondChanceController::class, 'index'])->name('second-chance.index');

        // ── Question of the Day ───────────────────────────────────────────────
        Route::get('/question-of-the-day',        [\App\Http\Controllers\MatchQuestionController::class, 'index'])->name('match-question.index');
        Route::post('/question-of-the-day',       [\App\Http\Controllers\MatchQuestionController::class, 'answer'])->name('match-question.answer');

        // ── Safe Date Check-In ────────────────────────────────────────────────
        Route::get('/safe-date',                  [\App\Http\Controllers\SafeDateController::class, 'index'])->name('safe-date.index');
        Route::post('/safe-date',                 [\App\Http\Controllers\SafeDateController::class, 'store'])->name('safe-date.store');
        Route::post('/safe-date/{checkin}/safe',  [\App\Http\Controllers\SafeDateController::class, 'markSafe'])->name('safe-date.safe');

        // ── What's New Announcements ──────────────────────────────────────────
        Route::get('/whats-new',                          [AnnouncementController::class, 'index'])->name('announcements.index');
        Route::get('/whats-new/unread',                   [AnnouncementController::class, 'unread'])->name('announcements.unread');
        Route::post('/whats-new/{announcement}/read',     [AnnouncementController::class, 'markRead'])->name('announcements.read');
        Route::post('/whats-new/read-all',                [AnnouncementController::class, 'readAll'])->name('announcements.read-all');
    });
});

// ── Public legal pages ────────────────────────────────────────────────────────
Route::prefix('legal')->name('legal.')->group(function () {
    Route::get('/terms',   fn() => view('legal.terms'))->name('terms');
    Route::get('/privacy', fn() => view('legal.privacy'))->name('privacy');
});

// ── Public info / support pages ───────────────────────────────────────────────
Route::prefix('pages')->name('pages.')->group(function () {
    Route::get('/contact',         [PageController::class, 'contact'])->name('contact');
    Route::post('/contact',        [PageController::class, 'contactSubmit'])->name('contact.submit');
    Route::get('/help-center',     [PageController::class, 'helpCenter'])->name('help-center');
    Route::get('/safety-tips',     [PageController::class, 'safetyTips'])->name('safety-tips');
    Route::get('/report-abuse',    [PageController::class, 'reportAbuse'])->name('report-abuse');
    Route::get('/cookie-settings', [PageController::class, 'cookieSettings'])->name('cookie-settings');
    Route::get('/feature-request', [FeatureRequestController::class, 'create'])->name('feature-request');
    Route::post('/feature-request',[FeatureRequestController::class, 'store'])->name('feature-request.store');
});

// ── Admin: Email template preview ────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/email-templates/{template}/preview', function (\App\Models\EmailTemplate $template) {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        abort_unless($user && ($user->is_admin ?? $user->hasRole('admin') ?? false), 403);
        $html = $template->render(array_fill_keys($template->variables ?? [], '<em style="color:#e91e8c">[' . ltrim(rtrim('%s', '}'), '{') . ']</em>'));
        // Replace each var with a coloured placeholder showing its name
        $vars = $template->variables ?? [];
        $html = $template->body;
        foreach ($vars as $var) {
            $label = htmlspecialchars($var);
            $html  = str_replace($var, '<span style="background:#fdf2f8;color:#e91e8c;border:1px dashed #e91e8c;border-radius:3px;padding:1px 5px;font-size:.85em">' . $label . '</span>', $html);
        }
        return view('emails.dynamic', [
            'html'    => $html,
            'subject' => $template->subject . ' (Preview)',
        ]);
    })->name('admin.email-templates.preview');
});

// ── Admin: Backup download ────────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/backup/download/{filename}', function (string $filename) {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        abort_unless($user && ($user->hasRole('admin') || $user->id === 1), 403);

        // Reject any path traversal attempts — allow only a plain .zip filename
        $filename = basename($filename);
        abort_unless(str_ends_with($filename, '.zip') && ! str_contains($filename, '/') && ! str_contains($filename, '\\'), 404);

        $path = storage_path('app/backups/' . $filename);
        abort_unless(file_exists($path), 404);

        return response()->download($path);
    })->name('admin.backup.download');
});

// ── Admin: Login-as-User (impersonation) ──────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::post('/admin-impersonate/{user}', function ($user) {
        /** @var \App\Models\User|null $userObj */
        $userObj = Auth::user();
        abort_unless($userObj && ($userObj->is_admin ?? $userObj->hasRole('admin') ?? false), 403);
        return app(ImpersonateController::class)->login(request(), \App\Models\User::findOrFail($user));
    })->name('impersonate.login');
});
Route::middleware(['auth'])->group(function () {
    Route::get('/admin-impersonate/leave', [ImpersonateController::class, 'leave'])->name('impersonate.leave');
});

// ── Dev-only: emergency admin bypass (local env only) ──────────────────────
if (app()->environment('local')) {
    Route::get('/dev-login-admin', function () {
        $user = \App\Models\User::find(1);
        if (! $user) {
            return 'User ID 1 not found.';
        }
        Auth::login($user, true);
        session()->regenerate();
        return redirect(\Filament\Facades\Filament::getPanel('admin')->getUrl() ?? (config('app.url') . '/admin'));
    });

    Route::get('/dev-auth-test', function () {
        $user = \App\Models\User::find(1);
        if (! $user) {
            return response()->json(['error' => 'User ID 1 not found']);
        }
        $attempt = Auth::attempt(['email' => $user->email, 'password' => 'Admin@2026']);
        $panel    = \Filament\Facades\Filament::getPanel('admin');
        $canAccess = $attempt ? $user->canAccessPanel($panel) : false;
        return response()->json([
            'email'             => $user->email,
            'attempt_result'    => $attempt,
            'can_access_panel'  => $canAccess,
            'email_verified_at' => $user->email_verified_at,
            'is_banned'         => $user->is_banned ?? false,
            'roles'             => $user->getRoleNames(),
            'app_url'           => config('app.url'),
        ]);
    });
}


