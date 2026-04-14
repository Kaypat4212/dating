# Laravel Reverb Production Deployment Guide

Complete guide for deploying Laravel Reverb WebSocket server in production environments.

## 📋 Table of Contents

1. [Prerequisites](#prerequisites)
2. [Environment Configuration](#environment-configuration)
3. [Supervisor Setup (Recommended)](#supervisor-setup-recommended)
4. [Systemd Service (Alternative)](#systemd-service-alternative)
5. [Nginx SSL Proxy](#nginx-ssl-proxy)
6. [Apache SSL Proxy](#apache-ssl-proxy)
7. [Testing & Verification](#testing--verification)
8. [Troubleshooting](#troubleshooting)
9. [Production Checklist](#production-checklist)

---

## 🔧 Prerequisites

- Linux server (Ubuntu 20.04+, CentOS 7+, or similar)
- PHP 8.2+ with CLI
- Nginx or Apache with mod_proxy_wstunnel
- SSL certificate (Let's Encrypt recommended)
- Supervisor or systemd for process management
- Root/sudo access for initial setup

---

## ⚙️ Environment Configuration

### 1. Update Your `.env` File

Add/update these Reverb settings in your production `.env`:

```env
# Broadcasting Driver
BROADCAST_CONNECTION=reverb

# Reverb Server Configuration (PRODUCTION)
REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret

# Server Settings (Internal - DO NOT change unless you know what you're doing)
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8080
REVERB_SERVER_PATH=/

# Client Connection Settings (PUBLIC - used by browser JavaScript)
REVERB_HOST=your-domain.com
REVERB_PORT=443
REVERB_SCHEME=https

# Performance Tuning
REVERB_APP_PING_INTERVAL=60
REVERB_APP_ACTIVITY_TIMEOUT=30
REVERB_APP_MAX_CONNECTIONS=1000
REVERB_APP_MAX_MESSAGE_SIZE=10000

# Scaling (Redis required)
REVERB_SCALING_ENABLED=false
REVERB_SCALING_CHANNEL=reverb

# Redis Configuration (if scaling enabled)
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null
REDIS_DB=0
```

### 2. Generate Reverb Credentials (First Time Only)

If you haven't set up Reverb credentials yet:

```bash
# These will be auto-generated and added to .env
php artisan reverb:install
```

Or manually set secure random values:

```bash
# Generate random credentials
REVERB_APP_ID=$(openssl rand -hex 16)
REVERB_APP_KEY=$(openssl rand -hex 32)
REVERB_APP_SECRET=$(openssl rand -hex 32)

# Add to .env
echo "REVERB_APP_ID=$REVERB_APP_ID" >> .env
echo "REVERB_APP_KEY=$REVERB_APP_KEY" >> .env
echo "REVERB_APP_SECRET=$REVERB_APP_SECRET" >> .env
```

### 3. Important Settings Explained

| Setting | Purpose | Example |
|---------|---------|---------|
| `REVERB_SERVER_HOST` | Internal binding (use 0.0.0.0 for all interfaces) | `0.0.0.0` |
| `REVERB_SERVER_PORT` | Internal port Reverb listens on | `8080` |
| `REVERB_HOST` | **PUBLIC** domain users connect to | `heartsconnect.site` |
| `REVERB_PORT` | **PUBLIC** port (443 for SSL, 80 for non-SSL) | `443` |
| `REVERB_SCHEME` | Protocol (`https` in production) | `https` |

⚠️ **Common Mistake:** Using `localhost` or `127.0.0.1` for `REVERB_HOST` — this won't work for remote clients!

---

## 🔄 Supervisor Setup (Recommended)

Supervisor ensures Reverb stays running and auto-restarts on crashes.

### 1. Install Supervisor

```bash
# Ubuntu/Debian
sudo apt-get update
sudo apt-get install supervisor

# CentOS/RHEL
sudo yum install supervisor
sudo systemctl enable supervisord
sudo systemctl start supervisord
```

### 2. Create Supervisor Config

Create `/etc/supervisor/conf.d/reverb.conf`:

```ini
[program:reverb]
process_name=%(program_name)s
command=php /var/www/html/dating/artisan reverb:start
directory=/var/www/html/dating
user=www-data
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
redirect_stderr=true
stdout_logfile=/var/www/html/dating/storage/logs/reverb.log
stdout_logfile_maxbytes=50MB
stdout_logfile_backups=10
startsecs=5
startretries=10
```

**Important:** Update these paths to match your setup:
- `/var/www/html/dating` → your actual project path
- `www-data` → your web server user (might be `apache`, `nginx`, or your username)

### 3. Start Reverb via Supervisor

```bash
# Reload supervisor configuration
sudo supervisorctl reread
sudo supervisorctl update

# Start Reverb
sudo supervisorctl start reverb

# Check status
sudo supervisorctl status reverb
```

### 4. Useful Supervisor Commands

```bash
# Start/stop/restart
sudo supervisorctl start reverb
sudo supervisorctl stop reverb
sudo supervisorctl restart reverb

# View real-time logs
sudo supervisorctl tail -f reverb

# View all processes
sudo supervisorctl status

# Reload supervisor after config changes
sudo supervisorctl reread
sudo supervisorctl update
```

---

## 🐧 Systemd Service (Alternative)

If you prefer systemd over Supervisor (modern Linux distributions).

### 1. Create Systemd Service

Create `/etc/systemd/system/reverb.service`:

```ini
[Unit]
Description=Laravel Reverb WebSocket Server
After=network.target mysql.service redis.service

[Service]
Type=simple
User=www-data
Group=www-data
Restart=always
RestartSec=5
ExecStart=/usr/bin/php /var/www/html/dating/artisan reverb:start
WorkingDirectory=/var/www/html/dating
StandardOutput=append:/var/www/html/dating/storage/logs/reverb.log
StandardError=append:/var/www/html/dating/storage/logs/reverb-error.log
SyslogIdentifier=reverb

# Security
PrivateTmp=true
NoNewPrivileges=true

[Install]
WantedBy=multi-user.target
```

**Important:** Update paths and user/group to match your setup.

### 2. Enable and Start Service

```bash
# Reload systemd
sudo systemctl daemon-reload

# Enable auto-start on boot
sudo systemctl enable reverb

# Start the service
sudo systemctl start reverb

# Check status
sudo systemctl status reverb
```

### 3. Useful Systemd Commands

```bash
# Start/stop/restart
sudo systemctl start reverb
sudo systemctl stop reverb
sudo systemctl restart reverb

# View logs
sudo journalctl -u reverb -f

# View recent logs
sudo journalctl -u reverb -n 100

# Check if enabled
sudo systemctl is-enabled reverb
```

---

## 🔒 Nginx SSL Proxy

Configure Nginx to proxy WebSocket connections to Reverb with SSL.

### 1. Install SSL Certificate (Let's Encrypt)

```bash
# Install certbot
sudo apt-get install certbot python3-certbot-nginx

# Get certificate for your domain
sudo certbot --nginx -d your-domain.com -d www.your-domain.com
```

### 2. Nginx Reverb Location Block

Add this to your Nginx site config (e.g., `/etc/nginx/sites-available/your-site`):

```nginx
# WebSocket Reverb Proxy
location /app {
    proxy_pass http://127.0.0.1:8080;
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "upgrade";
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto $scheme;
    proxy_read_timeout 86400;
    proxy_connect_timeout 60s;
    proxy_send_timeout 60s;
}
```

### 3. Complete Nginx Config Example

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name heartsconnect.site www.heartsconnect.site;
    
    # Redirect HTTP to HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name heartsconnect.site www.heartsconnect.site;

    root /var/www/html/dating/public;
    index index.php index.html;

    # SSL Configuration (managed by certbot)
    ssl_certificate /etc/letsencrypt/live/heartsconnect.site/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/heartsconnect.site/privkey.pem;
    include /etc/letsencrypt/options-ssl-nginx.conf;
    ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem;

    # Laravel WebSocket (Reverb) Proxy
    location /app {
        proxy_pass http://127.0.0.1:8080;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_read_timeout 86400;
        proxy_connect_timeout 60s;
        proxy_send_timeout 60s;
    }

    # Laravel Application
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 300;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 4. Test and Reload Nginx

```bash
# Test configuration
sudo nginx -t

# Reload if OK
sudo systemctl reload nginx
```

---

## 🔧 Apache SSL Proxy

If using Apache instead of Nginx.

### 1. Enable Required Modules

```bash
sudo a2enmod proxy
sudo a2enmod proxy_http
sudo a2enmod proxy_wstunnel
sudo a2enmod ssl
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### 2. Apache VirtualHost Config

Add to your Apache site config (e.g., `/etc/apache2/sites-available/your-site-ssl.conf`):

```apache
<VirtualHost *:443>
    ServerName heartsconnect.site
    ServerAlias www.heartsconnect.site
    
    DocumentRoot /var/www/html/dating/public
    
    # SSL Configuration
    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/heartsconnect.site/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/heartsconnect.site/privkey.pem
    Include /etc/letsencrypt/options-ssl-apache.conf
    
    # WebSocket Reverb Proxy
    ProxyPreserveHost On
    ProxyPass /app ws://127.0.0.1:8080/app retry=0 timeout=300
    ProxyPassReverse /app ws://127.0.0.1:8080/app
    
    <Directory /var/www/html/dating/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/dating-error.log
    CustomLog ${APACHE_LOG_DIR}/dating-access.log combined
</VirtualHost>
```

### 3. Test and Reload Apache

```bash
# Test configuration
sudo apache2ctl configtest

# Reload if OK
sudo systemctl reload apache2
```

---

## ✅ Testing & Verification

### 1. Check Reverb is Running

```bash
# Via Supervisor
sudo supervisorctl status reverb

# Via Systemd
sudo systemctl status reverb

# Via process list
ps aux | grep reverb
```

Expected output: Process should be running.

### 2. Check Port is Listening

```bash
sudo netstat -tlnp | grep 8080
```

Expected: `tcp 0 0 0.0.0.0:8080 0.0.0.0:* LISTEN 12345/php`

### 3. Test WebSocket Connection

```bash
# Install websocat
curl -L https://github.com/vi/websocat/releases/download/v1.11.0/websocat.x86_64-unknown-linux-musl -o websocat
chmod +x websocat
sudo mv websocat /usr/local/bin/

# Test internal connection
websocat ws://127.0.0.1:8080/app/your-app-key
```

### 4. Browser Console Test

Open your browser console (F12) on your site and run:

```javascript
// Should see connection attempt
console.log(window.Echo);

// Test connection
Echo.connector.pusher.connection.bind('connected', function() {
    console.log('✅ Connected to Reverb!');
});

Echo.connector.pusher.connection.bind('error', function(err) {
    console.error('❌ Reverb error:', err);
});
```

### 5. Check Laravel Logs

```bash
tail -f storage/logs/laravel.log
tail -f storage/logs/reverb.log
```

---

## 🐛 Troubleshooting

### Issue: "Connection refused" in browser

**Causes:**
- Reverb not running
- Firewall blocking port
- Wrong `REVERB_HOST` in .env
- Nginx/Apache proxy not configured

**Solutions:**
```bash
# Check if running
sudo supervisorctl status reverb

# Check firewall (if using firewalld)
sudo firewall-cmd --list-all

# Allow port 8080 internally (not public)
# Reverb should ONLY be accessed via Nginx/Apache proxy

# Verify .env
php artisan config:clear
grep REVERB .env
```

### Issue: "Mixed content" error (http/https)

**Cause:** `REVERB_SCHEME` is `http` instead of `https`

**Solution:**
```env
REVERB_SCHEME=https
REVERB_PORT=443
```

Then:
```bash
php artisan config:clear
sudo supervisorctl restart reverb
```

### Issue: Reverb crashes/stops randomly

**Cause:** Memory limit, crashes, or uncaught exceptions

**Solutions:**

1. **Check logs:**
```bash
sudo supervisorctl tail -f reverb
tail -f storage/logs/reverb.log
```

2. **Increase PHP memory:**
```bash
# Edit php.ini
sudo nano /etc/php/8.2/cli/php.ini

# Set:
memory_limit = 512M
```

3. **Supervisor auto-restart configured** (should already be in place)

### Issue: "WebSocket connection to 'wss://...' failed"

**Cause:** Nginx/Apache proxy not forwarding WebSocket upgrade properly

**Solution for Nginx:**
```nginx
# Ensure these are in your location block:
proxy_http_version 1.1;
proxy_set_header Upgrade $http_upgrade;
proxy_set_header Connection "upgrade";
```

**Solution for Apache:**
```apache
# Ensure mod_proxy_wstunnel is enabled
sudo a2enmod proxy_wstunnel
sudo systemctl restart apache2
```

### Issue: "Origin not allowed"

**Cause:** CORS/origin restriction

**Solution:**

Edit `config/reverb.php`:
```php
'allowed_origins' => ['*'], // Allow all origins (development)
// OR for production:
'allowed_origins' => ['https://heartsconnect.site', 'https://www.heartsconnect.site'],
```

### Issue: High CPU/Memory usage

**Solutions:**

1. **Limit connections:**
```env
REVERB_APP_MAX_CONNECTIONS=500
```

2. **Enable Redis scaling:**
```env
REVERB_SCALING_ENABLED=true
REDIS_HOST=127.0.0.1
```

3. **Monitor:**
```bash
# Install htop
sudo apt-get install htop
htop -p $(pgrep -f reverb)
```

---

## 📋 Production Checklist

Before going live, verify:

- [ ] **SSL certificate installed** (Let's Encrypt or commercial)
- [ ] **Reverb credentials generated** and set in `.env`
- [ ] **REVERB_HOST set to public domain** (not localhost)
- [ ] **REVERB_SCHEME=https** and **REVERB_PORT=443**
- [ ] **BROADCAST_CONNECTION=reverb** in `.env`
- [ ] **Supervisor or systemd configured** for auto-restart
- [ ] **Nginx/Apache proxy configured** with SSL
- [ ] **Firewall allows HTTPS traffic** (port 443)
- [ ] **Port 8080 NOT publicly exposed** (only accessible via proxy)
- [ ] **Laravel Echo configured** in `resources/js/bootstrap.js`
- [ ] **Browser test passed** (WebSocket connection successful)
- [ ] **Logs directory writable** (`storage/logs/`)
- [ ] **Config cache cleared** (`php artisan config:clear`)
- [ ] **`php artisan reverb:start` works** when run manually
- [ ] **Reverb appears in process list** (`ps aux | grep reverb`)

---

## 🚀 Quick Start Commands

### Development (Local)
```bash
# Start manually
php artisan reverb:start --debug

# Or via script
bash start-reverb.sh
```

### Production (Server)
```bash
# Initial setup
composer install --no-dev --optimize-autoloader
php artisan config:clear
php artisan cache:clear
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start reverb

# Deploy updates
git pull
composer install --no-dev
php artisan config:clear
sudo supervisorctl restart reverb
```

---

## 📚 Additional Resources

- **Laravel Reverb Docs:** https://laravel.com/docs/11.x/reverb
- **Supervisor Docs:** http://supervisord.org/
- **Let's Encrypt:** https://letsencrypt.org/
- **WebSocket Testing:** https://www.websocket.org/echo.html

---

## 🆘 Getting Help

If issues persist:

1. **Check logs:** `storage/logs/reverb.log` and `storage/logs/laravel.log`
2. **Enable debug mode:** `php artisan reverb:start --debug`
3. **Verify .env:** Ensure all `REVERB_*` variables are correct
4. **Test manually:** Run `php artisan reverb:start` and watch for errors
5. **Check process:** `ps aux | grep reverb` to confirm it's running

---

**Last Updated:** April 2026  
**Version:** 1.0  
**Project:** Hearts Connect Dating Platform
