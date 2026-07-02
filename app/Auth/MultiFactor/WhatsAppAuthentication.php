<?php

namespace App\Auth\MultiFactor;

use App\Models\Setting;
use App\Services\WhatsApp\WhatsAppCloudApi;
use Closure;
use Filament\Actions\Action;
use Filament\Auth\MultiFactor\Contracts\HasBeforeChallengeHook;
use Filament\Auth\MultiFactor\Contracts\MultiFactorAuthenticationProvider;
use Filament\Forms\Components\OneTimeCodeInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Text;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Admin-login 2FA: a code is sent via WhatsApp (Meta Cloud API) to every
 * number configured on the Two-Factor Authentication settings page. Unlike
 * Filament's built-in per-user Email/App providers, this one is a single
 * GLOBAL on/off switch — matching "add some numbers, turn 2FA on/off" rather
 * than each admin managing their own factor.
 *
 * If WhatsApp credentials haven't been configured yet (Meta Business setup
 * pending), the code is written to the log instead of sent, so the login
 * flow can still be tested end-to-end before that setup completes. This
 * fallback is NOT secure and is clearly logged as such.
 */
class WhatsAppAuthentication implements HasBeforeChallengeHook, MultiFactorAuthenticationProvider
{
    protected int $codeExpiryMinutes = 10;

    protected ?Closure $generateCodesUsing = null;

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'whatsapp_code';
    }

    public function getLoginFormLabel(): string
    {
        return 'WhatsApp Code';
    }

    public function isEnabled(Authenticatable $user): bool
    {
        return Setting::get('two_factor_enabled', '0') === '1';
    }

    public function beforeChallenge(Authenticatable $user): void
    {
        $this->sendCode($user);
    }

    public function sendCode(Authenticatable $user): bool
    {
        $rateLimitingKey = "whatsapp-authentication:{$user->getAuthIdentifier()}";

        if (RateLimiter::tooManyAttempts($rateLimitingKey, maxAttempts: 3)) {
            return false;
        }
        RateLimiter::hit($rateLimitingKey, decaySeconds: 60);

        $code = $this->generateCode();

        session()->put('whatsapp_authentication_code', Hash::make($code));
        session()->put('whatsapp_authentication_code_expires_at', now()->addMinutes($this->codeExpiryMinutes));

        $api = app(WhatsAppCloudApi::class);
        $numbers = $api->recipients();

        if (empty($numbers)) {
            Log::warning('WhatsApp 2FA: no recipient numbers configured — code not delivered anywhere.');

            return false;
        }

        if (! $api->isConfigured()) {
            // Dev/setup-pending fallback: NOT secure, visible to anyone with log
            // access. Lets the admin test the login flow before Meta approval.
            Log::warning('WhatsApp 2FA: WhatsApp API not configured — logging code instead of sending it.', [
                'code' => $code,
                'would_send_to' => $numbers,
            ]);

            return true;
        }

        $sentToAny = false;
        foreach ($numbers as $number) {
            if ($api->sendCode($number, $code)) {
                $sentToAny = true;
            }
        }

        return $sentToAny;
    }

    public function generateCodesUsing(?Closure $callback): static
    {
        $this->generateCodesUsing = $callback;

        return $this;
    }

    public function generateCode(): string
    {
        if ($this->generateCodesUsing) {
            return ($this->generateCodesUsing)();
        }

        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    public function verifyCode(string $code): bool
    {
        $codeHash = session('whatsapp_authentication_code');
        $codeExpiresAt = session('whatsapp_authentication_code_expires_at');

        if (
            blank($codeHash)
            || blank($codeExpiresAt)
            || (! Hash::check($code, $codeHash))
            || now()->greaterThan($codeExpiresAt)
        ) {
            return false;
        }

        session()->forget('whatsapp_authentication_code');
        session()->forget('whatsapp_authentication_code_expires_at');

        return true;
    }

    /**
     * @return array<\Filament\Schemas\Components\Component|Action>
     */
    public function getManagementSchemaComponents(): array
    {
        $enabled = Setting::get('two_factor_enabled', '0') === '1';

        return [
            Text::make($enabled
                ? 'WhatsApp two-factor authentication is ON for all admins. Manage numbers and the on/off switch on the Two-Factor Authentication settings page.'
                : 'WhatsApp two-factor authentication is OFF. Turn it on from the Two-Factor Authentication settings page.'),
        ];
    }

    /**
     * @return array<\Filament\Schemas\Components\Component|Action>
     */
    public function getChallengeFormComponents(Authenticatable $user): array
    {
        return [
            OneTimeCodeInput::make('code')
                ->label('WhatsApp code')
                ->validationAttribute('code')
                ->belowContent(Action::make('resend')
                    ->label('Resend code')
                    ->link()
                    ->action(function () use ($user): void {
                        if (! $this->sendCode($user)) {
                            Notification::make()
                                ->title('Please wait a moment before requesting another code.')
                                ->danger()
                                ->send();

                            return;
                        }

                        Notification::make()
                            ->title('Code resent.')
                            ->success()
                            ->send();
                    }))
                ->required()
                ->rule(function (): Closure {
                    return function (string $attribute, $value, Closure $fail): void {
                        if ($this->verifyCode($value)) {
                            return;
                        }

                        $fail('The code you entered is invalid or has expired.');
                    };
                }),
        ];
    }
}
