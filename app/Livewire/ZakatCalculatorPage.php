<?php

namespace App\Livewire;

use App\Services\Rates\RatesProvider;
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
        $allPrices = app(RatesProvider::class)->getAllPrices();

        $goldData = $allPrices['gold'] ?: [];
        $silverData = $allPrices['silver'] ?: [];
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

    public function render()
    {
        return view('livewire.zakat-calculator-page');
    }
}
