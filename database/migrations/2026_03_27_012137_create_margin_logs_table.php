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
        Schema::create('margin_logs', function (Blueprint $table) {
            $table->id();
            $table->string('metal');
            $table->string('karat')->nullable();
            $table->decimal('old_buy_margin', 10, 2);
            $table->decimal('new_buy_margin', 10, 2);
            $table->decimal('old_sell_margin', 10, 2);
            $table->decimal('new_sell_margin', 10, 2);
            $table->foreignId('changed_by')->constrained('users');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('margin_logs');
    }
};
