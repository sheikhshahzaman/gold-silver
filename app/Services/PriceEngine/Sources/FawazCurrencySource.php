<?php

namespace App\Services\PriceEngine\Sources;

use App\Services\PriceEngine\Contracts\PriceSourceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FawazCurrencySource implements PriceSourceInterface
{
    private const API_URL = 'https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies/usd.json';
    private const TIMEOUT = 10;

    private array $lastFetchedData = [];

    public function fetch(): array
    {
        try {
            $response = Http::timeout(self::TIMEOUT)->get(self::API_URL);

            if (!$response->successful()) {
                Log::warning('FawazCurrencySource: API call failed', [
                    'status' => $response->status(),
                ]);
                return [];
            }

            $data = $response->json();
            $rates = $data['usd'] ?? [];

            if (empty($rates)) {
                Log::warning('FawazCurrencySource: No rates found in response');
                return [];
            }

            $result = [];

            // Gold: the API gives how many XAU per 1 USD
            // So 1 USD = x XAU, meaning 1 XAU = 1/x USD
            if (isset($rates['xau']) && $rates['xau'] > 0) {
                $result['xau_usd'] = 1.0 / (float) $rates['xau'];
            }

            // Silver: same logic
            if (isset($rates['xag']) && $rates['xag'] > 0) {
                $result['xag_usd'] = 1.0 / (float) $rates['xag'];
            }

            // PKR rate: 1 USD = x PKR
            if (isset($rates['pkr'])) {
                $result['usd_pkr'] = (float) $rates['pkr'];
            }

            // Other currencies: USD to X
            $currencyMap = [
                'gbp' => 'gbp_usd',
                'eur' => 'eur_usd',
                'sar' => 'sar_usd',
                'aed' => 'aed_usd',
                'myr' => 'myr_usd',
            ];

            foreach ($currencyMap as $apiKey => $outputKey) {
                if (isset($rates[$apiKey])) {
                    $result[$outputKey] = (float) $rates[$apiKey];
                }
            }

            $this->lastFetchedData = $result;

            return $this->lastFetchedData;
        } catch (\Throwable $e) {
            Log::error('FawazCurrencySource: Exception during fetch', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return [];
        }
    }

    public function getSourceName(): string
    {
        return 'fawaz';
    }

    public function isAvailable(): bool
    {
        if (empty($this->lastFetchedData)) {
            $this->fetch();
        }

        return !empty($this->lastFetchedData)
            && isset($this->lastFetchedData['xau_usd'])
            && isset($this->lastFetchedData['usd_pkr'])
            && $this->lastFetchedData['xau_usd'] > 0;
    }
}
