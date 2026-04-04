<?php

namespace App\Services\PriceEngine\Sources;

use App\Services\PriceEngine\Contracts\PriceSourceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoldApiSource implements PriceSourceInterface
{
    private const BASE_URL = 'https://api.gold-api.com/price';
    private const TIMEOUT = 10;
    private const RETRY_TIMES = 1;
    private const RETRY_DELAY_MS = 500;

    private array $lastFetchedData = [];

    public function fetch(): array
    {
        try {
            $goldResponse = Http::timeout(self::TIMEOUT)
                ->retry(self::RETRY_TIMES, self::RETRY_DELAY_MS)
                ->get(self::BASE_URL . '/XAU');

            $silverResponse = Http::timeout(self::TIMEOUT)
                ->retry(self::RETRY_TIMES, self::RETRY_DELAY_MS)
                ->get(self::BASE_URL . '/XAG');

            if (!$goldResponse->successful() || !$silverResponse->successful()) {
                Log::warning('GoldApiSource: One or both API calls failed', [
                    'gold_status' => $goldResponse->status(),
                    'silver_status' => $silverResponse->status(),
                ]);
                return [];
            }

            $goldData = $goldResponse->json();
            $silverData = $silverResponse->json();

            $xauUsd = $goldData['price'] ?? $goldData['price_gram_24k'] ?? null;
            $xagUsd = $silverData['price'] ?? $silverData['price_gram_24k'] ?? null;

            if ($xauUsd === null || $xagUsd === null) {
                Log::warning('GoldApiSource: Could not extract prices from response', [
                    'gold_data' => $goldData,
                    'silver_data' => $silverData,
                ]);
                return [];
            }

            $this->lastFetchedData = [
                'xau_usd' => (float) $xauUsd,
                'xag_usd' => (float) $xagUsd,
            ];

            return $this->lastFetchedData;
        } catch (\Throwable $e) {
            Log::error('GoldApiSource: Exception during fetch', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return [];
        }
    }

    public function getSourceName(): string
    {
        return 'goldapi';
    }

    public function isAvailable(): bool
    {
        if (empty($this->lastFetchedData)) {
            $this->fetch();
        }

        return !empty($this->lastFetchedData)
            && isset($this->lastFetchedData['xau_usd'])
            && isset($this->lastFetchedData['xag_usd'])
            && $this->lastFetchedData['xau_usd'] > 0
            && $this->lastFetchedData['xag_usd'] > 0;
    }
}
