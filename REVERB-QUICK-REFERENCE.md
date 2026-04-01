# 🚀 Reverb Quick Reference

## 📋 Essential Commands (SSH)

### Start Server
```bash
./start-reverb.sh
```

### Stop Server
```bash
./stop-reverb.sh
```

### Check Status
```bash
ps aux | grep reverb | grep -v grep
```

### View Logs
```bash
tail -f storage/logs/reverb.log
```

### Manual Start (without script)
```bash
nohup php artisan reverb:start --host=0.0.0.0 --port=8080 > storage/logs/reverb.log 2>&1 &
```

### Manual Stop
```bash
pkill -f "reverb:start"
```

### Check Port
```bash
netstat -tuln | grep 8080
# or
lsof -i :8080
```

## 🔄 Auto-Restart (Cron)

Add to crontab (check every 5 minutes):
```cron
*/5 * * * * /home/username/public_html/reverb-watchdog.sh >> /home/username/public_html/storage/logs/reverb-watchdog.log 2>&1
```

## 🧪 Test Connection

### From SSH
```bash
telnet heartsconnect.cc 8080
```

### From Browser Console
```javascript
const ws = new WebSocket('ws://heartsconnect.cc:8080/app/dating-app');
ws.onopen = () => console.log('✅ Connected!');
ws.onerror = (err) => console.error('❌ Failed:', err);
```

### Using curl
```bash
curl -i -N -H "Connection: Upgrade" -H "Upgrade: websocket" -H "Host: heartsconnect.cc:8080" http://heartsconnect.cc:8080/
```

## 📊 Monitoring

### Process Info
```bash
# Find Reverb PID
pgrep -f "reverb:start"

# Detailed process info
ps aux | grep reverb

# CPU and memory usage
top -p $(pgrep -f "reverb:start")
```

### Logs
```bash
# Live Reverb logs
tail -f storage/logs/reverb.log

# Live watchdog logs
tail -f storage/logs/reverb-watchdog.log

# Last 50 lines of Reverb logs
tail -n 50 storage/logs/reverb.log

# Search for errors
grep -i error storage/logs/reverb.log
```

## 🔧 Troubleshooting

### Port already in use
```bash
# Find what's using port 8080
lsof -i :8080

# Kill specific process
kill -9 PID_NUMBER

# Then restart
./start-reverb.sh
```

### Permission denied
```bash
chmod +x start-reverb.sh stop-reverb.sh reverb-watchdog.sh
chmod -R 775 storage
```

### Can't connect from frontend
1. Check `.env` has correct domain:
   ```env
   VITE_REVERB_HOST=heartsconnect.cc
   VITE_REVERB_PORT=8080
   VITE_REVERB_SCHEME=ws
   ```

2. Rebuild frontend:
   ```bash
   npm run build
   ```

3. Check firewall allows port 8080

### Server crashes repeatedly
```bash
# Check error logs
tail -100 storage/logs/reverb.log

# Common issues:
# - Port already in use
# - Memory limit exceeded
# - Permission errors
```

## 📁 Important Files

| File | Purpose |
|------|---------|
| `start-reverb.sh` | Start Reverb server |
| `stop-reverb.sh` | Stop Reverb server |
| `reverb-watchdog.sh` | Auto-restart if crashed (use in cron) |
| `storage/logs/reverb.log` | Server output logs |
| `storage/logs/reverb-watchdog.log` | Watchdog script logs |
| `storage/logs/reverb.pid` | Current process ID |
| `REVERB-DEPLOYMENT.md` | Full deployment guide |

## 🌐 Production Configuration

### .env Settings
```env
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=dating-app
REVERB_APP_KEY=your-key
REVERB_APP_SECRET=your-secret
REVERB_HOST=0.0.0.0
REVERB_PORT=8080
REVERB_SCHEME=ws

# For SSL/HTTPS sites, use wss:
# REVERB_SCHEME=wss
# VITE_REVERB_SCHEME=wss
```

### Firewall Rules
```bash
# Allow incoming on port 8080
sudo ufw allow 8080/tcp

# For cPanel, contact support to open port 8080
```

## 🎯 First-Time Setup

```bash
# 1. Make scripts executable
chmod +x start-reverb.sh stop-reverb.sh reverb-watchdog.sh

# 2. Start server
./start-reverb.sh

# 3. Verify it's running
ps aux | grep reverb

# 4. Test connection
telnet localhost 8080

# 5. Set up cron watchdog
crontab -e
# Add: */5 * * * * /path/to/reverb-watchdog.sh

# 6. Test from browser
# Open developer console and run:
# new WebSocket('ws://heartsconnect.cc:8080/app/dating-app')
```

## 📞 Need Help?

- **Full Guide:** [REVERB-DEPLOYMENT.md](REVERB-DEPLOYMENT.md)
- **Deployment:** [DEPLOY.md](DEPLOY.md)
- **Laravel Reverb Docs:** https://laravel.com/docs/11.x/reverb
