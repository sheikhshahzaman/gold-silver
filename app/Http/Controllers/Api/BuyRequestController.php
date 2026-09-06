<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BuyRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Services\BuyRequests\BuyRequestPricing;
use App\Services\BuyRequests\BuyRequestPricingException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * "Request to buy gold/silver" — a call-back lead, not an order.
 */
class BuyRequestController extends Controller
{
    /**
     * Options the request screen needs: which categories apply to each metal,
     * the bar sizes taken from admin products, and the rawa units.
     */
    public function options(): JsonResponse
    {
        $bars = Product::active()
            ->with('productCategory')
            ->whereHas('productCategory', fn ($q) => $q->where('slug', 'like', '%bars%'))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $sizes = [];
        foreach (['gold', 'silver'] as $metal) {
            $sizes[$metal] = $bars
                ->where('metal', $metal)
                ->map(fn (Product $p) => [
                    'product_id' => $p->id,
                    'name' => $p->name,
                    'weight' => $p->weight,
                    'packaging_charge' => (float) $p->packaging_charge,
                ])
                ->values()
                ->all();
        }

        return response()->json([
            'categories' => [
                'gold' => BuyRequest::categoriesForMetal('gold'),
                'silver' => BuyRequest::categoriesForMetal('silver'),
            ],
            'category_labels' => BuyRequest::categoryOptions(),
            'bar_sizes' => $sizes,
            'rawa_units' => BuyRequest::unitOptions(),
            'rawa_packaging_charge' => (float) Setting::get('rawa_packaging_charge', 0),
        ]);
    }

    /** Prices a selection without saving anything. Used by "Calculate". */
    public function quote(Request $request, BuyRequestPricing $pricing): JsonResponse
    {
        $data = $this->validateSelection($request);

        try {
            $priced = $this->priceSelection($data, $pricing);
        } catch (BuyRequestPricingException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['quote' => $priced['amounts']]);
    }

    /** Stores the lead. Prices are recalculated here, never trusted from the client. */
    public function store(Request $request, BuyRequestPricing $pricing): JsonResponse
    {
        $data = $this->validateSelection($request, withCustomer: true);

        try {
            $priced = $this->priceSelection($data, $pricing);
        } catch (BuyRequestPricingException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        $product = $priced['product'];

        $buyRequest = BuyRequest::create([
            'source' => $data['source'] ?? Order::SOURCE_APP,
            'metal' => $data['metal'],
            'category' => $data['category'],
            'product_id' => $product?->id,
            'product_name' => $product?->name,
            'product_weight' => $product?->weight,
            'weight_value' => $data['weight_value'] ?? null,
            'weight_unit' => $data['weight_unit'] ?? null,
            'unit_price' => $priced['amounts']['unit_price'],
            'packaging_charge' => $priced['amounts']['packaging_charge'],
            'total_amount' => $priced['amounts']['total_amount'],
            'customer_name' => $data['customer_name'],
            'customer_phone' => $data['customer_phone'],
            'status' => BuyRequest::STATUS_NEW,
        ]);

        return response()->json([
            'request' => [
                'reference' => $buyRequest->reference,
                'metal' => $buyRequest->metal,
                'category' => $buyRequest->category,
                'selection' => $buyRequest->selection_label,
                'unit_price' => (float) $buyRequest->unit_price,
                'packaging_charge' => (float) $buyRequest->packaging_charge,
                'total_amount' => (float) $buyRequest->total_amount,
                'customer_name' => $buyRequest->customer_name,
                'customer_phone' => $buyRequest->customer_phone,
            ],
        ], 201);
    }

    private function validateSelection(Request $request, bool $withCustomer = false): array
    {
        $rules = [
            'metal' => ['required', Rule::in(['gold', 'silver'])],
            'category' => ['required', Rule::in(array_keys(BuyRequest::categoryOptions()))],
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'weight_value' => ['nullable', 'numeric', 'min:0.0001', 'max:100000'],
            'weight_unit' => ['nullable', Rule::in(array_keys(BuyRequest::unitOptions()))],
            'source' => ['nullable', Rule::in(array_keys(BuyRequest::sourceOptions()))],
        ];

        if ($withCustomer) {
            $rules['customer_name'] = ['required', 'string', 'min:2', 'max:255'];
            $rules['customer_phone'] = ['required', 'string', 'min:10', 'max:20'];
        }

        $data = $request->validate($rules);

        // Rawa is gold only.
        if ($data['category'] === BuyRequest::CATEGORY_RAWA && $data['metal'] !== 'gold') {
            abort(response()->json(['message' => 'Rawa is only available for gold.'], 422));
        }

        if ($data['category'] === BuyRequest::CATEGORY_BAR && empty($data['product_id'])) {
            abort(response()->json(['message' => 'Please choose a size.'], 422));
        }

        if ($data['category'] === BuyRequest::CATEGORY_RAWA
            && (empty($data['weight_value']) || empty($data['weight_unit']))) {
            abort(response()->json(['message' => 'Please enter a weight and choose gram or tola.'], 422));
        }

        return $data;
    }

    /** @return array{amounts: array, product: ?Product} */
    private function priceSelection(array $data, BuyRequestPricing $pricing): array
    {
        if ($data['category'] === BuyRequest::CATEGORY_RAWA) {
            return [
                'amounts' => $pricing->priceRawa((float) $data['weight_value'], $data['weight_unit']),
                'product' => null,
            ];
        }

        $product = Product::active()->find($data['product_id']);

        if (! $product) {
            throw new BuyRequestPricingException('That size is no longer available.');
        }

        if ($product->metal !== $data['metal']) {
            throw new BuyRequestPricingException('That size does not match the selected metal.');
        }

        return ['amounts' => $pricing->priceBar($product), 'product' => $product];
    }
}
