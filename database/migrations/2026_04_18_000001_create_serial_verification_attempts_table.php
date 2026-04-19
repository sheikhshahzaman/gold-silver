<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('serial_verification_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('entered_serial', 80);
            $table->foreignId('inventory_item_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('found')->default(false);
            $table->string('status_at_lookup', 20)->nullable();
            $table->string('customer_name', 100)->nullable();
            $table->string('customer_phone', 30)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('attempted_at')->useCurrent();
            $table->timestamps();

            $table->index(['entered_serial', 'attempted_at']);
            $table->index('found');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('serial_verification_attempts');
    }
};
