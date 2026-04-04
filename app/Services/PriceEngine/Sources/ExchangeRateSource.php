<?php

namespace App\Services\PriceEngine\Sources;

use App\Services\PriceEngine\Contracts\PriceSourceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExchangeRateSource implements PriceSourceInterface
{
    private const API_URL = 'https://open.er-api.com/v6/latest/USD';
    private const TIMEOUT = 10;

    /**
     * The currencies we need, mapped to their key name in our output.
     * For PKR the rate is direct (USD to PKR).
     * For GBP, EUR, SAR, AED, MYR the API gives USD->X, so we store as-is.
     */
    private const REQUIRED_CURRENCIES = ['PKR', 'GBP', 'EUR', 'SAR', 'AED', 'MYR'];

    private array $lastFetchedData = [];

    public function fetch(): array
    {
        try {
            $response = Http::timeout(self::TIMEOUT)->get(self::API_URL);

            if (!$response->successful()) {
                Log::warning('ExchangeRateSource: API call failed', [
                    'status' => $response->status(),
                ]);
                return [];
            }

            $data = $response->json();

            if (($data['result'] ?? '') !== 'success' || empty($data['rates'])) {
                Log::warning('ExchangeRateSource: Unexpected response format', [
                    'data' => $data,
                ]);
                return [];
            }

            $rates = $data['rates'];
            $result = [];

            foreach (self::REQUIRED_CURRENCIES as $currency) {
                if (!isset($rates[$currency])) {
                    Log::warning("ExchangeRateSource: Missing rate for {$currency}");
                    continue;
                }

                $key = strtolower($currency) . '_usd';
                if ($currency === 'PKR') {
                    // USD to PKR is a direct rate
                    $result['usd_pkr'] = (float) $rates[$currency];
                } else {
                    // Store as currency_usd (how many USD per 1 unit of currency)
                    // The API gives USD->X, so 1/rate = X->USD
                    $result[$key] = (float) $rates[$currency];
                }
            }

            $this->lastFetchedData = $result;

            return $this->lastFetchedData;
        } catch (\Throwable $e) {
            Log::error('ExchangeRateSource: Exception during fetch', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return [];
        }
    }

    public function getSourceName(): string
    {
        return 'exchangerate';
    }

    public function isAvailable(): bool
    {
        if (empty($this->lastFetchedData)) {
            $this->fetch();
        }

        return !empty($this->lastFetchedData) && isset($this->lastFetchedData['usd_pkr']);
    }
}
