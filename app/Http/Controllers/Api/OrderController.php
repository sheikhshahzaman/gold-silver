<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Setting;
use App\Services\Cart;
use App\Services\PriceEngine\PriceCacheManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Create an order from the app's local cart. The app sends product ids
     * and quantities; unit prices are ALWAYS resolved server-side at the
     * current rate — never trusted from the client.
     */
    public function store(Request $request, Cart $cart): JsonResponse
    {
        $data = $request->validate([
            'customer_name' => 'required|string|min:2|max:255',
            'customer_phone' => 'required|string|min:10|max:20',
            'items' => 'required|array|min:1|max:50',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1|max:9999',
        ]);

        $products = Product::active()
            ->whereIn('id', collect($data['items'])->pluck('product_id'))
            ->get()
            ->keyBy('id');

        $lines = [];
        $total = 0.0;
        foreach ($data['items'] as $line) {
            $product = $products->get($line['product_id']);
            if (!$product) {
                return response()->json([
                    'message' => 'One of the products is no longer available.',
                ], 422);
            }

            $unit = $cart->unitPriceFor($product);
            if ($unit === null) {
                return response()->json([
                    'message' => "Price for {$product->name} is currently unavailable. Please try again shortly.",
                ], 422);
            }

            $packaging = (float) $product->packaging_charge;
            $lines[] = ['product' => $product, 'quantity' => (int) $line['quantity'], 'unit' => $unit, 'packaging' => $packaging];
            $total += ($unit + $packaging) * $line['quantity'];
        }

        $order = DB::transaction(function () use ($data, $lines, $total) {
            $order = Order::create([
                'type' => 'buy',
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'total_amount' => $total,
                'status' => 'pending',
            ]);

            foreach ($lines as $line) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $line['product']->id,
                    'product_name' => $line['product']->name,
                    'product_weight' => $line['product']->weight,
                    'metal' => $line['product']->metal,
                    'karat' => $line['product']->karat,
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit'],
                    'packaging_charge' => $line['packaging'],
                    'line_total' => ($line['unit'] + $line['packaging']) * $line['quantity'],
                ]);
            }

            return $order;
        });

        return response()->json([
            'order' => $this->orderPayload($order->fresh(['items', 'payment'])),
            'payment_accounts' => $this->paymentAccounts(),
        ], 201);
    }

    /**
     * Create a single-item Buy/Sell order priced by metal weight (the app's
     * Buy & Sell wizards). Mirrors the website's BuyPage/SellPage::placeOrder,
     * but the unit price is ALWAYS re-derived server-side from the live price
     * cache — the client only sends the selection, never the price.
     */
    public function storeMetalOrder(Request $request, PriceCacheManager $cacheManager): JsonResponse
    {
        $data = $request->validate([
            'customer_name' => 'required|string|min:2|max:255',
            'customer_phone' => 'required|string|min:10|max:20',
            'type' => 'required|in:buy,sell',
            'metal' => 'required|in:gold,silver',
            'karat' => 'nullable|string|max:10',
            'unit' => 'required|string|max:20',
            'quantity' => 'required|numeric|min:0.0001|max:100000',
        ]);

        $karat = $data['metal'] === 'gold' ? strtolower($data['karat'] ?? '') : null;

        // Whitelist selections so a tampered request can't reach an unexpected
        // price bucket. These mirror the app's Buy/Sell wizard options.
        $allowedKarats = ['24k', '22k', '21k', '18k'];
        $allowedGoldUnits = ['gram', 'tola'];
        $allowedSilverUnits = ['kg', '10_tola', '10_tola_qr', '5_tola', 'tola', 'gram'];

        if ($data['metal'] === 'gold') {
            if (!in_array($karat, $allowedKarats, true) || !in_array($data['unit'], $allowedGoldUnits, true)) {
                return response()->json(['message' => 'Invalid gold selection.'], 422);
            }
        } elseif (!in_array($data['unit'], $allowedSilverUnits, true)) {
            return response()->json(['message' => 'Invalid silver selection.'], 422);
        }

        $prices = $cacheManager->getAllPrices();
        $unitPrice = $this->resolveMetalUnitPrice($prices, $data['metal'], $karat, $data['unit'], $data['type']);

        if ($unitPrice === null || $unitPrice <= 0) {
            return response()->json([
                'message' => 'Live price is currently unavailable. Please try again shortly.',
            ], 422);
        }

        $quantity = (float) $data['quantity'];
        $total = round($unitPrice * $quantity, 2);

        $order = Order::create([
            'type' => $data['type'],
            'metal' => $data['metal'],
            'karat' => $karat,
            'quantity' => $quantity,
            'unit' => $data['unit'],
            'locked_price' => $unitPrice,
            'total_amount' => $total,
            'customer_name' => $data['customer_name'],
            'customer_phone' => $data['customer_phone'],
            'status' => 'pending',
        ]);

        return response()->json([
            'order' => $this->orderPayload($order->fresh(['items', 'payment'])),
            'payment_accounts' => $this->paymentAccounts(),
        ], 201);
    }

    public function show(string $orderNumber): JsonResponse
    {
        $order = Order::with(['items', 'payment'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return response()->json([
            'order' => $this->orderPayload($order),
            'payment_accounts' => $order->payment ? null : $this->paymentAccounts(),
        ]);
    }

    /**
     * Attach the customer's payment proof to an order
     * (same rules as the website checkout step 3).
     */
    public function submitPayment(Request $request, string $orderNumber): JsonResponse
    {
        $order = Order::with('payment')->where('order_number', $orderNumber)->firstOrFail();

        if ($order->payment) {
            return response()->json(['message' => 'Payment has already been submitted for this order.'], 409);
        }

        $data = $request->validate([
            'method' => 'required|in:bank_transfer',
            'delivery_method' => 'required|in:pickup,delivery',
            'delivery_address' => 'required_if:delivery_method,delivery|nullable|string|max:1000',
            'proof_image' => 'required|image|max:5120',
            'reference_number' => 'nullable|string|max:255',
        ]);

        $deliveryCharge = $data['delivery_method'] === 'delivery'
            ? (float) Setting::get('delivery_charge', 0)
            : 0.0;

        $order->update([
            'delivery_method' => $data['delivery_method'],
            'delivery_address' => $data['delivery_method'] === 'delivery' ? $data['delivery_address'] : null,
            'delivery_charge' => $deliveryCharge,
        ]);

        $path = $request->file('proof_image')->store('payment-proofs', 'public');

        Payment::create([
            'order_id' => $order->id,
            'method' => $data['method'],
            'amount' => $order->fresh()->grand_total,
            'proof_image' => $path,
            'reference_number' => $data['reference_number'] ?? null,
            'status' => 'pending',
        ]);

        $order->update(['status' => 'awaiting_verification']);

        return response()->json(['order' => $this->orderPayload($order->fresh(['items', 'payment']))]);
    }

    private function orderPayload(Order $order): array
    {
        $items = $order->items->map(fn (OrderItem $i) => [
            'product_name' => $i->product_name,
            'metal' => $i->metal,
            'karat' => $i->karat,
            'quantity' => (float) $i->quantity,
            'unit' => null,
            'unit_price' => (float) $i->unit_price,
            'packaging_charge' => (float) $i->packaging_charge,
            'packaging_total' => (float) $i->packaging_total,
            'line_total' => (float) $i->line_total,
        ])->values()->all();

        // Single-item Buy/Sell orders have no order_items rows — synthesize a
        // display line from the metal columns so the app renders them uniformly.
        // No packaging charge applies here (it's a per-product-catalog concept).
        $isMetalOrder = empty($items);
        if ($isMetalOrder) {
            $items[] = [
                'product_name' => $this->metalLineLabel($order),
                'metal' => $order->metal,
                'karat' => $order->karat,
                'quantity' => (float) $order->quantity,
                'unit' => $order->unit,
                'unit_price' => (float) $order->locked_price,
                'packaging_charge' => 0.0,
                'packaging_total' => 0.0,
                'line_total' => (float) $order->total_amount,
            ];
        }

        return [
            'order_number' => $order->order_number,
            'status' => $order->status,
            'type' => $order->type,
            'customer_name' => $order->customer_name,
            'customer_phone' => $order->customer_phone,
            'total_amount' => (float) $order->total_amount,
            'delivery_method' => $order->delivery_method,
            'delivery_address' => $order->delivery_address,
            'delivery_charge' => (float) $order->delivery_charge,
            'grand_total' => (float) $order->grand_total,
            'created_at' => $order->created_at?->toIso8601String(),
            'items' => $items,
            'payment' => $order->payment ? [
                'method' => $order->payment->method,
                'status' => $order->payment->status,
                'reference_number' => $order->payment->reference_number,
            ] : null,
        ];
    }

    /**
     * Re-derive the live unit price for a metal selection from the price cache.
     * Returns null when the relevant rate isn't available. Mirrors the app's
     * Buy/Sell pricing so the confirmed total matches what the user saw.
     */
    private function resolveMetalUnitPrice(array $prices, string $metal, ?string $karat, string $unit, string $type): ?float
    {
        $gramsPerTola = 11.6638038;

        if ($metal === 'gold') {
            // The app derives gold prices from the per-gram rate (tola = gram ×
            // grams-per-tola); mirror that here so the confirmed total matches.
            $perGram = $prices['gold'][$karat]['gram'][$type] ?? null;
            if ($perGram === null) {
                return null;
            }
            $perGram = (float) $perGram;
            return $unit === 'tola' ? $perGram * $gramsPerTola : $perGram;
        }

        $silver = $prices['silver'] ?? [];
        $perGram = isset($silver['gram'][$type]) ? (float) $silver['gram'][$type] : null;
        if ($perGram === null && isset($silver['tola'][$type])) {
            $perGram = (float) $silver['tola'][$type] / $gramsPerTola;
        }

        $direct = fn (string $key) => isset($silver[$key][$type]) ? (float) $silver[$key][$type] : null;

        return match ($unit) {
            'kg' => $direct('kg') ?? ($perGram !== null ? $perGram * 1000 : null),
            '10_tola_qr' => $direct('10_tola_qr') ?? $direct('10_tola') ?? ($perGram !== null ? $perGram * $gramsPerTola * 10 : null),
            '10_tola' => $direct('10_tola') ?? ($perGram !== null ? $perGram * $gramsPerTola * 10 : null),
            '5_tola' => $direct('5_tola') ?? ($perGram !== null ? $perGram * $gramsPerTola * 5 : null),
            'tola' => $direct('tola') ?? ($perGram !== null ? $perGram * $gramsPerTola : null),
            'gram' => $perGram,
            default => null,
        };
    }

    /** Human label for a synthesized Buy/Sell order line. */
    private function metalLineLabel(Order $order): string
    {
        $metal = ucfirst((string) $order->metal);
        $karat = $order->karat ? ' ' . strtoupper($order->karat) : '';
        $type = ucfirst((string) $order->type);
        $unitLabel = $this->unitLabel($order->unit);
        $qty = rtrim(rtrim(number_format((float) $order->quantity, 4, '.', ''), '0'), '.');

        if ($order->metal === 'gold') {
            // Weight-based: e.g. "Gold 22K — 3.5 tola (Buy)"
            return "{$metal}{$karat} — {$qty} {$unitLabel} ({$type})";
        }

        // Package-based silver: e.g. "Silver — 10 Tola (QR) (Sell)" / "… × 2"
        $count = $qty === '1' ? '' : " × {$qty}";
        return "{$metal} — {$unitLabel}{$count} ({$type})";
    }

    private function unitLabel(?string $unit): string
    {
        return match ($unit) {
            'gram' => 'gram',
            'tola' => 'Tola',
            '5_tola' => '5 Tola',
            '10_tola' => '10 Tola',
            '10_tola_qr' => '10 Tola (QR)',
            'kg' => '1 KG',
            default => (string) $unit,
        };
    }

    private function paymentAccounts(): array
    {
        return [
            'bank_transfer' => [
                'bank_name' => Setting::get('payment_bank_name', ''),
                'account_title' => Setting::get('payment_bank_account_title', ''),
                'account_number' => Setting::get('payment_bank_account_number', ''),
                'iban' => Setting::get('payment_bank_iban', ''),
            ],
        ];
    }
}
