# AI Domain Finder — Deployment Guide

This guide covers deployment for **Shared Hosting (cPanel / Hostinger / Namecheap / Bluehost)** as well as **VPS / Cloud (DigitalOcean, AWS, Linode, Ubuntu/Nginx)**.

---

## 1. Server Requirements

Ensure your server meets the following minimum requirements:
- **PHP 8.2+** (PHP 8.2 or 8.3 recommended)
- **PHP Extensions**:
  - `pdo_mysql`
  - `curl`
  - `zip`
  - `mbstring`
  - `openssl`
  - `tokenizer`
  - `xml`
  - `ctype`
  - `json`
  - `fileinfo`
- **MySQL 8.0+** or **MariaDB 10.4+**
- **Apache** with `mod_rewrite` enabled or **Nginx**

---

## 2. Deploying on Shared Hosting (cPanel)

### Step 2.1: Prepare & Upload Files
1. Build frontend assets locally (already pre-compiled in `public/build`):
   ```bash
   npm run build
   ```
2. Compress project files into a `.zip` file (exclude `node_modules`, `.git`, and `.env`).
3. In your cPanel **File Manager**:
   - **Direct `public_html` Upload (Easiest)**: Upload all project files directly into `public_html/`. The included root `.htaccess` automatically routes all web traffic into `public/` transparently without `/public` appearing in the URL, while blocking direct access to `.env`, `vendor/`, and `storage/`.
   - **Alternative (Split Folders)**: Place Laravel files in your home directory (e.g. `/home/username/AIDomainFinder/`), and place or symlink the contents of `public/` into `public_html/`.
   - **Alternative (Document Root)**: In cPanel Domains / Addon Domains, set the Document Root directly to `/home/username/public_html/public`.

### Step 2.2: Configure `.env`
Create `.env` in your project root on the server:
```env
APP_NAME="AI Domain Finder"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_cpanel_dbname
DB_USERNAME=your_cpanel_dbuser
DB_PASSWORD=your_cpanel_dbpassword

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database

# Optional security token for web crons
CRON_SECRET=your_random_secret_token_here
```

### Step 2.3: Database & Permissions
Run through cPanel Terminal or SSH:
```bash
# Generate application key if not set
php artisan key:generate

# Run database migrations
php artisan migrate --force

# Seed initial categories & default settings
php artisan db:seed --force

# Ensure storage directory is writable
chmod -R 775 storage bootstrap/cache
```

---

## 3. Automated Cron Jobs on Shared Hosting

You have **two options** depending on whether your host allows CLI cron jobs.

### Option A: cPanel Scheduled Cron Jobs (Recommended)
In **cPanel > Cron Jobs**, set up the following entries:

1. **Laravel Scheduler (Runs daily fetch, Moz metrics, alerts, and 14-day pruning):**
   - **Interval:** Every minute (`* * * * *`)
   - **Command:**
     ```bash
     /usr/local/bin/php /home/username/AIDomainFinder/artisan schedule:run >> /dev/null 2>&1
     ```

2. **Safe Queue Worker (Processes domain ingestion & metrics without getting killed):**
   - **Interval:** Every 5 minutes (`*/5 * * * *`)
   - **Command:**
     ```bash
     /usr/local/bin/php /home/username/AIDomainFinder/artisan queue:work --stop-when-empty --max-time=50 --tries=3 >> /dev/null 2>&1
     ```

*(Replace `/home/username/AIDomainFinder` with your actual path, and `/usr/local/bin/php` with your server's PHP 8.2+ path, such as `/usr/bin/ea-php82` on cPanel).*

---

### Option B: Web URL Crons (External Cron Services like cron-job.org)
If your shared host has disabled command-line cron:

1. **Daily Domain Ingestion:**
   - **URL:** `https://yourdomain.com/cron/update-domains?sync=1&token=your_random_secret_token_here`
   - **Schedule:** Daily at 07:00 UTC
   - **Note:** `sync=1` executes the ingestion directly during the HTTP request without requiring a queue daemon.

2. **Schedule Runner:**
   - **URL:** `https://yourdomain.com/cron/schedule-run?token=your_random_secret_token_here`
   - **Schedule:** Every 1–5 minutes

3. **Background Queue Runner:**
   - **URL:** `https://yourdomain.com/cron/queue-work?token=your_random_secret_token_here`
   - **Schedule:** Every 5 minutes

---

## 4. Production Optimization Commands

Run these on production to dramatically speed up performance:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

*(Note: Whenever you modify `.env` or routes in production, run `php artisan config:clear` and re-cache).*

---

## 5. Domain Sources & API Configuration

Navigate to **Admin > Settings** in the web interface:

| Source | Status | Configuration |
|---|---|---|
| **WhoisFreaks Public GitHub** | Active (Free) | Default feed (~10,000 domains/day). No keys needed. |
| **DomainDataset.com** | Active (Free) | Daily removed ZIP/TXT archives. No keys needed. |
| **Real-Time Availability** | Active (Free) | Uses ICANN RDAP out-of-the-box; upgrades to GoDaddy API when keys are configured. |
| **GoDaddy API** | Optional | Enter API Key & Secret from [developer.godaddy.com](https://developer.godaddy.com/keys). |
| **Moz API** | Optional | Enter Access ID & Secret Key from [moz.com/products/api](https://moz.com/products/api) for DA/Spam scores. |
| **WhoisFreaks Paid API** | Optional | For 400,000+ daily drop lists. |
| **Custom Drop List Feed** | Optional | Enter any plain-text drop list URL or mirror. |
