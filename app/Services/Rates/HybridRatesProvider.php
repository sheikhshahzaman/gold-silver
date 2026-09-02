<?php

namespace App\Services\Rates;

use App\Models\CurrencyRate;
use App\Models\MetalPrice;
use App\Services\Rates\Concerns\NormalizesRates;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class HybridRatesProvider implements RatesProvider
{
    use NormalizesRates;

    public function __construct(
        private readonly ManualRatesProvider $manual,
        private readonly RemoteRatesProvider $remote,
    ) {
    }

    public function getAllPrices(): array
    {
        $manualPrices = $this->manual->getAllPrices();
        $remotePrices = $this->remote->getAllPrices();
        $marketOverrides = $this->adminMarketOverrides();

        $merged = array_merge($remotePrices, [
            'gold' => $manualPrices['gold'] ?? [],
            'silver' => $manualPrices['silver'] ?? [],
            'price_catalog' => $manualPrices['price_catalog'] ?? [],
            'last_updated' => $manualPrices['last_updated'] ?? ($remotePrices['last_updated'] ?? now()->toIso8601String()),
            'rate_mode' => 'manual',
        ]);

        if (! empty($marketOverrides['international'])) {
            $merged['international'] = array_replace($merged['international'] ?? [], $marketOverrides['international']);
        }

        if (! empty($marketOverrides['quotes'])) {
            $merged['quotes'] = array_replace($merged['quotes'] ?? [], $marketOverrides['quotes']);
        }

        if (! empty($marketOverrides['currencies'])) {
            $merged['currencies'] = array_replace($merged['currencies'] ?? [], $marketOverrides['currencies']);
        }

        if (! empty($marketOverrides['last_updated'])) {
            $merged['last_updated'] = $this->latestTimestamp(
                $merged['last_updated'] ?? null,
                $marketOverrides['last_updated'],
            );
        }

        return $this->normalizeRates($merged);
    }

    private function adminMarketOverrides(): array
    {
        $overrides = [
            'international' => [],
            'quotes' => [],
            'currencies' => [],
            'last_updated' => null,
        ];

        if (Schema::hasTable('metal_prices')) {
            $gold = MetalPrice::query()
                ->where('metal', 'gold')
                ->where('type', 'international')
                ->where('source', 'admin')
                ->orderByDesc('fetched_at')
                ->first();

            if ($gold) {
                $overrides['international']['xau_usd'] = (float) $gold->buy_price;
                $overrides['quotes']['gold'] = [
                    'bid' => (float) $gold->buy_price,
                    'ask' => (float) ($gold->sell_price ?? $gold->buy_price),
                ];
                $overrides['last_updated'] = $this->latestTimestamp($overrides['last_updated'], $gold->fetched_at?->toIso8601String());
            }

            $silver = MetalPrice::query()
                ->where('metal', 'silver')
                ->where('type', 'international')
                ->where('source', 'admin')
                ->orderByDesc('fetched_at')
                ->first();

            if ($silver) {
                $overrides['international']['xag_usd'] = (float) $silver->buy_price;
                $overrides['quotes']['silver'] = [
                    'bid' => (float) $silver->buy_price,
                    'ask' => (float) ($silver->sell_price ?? $silver->buy_price),
                ];
                $overrides['last_updated'] = $this->latestTimestamp($overrides['last_updated'], $silver->fetched_at?->toIso8601String());
            }
        }

        if (Schema::hasTable('currency_rates')) {
            $pairToKey = [
                'USD/PKR' => 'usd_pkr',
                'USD Interbank' => 'usd_interbank',
                'GBP/PKR' => 'gbp_pkr',
                'EUR/PKR' => 'eur_pkr',
                'SAR/PKR' => 'sar_pkr',
                'AED/PKR' => 'aed_pkr',
                'MYR/PKR' => 'myr_pkr',
            ];

            foreach ($pairToKey as $pair => $key) {
                $rate = CurrencyRate::query()
                    ->where('currency_pair', $pair)
                    ->where('source', 'admin')
                    ->orderByDesc('fetched_at')
                    ->first();

                if (! $rate) {
                    continue;
                }

                $overrides['currencies'][$key] = [
                    'buy' => (float) $rate->buy_rate,
                    'sell' => (float) $rate->sell_rate,
                ];
                $overrides['last_updated'] = $this->latestTimestamp($overrides['last_updated'], $rate->fetched_at?->toIso8601String());
            }
        }

        return $overrides;
    }

    private function latestTimestamp(?string $first, ?string $second): ?string
    {
        if (! $first) {
            return $second;
        }

        if (! $second) {
            return $first;
        }

        return Carbon::parse($first)->greaterThan(Carbon::parse($second)) ? $first : $second;
    }
}
