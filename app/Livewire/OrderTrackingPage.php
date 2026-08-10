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
            $this->orderNumber = trim($number);
            $this->track();
        }
    }

    public function track(): void
    {
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
        if (trim($this->orderNumber) === '') {
            return;
        }

        $this->order = Order::with(['items', 'payment'])
            ->where('order_number', trim($this->orderNumber))
            ->first();
    }

    public function render()
    {
        return view('livewire.order-tracking-page');
    }
}
