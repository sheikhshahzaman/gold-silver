<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Multi-item line table.
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();

            // Snapshotted at the time of order so future product edits don't
            // mutate historical orders.
            $table->string('product_name');
            $table->string('product_weight')->nullable();
            $table->string('metal')->nullable();
            $table->string('karat')->nullable();
            $table->string('unit')->nullable();

            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('line_total', 14, 2);

            $table->timestamps();
            $table->index('order_id');
        });

        // 2. Loosen the per-item columns on orders so cart-based orders --
        //    which span multiple line items -- can leave them NULL. Legacy
        //    single-product orders (from the Buy/Sell wizards) still work
        //    because they continue to set these fields.
        Schema::table('orders', function (Blueprint $table) {
            $table->string('metal')->nullable()->change();
            $table->string('karat')->nullable()->change();
            $table->unsignedInteger('quantity')->nullable()->change();
            $table->string('unit')->nullable()->change();
            $table->decimal('locked_price', 12, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');

        // No safe rollback for the column changes -- legacy rows with NULLs
        // would block them. Leave nullable; if needed, restore manually.
    }
};
