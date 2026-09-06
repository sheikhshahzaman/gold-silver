<?php

namespace App\Models;

use App\Support\OrderNumber;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    public const SOURCE_WEBSITE = 'website';

    public const SOURCE_APP = 'app';

    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_DISPATCHED = 'dispatched';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCELLED = 'cancelled';

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
        'source',
        'submitted_at',
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
            'submitted_at' => 'datetime',
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

    /** Only orders the customer actually completed. Drafts stay hidden. */
    public function scopeSubmitted($query)
    {
        return $query->whereNotNull('submitted_at');
    }

    public function isSubmitted(): bool
    {
        return $this->submitted_at !== null;
    }

    /** Marks checkout complete. Idempotent, so a double tap cannot re-stamp it. */
    public function markSubmitted(): static
    {
        if (! $this->isSubmitted()) {
            $this->forceFill(['submitted_at' => now()])->save();
        }

        return $this;
    }

    /** Human labels for where an order was placed. */
    public static function sourceOptions(): array
    {
        return [
            self::SOURCE_WEBSITE => 'Website',
            self::SOURCE_APP => 'Mobile App',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'Order pending',
            self::STATUS_CONFIRMED => 'Order confirmed',
            self::STATUS_DISPATCHED => 'Order dispatched',
            self::STATUS_DELIVERED => 'Order delivered',
            self::STATUS_CANCELLED => 'Order cancelled',
        ];
    }

    public static function normalizeStatus(?string $status): string
    {
        return match ($status) {
            'awaiting_verification' => self::STATUS_PENDING,
            'processing' => self::STATUS_DISPATCHED,
            self::STATUS_CONFIRMED,
            self::STATUS_DISPATCHED,
            self::STATUS_DELIVERED,
            self::STATUS_CANCELLED => $status,
            default => self::STATUS_PENDING,
        };
    }

    public function getDisplayStatusAttribute(): string
    {
        return self::statusOptions()[self::normalizeStatus($this->status)] ?? 'Order pending';
    }

    public function getStatusColorAttribute(): string
    {
        return match (self::normalizeStatus($this->status)) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_CONFIRMED => 'success',
            self::STATUS_DISPATCHED => 'info',
            self::STATUS_DELIVERED => 'success',
            self::STATUS_CANCELLED => 'danger',
            default => 'gray',
        };
    }

    public function getItemsSummaryAttribute(): string
    {
        if ($this->items->isNotEmpty()) {
            return $this->items
                ->map(function (OrderItem $item): string {
                    $quantity = number_format((float) $item->quantity, 0);
                    $weight = $item->product_weight ? " ({$item->product_weight})" : '';

                    return "{$quantity} x {$item->product_name}{$weight} - Rs "
                        . number_format((float) $item->line_total, 0);
                })
                ->implode("\n");
        }

        if ($this->metal) {
            $metal = ucfirst($this->metal);
            $karat = $this->karat ? ' ' . strtoupper($this->karat) : '';
            $unit = $this->unit ? str_replace('_', ' ', $this->unit) : '';
            $quantity = number_format((float) $this->quantity, 4);

            return trim("{$quantity} {$unit} {$metal}{$karat}") . ' - Rs '
                . number_format((float) $this->total_amount, 0);
        }

        return '-';
    }

    public function getDeliverySummaryAttribute(): string
    {
        $method = $this->delivery_method === 'delivery' ? 'Delivery' : 'Pickup from shop';
        $charge = (float) $this->delivery_charge > 0
            ? ' (Rs ' . number_format((float) $this->delivery_charge, 0) . ')'
            : '';
        $address = $this->delivery_address ? "\nAddress: {$this->delivery_address}" : '';

        return $method . $charge . $address;
    }

    public function trackingSteps(): array
    {
        $steps = [
            self::STATUS_PENDING,
            self::STATUS_CONFIRMED,
            self::STATUS_DISPATCHED,
            self::STATUS_DELIVERED,
        ];
        $current = self::normalizeStatus($this->status);
        $currentIndex = array_search($current, $steps, true);

        return array_map(function (string $status, int $index) use ($current, $currentIndex): array {
            $state = 'upcoming';

            if ($current === self::STATUS_CANCELLED) {
                $state = 'cancelled';
            } elseif ($currentIndex !== false) {
                if ($index < $currentIndex) {
                    $state = 'complete';
                } elseif ($index === $currentIndex) {
                    $state = 'current';
                }
            }

            return [
                'key' => $status,
                'label' => self::statusOptions()[$status],
                'state' => $state,
            ];
        }, $steps, array_keys($steps));
    }

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Order $order) {
            if (empty($order->order_number)) {
                $order->order_number = OrderNumber::generate();
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
