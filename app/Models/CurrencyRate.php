<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CurrencyRate extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'currency_pair',
        'type',
        'buy_rate',
        'sell_rate',
        'source',
        'fetched_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fetched_at' => 'datetime',
            'buy_rate' => 'decimal:4',
            'sell_rate' => 'decimal:4',
        ];
    }

    /**
     * Scope to get the latest rates.
     */
    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderByDesc('fetched_at');
    }

    /**
     * Scope to filter by currency pair.
     */
    public function scopeForPair(Builder $query, string $pair): Builder
    {
        return $query->where('currency_pair', $pair);
    }

    /**
     * Scope to filter interbank rates.
     */
    public function scopeInterbank(Builder $query): Builder
    {
        return $query->where('type', 'interbank');
    }

    /**
     * Scope to filter open market rates.
     */
    public function scopeOpenMarket(Builder $query): Builder
    {
        return $query->where('type', 'open_market');
    }
}
