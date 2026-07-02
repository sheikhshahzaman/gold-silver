<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OrderDeletionTest extends TestCase
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
            'customer_name' => 'Delete Test',
            'customer_phone' => '03001234567',
        ]);
    }

    public function test_order_without_payment_can_be_deleted(): void
    {
        $order = $this->makeOrder();

        $order->delete();

        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }

    public function test_order_with_payment_can_be_deleted_and_payment_cascades(): void
    {
        $order = $this->makeOrder();
        $payment = Payment::create([
            'order_id' => $order->id,
            'method' => 'bank_transfer',
            'amount' => $order->total_amount,
            'proof_image' => 'payment-proofs/test.png',
            'status' => 'pending',
        ]);

        // This is the exact scenario that would previously hit a raw FK
        // constraint error (payments.order_id had no cascade rule).
        $order->delete();

        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
        $this->assertDatabaseMissing('payments', ['id' => $payment->id]);
    }

    public function test_order_items_cascade_when_order_deleted(): void
    {
        $order = $this->makeOrder();
        $item = OrderItem::create([
            'order_id' => $order->id,
            'product_name' => 'Test Product',
            'quantity' => 1,
            'unit_price' => 100,
            'line_total' => 100,
        ]);

        $order->delete();

        $this->assertDatabaseMissing('order_items', ['id' => $item->id]);
    }

    public function test_admin_can_delete_order_via_resource_action(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $order = $this->makeOrder();

        Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\OrderResource\Pages\ListOrders::class)
            ->callTableAction('delete', $order)
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }

    public function test_admin_can_bulk_delete_orders(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $order1 = $this->makeOrder();
        $order2 = $this->makeOrder();

        Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\OrderResource\Pages\ListOrders::class)
            ->callTableBulkAction('delete', [$order1, $order2])
            ->assertHasNoTableBulkActionErrors();

        $this->assertDatabaseMissing('orders', ['id' => $order1->id]);
        $this->assertDatabaseMissing('orders', ['id' => $order2->id]);
    }
}
