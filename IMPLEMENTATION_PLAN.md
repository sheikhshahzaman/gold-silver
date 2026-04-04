# Gold & Silver Price Website - Implementation Plan

**Project:** Real-Time Gold & Silver Rates Website (Pakistan)
**Tech Stack:** Laravel 13 + Livewire 3 + Tailwind CSS + Filament v3
**Date:** 27 March, 2026

---

## Tech Stack Decision

| Layer | Technology | Reason |
|-------|-----------|--------|
| Backend | Laravel 13 (PHP 8.5) | Already installed, industry standard |
| Frontend | Livewire 3 + Blade | Real-time polling without JS frameworks, SEO-friendly |
| Styling | Tailwind CSS (Dark Mode) | Ships with Laravel, gold/emerald theme easy to configure |
| Interactivity | Alpine.js (ships with Livewire) | Zakat calculator, dropdowns, toggles — no extra install |
| Admin Panel | Filament v3 (free, open source) | Built on Livewire, generates CRUD in minutes |
| Cache | File Cache (upgrade to Redis later) | Current prices cached, zero DB load on reads |
| Queue | Database Queue | For price fetch jobs |
| Charts | Chart.js or ApexCharts | Lightweight, for price history graphs |

---

## Color Theme Configuration

| Element | Color | Tailwind Class |
|---------|-------|---------------|
| Primary Background | Deep Emerald (#0D3B2E) | Custom `bg-emerald-950` |
| Card Background | Semi-transparent dark green | Custom with opacity |
| Gold Accent | Copper/Gold (#D4A574) | Custom `text-gold` |
| Headings | Gold | Custom accent color |
| Body Text | White | `text-white` |
| Buy Price Badge | Green | `bg-green-600` |
| Sell Price Badge | Red/Rose | `bg-red-600` |
| Buttons | Gold gradient | Custom gold gradient |
| Links/Hover | Light gold | Custom lighter shade |

Configure in `tailwind.config.js` with custom color palette extending the default theme.

---

## Real-Time Price Data Strategy

### Primary Source (Web Scraping): PakGold.net (FREE)

- **URL:** `https://www.pakgold.net/`
- **Cost:** Completely free (web scraping)
- **Data Available — ALL IN ONE PAGE:**

| Category | Data Points | Details |
|----------|------------|---------|
| **Gold Open Market** | Pabsi (24K Pure) & Rawa | Buy/Sell per Tola + High/Low |
| **Gold Board Rates** | 24 Karat, 21K, 22K | Per Tola & Per 10 Gram |
| **Silver** | Local market | Buy/Sell per Tola |
| **International Gold** | XAU | Price in USD |
| **International Silver** | XAG | Price in USD |
| **Platinum** | Local + International | Buy/Sell in PKR + USD |
| **Palladium** | Local + International | Buy/Sell in PKR + USD |
| **Currency: Interbank USD** | USD/PKR | Buy/Sell |
| **Currency: Open Market USD** | USD/PKR | Buy/Sell |
| **Currency: British Pound** | GBP/PKR | Buy/Sell |
| **Currency: Euro** | EUR/PKR | Buy/Sell |
| **Currency: Malaysian Ringgit** | MYR/PKR | Buy/Sell |
| **Currency: Saudi Riyal** | SAR/PKR | Buy/Sell |
| **Currency: UAE Dirham** | AED/PKR | Buy/Sell |
| **Pakistan Stock Exchange** | KSE Index | Current + Change |

- **Source:** Rawalpindi / Islamabad bullion market benchmark (used nationwide)
- **Scraping method:** Laravel HTTP client + Symfony DomCrawler (server-rendered HTML tables)
- **Advantage:** Single scrape gives us gold, silver, platinum, currencies, and stock market — no need for multiple APIs

### Fallback Source 1: Gold-API.com (FREE API)

- **URL:** `https://api.gold-api.com/price/XAU` and `/price/XAG`
- **Cost:** Free, unlimited real-time requests, no API key needed
- **Use case:** Fallback for international XAU/XAG prices if PakGold.net scraping fails

### Fallback Source 2: Fawazahmed0 Currency API (FREE)

- **URL:** `https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies/usd.json`
- **Cost:** Free, unlimited, no API key, hosted on CDN
- **Use case:** Fallback for USD/PKR exchange rate + XAU/XAG prices

### Fallback Source 3: Open ExchangeRate API (FREE)

- **URL:** `https://open.er-api.com/v6/latest/USD`
- **Cost:** Free, unlimited, no API key
- **Use case:** Fallback for USD/PKR rate only

### Why PakGold.net is the Best Primary Source

1. **One scrape = everything we need** — gold, silver, platinum, currencies, stock index
2. **Already in PKR** — no currency conversion math needed for local rates
3. **Includes both Open Market and Board Rates** — exactly what Pakistani users expect
4. **Rawalpindi/Islamabad benchmark** — the standard reference used across Pakistan
5. **Includes Buy/Sell spread** — not just a single mid-price
6. **Includes Rawa rate** — local market standard that APIs don't provide
7. **No API key, no rate limits, no registration**

### Price Calculation Formulas (For Derived Prices Only)

When we need to calculate prices not directly available from scraping:

**Conversion Constants:**
- 1 Troy Ounce = 31.1035 grams
- 1 Tola = 11.6638 grams
- 1 KG = 1000 grams

**Karat Purity Multipliers (derive from 24K):**
- 24K = 100% (1.000)
- 22K = 91.67% (0.9167)
- 21K = 87.50% (0.8750)
- 18K = 75.00% (0.7500)
- Rawa = scraped directly from PakGold.net (no calculation needed)

**Deriving unit prices:**
- PakGold gives us Per Tola and Per 10 Gram rates
- Per Gram = Per 10 Gram / 10
- Per 5 Gram = Per 10 Gram / 2
- Per KG = Per Gram × 1000
- Per Ounce = Per Gram × 31.1035

### Price Fetching Architecture

1. **Laravel Scheduler** runs a scraping job every 5 minutes
2. Job scrapes PakGold.net → parses all data (gold, silver, platinum, currencies)
3. If PakGold.net fails → falls back to Gold-API.com for international rates + Open ExchangeRate for USD/PKR
4. Derives additional unit prices (per gram, per 5 gram, per KG) from scraped data
5. Stores in `prices` table (for history) AND writes to cache (for fast reads)
6. Cache TTL = 5 minutes (matches fetch interval)
7. Frontend Livewire components poll every 15-30 seconds, reading from cache only — zero DB load
8. Admin can manually override any price from the Filament panel if needed

---

## Payment Integration Strategy

### Phase 1 (Launch Day): Manual Bank Transfer — ZERO COST

**Implementation:**
- Display bank account details on checkout page (Bank Name, Account Title, Account Number, IBAN)
- Customer makes transfer via their banking app
- Customer uploads payment receipt/screenshot on the website
- Admin reviews receipt in Filament admin panel, cross-checks with bank statement
- Admin marks payment as Verified or Rejected
- System notifies customer via email/SMS

**Why start here:**
- Zero transaction fees
- No merchant account approval needed
- Works immediately on day one
- Most Pakistani customers are comfortable with this flow

### Phase 2 (Week 2-4): JazzCash Integration — ~1.5-2% Fee

**How it works:**
- Use JazzCash **Hosted Checkout (redirect)** method
- Our Laravel backend prepares payment data + generates HMAC SHA-256 hash
- Customer is redirected to JazzCash's payment page
- Customer pays via JazzCash wallet, debit card, or credit card
- JazzCash redirects back to our site with payment status
- We verify the response hash and update order status

**Requirements:**
- Apply for JazzCash Merchant Account (needs NTN/CNIC + active bank account)
- Approval takes 1-3 weeks
- We get: Merchant ID, Password, Hash Key (Integrity Salt)
- Sandbox available for testing while waiting for approval

**Supported Payment Methods via JazzCash:**
- JazzCash Mobile Wallet (MWALLET) — ~1.5-2% fee
- Debit Cards (Visa/MC) — ~2-2.5% fee
- Credit Cards (Visa/MC) — ~2.5-3% fee
- Over-the-Counter (OTC) — flat PKR 10-30

**Laravel Implementation:**
- Build a custom `JazzCashGateway` service class (simple HTTP POST + HMAC hash)
- No need for third-party packages — API is straightforward
- Use a **PaymentGateway interface** so we can add more gateways later

### Phase 3 (Month 2): EasyPaisa — ~1.5-2% Fee

- Nearly identical API flow to JazzCash
- Apply for merchant account separately
- Same hosted checkout redirect approach
- Adding EasyPaisa + JazzCash together covers ~85% of Pakistan's mobile wallet market

### Payment Gateway Architecture

Build a **PaymentGateway interface** from day one:
- `BankTransferGateway` — creates pending payment record
- `JazzCashGateway` — redirect flow + hash verification
- `EasyPaisaGateway` — redirect flow (added later)
- Gateway selection via config — adding new gateways doesn't touch controllers

### Order Flow

1. Customer selects metal, karat, quantity on Buy/Sell page
2. System locks current price and calculates total
3. Customer selects payment method (Bank Transfer / JazzCash)
4. Payment is processed based on selected method
5. Order created with status: Pending → Confirmed → Processing → Delivered
6. Admin manages orders via Filament admin panel

---

## Page-by-Page Implementation Plan

### Page 1: Home / Spot Price Page

**Route:** `/` (homepage)

**Components:**
- **Header:** Logo, site name, "Live" indicator badge (green dot)
- **News Ticker:** Scrolling marquee with latest gold market updates (admin-managed from Filament)

- **Section A — International Rates Table:**
  - Gold (XAU) — BID/ASK price in USD per ounce
  - Silver (XAG) — BID/ASK price in USD per ounce
  - Last updated timestamp

- **Section B — Gold Open Market Rates (PKR):**
  - Source: PakGold.net Islamabad/Rawalpindi Live Market
  - Tab buttons for quantity: 1 Tola | 10 Tola | 10 Gram | 5 Gram | 1 Gram
  - Table rows for each karat: 24K (Pabsi), Rawa, 22K, 21K, 18K
  - Buy (green) and Sell (red) price columns + High/Low
  - "Updated: DD Mon, YYYY HH:MM AM/PM" timestamp

- **Section C — Gold Board Rates (PKR):**
  - Source: PakGold.net Islamabad/Rawalpindi Board Rate
  - Karat rows: 24K, 22K, 21K — Per Tola & Per 10 Gram
  - These are jewellery retail benchmark rates

- **Section D — Silver Rates (PKR):**
  - Rows: 1 KG, 10 Tola, 1 Tola, 10 Gram, 1 Gram
  - Buy and Sell price columns
  - Updated timestamp

- **Section E — International Currency Rates:**
  - Source: PakGold.net currency section
  - Interbank USD/PKR — Buy/Sell
  - Open Market USD/PKR — Buy/Sell
  - GBP/PKR, EUR/PKR, SAR/PKR, AED/PKR, MYR/PKR — Buy/Sell
  - Updated timestamp

- **Section F — Platinum & Palladium (Optional):**
  - Platinum: Local (PKR) + International (USD)
  - Palladium: Local (PKR) + International (USD)

**Implementation:** Livewire page component that reads all prices from cache. Tab switching via Alpine.js. All sections auto-refresh via `wire:poll.15s`. Single scrape from PakGold.net feeds all sections.

---

### Page 2: Buy Page

**Route:** `/buy`

**Components:**
- **Header:** "Buy" title + Live badge + last updated timestamp + PKR badge
- **Step Wizard:**
  - Step 1: Choose Metal — Gold or Silver (icon cards, Alpine.js toggle)
  - Step 2: Enter Quantity — numeric input with unit selector (Tola, Gram, etc.)
  - Step 3: See instant BUY price — calculated client-side using Alpine.js with server-provided rates
- **Proceed to Payment** button → redirects to checkout/payment page

**Implementation:** Livewire component for price data + Alpine.js for the step wizard interactivity. Price calculation happens client-side for instant feedback. On "Proceed", a Livewire action locks the price and creates a pending order.

---

### Page 3: Sell Page

**Route:** `/sell`

**Components:**
- Identical layout to Buy page
- Shows SELL price instead of BUY price
- Step wizard: Choose Metal → Enter Quantity → See SELL price

**Implementation:** Same Livewire component as Buy page, reused with a `type` parameter (`buy` or `sell`) to switch price column.

---

### Page 4: Contact Us Page

**Route:** `/contact`

**Components:**
- **Location Card:** Address with "Copy" and "Open in Maps" buttons
- **Phone Number Card:** With "Copy" and "Call" action buttons
- **WhatsApp Card:** With "Copy" and "Message" buttons (opens `wa.me/` link)
- **Email Card:** With "Copy" and "Email" buttons (opens `mailto:`)
- **Opening Hours Table:** Day-wise schedule (Mon-Thu, Fri, Sat, Sun)
- **Contact Form** (optional): Name, Email, Phone, Message → stored in `contacts` table
- **WhatsApp tip:** "For urgent assistance, WhatsApp is the fastest option"

**Implementation:** Mostly static Blade template. Contact details managed via Filament settings panel. Copy buttons use Alpine.js with `navigator.clipboard`. Contact form uses Livewire for submission.

---

### Page 5: Buy/Sell Checkout & Payment Page

**Route:** `/checkout/{order}`

**Components:**
- **Order Summary:** Metal, karat, quantity, unit, locked price, total in PKR
- **Payment Method Selector:**
  - Bank Transfer tab: Shows bank details (Bank Name, Account Title, Account Number, IBAN) + receipt upload form
  - JazzCash tab: "Pay with JazzCash" button → redirects to JazzCash hosted page
- **Order Confirmation:** After payment, shows order status page
- **Receipt Upload:** File input (JPEG/PNG/PDF, max 5MB) + reference number text input

**Implementation:** Livewire component. Bank details stored in settings (admin-configurable). Receipt stored via Laravel Storage. JazzCash integration via custom gateway service class.

---

### Page 6: Zakat Calculator Page

**Route:** `/zakat-calculator`

**Components:**
- **Header:** "Live Zakat Estimator" + last updated timestamp + PKR Rs badge
- **Settings Panel:**
  - Price Basis dropdown: Buy / Sell
  - Nisab Basis dropdown: Silver Nisab / Gold Nisab
  - Tip text about Silver Nisab being more inclusive
- **Gold Section:**
  - Toggle On/Off
  - Dynamic entries list with "+ Add" button
  - Each entry: Category dropdown (24K, 22K, 21K, 18K, Spot) + Unit dropdown (Gram, Tola, KG) + Amount input
  - Shows estimated value per entry in real-time
- **Silver Section:**
  - Toggle On/Off
  - Dynamic entries list with "+ Add" button
  - Each entry: Unit dropdown (Gram, Tola, KG) + Amount input
  - Shows estimated value per entry in real-time
- **Results Panel:**
  - Total Gold Value (PKR)
  - Total Silver Value (PKR)
  - Combined Total Value
  - **Zakat Amount (2.5% of total)**
  - Nisab threshold indicator (eligible or not)
- **Buttons:** Cancel | View Result

**Implementation:** Entirely frontend using Alpine.js — no server round-trips needed. Current gold/silver prices injected into the page from Livewire on load. All calculations (karat conversion, unit conversion, 2.5% zakat) happen instantly in the browser. Alpine.js manages the dynamic entry arrays, totals, and toggles.

---

### Page 7: More / Menu Page

**Route:** `/more`

**Components:**
- List of navigation cards with icons:
  - Calculate Zakat → `/zakat-calculator`
  - About Us → `/about`
  - Disclaimer → `/disclaimer`
  - Privacy Policy → `/privacy-policy`
  - Terms & Conditions → `/terms`

**Implementation:** Static Blade template. Simple card list with icons and links.

---

### Pages 8-11: Static Content Pages

**Routes:** `/about`, `/disclaimer`, `/privacy-policy`, `/terms`

**Implementation:** Use a single `pages` table in the database. Each page has: title, slug, body (rich text), meta_title, meta_description. Content managed via Filament admin panel using a rich text editor. Single Blade template renders any page by slug. Admin can update content without touching code.

---

## Navigation Structure

### Desktop: Top Navigation Bar
- Logo (left)
- Menu items: Home | Gold Rates | Silver Rates | Buy | Sell | Zakat Calculator | Contact
- Live indicator (right)

### Mobile: Bottom Navigation Bar (5 tabs, matching the reference app)
1. Contact Us (icon: card/address)
2. Buy (icon: shopping cart)
3. Spot/Home (icon: chart line — center, highlighted)
4. Sell (icon: price tag)
5. More (icon: three dots)

**Implementation:** Blade layout component with responsive breakpoints. Desktop shows top nav, mobile shows bottom tab bar. Active tab highlighted with gold accent.

---

## Database Schema

| Table | Purpose | Key Columns |
|-------|---------|-------------|
| `users` | Authentication + roles | name, email, phone, password, role (user/admin) |
| `metal_prices` | Gold/Silver/Platinum price history | metal, type (open_market/board), karat, unit, buy_price, sell_price, high, low, source, fetched_at |
| `currency_rates` | Currency exchange rate history | currency_pair (USD_PKR, GBP_PKR, etc.), type (interbank/open_market), buy_rate, sell_rate, fetched_at |
| `price_snapshots` | Daily OHLC for charts | date, metal, karat, open, high, low, close |
| `orders` | Order management | user_id, order_number, metal, karat, quantity, unit, locked_price, total_amount, status |
| `payments` | Payment tracking | order_id, method, amount, proof_image, reference_number, status, verified_by, verified_at |
| `contacts` | Contact form submissions | name, email, phone, subject, message, is_read |
| `pages` | CMS content | title, slug, body, meta_title, meta_description, is_published |
| `settings` | Site configuration | key, value (stores margins, bank details, API keys, contact info, hours) |
| `news_tickers` | Scrolling news items | title, content, is_active, sort_order |
| `price_margins` | Admin margin control | metal, karat, buy_margin (PKR per Tola), sell_margin (PKR per Tola), updated_by, updated_at |
| `margin_logs` | Margin change history | metal, karat, old_buy_margin, new_buy_margin, old_sell_margin, new_sell_margin, changed_by, created_at |
| `scrape_logs` | Track scraping health | source, status (success/fail), error_message, response_time_ms, created_at |

**Price History Strategy:**
- Every fetch inserts new rows in `metal_prices` and `currency_rates` (gives tick-by-tick history)
- Nightly job aggregates daily OHLC into `price_snapshots`
- Prune `metal_prices` and `currency_rates` rows older than 90 days monthly
- `scrape_logs` tracks PakGold.net scraping health — alerts admin if multiple failures occur
- Index on `(metal, karat, fetched_at)` and `(currency_pair, fetched_at)` for fast queries

---

## Admin Panel (Filament v3)

### Dashboard Widgets
- Current Gold & Silver prices (live) — showing both scraped price and final price after margin
- Today's orders count and total revenue
- Pending payments awaiting verification
- Price fetch status (last successful fetch time)

### Price Margin Control (Admin Dashboard)

Admin can add/subtract a fixed amount (in PKR) to both Buy and Sell prices from the dashboard. This is the key feature for the business to control their profit margin.

**How it works:**
- Admin sets a margin value per metal type from the Filament dashboard
- The margin is stored in a `price_margins` table (not in settings, for granular control)
- The website displays: `Final Price = Scraped Price + Admin Margin`
- Margin can be positive (add to price) or negative (subtract from price)

**Margin Settings Available:**

| Metal/Type | Buy Margin (PKR) | Sell Margin (PKR) | Example |
|-----------|------------------|-------------------|---------|
| Gold 24K (per Tola) | +500 | +800 | Scraped Buy: 490,170 → Displayed: 490,670 |
| Gold Rawa (per Tola) | +500 | +800 | Scraped Buy: 480,175 → Displayed: 480,675 |
| Gold 22K (per Tola) | +400 | +700 | Applied proportionally |
| Gold 21K (per Tola) | +400 | +700 | Applied proportionally |
| Gold 18K (per Tola) | +300 | +600 | Applied proportionally |
| Silver (per Tola) | +50 | +100 | Scraped Buy: 8,770 → Displayed: 8,820 |

**Dashboard UI:**
- Simple form on the Filament dashboard with input fields for each metal's Buy/Sell margin
- Shows live preview: "Scraped Price → Your Price" side by side
- One-click save — takes effect immediately on the website
- History log of margin changes (who changed, when, old value → new value)

**Margin applies to all units automatically:**
- Admin sets margin per Tola only
- System auto-calculates proportional margin for other units:
  - Per 10 Gram margin = (Per Tola margin / 11.6638) × 10
  - Per Gram margin = Per Tola margin / 11.6638
  - Per 5 Gram margin = (Per Tola margin / 11.6638) × 5
  - Per KG margin = (Per Tola margin / 11.6638) × 1000

### Admin Resources
| Resource | Features |
|----------|----------|
| **Price Margins** | Set Buy/Sell margin per metal type, live preview, change history |
| **Price Management** | View current scraped prices, manually override if scraping is down |
| **Order Management** | View all orders, filter by status, update order status |
| **Payment Verification** | View pending payments, see receipt image, mark verified/rejected |
| **User Management** | View customers, manage admin users |
| **Page Management** | Edit About, Disclaimer, Privacy, Terms content (rich text editor) |
| **Contact Messages** | View contact form submissions, mark as read |
| **News Ticker** | Add/edit/delete scrolling news items |
| **Site Settings** | Configure bank details, contact info, opening hours, API keys |

---

## SEO Strategy

### URL Structure
| URL | Page |
|-----|------|
| `/` | Homepage with live prices |
| `/gold-rate-today-pakistan` | Gold rates (main SEO landing page) |
| `/silver-rate-today-pakistan` | Silver rates |
| `/gold-price-history` | Historical chart |
| `/buy` | Buy gold/silver |
| `/sell` | Sell gold/silver |
| `/zakat-calculator` | Zakat calculator |
| `/contact` | Contact page |
| `/about` | About page |

### SEO Essentials
- Dynamic `<title>` and `<meta description>` with current prices embedded
- JSON-LD structured data (ExchangeRateSpecification, Organization, FAQPage)
- XML sitemap via `spatie/laravel-sitemap` package
- Server-rendered prices in initial HTML (Livewire does this by default — critical for Googlebot)
- Canonical URLs on all pages
- Open Graph tags for social sharing

---

## Development Phases

### Phase 1: Foundation (Week 1)
- Install Livewire 3, Tailwind dark theme setup, Filament v3
- Configure custom emerald + gold color palette
- Create database migrations for all tables
- Build base layout (header, footer, mobile bottom nav)
- Set up Laravel scheduler and queue

### Phase 2: Price Engine (Week 1-2)
- Build PakGold.net scraper service (primary — scrapes gold, silver, platinum, currencies in one go)
- Build fallback services (Gold-API.com for international rates, Open ExchangeRate for USD/PKR)
- Build price calculator service (derive per gram, per 5 gram, per KG from scraped per tola / per 10 gram)
- Set up scheduled jobs (every 5 min scrape + cache update)
- Build caching layer
- Build scrape health monitoring (log successes/failures, alert admin on repeated failures)

### Phase 3: Core Pages (Week 2-3)
- Home/Spot page with live prices (Livewire polling)
- Gold rates section with quantity tabs
- Silver rates section
- Buy page with step wizard
- Sell page with step wizard
- Contact Us page

### Phase 4: Zakat Calculator (Week 3)
- Build Alpine.js based calculator
- Multi-entry gold/silver forms
- Nisab threshold logic
- Live calculation with current prices

### Phase 5: Payment & Orders (Week 3-4)
- Bank transfer flow (receipt upload + admin verification)
- Order management system
- JazzCash sandbox integration (test while waiting for merchant approval)
- Checkout page
- Order confirmation and tracking page

### Phase 6: Admin Panel (Week 4)
- Filament resources for all tables
- Dashboard with widgets
- Payment verification workflow
- CMS pages management
- Settings management

### Phase 7: Static Pages & SEO (Week 4-5)
- About Us, Disclaimer, Privacy Policy, Terms pages
- SEO meta tags, JSON-LD structured data
- XML sitemap
- Performance optimization
- Mobile responsiveness testing

### Phase 8: Go Live (Week 5)
- JazzCash production credentials (if approved)
- Final testing on production server
- Domain setup, SSL certificate
- Deploy and monitor

---

## Required Composer Packages

| Package | Purpose |
|---------|---------|
| `livewire/livewire` | Real-time frontend components |
| `filament/filament` | Admin panel |
| `spatie/laravel-sitemap` | SEO sitemap generation |
| `symfony/dom-crawler` | HTML parsing for web scraping (PakGold.net) |

All other functionality (HTTP client, cache, queue, scheduler, file storage) is built into Laravel core.

---

## Cost Summary

| Item | Cost |
|------|------|
| Primary Data Source (PakGold.net scraping) | FREE |
| Fallback API (Gold-API.com) | FREE |
| Fallback API (open.er-api.com) | FREE |
| Fallback API (fawazahmed0) | FREE |
| Filament Admin Panel | FREE (open source) |
| Livewire + Tailwind | FREE (ships with Laravel) |
| Bank Transfer Payments | FREE (zero transaction fee) |
| JazzCash Payments | ~1.5-3% per transaction |
| EasyPaisa Payments (later) | ~1.5-3% per transaction |
| Hosting (VPS) | ~$5-20/month |
| Domain + SSL | ~$10-15/year |

**Total Upfront Cost: $0 (all free tools and APIs)**
**Ongoing Cost: ~$5-20/month hosting + payment gateway transaction fees**
