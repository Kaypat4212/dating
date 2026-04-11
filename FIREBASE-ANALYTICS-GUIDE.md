# Firebase & Google Analytics Integration Guide

## Overview

Complete Firebase and Google Analytics 4 integration has been added to your dating platform. This allows you to:

- **Track user behavior** (page views, clicks, conversions)
- **Monitor engagement** (messages sent, matches made, profile views)
- **Analyze conversions** (signups, purchases, premium upgrades)
- **Firebase Analytics** (real-time event tracking)
- **Google Analytics 4** (comprehensive reporting and insights)

---

## Admin Configuration

### Step 1: Access Settings Page

1. Login to admin panel: `https://heartsconnect.cc/admin`
2. Navigate to: **Site Settings → Firebase & Analytics**
3. You'll see two sections:
   - Google Analytics 4
   - Firebase Configuration

---

### Step 2: Configure Google Analytics 4

**Get your Measurement ID:**

1. Go to [analytics.google.com](https://analytics.google.com)
2. Click **Admin** (gear icon, bottom left)
3. Select your property
4. Click **Data Streams**
5. Select your web stream (or create one)
6. Copy the **Measurement ID** (format: `G-XXXXXXXXXX`)

**In Admin Panel:**

1. Toggle **Enable Google Analytics** → ON
2. Paste your **Measurement ID** (e.g., `G-ABC123XYZ`)
3. Click **Save Settings**

---

### Step 3: Configure Firebase (Optional)

**Get Firebase Config:**

1. Go to [console.firebase.google.com](https://console.firebase.google.com)
2. Select your project (or create one)
3. Click **Project Settings** (gear icon)
4. Scroll to **Your apps** section
5. Click **Web app** (</> icon)
6. Copy values from `firebaseConfig` object

**In Admin Panel:**

Fill in all fields:
- **API Key**: `AIzaSy...`
- **Auth Domain**: `your-project.firebaseapp.com`
- **Project ID**: `your-project-id`
- **Storage Bucket**: `your-project.appspot.com`
- **Messaging Sender ID**: `123456789012`
- **App ID**: `1:123456789012:web:...`
- **Measurement ID**: `G-XXXXXXXXXX` (optional, can match GA4)

Toggle **Enable Firebase Integration** → ON

Click **Save Settings**

---

## Automatic Event Tracking

The following events are automatically tracked:

### User Events
- `sign_up` - New user registration
- `login` - User login
- `profile_view` - Profile views
- `search` - Search queries

### Engagement Events
- `match_created` - New matches
- `message_sent` - Messages sent (text, image, audio, snap)
- `page_view` - Page navigation

### Monetization Events
- `purchase` - Credit purchases, premium upgrades
- `add_to_cart` - Items added to cart

---

## Manual Event Tracking

### In Blade Templates

```blade
{{-- Track button clicks --}}
<button onclick="gtag('event', 'button_click', {button_name: 'Subscribe'})">
    Subscribe Now
</button>

{{-- Track form submissions --}}
<form onsubmit="gtag('event', 'form_submit', {form_name: 'contact'})">
    ...
</form>

{{-- Using helper function --}}
{!! analytics_track('video_play', ['video_id' => $video->id]) !!}
```

### In JavaScript

```javascript
// Google Analytics (if enabled)
gtag('event', 'custom_event', {
    category: 'engagement',
    label: 'Chat Opened',
    value: 1
});

// Firebase Analytics (if enabled)
if (window.logAnalyticsEvent && window.firebaseAnalytics) {
    window.logAnalyticsEvent(window.firebaseAnalytics, 'chat_opened', {
        recipient_id: 123
    });
}
```

### In PHP Controllers

```php
use App\Services\Analytics;

// Track custom event
Analytics::track('profile_updated', [
    'user_id' => auth()->id(),
    'section' => 'photos'
]);

// Track match
Analytics::trackMatch(auth()->id(), $otherUserId);

// Track message
Analytics::trackMessage('image');

// Track purchase
Analytics::trackPurchase(99.99, 'USD', 'Premium Monthly');

// Track search
Analytics::trackSearch($query);
```

---

## Event Examples by Feature

### Dating Features

```javascript
// Like someone
gtag('event', 'like_sent', {user_id: 123});

// Superlike
gtag('event', 'superlike_sent', {user_id: 456});

// Match made
gtag('event', 'match_created', {
    user1_id: 1,
    user2_id: 2
});

// Unmatch
gtag('event', 'unmatch', {user_id: 789});
```

### Messaging Features

```javascript
// Send message
gtag('event', 'message_sent', {message_type: 'text'});

// Send snap
gtag('event', 'snap_sent', {media_type: 'image'});

// Voice call initiated
gtag('event', 'call_initiated', {call_type: 'voice'});

// Video call initiated
gtag('event', 'call_initiated', {call_type: 'video'});
```

### Premium Features

```javascript
// Premium purchase
gtag('event', 'purchase', {
    transaction_id: 'txn_123',
    value: 9.99,
    currency: 'USD',
    items: [{
        item_id: 'premium_monthly',
        item_name: 'Premium Monthly',
        price: 9.99
    }]
});

// Credit purchase
gtag('event', 'purchase', {
    value: 4.99,
    currency: 'USD',
    item_name: '100 Credits'
});
```

---

## Viewing Analytics Data

### Google Analytics 4

1. Go to [analytics.google.com](https://analytics.google.com)
2. Select your property
3. View reports:
   - **Real-time**: See live user activity
   - **Engagement**: Page views, events, conversions
   - **User attributes**: Demographics, interests
   - **Monetization**: Revenue, purchases
   - **Retention**: User loyalty, lifetime value

### Firebase Console

1. Go to [console.firebase.google.com](https://console.firebase.google.com)
2. Select your project
3. Click **Analytics** in left menu
4. View:
   - **Dashboard**: Overview of users, engagement
   - **Events**: Real-time event stream
   - **User properties**: Custom user attributes
   - **Audiences**: User segments

---

## Custom Dimensions & Metrics

### User Properties (Set on page load)

```javascript
// Set user properties
gtag('set', 'user_properties', {
    user_type: 'premium',
    gender: 'female',
    age_group: '25-34'
});
```

### Custom Events

```javascript
// Streak milestone
gtag('event', 'streak_milestone', {
    streak_days: 7,
    achievement: 'week_streak'
});

// Profile completion
gtag('event', 'profile_completion', {
    completion_percentage: 85
});
```

---

## Debugging

### Check if Analytics is Loaded

**Browser Console:**

```javascript
// Check Google Analytics
console.log(typeof gtag); // Should be "function"

// Check Firebase Analytics
console.log(window.firebaseAnalytics); // Should be an object
console.log(window.logAnalyticsEvent); // Should be a function
```

### Real-time Event Testing

1. **Google Analytics:**
   - Go to Analytics → Reports → Realtime
   - Trigger an event on your site
   - Should appear within seconds

2. **Firebase:**
   - Firebase Console → Analytics → DebugView
   - Enable debug mode: Add `?debug_mode=1` to URL
   - Events appear in real-time

### Enable Debug Mode

```javascript
// Google Analytics debug
gtag('config', 'G-XXXXXXXXXX', {
    'debug_mode': true
});

// Firebase debug (add to URL)
// https://heartsconnect.cc/?debug_mode=1
```

---

## Privacy & GDPR Compliance

### Disable Analytics for Users Who Opt Out

```javascript
// Disable tracking
gtag('consent', 'update', {
    'analytics_storage': 'denied'
});

// Re-enable tracking
gtag('consent', 'update', {
    'analytics_storage': 'granted'
});
```

### Anonymize IP Addresses

Already configured in the integration. User IPs are automatically anonymized.

### Cookie Consent

Consider adding a cookie consent banner:

```html
<!-- Simple example -->
<div id="cookieConsent" style="position:fixed;bottom:0;width:100%;background:#333;color:#fff;padding:15px;text-align:center;">
    We use cookies to improve your experience. 
    <button onclick="acceptCookies()">Accept</button>
</div>

<script>
function acceptCookies() {
    gtag('consent', 'update', {'analytics_storage': 'granted'});
    document.getElementById('cookieConsent').style.display = 'none';
    localStorage.setItem('cookieConsent', 'true');
}

// Hide banner if already accepted
if (localStorage.getItem('cookieConsent') === 'true') {
    document.getElementById('cookieConsent').style.display = 'none';
}
</script>
```

---

## Troubleshooting

### Analytics Not Working

1. **Check settings are saved:**
   - Admin → Firebase & Analytics
   - Verify Measurement ID is correct
   - Ensure toggle is ON

2. **Clear cache:**
   ```bash
   php artisan config:clear
   php artisan view:clear
   ```

3. **Check browser console for errors**

4. **Verify scripts loaded:**
   - View page source
   - Search for `googletagmanager.com`
   - Search for `firebase-analytics`

### Events Not Appearing

1. **Wait 24-48 hours** - GA4 processing delay
2. **Check Realtime reports** - Should be instant
3. **Verify event name format** - Use lowercase, underscores
4. **Check browser ad blockers** - May block tracking

### Firebase Not Initializing

1. **Check all config values are set**
2. **Verify API key is valid**
3. **Check browser console for error messages**
4. **Ensure project is active in Firebase Console**

---

## Best Practices

### Event Naming

- Use **lowercase** with **underscores**: `user_signup`, `message_sent`
- Be **descriptive**: `premium_purchase` not `purchase`
- Group related events: `profile_view`, `profile_edit`, `profile_delete`

### Event Parameters

- Keep parameter names **consistent** across events
- Use **standard parameters** when possible: `value`, `currency`, `item_name`
- Limit to **25 parameters per event**

### Performance

- Analytics loads **asynchronously** - won't slow down page
- Events are **batched** - minimal network overhead
- Use **sparingly** - don't track every click

---

## Integration Status

After saving settings, check the **Integration Status** section in admin:

```
✅ Google Analytics: ENABLED (G-ABC123XYZ)
✅ Firebase: ENABLED (Project: your-project-id)
```

Or:

```
❌ Google Analytics: DISABLED
⚠️ Firebase: ENABLED but incomplete configuration
```

---

## API Reference

### Helper Functions

```php
// Track custom event
analytics_track('event_name', ['param' => 'value']);

// Track page view
analytics_page_view('/profile/john-doe', ['referrer' => 'search']);

// Track purchase
analytics_purchase(99.99, 'USD', 'Premium Subscription');

// Check if enabled
if (analytics_enabled()) {
    // Do something
}
```

### Service Class

```php
use App\Services\Analytics;

Analytics::track('custom_event', $params);
Analytics::trackPageView($path);
Analytics::trackSignup('google');
Analytics::trackLogin('email');
Analytics::trackPurchase($value, $currency, $item);
Analytics::trackMatch($userId, $matchedUserId);
Analytics::trackMessage('snap');
Analytics::trackProfileView($profileId);
Analytics::trackSearch($query);
```

---

All set! Analytics is now fully integrated and ready to track user behavior across your platform. 📊
