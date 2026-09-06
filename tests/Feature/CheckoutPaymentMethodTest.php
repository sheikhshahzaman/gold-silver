<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutPaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    private function makeOrder(array $attributes = []): Order
    {
        $order = new Order();
        $order->forceFill(array_merge([
            'customer_name' => 'Test Customer',
            'customer_phone' => '03001234567',
            'type' => 'buy',
            'status' => Order::STATUS_PENDING,
            'total_amount' => 1000,
        ], $attributes));
        $order->save();

        return $order->refresh();
    }

    public function test_pickup_offers_cash_and_delivery_offers_cod(): void
    {
        $this->assertSame(
            [Payment::METHOD_BANK_TRANSFER, Payment::METHOD_CASH],
            Payment::methodsForDelivery('pickup')
        );

        $this->assertSame(
            [Payment::METHOD_BANK_TRANSFER, Payment::METHOD_COD],
            Payment::methodsForDelivery('delivery')
        );
    }

    public function test_cash_is_not_allowed_for_delivery_and_cod_not_for_pickup(): void
    {
        $this->assertFalse(Payment::isValidForDelivery(Payment::METHOD_CASH, 'delivery'));
        $this->assertFalse(Payment::isValidForDelivery(Payment::METHOD_COD, 'pickup'));

        $this->assertTrue(Payment::isValidForDelivery(Payment::METHOD_CASH, 'pickup'));
        $this->assertTrue(Payment::isValidForDelivery(Payment::METHOD_COD, 'delivery'));
        $this->assertTrue(Payment::isValidForDelivery(Payment::METHOD_BANK_TRANSFER, 'pickup'));
        $this->assertTrue(Payment::isValidForDelivery(Payment::METHOD_BANK_TRANSFER, 'delivery'));
    }

    public function test_only_a_bank_transfer_requires_a_screenshot(): void
    {
        $this->assertTrue(Payment::requiresProof(Payment::METHOD_BANK_TRANSFER));
        $this->assertFalse(Payment::requiresProof(Payment::METHOD_CASH));
        $this->assertFalse(Payment::requiresProof(Payment::METHOD_COD));
    }

    public function test_a_draft_order_is_not_submitted(): void
    {
        $order = $this->makeOrder();

        $this->assertFalse($order->isSubmitted());
        $this->assertSame(0, Order::submitted()->where('id', $order->id)->count());
    }

    public function test_marking_submitted_makes_it_visible_and_is_idempotent(): void
    {
        $order = $this->makeOrder();
        $order->markSubmitted();

        $this->assertTrue($order->fresh()->isSubmitted());
        $this->assertSame(1, Order::submitted()->where('id', $order->id)->count());

        $first = $order->fresh()->submitted_at;
        $order->fresh()->markSubmitted();

        $this->assertEquals($first, $order->fresh()->submitted_at, 'Re-submitting re-stamped the timestamp.');
    }
}
