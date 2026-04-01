# 🚀 Reverb WebSocket Server - cPanel Deployment Guide

This guide explains how to run Laravel Reverb on cPanel hosting when the `exec()` PHP function is disabled.

---

## 📋 Table of Contents

1. [Understanding the Problem](#understanding-the-problem)
2. [Solution Options](#solution-options)
3. [Quick Start (Recommended)](#quick-start-recommended)
4. [Manual SSH Deployment](#manual-ssh-deployment)
5. [Auto-Restart with Cron Job](#auto-restart-with-cron-job)
6. [Monitoring & Troubleshooting](#monitoring--troubleshooting)
7. [Using Supervisor (If Available)](#using-supervisor-if-available)

---

## 🔍 Understanding the Problem

### Why doesn't the web control panel work?

The **Reverb Control** page in the admin panel uses PHP's `exec()` function to start/stop the WebSocket server. On cPanel shared hosting, `exec()` is typically disabled for security reasons.

**Error you see:**
```
⚠️ The exec() function is disabled on this server.
Please enable it in php.ini or contact your hosting provider.
```

### The Solution

You must start Reverb via **SSH terminal** instead of the web interface. The provided scripts make this easy!

---

## 🎯 Solution Options

| Method | Pros | Cons | Best For |
|--------|------|------|----------|
| **SSH Scripts (Recommended)** | Simple, fast, no cPanel changes needed | Requires SSH access | Most users |
| **Cron Watchdog** | Auto-restarts if crashed | 5-minute delay on crashes | Production stability |
| **Supervisor** | Best reliability, auto-restart | Requires cPanel config | Advanced users |
| **Screen/Tmux** | Persistent terminal session | Manual process | Development |

---

## ⚡ Quick Start (Recommended)

### Step 1: Upload Scripts to cPanel

Upload these files to your project root via **cPanel File Manager** or **FTP**:
- `start-reverb.sh`
- `stop-reverb.sh`
- `reverb-watchdog.sh`

### Step 2: Make Scripts Executable

Connect to your cPanel via **SSH** (Terminal in cPanel or PuTTY):

```bash
cd ~/public_html  # or wherever your project is located
chmod +x start-reverb.sh stop-reverb.sh reverb-watchdog.sh
```

### Step 3: Start Reverb Server

```bash
./start-reverb.sh
```

**Expected output:**
```
╔════════════════════════════════════════════════╗
║  Hearts Connect - Reverb Server Startup      ║
╚════════════════════════════════════════════════╝

✅ Environment loaded from .env
📋 Configuration:
   Host: 0.0.0.0
   Port: 8080
   Log:  storage/logs/reverb.log

🚀 Starting Reverb server...
✅ Reverb server started successfully!

════════════════════════════════════════════════
   PID: 12345
   WebSocket: ws://0.0.0.0:8080/app/dating-app
   Log file: storage/logs/reverb.log
════════════════════════════════════════════════
```

✅ **Done!** Your Reverb server is now running in the background.

---

## 🛠️ Manual SSH Deployment

If you prefer to run commands manually:

### Start Reverb (Background Process)

```bash
cd ~/public_html  # Your project directory
nohup php artisan reverb:start --host=0.0.0.0 --port=8080 > storage/logs/reverb.log 2>&1 &
```

### Check if Running

```bash
ps aux | grep reverb
```

Expected output:
```
username  12345  0.1  2.3 /usr/bin/php artisan reverb:start
```

### View Live Logs

```bash
tail -f storage/logs/reverb.log
```

### Stop Reverb

```bash
pkill -f "reverb:start"
```

### Restart Reverb

```bash
pkill -f "reverb:start" && sleep 2 && nohup php artisan reverb:start --host=0.0.0.0 --port=8080 > storage/logs/reverb.log 2>&1 &
```

---

## 🔄 Auto-Restart with Cron Job

Set up a cron job to automatically restart Reverb if it crashes.

### Step 1: Test the Watchdog Script

```bash
./reverb-watchdog.sh
```

If Reverb is already running, you'll see no output. If stopped, it will start automatically.

### Step 2: Add to Crontab

Edit your crontab:

```bash
crontab -e
```

Add this line (check every 5 minutes):

```cron
*/5 * * * * /home/username/public_html/reverb-watchdog.sh >> /home/username/public_html/storage/logs/reverb-watchdog.log 2>&1
```

**Replace `/home/username/public_html` with your actual project path!**

### Alternative: cPanel Cron Jobs UI

1. Log into **cPanel**
2. Go to **Cron Jobs**
3. Add new cron job:
   - **Minute:** `*/5` (every 5 minutes)
   - **Hour:** `*`
   - **Day:** `*`
   - **Month:** `*`
   - **Weekday:** `*`
   - **Command:** `/home/username/public_html/reverb-watchdog.sh`

### View Watchdog Logs

```bash
tail -f storage/logs/reverb-watchdog.log
```

---

## 🔍 Monitoring & Troubleshooting

### Check Server Status

```bash
# Check if process is running
ps aux | grep reverb | grep -v grep

# Check if port is listening
netstat -tuln | grep 8080

# Check what's using port 8080
lsof -i :8080
```

### View Reverb Logs

```bash
# Live log monitoring
tail -f storage/logs/reverb.log

# Last 100 lines
tail -n 100 storage/logs/reverb.log

# Search for errors
grep -i error storage/logs/reverb.log
```

### Common Issues

#### ❌ Port 8080 already in use

```bash
# Find what's using the port
lsof -i :8080

# Kill the process (replace 12345 with actual PID)
kill -9 12345

# Then restart
./start-reverb.sh
```

#### ❌ Permission denied

```bash
# Make sure scripts are executable
chmod +x start-reverb.sh stop-reverb.sh reverb-watchdog.sh

# Make sure storage/logs is writable
chmod -R 775 storage/logs
chown -R username:username storage/logs
```

#### ❌ Reverb starts but clients can't connect

**Check your firewall and `.env` configuration:**

```env
REVERB_HOST=0.0.0.0           # Listen on all interfaces
REVERB_PORT=8080              # Make sure this port is open
REVERB_SCHEME=ws              # Use 'wss' for SSL

VITE_REVERB_HOST=heartsconnect.cc   # Your domain
VITE_REVERB_PORT=8080
VITE_REVERB_SCHEME=ws         # Change to 'wss' if using SSL
```

**Test connection from browser console:**

```javascript
const ws = new WebSocket('ws://heartsconnect.cc:8080/app/dating-app');
ws.onopen = () => console.log('✅ Connected to Reverb!');
ws.onerror = (err) => console.error('❌ Connection failed:', err);
```

---

## 🔧 Using Supervisor (If Available)

If your cPanel has **Supervisor** installed, this is the most robust solution.

### Step 1: Create Supervisor Config

Create file: `/home/username/supervisor/reverb.conf`

```ini
[program:reverb]
process_name=%(program_name)s
command=php /home/username/public_html/artisan reverb:start --host=0.0.0.0 --port=8080
directory=/home/username/public_html
user=username
autostart=true
autorestart=true
redirect_stderr=true
stdout_logfile=/home/username/public_html/storage/logs/reverb.log
stdout_logfile_maxbytes=10MB
stopwaitsecs=60
```

### Step 2: Load Configuration

```bash
supervisorctl reread
supervisorctl update
supervisorctl start reverb
```

### Step 3: Manage Service

```bash
# Check status
supervisorctl status reverb

# Start
supervisorctl start reverb

# Stop
supervisorctl stop reverb

# Restart
supervisorctl restart reverb

# View logs
supervisorctl tail -f reverb
```

---

## 📊 Production Checklist

Before going live, ensure:

- ✅ Reverb is running and listening on the correct port
- ✅ Firewall allows incoming connections on port 8080
- ✅ `.env` has correct `VITE_REVERB_HOST` (your domain)
- ✅ Cron job watchdog is active (checks every 5 minutes)
- ✅ SSL certificate configured (use `wss://` instead of `ws://`)
- ✅ Test real-time features work on production
- ✅ Monitor `storage/logs/reverb.log` for errors

---

## 🌐 Using SSL/WSS (HTTPS Sites)

If your site uses HTTPS, you **must** use secure WebSocket (`wss://`).

### Option 1: Use a Reverse Proxy (Recommended)

Configure Apache/Nginx to proxy WebSocket traffic:

**Apache (`.htaccess` or virtualhost):**

```apache
# Enable required modules first
# a2enmod proxy proxy_http proxy_wstunnel

<VirtualHost *:443>
    ServerName heartsconnect.cc
    
    # ... your SSL config ...
    
    # Proxy WebSocket traffic
    ProxyPass /ws/ ws://127.0.0.1:8080/
    ProxyPassReverse /ws/ ws://127.0.0.1:8080/
</VirtualHost>
```

**Then update `.env`:**

```env
VITE_REVERB_HOST=heartsconnect.cc
VITE_REVERB_PORT=443
VITE_REVERB_SCHEME=wss
VITE_REVERB_PATH=/ws
```

### Option 2: Reverb with SSL Certificate

**Update `.env`:**

```env
REVERB_SCHEME=wss
REVERB_SSL_CERT=/path/to/ssl/cert.pem
REVERB_SSL_KEY=/path/to/ssl/key.pem
```

**Start with SSL:**

```bash
php artisan reverb:start \
  --host=0.0.0.0 \
  --port=8080 \
  --cert=/path/to/cert.pem \
  --key=/path/to/key.pem
```

---

## 📞 Getting Help

### Still having issues?

1. **Check logs:** `tail -f storage/logs/reverb.log`
2. **Test port:** `telnet heartsconnect.cc 8080`
3. **Check process:** `ps aux | grep reverb`
4. **View Laravel logs:** `tail -f storage/logs/laravel.log`

### Contact Support

- **cPanel Provider:** Ask if they can enable `exec()` or install Supervisor
- **Firewall Issues:** Ensure port 8080 is open in cPanel firewall
- **SSL Problems:** Verify your SSL certificate covers WebSocket connections

---

## 🎉 Success!

Once Reverb is running:

✅ **Real-time notifications** will work  
✅ **PWA badges** will update instantly  
✅ **Online status** will be live  
✅ **Typing indicators** will appear  
✅ **Messages** will arrive in real-time  

Monitor the **Reverb Control** page in admin panel to see the status (it will show "Control Unavailable" but that's OK - server is running via SSH!).

---

**Last Updated:** April 2026  
**Laravel Version:** 11.x  
**Reverb Version:** Latest
