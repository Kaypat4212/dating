# 🔧 Quick Fix for Echo/Daily.co Issues

## Problem
- ❌ Console shows "🔴 Laravel Echo initialized with Pusher" (should be Reverb)
- ❌ "Daily.co SDK not loaded" errors
- ❌ Real-time features not working

## Root Cause
Your `.env` file is configured for **Pusher** instead of **Reverb**, and frontend assets need rebuilding.

---

## 🚀 Quick Fix (Automated)

### On Server (SSH):
```bash
cd /home/heartsco/public_html

# Run the fix script
bash fix-reverb-echo.sh
```

**What it does:**
1. Updates `.env` to use Reverb
2. Clears Laravel caches
3. Rebuilds frontend assets with `npm run build`
4. Shows you next steps

---

## 🔧 Manual Fix (Alternative)

### Step 1: Edit .env file

```bash
nano .env
```

**Find and change these lines:**

```env
# BEFORE:
BROADCAST_CONNECTION=pusher

# AFTER:
BROADCAST_CONNECTION=reverb
```

**Add this line (after VITE_REVERB_SCHEME):**
```env
VITE_BROADCAST_DRIVER=reverb
```

**Fix this line:**
```env
# BEFORE:
VITE_REVERB_SCHEME=wss

# AFTER:
VITE_REVERB_SCHEME=https
```

**Fix this line:**
```env
# BEFORE:
REVERB_HOST=heartsconnect.site

# AFTER:
REVERB_HOST=heartsconnect.cc
```

### Step 2: Clear Caches

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### Step 3: Rebuild Frontend Assets

```bash
npm run build
```

⏱️ This will take 30-60 seconds. Wait for it to complete!

### Step 4: Restart Reverb

```bash
bash stop-reverb.sh
bash start-reverb.sh
```

### Step 5: Verify

1. Refresh your browser (Ctrl+Shift+R)
2. Open console (F12)
3. Look for: **"🟢 Laravel Echo initialized with Reverb"**
4. Try a voice/video call

---

## ✅ Expected Results After Fix

**Console should show:**
```
🟢 Laravel Echo initialized with Reverb
✅ Laravel Echo connected
✅ PWA Badge Manager initialized
```

**No more errors:**
- ❌ ~~"🔴 Laravel Echo initialized with Pusher"~~
- ❌ ~~"Laravel Echo not available"~~
- ❌ ~~"Daily.co SDK not loaded"~~ (only on non-chat pages, which is normal)

---

## 🐛 Troubleshooting

### If Echo still shows Pusher:

```bash
# Make sure .env has the correct values
grep BROADCAST .env
grep VITE_BROADCAST .env

# Should output:
# BROADCAST_CONNECTION=reverb
# VITE_BROADCAST_DRIVER=reverb
```

### If npm run build fails:

```bash
# Install/update dependencies first
npm install

# Then try again
npm run build
```

### If Reverb won't start:

```bash
# Check if port is blocked
netstat -tuln | grep 8080

# Kill any existing process
pkill -f "reverb:start"

# Start fresh
bash start-reverb.sh
```

---

## 📋 Summary

The issue is:
- Your `.env` has `BROADCAST_CONNECTION=pusher`
- Frontend JavaScript needs `VITE_BROADCAST_DRIVER=reverb`
- Assets must be rebuilt with `npm run build`

**Choose one:**
- 🟢 **Automated:** `bash fix-reverb-echo.sh` (recommended)
- 🔵 **Manual:** Follow steps 1-5 above

Both will get you to the same result: Reverb working with real-time features! 🎉
