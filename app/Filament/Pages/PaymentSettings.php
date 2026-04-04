<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string | \UnitEnum | null $navigationGroup = 'Orders & Payments';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Payment Settings';

    protected static ?string $title = 'Payment Account Details';

    protected string $view = 'filament.pages.payment-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'payment_easypaisa_name' => Setting::get('payment_easypaisa_name', ''),
            'payment_easypaisa_number' => Setting::get('payment_easypaisa_number', ''),
            'payment_jazzcash_name' => Setting::get('payment_jazzcash_name', ''),
            'payment_jazzcash_number' => Setting::get('payment_jazzcash_number', ''),
            'payment_raast_name' => Setting::get('payment_raast_name', ''),
            'payment_raast_id' => Setting::get('payment_raast_id', ''),
            'payment_bank_name' => Setting::get('payment_bank_name', ''),
            'payment_bank_account_title' => Setting::get('payment_bank_account_title', ''),
            'payment_bank_account_number' => Setting::get('payment_bank_account_number', ''),
            'payment_bank_iban' => Setting::get('payment_bank_iban', ''),
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('EasyPaisa')
                    ->schema([
                        TextInput::make('payment_easypaisa_name')
                            ->label('Account Name')
                            ->required(),
                        TextInput::make('payment_easypaisa_number')
                            ->label('Account Number')
                            ->required(),
                    ])
                    ->columns(2),
                Section::make('JazzCash')
                    ->schema([
                        TextInput::make('payment_jazzcash_name')
                            ->label('Account Name')
                            ->required(),
                        TextInput::make('payment_jazzcash_number')
                            ->label('Account Number')
                            ->required(),
                    ])
                    ->columns(2),
                Section::make('Raast')
                    ->schema([
                        TextInput::make('payment_raast_name')
                            ->label('Account Name')
                            ->required(),
                        TextInput::make('payment_raast_id')
                            ->label('Raast ID')
                            ->required(),
                    ])
                    ->columns(2),
                Section::make('Bank Transfer')
                    ->schema([
                        TextInput::make('payment_bank_name')
                            ->label('Bank Name')
                            ->required(),
                        TextInput::make('payment_bank_account_title')
                            ->label('Account Title')
                            ->required(),
                        TextInput::make('payment_bank_account_number')
                            ->label('Account Number')
                            ->required(),
                        TextInput::make('payment_bank_iban')
                            ->label('IBAN')
                            ->required(),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        Notification::make()
            ->title('Payment settings saved successfully.')
            ->success()
            ->send();
    }
}
