<?php

namespace App\Livewire;

use App\Models\Order;
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

    private function formatOrderNumber(string $value): string
    {
        $cleaned = preg_replace('/[^A-Za-z0-9]/', '', strtoupper($value)) ?? '';

        if ($cleaned === '') {
            return '';
        }

        $body = substr($cleaned, 0, 18);
        $first = substr($body, 0, 8);
        $second = substr($body, 8, 10);

        return $first.($second !== '' ? '-'.$second : '');
    }

    public function render()
    {
        return view('livewire.order-tracking-page');
    }
}
