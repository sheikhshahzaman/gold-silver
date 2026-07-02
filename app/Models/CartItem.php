<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = [
        'session_id',
        'user_id',
        'product_id',
        'quantity',
        'locked_unit_price',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'locked_unit_price' => 'decimal:2',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Per-unit packaging charge, read live from the product (not locked —
     * it's frozen onto the OrderItem only once the order is actually placed).
     */
    public function getPackagingChargeAttribute(): float
    {
        return (float) ($this->product?->packaging_charge ?? 0);
    }

    /**
     * Line subtotal: (unit price + packaging charge) × quantity. Falls back
     * to product's fixed_price if the locked price hasn't been captured.
     */
    public function getLineTotalAttribute(): float
    {
        $unit = (float) ($this->locked_unit_price ?? $this->product?->fixed_price ?? 0);
        return ($unit + $this->packaging_charge) * $this->quantity;
    }
}
