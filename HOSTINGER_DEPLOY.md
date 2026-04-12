# Hostinger Deployment Guide — Islamabad Bullion Exchange

Complete, step-by-step guide to deploy this Laravel + Livewire + Filament
application to Hostinger shared hosting for the domain
**islamabadbullionexchange.com**.

This document is written so that a collaborator (human or AI) who has never
seen this project before can follow it top-to-bottom and end up with a
working production site. Every command is copy-pasteable; every value that
needs to be replaced is clearly marked with `<ANGLE BRACKETS>`.

---

## Handoff note — for the Claude collaborator running this deploy

Hello. The project owner is handing this runbook to you because they want
**islamabadbullionexchange.com** live on their Hostinger shared hosting
account, powered by this repository. You are expected to do the full
deployment on their behalf — they will supply credentials and the hPanel
account, and you will execute the steps below.

**Repo**: https://github.com/sheikhshahzaman/gold-silver
**Branch**: `main` (always the latest deploy-ready commit)
**hPanel URL**: https://hpanel.hostinger.com/websites/islamabadbullionexchange.com
**End state**: https://islamabadbullionexchange.com is live, the homepage
renders real prices with a 3-second poll, `/admin` logs in, and the
1-minute price-fetch cron is ticking.

### What to ask the project owner before you start

Collect all of these in a single message so you don't have to keep
pausing the deploy to wait for answers:

1. Hostinger **SSH host**, **port**, **username**, and **password**
   (they can find/reset these in hPanel → Advanced → SSH Access).
2. MySQL **database name**, **username**, **password**, and **host** — or
   confirmation that you should walk them through creating a new database
   in Section 2 of this doc.
3. Whether they want to keep the default seeded admin account
   (`admin@islamabadbullionexchange.com` / `password`, which you will
   force them to change after first login) or whether they want a
   specific admin email set up now.
4. Confirmation that `islamabadbullionexchange.com` is already pointed at
   their Hostinger account (they can verify on the hPanel dashboard).
5. Whether they have a preferred PHP version override already applied,
   or whether you should set it to PHP 8.3 in Section 4.

### What you do not need

No Node.js on the server. No rsync, scp, or File Manager uploads. No
local npm runs. The pre-built Vite assets (`public/build/`) ship through
the git clone directly, so `git clone` + `composer install` gives the
server everything it needs to serve the site.

### Ground rules

- **Work through the numbered sections in order.** They are ordered so
  that each step's prerequisites are satisfied by the steps above it.
  Do not skip or reorder.
- **Every command is copy-pasteable.** Replace `<PLACEHOLDERS>` with the
  real values the project owner gave you. Do not guess values.
- **If a command fails**, check Section 19 (Troubleshooting) before
  improvising. If the error isn't covered there, paste the exact output
  to the project owner and ask for direction — do not attempt to
  "fix it by trial" on a production system.
