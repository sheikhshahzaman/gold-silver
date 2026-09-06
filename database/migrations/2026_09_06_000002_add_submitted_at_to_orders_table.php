<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Null until the customer completes checkout. Cash/COD orders are
            // submitted at the payment step; bank transfers only once the
            // payment screenshot is uploaded. Admin lists submitted orders only.
            $table->timestamp('submitted_at')->nullable()->after('status');
        });

        // Backfill: anything the shop could already act on stays visible.
        // Orders with a payment are definitely complete; orders that at least
        // captured a customer name were real. Empty drafts stay hidden.
        DB::table('orders')
            ->whereNull('submitted_at')
            ->where(function ($query) {
                $query->whereNotNull('customer_name')
                    ->orWhereIn('id', fn ($sub) => $sub->select('order_id')->from('payments'));
            })
            ->update(['submitted_at' => DB::raw('created_at')]);
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('submitted_at');
        });
    }
};
