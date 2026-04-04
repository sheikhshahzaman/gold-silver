<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarginLog extends Model
{
    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'metal',
        'karat',
        'old_buy_margin',
        'new_buy_margin',
        'old_sell_margin',
        'new_sell_margin',
        'changed_by',
        'created_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'old_buy_margin' => 'decimal:2',
            'new_buy_margin' => 'decimal:2',
            'old_sell_margin' => 'decimal:2',
            'new_sell_margin' => 'decimal:2',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Get the user who changed the margin.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
