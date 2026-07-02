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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('delivery_method')->default('pickup')->after('total_amount');
            $table->text('delivery_address')->nullable()->after('delivery_method');
            $table->decimal('delivery_charge', 10, 2)->default(0)->after('delivery_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['delivery_method', 'delivery_address', 'delivery_charge']);
        });
    }
};
