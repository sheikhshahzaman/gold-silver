<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderSourceTest extends TestCase
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

    public function test_orders_default_to_website(): void
    {
        $this->assertSame(Order::SOURCE_WEBSITE, $this->makeOrder()->source);
    }

    public function test_an_app_order_is_recorded_as_app(): void
    {
        $order = $this->makeOrder(['source' => Order::SOURCE_APP]);

        $this->assertSame(Order::SOURCE_APP, $order->source);
        $this->assertSame('Mobile App', Order::sourceOptions()[$order->source]);
    }

    public function test_source_is_fillable(): void
    {
        $order = Order::create([
            'source' => Order::SOURCE_APP,
            'type' => 'buy',
            'customer_name' => 'Fillable Check',
            'customer_phone' => '03001234567',
            'total_amount' => 500,
            'status' => Order::STATUS_PENDING,
        ]);

        $this->assertSame(Order::SOURCE_APP, $order->refresh()->source);
    }
}
