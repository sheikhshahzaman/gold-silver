<?php

namespace App\Services\PriceEngine;

use App\Models\CurrencyRate;
use App\Models\MetalPrice;
use Illuminate\Support\Facades\Cache;

class PriceCacheManager
{
    /**
     * Cache TTL in seconds (15 minutes).
     */
    private const TTL = 900;

    /**
     * Short TTL used when serving stale DB data because both the cache and an
     * on-demand refetch failed — keeps the next request from waiting 15 minutes
     * before trying to recover.
     */
    private const STALE_FALLBACK_TTL = 60;

    /**
     * Cache key prefixes.
     */
    private const KEY_GOLD = 'prices.gold';
    private const KEY_SILVER = 'prices.silver';
    private const KEY_CURRENCIES = 'prices.currencies';
    private const KEY_INTERNATIONAL = 'prices.international';
    private const KEY_PLATINUM = 'prices.platinum';
    private const KEY_PALLADIUM = 'prices.palladium';
    private const KEY_CRUDE_OIL = 'prices.crude_oil';
    private const KEY_PSX = 'prices.psx';
    private const KEY_LAST_UPDATED = 'prices.last_updated';
    public const KEY_ALL = 'prices.all_prices';

    /**
     * Store all price data in cache with TTL.
     *
     * @param array $data Expected keys: gold, silver, currencies, international, platinum, palladium, crude_oil, psx
     */
    public function cacheAllPrices(array $data): void
    {
        if (isset($data['gold'])) {
            Cache::put(self::KEY_GOLD, $data['gold'], self::TTL);
        }

        if (isset($data['silver'])) {
            Cache::put(self::KEY_SILVER, $data['silver'], self::TTL);
        }

        if (isset($data['currencies'])) {
            Cache::put(self::KEY_CURRENCIES, $data['currencies'], self::TTL);
        }

        if (isset($data['international'])) {
            Cache::put(self::KEY_INTERNATIONAL, $data['international'], self::TTL);
        }

        if (isset($data['platinum'])) {
            Cache::put(self::KEY_PLATINUM, $data['platinum'], self::TTL);
        }

        if (isset($data['palladium'])) {
            Cache::put(self::KEY_PALLADIUM, $data['palladium'], self::TTL);
        }

        if (array_key_exists('crude_oil', $data)) {
            Cache::put(self::KEY_CRUDE_OIL, $data['crude_oil'], self::TTL);
        }

        if (isset($data['psx'])) {
            Cache::put(self::KEY_PSX, $data['psx'], self::TTL);
        }

        Cache::put(self::KEY_LAST_UPDATED, now()->toIso8601String(), self::TTL);

        // Single-key aggregate so the API endpoint needs only 1 cache read
        Cache::put(self::KEY_ALL, [
            'gold' => $data['gold'] ?? [],
            'silver' => $data['silver'] ?? [],
            'currencies' => $data['currencies'] ?? [],
            'international' => $data['international'] ?? [],
            'platinum' => $data['platinum'] ?? [],
            'palladium' => $data['palladium'] ?? [],
            'crude_oil' => $data['crude_oil'] ?? 0,
            'psx' => $data['psx'] ?? [],
            'last_updated' => now()->toIso8601String(),
        ], self::TTL);
    }

    /**
     * Get current gold prices from cache.
     */
    public function getCurrentGoldPrices(): ?array
    {
        return Cache::get(self::KEY_GOLD);
    }

    /**
     * Get current silver prices from cache.
     */
    public function getCurrentSilverPrices(): ?array
    {
        return Cache::get(self::KEY_SILVER);
    }

    /**
     * Get current currency rates from cache.
     */
    public function getCurrentCurrencyRates(): ?array
    {
        return Cache::get(self::KEY_CURRENCIES);
    }

    /**
     * Get international rates from cache.
     */
    public function getInternationalRates(): ?array
    {
        return Cache::get(self::KEY_INTERNATIONAL);
    }

    /**
     * Get platinum rates from cache.
     */
    public function getPlatinumRates(): ?array
    {
        return Cache::get(self::KEY_PLATINUM);
    }

    /**
     * Get palladium rates from cache.
     */
    public function getPalladiumRates(): ?array
    {
        return Cache::get(self::KEY_PALLADIUM);
    }

    /**
     * Get crude oil price from cache.
     */
    public function getCrudeOilPrice(): float
    {
        return Cache::get(self::KEY_CRUDE_OIL, 0);
    }

    /**
     * Get PSX data from cache.
     */
    public function getPsxData(): ?array
    {
        return Cache::get(self::KEY_PSX);
    }

