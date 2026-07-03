<?php

namespace Tests\Feature;

use App\Models\InventoryItem;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerifyQrInputTest extends TestCase
{
    use RefreshDatabase;

    private function makeSoldItem(): InventoryItem
    {
        $category = ProductCategory::create(['name' => 'Silver Bars', 'slug' => 'silver-bars']);
        $product = Product::create([
            'name' => '1-Tola Silver Bar (IBE Verified)',
            'slug' => '1-tola-silver-bar-' . uniqid(),
            'weight' => '1-Tola',
            'metal' => 'silver',
            'karat' => '24k',
            'category_id' => $category->id,
            'price_type' => 'live',
            'price_key' => 'silver.tola',
            'is_active' => true,
        ]);

        return InventoryItem::create([
            'product_id' => $product->id,
            'verification_token' => 'c339428eec932d822cebdf4e4cc63a92e8a39de7',
            'serial_number' => 'IBE-SLV-000253',
            'status' => 'sold',
            'sold_at' => now()->subMonth(),
        ]);
    }

    /** The QR sticker encodes the full verify URL — a scan sends it as-is. */
    public function test_full_verify_url_in_serial_field_resolves(): void
    {
        $this->makeSoldItem();

        $this->postJson('/api/verify', [
            'serial' => 'https://islamabadbullionexchange.com/verify/c339428eec932d822cebdf4e4cc63a92e8a39de7',
        ])->assertOk()
            ->assertJsonPath('valid', true)
            ->assertJsonPath('item.serial_number', 'IBE-SLV-000253')
            ->assertJsonPath('item.status', 'sold');
    }

    public function test_short_qr_url_resolves(): void
    {
        $this->makeSoldItem();

        $this->postJson('/api/verify', [
            'serial' => 'https://islamabadbullionexchange.com/v/c339428eec932d822cebdf4e4cc63a92e8a39de7',
        ])->assertOk()->assertJsonPath('valid', true);
    }

    public function test_bare_token_in_serial_field_resolves(): void
    {
        $this->makeSoldItem();

        $this->postJson('/api/verify', [
            'serial' => 'c339428eec932d822cebdf4e4cc63a92e8a39de7',
        ])->assertOk()->assertJsonPath('valid', true);
    }

    public function test_printed_serial_still_resolves(): void
    {
        $this->makeSoldItem();

        $this->postJson('/api/verify', ['serial' => 'IBE-SLV-000253'])
            ->assertOk()->assertJsonPath('valid', true);

        $this->postJson('/api/verify', ['serial' => 'slv-000253'])
            ->assertOk()->assertJsonPath('valid', true);
    }

    public function test_unknown_serial_is_invalid_not_an_error(): void
    {
        $this->makeSoldItem();

        $this->postJson('/api/verify', ['serial' => 'IBE-FAKE-999999'])
            ->assertOk()->assertJsonPath('valid', false);
    }
}
