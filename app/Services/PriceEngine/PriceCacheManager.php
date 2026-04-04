<?php

namespace App\Services\PriceEngine;

use Illuminate\Support\Facades\Cache;

class PriceCacheManager
{
    /**
     * Cache TTL in seconds (15 minutes).
     */
    private const TTL = 900;

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
        return [
            'gold' => $this->getCurrentGoldPrices() ?? [],
            'silver' => $this->getCurrentSilverPrices() ?? [],
            'currencies' => $this->getCurrentCurrencyRates() ?? [],
            'international' => $this->getInternationalRates() ?? [],
            'platinum' => $this->getPlatinumRates() ?? [],
            'palladium' => $this->getPalladiumRates() ?? [],
            'crude_oil' => $this->getCrudeOilPrice(),
            'psx' => $this->getPsxData() ?? [],
            'last_updated' => $this->getLastUpdated(),
        ];
    }

    /**
     * Get the last update timestamp.
     */
    public function getLastUpdated(): ?string
    {
        return Cache::get(self::KEY_LAST_UPDATED);
    }
}
