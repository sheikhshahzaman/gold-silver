<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PriceMargin;
use App\Models\User;
use App\Services\PriceEngine\PriceFetcher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PriceMarginSyncController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $expectedToken = (string) config('services.price_margin_sync.token', '');
        $providedToken = (string) ($request->bearerToken() ?: $request->header('X-Price-Sync-Token', ''));

        if ($expectedToken === '' || ! hash_equals($expectedToken, $providedToken)) {
            abort(403);
        }

        $validated = $request->validate([
            'rows' => ['required', 'array', 'min:1'],
            'rows.*.metal' => ['required', 'string', 'in:gold,silver'],
            'rows.*.karat' => ['nullable', 'string', 'max:30'],
            'rows.*.unit' => ['nullable', 'string', 'max:50'],
            'rows.*.buy_margin' => ['nullable', 'numeric'],
            'rows.*.sell_margin' => ['nullable', 'numeric'],
            'rows.*.manual_buy_price' => ['nullable', 'numeric'],
            'rows.*.manual_sell_price' => ['nullable', 'numeric'],
            'rows.*.updated_by' => ['nullable', 'integer'],
            'rows.*.created_at' => ['nullable', 'date'],
            'rows.*.updated_at' => ['nullable', 'date'],
        ]);

        DB::transaction(function () use ($validated): void {
            foreach ($validated['rows'] as $row) {
                $updatedBy = $row['updated_by'] ?? null;
                if ($updatedBy && ! User::query()->whereKey($updatedBy)->exists()) {
                    $updatedBy = null;
                }

                PriceMargin::query()->updateOrCreate(
                    [
                        'metal' => $row['metal'],
                        'karat' => $row['karat'] ?? null,
                        'unit' => $row['unit'] ?? null,
                    ],
                    [
                        'buy_margin' => $row['buy_margin'] ?? 0,
                        'sell_margin' => $row['sell_margin'] ?? 0,
                        'manual_buy_price' => $row['manual_buy_price'] ?? null,
                        'manual_sell_price' => $row['manual_sell_price'] ?? null,
                        'updated_by' => $updatedBy,
                        'created_at' => $row['created_at'] ?? now(),
                        'updated_at' => $row['updated_at'] ?? now(),
                    ],
                );
            }
        });

        $this->clearPriceCaches();

        if (class_exists(PriceFetcher::class)) {
            try {
                app(PriceFetcher::class)->fetchAndStore();
            } catch (\Throwable $e) {
                Log::warning('Price margin sync saved rows but failed to rebuild price cache', [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'ok' => true,
            'synced' => count($validated['rows']),
        ], 200, ['Cache-Control' => 'no-store']);
    }

    private function clearPriceCaches(): void
    {
        foreach ([
            'prices.all_prices',
            'prices.gold',
            'prices.silver',
            'prices.currencies',
            'prices.international',
            'prices.platinum',
            'prices.palladium',
            'prices.crude_oil',
            'prices.psx',
            'prices.last_updated',
            'rates.remote.current',
            'rates.remote.stale',
        ] as $key) {
            Cache::forget($key);
        }
    }
}
