# 🚀 Reverb Production Deployment - Complete Package

This folder contains everything you need to deploy Laravel Reverb WebSocket server in production.

## 📦 What's Included

```
deployment/
├── deploy-reverb.sh                 # Automated deployment script
├── .env.reverb.production           # Production .env template
├── supervisor/
│   └── reverb.conf                  # Supervisor process manager config
├── systemd/
│   └── reverb.service               # Systemd service config
├── nginx/
│   └── reverb-site.conf             # Nginx SSL proxy config
└── apache/
    └── heartsconnect-ssl.conf       # Apache SSL proxy config
```

## 🎯 Quick Start (Automated)

**Prerequisites:**
- Linux server (Ubuntu 20.04+, CentOS 7+)
- PHP 8.2+ with CLI
- Composer
- SSL certificate installed
- Root/sudo access

**Deploy in 2 steps:**

```bash
# 1. Upload your code to server
cd /var/www/html/dating

# 2. Run deployment script
sudo bash deployment/deploy-reverb.sh
```

The script will:
- ✅ Install dependencies
- ✅ Generate Reverb credentials
- ✅ Configure Supervisor or Systemd
- ✅ Set correct permissions
- ✅ Start Reverb server
- ✅ Verify installation

## 📋 Manual Setup Guide

### 1. Update .env File

```bash
cd /var/www/html/dating

# Copy production template
cat deployment/.env.reverb.production >> .env

# Edit with your domain
nano .env
```

**Critical settings:**
```env
BROADCAST_CONNECTION=reverb
REVERB_HOST=your-domain.com          # Your public domain (NO http://)
REVERB_PORT=443                      # 443 for HTTPS
REVERB_SCHEME=https                  # Use https in production
REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-secret
```

### 2. Choose Process Manager

#### Option A: Supervisor (Recommended)

```bash
# Install Supervisor
sudo apt-get update
sudo apt-get install supervisor

# Copy config
sudo cp deployment/supervisor/reverb.conf /etc/supervisor/conf.d/

# Update paths in config if different from /var/www/html/dating
sudo nano /etc/supervisor/conf.d/reverb.conf

# Start Reverb
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start reverb

# Check status
sudo supervisorctl status reverb
```

#### Option B: Systemd

```bash
# Copy service file
sudo cp deployment/systemd/reverb.service /etc/systemd/system/

# Update paths if needed
sudo nano /etc/systemd/system/reverb.service

# Enable and start
sudo systemctl daemon-reload
sudo systemctl enable reverb
sudo systemctl start reverb

# Check status
sudo systemctl status reverb
```

### 3. Configure Web Server

#### For Nginx:

```bash
# Install Nginx and certbot
sudo apt-get install nginx certbot python3-certbot-nginx

# Get SSL certificate
sudo certbot --nginx -d your-domain.com

# Copy site config (UPDATE domain and paths first!)
sudo cp deployment/nginx/reverb-site.conf /etc/nginx/sites-available/heartsconnect

# Edit to match your domain
sudo nano /etc/nginx/sites-available/heartsconnect

# Enable site
sudo ln -s /etc/nginx/sites-available/heartsconnect /etc/nginx/sites-enabled/

# Test and reload
sudo nginx -t
sudo systemctl reload nginx
```

#### For Apache:

```bash
# Enable required modules
sudo a2enmod ssl proxy proxy_http proxy_wstunnel rewrite headers

# Get SSL certificate
sudo apt-get install certbot python3-certbot-apache
sudo certbot --apache -d your-domain.com

# Copy site config (UPDATE domain and paths first!)
sudo cp deployment/apache/heartsconnect-ssl.conf /etc/apache2/sites-available/

# Edit to match your domain
sudo nano /etc/apache2/sites-available/heartsconnect-ssl.conf

# Enable site
sudo a2ensite heartsconnect-ssl

# Test and reload
sudo apache2ctl configtest
sudo systemctl reload apache2
```

### 4. Clear Caches & Set Permissions

```bash
cd /var/www/html/dating

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Fix permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### 5. Verify Installation

```bash
# Check Reverb is running
ps aux | grep reverb

# Check port is listening
sudo netstat -tlnp | grep 8080

