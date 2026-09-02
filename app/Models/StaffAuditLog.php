<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffAuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'staff_name',
        'staff_email',
        'feature',
        'action',
        'auditable_type',
        'auditable_id',
        'auditable_label',
        'ip_address',
        'user_agent',
        'changes',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'changes' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
