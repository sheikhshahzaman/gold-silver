<?php

namespace App\Livewire;

use App\Models\CurrencyRate;
use App\Models\MetalPrice;
use App\Models\Setting;
use App\Services\PriceEngine\PriceCacheManager;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Live Gold & Silver Prices in Pakistan - Islamabad Bullion Exchange')]
class SpotPricePage extends Component
{
    /**
     * Gold prices keyed by karat then unit.
     * Structure: ['24k' => ['tola' => ['buy' => ..., 'sell' => ...], ...], ...]
     */
    public array $goldPrices = [];

    /**
     * Silver prices keyed by unit.
     * Structure: ['tola' => ['buy' => ..., 'sell' => ...], ...]
     */
    public array $silverPrices = [];

    /**
     * International spot rates.
     * Structure: ['xau_usd' => ..., 'xag_usd' => ...]
     */
    public array $internationalRates = [];

    /**
     * Currency exchange rates.
     * Structure: ['usd_pkr' => ['buy' => ..., 'sell' => ...], ...]
     */
    public array $currencyRates = [];

    /**
     * Platinum rates.
     * Structure: ['international' => ..., 'local' => ...]
     */
    public array $platinumRates = [];

    /**
     * Palladium rates.
     * Structure: ['international' => ..., 'local' => ...]
     */
    public array $palladiumRates = [];

    /**
     * Crude oil price in USD.
     */
    public float $crudeOilPrice = 0;

    /**
     * PSX / KSE data.
     * Structure: ['index' => ..., 'high' => ..., 'low' => ..., 'change' => ...]
     */
    public array $psxData = [];

    /**
     * Timestamp of the last cache update.
     */
    public ?string $lastUpdated = null;

    /**
     * Currently selected gold unit tab.
     */
    public string $selectedUnit = 'tola';

    /**
     * International gold ASK spread, as a percentage of BID (e.g. 0.05 = 0.05%).
     * Loaded once in mount() from settings.
     */
    public float $goldSpreadPct = 0.05;

    /**
     * International silver ASK spread, as a percentage of BID (e.g. 0.1 = 0.1%).
     * Loaded once in mount() from settings.
     */
    public float $silverSpreadPct = 0.1;

    /**
     * Map of unit keys to display multiplier info.
     */
    private const UNIT_MAP = [
        'tola' => ['label' => '1 Tola', 'key' => 'tola'],
        '10_gram' => ['label' => '10 Gram', 'key' => '10_gram'],
        '5_gram' => ['label' => '5 Gram', 'key' => '5_gram'],
        'gram' => ['label' => '1 Gram', 'key' => 'gram'],
    ];

    public function mount(): void
    {
        $this->goldSpreadPct = (float) Setting::get('international_spread_gold_pct', 0.05);
        $this->silverSpreadPct = (float) Setting::get('international_spread_silver_pct', 0.1);
        $this->loadPrices();
    }

    /**
     * Change the selected gold unit tab.
     */
    public function selectUnit(string $unit): void
    {
        if (array_key_exists($unit, self::UNIT_MAP)) {
            $this->selectedUnit = $unit;
        }
    }

    /**
     * Called by wire:poll to refresh data from cache.
     */
    public function refresh(): void
    {
        $this->loadPrices();
    }

    /**
     * Load all price data from cache, falling back to database if cache is empty.
     */
    private function loadPrices(): void
    {
        $cacheManager = app(PriceCacheManager::class);
        $allPrices = $cacheManager->getAllPrices();

        $this->goldPrices = $allPrices['gold'] ?: $this->loadGoldFromDatabase();
        $this->silverPrices = $allPrices['silver'] ?: $this->loadSilverFromDatabase();
        $this->internationalRates = $allPrices['international'] ?: $this->loadInternationalFromDatabase();
        $this->currencyRates = $allPrices['currencies'] ?: $this->loadCurrenciesFromDatabase();
        $this->platinumRates = $allPrices['platinum'] ?: [];
        $this->palladiumRates = $allPrices['palladium'] ?: [];
        $this->crudeOilPrice = $allPrices['crude_oil'] ?: 0;
        $this->psxData = $allPrices['psx'] ?: [];
        $this->lastUpdated = $allPrices['last_updated'];
    }

    /**
     * Fallback: load latest gold prices from the database if cache is empty.
     */
    private function loadGoldFromDatabase(): array
    {
        $goldData = [];

        // Get the most recent fetched_at timestamp for gold
        $latestGold = MetalPrice::gold()->orderByDesc('fetched_at')->first();

        if (!$latestGold) {
            return $goldData;
        }

        $prices = MetalPrice::gold()
            ->where('fetched_at', $latestGold->fetched_at)
            ->get();

        foreach ($prices as $price) {
            $goldData[$price->karat][$price->unit] = [
                'buy' => (float) $price->buy_price,
                'sell' => (float) $price->sell_price,
            ];
        }

        return $goldData;
    }

