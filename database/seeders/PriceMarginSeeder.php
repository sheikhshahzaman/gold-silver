<?php

namespace Database\Seeders;

use App\Models\PriceMargin;
use Illuminate\Database\Seeder;

class PriceMarginSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $margins = [
            ['metal' => 'gold', 'karat' => '24K', 'unit' => null],
            ['metal' => 'gold', 'karat' => 'Rawa', 'unit' => null],
            ['metal' => 'gold', 'karat' => '22K', 'unit' => null],
            ['metal' => 'gold', 'karat' => '21K', 'unit' => null],
            ['metal' => 'gold', 'karat' => '18K', 'unit' => null],
            ['metal' => 'silver', 'karat' => null, 'unit' => '10_tola_qr'],
            ['metal' => 'silver', 'karat' => null, 'unit' => '10_tola'],
            ['metal' => 'silver', 'karat' => null, 'unit' => 'kg'],
            ['metal' => 'silver', 'karat' => null, 'unit' => '5_tola'],
            ['metal' => 'silver', 'karat' => null, 'unit' => 'tola'],
        ];

        foreach ($margins as $margin) {
            PriceMargin::updateOrCreate(
                ['metal' => $margin['metal'], 'karat' => $margin['karat'], 'unit' => $margin['unit']],
                [
                    'buy_margin' => 0,
                    'sell_margin' => 0,
                ]
            );
        }
    }
}
