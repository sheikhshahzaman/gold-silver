<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SerialVerificationAttempt extends Model
{
    protected $fillable = [
        'entered_serial', 'inventory_item_id', 'found',
        'status_at_lookup', 'customer_name', 'customer_phone',
        'ip_address', 'user_agent', 'attempted_at',
    ];

    protected $casts = [
        'found' => 'boolean',
        'attempted_at' => 'datetime',
    ];

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }
}
