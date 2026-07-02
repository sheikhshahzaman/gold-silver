<?php

namespace Tests\Feature;

use App\Livewire\CartPage;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Services\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PackagingChargeTest extends TestCase
{
    use RefreshDatabase;

    private function makeProduct(float $fixedPrice, float $packaging): Product
    {
        $category = ProductCategory::create(['name' => 'Gold Bars', 'slug' => 'gold-bars']);

        return Product::create([
            'name' => '24K Gold Bar',
            'slug' => '24k-gold-bar-' . uniqid(),
            'weight' => '1 Tola',
            'metal' => 'gold',
            'karat' => '24K',
            'category_id' => $category->id,
            'price_type' => 'fixed',
            'fixed_price' => $fixedPrice,
            'packaging_charge' => $packaging,
            'is_active' => true,
        ]);
    }

    public function test_cart_line_total_includes_packaging_charge(): void
    {
        $product = $this->makeProduct(445600, 500);
        $cart = app(Cart::class);
        $item = $cart->add($product, 2);

        // (445600 + 500) * 2
        $this->assertEquals(892200.0, $item->line_total);
        $this->assertEquals(500.0, $item->packaging_charge);
    }

    public function test_checkout_snapshots_packaging_charge_onto_order_item(): void
    {
        $product = $this->makeProduct(445600, 500);
        $cart = app(Cart::class);
        $cart->add($product, 2);

        Livewire::test(CartPage::class)->call('checkout');

        $orderItem = \App\Models\OrderItem::first();
        $this->assertNotNull($orderItem);
        $this->assertEquals(500.0, (float) $orderItem->packaging_charge);
        $this->assertEquals(1000.0, $orderItem->packaging_total); // 500 * 2
        $this->assertEquals(892200.0, (float) $orderItem->line_total);

        $order = $orderItem->order;
        $this->assertEquals(892200.0, (float) $order->total_amount);
    }

    public function test_zero_packaging_charge_does_not_affect_total(): void
    {
        $product = $this->makeProduct(445600, 0);
        $cart = app(Cart::class);
        $item = $cart->add($product, 1);

        $this->assertEquals(445600.0, $item->line_total);
    }

    public function test_editing_product_packaging_charge_does_not_change_past_orders(): void
    {
        $product = $this->makeProduct(445600, 500);
        $cart = app(Cart::class);
        $cart->add($product, 1);

        Livewire::test(CartPage::class)->call('checkout');
        $orderItem = \App\Models\OrderItem::first();

        // Admin later changes the packaging charge.
        $product->update(['packaging_charge' => 900]);

        $orderItem->refresh();
        $this->assertEquals(500.0, (float) $orderItem->packaging_charge, 'Historical order must keep the price at time of purchase.');
    }
}
