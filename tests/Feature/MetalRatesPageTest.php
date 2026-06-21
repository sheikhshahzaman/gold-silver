<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MetalRatesPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_gold_silver_rates_admin_page_renders(): void
    {
        $admin = User::factory()->create([
            'email' => 'verify-admin@example.com',
            'role' => 'admin',
        ]);

        Setting::set('rate_mode', 'manual');
        Setting::set('manual_gold_24k', '445500');
        Setting::set('manual_silver_tola', '7400');

        $response = $this->actingAs($admin)->get('/admin/metal-rates');

        $response->assertStatus(200);
        $response->assertSee('Gold & Silver Rates');     // page title
        $response->assertSee('Rate Source');             // section heading
        $response->assertSee('Use manual');              // Manual/Live toggle label
        $response->assertSee('Pull current live rates');  // prefill button
        $response->assertSee('Save Rates');              // submit button
        $response->assertSee('24K');                     // per-karat gold input
        $response->assertSee('22K');
        $response->assertSee('Rawa');
    }

    public function test_non_admin_cannot_access_metal_rates(): void
    {
        $user = User::factory()->create([
            'email' => 'verify-customer@example.com',
            'role' => 'customer',
        ]);

        $response = $this->actingAs($user)->get('/admin/metal-rates');

        // Filament denies non-admins (403) or redirects them away (302).
        $this->assertContains($response->status(), [403, 302]);
    }
}
