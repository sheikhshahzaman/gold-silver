<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Order extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'order_number',
        'metal',
        'karat',
        'quantity',
        'unit',
        'type',
        'locked_price',
        'total_amount',
        'delivery_method',
        'delivery_address',
        'delivery_charge',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:4',
            'locked_price' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'delivery_charge' => 'decimal:2',
        ];
    }

    /**
     * Product/metal total plus delivery charge (0 for pickup). This is the
     * actual amount the customer pays and what the Payment row records.
     */
    public function getGrandTotalAttribute(): float
    {
        return (float) $this->total_amount + (float) $this->delivery_charge;
    }

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Order $order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(Str::random(8)) . '-' . time();
            }
        });
    }

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the payment associated with the order.
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Line items for cart-based orders. Single-product orders (Buy/Sell
     * wizards) leave this empty and use the metal/karat/quantity columns
     * on `orders` directly.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /** True when this order was built from a multi-item cart checkout. */
    public function isCartOrder(): bool
    {
        return $this->items()->exists();
    }
}
