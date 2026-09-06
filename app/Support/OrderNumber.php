<?php

namespace App\Support;

use App\Models\Order;
use Illuminate\Support\Carbon;

/**
 * Order number format: IBE-YYDDD-NNNNNNNN
 *
 *   IBE       fixed brand prefix, always added automatically
 *   YYDDD     2-digit year + day of year (001-366), so the number carries its
 *             own date and sorts chronologically
 *   NNNNNNNN  8 random digits, unique within that day
 *
 * Example: IBE-26249-40571836
 *
 * Legacy numbers (ORD-XXXXXXXX-0000000000) are still recognised so orders
 * placed before this change remain trackable.
 */
class OrderNumber
{
    public const PREFIX = 'IBE';

    public const LEGACY_PREFIX = 'ORD';

    /** Generates the next unique order number. */
    public static function generate(?Carbon $at = null): string
    {
        $at ??= Carbon::now();

        $datePart = $at->format('y').str_pad((string) $at->dayOfYear, 3, '0', STR_PAD_LEFT);

        do {
            $serial = str_pad((string) random_int(0, 99_999_999), 8, '0', STR_PAD_LEFT);
            $number = self::PREFIX.'-'.$datePart.'-'.$serial;
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }

    /**
     * Turns anything the customer types into the canonical stored form.
     *
     * Accepts lower case, missing hyphens, and a missing IBE prefix, so
     * "ibe2624940571836", "26249 40571836" and "IBE-26249-40571836" all
     * resolve to the same number.
     */
    public static function normalize(string $value): string
    {
        $cleaned = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $value) ?? '');

        if ($cleaned === '') {
            return '';
        }

        if (str_starts_with($cleaned, self::LEGACY_PREFIX)) {
            return self::formatLegacy(substr($cleaned, 3));
        }

        if (str_starts_with($cleaned, self::PREFIX)) {
            $cleaned = substr($cleaned, 3);
        }

        $digits = substr(preg_replace('/\D/', '', $cleaned) ?? '', 0, 13);

        if ($digits === '') {
            return '';
        }

        $date = substr($digits, 0, 5);
        $serial = substr($digits, 5, 8);

        return self::PREFIX.'-'.$date.($serial !== '' ? '-'.$serial : '');
    }

    private static function formatLegacy(string $body): string
    {
        $first = substr($body, 0, 8);
        $second = substr($body, 8, 10);

        return self::LEGACY_PREFIX.'-'.$first.($second !== '' ? '-'.$second : '');
    }
}
