<?php

namespace App\Livewire;

use App\Services\Cart;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Tiny widget for the cart icon + live count badge. Listens for the
 * `cart-updated` event broadcast by any other Livewire component (e.g.
 * ProductsPage::addToCart, CartPage::remove) and re-renders itself so
 * the badge updates instantly across the page.
 */
class CartIconWidget extends Component
{
    /** Visual variant for the surrounding chrome — 'dark' or 'light'. */
    public string $variant = 'dark';

    /** Render the icon as the mobile bottom-nav tab (with label below) instead of the header pill. */
    public bool $mobile = false;

    #[On('cart-updated')]
    public function refresh(): void
    {
        // No-op — Livewire's render() runs the count query fresh.
    }

    public function render()
    {
        return view('livewire.cart-icon-widget', [
            'count' => app(Cart::class)->count(),
        ]);
    }
}
