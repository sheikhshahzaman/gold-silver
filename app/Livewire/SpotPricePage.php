<?php

namespace App\Livewire;

use App\Models\Setting;
use App\Services\Rates\RatesProvider;
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
        $allPrices = app(RatesProvider::class)->getAllPrices();

        $this->goldPrices = $allPrices['gold'] ?: [];
        $this->silverPrices = $allPrices['silver'] ?: [];
        $this->internationalRates = $allPrices['international'] ?: [];
        $this->currencyRates = $allPrices['currencies'] ?: [];
        $this->platinumRates = $allPrices['platinum'] ?: [];
        $this->palladiumRates = $allPrices['palladium'] ?: [];
        $this->crudeOilPrice = $allPrices['crude_oil'] ?: 0;
        $this->psxData = $allPrices['psx'] ?: [];
        $this->lastUpdated = $allPrices['last_updated'];
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
