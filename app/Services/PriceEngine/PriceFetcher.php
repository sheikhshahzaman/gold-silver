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

        // Strategy 1: PakGold scraper (direct PKR prices) — still needed for the
        // international spot (USD/oz), currency rates, platinum/palladium, crude
        // oil and PSX. Local gold/silver PKR prices are always admin-set below.
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
            // Local gold/silver are always admin-set, so a scraper outage never
            // blocks publishing them — fall back to the last known spot/currency
            // data in the DB and keep going.
            $result = $this->databaseFallbackResult();
        }

        try {
            // Gold & silver local PKR prices are always the admin's direct entry
            // from the Gold & Silver Prices page — no live market, no margin math.
            $goldCacheData = $this->processGoldPrices($this->manualGoldDirectPrices());
            $silverCacheData = $this->processSilverPrices($this->manualSilverDirectPrices());

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
     * Process and store gold prices for all karats and units, straight from the
     * admin's direct per-karat buy/sell entry (Gold & Silver Prices page) — no
     * margin math. Per-gram/10-gram/etc are derived from the per-tola value.
     */
    private function processGoldPrices(array $directPrices): array
    {
        $now = now();
        $cacheData = [];

        foreach (self::GOLD_KARATS as $karat) {
            $buyPerTola = (float) ($directPrices[$karat]['buy_per_tola'] ?? 0);
            $sellPerTola = (float) ($directPrices[$karat]['sell_per_tola'] ?? 0);

            // Nothing entered for this karat yet — skip rather than publish Rs 0.
            if ($buyPerTola <= 0) {
                continue;
            }
            if ($sellPerTola <= 0) {
                $sellPerTola = $buyPerTola;
            }

            $buyUnitPrices = $this->calculator->deriveAllUnitPrices($buyPerTola);
            $sellUnitPrices = $this->calculator->deriveAllUnitPrices($sellPerTola);

            foreach (self::GOLD_UNITS as $unit) {
                $buyPrice = $buyUnitPrices[$unit];
                $sellPrice = $sellUnitPrices[$unit];

                $cacheData[$karat][$unit] = [
                    'buy' => $buyPrice,
                    'sell' => $sellPrice,
                    'base' => $buyPrice,
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
                        'source' => 'manual',
                    ],
                );
            }
        }

        return $cacheData;
    }

    /**
     * Process and store silver prices for all units, straight from the admin's
     * direct per-unit buy/sell entry (Gold & Silver Prices page) — no margin math.
     */
    private function processSilverPrices(array $directPrices): array
    {
        $now = now();
        $cacheData = [];

        foreach (self::SILVER_UNITS as $unit) {
            $buyPrice = (float) ($directPrices[$unit]['buy'] ?? 0);
            $sellPrice = (float) ($directPrices[$unit]['sell'] ?? 0);

            // Nothing entered for this unit yet — skip rather than publish Rs 0.
            if ($buyPrice <= 0) {
                continue;
            }
            if ($sellPrice <= 0) {
                $sellPrice = $buyPrice;
            }

            $cacheData[$unit] = [
                'buy' => $buyPrice,
                'sell' => $sellPrice,
                'base' => $buyPrice,
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
                    'source' => 'manual',
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
     * Where local gold/silver rates come from. Always 'manual' now — the admin
     * types final buy/sell prices directly on the Gold & Silver Prices page.
     * Spot USD/oz is always live regardless. Kept for the /api/prices response.
     */
    private function rateMode(): string
    {
        return 'manual';
    }

    /**
     * Build the gold "direct price" array (per-karat, buy/sell per tola) from the
     * admin's Gold & Silver Prices page. A karat left blank (or zeroed) is
     * derived from 24K by purity, so the admin can set just 24K if they prefer.
     *
     * @return array<string, array{buy_per_tola: float, sell_per_tola: float}>
     */
    private function manualGoldDirectPrices(): array
    {
        $rows = PriceMargin::where('metal', 'gold')->get()
            ->keyBy(fn ($r) => strtolower($r->karat ?? ''));

        $base24k = $rows->get('24k');
        $base24kBuy = (float) ($base24k?->manual_buy_price ?? 0);
        $base24kSell = (float) ($base24k?->manual_sell_price ?? 0);

        $prices = [];

        foreach (self::GOLD_KARATS as $karat) {
            $row = $rows->get($karat);
            $buyPerTola = (float) ($row?->manual_buy_price ?? 0);
            $sellPerTola = (float) ($row?->manual_sell_price ?? 0);

            if ($buyPerTola <= 0 && $base24kBuy > 0) {
                $buyPerTola = $this->calculator->applyKaratPurity($base24kBuy, $karat);
            }
            if ($sellPerTola <= 0 && $base24kSell > 0) {
                $sellPerTola = $this->calculator->applyKaratPurity($base24kSell, $karat);
            }

            if ($buyPerTola > 0 || $sellPerTola > 0) {
                $prices[$karat] = [
                    'buy_per_tola' => $buyPerTola,
                    'sell_per_tola' => $sellPerTola,
                ];
            }
        }

        return $prices;
    }

    /**
     * Admin-set silver buy/sell price per unit, from the Gold & Silver Prices
     * page. A unit left blank (or zeroed) is derived from the 1-tola rate, so
     * the admin can set just tola if they prefer.
     *
     * @return array<string, array{buy: float, sell: float}>
     */
    private function manualSilverDirectPrices(): array
    {
        $rows = PriceMargin::where('metal', 'silver')->get()->keyBy('unit');

        $tolaRow = $rows->get('tola');
        $tolaBuy = (float) ($tolaRow?->manual_buy_price ?? 0);
        $tolaSell = (float) ($tolaRow?->manual_sell_price ?? 0);
        $derivedBuy = $tolaBuy > 0 ? $this->calculator->deriveAllUnitPrices($tolaBuy) : [];
        $derivedSell = $tolaSell > 0 ? $this->calculator->deriveAllUnitPrices($tolaSell) : [];

        $out = [];
        foreach (self::SILVER_UNITS as $unit) {
            $row = $rows->get($unit);
            $buy = (float) ($row?->manual_buy_price ?? 0);
            $sell = (float) ($row?->manual_sell_price ?? 0);

            if ($buy <= 0) {
                $buy = (float) ($derivedBuy[$unit] ?? 0);
            }
            if ($sell <= 0) {
                $sell = (float) ($derivedSell[$unit] ?? 0);
            }

            $out[$unit] = ['buy' => $buy, 'sell' => $sell];
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
