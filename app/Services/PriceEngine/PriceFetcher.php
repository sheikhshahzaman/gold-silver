<?php

namespace App\Services\PriceEngine;

use App\Models\CurrencyRate;
use App\Models\MetalPrice;
use App\Models\PriceMargin;
use App\Models\ScrapeLog;
use App\Models\Setting;
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

        $rateMode = $this->rateMode();

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
            // Manual mode doesn't need a live source for gold/silver (they're
            // admin-set), so fall back to the last spot/currency data in the DB
            // and keep publishing. Only hard-fail when we're in live mode.
            if ($rateMode !== 'manual') {
                Log::error('PriceFetcher: All price sources failed');
                return false;
            }
            $result = $this->databaseFallbackResult();
        }

        try {
            // Load margins from DB
            $margins = $this->loadMargins();

            if ($rateMode === 'manual') {
                // Gold: push the admin's per-karat per-tola rates through the same
                // "direct price" path the scraper uses, so unit derivation and
                // margins behave identically.
                $result['gold_pkr_direct'] = true;
                $result['gold_prices'] = $this->manualGoldDirectPrices();

                $goldCacheData = $this->processGoldPrices($result, $margins);

                // Silver: admin sets each unit's base directly (no tola derivation).
                $silverCacheData = $this->processSilverPrices($result, $margins, $this->manualSilverPerUnit());
            } else {
                // Calculate and store gold prices
                $goldCacheData = $this->processGoldPrices($result, $margins);

                // Calculate and store silver prices
                $silverCacheData = $this->processSilverPrices($result, $margins);
            }

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
     *
     * @param array|null $manualUnitBase When provided (manual mode), each unit's
     *                                   base price is taken directly from this map
     *                                   instead of being derived from a per-tola rate.
     */
    private function processSilverPrices(array $result, array $margins, ?array $manualUnitBase = null): array
    {
        $now = now();
        $cacheData = [];

        // Silver margins are configured per unit (1 KG, 10 Tola, 1 Tola, 10 Gram,
        // 5 Gram, 1 Gram). Each row's value is added directly to that unit's price.
        $silverMarginsByUnit = $margins['silver_by_unit'] ?? [];

        if ($manualUnitBase !== null) {
            // Manual mode: admin-set base price for each unit, used as-is.
            $unitPrices = $manualUnitBase;
            $sellUnitPrices = $manualUnitBase;
        } else {
            $silverPerTola = null;

            if (!empty($result['silver_pkr_direct']) && !empty($result['silver_prices'])) {
                // Silver from PakGold now has 'reti' and 'pees' sub-keys
                $silverPerTola = $result['silver_prices']['reti']['buy_per_tola']
                    ?? $result['silver_prices']['pees']['buy_per_tola']
                    ?? null;
            }

            if ($silverPerTola === null && isset($result['xag_usd']) && isset($result['usd_pkr'])) {
                $silverPerTola = $this->calculator->ounceToPkrPerTola(
                    $result['xag_usd'],
                    $result['usd_pkr']
                );
            }

            if ($silverPerTola === null || $silverPerTola <= 0) {
                return $cacheData;
            }

            // Derive base buy/sell unit prices (no margin yet).
            $unitPrices = $this->calculator->deriveAllUnitPrices($silverPerTola);

            $silverSellPerTola = null;
            if (!empty($result['silver_pkr_direct']) && !empty($result['silver_prices'])) {
                $silverSellPerTola = $result['silver_prices']['reti']['sell_per_tola']
                    ?? $result['silver_prices']['pees']['sell_per_tola']
                    ?? null;
            }
            $sellUnitPrices = $silverSellPerTola
                ? $this->calculator->deriveAllUnitPrices($silverSellPerTola)
                : $unitPrices;
        }

        foreach (self::SILVER_UNITS as $unit) {
            $basePrice = (float) ($unitPrices[$unit] ?? 0);
            $baseSellPrice = (float) ($sellUnitPrices[$unit] ?? $basePrice);

            // Skip units without a usable base price (e.g. unset manual value).
            if ($basePrice <= 0) {
                continue;
            }

            // Per-unit margin, applied directly to that unit's price.
            $buyMarginForUnit  = $silverMarginsByUnit[$unit]['buy_margin']  ?? 0;
            $sellMarginForUnit = $silverMarginsByUnit[$unit]['sell_margin'] ?? 0;

            $buyPrice  = round($basePrice + $buyMarginForUnit, 2);
            $sellPrice = round($baseSellPrice + $sellMarginForUnit, 2);

            $cacheData[$unit] = [
                'buy' => $buyPrice,
                'sell' => $sellPrice,
                'base' => $basePrice,
            ];

            MetalPrice::updateOrCreate(
                [
                    'metal' => 'silver',
                    'type' => 'local',
                    'karat' => null,
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
        // Gold margins are keyed by karat (one row covers all units via per-tola conversion).
        // Silver margins are keyed by unit (one row per weight category: tola, kg, 10_gram, ...).
        $margins = ['gold' => [], 'silver_by_unit' => []];

        foreach (PriceMargin::all() as $record) {
            if ($record->metal === 'gold') {
                $margins['gold'][strtolower($record->karat ?? '')] = [
                    'buy_margin' => (float) $record->buy_margin,
                    'sell_margin' => (float) $record->sell_margin,
                ];
            } elseif ($record->metal === 'silver') {
                $margins['silver_by_unit'][$record->unit ?? ''] = [
                    'buy_margin' => (float) $record->buy_margin,
                    'sell_margin' => (float) $record->sell_margin,
                ];
            }
        }

        return $margins;
    }

    /**
     * Where local gold/silver rates come from: 'manual' (admin-set) or 'live' (market).
     * Defaults to 'manual'. Spot USD/oz is always live regardless.
     */
    private function rateMode(): string
    {
        return Setting::get('rate_mode', 'manual') === 'live' ? 'live' : 'manual';
    }

    /**
     * Build the gold "direct price" array (per-karat, buy/sell per tola) from the
     * admin's manual rates. A karat left blank is derived from 24K by purity, so
     * the admin can set just 24K if they prefer.
     *
     * @return array<string, array{buy_per_tola: float, sell_per_tola: float}>
     */
    private function manualGoldDirectPrices(): array
    {
        $base24k = (float) Setting::get('manual_gold_24k', 0);
        $prices = [];

        foreach (self::GOLD_KARATS as $karat) {
            $perTola = (float) Setting::get('manual_gold_' . $karat, 0);

            if ($perTola <= 0 && $base24k > 0) {
                $perTola = $this->calculator->applyKaratPurity($base24k, $karat);
            }

            if ($perTola > 0) {
                $prices[$karat] = [
                    'buy_per_tola' => $perTola,
                    'sell_per_tola' => $perTola,
                ];
            }
        }

        return $prices;
    }

    /**
     * Admin-set silver base price per unit. A unit left blank is derived from the
     * 1-tola rate, so the admin can set just tola if they prefer.
     *
     * @return array<string, float>
     */
    private function manualSilverPerUnit(): array
    {
        $baseTola = (float) Setting::get('manual_silver_tola', 0);
        $derived = $baseTola > 0 ? $this->calculator->deriveAllUnitPrices($baseTola) : [];

        $out = [];
        foreach (self::SILVER_UNITS as $unit) {
            $value = (float) Setting::get('manual_silver_' . $unit, 0);

            if ($value <= 0) {
                $value = (float) ($derived[$unit] ?? 0);
            }

            $out[$unit] = $value;
        }

        return $out;
    }

    /**
     * Build a minimal result from the last values stored in the database. Used in
     * manual mode when every upstream source is unavailable, so admin-set
     * gold/silver can still publish alongside the last known spot/currency data.
     */
    private function databaseFallbackResult(): array
    {
        $result = ['source' => 'manual', 'currency_rates' => []];

        $goldIntl = MetalPrice::where('metal', 'gold')->where('type', 'international')
            ->orderByDesc('fetched_at')->first();
        if ($goldIntl) {
            $result['xau_usd'] = (float) $goldIntl->buy_price;
        }

        $silverIntl = MetalPrice::where('metal', 'silver')->where('type', 'international')
            ->orderByDesc('fetched_at')->first();
        if ($silverIntl) {
            $result['xag_usd'] = (float) $silverIntl->buy_price;
        }

        $latestRate = CurrencyRate::orderByDesc('fetched_at')->first();
        if ($latestRate) {
            $pairToKey = [
                'USD/PKR' => 'usd_pkr', 'USD Interbank' => 'usd_interbank', 'GBP/PKR' => 'gbp_pkr',
                'EUR/PKR' => 'eur_pkr', 'SAR/PKR' => 'sar_pkr', 'AED/PKR' => 'aed_pkr', 'MYR/PKR' => 'myr_pkr',
            ];
            foreach (CurrencyRate::where('fetched_at', $latestRate->fetched_at)->get() as $rate) {
                $key = $pairToKey[$rate->currency_pair] ?? null;
                if ($key) {
                    $result['currency_rates'][$key] = [
                        'buy' => (float) $rate->buy_rate,
                        'sell' => (float) $rate->sell_rate,
                    ];
                    if ($key === 'usd_pkr') {
                        $result['usd_pkr'] = (float) $rate->buy_rate;
                    }
                }
            }
        }

        return $result;
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
