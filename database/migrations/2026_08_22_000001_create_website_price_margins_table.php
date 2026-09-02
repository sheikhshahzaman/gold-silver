<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('price_margins')) {
            Schema::create('price_margins', function (Blueprint $table) {
                $table->id();
                $table->string('metal');
                $table->string('karat')->nullable();
                $table->string('unit')->nullable();
                $table->decimal('buy_margin', 10, 2)->default(0);
                $table->decimal('sell_margin', 10, 2)->default(0);
                $table->decimal('manual_buy_price', 14, 2)->nullable();
                $table->decimal('manual_sell_price', 14, 2)->nullable();
                $table->foreignId('updated_by')->nullable()->constrained('users');
                $table->timestamps();

                $table->unique(['metal', 'karat', 'unit']);
            });

            return;
        }

        Schema::table('price_margins', function (Blueprint $table) {
            if (! Schema::hasColumn('price_margins', 'unit')) {
                $table->string('unit')->nullable()->after('karat');
            }

            if (! Schema::hasColumn('price_margins', 'manual_buy_price')) {
                $table->decimal('manual_buy_price', 14, 2)->nullable()->after('sell_margin');
            }

            if (! Schema::hasColumn('price_margins', 'manual_sell_price')) {
                $table->decimal('manual_sell_price', 14, 2)->nullable()->after('manual_buy_price');
            }
        });
    }

    public function down(): void
    {
        //
    }
};
