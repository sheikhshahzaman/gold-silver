<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('price_margins', function (Blueprint $table) {
            $table->decimal('manual_buy_price', 14, 2)->nullable()->after('sell_margin');
            $table->decimal('manual_sell_price', 14, 2)->nullable()->after('manual_buy_price');
        });

        // Seed from the last computed board prices so rows aren't blank (Rs 0)
        // the moment the admin page switches to direct entry.
        $latest = DB::table('metal_prices')
            ->where('type', 'local')
            ->whereIn('id', function ($q) {
                $q->selectRaw('MAX(id)')
                    ->from('metal_prices')
                    ->where('type', 'local')
                    ->groupBy('metal', 'karat', 'unit');
            })
            ->get();

        foreach ($latest as $row) {
            if ($row->metal === 'gold' && $row->unit === 'tola') {
                DB::table('price_margins')
                    ->where('metal', 'gold')
                    ->whereRaw('UPPER(karat) = ?', [strtoupper($row->karat)])
                    ->update([
                        'manual_buy_price' => $row->buy_price,
                        'manual_sell_price' => $row->sell_price,
                    ]);
            } elseif ($row->metal === 'silver') {
                DB::table('price_margins')
                    ->where('metal', 'silver')
                    ->where('unit', $row->unit)
                    ->update([
                        'manual_buy_price' => $row->buy_price,
                        'manual_sell_price' => $row->sell_price,
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('price_margins', function (Blueprint $table) {
            $table->dropColumn(['manual_buy_price', 'manual_sell_price']);
        });
    }
};
