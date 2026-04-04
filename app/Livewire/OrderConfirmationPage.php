<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Order Status - PakGold Rates')]
class OrderConfirmationPage extends Component
{
    public Order $order;
    public string $whatsappNumber = '';

    public function mount(string $orderNumber): void
    {
        $this->order = Order::with('payment')->where('order_number', $orderNumber)->firstOrFail();
        $this->whatsappNumber = Setting::get('contact_whatsapp', '');
    }

    public function render()
    {
        return view('livewire.order-confirmation-page');
    }
}
