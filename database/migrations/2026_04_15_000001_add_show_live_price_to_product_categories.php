<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_categories', function (Blueprint $table) {
            $table->boolean('show_live_price')->default(false)->after('icon');
        });

        // Set bullion categories to show live prices
        \App\Models\ProductCategory::whereIn('slug', ['gold-bars', 'gold-coins', 'silver-bars', 'silver-coins'])
            ->update(['show_live_price' => true]);
    }

    public function down(): void
    {
        Schema::table('product_categories', function (Blueprint $table) {
            $table->dropColumn('show_live_price');
        });
    }
};
