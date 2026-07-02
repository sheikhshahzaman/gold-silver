<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Services\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class CartStaleSessionTest extends TestCase
{
    use RefreshDatabase;

    private function makeProduct(): Product
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
            'fixed_price' => 445600,
            'is_active' => true,
        ]);
    }

    /**
     * A session can still hold a login id after that user row is deleted.
     * Auth::check() is false but Auth::id() falls back to the raw session
     * value, so trusting it blindly inserts a dangling user_id and hits the
     * cart_items foreign key. The visitor must fall back to a guest cart.
     */
    public function test_visitor_with_deleted_user_session_can_still_add_to_cart(): void
    {
        $product = $this->makeProduct();

        // Simulate the stale state: auth session key points at a user id
        // that no longer exists.
        session()->put(Auth::guard('web')->getName(), 999);

        $this->assertFalse(Auth::check());
        $this->assertSame(999, Auth::id()); // the Laravel quirk this guards against

        $item = app(Cart::class)->add($product, 1);

        $this->assertNull($item->user_id);
        $this->assertNotNull($item->session_id);
        $this->assertDatabaseHas('cart_items', [
            'id' => $item->id,
            'user_id' => null,
        ]);
    }

    /** Same stale-session scenario, but for order creation at checkout. */
    public function test_visitor_with_deleted_user_session_can_checkout(): void
    {
        $product = $this->makeProduct();

        session()->put(Auth::guard('web')->getName(), 999);
        app(Cart::class)->add($product, 1);

        $this->assertFalse(Auth::check());

        \Livewire\Livewire::test(\App\Livewire\CartPage::class)->call('checkout');

        $order = \App\Models\Order::latest('id')->first();
        $this->assertNotNull($order);
        $this->assertNull($order->user_id);
    }
}
