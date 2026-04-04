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
            ['metal' => 'gold', 'karat' => '24K'],
            ['metal' => 'gold', 'karat' => 'Rawa'],
            ['metal' => 'gold', 'karat' => '22K'],
            ['metal' => 'gold', 'karat' => '21K'],
            ['metal' => 'gold', 'karat' => '18K'],
            ['metal' => 'silver', 'karat' => null],
        ];

        foreach ($margins as $margin) {
            PriceMargin::updateOrCreate(
                ['metal' => $margin['metal'], 'karat' => $margin['karat']],
                [
                    'buy_margin' => 0,
                    'sell_margin' => 0,
                ]
            );
        }
    }
}
