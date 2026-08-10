<?php

namespace App\Services\Orders;

use App\Models\Order;
use App\Services\Cart;
use Illuminate\Support\Facades\DB;

class OrderPricingService
{
    public function __construct(private readonly Cart $cart)
    {
    }

    public function refreshCartOrder(Order $order): Order
    {
        $order->loadMissing('items.product', 'payment');

        if ($order->payment || $order->items->isEmpty()) {
            return $order;
        }

        return DB::transaction(function () use ($order): Order {
            $total = 0.0;

            foreach ($order->items as $item) {
                $product = $item->product;
                if (! $product || ! $product->is_active) {
                    continue;
                }

                $unit = (float) $this->cart->unitPriceFor($product);
                $packaging = (float) $product->packaging_charge;
                $lineTotal = ($unit + $packaging) * (int) $item->quantity;

                $item->update([
                    'product_name' => $product->name,
                    'product_weight' => $product->weight,
                    'metal' => $product->metal,
                    'karat' => $product->karat,
                    'unit' => $product->unit,
                    'unit_price' => $unit,
                    'packaging_charge' => $packaging,
                    'line_total' => $lineTotal,
                ]);

                $total += $lineTotal;
            }

            $order->update(['total_amount' => $total]);

            return $order->refresh()->load('items.product', 'payment');
        });
    }
}
