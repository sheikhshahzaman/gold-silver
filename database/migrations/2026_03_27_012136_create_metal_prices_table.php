<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('metal_prices', function (Blueprint $table) {
            $table->id();
            $table->string('metal');
            $table->string('type');
            $table->string('karat')->nullable();
            $table->string('unit');
            $table->decimal('buy_price', 14, 2)->nullable();
            $table->decimal('sell_price', 14, 2)->nullable();
            $table->decimal('high', 14, 2)->nullable();
            $table->decimal('low', 14, 2)->nullable();
            $table->string('currency')->default('PKR');
            $table->string('source');
            $table->timestamp('fetched_at');
            $table->timestamps();

            $table->index(['metal', 'karat', 'fetched_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metal_prices');
    }
};
