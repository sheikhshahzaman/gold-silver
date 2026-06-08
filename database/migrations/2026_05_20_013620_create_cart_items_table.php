<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            // One of these is always set. Guests use session_id; signed-in
            // users use user_id (and we copy session items over on login).
            $table->string('session_id', 64)->nullable()->index();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();

            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);

            // Snapshot the unit price at the moment of "add to cart" so the
            // user isn't surprised by a market move between add and checkout.
            // Re-priced on demand in CartService::reprice().
            $table->decimal('locked_unit_price', 12, 2)->nullable();

            $table->timestamps();

            // A given product can only appear once per cart -- adding it
            // again just increments quantity.
            $table->unique(['session_id', 'product_id'], 'cart_items_session_product_unique');
            $table->unique(['user_id', 'product_id'], 'cart_items_user_product_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
