<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add a nullable `unit` column to price_margins and split silver into one
     * row per weight category (tola, 10_tola, 10_gram, 5_gram, gram, kg). Gold
     * keeps a single row per karat with unit = NULL -- gold margins still apply
     * per-tola and are converted across units by the price engine.
     */
    public function up(): void
    {
        Schema::table('price_margins', function (Blueprint $table) {
            if (!Schema::hasColumn('price_margins', 'unit')) {
                $table->string('unit', 20)->nullable()->after('karat')->index();
            }
        });

        // Capture the existing single silver margin (so the admin's saved values
        // carry over as the per-tola starting point for the new rows).
        $existing = DB::table('price_margins')
            ->where('metal', 'silver')
            ->whereNull('unit')
            ->first();

        $buyPerTola  = $existing ? (float) $existing->buy_margin  : 0.0;
        $sellPerTola = $existing ? (float) $existing->sell_margin : 0.0;

        // 1 tola = 11.6638 g. Convert the per-tola margin into a per-unit
        // equivalent so the new rows produce the same price as before until
        // the admin tweaks them.
        $perGramBuy  = $buyPerTola  / 11.6638;
        $perGramSell = $sellPerTola / 11.6638;

        $rows = [
            'tola'    => [round($buyPerTola, 2),         round($sellPerTola, 2)],
            '10_tola' => [round($buyPerTola * 10, 2),    round($sellPerTola * 10, 2)],
            'kg'      => [round($perGramBuy * 1000, 2),  round($perGramSell * 1000, 2)],
            '10_gram' => [round($perGramBuy * 10, 2),    round($perGramSell * 10, 2)],
            '5_gram'  => [round($perGramBuy * 5, 2),     round($perGramSell * 5, 2)],
            'gram'    => [round($perGramBuy, 2),         round($perGramSell, 2)],
        ];

        foreach ($rows as $unit => [$buy, $sell]) {
            DB::table('price_margins')->updateOrInsert(
                ['metal' => 'silver', 'unit' => $unit],
                [
                    'karat' => null,
                    'buy_margin' => $buy,
                    'sell_margin' => $sell,
                    'updated_by' => $existing->updated_by ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
        }

        // Remove the legacy single karatless+unitless silver row.
        DB::table('price_margins')
            ->where('metal', 'silver')
            ->whereNull('karat')
            ->whereNull('unit')
            ->delete();
    }

    public function down(): void
    {
        // Restore the single karatless silver row using the tola margin from
        // the per-unit row so the values round-trip cleanly.
        $tolaRow = DB::table('price_margins')
            ->where('metal', 'silver')
            ->where('unit', 'tola')
            ->first();

        DB::table('price_margins')
            ->where('metal', 'silver')
            ->delete();

        DB::table('price_margins')->insert([
            'metal' => 'silver',
            'karat' => null,
            'unit' => null,
            'buy_margin' => $tolaRow->buy_margin ?? 0,
            'sell_margin' => $tolaRow->sell_margin ?? 0,
            'updated_by' => $tolaRow->updated_by ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Schema::table('price_margins', function (Blueprint $table) {
            if (Schema::hasColumn('price_margins', 'unit')) {
                $table->dropColumn('unit');
            }
        });
    }
};
