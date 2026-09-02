<?php

namespace App\Services\Rates;

use App\Models\PriceMargin;
use App\Services\Rates\Concerns\NormalizesRates;
use Illuminate\Support\Carbon;

class ManualRatesProvider implements RatesProvider
{
    use NormalizesRates;

    private const TOLA_IN_GRAMS = 11.6638;
    private const TROY_OUNCE_IN_GRAMS = 31.1035;
    private const GOLD_KARATS = ['24k', 'rawa', '22k', '21k', '18k'];
    private const GOLD_UNITS = ['tola', '10_gram', '5_gram', 'gram'];
    private const SILVER_UNITS = ['10_tola_qr', '10_tola', 'kg', '5_tola', 'tola', '10_gram', '5_gram', 'gram'];

    public function getAllPrices(): array
    {
        $gold = $this->goldPrices();
        $silver = $this->silverPrices();

        return $this->normalizeRates([
            'gold' => $gold,
            'silver' => $silver,
            'price_catalog' => $this->priceCatalog($gold),
            'last_updated' => $this->lastUpdated(),
            'rate_mode' => 'manual',
        ]);
    }

    private function goldPrices(): array
    {
        $rows = PriceMargin::query()
            ->where('metal', 'gold')
            ->get()
            ->keyBy(fn (PriceMargin $row): string => strtolower((string) $row->karat));

        $base24k = $rows->get('24k');
        $base24kBuy = (float) ($base24k?->manual_buy_price ?? 0);
        $base24kSell = (float) ($base24k?->manual_sell_price ?? 0);
        $prices = [];

        foreach (self::GOLD_KARATS as $karat) {
            $row = $rows->get($karat);
            $buyPerTola = (float) ($row?->manual_buy_price ?? 0);
            $sellPerTola = (float) ($row?->manual_sell_price ?? 0);

            if ($buyPerTola <= 0 && $base24kBuy > 0) {
                $buyPerTola = $this->applyKaratPurity($base24kBuy, $karat);
            }

            if ($sellPerTola <= 0 && $base24kSell > 0) {
                $sellPerTola = $this->applyKaratPurity($base24kSell, $karat);
            }

            if ($buyPerTola <= 0 && $sellPerTola <= 0) {
                continue;
            }

            $buyUnits = $this->deriveUnitPrices($buyPerTola);
            $sellUnits = $this->deriveUnitPrices($sellPerTola);

            foreach (self::GOLD_UNITS as $unit) {
                $prices[$karat][$unit] = [
                    'buy' => $buyUnits[$unit] ?? 0,
                    'sell' => $sellUnits[$unit] ?? 0,
                    'base' => $buyUnits[$unit] ?? 0,
                ];
            }
        }

        return $prices;
    }

    private function silverPrices(): array
    {
        $rows = PriceMargin::query()
            ->where('metal', 'silver')
            ->get()
            ->keyBy('unit');

        $tolaRow = $rows->get('tola');
        $tolaBuy = (float) ($tolaRow?->manual_buy_price ?? 0);
        $tolaSell = (float) ($tolaRow?->manual_sell_price ?? 0);
        $derivedBuy = $tolaBuy > 0 ? $this->deriveUnitPrices($tolaBuy) : [];
        $derivedSell = $tolaSell > 0 ? $this->deriveUnitPrices($tolaSell) : [];
        $prices = [];

        foreach (self::SILVER_UNITS as $unit) {
            $row = $rows->get($unit);
            $buy = (float) ($row?->manual_buy_price ?? 0);
            $sell = (float) ($row?->manual_sell_price ?? 0);

            if ($buy <= 0) {
                $buy = $this->derivedSilverUnitPrice($unit, $derivedBuy, $rows, 'manual_buy_price');
            }

            if ($sell <= 0) {
                $sell = $this->derivedSilverUnitPrice($unit, $derivedSell, $rows, 'manual_sell_price');
            }

            $prices[$unit] = [
                'buy' => round($buy, 2),
                'sell' => round($sell, 2),
                'base' => round($buy, 2),
            ];
        }

        return $prices;
    }

