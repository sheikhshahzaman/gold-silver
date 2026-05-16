<?php

namespace App\Services\PriceEngine;

use App\Models\CurrencyRate;
use App\Models\MetalPrice;
use App\Models\PriceMargin;
use App\Models\ScrapeLog;
use App\Services\PriceEngine\Sources\ExchangeRateSource;
use App\Services\PriceEngine\Sources\FawazCurrencySource;
use App\Services\PriceEngine\Sources\GoldApiSource;
use App\Services\PriceEngine\Sources\PakGoldScraper;
use Illuminate\Support\Facades\Log;

class PriceFetcher
{
    /**
     * Gold karats to generate prices for.
     */
    private const GOLD_KARATS = ['24k', 'rawa', '22k', '21k', '18k'];

    /**
     * Gold unit types to store.
     */
    private const GOLD_UNITS = ['tola', '10_gram', '5_gram', 'gram'];

    /**
     * Silver unit types to store.
     */
    private const SILVER_UNITS = ['tola', '10_tola', '10_gram', '5_gram', 'gram', 'kg'];

    /**
     * Silver karats. Same shape as gold so admin can configure per-karat margins
     * and the frontend can mirror the gold karat selector. 24K is treated as the
     * .999-fine market base; the others are derived using the standard purity
     * ratios from PriceCalculator::deriveAllKaratPrices().
     */
    private const SILVER_KARATS = ['24k', 'rawa', '22k', '21k', '18k'];

    /**
     * Currency pairs to store.
     */
    private const CURRENCY_PAIRS = [
        'usd_pkr' => 'USD/PKR',
        'usd_interbank' => 'USD Interbank',
        'gbp_pkr' => 'GBP/PKR',
        'eur_pkr' => 'EUR/PKR',
        'sar_pkr' => 'SAR/PKR',
        'aed_pkr' => 'AED/PKR',
        'myr_pkr' => 'MYR/PKR',
    ];

    public function __construct(
        private readonly PriceCalculator $calculator,
        private readonly PriceCacheManager $cacheManager,
    ) {}

