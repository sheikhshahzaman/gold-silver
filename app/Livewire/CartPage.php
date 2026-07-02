<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\Cart;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Your Cart - Islamabad Bullion Exchange')]
class CartPage extends Component
{
    public function increase(int $cartItemId): void
    {
        $cart = app(Cart::class);
        // We pull the existing quantity and bump it.
        $item = $cart->items()->firstWhere('id', $cartItemId);
        if ($item) $cart->updateQuantity($cartItemId, $item->quantity + 1);
        $this->dispatch('cart-updated');
    }

    public function decrease(int $cartItemId): void
    {
        $cart = app(Cart::class);
        $item = $cart->items()->firstWhere('id', $cartItemId);
        if ($item) $cart->updateQuantity($cartItemId, $item->quantity - 1);
        $this->dispatch('cart-updated');
    }

    public function remove(int $cartItemId): void
    {
        app(Cart::class)->remove($cartItemId);
        $this->dispatch('cart-updated');
    }

    public function clear(): void
    {
        app(Cart::class)->clear();
        $this->dispatch('cart-updated');
    }

    /**
     * Create an Order (+ OrderItems) from the current cart, clear the cart,
     * then redirect into the existing /checkout/{orderNumber} flow which
     * collects customer info and payment.
     */
    public function checkout()
    {
        $cart = app(Cart::class);
        $cart->reprice();
        $items = $cart->items();

        if ($items->isEmpty()) {
            $this->dispatch('cart-updated');
            return null;
        }

        // Refuse to place an order containing a line the price engine could
        // not price (e.g. a misconfigured price key) — a Rs 0 line would
        // otherwise be locked onto the order.
        $unpriced = $items->first(fn ($i) => (float) ($i->locked_unit_price ?? 0) <= 0);
        if ($unpriced) {
            $this->addError('checkout', 'Pricing is temporarily unavailable for "'
                . ($unpriced->product?->name ?? 'an item')
                . '" — please remove it from the cart or try again in a moment.');
            return null;
        }

        $subtotal = $cart->subtotal();

        $order = DB::transaction(function () use ($items, $subtotal) {
            $order = Order::create([
                // Guard against a stale session id for a deleted user —
                // auth()->id() can be non-null while auth()->check() is false,
                // which would fail the orders.user_id foreign key.
                'user_id' => auth()->check() ? auth()->id() : null,
                'type' => 'buy',
                'total_amount' => $subtotal,
                'status' => 'pending',
                // metal/karat/quantity/unit/locked_price remain NULL for
                // cart orders -- the per-line detail lives on order_items.
            ]);

            foreach ($items as $item) {
                $product = $item->product;
                $unit = (float) ($item->locked_unit_price ?? 0);
                $packaging = (float) ($product?->packaging_charge ?? 0);
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product?->id,
                    'product_name' => $product?->name ?? 'Unknown product',
                    'product_weight' => $product?->weight,
                    'metal' => $product?->metal,
                    'karat' => $product?->karat,
                    'unit' => $product?->unit ?? null,
                    'quantity' => $item->quantity,
                    'unit_price' => $unit,
                    'packaging_charge' => $packaging,
                    'line_total' => ($unit + $packaging) * $item->quantity,
                ]);
            }

            return $order;
        });

        // Empty the cart now that an order exists for these items.
        $cart->clear();
        $this->dispatch('cart-updated');

        return redirect()->route('checkout', $order->order_number);
    }

    #[On('cart-updated')]
    public function refresh(): void
    {
        // Trigger a re-render so totals + count badge update.
    }

    public function render()
    {
        $cart = app(Cart::class);

        // Re-price every line on view so customers always see the current
        // market rate. The cart-stored price still wins at checkout, but
        // we surface live changes here so they can decide.
        $cart->reprice();

        $items = $cart->items();
        return view('livewire.cart-page', [
            'items' => $items,
            'subtotal' => $cart->subtotal(),
            'itemCount' => $cart->count(),
        ]);
    }
}