# Check logs
tail -f storage/logs/reverb.log

# Test from browser (open F12 console)
# You should see WebSocket connection established
```

## ✅ Verification Checklist

After deployment, verify:

- [ ] **Reverb is running:** `sudo supervisorctl status reverb` or `sudo systemctl status reverb`
- [ ] **Port 8080 listening:** `sudo netstat -tlnp | grep 8080`
- [ ] **SSL certificate valid:** `sudo certbot certificates`
- [ ] **Web server reloaded:** `sudo systemctl reload nginx` or `apache2`
- [ ] **No errors in logs:** `tail -100 storage/logs/reverb.log`
- [ ] **WebSocket connects from browser:** Check F12 console for Echo connection
- [ ] **REVERB_HOST is public domain:** `grep REVERB_HOST .env` (NOT localhost!)
- [ ] **REVERB_SCHEME=https:** `grep REVERB_SCHEME .env`

## 🔄 Common Operations

### Restart Reverb
```bash
# With Supervisor
sudo supervisorctl restart reverb

# With Systemd
sudo systemctl restart reverb
```

### View Logs
```bash
# Reverb logs
tail -f storage/logs/reverb.log

# Supervisor logs
sudo supervisorctl tail -f reverb

# Systemd logs
sudo journalctl -u reverb -f
```

### Deploy Code Updates
```bash
cd /var/www/html/dating
git pull
composer install --no-dev --optimize-autoloader
php artisan config:clear
sudo supervisorctl restart reverb
```

## 🐛 Troubleshooting

### Reverb won't start
```bash
# Check logs
tail -100 storage/logs/reverb.log

# Try manual start to see errors
php artisan reverb:start --debug

# Check .env configuration
grep REVERB .env

# Verify permissions
ls -la storage/logs/
```

### Connection refused in browser
```bash
# Check if Reverb is running
ps aux | grep reverb

# Check web server proxy config
sudo nginx -t  # or sudo apache2ctl configtest

# Check firewall
sudo ufw status
sudo ufw allow 443/tcp
```

### Port 8080 already in use
```bash
# Find what's using the port
sudo lsof -i :8080

# Kill that process
sudo kill -9 <PID>

# Or change Reverb port in .env
REVERB_SERVER_PORT=8081
```

## 📚 Documentation

- **Full Guide:** `../REVERB-PRODUCTION-GUIDE.md` (comprehensive 100+ page guide)
- **Quick Reference:** `../REVERB-QUICK-REFERENCE.md` (for cPanel/shared hosting)
- **Command Cheat Sheet:** `../REVERB-COMMANDS.md` (quick commands)

## 🆘 Support

If you encounter issues:

1. **Check logs:** `storage/logs/reverb.log` and `storage/logs/laravel.log`
2. **Run debug mode:** `php artisan reverb:start --debug`
3. **Verify .env:** Ensure all REVERB_* variables are correct
4. **Check process:** `ps aux | grep reverb`
5. **Test port:** `sudo netstat -tlnp | grep 8080`

## 🔐 Security Notes

- ✅ **Never expose port 8080** publicly - only access via Nginx/Apache proxy
- ✅ **Use HTTPS** (REVERB_SCHEME=https) in production
- ✅ **Keep credentials secret** (REVERB_APP_KEY, REVERB_APP_SECRET)
- ✅ **Use strong SSL certificates** (Let's Encrypt or commercial)
- ✅ **Set proper file permissions** (www-data user for storage/)

## 📞 Production Checklist

Before going live:

- [ ] SSL certificate installed and valid
- [ ] Reverb credentials generated and configured
- [ ] REVERB_HOST set to public domain (not localhost)
- [ ] REVERB_SCHEME=https and REVERB_PORT=443
- [ ] Process manager (Supervisor/Systemd) configured
- [ ] Web server proxy configured with SSL
- [ ] Firewall allows HTTPS (port 443)
- [ ] Port 8080 NOT publicly exposed
- [ ] All caches cleared
- [ ] Browser WebSocket test passed
- [ ] Logs directory writable

---

**Last Updated:** April 2026  
**Project:** Hearts Connect Dating Platform  
**Support:** See full documentation in parent directory
