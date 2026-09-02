<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('staff_name')->nullable();
            $table->string('staff_email')->nullable();
            $table->string('feature')->index();
            $table->string('action')->index();
            $table->string('auditable_type')->nullable();
            $table->string('auditable_id')->nullable();
            $table->string('auditable_label')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('changes')->nullable();
            $table->timestamp('created_at')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_audit_logs');
    }
};
