<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    protected $fillable = [
        'product_id', 'verification_token', 'serial_number',
        'actual_weight', 'purity_tested', 'notes', 'status',
        'sold_at', 'sold_to_name', 'sold_to_phone', 'sold_price',
        'order_id', 'qr_code_path', 'claimed_by_phone',
        'first_scanned_at', 'scan_count',
    ];

    protected $casts = [
        'actual_weight' => 'decimal:4',
        'sold_price' => 'decimal:2',
        'sold_at' => 'datetime',
        'first_scanned_at' => 'datetime',
        'scan_count' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (InventoryItem $item) {
            if (empty($item->verification_token)) {
                $item->verification_token = bin2hex(random_bytes(20)); // 40 hex chars
            }

            if (empty($item->serial_number)) {
                $item->serial_number = static::generateSerialNumber($item);
            }
        });

        static::created(function (InventoryItem $item) {
            if (empty($item->qr_code_path)) {
                $item->generateQrCode();
            }
        });
    }

    public static function generateSerialNumber(InventoryItem $item): string
    {
        $product = $item->product ?? Product::find($item->product_id);

        $metalCode = match (true) {
            $product && $product->metal === 'silver' => 'SLV',
            $product && str_contains(strtolower($product->karat ?? ''), '22') => 'G22K',
            $product && str_contains(strtolower($product->karat ?? ''), '21') => 'G21K',
            $product && str_contains(strtolower($product->karat ?? ''), '18') => 'G18K',
            default => 'G24K',
        };

        $lastId = static::max('id') ?? 0;
        $nextId = $lastId + 1;

        return 'IBE-' . $metalCode . '-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
    }

    public function generateQrCode(): void
    {
        $url = url('/verify/' . $this->verification_token);
        $directory = public_path('qr-codes');

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $filename = 'qr-' . $this->verification_token . '.svg';
        $path = $directory . '/' . $filename;

        $svg = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(400)
            ->errorCorrection('H')
            ->margin(2)
            ->generate($url);

        file_put_contents($path, $svg);

        $this->update(['qr_code_path' => 'qr-codes/' . $filename]);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function scanLogs(): HasMany
    {
        return $this->hasMany(InventoryScanLog::class);
    }

    public function getVerificationUrlAttribute(): string
    {
        return url('/verify/' . $this->verification_token);
    }

    public function getQrCodeUrlAttribute(): ?string
    {
        return $this->qr_code_path ? asset($this->qr_code_path) : null;
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'in_stock' => 'success',
            'reserved' => 'info',
            'sold' => 'warning',
            'returned' => 'danger',
            default => 'gray',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'in_stock' => 'In Stock',
            'reserved' => 'Reserved',
            'sold' => 'Sold',
            'returned' => 'Returned',
            default => ucfirst($this->status),
        };
    }

    public function markAsSold(string $name, string $phone, float $price): void
    {
        $this->update([
            'status' => 'sold',
            'sold_at' => now(),
            'sold_to_name' => $name,
            'sold_to_phone' => $phone,
            'sold_price' => $price,
        ]);
    }

    public function logScan(string $ip, ?string $userAgent): void
    {
        $this->scanLogs()->create([
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'scanned_at' => now(),
        ]);

        $this->increment('scan_count');

        if (!$this->first_scanned_at) {
            $this->update(['first_scanned_at' => now()]);
        }
    }
}
