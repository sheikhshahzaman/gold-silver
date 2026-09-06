<?php

namespace App\Livewire;

use App\Models\Order;
use App\Support\OrderNumber;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Track Order - Islamabad Bullion Exchange')]
class OrderTrackingPage extends Component
{
    public string $orderNumber = '';
    public ?Order $order = null;
    public bool $searched = false;

    public function mount(): void
    {
        $number = request()->query('order');

        if (is_string($number) && trim($number) !== '') {
            $this->orderNumber = $this->formatOrderNumber($number);
            $this->track();
        }
    }

    public function updatedOrderNumber(): void
    {
        $formatted = $this->formatOrderNumber($this->orderNumber);

        if ($this->orderNumber !== $formatted) {
            $this->orderNumber = $formatted;
        }
    }

    public function track(): void
    {
        $this->orderNumber = $this->formatOrderNumber($this->orderNumber);

        $this->validate([
            'orderNumber' => 'required|string|min:5|max:80',
        ], [
            'orderNumber.required' => 'Please enter your order number.',
        ]);

        $this->searched = true;
        $this->refreshOrder();
    }

    public function refreshOrder(): void
    {
        $this->orderNumber = $this->formatOrderNumber($this->orderNumber);

        if ($this->orderNumber === '') {
            return;
        }

        $this->order = Order::with(['items', 'payment'])
            ->where('order_number', $this->orderNumber)
            ->first();
    }

    /**
     * Canonicalises whatever the customer typed. The IBE prefix is added
     * automatically and everything is upper-cased, so lower case input and
     * missing hyphens still find the order.
     */
    private function formatOrderNumber(string $value): string
    {
        return OrderNumber::normalize($value);
    }

    public function render()
    {
        return view('livewire.order-tracking-page');
    }
}
