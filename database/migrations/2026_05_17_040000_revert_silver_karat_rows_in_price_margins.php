<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Reverts the earlier 2026_05_17_032905_add_silver_karat_rows_to_price_margins
 * migration after a change of plan: silver goes back to a single karatless row.
 *
 * Forward-only (no down) because the previous migration file no longer exists
 * in the codebase; there is nothing to roll back to.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Drop the four extra silver karat rows added earlier.
        DB::table('price_margins')
            ->where('metal', 'silver')
            ->whereIn('karat', ['Rawa', '22K', '21K', '18K'])
            ->delete();

        // Revert the remaining silver row's karat back to NULL (keeps its margins).
        DB::table('price_margins')
            ->where('metal', 'silver')
            ->where('karat', '24K')
            ->update(['karat' => null, 'updated_at' => now()]);

        // Also clean up any local-price metal_prices rows the karat fetcher
        // wrote for the now-defunct silver karats; the next prices:fetch will
        // re-populate the single karatless silver rows.
        DB::table('metal_prices')
            ->where('metal', 'silver')
            ->where('type', 'local')
            ->whereIn('karat', ['rawa', '22k', '21k', '18k'])
            ->delete();
    }

    public function down(): void
    {
        // No-op: the original add-silver-karat-rows migration has been removed
        // from the codebase, so there's no consistent state to roll back to.
    }
};
