<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buy_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('source', 20)->default('app');

            $table->string('metal', 20);              // gold | silver
            $table->string('category', 20);           // bar | rawa

            // Bar: the admin product the customer picked.
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name')->nullable();
            $table->string('product_weight')->nullable();

            // Rawa: a free weight the customer typed.
            $table->decimal('weight_value', 12, 4)->nullable();
            $table->string('weight_unit', 10)->nullable();   // gram | tola

            // Always recalculated server-side; the app never sends a price.
            $table->decimal('unit_price', 14, 2)->default(0);
            $table->decimal('packaging_charge', 14, 2)->default(0);
            $table->decimal('total_amount', 14, 2)->default(0);

            $table->string('customer_name');
            $table->string('customer_phone');

            $table->string('status', 20)->default('new'); // new | contacted | closed
            $table->text('admin_notes')->nullable();

            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['metal', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buy_requests');
    }
};
