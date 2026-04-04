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
        'buy_margin',
        'sell_margin',
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
        ];
    }

    /**
     * Get the user who last updated the margin.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the margin converted for a specific unit.
     * Margins are stored per tola and converted to the requested unit.
     *
     * @return array{buy_margin: float, sell_margin: float}
     */
    public function getMarginForUnit(string $unit): array
    {
        $buyMargin = (float) $this->buy_margin;
        $sellMargin = (float) $this->sell_margin;

        // Per-tola margin: convert to requested unit
        $perGramBuy = $buyMargin / self::TOLA_TO_GRAM;
        $perGramSell = $sellMargin / self::TOLA_TO_GRAM;

        return match ($unit) {
            'tola' => [
                'buy_margin' => round($buyMargin, 2),
                'sell_margin' => round($sellMargin, 2),
            ],
            'gram' => [
                'buy_margin' => round($perGramBuy, 2),
                'sell_margin' => round($perGramSell, 2),
            ],
            '10_gram' => [
                'buy_margin' => round($perGramBuy * 10, 2),
                'sell_margin' => round($perGramSell * 10, 2),
            ],
            'ounce' => [
                'buy_margin' => round($perGramBuy * self::OUNCE_TO_GRAM, 2),
                'sell_margin' => round($perGramSell * self::OUNCE_TO_GRAM, 2),
            ],
            'kg' => [
                'buy_margin' => round($perGramBuy * 1000, 2),
                'sell_margin' => round($perGramSell * 1000, 2),
            ],
            default => [
                'buy_margin' => round($buyMargin, 2),
                'sell_margin' => round($sellMargin, 2),
            ],
        };
    }
}
