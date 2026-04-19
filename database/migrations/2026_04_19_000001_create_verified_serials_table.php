<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verified_serials', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number', 80)->unique();
            $table->string('label', 150)->nullable();
            $table->string('product_name', 150)->nullable();
            $table->string('metal', 20)->nullable();
            $table->string('karat', 20)->nullable();
            $table->decimal('weight', 10, 4)->nullable();
            $table->string('purity', 20)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verified_serials');
    }
};
