# Daily.co Video Calling Setup Guide

Your dating site now uses **Daily.co** for high-quality voice and video calls (Jitsi has been completely removed).

## 🚀 Quick Setup (5 minutes)

### Step 1: Get Your Free Daily.co API Key

1. Go to https://dashboard.daily.co/signup
2. Sign up with your email (free tier: 10,000 participant-minutes/month)
3. Verify your email
4. Log in to https://dashboard.daily.co
5. Copy your **API Key** from the dashboard
6. Copy your **Domain** (the subdomain part before `.daily.co`, e.g., `heartsconnect`)

### Step 2: Add to Your .env File

Open `c:\xampp\htdocs\dating\.env` and add these lines:

```env
DAILY_CO_API_KEY=your_api_key_here
DAILY_CO_DOMAIN=your_domain_here
```

**Example:**
```env
DAILY_CO_API_KEY=abc123def456ghi789jkl012mno345pqr678stu901vwx234yz
DAILY_CO_DOMAIN=heartsconnect
```

### Step 3: Clear Caches

Run these commands in your terminal:

```bash
php artisan config:clear
php artisan cache:clear
php artisan optimize:clear
```

### Step 4: Test

1. Go to **Admin Panel** → **System** → **API Key Tester**
2. Scroll to **Daily.co Video Calls** section
3. Click **Test Daily.co**
4. You should see: ✅ **Valid Daily.co API key**

## 📱 Using Voice & Video Calls

### For Users

1. **Start a call:** Click the 📞 (voice) or 📹 (video) button in any conversation
2. **Answer a call:** When someone calls, click the green **Answer** button
3. **During call:**
   - 🎤 Toggle microphone
   - 📹 Toggle camera (video calls)
   - 🔊 Toggle speaker
   - ❌ Hang up

### Features

- ✅ Peer-to-peer HD video/audio (up to 2 participants)
- ✅ End-to-end encryption
- ✅ Works on mobile & desktop
- ✅ No app downloads required
- ✅ Daily call limits (configurable in admin)
- ✅ Call history & duration tracking
- ✅ Real-time notifications via Laravel Reverb

## ⚙️ Admin Configuration

**Site Settings → Voice Calls:**

- **Voice Calls Enabled:** Turn calling feature on/off
- **Daily Limit:** Max calls per user per day (0 = unlimited)
- **Token Expire:** How long call tokens are valid (3600 seconds = 1 hour)

## 📊 Free Tier Limits

Daily.co Free tier includes:
- **10,000 participant-minutes/month** (e.g., 5,000 × 2-minute calls)
- **Unlimited rooms** (auto-created and deleted)
- **2 participants max** (perfect for 1-on-1 dating calls)
- **No credit card required**

## 🛠️ Troubleshooting

### "Daily.co API key not configured" Error

**Solution:** Make sure you added `DAILY_CO_API_KEY` to your `.env` file and ran `php artisan config:clear`.

### Calls Not Connecting

1. **Check API key:** Visit Admin → API Key Tester
2. **Check browser console:** Press F12, look for errors
3. **Check Laravel logs:** `storage/logs/laravel.log`
4. **Test internet:** Daily.co requires stable connection

### "Failed to create Daily.co room" Error

**Possible causes:**
- Invalid API key
- Daily.co API down (check https://status.daily.co)
- Rate limit exceeded (upgrade plan if needed)

### Video Not Showing

1. **Camera permissions:** Browser must allow camera access
2. **HTTPS required:** Daily.co requires secure connection (use `https://` not `http://`)
3. **Check camera:** Test at https://webcamtests.com

## 🔒 Security

- ✅ **Tokens expire** after 1 hour (configurable)
- ✅ **Rooms auto-delete** after calls end
- ✅ **End-to-end encryption** enabled by default
- ✅ **No third-party data sharing**
- ✅ **GDPR compliant**

## 📈 Upgrade Daily.co Plan (Optional)

If you exceed 10,000 minutes/month:

- **Starter:** $99/month (100,000 mins)
- **Business:** $299/month (500,000 mins)
- **Enterprise:** Custom pricing

Visit https://www.daily.co/pricing for details.

## 📞 Support

- **Daily.co Docs:** https://docs.daily.co
- **Daily.co Support:** support@daily.co
- **Status Page:** https://status.daily.co

---

**Note:** Jitsi Meet has been completely removed. Daily.co is now the only calling provider.
