# Firebase Cloud Messaging (FCM) Setup Guide

## Overview
Firebase Cloud Messaging (FCM) enables push notifications to users' browsers and mobile devices, allowing real-time alerts even when they're not actively on your dating site.

## Your Firebase Credentials

### API Key
```
AIzaSyAFBjyUOQ1DIhcTMyqo46fP27eWfsU38_I
```

### Service Account
- Project ID: `fire-base-dojo-9`
- Credentials: `storage/app/fire-base-dojo-9-38865f485255.json`

## Setup Steps

### 1. Get Additional Firebase Config Values

Visit [Firebase Console](https://console.firebase.google.com/project/fire-base-dojo-9/settings/general)

You need to get:
- **Messaging Sender ID** (from Cloud Messaging tab)
- **App ID** (from General tab)
- **VAPID Key** (from Cloud Messaging > Web Push certificates)

### 2. Add to Environment Variables

Add these to your `.env` file:

```env
# Firebase Cloud Messaging
FIREBASE_API_KEY=AIzaSyAFBjyUOQ1DIhcTMyqo46fP27eWfsU38_I
FIREBASE_PROJECT_ID=fire-base-dojo-9
FIREBASE_MESSAGING_SENDER_ID=your_sender_id_here
FIREBASE_APP_ID=your_app_id_here
FIREBASE_VAPID_KEY=your_vapid_key_here
```

### 3. Run Database Migration

```bash
php artisan migrate
```

This adds the `fcm_token` column to the users table.

### 4. Add API Routes

Add to `routes/web.php`:

```php
use App\Http\Controllers\FcmTokenController;

// FCM token management (requires authentication)
Route::middleware('auth')->group(function () {
    Route::post('/api/save-fcm-token', [FcmTokenController::class, 'store']);
    Route::delete('/api/remove-fcm-token', [FcmTokenController::class, 'destroy']);
});
```

### 5. Install Firebase SDK (Optional - for better integration)

```bash
npm install firebase
```

### 6. Update Frontend Files

Update the config values in these files with your actual Firebase credentials:
- `resources/js/firebase-init.js`
- `public/firebase-messaging-sw.js`

### 7. Include Firebase Script in Your Layout

Add to your main layout file (e.g., `resources/views/layouts/app.blade.php`):

```html
<script src="{{ asset('js/firebase-init.js') }}"></script>
```

## Using FCM in Your Notifications

### Example: Send Push Notification on New Match

Modify `app/Notifications/NewMatchNotification.php`:

```php
use App\Services\FirebaseCloudMessagingService;

public function via($notifiable)
{
    return ['database']; // existing channels
}

// Add after sending database notification
public function sendPushNotification($user)
{
    if ($user->fcm_token) {
        $fcm = app(FirebaseCloudMessagingService::class);
        $fcm->sendToDevice(
            $user->fcm_token,
            "New Match! 💕",
            "You have a new match. Say hello!",
            ['type' => 'match', 'url' => '/matches']
        );
    }
}
```

### Example: Send to Multiple Users

```php
$fcm = app(FirebaseCloudMessagingService::class);

$tokens = User::whereNotNull('fcm_token')
    ->whereIn('id', $userIds)
    ->pluck('fcm_token')
    ->toArray();

$fcm->sendToMultipleDevices(
    $tokens,
    "Special Event! 🎉",
    "Check out our Valentine's Day special offers"
);
```

### Example: Broadcast to All Users

```php
$fcm = app(FirebaseCloudMessagingService::class);

$fcm->sendToTopic(
    'all_users',
    "Maintenance Notice",
    "The app will be down for maintenance from 2-3 AM PST"
);
```

## Integration with Existing Notifications

Your app already has these notification types that can benefit from FCM:

✅ **Real-time Alerts:**
- NewMessageNotification
- NewMatchNotification
- ProfileLikedNotification
- WaveReceivedNotification
- TipReceivedNotification

✅ **Engagement Reminders:**
- ProfileReminderNotification
- LikeResetNotification
- WeeklyDigestNotification

✅ **Transaction Updates:**
- WalletFundedNotification
- DepositRejectedNotification
- PremiumPurchasedNotification

## Testing FCM

### Test from Browser Console
```javascript
// Request notification permission
window.requestNotificationPermission();
```

### Test from Backend
```php
use App\Services\FirebaseCloudMessagingService;

Route::get('/test-fcm', function() {
    $fcm = app(FirebaseCloudMessagingService::class);
    $user = auth()->user();
    
    if ($user && $user->fcm_token) {
        $result = $fcm->sendToDevice(
            $user->fcm_token,
            "Test Notification",
            "This is a test push notification!"
        );
        
        return $result ? 'Sent!' : 'Failed';
    }
    
    return 'No FCM token found';
});
```

## Troubleshooting

### Notifications Not Showing?
1. Check browser notification permissions
2. Verify FCM token is saved in database
3. Check browser console for errors
4. Ensure service worker is registered

### Check Service Worker Status
Visit: `chrome://serviceworker-internals/` (Chrome) or `about:debugging#/runtime/this-firefox` (Firefox)

### Check Logs
```bash
php artisan pail
```

Look for FCM-related log entries.

## Security Notes

⚠️ **Never commit API keys to public repositories**
- Add `.env` to `.gitignore`
- Use environment variables for all secrets
- Rotate keys if exposed

## Next Steps

1. ✅ Set up Firebase config in console
2. ✅ Add environment variables
3. ✅ Run migration
4. ✅ Test notification permission flow
5. ✅ Integrate with existing notifications
6. Monitor and optimize delivery rates

## Resources

- [Firebase Console](https://console.firebase.google.com/)
- [FCM Documentation](https://firebase.google.com/docs/cloud-messaging)
- [Web Push Notifications](https://firebase.google.com/docs/cloud-messaging/js/client)