    private function derivedSilverUnitPrice(string $unit, array $derived, $rows, string $column): float
    {
        if ($unit === '10_tola_qr') {
            $tenTola = (float) ($rows->get('10_tola')?->{$column} ?? 0);

            if ($tenTola > 0) {
                return $tenTola;
            }
        }

        if ($unit === '5_tola') {
            return (float) (($derived['tola'] ?? 0) * 5);
        }

        return (float) ($derived[$unit] ?? 0);
    }

    private function deriveUnitPrices(float $perTolaPrice): array
    {
        $pricePerGram = $perTolaPrice / self::TOLA_IN_GRAMS;

        return [
            'tola' => round($perTolaPrice, 2),
            '10_tola' => round($perTolaPrice * 10, 2),
            '10_gram' => round($pricePerGram * 10, 2),
            '5_gram' => round($pricePerGram * 5, 2),
            'gram' => round($pricePerGram, 2),
            'kg' => round($pricePerGram * 1000, 2),
            'ounce' => round($pricePerGram * self::TROY_OUNCE_IN_GRAMS, 2),
        ];
    }

    private function applyKaratPurity(float $price24k, string $karat): float
    {
        return round($price24k * match (strtolower($karat)) {
            'rawa' => 0.995,
            '22k' => 0.9167,
            '21k' => 0.875,
            '18k' => 0.75,
            default => 1,
        }, 2);
    }

    private function priceCatalog(array $goldPrices): array
    {
        $goldKarats = PriceMargin::query()
            ->where('metal', 'gold')
            ->whereNotNull('karat')
            ->orderByRaw("CASE LOWER(karat) WHEN '24k' THEN 0 WHEN 'rawa' THEN 1 WHEN '22k' THEN 2 WHEN '21k' THEN 3 WHEN '18k' THEN 4 ELSE 99 END")
            ->orderBy('id')
            ->get()
            ->mapWithKeys(fn (PriceMargin $row): array => [
                strtolower((string) $row->karat) => $this->karatLabel((string) $row->karat),
            ])
            ->all();

        $firstGoldRow = reset($goldPrices);
        $goldUnits = is_array($firstGoldRow)
            ? collect(array_keys($firstGoldRow))
                ->mapWithKeys(fn (string $unit): array => [$unit => $this->unitLabel($unit, 'gold')])
                ->all()
            : [];

        $silverUnits = PriceMargin::query()
            ->where('metal', 'silver')
            ->whereNotNull('unit')
            ->orderByRaw("CASE unit WHEN '10_tola_qr' THEN 0 WHEN '10_tola' THEN 1 WHEN 'kg' THEN 2 WHEN '5_tola' THEN 3 WHEN 'tola' THEN 4 ELSE 99 END")
            ->orderBy('id')
            ->get()
            ->mapWithKeys(fn (PriceMargin $row): array => [
                (string) $row->unit => $this->unitLabel((string) $row->unit, 'silver'),
            ])
            ->all();

        return [
            'metals' => array_filter([
                'gold' => $goldKarats ? 'Gold' : null,
                'silver' => $silverUnits ? 'Silver' : null,
            ]),
            'gold' => [
                'karats' => $goldKarats,
                'units' => $goldUnits,
            ],
            'silver' => [
                'units' => $silverUnits,
            ],
        ];
    }

    private function karatLabel(string $karat): string
    {
        return strtolower($karat) === 'rawa' ? 'Rawa' : strtoupper($karat);
    }

    private function unitLabel(string $unit, string $metal): string
    {
        return match ($unit) {
            '10_tola_qr' => '10 Tola (QR Packaging)',
            '10_tola' => $metal === 'silver' ? '10 Tola (999)' : '10 Tola',
            '5_tola' => $metal === 'silver' ? '5 Tola (Bar)' : '5 Tola',
            'tola' => $metal === 'silver' ? '1 Tola (Bar)' : '1 Tola',
            'kg' => '1 KG',
            '10_gram' => '10 Gram',
            '5_gram' => '5 Gram',
            'gram' => '1 Gram',
            default => ucwords(str_replace('_', ' ', $unit)),
        };
    }

    private function lastUpdated(): string
    {
        $lastUpdated = PriceMargin::query()->max('updated_at');

        return $lastUpdated ? Carbon::parse($lastUpdated)->toIso8601String() : now()->toIso8601String();
    }
}
