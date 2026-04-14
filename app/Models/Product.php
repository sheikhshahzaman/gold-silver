<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'weight', 'metal', 'karat',
        'image', 'gallery', 'category', 'category_id', 'price_type',
        'fixed_price', 'discount_type', 'discount_value',
        'discount_starts_at', 'discount_ends_at',
        'price_key', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'fixed_price' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'gallery' => 'array',
        'discount_starts_at' => 'datetime',
        'discount_ends_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function productCategory(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Check if the product has an active discount right now.
     */
    public function hasActiveDiscount(): bool
    {
        if (!$this->discount_type || !$this->discount_value) {
            return false;
        }

        $now = Carbon::now();

        if ($this->discount_starts_at && $now->lt($this->discount_starts_at)) {
            return false;
        }

        if ($this->discount_ends_at && $now->gt($this->discount_ends_at)) {
            return false;
        }

        return true;
    }

    /**
     * Apply discount to a given price and return the discounted price.
     */
    public function applyDiscount(float $price): float
    {
        if (!$this->hasActiveDiscount()) {
            return $price;
        }

        if ($this->discount_type === 'percent') {
            return round($price * (1 - $this->discount_value / 100), 2);
        }

        // flat discount
        return max(0, round($price - $this->discount_value, 2));
    }

    /**
     * Get the discount label for display (e.g. "10% OFF" or "Rs 500 OFF").
     */
    public function getDiscountLabelAttribute(): ?string
    {
        if (!$this->hasActiveDiscount()) {
            return null;
        }

        if ($this->discount_type === 'percent') {
            return number_format($this->discount_value, 0) . '% OFF';
        }

        return 'Rs ' . number_format($this->discount_value) . ' OFF';
    }

    /**
     * Get display category name.
     */
    public function getCategoryNameAttribute(): string
    {
        if ($this->productCategory) {
            return $this->productCategory->name;
        }

        return match ($this->category) {
            'bars' => 'Gold Bars',
            'coins' => 'Gold Coins',
            'silver_bars' => 'Silver Bars',
            'silver_coins' => 'Silver Coins',
            'jewelry' => 'Jewelry',
            default => $this->category ?? 'Other',
        };
    }
}
