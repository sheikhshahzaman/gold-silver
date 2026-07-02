<?php

namespace App\Services\WhatsApp;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Thin client for Meta's WhatsApp Cloud API (official, free within the
 * monthly conversation quota). Credentials are set by the admin on the
 * Two-Factor Authentication settings page — until they are, sendMessage()
 * fails closed and the caller falls back to logging the code instead.
 */
class WhatsAppCloudApi
{
    public function isConfigured(): bool
    {
        return filled(Setting::get('whatsapp_api_token'))
            && filled(Setting::get('whatsapp_phone_number_id'))
            && filled(Setting::get('whatsapp_template_name'));
    }

    /**
     * Send a one-time code to a single WhatsApp number via an approved
     * template message (business-initiated messages require a template).
     * The template must accept exactly one text parameter: the code.
     */
    public function sendCode(string $toNumber, string $code): bool
    {
        if (! $this->isConfigured()) {
            return false;
        }

        $phoneNumberId = Setting::get('whatsapp_phone_number_id');
        $token = Setting::get('whatsapp_api_token');
        $template = Setting::get('whatsapp_template_name');
        $digits = preg_replace('/[^0-9]/', '', $toNumber);

        try {
            $response = Http::withToken($token)
                ->timeout(10)
                ->post("https://graph.facebook.com/v20.0/{$phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => $digits,
                    'type' => 'template',
                    'template' => [
                        'name' => $template,
                        'language' => ['code' => 'en_US'],
                        'components' => [
                            [
                                'type' => 'body',
                                'parameters' => [
                                    ['type' => 'text', 'text' => $code],
                                ],
                            ],
                        ],
                    ],
                ]);

            if ($response->failed()) {
                Log::warning('WhatsApp Cloud API: failed to send 2FA code', [
                    'to' => $digits,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::warning('WhatsApp Cloud API: exception sending 2FA code', [
                'to' => $digits,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * @return array<int, string> configured recipient numbers
     */
    public function recipients(): array
    {
        $raw = Setting::get('two_factor_numbers', '[]');
        $numbers = json_decode((string) $raw, true);

        return is_array($numbers) ? array_values(array_filter($numbers)) : [];
    }
}
