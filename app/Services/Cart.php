<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;
use App\Services\PriceEngine\PriceCacheManager;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

/**
 * Session/user-keyed cart for the product catalog.
 *
 * Guests are identified by their session_id; signed-in users by user_id.
 * When a guest signs in, mergeFromSession() copies their session cart
 * into their user-bound cart (callable from a login listener if needed).
 */
class Cart
{
    /**
     * Resolve the live unit price for a product. Falls back through:
     *   1. Product's fixed_price (if it's a fixed-price item)
     *   2. Live spot price via price_key (e.g. 'gold.24k.tola')
     *   3. null (price unknown — checkout will say "Contact for quote")
     */
    public function unitPriceFor(Product $product): ?float
    {
        if ($product->price_type === 'fixed' && $product->fixed_price) {
            $base = (float) $product->fixed_price;
            return $product->hasActiveDiscount() ? (float) $product->applyDiscount($base) : $base;
        }

        if ($product->price_type === 'live' && $product->price_key) {
            $prices = app(PriceCacheManager::class)->getAllPrices();
            $parts = explode('.', $product->price_key);
            $base = null;
            if (count($parts) === 3) {
                $bucket = $parts[0] === 'silver' ? ($prices['silver'] ?? []) : ($prices['gold'] ?? []);
                $base = $bucket[$parts[1]][$parts[2]]['buy'] ?? null;
            } elseif (count($parts) === 2 && $parts[0] === 'silver') {
                $base = $prices['silver'][$parts[1]]['buy'] ?? null;
            }
            if ($base !== null) {
                return $product->hasActiveDiscount() ? (float) $product->applyDiscount($base) : (float) $base;
            }
        }

        return null;
    }

    /**
     * Add a product to the cart (or bump quantity if it's already there).
     * Returns the resulting CartItem.
     */
    public function add(Product $product, int $quantity = 1): CartItem
    {
        $quantity = max(1, $quantity);

        $item = $this->findItem($product->id);
        $unit = $this->unitPriceFor($product);

        if ($item) {
            $item->quantity += $quantity;
            $item->locked_unit_price = $unit; // refresh the captured price
            $item->save();
            return $item;
        }

        return CartItem::create([
            'session_id' => Auth::check() ? null : $this->sessionId(),
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'quantity' => $quantity,
            'locked_unit_price' => $unit,
        ]);
    }

    public function updateQuantity(int $cartItemId, int $quantity): void
    {
        $item = $this->ownedQuery()->where('id', $cartItemId)->first();
        if (!$item) return;

        if ($quantity < 1) {
            $item->delete();
            return;
        }
        $item->quantity = min($quantity, 9999);
        $item->save();
    }

    public function remove(int $cartItemId): void
    {
        $this->ownedQuery()->where('id', $cartItemId)->delete();
    }

    public function clear(): void
    {
        $this->ownedQuery()->delete();
    }

    /**
     * Snapshot the live price of every line item back onto its
     * locked_unit_price column. Call before checkout so the price the
     * customer pays matches what they saw in the cart.
     */
    public function reprice(): void
    {
        foreach ($this->items() as $item) {
            $unit = $this->unitPriceFor($item->product);
            if ($unit !== null && (float) $item->locked_unit_price !== (float) $unit) {
                $item->locked_unit_price = $unit;
                $item->save();
            }
        }
    }

    /** Eager-loaded CartItems for the current cart, oldest first. */
    public function items(): Collection
    {
        return $this->ownedQuery()
            ->with('product.productCategory')
            ->orderBy('id')
            ->get();
    }

    public function count(): int
    {
        return (int) $this->ownedQuery()->sum('quantity');
    }

    public function subtotal(): float
    {
        return (float) $this->items()->sum(fn (CartItem $i) => (float) $i->line_total);
    }

    /**
     * Move every session-keyed item onto the freshly logged-in user's cart.
     * Idempotent: if the user already has the same product, we bump qty.
     */
    public function mergeFromSession(): void
    {
        if (!Auth::check()) return;
        $sessionItems = CartItem::query()
            ->where('session_id', $this->sessionId())
            ->whereNull('user_id')
            ->get();

        foreach ($sessionItems as $session) {
            $existing = CartItem::query()
                ->where('user_id', Auth::id())
                ->where('product_id', $session->product_id)
                ->first();
            if ($existing) {
                $existing->quantity = min(9999, $existing->quantity + $session->quantity);
                $existing->save();
                $session->delete();
            } else {
                $session->user_id = Auth::id();
                $session->session_id = null;
                $session->save();
            }
        }
    }

    /* ─── internal ─────────────────────────────────────────────────── */

    /** Eloquent query scoped to whichever owner identifies the current cart. */
    private function ownedQuery()
    {
        if (Auth::check()) {
            return CartItem::query()->where('user_id', Auth::id());
        }
        return CartItem::query()
            ->where('session_id', $this->sessionId())
            ->whereNull('user_id');
    }

    private function findItem(int $productId): ?CartItem
    {
        return $this->ownedQuery()->where('product_id', $productId)->first();
    }

    private function sessionId(): string
    {
        // Ensure the session is started so getId() returns a stable value.
        if (!Session::isStarted()) Session::start();
        return Session::getId();
    }
}
