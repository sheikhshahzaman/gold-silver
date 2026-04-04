<?php

namespace App\Livewire;

use App\Models\MetalPrice;
use App\Models\Order;
use App\Services\PriceEngine\PriceCacheManager;
use App\Services\PriceEngine\PriceCalculator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Buy Gold & Silver - PakGold Rates')]
class BuyPage extends Component
{
    /**
     * Current step in the wizard (1 or 2).
     */
    public int $step = 1;

    /**
     * Selected metal: 'gold' or 'silver'.
     */
    public string $selectedMetal = 'gold';

    /**
     * Selected karat (only applies to gold).
     */
    public string $selectedKarat = '24k';

    /**
     * Quantity of units.
     */
    public int $quantity = 1;

    /**
     * Selected unit: 'tola', 'gram', '10_gram', 'kg'.
     */
    public string $selectedUnit = 'tola';

    /**
     * Calculated total price.
     */
    public ?float $calculatedPrice = null;

    /**
     * Last updated timestamp.
     */
    public ?string $lastUpdated = null;

    /**
     * Gold prices from cache.
     * Structure: ['24k' => ['tola' => ['buy' => ..., 'sell' => ...], ...], ...]
     */
    public array $goldPrices = [];

    /**
     * Silver prices from cache.
     * Structure: ['tola' => ['buy' => ..., 'sell' => ...], ...]
     */
    public array $silverPrices = [];

    public function mount(): void
    {
        $this->loadPrices();
        $this->calculatePrice();
    }

    /**
     * Select metal type.
     */
    public function selectMetal(string $metal): void
    {
        $this->selectedMetal = $metal;

        if ($metal === 'silver') {
            $this->selectedKarat = '24k';
        }

        $this->calculatePrice();
    }

    /**
     * Select karat (gold only).
     */
    public function selectKarat(string $karat): void
    {
        $this->selectedKarat = $karat;
        $this->calculatePrice();
    }

    /**
     * Select unit.
     */
    public function selectUnit(string $unit): void
    {
        $this->selectedUnit = $unit;
        $this->calculatePrice();
    }

    /**
     * Move to step 2.
     */
    public function nextStep(): void
    {
        $this->step = 2;
        $this->calculatePrice();
    }

    /**
     * Go back to step 1.
     */
    public function prevStep(): void
    {
        $this->step = 1;
    }

    /**
     * Increment quantity.
     */
    public function incrementQuantity(): void
    {
        $this->quantity = min($this->quantity + 1, 9999);
        $this->calculatePrice();
    }

    /**
     * Decrement quantity.
     */
    public function decrementQuantity(): void
    {
        $this->quantity = max($this->quantity - 1, 1);
        $this->calculatePrice();
    }

    /**
     * Recalculate on any property update.
     */
    public function updated($property): void
    {
        if (in_array($property, ['quantity', 'selectedUnit', 'selectedKarat', 'selectedMetal'])) {
            if ($property === 'quantity') {
                $this->quantity = max(1, min((int) $this->quantity, 9999));
            }
            $this->calculatePrice();
        }
    }

    /**
     * Calculate the total buy price based on current selections.
     */
    public function calculatePrice(): void
    {
        $unitPrice = $this->getUnitBuyPrice();

        if ($unitPrice !== null) {
            $this->calculatedPrice = $unitPrice * $this->quantity;
        } else {
            $this->calculatedPrice = null;
        }
    }

    /**
     * Get the buy price for the current selection.
     */
    protected function getUnitBuyPrice(): ?float
    {
        if ($this->selectedMetal === 'gold') {
            return $this->goldPrices[$this->selectedKarat][$this->selectedUnit]['buy'] ?? null;
        }

        return $this->silverPrices[$this->selectedUnit]['buy'] ?? null;
    }

    /**
     * Load prices from cache, falling back to database.
     */
    private function loadPrices(): void
    {
        $cacheManager = app(PriceCacheManager::class);
        $allPrices = $cacheManager->getAllPrices();

        $this->goldPrices = $allPrices['gold'] ?: $this->loadGoldFromDatabase();
        $this->silverPrices = $allPrices['silver'] ?: $this->loadSilverFromDatabase();
        $this->lastUpdated = $allPrices['last_updated'];
    }

    /**
     * Fallback: load latest gold prices from database.
     */
    private function loadGoldFromDatabase(): array
    {
        $goldData = [];
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
     * Fallback: load latest silver prices from database.
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

    public function placeOrder()
    {
        $unitPrice = $this->getUnitBuyPrice();

        if ($unitPrice === null) {
            return;
        }

        $order = Order::create([
            'metal' => $this->selectedMetal,
            'karat' => $this->selectedMetal === 'gold' ? $this->selectedKarat : null,
            'quantity' => $this->quantity,
            'unit' => $this->selectedUnit,
            'type' => 'buy',
            'locked_price' => $unitPrice,
            'total_amount' => $unitPrice * $this->quantity,
            'status' => 'pending',
        ]);

        return redirect()->route('checkout', $order->order_number);
    }

    public function render()
    {
        return view('livewire.buy-page');
    }
}
