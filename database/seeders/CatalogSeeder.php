<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'gold-bars' => [
                'name' => 'Gold Bars',
                'icon' => '🥇',
                'sort_order' => 1,
                'show_live_price' => true,
            ],
            'gold-coins' => [
                'name' => 'Gold Coins',
                'icon' => '🪙',
                'sort_order' => 2,
                'show_live_price' => true,
            ],
            'silver-bars' => [
                'name' => 'Silver Bars',
                'icon' => '🥈',
                'sort_order' => 3,
                'show_live_price' => true,
            ],
            'silver-coins' => [
                'name' => 'Silver Coins',
                'icon' => '🥈',
                'sort_order' => 4,
                'show_live_price' => true,
            ],
            'gold-jewelry' => [
                'name' => 'Gold Jewelry',
                'icon' => '💍',
                'sort_order' => 5,
                'show_live_price' => false,
            ],
        ];

        $catModels = [];
        foreach ($categories as $slug => $attrs) {
            $catModels[$slug] = ProductCategory::updateOrCreate(
                ['slug' => $slug],
                $attrs + ['is_active' => true],
            );
        }

        $products = [
            // ── Gold Bars ──────────────────────────────────────────────
            [
                'name' => '1 Gram 24K Gold Bar',
                'description' => '999.9 purity 24-karat gold bar, sealed with assay certificate.',
                'metal' => 'gold', 'karat' => '24k', 'weight' => '1 gram',
                'category' => 'bars', 'category_id' => $catModels['gold-bars']->id,
                'price_type' => 'live', 'price_key' => 'gold.24k.gram',
                'sort_order' => 1,
            ],
            [
                'name' => '5 Gram 24K Gold Bar',
                'description' => '999.9 purity 24-karat gold bar, sealed with assay certificate.',
                'metal' => 'gold', 'karat' => '24k', 'weight' => '5 gram',
                'category' => 'bars', 'category_id' => $catModels['gold-bars']->id,
                'price_type' => 'live', 'price_key' => 'gold.24k.5_gram',
                'sort_order' => 2,
            ],
            [
                'name' => '10 Gram 24K Gold Bar',
                'description' => '999.9 purity 24-karat gold bar, sealed with assay certificate.',
                'metal' => 'gold', 'karat' => '24k', 'weight' => '10 gram',
                'category' => 'bars', 'category_id' => $catModels['gold-bars']->id,
                'price_type' => 'live', 'price_key' => 'gold.24k.10_gram',
                'sort_order' => 3,
            ],
            [
                'name' => '1 Tola 24K Gold Bar',
                'description' => 'Standard 11.6638g, 999.9 purity, traditional Pakistani tola format.',
                'metal' => 'gold', 'karat' => '24k', 'weight' => '1 tola (11.6638g)',
                'category' => 'bars', 'category_id' => $catModels['gold-bars']->id,
                'price_type' => 'live', 'price_key' => 'gold.24k.tola',
                'sort_order' => 4,
            ],
            [
                'name' => '1 Tola 22K Gold Bar',
                'description' => '916 purity 22-karat gold bar, suitable for jewelry conversion.',
                'metal' => 'gold', 'karat' => '22k', 'weight' => '1 tola (11.6638g)',
                'category' => 'bars', 'category_id' => $catModels['gold-bars']->id,
                'price_type' => 'live', 'price_key' => 'gold.22k.tola',
                'sort_order' => 5,
            ],

            // ── Gold Coins ─────────────────────────────────────────────
            [
                'name' => '1 Gram 24K Gold Coin',
                'description' => 'Minted 24-karat gold coin, 999.9 purity, presentation packaging.',
                'metal' => 'gold', 'karat' => '24k', 'weight' => '1 gram',
                'category' => 'coins', 'category_id' => $catModels['gold-coins']->id,
                'price_type' => 'live', 'price_key' => 'gold.24k.gram',
                'sort_order' => 1,
            ],
            [
                'name' => '5 Gram 24K Gold Coin',
                'description' => 'Minted 24-karat gold coin, 999.9 purity, presentation packaging.',
                'metal' => 'gold', 'karat' => '24k', 'weight' => '5 gram',
                'category' => 'coins', 'category_id' => $catModels['gold-coins']->id,
                'price_type' => 'live', 'price_key' => 'gold.24k.5_gram',
                'sort_order' => 2,
            ],
            [
                'name' => '1 Tola 24K Gold Coin',
                'description' => 'Premium 1-tola minted coin, 999.9 purity, with certificate of authenticity.',
                'metal' => 'gold', 'karat' => '24k', 'weight' => '1 tola (11.6638g)',
                'category' => 'coins', 'category_id' => $catModels['gold-coins']->id,
                'price_type' => 'live', 'price_key' => 'gold.24k.tola',
                'sort_order' => 3,
            ],

            // ── Silver Bars ────────────────────────────────────────────
            [
                'name' => '10 Gram Silver Bar',
                'description' => '999 purity silver bar, hallmarked.',
                'metal' => 'silver', 'karat' => null, 'weight' => '10 gram',
                'category' => 'silver_bars', 'category_id' => $catModels['silver-bars']->id,
                'price_type' => 'live', 'price_key' => 'silver.10_gram',
                'sort_order' => 1,
            ],
            [
                'name' => '1 Tola Silver Bar',
                'description' => '999 purity silver bar in 1-tola weight (11.6638g).',
                'metal' => 'silver', 'karat' => null, 'weight' => '1 tola (11.6638g)',
                'category' => 'silver_bars', 'category_id' => $catModels['silver-bars']->id,
                'price_type' => 'live', 'price_key' => 'silver.tola',
                'sort_order' => 2,
            ],
            [
                'name' => '10 Tola Silver Bar',
                'description' => '999 purity silver bar in 10-tola weight (116.638g).',
                'metal' => 'silver', 'karat' => null, 'weight' => '10 tola (116.638g)',
                'category' => 'silver_bars', 'category_id' => $catModels['silver-bars']->id,
                'price_type' => 'live', 'price_key' => 'silver.10_tola',
                'sort_order' => 3,
            ],
            [
                'name' => '1 KG Silver Bar',
                'description' => 'Investment-grade 1 kilogram silver bar, 999 purity, serialized.',
                'metal' => 'silver', 'karat' => null, 'weight' => '1 kg',
                'category' => 'silver_bars', 'category_id' => $catModels['silver-bars']->id,
                'price_type' => 'live', 'price_key' => 'silver.kg',
                'sort_order' => 4,
            ],

            // ── Silver Coins ───────────────────────────────────────────
            [
                'name' => '10 Gram Silver Coin',
                'description' => '999 purity minted silver coin, presentation packaging.',
                'metal' => 'silver', 'karat' => null, 'weight' => '10 gram',
                'category' => 'silver_coins', 'category_id' => $catModels['silver-coins']->id,
                'price_type' => 'live', 'price_key' => 'silver.10_gram',
                'sort_order' => 1,
            ],
            [
                'name' => '1 Tola Silver Coin',
                'description' => '999 purity minted silver coin, 1-tola weight, certified.',
                'metal' => 'silver', 'karat' => null, 'weight' => '1 tola (11.6638g)',
                'category' => 'silver_coins', 'category_id' => $catModels['silver-coins']->id,
                'price_type' => 'live', 'price_key' => 'silver.tola',
                'sort_order' => 2,
            ],

            // ── Gold Jewelry ───────────────────────────────────────────
            [
                'name' => '22K Gold Bangle',
                'description' => 'Hand-finished 22-karat bangle. Pricing varies by weight and design — request a quote.',
                'metal' => 'gold', 'karat' => '22k', 'weight' => 'varies',
                'category' => 'jewelry', 'category_id' => $catModels['gold-jewelry']->id,
                'price_type' => 'custom_quote', 'price_key' => null,
                'sort_order' => 1,
            ],
            [
                'name' => '22K Gold Ring',
                'description' => '22-karat gold ring. Multiple designs available — request a quote with size and pattern.',
                'metal' => 'gold', 'karat' => '22k', 'weight' => 'varies',
                'category' => 'jewelry', 'category_id' => $catModels['gold-jewelry']->id,
                'price_type' => 'custom_quote', 'price_key' => null,
                'sort_order' => 2,
            ],
            [
                'name' => '18K Gold Pendant',
                'description' => '18-karat gold pendant with optional gemstone setting. Request a quote.',
                'metal' => 'gold', 'karat' => '18k', 'weight' => 'varies',
                'category' => 'jewelry', 'category_id' => $catModels['gold-jewelry']->id,
                'price_type' => 'custom_quote', 'price_key' => null,
                'sort_order' => 3,
            ],
        ];

        foreach ($products as $attrs) {
            $slug = \Illuminate\Support\Str::slug($attrs['name']);
            Product::updateOrCreate(
                ['slug' => $slug],
                $attrs + ['slug' => $slug, 'is_active' => true],
            );
        }
    }
}
