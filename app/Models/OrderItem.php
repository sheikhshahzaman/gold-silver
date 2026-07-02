<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_weight',
        'metal',
        'karat',
        'unit',
        'quantity',
        'unit_price',
        'packaging_charge',
        'line_total',
    ];

    protected function casts(): array
    {
        return [
            'quantity'   => 'integer',
            'unit_price' => 'decimal:2',
            'packaging_charge' => 'decimal:2',
            'line_total' => 'decimal:2',
        ];
    }

    /** Total packaging cost for this line (per-unit charge x quantity). */
    public function getPackagingTotalAttribute(): float
    {
        return (float) $this->packaging_charge * $this->quantity;
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
