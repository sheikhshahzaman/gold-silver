<?php

namespace App\Services\Rates;

use App\Models\PriceMargin;

class PriceCatalogBuilder
{
    public function build(array $goldPrices = []): array
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
}
