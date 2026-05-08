<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop pre-existing duplicates so the unique indexes can be added safely.
        DB::statement("
            DELETE FROM metal_prices
            WHERE id NOT IN (
                SELECT max_id FROM (
                    SELECT MAX(id) AS max_id
                    FROM metal_prices
                    GROUP BY metal, COALESCE(karat, ''), unit, type, fetched_at
                ) AS keep
            )
        ");

        DB::statement("
            DELETE FROM currency_rates
            WHERE id NOT IN (
                SELECT max_id FROM (
                    SELECT MAX(id) AS max_id
                    FROM currency_rates
                    GROUP BY currency_pair, type, fetched_at
                ) AS keep
            )
        ");

        Schema::table('metal_prices', function (Blueprint $table) {
            $table->unique(
                ['metal', 'karat', 'unit', 'type', 'fetched_at'],
                'metal_prices_unique_snapshot',
            );
        });

        Schema::table('currency_rates', function (Blueprint $table) {
            $table->unique(
                ['currency_pair', 'type', 'fetched_at'],
                'currency_rates_unique_snapshot',
            );
        });
    }

    public function down(): void
    {
        Schema::table('metal_prices', function (Blueprint $table) {
            $table->dropUnique('metal_prices_unique_snapshot');
        });

        Schema::table('currency_rates', function (Blueprint $table) {
            $table->dropUnique('currency_rates_unique_snapshot');
        });
    }
};
