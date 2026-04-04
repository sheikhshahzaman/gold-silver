<?php

namespace App\Services\PriceEngine\Sources;

use App\Services\PriceEngine\Contracts\PriceSourceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PakGoldScraper implements PriceSourceInterface
{
    private const BASE_URL = 'https://www.pakgold.net';
    private const TIMEOUT = 15;

    private array $lastFetchedData = [];
    private bool $available = false;

    public function fetch(): array
    {
        try {
            $goldRate = $this->callApi('/PGR/GoldRate');
            $currencyRate = $this->callApi('/PGR/CurrencyRate');
            $boardRate = $this->callApi('/PGR/BoardRate');

            if (empty($goldRate) && empty($currencyRate) && empty($boardRate)) {
                Log::warning('PakGoldScraper: All API endpoints returned empty data');
                $this->available = false;
                return [];
            }

            $data = [
                'gold' => [
                    '24k' => [
                        'buy_per_tola' => floatval($goldRate['pcsBuy'] ?? 0),
                        'sell_per_tola' => floatval($goldRate['pcsSell'] ?? 0),
                    ],
                    'rawa' => [
                        'buy_per_tola' => floatval($goldRate['rawaBuy'] ?? 0),
                        'sell_per_tola' => floatval($goldRate['rawaSell'] ?? 0),
                    ],
                    '22k' => [
                        'buy_per_tola' => floatval($boardRate['_22ktBuy'] ?? 0),
                        'sell_per_tola' => floatval($boardRate['_22ktSell'] ?? 0),
                        'buy_per_10gram' => floatval($boardRate['_22ktBuy10'] ?? 0),
                        'sell_per_10gram' => floatval($boardRate['_22ktSell10'] ?? 0),
                    ],
                    '21k' => [
                        'buy_per_tola' => floatval($boardRate['_21ktBuy'] ?? 0),
                        'sell_per_tola' => floatval($boardRate['_21ktSell'] ?? 0),
                        'buy_per_10gram' => floatval($boardRate['_21ktBuy10'] ?? 0),
                        'sell_per_10gram' => floatval($boardRate['_21ktSell10'] ?? 0),
                    ],
                ],
                'silver' => [
                    'reti' => [
                        'buy_per_tola' => floatval($goldRate['localSilverBuy'] ?? 0),
                        'sell_per_tola' => floatval($goldRate['localSilverSell'] ?? 0),
                    ],
                    'pees' => [
                        'buy_per_tola' => floatval($goldRate['tTbarSilverBuy'] ?? 0),
                        'sell_per_tola' => floatval($goldRate['tTbarSilverSell'] ?? 0),
                    ],
                ],
                'international' => [
                    'xau_usd' => floatval($goldRate['gold'] ?? 0),
                    'xag_usd' => floatval($goldRate['silver'] ?? 0),
                ],
                'platinum' => [
                    'international' => floatval($goldRate['ptInternational'] ?? 0),
                    'local' => floatval($goldRate['ptLocal'] ?? 0),
                ],
                'palladium' => [
                    'international' => floatval($goldRate['pdInternational'] ?? 0),
                    'local' => floatval($goldRate['pdLocal'] ?? 0),
                ],
                'crude_oil' => floatval($goldRate['cruideOil'] ?? 0),
                'psx' => [
                    'index' => floatval($goldRate['kse'] ?? 0),
                    'high' => floatval($goldRate['kseHigh'] ?? 0),
                    'low' => floatval($goldRate['kseLow'] ?? 0),
                    'change' => floatval($goldRate['ksePoint'] ?? 0),
                ],
                'currency' => [
                    'usd_pkr' => [
                        'buy' => floatval($currencyRate['dollarBuy'] ?? 0),
                        'sell' => floatval($currencyRate['dollarSell'] ?? 0),
                    ],
                    'usd_interbank' => [
                        'buy' => floatval($currencyRate['dollarInterBankBuy'] ?? 0),
                        'sell' => floatval($currencyRate['dollarInterBankSell'] ?? 0),
                    ],
                    'gbp_pkr' => [
                        'buy' => floatval($currencyRate['gbpBuy'] ?? 0),
                        'sell' => floatval($currencyRate['gbpSell'] ?? 0),
                    ],
                    'eur_pkr' => [
                        'buy' => floatval($currencyRate['euroBuy'] ?? 0),
                        'sell' => floatval($currencyRate['euroSell'] ?? 0),
                    ],
                    'myr_pkr' => [
                        'buy' => floatval($currencyRate['malayBuy'] ?? 0),
                        'sell' => floatval($currencyRate['malaySell'] ?? 0),
                    ],
                    'sar_pkr' => [
                        'buy' => floatval($currencyRate['riyalBuy'] ?? 0),
                        'sell' => floatval($currencyRate['riyalSell'] ?? 0),
                    ],
                    'aed_pkr' => [
                        'buy' => floatval($currencyRate['dirhamBuy'] ?? 0),
                        'sell' => floatval($currencyRate['dirhamSell'] ?? 0),
                    ],
                ],
                'update_times' => [
                    'rate' => $goldRate['rateUpdateTime'] ?? null,
                    'international' => $goldRate['internalMarketUpdateTime'] ?? null,
                    'currency' => $currencyRate['dollarUpdateTime'] ?? null,
                ],
            ];

            // Verify we got meaningful gold data
            $gold24kBuy = $data['gold']['24k']['buy_per_tola'] ?? 0;
            if ($gold24kBuy < 1000) {
                Log::warning('PakGoldScraper: Gold 24k buy price suspiciously low', ['price' => $gold24kBuy]);
                $this->available = false;
                return [];
            }

            $this->lastFetchedData = $data;
            $this->available = true;

            return $this->lastFetchedData;
        } catch (\Throwable $e) {
            Log::error('PakGoldScraper: Exception during fetch', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->available = false;
            return [];
        }
    }

    public function getSourceName(): string
    {
        return 'pakgold';
    }

    public function isAvailable(): bool
    {
        if (empty($this->lastFetchedData)) {
            $this->fetch();
        }

        return $this->available;
    }

    /**
     * Call a PakGold JSON API endpoint.
     *
     * @param string $path The API path (e.g., '/PGR/GoldRate')
     * @return array The decoded JSON response, or empty array on failure.
     */
    private function callApi(string $path): array
    {
        try {
            $response = Http::timeout(self::TIMEOUT)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept' => 'application/json, text/plain, */*',
                    'Accept-Language' => 'en-US,en;q=0.5',
                    'Referer' => self::BASE_URL . '/',
                ])
                ->get(self::BASE_URL . $path);

            if (!$response->successful()) {
                Log::warning('PakGoldScraper: API request failed', [
                    'path' => $path,
                    'status' => $response->status(),
                ]);
                return [];
            }

            $json = $response->json();

            if (!is_array($json)) {
                Log::warning('PakGoldScraper: API response is not valid JSON', [
                    'path' => $path,
                ]);
                return [];
            }

            return $json;
        } catch (\Throwable $e) {
            Log::error('PakGoldScraper: API call exception', [
                'path' => $path,
                'message' => $e->getMessage(),
            ]);
            return [];
        }
    }
}
