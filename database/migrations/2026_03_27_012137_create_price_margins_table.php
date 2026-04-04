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
        Schema::create('price_margins', function (Blueprint $table) {
            $table->id();
            $table->string('metal');
            $table->string('karat')->nullable();
            $table->decimal('buy_margin', 10, 2)->default(0);
            $table->decimal('sell_margin', 10, 2)->default(0);
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->unique(['metal', 'karat']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_margins');
    }
};
