<?php

namespace Tests\Feature;

use App\Models\BuyRequest;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Services\BuyRequests\BuyRequestPricing;
use App\Services\Rates\RatesProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuyRequestTest extends TestCase
{
    use RefreshDatabase;

    /** Rawa board rows, mirroring what /api/prices returns. */
    private function fakeRates(?array $rawa = null): void
    {
        $rawa ??= [
            'tola' => ['buy' => 454321, 'sell' => 451000, 'base' => 454321],
            'gram' => ['buy' => 38951.37, 'sell' => 38666.64, 'base' => 38951.37],
        ];

        $this->app->bind(RatesProvider::class, fn () => new class($rawa) implements RatesProvider {
            public function __construct(private array $rawa) {}

            public function getAllPrices(): array
            {
                return ['gold' => ['rawa' => $this->rawa]];
            }
        });
    }

    public function test_rawa_is_priced_per_gram_from_the_admin_board(): void
    {
        $this->fakeRates();

        $priced = app(BuyRequestPricing::class)->priceRawa(10, BuyRequest::UNIT_GRAM);

        $this->assertSame(38951.37, $priced['unit_price']);
        $this->assertSame(389513.70, $priced['total_amount']);
    }

    public function test_rawa_is_priced_per_tola_from_the_admin_board(): void
    {
        $this->fakeRates();

        $priced = app(BuyRequestPricing::class)->priceRawa(2, BuyRequest::UNIT_TOLA);

        $this->assertSame(454321.0, $priced['unit_price']);
        $this->assertSame(908642.0, $priced['total_amount']);
    }

    public function test_a_fractional_rawa_weight_is_priced_correctly(): void
    {
        $this->fakeRates();

        $priced = app(BuyRequestPricing::class)->priceRawa(2.5, BuyRequest::UNIT_GRAM);

        $this->assertSame(97378.43, $priced['total_amount']);
    }

    public function test_a_missing_gram_row_falls_back_to_the_tola_rate(): void
    {
        $this->fakeRates(['tola' => ['buy' => 454321]]);

        $priced = app(BuyRequestPricing::class)->priceRawa(1, BuyRequest::UNIT_GRAM);

        // 454321 / 11.6638038 grams per tola = 38951.36
        $this->assertEqualsWithDelta(38951.36, $priced['unit_price'], 0.01);
    }

    public function test_unavailable_rawa_rates_are_reported_not_guessed(): void
    {
        $this->fakeRates([]);

        $this->expectExceptionMessage('Rawa rates are currently unavailable');
        app(BuyRequestPricing::class)->priceRawa(1, BuyRequest::UNIT_GRAM);
    }

    public function test_rawa_is_rejected_for_silver(): void
    {
        $this->fakeRates();

        $this->postJson('/api/buy-requests', [
            'metal' => 'silver',
            'category' => BuyRequest::CATEGORY_RAWA,
            'weight_value' => 5,
            'weight_unit' => BuyRequest::UNIT_GRAM,
            'customer_name' => 'Test',
            'customer_phone' => '03001234567',
        ])->assertStatus(422)->assertJson(['message' => 'Rawa is only available for gold.']);
    }

    public function test_a_bar_request_is_stored_with_a_server_side_price(): void
    {
        $this->fakeRates();

        $category = ProductCategory::create([
            'name' => 'Gold Bars', 'slug' => 'gold-bars', 'is_active' => true,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => '1-Tola Gold Bar',
            'slug' => '1-tola-gold-bar',
            'weight' => '1-Tola',
            'metal' => 'gold',
            'price_type' => 'fixed',
            'fixed_price' => 458523,
            'packaging_charge' => 3000,
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/buy-requests', [
            'metal' => 'gold',
            'category' => BuyRequest::CATEGORY_BAR,
            'product_id' => $product->id,
            // A price sent by the client must be ignored entirely.
            'total_amount' => 1,
            'customer_name' => 'Shahzaman',
            'customer_phone' => '03215505100',
        ])->assertStatus(201);

        $response->assertJsonPath('request.unit_price', 458523);
        $response->assertJsonPath('request.packaging_charge', 3000);
        $response->assertJsonPath('request.total_amount', 461523);

        $saved = BuyRequest::first();
        $this->assertSame('gold', $saved->metal);
        $this->assertSame(BuyRequest::CATEGORY_BAR, $saved->category);
        $this->assertSame('1-Tola Gold Bar', $saved->product_name);
        $this->assertSame(BuyRequest::STATUS_NEW, $saved->status);
        $this->assertSame('app', $saved->source);
        $this->assertMatchesRegularExpression('/^REQ-\d{5}-\d{6}$/', $saved->reference);
    }

    public function test_a_bar_request_needs_a_size(): void
    {
        $this->fakeRates();

        $this->postJson('/api/buy-requests', [
            'metal' => 'gold',
            'category' => BuyRequest::CATEGORY_BAR,
            'customer_name' => 'Test',
            'customer_phone' => '03001234567',
        ])->assertStatus(422)->assertJson(['message' => 'Please choose a size.']);
    }

    public function test_a_rawa_request_needs_a_weight_and_unit(): void
    {
        $this->fakeRates();

        $this->postJson('/api/buy-requests', [
            'metal' => 'gold',
            'category' => BuyRequest::CATEGORY_RAWA,
            'customer_name' => 'Test',
            'customer_phone' => '03001234567',
        ])->assertStatus(422);
    }

    public function test_options_lists_categories_per_metal(): void
    {
        $this->getJson('/api/buy-requests/options')
            ->assertOk()
            ->assertJsonPath('categories.gold', ['bar', 'rawa'])
            ->assertJsonPath('categories.silver', ['bar']);
    }
}
