<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

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
                Section::make('Admin Login Verification')
                    ->description('When ON, every admin login requires a WhatsApp code in addition to the password. The code is sent to every number listed below.')
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
                    ->description('From your Meta Business/WhatsApp Business Platform account, once approved. Until these are filled in, codes are written to the server log instead of being sent — useful for testing the flow, but NOT secure for real use.')
                    ->schema([
                        TextInput::make('whatsapp_api_token')
                            ->label('Access Token')
                            ->password()
                            ->revealable()
                            ->autocomplete('off'),
                        TextInput::make('whatsapp_phone_number_id')
                            ->label('Phone Number ID'),
                        TextInput::make('whatsapp_template_name')
                            ->label('Approved Template Name')
                            ->helperText('The template must accept one text parameter: the code.'),
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
