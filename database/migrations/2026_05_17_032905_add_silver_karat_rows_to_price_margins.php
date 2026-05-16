<?php

use App\Models\PriceMargin;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Convert silver from a single karatless row into the same 5-karat structure
     * gold uses (24K, Rawa, 22K, 21K, 18K). The old null-karat row becomes the
     * 24K entry (its margin values are preserved) and four new rows are added
     * with margin 0/0.
     */
    public function up(): void
    {
        // Promote the existing null-karat silver row to 24K (keeps existing margins).
        DB::table('price_margins')
            ->where('metal', 'silver')
            ->whereNull('karat')
            ->update(['karat' => '24K', 'updated_at' => now()]);

        // Add the four other karats with zero margins (idempotent: only insert
        // those that don't already exist, so re-running the migration is safe).
        foreach (['Rawa', '22K', '21K', '18K'] as $karat) {
            DB::table('price_margins')->updateOrInsert(
                ['metal' => 'silver', 'karat' => $karat],
                [
                    'buy_margin' => 0,
                    'sell_margin' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
        }
    }

    public function down(): void
    {
        // Drop the four extra karats and revert 24K back to NULL.
        DB::table('price_margins')
            ->where('metal', 'silver')
            ->whereIn('karat', ['Rawa', '22K', '21K', '18K'])
            ->delete();

        DB::table('price_margins')
            ->where('metal', 'silver')
            ->where('karat', '24K')
            ->update(['karat' => null, 'updated_at' => now()]);
    }
};
