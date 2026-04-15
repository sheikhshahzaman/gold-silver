<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('verification_token', 64)->unique();
            $table->string('serial_number', 50)->unique();
            $table->decimal('actual_weight', 10, 4)->nullable();
            $table->string('purity_tested', 20)->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('in_stock');
            $table->timestamp('sold_at')->nullable();
            $table->string('sold_to_name')->nullable();
            $table->string('sold_to_phone', 30)->nullable();
            $table->decimal('sold_price', 14, 2)->nullable();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('qr_code_path')->nullable();
            $table->string('claimed_by_phone', 30)->nullable();
            $table->timestamp('first_scanned_at')->nullable();
            $table->unsignedInteger('scan_count')->default(0);
            $table->timestamps();

            $table->index('status');
            $table->index('sold_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
