<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('services')
            ->where('title', 'Buy Gold & Silver')
            ->update([
                'title' => 'Product Catalog',
                'description' => 'Browse certified gold and silver products with live ecommerce pricing.',
                'updated_at' => now(),
            ]);

        DB::table('services')
            ->where('title', 'Sell Your Gold')
            ->update([
                'title' => 'Transparent Pricing',
                'description' => 'See current gold and silver rates with clear pricing and no hidden charges.',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('services')
            ->where('title', 'Product Catalog')
            ->update([
                'title' => 'Buy Gold & Silver',
                'description' => 'Purchase 24K, 22K, 21K, and 18K gold bars, coins, and silver in Tola, grams, and ounce units.',
                'updated_at' => now(),
            ]);

        DB::table('services')
            ->where('title', 'Transparent Pricing')
            ->update([
                'title' => 'Sell Your Gold',
                'description' => 'Get the best rates when selling your gold and silver. Transparent pricing with no hidden charges.',
                'updated_at' => now(),
            ]);
    }
};
