<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InternalLiveRatesController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $expectedToken = (string) config('services.rates.token', '');

        if ($expectedToken !== '') {
            $providedToken = (string) ($request->header('X-Rates-Token') ?: $request->bearerToken());

            if (! hash_equals($expectedToken, $providedToken)) {
                abort(403);
            }
        }

        $cacheManager = \App\Services\PriceEngine\PriceCacheManager::class;

        if (! class_exists($cacheManager)) {
            abort(404);
        }

        return response()->json(
            app($cacheManager)->getAllPrices(),
            200,
            ['Cache-Control' => 'no-store'],
        );
    }
}
