<?php

namespace Tests\Feature;

use App\Auth\MultiFactor\WhatsAppAuthentication;
use App\Filament\Pages\TwoFactorSettings;
use App\Models\Setting;
use App\Models\User;
use Filament\Auth\Pages\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class WhatsAppTwoFactorTest extends TestCase
{
    use RefreshDatabase;

    public function test_correct_code_verifies_and_wrong_code_does_not(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $provider = WhatsAppAuthentication::make()->generateCodesUsing(fn () => '482913');

        // No WhatsApp credentials configured — takes the log fallback path,
        // but must still store the code for verification.
        $provider->sendCode($user);

        $this->assertFalse($provider->verifyCode('000000'));
        $this->assertTrue($provider->verifyCode('482913'));
        // A code can only be used once.
        $this->assertFalse($provider->verifyCode('482913'));
    }

    public function test_expired_code_does_not_verify(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $provider = WhatsAppAuthentication::make()->generateCodesUsing(fn () => '111111');

        $provider->sendCode($user);
        $this->travel(11)->minutes(); // codeExpiryMinutes is 10

        $this->assertFalse($provider->verifyCode('111111'));
    }

    public function test_provider_is_enabled_only_when_setting_is_on(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $provider = WhatsAppAuthentication::make();

        Setting::set('two_factor_enabled', '0');
        $this->assertFalse($provider->isEnabled($user));

        Setting::set('two_factor_enabled', '1');
        $this->assertTrue($provider->isEnabled($user));
    }

    public function test_settings_page_saves_numbers_and_toggle(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)
            ->test(TwoFactorSettings::class)
            ->fillForm([
                'two_factor_enabled' => true,
                'two_factor_numbers' => ['923001234567', '923009876543'],
                'whatsapp_api_token' => 'test-token',
                'whatsapp_phone_number_id' => '123456',
                'whatsapp_template_name' => 'otp_code',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('1', Setting::get('two_factor_enabled'));
        $this->assertSame(
            ['923001234567', '923009876543'],
            json_decode(Setting::get('two_factor_numbers'), true)
        );
        $this->assertSame('test-token', Setting::get('whatsapp_api_token'));
    }

    public function test_login_completes_in_one_step_when_two_factor_is_off(): void
    {
        Setting::set('two_factor_enabled', '0');
        $user = User::factory()->create(['role' => 'admin', 'password' => bcrypt('password')]);

        Livewire::test(Login::class)
            ->fillForm(['email' => $user->email, 'password' => 'password'])
            ->call('authenticate');

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_does_not_complete_after_password_alone_when_two_factor_is_on(): void
    {
        Setting::set('two_factor_enabled', '1');
        Setting::set('two_factor_numbers', json_encode(['923001234567']));
        $user = User::factory()->create(['role' => 'admin', 'password' => bcrypt('password')]);

        Livewire::test(Login::class)
            ->fillForm(['email' => $user->email, 'password' => 'password'])
            ->call('authenticate');

        // Password was correct, but the WhatsApp code hasn't been entered yet.
        $this->assertGuest();
    }
}
