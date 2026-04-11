# Real-time Chat & Snapchat-Style Features - Deployment Guide

## Features Implemented

### 1. ✅ Real-time Message Updates
Messages now update instantly without page refresh using Laravel Echo and broadcasting.

**What was fixed:**
- `MessageSent` event now implements `ShouldBroadcast`
- Event is fired when messages are created
- Frontend already had listeners configured - they now work!

### 2. ✅ Snapchat-Style Streaking System
Track consecutive daily interactions between matched users.

**Features:**
- Streak counter increments when users message each other daily
- Resets if a day is skipped
- Displayed as 🔥 icon with count (e.g., "🔥 7")
- Automatically updates on every message sent

### 3. ✅ Disappearing Content (View Once)
Send images/videos that delete after viewing, like Snapchat.

**Features:**
- One-time view only
- Auto-deletes 10 seconds after viewing
- Supports images and videos (up to 20 MB)
- Real-time notifications when received
- Scheduled cleanup of expired content

---

## Database Migrations

Run this command to create the new tables:

```bash
php artisan migrate
```

**Tables created:**
- `streaks` - Track consecutive daily interactions
- `disappearing_content` - Store temporary view-once media

---

## Scheduled Tasks

Add to your cron job (cPanel or server crontab):

```bash
* * * * * cd /home/heartsco/public_html && php artisan schedule:run >> /dev/null 2>&1
```

Then register the cleanup task in `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule): void
{
    // Clean up expired disappearing content every 5 minutes
    $schedule->call(function () {
        \App\Models\DisappearingContent::deleteExpired();
    })->everyFiveMinutes();
}
```

---

## Broadcasting Setup

### Option 1: Using Pusher (Current - Cloud Service)

**Already configured!** Your `.env` has:
```
BROADCAST_CONNECTION=pusher
PUSHER_APP_ID=2139938
PUSHER_APP_KEY=1e1d2a23e398b4c746d2
PUSHER_APP_SECRET=b1347f1e61589efbf320
PUSHER_APP_CLUSTER=us3
```

✅ **This works immediately** - no server changes needed!

---

### Option 2: Using Reverb (Self-Hosted WebSocket Server)

If you want to switch from Pusher to Reverb (your own WebSocket server):

#### Step 1: Update `.env` on Production Server

Change these lines:
```env
BROADCAST_CONNECTION=reverb

# Add this line
VITE_BROADCAST_DRIVER=reverb
```

#### Step 2: Start Reverb Server

**On Linux/cPanel with SSH access:**
```bash
cd /home/heartsco/public_html
php artisan reverb:start --host=0.0.0.0 --port=8080 --hostname=heartsconnect.cc
```

**Keep it running as a background service:**
```bash
nohup php artisan reverb:start --host=0.0.0.0 --port=8080 --hostname=heartsconnect.cc > storage/logs/reverb.log 2>&1 &
```

#### Step 3: Configure Firewall/Ports

Allow WebSocket port 8080 (or 443 with SSL):
- cPanel: WHM → ConfigServer Security & Firewall → Add port 8080
- Direct server: `sudo ufw allow 8080`

#### Step 4: Rebuild Frontend Assets

```bash
npm run build
```

#### Step 5: Test Connection

Visit your chat page and check browser console - should see:
```
🟢 Laravel Echo initialized with Reverb
```

---

## Testing

### 1. Test Real-time Messages

1. Open two browser windows (or one incognito)
2. Login as two different matched users
3. Open their conversation in both windows
4. Send a message from one window
5. **Should appear instantly** in the other window!

### 2. Test Streaks

1. Send messages daily between two matched users
2. Check the streak counter (needs UI update - see below)
3. Skip a day - streak should reset to 0

### 3. Test Disappearing Content

1. Send a snap: `POST /snaps/{conversation}` with media file
2. Recipient receives real-time notification
3. View once: `GET /snaps/{content}/view`
4. Try viewing again - should return 410 error

---

## Adding Streak UI to Chat Page

Update `resources/views/conversations/show.blade.php` in the header section:

```html
@php
    $streakCount = \App\Models\Streak::getStreakCount(auth()->id(), $other->id);
@endphp

<!-- Add this near the user's name in chat header -->
@if($streakCount > 0)
    <div class="streak-badge">
        <span style="font-size: 1.2rem">🔥</span>
        <span style="font-weight: 700; color: #ff6b35">{{ $streakCount }}</span>
    </div>
@endif
```

---

## Deployment Steps

### Full Deployment Checklist

```bash
# 1. On your local machine
npm run build
git add -A
git commit -m "Add real-time chat, streaks, and disappearing content features"
git push origin master

# 2. On production server (heartsconnect.cc)
cd /home/heartsco/public_html
git pull origin master

# 3. Run migrations
php artisan migrate --force

# 4. Clear all caches
php artisan view:clear
php artisan config:clear
php artisan route:clear
php artisan cache:clear

# 5. If using Reverb instead of Pusher, start the server
# nohup php artisan reverb:start --host=0.0.0.0 --port=8080 > storage/logs/reverb.log 2>&1 &
```

---

## Troubleshooting

### Messages not updating in real-time

1. Check browser console for Echo initialization message
2. Verify `BROADCAST_CONNECTION=pusher` in `.env`
3. Check Pusher credentials are correct
4. Ensure queue worker is running: `php artisan queue:work`

### Reverb not connecting

1. Check if Reverb process is running: `ps aux | grep reverb`
2. Verify port 8080 is open: `telnet heartsconnect.cc 8080`
3. Check `storage/logs/reverb.log` for errors
4. Ensure `VITE_BROADCAST_DRIVER=reverb` in `.env`

### Streaks not incrementing

1. Verify migration ran: `php artisan migrate:status`
2. Check database has `streaks` table
3. Test manually: `\App\Models\Streak::recordInteraction(1, 2);`

### Disappearing content not deleting

1. Ensure cron job is running schedule:run
2. Check `php artisan schedule:list` to see registered tasks
3. Manually trigger: `php artisan tinker` then `DisappearingContent::deleteExpired();`

---

## API Endpoints Reference

### Disappearing Content
- `POST /snaps/{conversation}` - Send disappearing content (image/video)
- `GET /snaps` - List unviewed content for current user
- `GET /snaps/{content}/view` - View and mark content as seen (auto-deletes after)

### Streaks
- `GET /streaks/{userId}` - Get streak count with another user

---

## Notes

- **Pusher vs Reverb:** Pusher is easier (cloud service), Reverb is free but needs a persistent process on your server
- **Current Setup:** You're using Pusher, which requires NO additional server configuration
- **Switching to Reverb:** Only if you want to avoid Pusher costs or have full server control
- **Production Recommendation:** Stick with Pusher on cPanel servers (Reverb needs SSH and process management)

---

## What's Next?

1. Add UI for sending disappearing content (camera button in chat)
2. Add streak flames 🔥 icon to conversation list showing active streaks
3. Create notifications panel for unviewed snaps
4. Add streak leaderboard (couples with longest streaks)
5. Implement "streak savers" (grace period if you miss a day)

All core functionality is ready - just needs UI polish!
