<?php

namespace Tests\Feature;

use App\Livewire\CheckoutPage;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

class CheckoutDeliveryTest extends TestCase
{
    use RefreshDatabase;

    private function makeOrder(): Order
    {
        return Order::create([
            'metal' => 'gold',
            'karat' => '24k',
            'quantity' => 1,
            'unit' => 'tola',
            'type' => 'buy',
            'locked_price' => 445600,
            'total_amount' => 445600,
            'status' => 'pending',
        ]);
    }

    public function test_pickup_is_free_and_requires_no_address(): void
    {
        $order = $this->makeOrder();

        Livewire::test(CheckoutPage::class, ['orderNumber' => $order->order_number])
            ->set('customerName', 'Test Customer')
            ->set('customerPhone', '03001234567')
            ->call('goToStep2')
            ->assertSet('step', 2)
            ->call('goToStep3')
            ->assertSet('step', 3)
            ->assertHasNoErrors();

        $order->refresh();
        $this->assertSame('pickup', $order->delivery_method);
        $this->assertNull($order->delivery_address);
        $this->assertEquals(0, (float) $order->delivery_charge);
        $this->assertEquals(445600.0, $order->grand_total);
    }

    public function test_delivery_requires_address_and_adds_charge(): void
    {
        Setting::set('delivery_charge', 500);
        $order = $this->makeOrder();

        $component = Livewire::test(CheckoutPage::class, ['orderNumber' => $order->order_number])
            ->set('customerName', 'Test Customer')
            ->set('customerPhone', '03001234567')
            ->call('goToStep2')
            ->call('selectDeliveryMethod', 'delivery')
            ->call('goToStep3') // no address yet — should fail validation
            ->assertHasErrors(['deliveryAddress']);

        $component->set('deliveryAddress', 'House 12, Street 5, F-7, Islamabad')
            ->call('goToStep3')
            ->assertSet('step', 3)
            ->assertHasNoErrors();

        $order->refresh();
        $this->assertSame('delivery', $order->delivery_method);
        $this->assertSame('House 12, Street 5, F-7, Islamabad', $order->delivery_address);
        $this->assertEquals(500.0, (float) $order->delivery_charge);
        $this->assertEquals(446100.0, $order->grand_total);
    }

    public function test_zero_delivery_charge_shows_as_free(): void
    {
        Setting::set('delivery_charge', 0);
        $order = $this->makeOrder();

        Livewire::test(CheckoutPage::class, ['orderNumber' => $order->order_number])
            ->set('customerName', 'Test Customer')
            ->set('customerPhone', '03001234567')
            ->call('goToStep2')
            ->assertSee('Free Delivery');
    }

    public function test_payment_amount_matches_grand_total_including_delivery(): void
    {
        Setting::set('delivery_charge', 300);
        Setting::set('payment_bank_name', 'Test Bank');
        $order = $this->makeOrder();

        Livewire::test(CheckoutPage::class, ['orderNumber' => $order->order_number])
            ->set('customerName', 'Test Customer')
            ->set('customerPhone', '03001234567')
            ->call('goToStep2')
            ->call('selectDeliveryMethod', 'delivery')
            ->set('deliveryAddress', 'Some address')
            ->call('goToStep3')
            ->set('proofImage', UploadedFile::fake()->image('proof.jpg'))
            ->call('submitPayment');

        $order->refresh();
        $this->assertNotNull($order->payment);
        $this->assertEquals(445900.0, (float) $order->payment->amount); // 445600 + 300
    }
}
