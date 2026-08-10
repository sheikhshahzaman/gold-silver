<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;

class OrderSmsSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string | \UnitEnum | null $navigationGroup = 'System';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Order SMS / Webhook';

    protected static ?string $title = 'Order SMS / Webhook';

    protected string $view = 'filament.pages.order-sms-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'order_sms_enabled' => Setting::get('order_sms_enabled', '0') === '1',
            'order_sms_webhook_url' => Setting::get('order_sms_webhook_url', config('services.order_sms.webhook_url', '')),
            'order_sms_auth_token' => Setting::get('order_sms_auth_token', ''),
            'order_sms_to' => Setting::get('order_sms_to', config('services.order_sms.to', '')),
            'order_sms_template' => Setting::get(
                'order_sms_template',
                'New order {order_number}: {customer_name} ({customer_phone}), total Rs {total}.'
            ),
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('How Order SMS Works')
                    ->description('This project can send order notifications through a webhook. The webhook can be a real SMS provider API, or an automation service like Make/Zapier/Pabbly that forwards the message to an SMS gateway.')
                    ->schema([
                        Placeholder::make('order_sms_guidance')
                            ->label('')
                            ->content(new HtmlString(
                                '<div class="space-y-2 text-sm">'
                                . '<p><strong>What you need:</strong> an SMS provider account or webhook URL. Search online for “Pakistan transactional SMS API”, “Twilio SMS API”, “Vonage SMS API”, or use Make/Zapier/Pabbly webhook if you want no-code setup.</p>'
                                . '<p><strong>Webhook URL:</strong> paste the provider/API/webhook endpoint that accepts a POST request. We send JSON data: order number, customer name, customer phone, total, and message.</p>'
                                . '<p><strong>Auth token:</strong> optional. If your provider gives an API token/bearer token, paste it here. We send it as <code>Authorization: Bearer TOKEN</code>.</p>'
                                . '<p><strong>Admin number:</strong> the number that should receive the order alert. Use country code format, e.g. <code>923001234567</code>.</p>'
                                . '<p><strong>Important:</strong> real SMS is normally paid. Free testing may be possible through provider trial credits, but production delivery usually needs balance/package.</p>'
                                . '</div>'
                            )),
                    ]),
                Section::make('Order Notification Settings')
                    ->schema([
                        Toggle::make('order_sms_enabled')
                            ->label('Send order notification webhook')
                            ->helperText('When ON, the website sends a POST request when payment proof is submitted from the app or website.')
                            ->onColor('success')
                            ->offColor('gray'),
                        TextInput::make('order_sms_webhook_url')
                            ->label('Webhook / SMS API URL')
                            ->placeholder('https://...')
                            ->url()
                            ->helperText('Paste the SMS provider endpoint or automation webhook URL. Leave empty to only log notifications.')
                            ->columnSpanFull(),
                        TextInput::make('order_sms_auth_token')
                            ->label('Auth Token / API Key')
                            ->password()
                            ->revealable()
                            ->autocomplete('off')
                            ->helperText('Optional. If filled, it is sent as Authorization: Bearer YOUR_TOKEN.'),
                        TextInput::make('order_sms_to')
                            ->label('Admin number to notify')
                            ->placeholder('923001234567')
                            ->helperText('Use country code. No spaces. Example Pakistan: 923001234567.'),
                        Textarea::make('order_sms_template')
                            ->label('Message Template')
                            ->rows(3)
                            ->helperText('Available placeholders: {order_number}, {customer_name}, {customer_phone}, {total}, {status}.')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Setting::set('order_sms_enabled', ($data['order_sms_enabled'] ?? false) ? '1' : '0');
        Setting::set('order_sms_webhook_url', (string) ($data['order_sms_webhook_url'] ?? ''));
        Setting::set('order_sms_auth_token', (string) ($data['order_sms_auth_token'] ?? ''));
        Setting::set('order_sms_to', (string) ($data['order_sms_to'] ?? ''));
        Setting::set('order_sms_template', (string) ($data['order_sms_template'] ?? ''));

        Notification::make()
            ->title('Order SMS settings saved.')
            ->success()
            ->send();
    }

    public function sendTest(): void
    {
        $data = $this->form->getState();
        $url = trim((string) ($data['order_sms_webhook_url'] ?? ''));

        if ($url === '') {
            Notification::make()
                ->title('Add a webhook URL first.')
                ->warning()
                ->send();

            return;
        }

        $payload = [
            'order_number' => 'TEST-ORDER',
            'to' => (string) ($data['order_sms_to'] ?? ''),
            'customer_phone' => '923001234567',
            'customer_name' => 'Test Customer',
            'message' => $this->renderMessage((string) ($data['order_sms_template'] ?? ''), [
                'order_number' => 'TEST-ORDER',
                'customer_name' => 'Test Customer',
                'customer_phone' => '923001234567',
                'total' => '1,000',
                'status' => 'Order pending',
            ]),
            'total' => 1000,
            'status' => 'Order pending',
        ];

        try {
            $request = Http::timeout(10);
            $token = trim((string) ($data['order_sms_auth_token'] ?? ''));
            if ($token !== '') {
                $request = $request->withToken($token);
            }

            $response = $request->post($url, $payload);

            if ($response->successful()) {
                Notification::make()
                    ->title('Test webhook sent successfully.')
                    ->success()
                    ->send();

                return;
            }

            Notification::make()
                ->title('Webhook returned HTTP ' . $response->status())
                ->danger()
                ->send();
        } catch (\Throwable $e) {
            Log::warning('Order SMS test webhook failed', ['error' => $e->getMessage()]);

            Notification::make()
                ->title('Webhook test failed.')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    private function renderMessage(string $template, array $values): string
    {
        foreach ($values as $key => $value) {
            $template = str_replace('{' . $key . '}', (string) $value, $template);
        }

        return $template;
    }
}
