# cPanel Deployment Guide

## 1. Generate the production zip

Run this once from the project root on your Windows machine:

```powershell
powershell -ExecutionPolicy Bypass -File build-cpanel.ps1
```

This will:
- Build front-end assets (`npm run build`)
- Clear all Laravel caches
- Create `dating-production.zip` (excludes `node_modules`, `.git`, logs, sessions)

---

## 2. Upload & Extract on cPanel

1. Log into **cPanel → File Manager**
2. Navigate to your home directory (e.g. `/home/yourusername/`)
3. Upload `dating-production.zip`
4. Right-click → **Extract** — it creates a `dating/` folder

---

## 3. Point the domain's Document Root

**Option A – Subdomain/add-on domain (recommended)**  
In cPanel → Domains → point the document root to:
```
/home/yourusername/dating/public
```

**Option B – Main domain (public_html)**  
Copy everything inside `dating/public/` into `public_html/`, then edit `public_html/index.php`:

```php
require __DIR__.'/../dating/vendor/autoload.php';
$app = require_once __DIR__.'/../dating/bootstrap/app.php';
```

---

## 4. Configure `.env`

```bash
cp .env.example .env
```
Edit `.env` — minimum required values:
```env
APP_NAME="HeartsConnect"
APP_ENV=production          # MUST be "production" — hides dev tools, enables optimisations
APP_KEY=                    # generated in step 5 — leave blank; key:generate will fill it
APP_DEBUG=false             # MUST be false in production — never expose stack traces
APP_URL=https://heartsconnect.site    # ← exact public URL, no trailing slash
ASSET_URL=https://heartsconnect.site  # ← same as APP_URL

DB_CONNECTION=mysql
DB_HOST=localhost           # cPanel MySQL is always localhost
DB_PORT=3306
DB_DATABASE=heartsco_bd     # cPanel format: cPanelUser_dbname
DB_USERNAME=heartsco_user   # cPanel format: cPanelUser_dbuser
DB_PASSWORD=your_db_password

MAIL_MAILER=smtp
MAIL_HOST=mail.heartsconnect.site
MAIL_PORT=465
MAIL_USERNAME=noreply@heartsconnect.site
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=noreply@heartsconnect.site
MAIL_FROM_NAME="HeartsConnect"

FILESYSTEM_DISK=public
SESSION_DRIVER=file
SESSION_DOMAIN=heartsconnect.site   # ← REQUIRED: must match your domain to avoid 419 errors
SESSION_SECURE_COOKIE=true          # ← REQUIRED on HTTPS: cookies only sent over SSL
SESSION_SAME_SITE=lax
CACHE_STORE=file
QUEUE_CONNECTION=sync
BROADCAST_CONNECTION=reverb         # or pusher if using Pusher
```

> **Why 419 "Page Expired" happens:** If `APP_URL` is wrong (e.g. still `localhost`) or
> `SESSION_DOMAIN` is blank, the session cookie is stored under the wrong host.
> When the login form POSTs, the browser cannot send back the cookie, so Laravel cannot
> verify the CSRF token → 419.  Setting `APP_URL`, `SESSION_DOMAIN`, and
> `SESSION_SECURE_COOKIE=true` corrects this.

> **Dev quick-login buttons on the login page** are only shown when `APP_ENV=local`.
> Setting `APP_ENV=production` hides them automatically — no code change needed.

---

## 5. Run artisan commands via cPanel Terminal (or SSH)

```bash
cd /home/yourusername/dating

php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan optimize:clear
```

If cPanel has no terminal, use **phpMyAdmin** to import your migrations manually,
or enable SSH access in cPanel security settings.

---

## 6. File permissions

```bash
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;
chmod -R 775 storage bootstrap/cache
```

---

## 7. Cron job (optional – for premium expiry, etc.)

In cPanel → Cron Jobs, add:
```
* * * * * /usr/local/bin/php /home/yourusername/dating/artisan schedule:run >> /dev/null 2>&1
```

---

## 8. Troubleshooting

| Symptom | Fix |
|---|---|
| 500 error on all pages | Check `storage/logs/laravel.log`; ensure `APP_KEY` is set |
| White screen / blank | Set `APP_DEBUG=true` temporarily to see errors |
| Images/uploads not showing | Run `php artisan storage:link` |
| Mail not sending | Check SMTP credentials; use `MAIL_MAILER=log` to test without real email |
| CSS/JS 404 | Ensure `public/build/` was included in the zip and `APP_URL` is correct |
| **419 Page Expired** | Set `APP_URL=https://yourdomain.com`, `SESSION_DOMAIN=yourdomain.com`, `SESSION_SECURE_COOKIE=true` in `.env`, then `php artisan optimize:clear` |
| Session errors | Ensure `storage/framework/sessions/` is writable (chmod 775) |
| **"Please provide a valid cache path"** | Storage directories missing — run the commands in section 9 below |

---

## 9. Fix: "Please provide a valid cache path" (missing storage dirs)

This happens when the ZIP did not include the required writable directories.
Run these commands via **cPanel Terminal** or **SSH**:

```bash
cd /home/heartsco/public_html

# Create all required writable directories
mkdir -p storage/framework/views \
         storage/framework/sessions \
         storage/framework/cache/data \
         storage/app/public \
         storage/logs \
         bootstrap/cache

# Set correct permissions
chmod -R 775 storage bootstrap/cache

# Clear and rebuild all caches
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

After running these commands, reload the site — the error will be gone.
