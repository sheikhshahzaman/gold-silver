<?php

namespace App\Services\Rates;

use App\Services\Rates\Concerns\NormalizesRates;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RemoteRatesProvider implements RatesProvider
{
    use NormalizesRates;

    private const CACHE_KEY = 'rates.remote.current';
    private const STALE_KEY = 'rates.remote.stale';

    public function getAllPrices(): array
    {
        $ttl = max(1, (int) config('services.rates.cache_ttl', 10));

        return Cache::remember(self::CACHE_KEY, $ttl, function () {
            $fresh = $this->fetchRemotePrices();

            if ($fresh !== null) {
                Cache::put(self::STALE_KEY, $fresh, max(60, (int) config('services.rates.stale_ttl', 300)));
                return $fresh;
            }

            return Cache::get(self::STALE_KEY, $this->normalizeRates([]));
        });
    }

    private function fetchRemotePrices(): ?array
    {
        $url = trim((string) config('services.rates.url', ''));

        if ($url === '') {
            return null;
        }

        try {
            $request = Http::acceptJson()
                ->timeout(max(2, (int) config('services.rates.timeout', 8)))
                ->withHeaders($this->headers());

            if (! filter_var(config('services.rates.verify_ssl', true), FILTER_VALIDATE_BOOLEAN)) {
                $request = $request->withoutVerifying();
            }

            $response = $request->get($url);

            if (! $response->successful() || ! is_array($response->json())) {
                Log::warning('Remote rates request failed', [
                    'url' => $url,
                    'status' => $response->status(),
                ]);

                return null;
            }

            return $this->normalizeRates($response->json());
        } catch (\Throwable $e) {
            Log::warning('Remote rates request threw an exception', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function headers(): array
    {
        $headers = [];
        $token = trim((string) config('services.rates.token', ''));
        $host = trim((string) config('services.rates.host_header', ''));

        if ($token !== '') {
            $headers['X-Rates-Token'] = $token;
        }

        if ($host !== '') {
            $headers['Host'] = $host;
        }

        return $headers;
    }
}
