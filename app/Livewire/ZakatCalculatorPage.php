<?php

namespace App\Livewire;

use App\Models\MetalPrice;
use App\Services\PriceEngine\PriceCacheManager;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Zakat Calculator - Islamabad Bullion Exchange')]
class ZakatCalculatorPage extends Component
{
    /**
     * Gold prices keyed by karat => ['buy' => per_gram_buy, 'sell' => per_gram_sell].
     */
    public array $goldPrices = [];

    /**
     * Silver price per gram: ['buy' => ..., 'sell' => ...].
     */
    public array $silverPricePerGram = [];

    /**
     * Timestamp of the last price update.
     */
    public ?string $lastUpdated = null;

    public function mount(): void
    {
        $this->loadPrices();
    }

    /**
     * Load current gold and silver prices from cache, falling back to database.
     */
    private function loadPrices(): void
    {
        $cacheManager = app(PriceCacheManager::class);
        $allPrices = $cacheManager->getAllPrices();

        $goldData = $allPrices['gold'] ?: $this->loadGoldFromDatabase();
        $silverData = $allPrices['silver'] ?: $this->loadSilverFromDatabase();
        $this->lastUpdated = $allPrices['last_updated'];

        // Extract per-gram buy/sell prices for each karat
        $karats = ['24k', '22k', '21k', '18k'];
        foreach ($karats as $karat) {
            $this->goldPrices[$karat] = [
                'buy' => $goldData[$karat]['gram']['buy'] ?? 0,
                'sell' => $goldData[$karat]['gram']['sell'] ?? 0,
            ];
        }

        // Also include spot (same as 24k for calculation purposes)
        $this->goldPrices['spot'] = $this->goldPrices['24k'];

        // Silver per gram
        $this->silverPricePerGram = [
            'buy' => $silverData['gram']['buy'] ?? 0,
            'sell' => $silverData['gram']['sell'] ?? 0,
        ];
    }

    /**
     * Fallback: load latest gold prices from the database.
     */
    private function loadGoldFromDatabase(): array
    {
        $goldData = [];

        $latestGold = MetalPrice::gold()->orderByDesc('fetched_at')->first();

        if (! $latestGold) {
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
     * Fallback: load latest silver prices from the database.
     */
    private function loadSilverFromDatabase(): array
    {
        $silverData = [];

        $latestSilver = MetalPrice::silver()->orderByDesc('fetched_at')->first();

        if (! $latestSilver) {
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

    public function render()
    {
        return view('livewire.zakat-calculator-page');
    }
}
