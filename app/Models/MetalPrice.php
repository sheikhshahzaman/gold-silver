<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MetalPrice extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'metal',
        'type',
        'karat',
        'unit',
        'buy_price',
        'sell_price',
        'high',
        'low',
        'currency',
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
            'buy_price' => 'decimal:2',
            'sell_price' => 'decimal:2',
            'high' => 'decimal:2',
            'low' => 'decimal:2',
        ];
    }

    /**
     * Scope to get the latest prices.
     */
    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderByDesc('fetched_at');
    }

    /**
     * Scope to filter by metal type.
     */
    public function scopeForMetal(Builder $query, string $metal): Builder
    {
        return $query->where('metal', $metal);
    }

    /**
     * Scope to filter by karat.
     */
    public function scopeForKarat(Builder $query, string $karat): Builder
    {
        return $query->where('karat', $karat);
    }

    /**
     * Scope to filter gold prices.
     */
    public function scopeGold(Builder $query): Builder
    {
        return $query->where('metal', 'gold');
    }

    /**
     * Scope to filter silver prices.
     */
    public function scopeSilver(Builder $query): Builder
    {
        return $query->where('metal', 'silver');
    }
}
