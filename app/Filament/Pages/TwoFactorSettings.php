<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class TwoFactorSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-shield-check';

    protected static string | \UnitEnum | null $navigationGroup = 'System';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Two-Factor Authentication';

    protected static ?string $title = 'Two-Factor Authentication';

    protected string $view = 'filament.pages.two-factor-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $numbers = json_decode((string) Setting::get('two_factor_numbers', '[]'), true);

        $this->form->fill([
            'two_factor_enabled' => Setting::get('two_factor_enabled', '0') === '1',
            'two_factor_numbers' => is_array($numbers) ? $numbers : [],
            'whatsapp_api_token' => Setting::get('whatsapp_api_token', ''),
            'whatsapp_phone_number_id' => Setting::get('whatsapp_phone_number_id', ''),
            'whatsapp_template_name' => Setting::get('whatsapp_template_name', ''),
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('How WhatsApp 2FA Works')
                    ->description('This protects admin login. It is WhatsApp Cloud API based, not normal SMS.')
                    ->schema([
                        Placeholder::make('two_factor_guidance')
                            ->label('')
                            ->content(new HtmlString(
                                '<div class="space-y-2 text-sm">'
                                . '<p><strong>What happens:</strong> after email/password login, the admin must enter a one-time code. The code is sent to the WhatsApp numbers listed below.</p>'
                                . '<p><strong>Where to get API details:</strong> create or open a Meta Developer app at <code>developers.facebook.com</code>, add “WhatsApp”, then open WhatsApp → API Setup. There you will find the <strong>Access Token</strong> and <strong>Phone Number ID</strong>.</p>'
                                . '<p><strong>Template name:</strong> in Meta WhatsApp Manager, create an approved authentication/utility template that accepts one text value: the login code. Paste the approved template name here.</p>'
                                . '<p><strong>Recipient numbers:</strong> add trusted admin WhatsApp numbers with country code and no plus sign, e.g. <code>923001234567</code>.</p>'
                                . '<p><strong>Testing note:</strong> if API fields are empty, the code is written to server logs instead of WhatsApp. That is useful locally, but not secure for production.</p>'
                                . '</div>'
                            )),
                    ]),
                Section::make('Admin Login Verification')
                    ->description('Turn this ON only after your WhatsApp API fields and recipient numbers are ready.')
                    ->schema([
                        Toggle::make('two_factor_enabled')
                            ->label('Require a WhatsApp code to log in')
                            ->onColor('success')
                            ->offColor('gray'),
                        TagsInput::make('two_factor_numbers')
                            ->label('WhatsApp numbers that receive the code')
                            ->placeholder('e.g. 923001234567, press Enter')
                            ->helperText('Include country code, no + or spaces (e.g. 923001234567). Any of these numbers can be used to complete a login.')
                            ->splitKeys([',', 'Enter'])
                            ->columnSpanFull(),
                    ]),
                Section::make('WhatsApp Cloud API')
                    ->description('Values come from Meta Developer / WhatsApp Business Platform. Search online for “Meta WhatsApp Cloud API setup” if you need the official step-by-step.')
                    ->schema([
                        TextInput::make('whatsapp_api_token')
                            ->label('Access Token')
                            ->password()
                            ->revealable()
                            ->autocomplete('off')
                            ->helperText('Meta Developer → your app → WhatsApp → API Setup → Temporary/permanent access token. Use a permanent system-user token for production.'),
                        TextInput::make('whatsapp_phone_number_id')
                            ->label('Phone Number ID')
                            ->helperText('Meta Developer → WhatsApp → API Setup. This is not your phone number; it is the numeric Phone Number ID.'),
                        TextInput::make('whatsapp_template_name')
                            ->label('Approved Template Name')
                            ->helperText('Meta WhatsApp Manager → Message Templates. Use an approved template that accepts one text parameter: the code.'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Setting::set('two_factor_enabled', ($data['two_factor_enabled'] ?? false) ? '1' : '0');
        Setting::set('two_factor_numbers', json_encode(array_values($data['two_factor_numbers'] ?? [])));
        Setting::set('whatsapp_api_token', (string) ($data['whatsapp_api_token'] ?? ''));
        Setting::set('whatsapp_phone_number_id', (string) ($data['whatsapp_phone_number_id'] ?? ''));
        Setting::set('whatsapp_template_name', (string) ($data['whatsapp_template_name'] ?? ''));

        Notification::make()
            ->title('Two-factor authentication settings saved.')
            ->success()
            ->send();
    }
}
