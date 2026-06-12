<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Setting;
use App\Services\Cart;
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

            $lines[] = ['product' => $product, 'quantity' => (int) $line['quantity'], 'unit' => $unit];
            $total += $unit * $line['quantity'];
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
                    'line_total' => $line['unit'] * $line['quantity'],
                ]);
            }

            return $order;
        });

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
            'method' => 'required|in:easypaisa,jazzcash,raast,bank_transfer',
            'proof_image' => 'required|image|max:5120',
            'reference_number' => 'nullable|string|max:255',
        ]);

        $path = $request->file('proof_image')->store('payment-proofs', 'public');

        Payment::create([
            'order_id' => $order->id,
            'method' => $data['method'],
            'amount' => $order->total_amount,
            'proof_image' => $path,
            'reference_number' => $data['reference_number'] ?? null,
            'status' => 'pending',
        ]);

        $order->update(['status' => 'awaiting_verification']);

        return response()->json(['order' => $this->orderPayload($order->fresh(['items', 'payment']))]);
    }

    private function orderPayload(Order $order): array
    {
        return [
            'order_number' => $order->order_number,
            'status' => $order->status,
            'customer_name' => $order->customer_name,
            'customer_phone' => $order->customer_phone,
            'total_amount' => (float) $order->total_amount,
            'created_at' => $order->created_at?->toIso8601String(),
            'items' => $order->items->map(fn (OrderItem $i) => [
                'product_name' => $i->product_name,
                'metal' => $i->metal,
                'karat' => $i->karat,
                'quantity' => $i->quantity,
                'unit_price' => (float) $i->unit_price,
                'line_total' => (float) $i->line_total,
            ])->values(),
            'payment' => $order->payment ? [
                'method' => $order->payment->method,
                'status' => $order->payment->status,
                'reference_number' => $order->payment->reference_number,
            ] : null,
        ];
    }

    private function paymentAccounts(): array
    {
        return [
            'easypaisa' => [
                'number' => Setting::get('payment_easypaisa_number', ''),
                'name' => Setting::get('payment_easypaisa_name', ''),
            ],
            'jazzcash' => [
                'number' => Setting::get('payment_jazzcash_number', ''),
                'name' => Setting::get('payment_jazzcash_name', ''),
            ],
            'raast' => [
                'id' => Setting::get('payment_raast_id', ''),
                'name' => Setting::get('payment_raast_name', ''),
            ],
            'bank_transfer' => [
                'bank_name' => Setting::get('payment_bank_name', ''),
                'account_title' => Setting::get('payment_bank_account_title', ''),
                'account_number' => Setting::get('payment_bank_account_number', ''),
                'iban' => Setting::get('payment_bank_iban', ''),
            ],
        ];
    }
}