    /**
     * Fetch prices from sources, calculate derived prices, store in DB and cache.
     *
     * Strategy:
     * 1. Try PakGoldScraper first (direct PKR prices).
     * 2. If PakGold fails, try GoldApiSource + ExchangeRateSource.
     * 3. If those fail, try FawazCurrencySource as last resort.
     */
    public function fetchAndStore(): bool
    {
        $startTime = microtime(true);

        // Strategy 1: PakGold scraper (direct PKR prices)
        $result = $this->tryPakGold();

        // Strategy 2: GoldApi + ExchangeRate
        if ($result === null) {
            $result = $this->tryGoldApiWithExchangeRate();
        }

        // Strategy 3: Fawaz currency API (fallback)
        if ($result === null) {
            $result = $this->tryFawazFallback();
        }

        if ($result === null) {
            Log::error('PriceFetcher: All price sources failed');
            return false;
        }

        try {
            // Load margins from DB
            $margins = $this->loadMargins();

            // Calculate and store gold prices
            $goldCacheData = $this->processGoldPrices($result, $margins);

            // Calculate and store silver prices
            $silverCacheData = $this->processSilverPrices($result, $margins);

            // Store currency rates
            $currencyCacheData = $this->storeCurrencyRates($result);

            // Build international rates for cache and store in DB
            $internationalData = [
                'xau_usd' => $result['xau_usd'] ?? null,
                'xag_usd' => $result['xag_usd'] ?? null,
            ];

            $this->storeInternationalRates($internationalData, $result['source']);

            // Update cache with all data including new fields
            $this->cacheManager->cacheAllPrices([
                'gold' => $goldCacheData,
                'silver' => $silverCacheData,
                'currencies' => $currencyCacheData,
                'international' => $internationalData,
                'platinum' => $result['platinum'] ?? [],
                'palladium' => $result['palladium'] ?? [],
                'crude_oil' => $result['crude_oil'] ?? 0,
                'psx' => $result['psx'] ?? [],
            ]);

            $durationMs = round((microtime(true) - $startTime) * 1000);

            ScrapeLog::create([
                'source' => $result['source'],
                'status' => 'success',
                'error_message' => null,
                'response_time_ms' => $durationMs,
            ]);

            Log::info('PriceFetcher: Prices updated successfully', [
                'source' => $result['source'],
                'duration_ms' => $durationMs,
            ]);

            return true;
        } catch (\Throwable $e) {
            $durationMs = round((microtime(true) - $startTime) * 1000);

            ScrapeLog::create([
                'source' => $result['source'] ?? 'unknown',
                'status' => 'failure',
                'error_message' => $e->getMessage(),
                'response_time_ms' => $durationMs,
            ]);

            Log::error('PriceFetcher: Failed to process and store prices', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return false;
        }
    }

    /**
     * Try fetching from PakGold scraper.
     * Returns normalized result array or null on failure.
     */
    private function tryPakGold(): ?array
    {
        $startTime = microtime(true);

        try {
            $scraper = new PakGoldScraper();
            $data = $scraper->fetch();

            if (empty($data) || empty($data['gold'])) {
                $this->logSourceAttempt('pakgold', 'failure', 'Source not available or no gold data', $startTime);
                return null;
            }

            $result = [
                'source' => 'pakgold',
                'gold_pkr_direct' => true,
                'gold_prices' => $data['gold'],
                'xau_usd' => $data['international']['xau_usd'] ?? null,
                'xag_usd' => $data['international']['xag_usd'] ?? null,
                'usd_pkr' => $data['currency']['usd_pkr']['buy'] ?? null,
                'currency_rates' => $data['currency'] ?? [],
                'platinum' => $data['platinum'] ?? [],
                'palladium' => $data['palladium'] ?? [],
                'crude_oil' => $data['crude_oil'] ?? 0,
                'psx' => $data['psx'] ?? [],
            ];

            // Extract silver data if available
            if (!empty($data['silver'])) {
                $result['silver_pkr_direct'] = true;
                $result['silver_prices'] = $data['silver'];
            }

            $this->logSourceAttempt('pakgold', 'success', null, $startTime);

            return $result;
        } catch (\Throwable $e) {
            $this->logSourceAttempt('pakgold', 'failure', $e->getMessage(), $startTime);
            return null;
        }
    }

    /**
     * Try fetching from GoldApi + ExchangeRate sources.
     */
    private function tryGoldApiWithExchangeRate(): ?array
    {
        $startTime = microtime(true);

        try {
            $goldApi = new GoldApiSource();
            $metalData = $goldApi->fetch();

            if (empty($metalData) || !isset($metalData['xau_usd'])) {
                $this->logSourceAttempt('goldapi', 'failure', 'No metal price data', $startTime);
                return null;
            }

            $exchangeRate = new ExchangeRateSource();
            $rateData = $exchangeRate->fetch();

            if (empty($rateData) || !isset($rateData['usd_pkr'])) {
                $this->logSourceAttempt('exchangerate', 'failure', 'No exchange rate data', $startTime);
                return null;
            }

            $this->logSourceAttempt('goldapi', 'success', null, $startTime);

            return [
                'source' => 'goldapi',
                'gold_pkr_direct' => false,
                'xau_usd' => $metalData['xau_usd'],
                'xag_usd' => $metalData['xag_usd'] ?? null,
                'usd_pkr' => $rateData['usd_pkr'],
                'currency_rates' => $rateData,
            ];
        } catch (\Throwable $e) {
            $this->logSourceAttempt('goldapi', 'failure', $e->getMessage(), $startTime);
            return null;
        }
    }

    /**
     * Try fetching from Fawaz currency API as last resort.
     */
    private function tryFawazFallback(): ?array
    {
        $startTime = microtime(true);

        try {
            $fawaz = new FawazCurrencySource();
            $data = $fawaz->fetch();

            if (empty($data) || !isset($data['xau_usd']) || !isset($data['usd_pkr'])) {
                $this->logSourceAttempt('fawaz', 'failure', 'Insufficient data from Fawaz API', $startTime);
                return null;
            }

            $this->logSourceAttempt('fawaz', 'success', null, $startTime);

            return [
                'source' => 'fawaz',
                'gold_pkr_direct' => false,
                'xau_usd' => $data['xau_usd'],
                'xag_usd' => $data['xag_usd'] ?? null,
                'usd_pkr' => $data['usd_pkr'],
                'currency_rates' => $data,
            ];
        } catch (\Throwable $e) {
            $this->logSourceAttempt('fawaz', 'failure', $e->getMessage(), $startTime);
            return null;
        }
    }

    /**
     * Process and store gold prices for all karats and units.
     */
    private function processGoldPrices(array $result, array $margins): array
    {
        $now = now();
        $cacheData = [];

        // 24K-per-tola anchor for any karat the upstream source doesn't quote directly (e.g. 18K).
        $gold24kPerTola = null;

        if (!empty($result['gold_pkr_direct']) && !empty($result['gold_prices'])) {
            $gold24kPerTola = $result['gold_prices']['24k']['buy_per_tola']
                ?? $result['gold_prices']['24k']['sell_per_tola']
                ?? null;
        }

        if ($gold24kPerTola === null) {
            if (!isset($result['xau_usd']) || !isset($result['usd_pkr'])) {
                return $cacheData;
            }

            $gold24kPerTola = $this->calculator->ounceToPkrPerTola(
                $result['xau_usd'],
                $result['usd_pkr']
            );
        }

        $derivedKaratPrices = $this->calculator->deriveAllKaratPrices($gold24kPerTola);
        $directPrices = $result['gold_prices'] ?? [];

        foreach (self::GOLD_KARATS as $karat) {
            // Prefer the source's direct per-karat buy/sell. Pakistan's bullion market quotes
            // 22K/21K/Rawa with local spreads that are not a clean purity-ratio of 24K, so
            // math-deriving them produces prices several thousand PKR off the actual board rate.
            $directBuy = (float) ($directPrices[$karat]['buy_per_tola'] ?? 0);
            $directSell = (float) ($directPrices[$karat]['sell_per_tola'] ?? 0);

            if ($directBuy > 0) {
                $buyPerTola = $directBuy;
                $sellPerTola = $directSell > 0 ? $directSell : $directBuy;
            } else {
                $buyPerTola = $derivedKaratPrices[$karat];
                $sellPerTola = $derivedKaratPrices[$karat];
            }

            $buyMargin = $margins['gold'][$karat]['buy_margin'] ?? 0;
            $sellMargin = $margins['gold'][$karat]['sell_margin'] ?? 0;

            $buyUnitPrices = $this->calculator->deriveAllUnitPrices($buyPerTola);
            $sellUnitPrices = $this->calculator->deriveAllUnitPrices($sellPerTola);

            foreach (self::GOLD_UNITS as $unit) {
                $buyBase = $buyUnitPrices[$unit];
                $sellBase = $sellUnitPrices[$unit];

                $buyPrice = $this->calculator->applyMargin($buyBase, $buyMargin, $unit);
                $sellPrice = $this->calculator->applyMargin($sellBase, $sellMargin, $unit);

                $cacheData[$karat][$unit] = [
                    'buy' => $buyPrice,
                    'sell' => $sellPrice,
                    'base' => $buyBase,
                ];

                MetalPrice::updateOrCreate(
                    [
                        'metal' => 'gold',
                        'type' => 'local',
                        'karat' => $karat,
                        'unit' => $unit,
                        'fetched_at' => $now,
                    ],
                    [
                        'buy_price' => $buyPrice,
                        'sell_price' => $sellPrice,
                        'currency' => 'PKR',
                        'source' => $result['source'],
                    ],
                );
            }
        }

        return $cacheData;
    }

    /**
     * Process and store silver prices for all units.
     */
    private function processSilverPrices(array $result, array $margins): array
    {
        $now = now();
        $cacheData = [];

        // Establish the 24K (pure) anchor for silver.
        $silver24kBuyPerTola = null;
        $silver24kSellPerTola = null;

        if (!empty($result['silver_pkr_direct']) && !empty($result['silver_prices'])) {
            // PakGold's localSilverBuy/Sell is the pure-silver retail market rate.
            $silver24kBuyPerTola = $result['silver_prices']['reti']['buy_per_tola']
                ?? $result['silver_prices']['pees']['buy_per_tola']
                ?? null;
            $silver24kSellPerTola = $result['silver_prices']['reti']['sell_per_tola']
                ?? $result['silver_prices']['pees']['sell_per_tola']
                ?? null;
        }

        if ($silver24kBuyPerTola === null && isset($result['xag_usd']) && isset($result['usd_pkr'])) {
            $silver24kBuyPerTola = $this->calculator->ounceToPkrPerTola(
                $result['xag_usd'],
                $result['usd_pkr']
            );
        }

        if ($silver24kBuyPerTola === null || $silver24kBuyPerTola <= 0) {
            return $cacheData;
        }

        // Silver doesn't have a real per-karat market (the source only quotes a
        // single value), so we derive the other karats with the same purity
        // multipliers gold uses. The output mirrors the gold structure exactly
        // so the frontend can treat both metals uniformly.
        $silverBuyByKarat = $this->calculator->deriveAllKaratPrices($silver24kBuyPerTola);
        $silverSellByKarat = $silver24kSellPerTola
            ? $this->calculator->deriveAllKaratPrices($silver24kSellPerTola)
            : $silverBuyByKarat;

        foreach (self::SILVER_KARATS as $karat) {
            $buyPerTola = $silverBuyByKarat[$karat];
            $sellPerTola = $silverSellByKarat[$karat];

            // Per-karat silver margins (added in 2026_05_17 migration). Falls
            // back to the legacy single-row silver margin if a karat-specific
            // row isn't configured yet.
            $buyMargin = $margins['silver'][$karat]['buy_margin']
                ?? $margins['silver']['']['buy_margin']
                ?? 0;
            $sellMargin = $margins['silver'][$karat]['sell_margin']
                ?? $margins['silver']['']['sell_margin']
                ?? 0;

            $buyUnitPrices = $this->calculator->deriveAllUnitPrices($buyPerTola);
            $sellUnitPrices = $this->calculator->deriveAllUnitPrices($sellPerTola);

            foreach (self::SILVER_UNITS as $unit) {
                $buyBase = $buyUnitPrices[$unit];
                $sellBase = $sellUnitPrices[$unit];

                $buyPrice = $this->calculator->applyMargin($buyBase, $buyMargin, $unit);
                $sellPrice = $this->calculator->applyMargin($sellBase, $sellMargin, $unit);

                $cacheData[$karat][$unit] = [
                    'buy' => $buyPrice,
                    'sell' => $sellPrice,
                    'base' => $buyBase,
                ];

                MetalPrice::updateOrCreate(
                    [
                        'metal' => 'silver',
                        'type' => 'local',
                        'karat' => $karat,
                        'unit' => $unit,
                        'fetched_at' => $now,
                    ],
                    [
                        'buy_price' => $buyPrice,
                        'sell_price' => $sellPrice,
                        'currency' => 'PKR',
                        'source' => $result['source'],
                    ],
                );
            }
        }

        return $cacheData;
    }

    /**
     * Store currency rates in the database.
     * Currency data from PakGold now has buy/sell pairs.
     * For fallback sources, rates are wrapped in buy/sell format with the same value.
     */
    private function storeCurrencyRates(array $result): array
    {
        $now = now();
        $cacheData = [];
        $source = $result['source'];
        $rates = $result['currency_rates'] ?? [];

        foreach (self::CURRENCY_PAIRS as $key => $pairLabel) {
            $rateValue = $rates[$key] ?? null;

            if ($rateValue === null) {
                continue;
            }

            // Normalize: if it's already a buy/sell array, use it; otherwise wrap scalar
            if (is_array($rateValue)) {
                $buyRate = $rateValue['buy'] ?? 0;
                $sellRate = $rateValue['sell'] ?? 0;
            } else {
                // Scalar value from fallback sources
                $buyRate = floatval($rateValue);
                $sellRate = floatval($rateValue);
            }

            if ($buyRate <= 0 && $sellRate <= 0) {
                continue;
            }

            $cacheData[$key] = ['buy' => $buyRate, 'sell' => $sellRate];

            CurrencyRate::updateOrCreate(
                [
                    'currency_pair' => $pairLabel,
                    'type' => 'open_market',
                    'fetched_at' => $now,
                ],
                [
                    'buy_rate' => $buyRate,
                    'sell_rate' => $sellRate,
                    'source' => $source,
                ],
            );
        }

        return $cacheData;
    }

    /**
     * Load price margins from the database, organized by metal and karat.
     *
     * @return array{gold: array, silver: array}
     */
    private function loadMargins(): array
    {
        $margins = ['gold' => [], 'silver' => []];

        $records = PriceMargin::all();

        foreach ($records as $record) {
            $margins[$record->metal][strtolower($record->karat ?? '')] = [
                'buy_margin' => (float) $record->buy_margin,
                'sell_margin' => (float) $record->sell_margin,
            ];
        }

        return $margins;
    }

    /**
     * Store international XAU/XAG USD rates in the database for fallback.
     */
    private function storeInternationalRates(array $data, string $source): void
    {
        $now = now();

        if (!empty($data['xau_usd'])) {
            MetalPrice::updateOrCreate(
                [
                    'metal' => 'gold',
                    'type' => 'international',
                    'karat' => '24k',
                    'unit' => 'ounce',
                    'fetched_at' => $now,
                ],
                [
                    'buy_price' => $data['xau_usd'],
                    'sell_price' => $data['xau_usd'] + 0.50,
                    'currency' => 'USD',
                    'source' => $source,
                ],
            );
        }

        if (!empty($data['xag_usd'])) {
            MetalPrice::updateOrCreate(
                [
                    'metal' => 'silver',
                    'type' => 'international',
                    'karat' => null,
                    'unit' => 'ounce',
                    'fetched_at' => $now,
                ],
                [
                    'buy_price' => $data['xag_usd'],
                    'sell_price' => $data['xag_usd'] + 0.03,
                    'currency' => 'USD',
                    'source' => $source,
                ],
            );
        }
    }

    /**
     * Log a source fetch attempt to the scrape_logs table.
     */
    private function logSourceAttempt(string $source, string $status, ?string $message, float $startTime): void
    {
        $durationMs = round((microtime(true) - $startTime) * 1000);

        try {
            ScrapeLog::create([
                'source' => $source,
                'status' => $status,
                'error_message' => $message,
                'response_time_ms' => $durationMs,
            ]);
        } catch (\Throwable $e) {
            Log::warning('PriceFetcher: Failed to write scrape log', [
                'source' => $source,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
