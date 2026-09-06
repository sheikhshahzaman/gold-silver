<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Services\Cart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CatalogController extends Controller
{
    /**
     * Product catalog for the mobile app. Prices are resolved live
     * (fixed price, or spot price via price_key, discounts applied) —
     * the same logic the website cart uses.
     */
    public function products(Request $request, Cart $cart): JsonResponse
    {
        $query = Product::active()->ordered()->with('productCategory');

        if ($slug = $request->query('category')) {
            $query->whereHas('productCategory', fn ($q) => $q->where('slug', $slug));
        }

        $products = $query->get()->map(fn (Product $p) => [
            'id' => $p->id,
            'name' => $p->name,
            'slug' => $p->slug,
            'description' => $p->description,
            'weight' => $p->weight,
            'metal' => $p->metal,
            'karat' => $p->karat,
            'image' => $p->image ? url(Storage::url($p->image)) : null,
            // Small version for list tiles; the app never draws these larger
            // than 68px, so shipping the full picture wasted seconds.
            'image_thumb' => $p->thumbnailUrl(),
            'category' => $p->productCategory ? [
                'id' => $p->productCategory->id,
                'name' => $p->productCategory->name,
                'slug' => $p->productCategory->slug,
            ] : null,
            'price_type' => $p->price_type,
            'current_price' => $cart->unitPriceFor($p),
            'packaging_charge' => (float) $p->packaging_charge,
            'discount_label' => $p->discount_label,
            'stock_count' => $p->stock_count,
        ]);

        return response()->json([
            'products' => $products,
            'last_updated' => now()->toIso8601String(),
        ], 200, ['Cache-Control' => 'no-store']);
    }

    public function categories(): JsonResponse
    {
        $categories = ProductCategory::active()
            ->orderBy('sort_order')
            ->get()
            ->map(fn (ProductCategory $c) => [
                'id' => $c->id,
                'name' => $c->name,
                'slug' => $c->slug,
                'icon' => $c->icon,
            ]);

        return response()->json(['categories' => $categories]);
    }
}
