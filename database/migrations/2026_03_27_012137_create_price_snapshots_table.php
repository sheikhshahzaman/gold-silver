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
        Schema::create('price_snapshots', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('metal');
            $table->string('karat')->nullable();
            $table->decimal('open_price', 14, 2);
            $table->decimal('high_price', 14, 2);
            $table->decimal('low_price', 14, 2);
            $table->decimal('close_price', 14, 2);
            $table->timestamps();

            $table->unique(['date', 'metal', 'karat']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_snapshots');
    }
};