- **Do not commit or push anything from the server.** The server is a
  pull-only consumer of `main`. All git write operations stay on the
  project owner's local machine. If you find yourself wanting to `git
  commit` on Hostinger, stop and ask first.
- **Never `git push --force`.** Ever. For any reason.
- **Do not re-run seeders on an already-live database.** `db:seed --force`
  is listed in Section 10 for the initial deploy only; running it a
  second time will duplicate pages, ticker items, and payment settings.
  Migrations (`migrate --force`) are safe to re-run; seeders are not.
- **If you change `.env`**, tell the project owner exactly which keys
  you modified so they know what changed.
- **After Section 17 end-to-end verification passes**, report back to the
  project owner with the live URL and a quick summary of what you did.
  If any verification check failed, do not claim success — stop and ask.

If you encounter anything this document doesn't cover, or a situation
that feels destructive or irreversible, stop and ask the project owner
before proceeding. Shared hosting is forgiving of mistakes that only
affect the filesystem and are under `~/gold-website/`, but destructive
MySQL operations and permissions changes outside the home directory can
break the whole account.

---

## 0. What you are deploying

- **Stack**: PHP 8.3+, Laravel 13, Livewire 4, Filament 5, Tailwind 4, Vite 8
- **Database**: MySQL (Hostinger-provided)
- **Frontend build**: Vite (assets compiled into `public/build/`)
- **Price source**: scrapes pakgold.net every minute via a cron-driven
  Laravel scheduler
- **Admin panel**: Filament at `/admin`
- **Public site**: live gold/silver/currency rates at `/`

The dashboard refreshes every 3 seconds on the client and every 1 minute
on the server. Both cadences are critical — **missing the 1-minute cron
will silently turn the site into a stale rate board**.

---

## 1. Prerequisites (before you start)

Gather these things on a sticky note — you will need them in several steps:

| Item                       | Where to find it                           | Example                         |
|----------------------------|--------------------------------------------|---------------------------------|
| hPanel login               | https://hpanel.hostinger.com               | email + password                |
| Domain                     | already set up on the plan                 | `islamabadbullionexchange.com`  |
| Hostinger SSH host         | hPanel → Advanced → SSH Access             | `145.223.xx.xx`                 |
| Hostinger SSH port         | same panel                                 | `65002`                         |
| Hostinger SSH username     | same panel                                 | `u123456789`                    |
| Hostinger SSH password     | same panel (you set it)                    | *********                       |
| MySQL DB name              | hPanel → Databases → MySQL                 | `u123456789_ibe`                |
| MySQL DB username          | same panel                                 | `u123456789_ibe`                |
| MySQL DB password          | same panel (you set it)                    | *********                       |
| MySQL DB host              | same panel                                 | `localhost`                     |
| GitHub repo URL            | https://github.com/sheikhshahzaman/gold-silver | https://github.com/sheikhshahzaman/gold-silver.git |

You also need a **local machine** with Node.js 20+ and npm installed. You
will build the frontend assets locally and upload them — Hostinger shared
hosting does not have Node.js available for arbitrary builds.

> **PHP version check**: Hostinger's default PHP may be 8.1 or 8.2.
> This project requires **PHP 8.3 or higher**. Set it in
> hPanel → Advanced → PHP Configuration → PHP version **before** running
> any `php artisan` or `composer` command.

---

## 2. Create the MySQL database

1. Open https://hpanel.hostinger.com/websites/islamabadbullionexchange.com
2. Sidebar → **Databases → Management**
3. Click **Create a new database**
4. Fill in:
   - Database name: `ibe` (Hostinger will prefix it with your account ID,
     so the full name becomes something like `u123456789_ibe` — write this
     down)
   - Username: `ibe` (likewise prefixed)
   - Password: generate a strong one and save it
5. After creation, on the same page confirm:
   - **DB host** (usually `localhost` on shared plans)
   - Full **DB name** (with the `u123456789_` prefix)
   - Full **DB username** (with the `u123456789_` prefix)
   - **DB password**
6. Keep this tab open — you will paste these values into `.env` shortly.

---

## 3. Enable SSH access

1. hPanel → **Advanced → SSH Access**
2. Toggle SSH **on** if it is off
3. Note down the **IP address**, **port**, and **username** shown
4. Click **Change SSH password** and set one you will remember
5. Test connection from your local machine:
   ```bash
   ssh -p <PORT> <USERNAME>@<IP>
   ```
   Accept the host fingerprint. You should land at a shell prompt like
   `u123456789@srv1234:~$`.

> If `ssh` refuses with "Connection refused" or "host is down", wait 60
> seconds — Hostinger's SSH activation is not instant — and retry. If it
> still fails, your plan tier may not include SSH; contact Hostinger
> support.

---

## 4. Set PHP version to 8.3+ (CRITICAL)

Before anything else, make sure the PHP version is 8.3 or higher, because
Composer will refuse to install dependencies otherwise.

1. hPanel → **Advanced → PHP Configuration**
2. **PHP version** tab → select **PHP 8.3** (or newer)
3. Click **Save**
4. In the **PHP options** tab, set:
   - `memory_limit` = `512M` (Composer installs need this)
   - `max_execution_time` = `300`
   - `upload_max_filesize` = `32M`
   - `post_max_size` = `32M`
5. Click **Save**
6. Verify from SSH:
   ```bash
   php -v
   ```
   Should print `PHP 8.3.x` or newer.

---

## 5. Clone the repository

SSH into the server (if you aren't already):

```bash
ssh -p <PORT> <USERNAME>@<IP>
```

Clone into your home directory, **not** into `public_html`:

```bash
cd ~
git clone https://github.com/sheikhshahzaman/gold-silver.git gold-website
cd gold-website
git status                       # should say: clean, on branch main
git log --oneline -5              # should show the 3 deployment commits
```

You should see these 3 recent commits near the top of `git log`:

```
e3074c5 Make dashboard feel real-time: 1-min fetch, 3s poll, flash + ticker arrows
07d1bb7 Replace fake intl BID/ASK with admin-configurable spread; drop dead 10_tola
1ebba4e Fix browser tab titles and rebrand leaks to Islamabad Bullion Exchange
```

If not, you are on an old branch — `git checkout main && git pull`.

---

## 6. Install PHP dependencies (Composer)

Still SSHed into the server, from `~/gold-website`:

```bash
composer install --no-dev --optimize-autoloader --no-interaction
```

What this does:
- Installs packages listed in `composer.json` into `vendor/`
- Skips `require-dev` (no PHPUnit, no Pint, etc. in production)
- Dumps an optimized autoloader for better runtime performance

If Composer errors out with "memory limit exceeded", run:
```bash
php -d memory_limit=-1 /usr/local/bin/composer install --no-dev --optimize-autoloader --no-interaction
```

If `composer` is not found, Hostinger provides it at `/usr/local/bin/composer`
or you may need to install it:
```bash
cd ~
curl -sS https://getcomposer.org/installer | php
mv composer.phar ~/bin/composer   # adjust if ~/bin isn't on PATH
```

---

## 7. Verify the frontend assets came through git

Hostinger shared hosting does not have Node.js, so this repo **commits the
pre-built Vite output** (`public/build/`) directly to git. That means the
`git clone` you just ran already pulled the CSS/JS bundles. No separate
build or upload step is required on the server.

Verify:

```bash
cd ~/gold-website
ls public/build/
# must show: assets/  manifest.json

