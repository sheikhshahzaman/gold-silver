<?php

namespace App\Livewire;

use App\Models\InventoryItem;
use App\Services\PriceEngine\PriceCacheManager;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class VerifyItemPage extends Component
{
    public ?InventoryItem $item = null;
    public bool $valid = false;
    public string $claimPhone = '';
    public bool $showClaimForm = false;
    public bool $claimed = false;

    public function mount(string $token)
    {
        $this->item = InventoryItem::with('product.productCategory')
            ->where('verification_token', $token)
            ->first();

        if ($this->item) {
            $this->valid = true;
            $this->item->logScan(request()->ip(), request()->userAgent());
            $this->claimed = !empty($this->item->claimed_by_phone);
        }
    }

    public function claimItem()
    {
        $this->validate([
            'claimPhone' => 'required|string|min:10|max:30',
        ]);

        if ($this->item && !$this->item->claimed_by_phone && $this->item->status === 'in_stock') {
            $this->item->update(['claimed_by_phone' => $this->claimPhone]);
            $this->claimed = true;
            $this->showClaimForm = false;
        }
    }

    public function render()
    {
        $prices = [];
        if ($this->item && $this->item->product && $this->item->product->price_key) {
            try {
                $allPrices = app(PriceCacheManager::class)->getAllPrices();
                $keys = explode('.', $this->item->product->price_key);
                $current = $allPrices;
                foreach ($keys as $key) {
                    $current = $current[$key] ?? null;
                    if ($current === null) break;
                }
                if (is_array($current)) {
                    $prices = $current;
                } elseif (is_numeric($current)) {
                    $prices = ['sell' => $current];
                }
            } catch (\Throwable) {
                // Price unavailable
            }
        }

        return view('livewire.verify-item-page', [
            'prices' => $prices,
        ])->title($this->valid ? 'Verify: ' . $this->item->serial_number : 'Verification Failed');
    }
}
