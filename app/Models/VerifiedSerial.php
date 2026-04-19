<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerifiedSerial extends Model
{
    protected $fillable = [
        'serial_number', 'label', 'product_name', 'metal', 'karat',
        'weight', 'purity', 'notes', 'is_active',
    ];

    protected $casts = [
        'weight' => 'decimal:4',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (VerifiedSerial $serial) {
            $serial->serial_number = strtoupper(trim($serial->serial_number));
        });
    }
}
