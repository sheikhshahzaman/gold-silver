<?php

namespace Tests\Feature;

use App\Models\PriceMargin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoldSilverPricesPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_gold_silver_prices_admin_list_renders(): void
    {
        $admin = User::factory()->create([
            'email' => 'verify-admin@example.com',
            'role' => 'admin',
        ]);

        PriceMargin::create(['metal' => 'gold', 'karat' => '24K', 'manual_buy_price' => 445600, 'manual_sell_price' => 446500]);
        PriceMargin::create(['metal' => 'silver', 'unit' => 'tola', 'manual_buy_price' => 7400, 'manual_sell_price' => 7400]);

        $response = $this->actingAs($admin)->get('/admin/price-margins');

        $response->assertStatus(200);
        $response->assertSee('Gold & Silver Prices'); // nav label / heading
        $response->assertSee('Buy Price');
        $response->assertSee('Sell Price');
        $response->assertSee('24K');
    }

    public function test_gold_silver_prices_admin_edit_renders(): void
    {
        $admin = User::factory()->create([
            'email' => 'verify-admin-2@example.com',
            'role' => 'admin',
        ]);

        $row = PriceMargin::create(['metal' => 'gold', 'karat' => '24K', 'manual_buy_price' => 445600, 'manual_sell_price' => 446500]);

        $response = $this->actingAs($admin)->get("/admin/price-margins/{$row->id}/edit");

        $response->assertStatus(200);
        $response->assertSee('Gold 24K Price'); // page title
        $response->assertSee('Buy Price');
        $response->assertSee('Sell Price');
        // "PriceMargin"/"PriceMarginResource" class names still appear inside
        // Livewire's internal wire:snapshot hydration payload — that's fine,
        // it's never rendered as visible text. What matters is the customer/
        // admin-facing copy says "margin" nowhere outside those attributes.
        $response->assertDontSee('Margin Settings');
        $response->assertDontSee('per-tola margin');
    }

    public function test_non_admin_cannot_access_gold_silver_prices(): void
    {
        $user = User::factory()->create([
            'email' => 'verify-customer@example.com',
            'role' => 'customer',
        ]);

        $response = $this->actingAs($user)->get('/admin/price-margins');

        // Filament denies non-admins (403) or redirects them away (302).
        $this->assertContains($response->status(), [403, 302]);
    }
}
