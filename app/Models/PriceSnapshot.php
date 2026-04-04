<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceSnapshot extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'date',
        'metal',
        'karat',
        'open_price',
        'high_price',
        'low_price',
        'close_price',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'open_price' => 'decimal:2',
            'high_price' => 'decimal:2',
            'low_price' => 'decimal:2',
            'close_price' => 'decimal:2',
        ];
    }
}
