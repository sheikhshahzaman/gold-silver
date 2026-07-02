<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Services\Cart;
use App\Services\PriceEngine\PriceCacheManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class LivePriceKeyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cart::forgetPriceMatrixMemo();
    }

    /** Board: gold 24k tola 445600, gram row present; silver tola 7960, kg row present. */
    private function seedPriceCache(): void
    {
        Cache::put(PriceCacheManager::KEY_ALL, [
            'gold' => [
                '24k' => [
                    'tola' => ['buy' => 445600, 'sell' => 446500, 'base' => 445600],
                    'gram' => ['buy' => 38203.67, 'sell' => 38280.83, 'base' => 38203.67],
                ],
            ],
            'silver' => [
                'tola' => ['buy' => 7960, 'sell' => 8860, 'base' => 7960],
                'kg' => ['buy' => 582441.61, 'sell' => 645941.61, 'base' => 582441.61],
            ],
        ], 300);
    }

    private function makeLiveProduct(string $key): Product
    {
        $category = ProductCategory::firstOrCreate(
            ['slug' => 'gold-bars'],
            ['name' => 'Gold Bars'],
        );

        return Product::create([
            'name' => 'Test ' . $key,
            'slug' => 'test-' . md5($key),
            'weight' => 'test',
            'metal' => str_starts_with($key, 'silver') ? 'silver' : 'gold',
            'karat' => '24K',
            'category_id' => $category->id,
            'price_type' => 'live',
            'price_key' => $key,
            'is_active' => true,
        ]);
    }

    public function test_exact_board_rows_still_resolve_exactly(): void
    {
        $this->seedPriceCache();
        $cart = app(Cart::class);

        $this->assertEquals(445600.0, $cart->unitPriceFor($this->makeLiveProduct('gold.24k.tola')));
        $this->assertEquals(38203.67, $cart->unitPriceFor($this->makeLiveProduct('gold.24k.gram')));
        // Silver kg has its own independent board row — must NOT be derived.
        $this->assertEquals(582441.61, $cart->unitPriceFor($this->makeLiveProduct('silver.kg')));
    }

    public function test_missing_units_derive_from_tola_by_weight(): void
    {
        $this->seedPriceCache();
        $cart = app(Cart::class);
        $perGram = 445600 / Cart::GRAMS_PER_TOLA;

        $this->assertEquals(round($perGram * 2.5, 2), $cart->unitPriceFor($this->makeLiveProduct('gold.24k.2.5_gram')));
        $this->assertEquals(round($perGram * 50, 2), $cart->unitPriceFor($this->makeLiveProduct('gold.24k.50_gram')));
        $this->assertEquals(round($perGram * 31.1034768, 2), $cart->unitPriceFor($this->makeLiveProduct('gold.24k.ounce')));
        $this->assertEquals(round(445600 / 2, 2), $cart->unitPriceFor($this->makeLiveProduct('gold.24k.half_tola')));
        $this->assertEquals(round(445600 * 2, 2), $cart->unitPriceFor($this->makeLiveProduct('gold.24k.2_tola')));

        $silverPerGram = 7960 / Cart::GRAMS_PER_TOLA;
        $this->assertEquals(round($silverPerGram * 58.319019, 2), $cart->unitPriceFor($this->makeLiveProduct('silver.5_tola')));
        $this->assertEquals(round(7960 * 10, 2), $cart->unitPriceFor($this->makeLiveProduct('silver.10_tola')));
    }

    public function test_quote_returns_buy_and_sell_for_derived_units(): void
    {
        $this->seedPriceCache();
        $cart = app(Cart::class);

        $quote = $cart->unitQuoteFor($this->makeLiveProduct('gold.24k.ounce'));
        $this->assertEquals(round(445600 / Cart::GRAMS_PER_TOLA * 31.1034768, 2), $quote['buy']);
        $this->assertEquals(round(446500 / Cart::GRAMS_PER_TOLA * 31.1034768, 2), $quote['sell']);

        $exact = $cart->unitQuoteFor($this->makeLiveProduct('silver.tola'));
        $this->assertEquals(7960.0, $exact['buy']);
        $this->assertEquals(8860.0, $exact['sell']);
    }

    public function test_unknown_or_slug_keys_return_null(): void
    {
        $this->seedPriceCache();
        $cart = app(Cart::class);

        $this->assertNull($cart->unitPriceFor($this->makeLiveProduct('1-gram-gold-bar-ary-verified')));
        $this->assertNull($cart->unitPriceFor($this->makeLiveProduct('gold.24k.bogus_unit')));
    }
}
