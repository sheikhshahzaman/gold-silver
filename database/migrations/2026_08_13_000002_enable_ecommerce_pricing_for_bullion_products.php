<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('product_categories')
            ->whereIn('slug', ['gold-bars', 'gold-coins', 'silver-bars', 'silver-coins'])
            ->update(['show_live_price' => true, 'is_active' => true]);

        DB::table('products')
            ->orderBy('id')
            ->select(['id', 'name', 'weight', 'metal', 'category'])
            ->get()
            ->each(function ($product): void {
                $priceKey = $this->priceKeyFor($product);

                if (!$priceKey) {
                    return;
                }

                DB::table('products')
                    ->where('id', $product->id)
                    ->update([
                        'price_type' => 'live',
                        'price_key' => $priceKey,
                        'is_active' => true,
                        'updated_at' => now(),
                    ]);
            });
    }

    public function down(): void
    {
        // Intentionally no-op: product pricing may be edited by admins after deploy.
    }

    private function priceKeyFor(object $product): ?string
    {
        $text = Str::of($product->name . ' ' . $product->weight)
            ->lower()
            ->replace(['-', '–', '—'], ' ')
            ->squish()
            ->toString();

        $metal = $product->metal ?: (str_contains($text, 'silver') ? 'silver' : (str_contains($text, 'gold') ? 'gold' : null));
        if (!$metal) {
            return null;
        }

        $unit = $this->unitFromText($text);
        if (!$unit) {
            return null;
        }

        if ($metal === 'silver') {
            if ($unit === '10_tola' && str_contains($text, 'qr')) {
                return 'silver.10_tola_qr';
            }

            return "silver.{$unit}";
        }

        $karat = '24k';
        if (preg_match('/\\b(18|21|22|24)\\s*k\\b/', $text, $match)) {
            $karat = $match[1] . 'k';
        } elseif (str_contains($text, 'rawa')) {
            $karat = 'rawa';
        }

        return "gold.{$karat}.{$unit}";
    }

    private function unitFromText(string $text): ?string
    {
        return match (true) {
            preg_match('/\\b1\\s*kg\\b|\\b1000\\s*g(?:ram)?s?\\b/', $text) === 1 => 'kg',
            preg_match('/\\b100\\s*g(?:ram)?s?\\b/', $text) === 1 => '100_gram',
            preg_match('/\\b50\\s*g(?:ram)?s?\\b/', $text) === 1 => '50_gram',
            preg_match('/\\b10\\s*tola\\b/', $text) === 1 => '10_tola',
            preg_match('/\\b5\\s*tola\\b/', $text) === 1 => '5_tola',
            preg_match('/\\b2\\s*tola\\b/', $text) === 1 => '2_tola',
            preg_match('/\\b1\\s*tola\\b|\\bone\\s*tola\\b/', $text) === 1 => 'tola',
            preg_match('/\\b10\\s*g(?:ram)?s?\\b/', $text) === 1 => '10_gram',
            preg_match('/\\b2\\.5\\s*g(?:ram)?s?\\b/', $text) === 1 => '2.5_gram',
            preg_match('/\\b5\\s*g(?:ram)?s?\\b/', $text) === 1 => '5_gram',
            preg_match('/\\b1\\s*g(?:ram)?s?\\b|\\bone\\s*g(?:ram)?\\b/', $text) === 1 => 'gram',
            str_contains($text, 'ounce') || str_contains($text, 'oz') => 'ounce',
            default => null,
        };
    }
};
