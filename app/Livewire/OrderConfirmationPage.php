<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Order Status - Islamabad Bullion Exchange')]
class OrderConfirmationPage extends Component
{
    public Order $order;
    public string $whatsappNumber = '';
    public string $contactPhone = '';
    public string $contactAddress = '';

    public function mount(string $orderNumber): void
    {
        $this->order = Order::with(['items', 'payment'])->where('order_number', $orderNumber)->firstOrFail();
        $this->whatsappNumber = Setting::get('contact_whatsapp', '');
        $this->contactPhone = Setting::get('contact_phone', '');
        $this->contactAddress = Setting::get('contact_address', '');
    }

    public function render()
    {
        return view('livewire.order-confirmation-page');
    }
}
