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
            // Where the order was placed: 'website' or 'app'.
            $table->string('source', 20)->default('website')->after('type');
        });

        // Existing rows predate the mobile app checkout, so leave them as the
        // default 'website' rather than guessing.
        DB::table('orders')->whereNull('source')->update(['source' => 'website']);
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('source');
        });
    }
};
