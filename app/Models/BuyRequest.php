<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * A "Request to buy gold/silver" lead from the app or website.
 *
 * Deliberately NOT an Order: nothing is reserved, nothing is paid. The shop
 * calls the customer back. Kept in its own table and its own admin section so
 * these never mix with real orders or contact messages.
 */
class BuyRequest extends Model
{
    public const CATEGORY_BAR = 'bar';

    public const CATEGORY_RAWA = 'rawa';

    public const STATUS_NEW = 'new';

    public const STATUS_CONTACTED = 'contacted';

    public const STATUS_CLOSED = 'closed';

    public const UNIT_GRAM = 'gram';

    public const UNIT_TOLA = 'tola';

    protected $fillable = [
        'reference',
        'source',
        'metal',
        'category',
        'product_id',
        'product_name',
        'product_weight',
        'weight_value',
        'weight_unit',
        'unit_price',
        'packaging_charge',
        'total_amount',
        'customer_name',
        'customer_phone',
        'status',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'weight_value' => 'decimal:4',
            'unit_price' => 'decimal:2',
            'packaging_charge' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (BuyRequest $request) {
            if (empty($request->reference)) {
                $request->reference = self::generateReference();
            }
        });
    }

    /** REQ-YYDDD-NNNNNN, same shape as an order number but clearly not one. */
    public static function generateReference(?Carbon $at = null): string
    {
        $at ??= Carbon::now();
        $datePart = $at->format('y').str_pad((string) $at->dayOfYear, 3, '0', STR_PAD_LEFT);

        do {
            $serial = str_pad((string) random_int(0, 999_999), 6, '0', STR_PAD_LEFT);
            $reference = 'REQ-'.$datePart.'-'.$serial;
        } while (self::where('reference', $reference)->exists());

        return $reference;
    }

    public static function categoryOptions(): array
    {
        return [
            self::CATEGORY_BAR => 'Bar',
            self::CATEGORY_RAWA => 'Rawa',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_NEW => 'New',
            self::STATUS_CONTACTED => 'Contacted',
            self::STATUS_CLOSED => 'Closed',
        ];
    }

    public static function sourceOptions(): array
    {
        return [
            Order::SOURCE_WEBSITE => 'Website',
            Order::SOURCE_APP => 'Mobile App',
        ];
    }

    public static function unitOptions(): array
    {
        return [
            self::UNIT_GRAM => 'Gram',
            self::UNIT_TOLA => 'Tola',
        ];
    }

    /** Categories offered for a metal. Rawa is gold only. */
    public static function categoriesForMetal(string $metal): array
    {
        return $metal === 'silver'
            ? [self::CATEGORY_BAR]
            : [self::CATEGORY_BAR, self::CATEGORY_RAWA];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** One line describing what the customer asked for, for admin lists. */
    public function getSelectionLabelAttribute(): string
    {
        if ($this->category === self::CATEGORY_RAWA) {
            $unit = self::unitOptions()[$this->weight_unit] ?? $this->weight_unit;

            return trim(rtrim(rtrim((string) $this->weight_value, '0'), '.')).' '.$unit.' Rawa';
        }

        return trim(($this->product_weight ? $this->product_weight.' - ' : '').$this->product_name);
    }
}