    /**
     * Get all cached price data - everything the frontend needs.
     */
    public function getAllPrices(): array
    {
        // Try single-key aggregate first (1 cache read instead of 9)
        $all = Cache::get(self::KEY_ALL);
        if ($all !== null) {
            return $all;
        }

        // Cache is cold. Attempt a synchronous refetch first so the system
        // self-heals even when the 1-minute scheduler isn't running on the host.
        // Cache::lock prevents a thundering herd of concurrent requests from
        // all hitting the upstream source.
        if ($this->tryRefreshSync()) {
            $all = Cache::get(self::KEY_ALL);
            if ($all !== null) {
                return $all;
            }
        }

        // Last resort: serve whatever's in the DB. Cache it with a SHORT TTL
        // (not the full 15-min) so the next request retries the refresh rather
        // than silently locking in stale data for 15 minutes at a time.
        $data = $this->loadFromDatabase();
        if (!empty($data['gold']) || !empty($data['international'])) {
            Cache::put(self::KEY_ALL, $data + ['last_updated' => now()->toIso8601String()], self::STALE_FALLBACK_TTL);
        }

        return $data;
    }

    /**
     * Try to refresh prices synchronously, guarded by a short lock so concurrent
     * requests don't pile on the upstream source.
     */
    private function tryRefreshSync(): bool
    {
        $lock = Cache::lock('prices.refresh.lock', 30);

        if (!$lock->get()) {
            return false;
        }

        try {
            return app(PriceFetcher::class)->fetchAndStore();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('PriceCacheManager: on-demand refresh failed', [
                'error' => $e->getMessage(),
            ]);
            return false;
        } finally {
            $lock->release();
        }
    }

    /**
     * Load latest prices from the database when cache is empty.
     */
    private function loadFromDatabase(): array
    {
        $data = ['gold' => [], 'silver' => [], 'currencies' => [], 'international' => [],
                 'platinum' => [], 'palladium' => [], 'crude_oil' => 0, 'psx' => []];

        // Gold
        $latestGold = MetalPrice::gold()->where('type', '!=', 'international')->orderByDesc('fetched_at')->first();
        if ($latestGold) {
            foreach (MetalPrice::gold()->where('type', '!=', 'international')->where('fetched_at', $latestGold->fetched_at)->get() as $p) {
                $data['gold'][$p->karat][$p->unit] = ['buy' => (float) $p->buy_price, 'sell' => (float) $p->sell_price, 'base' => (float) $p->buy_price];
            }
        }

        // Silver
        $latestSilver = MetalPrice::silver()->where('type', '!=', 'international')->orderByDesc('fetched_at')->first();
        if ($latestSilver) {
            foreach (MetalPrice::silver()->where('type', '!=', 'international')->where('fetched_at', $latestSilver->fetched_at)->get() as $p) {
                $data['silver'][$p->unit] = ['buy' => (float) $p->buy_price, 'sell' => (float) $p->sell_price, 'base' => (float) $p->buy_price];
            }
        }

        // International
        $goldIntl = MetalPrice::where('metal', 'gold')->where('type', 'international')->orderByDesc('fetched_at')->first();
        if ($goldIntl) $data['international']['xau_usd'] = (float) $goldIntl->buy_price;
        $silverIntl = MetalPrice::where('metal', 'silver')->where('type', 'international')->orderByDesc('fetched_at')->first();
        if ($silverIntl) $data['international']['xag_usd'] = (float) $silverIntl->buy_price;

        // Currencies
        $latestRate = CurrencyRate::orderByDesc('fetched_at')->first();
        if ($latestRate) {
            $pairToKey = ['USD/PKR' => 'usd_pkr', 'USD Interbank' => 'usd_interbank', 'GBP/PKR' => 'gbp_pkr',
                          'EUR/PKR' => 'eur_pkr', 'SAR/PKR' => 'sar_pkr', 'AED/PKR' => 'aed_pkr', 'MYR/PKR' => 'myr_pkr'];
            foreach (CurrencyRate::where('fetched_at', $latestRate->fetched_at)->get() as $rate) {
                $key = $pairToKey[$rate->currency_pair] ?? null;
                if ($key) $data['currencies'][$key] = ['buy' => (float) $rate->buy_rate, 'sell' => (float) $rate->sell_rate];
            }
        }

        return $data;
    }

    /**
     * Get the last update timestamp.
     */
    public function getLastUpdated(): ?string
    {
        return Cache::get(self::KEY_LAST_UPDATED);
    }
}
