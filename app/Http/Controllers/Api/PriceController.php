<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\PriceEngine\PriceCacheManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class PriceController extends Controller
{
    public function index(PriceCacheManager $cacheManager): JsonResponse
    {
        $prices = $cacheManager->getAllPrices();

        $goldSpreadPct = (float) Cache::remember('setting.intl_spread_gold', 60,
            fn () => Setting::get('international_spread_gold_pct', 0.05));
        $silverSpreadPct = (float) Cache::remember('setting.intl_spread_silver', 60,
            fn () => Setting::get('international_spread_silver_pct', 0.1));

        $goldBid = $prices['international']['xau_usd'] ?? null;
        $silverBid = $prices['international']['xag_usd'] ?? null;

        $prices['quotes'] = [
            'gold' => $goldBid ? ['bid' => (float) $goldBid, 'ask' => round((float) $goldBid * (1 + $goldSpreadPct / 100), 2)] : null,
            'silver' => $silverBid ? ['bid' => (float) $silverBid, 'ask' => round((float) $silverBid * (1 + $silverSpreadPct / 100), 2)] : null,
        ];

        return response()->json($prices, 200, ['Cache-Control' => 'no-store']);
    }
}
