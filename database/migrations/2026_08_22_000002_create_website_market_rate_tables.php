<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('metal_prices')) {
            Schema::create('metal_prices', function (Blueprint $table) {
                $table->id();
                $table->string('metal');
                $table->string('type');
                $table->string('karat')->nullable();
                $table->string('unit');
                $table->decimal('buy_price', 14, 4)->nullable();
                $table->decimal('sell_price', 14, 4)->nullable();
                $table->decimal('high', 14, 4)->nullable();
                $table->decimal('low', 14, 4)->nullable();
                $table->string('currency')->default('PKR');
                $table->string('source');
                $table->timestamp('fetched_at');
                $table->timestamps();

                $table->index(['metal', 'karat', 'fetched_at']);
            });
        }

        if (! Schema::hasTable('currency_rates')) {
            Schema::create('currency_rates', function (Blueprint $table) {
                $table->id();
                $table->string('currency_pair');
                $table->string('type');
                $table->decimal('buy_rate', 10, 4);
                $table->decimal('sell_rate', 10, 4);
                $table->string('source');
                $table->timestamp('fetched_at');
                $table->timestamps();

                $table->index(['currency_pair', 'type', 'fetched_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('currency_rates');
        Schema::dropIfExists('metal_prices');
    }
};
