# 🎯 Reverb Production Commands Cheat Sheet

## 🚀 Supervisor (Recommended for Production)

```bash
# Status & Control
sudo supervisorctl status reverb          # Check if running
sudo supervisorctl start reverb           # Start
sudo supervisorctl stop reverb            # Stop
sudo supervisorctl restart reverb         # Restart
sudo supervisorctl tail -f reverb         # Live logs

# Configuration
sudo supervisorctl reread                 # Reload config files
sudo supervisorctl update                 # Apply config changes
sudo nano /etc/supervisor/conf.d/reverb.conf  # Edit config

# Logs
tail -f /var/www/html/dating/storage/logs/reverb.log
sudo supervisorctl tail -f reverb stderr  # Error logs only
```

## 🐧 Systemd (Alternative)

```bash
# Status & Control
sudo systemctl status reverb              # Check status  
sudo systemctl start reverb               # Start
sudo systemctl stop reverb                # Stop
sudo systemctl restart reverb             # Restart

# Auto-start
sudo systemctl enable reverb              # Enable on boot
sudo systemctl disable reverb             # Disable on boot

# Logs
sudo journalctl -u reverb -f              # Live logs
sudo journalctl -u reverb -n 100          # Last 100 lines
sudo journalctl -u reverb --since today   # Today only

# Configuration
sudo systemctl daemon-reload              # After config changes
sudo nano /etc/systemd/system/reverb.service
```

## 🌐 Nginx Commands

```bash
# Test & Reload
sudo nginx -t                             # Test config
sudo systemctl reload nginx               # Reload (no downtime)
sudo systemctl restart nginx              # Full restart

# Logs
tail -f /var/log/nginx/error.log
tail -f /var/log/nginx/access.log

# Edit Config
sudo nano /etc/nginx/sites-available/heartsconnect
```

## 🔧 Apache Commands

```bash
# Test & Reload
sudo apache2ctl configtest                # Test config
sudo systemctl reload apache2             # Reload
sudo systemctl restart apache2            # Restart

# Enable Modules
sudo a2enmod proxy proxy_http proxy_wstunnel ssl rewrite
sudo systemctl restart apache2

# Edit Config
sudo nano /etc/apache2/sites-available/heartsconnect-ssl.conf
```

## 🔍 Debugging

```bash
# Check if Reverb is running
ps aux | grep reverb
pgrep -f reverb

# Check what's listening on port 8080
sudo netstat -tlnp | grep 8080
sudo lsof -i :8080

# Count active connections
sudo netstat -an | grep :8080 | grep ESTABLISHED | wc -l

# View .env Reverb settings
grep REVERB .env

# Clear Laravel caches
php artisan config:clear
php artisan cache:clear

# Manual test with debug
cd /var/www/html/dating
php artisan reverb:start --debug
```

## 🚨 Emergency Fixes

```bash
# Force kill Reverb
sudo pkill -9 -f reverb

# Restart everything
sudo supervisorctl restart reverb  # OR
sudo systemctl restart reverb
sudo systemctl reload nginx

# Check logs for errors
tail -100 storage/logs/reverb.log
tail -100 storage/logs/laravel.log
```

## 📦 Deployment

```bash
# Update code & restart
cd /var/www/html/dating
git pull
composer install --no-dev --optimize-autoloader
php artisan config:clear
sudo supervisorctl restart reverb  # OR systemctl restart reverb

# Full deployment (automated)
sudo bash deployment/deploy-reverb.sh
```

## ⚙️ Configuration Files

```bash
# Supervisor
/etc/supervisor/conf.d/reverb.conf

# Systemd
/etc/systemd/system/reverb.service

# Nginx
/etc/nginx/sites-available/heartsconnect

# Apache
/etc/apache2/sites-available/heartsconnect-ssl.conf

# Project Files
/var/www/html/dating/.env
/var/www/html/dating/config/reverb.php
/var/www/html/dating/config/broadcasting.php
```

## 🔐 SSL (Let's Encrypt)

```bash
# Install certificate
sudo certbot --nginx -d heartsconnect.site

# Renew certificate
sudo certbot renew

# Test renewal
sudo certbot renew --dry-run

# Check expiry
sudo certbot certificates
```

## 📖 Full Guides

- **Complete Guide:** `REVERB-PRODUCTION-GUIDE.md`
- **Deployment Script:** `deployment/deploy-reverb.sh`
- **Template Configs:** `deployment/` folder

---

**Quick Help:** For detailed troubleshooting, see `REVERB-PRODUCTION-GUIDE.md`
