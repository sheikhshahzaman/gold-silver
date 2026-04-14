<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('weight')->nullable();
            $table->string('metal')->default('gold');
            $table->string('karat')->nullable();
            $table->string('image')->nullable();
            $table->string('category')->default('bars');
            $table->string('price_type')->default('live'); // live = use live price, fixed = custom
            $table->decimal('fixed_price', 14, 2)->nullable();
            $table->string('price_key')->nullable(); // e.g. "gold.24k.tola" for live price lookup
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
