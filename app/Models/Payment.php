<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    public const METHOD_BANK_TRANSFER = 'bank_transfer';

    /** Paid in cash at the shop. Pickup only. */
    public const METHOD_CASH = 'cash';

    /** Cash on delivery. Delivery only. */
    public const METHOD_COD = 'cod';

    /** Human labels for every payment method. */
    public static function methodOptions(): array
    {
        return [
            self::METHOD_BANK_TRANSFER => 'Bank Transfer',
            self::METHOD_CASH => 'Cash at Shop',
            self::METHOD_COD => 'Cash on Delivery',
        ];
    }

    /**
     * Methods offered for a delivery choice. Cash is only sensible when the
     * customer collects; COD only when we deliver.
     */
    public static function methodsForDelivery(string $deliveryMethod): array
    {
        return $deliveryMethod === 'delivery'
            ? [self::METHOD_BANK_TRANSFER, self::METHOD_COD]
            : [self::METHOD_BANK_TRANSFER, self::METHOD_CASH];
    }

    /** Only a bank transfer needs the customer to upload a screenshot. */
    public static function requiresProof(?string $method): bool
    {
        return $method === self::METHOD_BANK_TRANSFER;
    }

    public static function isValidForDelivery(?string $method, string $deliveryMethod): bool
    {
        return in_array($method, self::methodsForDelivery($deliveryMethod), true);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'order_id',
        'method',
        'amount',
        'proof_image',
        'reference_number',
        'transaction_id',
        'status',
        'verified_by',
        'verified_at',
        'gateway_response',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
            'gateway_response' => 'array',
            'amount' => 'decimal:2',
        ];
    }

    /**
     * Get the order that owns the payment.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user who verified the payment.
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
