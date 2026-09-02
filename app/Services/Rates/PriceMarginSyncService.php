<?php

namespace App\Services\Rates;

use App\Models\PriceMargin;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PriceMarginSyncService
{
    public function sync(): bool
    {
        $url = trim((string) config('services.price_margin_sync.url', ''));
        $token = trim((string) config('services.price_margin_sync.token', ''));

        if ($url === '' || $token === '') {
            return false;
        }

        $rows = PriceMargin::query()
            ->orderBy('id')
            ->get()
            ->map(fn (PriceMargin $row): array => [
                'metal' => $row->metal,
                'karat' => $row->karat,
                'unit' => $row->unit,
                'buy_margin' => $row->buy_margin,
                'sell_margin' => $row->sell_margin,
                'manual_buy_price' => $row->manual_buy_price,
                'manual_sell_price' => $row->manual_sell_price,
                'updated_by' => $row->updated_by,
                'created_at' => optional($row->created_at)->toDateTimeString(),
                'updated_at' => optional($row->updated_at)->toDateTimeString(),
            ])
            ->values()
            ->all();

        try {
            $request = Http::acceptJson()
                ->asJson()
                ->timeout((int) config('services.price_margin_sync.timeout', 10))
                ->withToken($token);

            $host = trim((string) config('services.price_margin_sync.host_header', ''));
            if ($host !== '') {
                $request = $request->withHeaders(['Host' => $host]);
            }

            if (! filter_var(config('services.price_margin_sync.verify_ssl', true), FILTER_VALIDATE_BOOLEAN)) {
                $request = $request->withoutVerifying();
            }

            $response = $request->post($url, ['rows' => $rows]);

            if (! $response->successful()) {
                Log::warning('Price margin sync failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::warning('Price margin sync threw an exception', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