    /**
     * Fallback: load latest silver prices from the database if cache is empty.
     */
    private function loadSilverFromDatabase(): array
    {
        $silverData = [];

        $latestSilver = MetalPrice::silver()->orderByDesc('fetched_at')->first();

        if (!$latestSilver) {
            return $silverData;
        }

        $prices = MetalPrice::silver()
            ->where('fetched_at', $latestSilver->fetched_at)
            ->get();

        foreach ($prices as $price) {
            $silverData[$price->unit] = [
                'buy' => (float) $price->buy_price,
                'sell' => (float) $price->sell_price,
            ];
        }

        return $silverData;
    }

    /**
     * Fallback: load latest currency rates from the database if cache is empty.
     * Returns data in buy/sell format.
     */
    private function loadCurrenciesFromDatabase(): array
    {
        $currencyData = [];

        $latestRate = CurrencyRate::orderByDesc('fetched_at')->first();

        if (!$latestRate) {
            return $currencyData;
        }

        $rates = CurrencyRate::where('fetched_at', $latestRate->fetched_at)->get();

        // Map currency_pair labels back to cache keys
        $pairToKey = [
            'USD/PKR' => 'usd_pkr',
            'USD Interbank' => 'usd_interbank',
            'GBP/PKR' => 'gbp_pkr',
            'EUR/PKR' => 'eur_pkr',
            'SAR/PKR' => 'sar_pkr',
            'AED/PKR' => 'aed_pkr',
            'MYR/PKR' => 'myr_pkr',
        ];

        foreach ($rates as $rate) {
            $key = $pairToKey[$rate->currency_pair] ?? null;
            if ($key) {
                $currencyData[$key] = [
                    'buy' => (float) $rate->buy_rate,
                    'sell' => (float) $rate->sell_rate,
                ];
            }
        }

        return $currencyData;
    }

    /**
     * Fallback: load international rates from the database if cache is empty.
     */
    private function loadInternationalFromDatabase(): array
    {
        $data = [];

        $latestGoldIntl = MetalPrice::where('metal', 'gold')
            ->where('type', 'international')
            ->orderByDesc('fetched_at')
            ->first();

        if ($latestGoldIntl) {
            $data['xau_usd'] = (float) $latestGoldIntl->buy_price;
        }

        $latestSilverIntl = MetalPrice::where('metal', 'silver')
            ->where('type', 'international')
            ->orderByDesc('fetched_at')
            ->first();

        if ($latestSilverIntl) {
            $data['xag_usd'] = (float) $latestSilverIntl->buy_price;
        }

        return $data;
    }

    /**
     * Get the selected unit label for display.
     */
    public function getSelectedUnitLabelProperty(): string
    {
        return self::UNIT_MAP[$this->selectedUnit]['label'] ?? '1 Tola';
    }

    /**
     * Get gold tab options for the view.
     */
    public function getGoldTabsProperty(): array
    {
        return [
            'tola' => '1 Tola',
            '10_gram' => '10 Gram',
            '5_gram' => '5 Gram',
            'gram' => '1 Gram',
        ];
    }

    /**
     * Get the gold price for a specific karat in the currently selected unit.
     */
    public function getGoldPrice(string $karat, string $type = 'buy'): ?float
    {
        return $this->goldPrices[$karat][$this->selectedUnit][$type] ?? null;
    }

    /**
     * International gold BID/ASK quote in USD/oz. ASK = BID * (1 + spread/100).
     * Returns null if we don't have an upstream spot price to build on.
     *
     * @return array{bid: float, ask: float}|null
     */
    public function getGoldQuoteProperty(): ?array
    {
        $bid = $this->internationalRates['xau_usd'] ?? null;
        if (!$bid) {
            return null;
        }

        return [
            'bid' => (float) $bid,
            'ask' => (float) $bid * (1 + $this->goldSpreadPct / 100),
        ];
    }

    /**
     * International silver BID/ASK quote in USD/oz. ASK = BID * (1 + spread/100).
     * Returns null if we don't have an upstream spot price to build on.
     *
     * @return array{bid: float, ask: float}|null
     */
    public function getSilverQuoteProperty(): ?array
    {
        $bid = $this->internationalRates['xag_usd'] ?? null;
        if (!$bid) {
            return null;
        }

        return [
            'bid' => (float) $bid,
            'ask' => (float) $bid * (1 + $this->silverSpreadPct / 100),
        ];
    }

    public function render()
    {
        return view('livewire.spot-price-page');
    }
}
