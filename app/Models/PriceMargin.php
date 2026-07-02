<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceMargin extends Model
{
    /**
     * Conversion constants.
     * 1 tola = 11.6638 grams
     */
    public const TOLA_TO_GRAM = 11.6638;
    public const OUNCE_TO_GRAM = 31.1035;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'metal',
        'karat',
        'unit',
        'buy_margin',
        'sell_margin',
        'manual_buy_price',
        'manual_sell_price',
        'updated_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'buy_margin' => 'decimal:2',
            'sell_margin' => 'decimal:2',
            'manual_buy_price' => 'decimal:2',
            'manual_sell_price' => 'decimal:2',
        ];
    }

    /**
     * Get the user who last updated this row.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
