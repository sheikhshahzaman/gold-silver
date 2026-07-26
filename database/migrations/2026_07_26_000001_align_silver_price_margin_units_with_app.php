<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $tola = DB::table('price_margins')
            ->where('metal', 'silver')
            ->where('unit', 'tola')
            ->first();

        $tenTola = DB::table('price_margins')
            ->where('metal', 'silver')
            ->where('unit', '10_tola')
            ->first();

        $tolaBuy = (float) ($tola->manual_buy_price ?? $tola->buy_margin ?? 0);
        $tolaSell = (float) ($tola->manual_sell_price ?? $tola->sell_margin ?? 0);
        $tenTolaBuy = (float) ($tenTola->manual_buy_price ?? $tenTola->buy_margin ?? ($tolaBuy * 10));
        $tenTolaSell = (float) ($tenTola->manual_sell_price ?? $tenTola->sell_margin ?? ($tolaSell * 10));

        $rows = [
            '10_tola_qr' => [round($tenTolaBuy, 2), round($tenTolaSell, 2)],
            '10_tola' => [round($tenTolaBuy, 2), round($tenTolaSell, 2)],
            'kg' => [
                (float) (DB::table('price_margins')->where('metal', 'silver')->where('unit', 'kg')->value('manual_buy_price') ?? 0) ?: round($tolaBuy / 11.6638 * 1000, 2),
                (float) (DB::table('price_margins')->where('metal', 'silver')->where('unit', 'kg')->value('manual_sell_price') ?? 0) ?: round($tolaSell / 11.6638 * 1000, 2),
            ],
            '5_tola' => [round($tolaBuy * 5, 2), round($tolaSell * 5, 2)],
            'tola' => [round($tolaBuy, 2), round($tolaSell, 2)],
        ];

        foreach ($rows as $unit => [$buy, $sell]) {
            DB::table('price_margins')->updateOrInsert(
                ['metal' => 'silver', 'unit' => $unit],
                [
                    'karat' => null,
                    'buy_margin' => 0,
                    'sell_margin' => 0,
                    'manual_buy_price' => $buy,
                    'manual_sell_price' => $sell,
                    'updated_by' => $tola->updated_by ?? $tenTola->updated_by ?? null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            );
        }

        DB::table('price_margins')
            ->where('metal', 'silver')
            ->whereIn('unit', ['10_gram', '5_gram', 'gram'])
            ->delete();
    }

    public function down(): void
    {
        DB::table('price_margins')
            ->where('metal', 'silver')
            ->whereIn('unit', ['10_tola_qr', '5_tola'])
            ->delete();
    }
};
