# Hostinger Deployment Guide - Islamabad Bullion Exchange

## Step 1: Create MySQL Database on Hostinger

1. Go to https://hpanel.hostinger.com/websites/islamabadbullionexchange.com
2. Navigate to **Databases → MySQL Databases**
3. Create a new database:
   - Database name: `islamabad_bullion`
   - Username: `islamabad_user`
   - Password: (set a strong password, save it)
4. Note down: DB name, DB username, DB password, DB host (usually `localhost` or shown in panel)

## Step 2: Enable SSH Access

1. In hPanel → **Advanced → SSH Access**
2. Enable SSH and note the SSH credentials (host, port, username)
3. Connect via terminal: `ssh username@host -p port`

## Step 3: Upload Code via Git

SSH into your server, then:

```bash
cd ~
git clone https://github.com/sheikhshahzaman/gold-silver.git gold-website
cd gold-website
```

## Step 4: Install Dependencies

```bash
cd ~/gold-website
composer install --no-dev --optimize-autoloader
```

(Node/npm is NOT needed on server - assets are pre-built)

## Step 5: Configure Environment

```bash
cp .env.example .env
php artisan key:generate
```

Then edit `.env` with your database credentials:
```bash
nano .env
```

Set these values:
```
APP_NAME="Islamabad Bullion Exchange"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://islamabadbullionexchange.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_actual_db_name
DB_USERNAME=your_actual_db_user
DB_PASSWORD=your_actual_db_password
```

## Step 6: Set Up public_html

The key trick for Laravel on Hostinger - link `public_html` to Laravel's `public/` folder:

```bash
# Backup existing public_html
mv ~/public_html ~/public_html_backup

# Create symlink
ln -s ~/gold-website/public ~/public_html
```

## Step 7: Run Migrations & Seeders

```bash
cd ~/gold-website
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
```

## Step 8: Set Permissions

```bash
chmod -R 775 ~/gold-website/storage
chmod -R 775 ~/gold-website/bootstrap/cache
```

## Step 9: Optimize for Production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan filament:cache-components
```

## Step 10: Set Up Cron Job for Auto Price Updates

1. In hPanel → **Advanced → Cron Jobs**
2. Add a new cron job:
   - Command: `cd ~/gold-website && php artisan schedule:run >> /dev/null 2>&1`
   - Interval: **Every minute** — the schedule expression must be `* * * * *`. The scheduler in `routes/console.php` dispatches `prices:fetch` every minute with a 55-second overlap lock, so anything slower defeats the real-time feel.

## Step 11: Set Up Laravel Scheduler

The price fetcher runs via Laravel's scheduler. Make sure `routes/console.php` has the schedule configured. If not, the cron command can directly call:

```
cd ~/gold-website && php artisan prices:fetch >> /dev/null 2>&1
```
Set this to run every minute (`* * * * *`).

## Step 12: Force HTTPS (if not automatic)

Add to `public/.htaccess` (before existing rules):
```apache
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

## Troubleshooting

- **500 Error**: Check `storage/logs/laravel.log` and ensure permissions are correct
- **Blank page**: Run `php artisan config:clear` and check `.env`
- **Assets not loading**: Ensure `public/build/` directory exists with compiled assets
- **Images not showing**: Run `php artisan storage:link`

## Admin Access

- URL: https://islamabadbullionexchange.com/admin
- Email: admin@islamabadbullionexchange.com
- Password: password (CHANGE THIS IMMEDIATELY after first login)
