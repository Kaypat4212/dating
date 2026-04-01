# ✅ Reverb Control - No SSH Required!

## 🎉 Good News!

Your **Reverb WebSocket server** can now be controlled directly from the web interface, **even without SSH access and with `exec()` disabled!**

The solution uses `proc_open()` instead of `exec()`, which is rarely blocked on cPanel hosting.

---

## 🚀 How to Start Reverb (3 Easy Ways)

### Method 1: Reverb Control Page (Recommended)

1. Log into your **Admin Panel**
2. Go to **System → Reverb Server**
3. Click **"Start Server"** button
4. Wait 2-3 seconds, server will start!

✅ **That's it!** No SSH, no terminal commands needed.

---

### Method 2: Artisan Runner Page (Quick Access)

1. Log into your **Admin Panel**
2. Go to **System → Artisan Runner**
3. Scroll to **"Reverb WebSocket"** section
4. Click **"🚀 Start Reverb Server"** button

✅ Server starts in the background automatically!

---

### Method 3: Shell Terminal (For Custom Commands)

1. Log into your **Admin Panel**
2. Go to **System → Artisan Runner**
3. Scroll to **"Shell Terminal"** section (amber/yellow colored)
4. Type: `php artisan reverb:start --host=0.0.0.0 --port=8080 &`
5. Click **"Execute Shell Command"**

✅ Full flexibility for advanced users!

---

## 📊 Check Server Status

**Reverb Control Page** shows real-time status:
- 🟢 **Server Running** - WebSocket server is active
- 🔴 **Server Stopped** - Server is not running
- ⚪ **Status Unknown** - Unable to determine (refresh the page)

---

## 🛑 Stop Reverb Server

**From Reverb Control Page:**
1. Go to **System → Reverb Server**
2. Click **"Stop Server"** button

**From Artisan Runner Shell Terminal:**
```bash
pkill -f "reverb:start"
```

---

## 🔄 Restart Reverb Server

**From Reverb Control Page:**
1. Go to **System → Reverb Server**
2. Click **"Restart Server"** button

This automatically stops and starts the server with a 2-second delay.

---

## 📝 View Logs

**Log Location:** `storage/logs/reverb.log`

**To view logs:**
1. cPanel **File Manager** → `your-project/storage/logs/reverb.log`
2. Right-click → **Edit** or **View**

**From Reverb Control Page:**
- The console output shows the last command's result
- Logs show startup messages, errors, and WebSocket connections

---

## ✨ Features That Work After Starting Reverb

Once Reverb is running, these features become **real-time**:

- ✅ **Online Status** - See who's online instantly
- ✅ **Live Notifications** - Push notifications appear immediately
- ✅ **Real-time Messaging** - Messages arrive without refresh
- ✅ **Typing Indicators** - See when someone is typing
- ✅ **Match Notifications** - New matches appear instantly
- ✅ **PWA Badge Updates** - App icon shows unread message count

---

## 🧪 Test WebSocket Connection

Visit: `https://heartsconnect.cc/reverb-test.html`

Or open browser console (F12) and run:
```javascript
const ws = new WebSocket('ws://heartsconnect.cc:8080/app/dating-app');
ws.onopen = () => console.log('✅ Connected to Reverb!');
ws.onerror = (err) => console.error('❌ Connection failed:', err);
```

---

## 🔧 Troubleshooting

### ❌ "Failed to start Reverb process"

**Solution 1:** Check if port 8080 is already in use
1. Go to **Artisan Runner → Shell Terminal**
2. Run: `netstat -tuln | grep 8080`
3. If port is used, either:
   - Stop the existing process using the "Stop Server" button
   - Change `REVERB_PORT` in `.env` to a different port (e.g., 8081)

**Solution 2:** Check permissions
- Ensure `storage/logs` directory is writable (chmod 775)

**Solution 3:** Check `.env` configuration
```env
REVERB_HOST=0.0.0.0
REVERB_PORT=8080
REVERB_SCHEME=ws
```

---

### ❌ "Server shows 'Running' but clients can't connect"

**Firewall Issue:** Port 8080 might be blocked by cPanel firewall

**Solution:**
1. Contact your hosting provider to open port 8080
2. OR use a reverse proxy (see REVERB-DEPLOYMENT.md)
3. OR use a different port (check which ports are open)

---

### ❌ "Server stops after a while"

**Memory Limit:** PHP process might be killed due to resource limits

**Solution:**
1. Set up the watchdog cron job (checks every 5 minutes):
   ```cron
   */5 * * * * /home/username/public_html/reverb-watchdog.sh
   ```

2. If no cron access, restart manually when needed via the web interface

---

## 🎯 Production Deployment Checklist

Before going live, ensure:

- ✅ Start Reverb from **Reverb Control** or **Artisan Runner**
- ✅ Verify status shows **"Server Running"** (green)
- ✅ Test connection: https://heartsconnect.cc/reverb-test.html
- ✅ Check `.env` has correct domain:
  ```env
  VITE_REVERB_HOST=heartsconnect.cc
  VITE_REVERB_PORT=8080
  VITE_REVERB_SCHEME=ws
  ```
- ✅ Rebuild frontend: `npm run build` (if changed .env)
- ✅ Test real-time features on production
- ✅ Monitor `storage/logs/reverb.log` for errors

---

## 📚 Additional Resources

- **Quick Reference:** [REVERB-QUICK-REFERENCE.md](REVERB-QUICK-REFERENCE.md)
- **Complete Deployment Guide:** [REVERB-DEPLOYMENT.md](REVERB-DEPLOYMENT.md)
- **General Deployment:** [DEPLOY.md](DEPLOY.md)
- **WebSocket Tester:** https://heartsconnect.cc/reverb-test.html

---

## 💡 Why This Works

**The Problem:**
- cPanel disables `exec()` for security
- SSH/PuTTY requires complex setup and credentials
- Previous solution required terminal/SSH access

**The Solution:**
- Uses `proc_open()` instead of `exec()`
- `proc_open()` is rarely blocked on cPanel
- Same method used in Artisan Runner Shell Terminal
- Works directly from web browser
- No SSH or special server access needed

**Technical Details:**
```php
// Old method (blocked):
exec('php artisan reverb:start');

// New method (works!):
proc_open('php artisan reverb:start', $descriptorspec, $pipes);
```

---

## 🎊 You're All Set!

Just click **"Start Server"** from the admin panel and you're done. No SSH, no terminal, no complex setup required!

**Last Updated:** April 2026  
**Laravel Version:** 11.x  
**Works on:** Most cPanel hosting environments
