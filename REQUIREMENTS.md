# Gold & Silver Price Website - Requirements Document

**Project:** Real-Time Gold & Silver Price Website (Pakistan)
**Platform:** Web (Laravel - Responsive)
**Date:** 27 March, 2026

---

## Color Theme & Branding

- **Primary Background:** Dark Green (#0D3B2E / Deep Emerald)
- **Accent Color:** Gold/Copper (#D4A574)
- **Cards/Sections:** Semi-transparent dark green with subtle borders
- **Text:** White (primary), Gold (headings/highlights)
- **Buy Price:** Green badges
- **Sell Price:** Red/Orange badges
- **Overall Feel:** Professional, luxurious, bullion/jewellery industry standard

---

## Pages & Features

### 1. Home / Spot Price Page (Main Dashboard)
- **Live indicator** (green dot with "Live" badge)
- **News ticker/marquee** at the top with latest gold market updates
- **International Metal Rates Table:**
  - Gold (XAU) - BID & ASK price in USD per ounce
  - Silver (XAG) - BID & ASK price in USD per ounce
  - Last updated timestamp (PKT timezone)
- **Gold (XAU) Rates in PKR:**
  - Tab-based quantity selector: **1 Tola | 10 Tola | 10 Gram | 5 Gram | 1 Gram**
  - Karat-wise rows: **24K, Rawa, 22K, 21K, 18K**
  - Each row shows **Buy** and **Sell** price in PKR
  - "Prices calculated for selected quantity" note
  - Last updated timestamp
- **Silver (XAG) Rates in PKR:**
  - Rows: **1 KG, 10 Tola, 1 Tola, 10 Gram, 1 Gram**
  - Each row shows **Buy** and **Sell** price in PKR
  - Last updated timestamp

### 2. Buy Page
- Shows last updated timestamp with PKR currency badge
- **Step-by-step wizard:**
  - **Step 1:** Choose Metal - Gold or Silver (icon-based selection)
  - **Step 2:** Enter Quantity
  - Instantly shows **BUY price** based on selection

### 3. Sell Page
- Same layout as Buy page
- **Step-by-step wizard:**
  - **Step 1:** Choose Metal - Gold or Silver
  - **Step 2:** Enter Quantity
  - Instantly shows **SELL price** based on selection

### 4. Contact Us Page
- **Location:** Address with Copy & Open (map link) buttons
- **Phone Number:** With Copy & Call buttons
- **WhatsApp:** With Copy & Message buttons
- **Email Address:** With Copy & Email buttons
- **Opening Hours Table:**
  - Mon-Thu, Fri, Sat, Sun with respective timings
  - Tip note for urgent assistance via WhatsApp

### 5. More / Tools Page (Menu)
Links to the following sub-pages:
- Calculate Zakat
- About Us
- Disclaimer
- Privacy Policy
- Terms & Conditions

### 6. Zakat Calculator Page
- **Header:** "Live Zakat Estimator" with updated timestamp
- **Currency:** PKR Rs
- **Settings Section:**
  - Price basis: Sell (dropdown)
  - Nisab basis: Silver Nisab / Gold Nisab (dropdown)
  - Tip: "Silver Nisab is commonly used because it is more inclusive"
- **What You Own:**
  - Toggle Gold on/off - Add multiple categories (24K, 22K, etc.)
  - Toggle Silver on/off - Add silver weight
- **Gold Entries:**
  - Add multiple entries (+Add button)
  - Each entry: Category dropdown (Spot, 24K, 22K, etc.) + Unit dropdown (Gram, Tola, etc.)
  - Input: Gold amount
  - Shows estimated value in PKR
- **Silver Entries:**
  - Add multiple entries (+Add button)
  - Each entry: Unit dropdown (Gram, Tola, etc.)
  - Input: Silver amount
  - Shows estimated value in PKR
- **Estimated Total Section:**
  - Gold value (PKR)
  - Silver value (PKR)
  - **Total Zakat amount** (2.5% of total)
- **Buttons:** Cancel | View Result

### 7. Payment / Checkout Page
- Integrated into the **Buy flow** after selecting metal and quantity
- **Payment Methods:**
  - **JazzCash** - Mobile wallet payment integration (JazzCash Payment Gateway API)
  - **Bank Transfer** - Direct bank account payment with account details display
    - Account Title, Bank Name, Account Number, IBAN
    - Upload payment receipt/screenshot for confirmation
- **Order Summary** before payment:
  - Selected metal, karat, quantity, unit
  - Current live price at time of order
  - Total amount in PKR
- **Order Confirmation** page after successful payment
- **Order Tracking** - status updates (Pending, Confirmed, Processing, Delivered)

### 8. About Us Page
- Company introduction and background

### 9. Disclaimer Page
- Important notes about rates and information accuracy

### 10. Privacy Policy Page
- How user data is handled

### 11. Terms & Conditions Page
- Terms of use for the website

---

## Navigation

**Bottom Navigation Bar (5 tabs):**
1. Contact Us
2. Buy
3. Spot (Home - center/highlighted)
4. Sell
5. More

---

## Technical Requirements

- **Real-time price updates** (auto-refresh or API-based)
- **Responsive design** (mobile-first, works on all devices)
- **PKT timezone** for all timestamps
- **PKR currency** as default
- **Admin panel** to manage/update prices (if not API-driven)
- **Admin Price Margin Control:**
  - Admin can add/subtract a fixed PKR amount to Buy and Sell prices from the dashboard
  - Separate margin settings for each metal type (Gold 24K, Rawa, 22K, 21K, 18K, Silver)
  - Final displayed price = Scraped Market Price + Admin Margin
  - Margin set per Tola — auto-calculated for all other units (Gram, 10 Gram, KG, etc.)
  - Live preview showing "Market Price → Your Price" before saving
  - Change history log (who changed margin, when, old → new values)
- **SEO optimized** pages for gold/silver price searches in Pakistan
- **Payment Gateway Integration:**
  - JazzCash Payment Gateway API
  - Bank Account Transfer (manual verification)
- **Price Data Source (Free — Web Scraping + API Fallbacks):**
  - **Primary:** Scrape PakGold.net (www.pakgold.net) — provides ALL data in one page:
    - Gold open market rates (Pabsi/24K, Rawa) — Buy/Sell in PKR
    - Gold board rates (24K, 22K, 21K) — Per Tola & Per 10 Gram
    - Silver rates — Buy/Sell in PKR
    - International Gold (XAU) & Silver (XAG) in USD
    - Platinum & Palladium — Local + International
    - Currency exchange rates (USD, GBP, EUR, SAR, AED, MYR — Buy/Sell)
    - Pakistan Stock Exchange index
  - **Fallback 1:** Gold-API.com (free API, no key) — for international XAU/XAG prices
  - **Fallback 2:** Open ExchangeRate API (free) — for USD/PKR rate
  - **Fallback 3:** Fawazahmed0 Currency API (free CDN) — for XAU + XAG + PKR combined
  - **Last Resort:** Admin can manually update prices from Filament panel
  - Prices auto-refresh every 5 minutes via Laravel scheduler
  - If primary source fails, system automatically switches to fallback APIs

---

## Data Points Required

| Category | Data Points | Units | Price Types |
|----------|------------|-------|-------------|
| Gold (Open Market) | 24K (Pabsi), Rawa | 1 Tola, 10 Tola, 10 Gram, 5 Gram, 1 Gram | Buy & Sell + High/Low |
| Gold (Board Rate) | 24K, 22K, 21K | Per Tola, Per 10 Gram | Retail benchmark |
| Gold (Derived) | 18K | Per Tola, Per Gram (calculated from 24K) | Buy & Sell |
| Silver | Standard | 1 KG, 10 Tola, 1 Tola, 10 Gram, 1 Gram | Buy & Sell |
| Gold (International) | XAU | Per Ounce (USD) | BID & ASK |
| Silver (International) | XAG | Per Ounce (USD) | BID & ASK |
| Platinum | Local + International | PKR + USD | Buy & Sell |
| Currencies | USD, GBP, EUR, SAR, AED, MYR | vs PKR | Buy & Sell (Interbank + Open Market) |

---

**Note:** This document is for client review and approval. Development will begin after confirmation of these requirements.
