<?php

use App\Models\Order;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('orders')
            ->where('status', 'awaiting_verification')
            ->update(['status' => Order::STATUS_PENDING]);

        DB::table('orders')
            ->where('status', 'processing')
            ->update(['status' => Order::STATUS_DISPATCHED]);
    }

    public function down(): void
    {
        DB::table('orders')
            ->where('status', Order::STATUS_DISPATCHED)
            ->update(['status' => 'processing']);
    }
};