ls public/build/assets/
# must show something like: app-<HASH>.css  app-<HASH>.js
```

If `public/build/` is missing or empty, the clone failed or you are on an
old branch. Run `git checkout main && git pull origin main` and check
again.

> **Developer note**: when making future changes to `resources/css/**`,
> `resources/js/**`, or any blade/Tailwind-class edit that changes the
> generated CSS bundle, run `npm run build` on your local machine
> **before committing**. The CI-free flow is: `npm run build && git add
> public/build && git commit`. Otherwise the deployed assets will lag
> behind the code.

---

## 8. Configure the environment (.env)

Back on the server, in `~/gold-website`:

```bash
cp .env.example .env
php artisan key:generate --force
```

`key:generate` sets `APP_KEY=base64:...` in `.env`. Now edit the rest.
Use `nano` (Hostinger has it) or any editor you prefer:

```bash
nano .env
```

Fill in the values you collected in steps 1 and 2. The important ones:

```ini
APP_NAME="Islamabad Bullion Exchange"
APP_ENV=production
APP_KEY=                                  # already set by key:generate — leave alone
APP_DEBUG=false                           # MUST be false in production
APP_URL=https://islamabadbullionexchange.com

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error                           # production = errors only

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=<FULL_DB_NAME_FROM_HPANEL>    # e.g. u123456789_ibe
DB_USERNAME=<FULL_DB_USERNAME_FROM_HPANEL>
DB_PASSWORD=<DB_PASSWORD>

SESSION_DRIVER=database
SESSION_LIFETIME=120

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database                      # uses the cache DB table — keep this

MAIL_MAILER=log                           # change later if you wire up SMTP
MAIL_FROM_ADDRESS="info@islamabadbullionexchange.com"
MAIL_FROM_NAME="${APP_NAME}"

VITE_APP_NAME="${APP_NAME}"

# Price source URLs (upstream data providers — do not change without reason)
PAKGOLD_URL=https://www.pakgold.net/
GOLD_API_URL=https://api.gold-api.com/price
EXCHANGE_RATE_URL=https://open.er-api.com/v6/latest/USD
FAWAZ_CURRENCY_URL=https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies
```

> **Legacy env vars you can ignore**: `.env.example` has
> `PRICE_FETCH_INTERVAL=5` and `PRICE_CACHE_TTL=300` but nothing in the
> codebase reads those anymore. The fetch interval is controlled by
> `routes/console.php` (1 minute) and the cache TTL is hardcoded to 900s
> in `app/Services/PriceEngine/PriceCacheManager.php`. Leave the variables
> if you like — or remove them — they have no effect either way.

Save and exit (`Ctrl+O`, `Enter`, `Ctrl+X` in nano).

---

## 9. Point `public_html` at Laravel's `public/`

Hostinger's web server serves files out of `~/public_html` (on most
shared plans) or `~/domains/<domain>/public_html` (on multi-domain plans).
Laravel's web root lives inside the project at `~/gold-website/public/`.
We need to bridge the two with a symlink.

**First, figure out which layout your account uses:**

```bash
ls ~                          # do you see public_html OR domains/?
ls ~/domains 2>/dev/null       # on business plans this shows domain dirs
```

### Case A: single-domain layout (`~/public_html`)

```bash
# Backup whatever is in there (Hostinger's default placeholder page)
mv ~/public_html ~/public_html_backup_$(date +%Y%m%d)

# Symlink to Laravel's public/
ln -s ~/gold-website/public ~/public_html

# Verify
ls -la ~/public_html
# should show: lrwxrwxrwx ... public_html -> /home/u.../gold-website/public
```

### Case B: multi-domain layout (`~/domains/islamabadbullionexchange.com/public_html`)

```bash
cd ~/domains/islamabadbullionexchange.com
mv public_html public_html_backup_$(date +%Y%m%d)
ln -s ~/gold-website/public public_html
ls -la
```

### Alternative: `.htaccess` redirect (if symlinks are disabled)

Some Hostinger plans block `ln -s`. In that case, create
`~/public_html/.htaccess` (or the multi-domain equivalent) with:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ /gold-website/public/$1 [L]
</IfModule>
```

…and move or symlink `~/gold-website` into the doc root instead. The
symlink method is strongly preferred — only fall back to this if it fails.

---

## 10. Run migrations and seed initial data

Back in `~/gold-website`:

```bash
php artisan migrate --force
```

The `--force` flag is mandatory in production (Laravel refuses to run
migrations interactively when `APP_ENV=production`).

This creates every table: `users`, `cache`, `cache_locks`, `sessions`,
`metal_prices`, `currency_rates`, `settings`, `pages`, `news_tickers`,
`price_margins`, `scrape_logs`, `orders`, `payments`, `contacts`, etc.

Then seed initial content (static pages, payment settings defaults,
news ticker items, and an admin user):

```bash
php artisan db:seed --force
```

> **Admin user**: the `DatabaseSeeder` creates a default admin. Credentials
> default to `admin@islamabadbullionexchange.com` / `password`. **Change
> the password immediately after first login** (see section 17).

---

## 11. Create the public storage symlink

```bash
php artisan storage:link
```

This creates `public/storage → storage/app/public` so uploaded files
(e.g. the site logo from Filament → Site Settings) are publicly servable.

---

## 12. Set filesystem permissions

Laravel writes logs, compiled views, and cache files into `storage/`
and `bootstrap/cache/`. Those directories must be writable by the web
server user.

```bash
chmod -R 775 ~/gold-website/storage
chmod -R 775 ~/gold-website/bootstrap/cache
```

On some Hostinger plans the web server runs as the same user as your
shell, in which case `755` is enough. If you see "permission denied"
errors in logs after deploy, raise these back to `775`.

Also make sure `storage/logs/` is writable:

```bash
mkdir -p ~/gold-website/storage/logs
touch ~/gold-website/storage/logs/laravel.log
chmod 664 ~/gold-website/storage/logs/laravel.log
```

---

## 13. Warm the production caches

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan filament:cache-components
```

These four commands compile your config, routes, Blade templates, and
Filament components into fast static representations. Do this **after**
every code deploy, and re-run them if you change `.env` or any Blade file.

If `view:cache` errors with "class not found", re-run composer's
autoloader dump:

```bash
composer dump-autoload --optimize
```

…and try again.

---

## 14. Seed the cache with a first price fetch

Pull the first batch of upstream prices into the cache table, so the
homepage has something to display before the first scheduled tick:

```bash
php artisan prices:fetch
```

Expected output:

```
Fetching latest prices...
Prices updated successfully.
```

Verify the cache row exists:

```bash
php artisan tinker --execute="
\$p = app(\App\Services\PriceEngine\PriceCacheManager::class)->getAllPrices();
echo '24K Gold 1 Tola buy: Rs ' . number_format(\$p['gold']['24k']['tola']['buy']) . PHP_EOL;
echo 'Last updated:        ' . \$p['last_updated'] . PHP_EOL;
"
```

If you get a gold price and a recent timestamp, the scraper works end
to end.

---

## 15. Set up the 1-minute cron (CRITICAL)

This is the single most important step. The dashboard is designed to feel
real-time because of two clocks running in lockstep: a 1-minute server
refetch and a 3-second client poll. If the cron is set to anything slower
than every minute, the "real-time feel" silently degrades to stale data.

1. hPanel → **Advanced → Cron Jobs**
2. Click **Create a cron job**
3. Fill in:
   - **Type**: Custom (or "Advanced")
   - **Minute**: `*`
   - **Hour**: `*`
   - **Day**: `*`
   - **Month**: `*`
   - **Weekday**: `*`
   - **Command**:
     ```
     cd ~/gold-website && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
     ```
   - (If `/usr/bin/php` is not the 8.3 binary, use the full path Hostinger
     shows for PHP 8.3 — often `/opt/alt/php83/usr/bin/php` or similar.
     Run `which php` from SSH to find it.)
4. Click **Create**

Expected cron schedule line after creation: `* * * * *`.

### Verify the schedule

From SSH:

```bash
cd ~/gold-website
php artisan schedule:list
```

Should show exactly one entry:

```
* * * * *  php artisan prices:fetch ........... Next Due: NN seconds from now
```

Wait 70 seconds, then check that the scheduler has actually been firing:

```bash
tail -n 20 storage/logs/laravel.log
```

You should see `PriceFetcher: Prices updated successfully` entries with
timestamps roughly 60 seconds apart. If not, see **Troubleshooting →
"Cron is not running"** below.

---

## 16. Force HTTPS

Hostinger issues a free Let's Encrypt certificate for your domain
automatically. To force all traffic to HTTPS, edit
`~/gold-website/public/.htaccess` and add the following lines **immediately
after** `RewriteEngine On`:

```apache
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

Alternatively, hPanel → **Advanced → Force HTTPS** toggle does the same
thing at the panel level.

Verify:

```bash
curl -I http://islamabadbullionexchange.com
# should return: HTTP/1.1 301 Moved Permanently
#                Location: https://islamabadbullionexchange.com/
```

---

## 17. Verify the deployment end-to-end

### 17a. Homepage renders

Open https://islamabadbullionexchange.com in a browser. You should see:

- Browser tab title: **"Live Gold & Silver Prices in Pakistan - Islamabad Bullion Exchange"**
- Header/footer show **Islamabad Bullion Exchange**
- The top "International Metal Rates" card shows gold BID + ASK roughly
  0.05% apart, and silver BID + ASK roughly 0.1% apart (the admin-configurable
  spread)
- Gold tabs: `1 Tola`, `10 Gram`, `5 Gram`, `1 Gram` (no "10 Tola" tab)
- Every price cell has a subtle scanning green line underneath it
- The "LIVE" badge in the top-right of the international card has a
  pulsing green dot

Open DevTools → **Network** → filter `livewire`. Within a few seconds you
should see POST requests to `/livewire/update` firing roughly every **3 s**.

### 17b. Prices refresh every minute

Stay on the page for 2–3 minutes. The "Updated NN seconds ago" counter
should reset every minute (not every 5 minutes). Occasionally a price
cell will briefly flash green or red and its ● indicator will upgrade
to a green ▲ or red ▼.

### 17c. Admin panel works

Visit https://islamabadbullionexchange.com/admin. Log in with:

- Email: `admin@islamabadbullionexchange.com`
- Password: `password`

**Immediately change the password**: admin profile icon (top right) →
**Edit profile** → set new password.

Then navigate to **System → Site Settings** and verify:

- A **Site Identity** section with `Site Name` field
- A **International Spread** section with two numeric inputs:
  - Gold Spread (default `0.05` %)
  - Silver Spread (default `0.1` %)

Change Gold Spread to `0.25`, Save, reload the homepage. The gold ASK
should now be about `BID × 1.0025`. Revert to `0.05` when done.

### 17d. Other pages

Click through `/buy`, `/sell`, `/zakat-calculator`, `/contact`, and
`/about-us`. Every browser tab title should end with
**"Islamabad Bullion Exchange"**.

### 17e. Cron is ticking

```bash
tail -f ~/gold-website/storage/logs/laravel.log
```

You should see a `Prices updated successfully` line appearing about once
per minute. `Ctrl+C` to exit `tail`.

---

## 18. Deploying future updates

Because `public/build/` ships through git, future deploys are a single
`git pull` on the server plus cache rewarm.

**On your local machine**, before pushing any change that touches CSS,
JS, or Tailwind-class usage:

```bash
cd <local-path>/gold-website
npm run build                # regenerate public/build/
git add public/build         # stage the regenerated assets
git add <your-other-changed-files>
git commit -m "your message"
git push origin main
```

(If the change is pure PHP and doesn't affect styles, you can skip the
`npm run build` step.)

**Then from SSH on the server**:

```bash
cd ~/gold-website
git pull origin main
composer install --no-dev --optimize-autoloader --no-interaction
php artisan migrate --force
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan filament:cache-components
```

The site should now be running the latest code. The clear-then-cache
sequence takes under a second, so there's no visible downtime window.

---

## 19. Troubleshooting

### "500 Internal Server Error" on every page

1. Check `~/gold-website/storage/logs/laravel.log` — almost every Laravel
   error lands here with a stack trace:
   ```bash
   tail -n 100 ~/gold-website/storage/logs/laravel.log
   ```
2. Common causes:
   - Wrong `DB_*` values in `.env` → Laravel can't connect to MySQL
   - `APP_KEY` empty → `php artisan key:generate --force`
   - `storage/` or `bootstrap/cache/` not writable → re-run chmod in step 12
   - Stale config cache after an `.env` change →
     `php artisan config:clear && php artisan config:cache`

### Blank white page, no error

- `APP_DEBUG=false` in production hides error details. Temporarily set
  `APP_DEBUG=true`, clear config (`php artisan config:clear`), reload,
  read the error, then **set it back to false**.

### "Vite manifest not found" or broken CSS/JS

- The pre-built assets didn't come through the git clone for some reason.
  ```bash
  ls -la ~/gold-website/public/build/
  # must contain manifest.json and assets/
  ```
- If empty or missing, force a clean pull:
  ```bash
  cd ~/gold-website
  git fetch origin
  git checkout main
  git pull origin main
  ls public/build/      # should now show manifest.json + assets/
  ```
- If `public/build/` is still missing after that, the developer may have
  committed a code change without rebuilding. Ask them to run
  `npm run build && git add public/build && git commit --amend` (or a new
  commit) and push again.

### Images or logo upload not showing

```bash
cd ~/gold-website
php artisan storage:link
```

Then confirm `public/storage` exists and is a symlink:
```bash
ls -la public/storage
# lrwxrwxrwx ... storage -> /home/u.../gold-website/storage/app/public
```

### Cron is not running

1. Verify the schedule entry in hPanel shows `* * * * *` exactly.
2. From SSH, run the exact same command manually:
   ```bash
   cd ~/gold-website && /usr/bin/php artisan schedule:run
   ```
   If this errors, the cron will also error. Fix the error (usually a
   wrong PHP path or a missing `.env`).
3. Check the cron's own log in hPanel → Advanced → Cron Jobs → "Cron
   output" — Hostinger captures stderr there even though we redirect to
   `/dev/null`.
4. Confirm `routes/console.php` contains the schedule:
   ```bash
   grep -n prices:fetch routes/console.php
   # should show: Schedule::command('prices:fetch')->everyMinute()->withoutOverlapping(55);
   ```

### Schedule runs but prices never update

- Try running the fetcher directly and watch its output:
  ```bash
  php artisan prices:fetch -v
  ```
- If upstream pakgold.net is unreachable from Hostinger's IP range, the
  scraper will log `Source not available`. In that case the fallback chain
  (GoldApi → ExchangeRate → FawazCurrency) should take over — you'll see
  which source succeeded in `scrape_logs` table:
  ```bash
  php artisan tinker --execute="
  foreach(\App\Models\ScrapeLog::latest()->take(5)->get() as \$r) {
      echo \$r->created_at.' '.\$r->source.' '.\$r->status.PHP_EOL;
  }
  "
  ```

### Livewire "Component not found" or stale morph errors

- Usually means `view:cache` has stale compiled Blade templates.
  ```bash
  php artisan view:clear
  php artisan view:cache
  ```

### Filament admin: "Class not found"

```bash
composer dump-autoload --optimize
php artisan filament:cache-components
```

### Prices on `/` show "—" dashes instead of numbers

- The cache is empty. Run `php artisan prices:fetch` manually.
- If that fails, check `storage/logs/laravel.log` for the scraper's error
  and the `scrape_logs` table for recent failures.

### HTTPS redirect loop

- You enabled both the hPanel "Force HTTPS" toggle **and** the `.htaccess`
  rewrite rule. Pick one, not both.

---

## 20. Rollback plan

If a deploy breaks production, roll back the git commit and re-warm
caches. Because `public/build/` is committed alongside source code in
every deploy-ready commit, resetting to an older commit automatically
also reverts to that commit's compiled assets. The MySQL data is
unaffected.

```bash
cd ~/gold-website
git log --oneline -5              # find the commit that worked
git reset --hard <GOOD_COMMIT_SHA>
composer install --no-dev --optimize-autoloader --no-interaction

php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan filament:cache-components
```

If a migration was part of the broken deploy, roll it back explicitly:
```bash
php artisan migrate:rollback --step=1 --force
```

(Only roll back migrations you understand — destructive migrations like
`dropColumn` will lose data.)

---

## Admin access summary

- **URL**: https://islamabadbullionexchange.com/admin
- **Default email**: `admin@islamabadbullionexchange.com`
- **Default password**: `password`
- **CHANGE THE PASSWORD IMMEDIATELY AFTER FIRST LOGIN**

Key admin screens:

- **System → Site Settings**: site name, logo, and the new
  **International Spread** (gold/silver percentage margin on BID→ASK)
- **Content → Pages**: About/Disclaimer/Privacy/Terms markdown content
- **Content → News Tickers**: items in the scrolling top ticker
- **Content → Price Margins**: buy/sell margins per karat (applied to
  gold prices derived from upstream)
- **Commerce → Orders / Payments**: buy/sell order management

---

## Deployment checklist (TL;DR)

Copy this into a task tracker and tick off as you go:

- [ ] 1. Create MySQL DB in hPanel, save credentials
- [ ] 2. Enable SSH in hPanel, test `ssh` from local
- [ ] 3. Set PHP version to 8.3+ in hPanel
- [ ] 4. Set `memory_limit=512M` and friends in PHP options
- [ ] 5. `git clone` into `~/gold-website`
- [ ] 6. `composer install --no-dev --optimize-autoloader`
- [ ] 7. Verify `public/build/` is present after clone (ships via git)
- [ ] 8. `cp .env.example .env` + `php artisan key:generate --force`
- [ ] 9. Fill DB_*, APP_URL, APP_DEBUG=false in `.env`
- [ ] 10. Symlink `~/public_html` → `~/gold-website/public`
- [ ] 11. `php artisan migrate --force && php artisan db:seed --force`
- [ ] 12. `php artisan storage:link`
- [ ] 13. `chmod -R 775 storage bootstrap/cache`
- [ ] 14. `config:cache && route:cache && view:cache && filament:cache-components`
- [ ] 15. `php artisan prices:fetch` — verify cache populated
- [ ] 16. hPanel → Cron → `* * * * *` → `cd ~/gold-website && php artisan schedule:run >> /dev/null 2>&1`
- [ ] 17. Force HTTPS (`.htaccess` rule or hPanel toggle)
- [ ] 18. Open https://islamabadbullionexchange.com — verify title, tabs, BID/ASK spread, 3s polls
- [ ] 19. Open `/admin`, log in, change default password
- [ ] 20. `tail -f storage/logs/laravel.log` — confirm `prices:fetch` fires every minute
