<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Services\PriceEngine\PriceCacheManager;
use App\Services\PriceEngine\PriceFetcher;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Cache;

class MetalRates extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static string | \UnitEnum | null $navigationGroup = 'Price Management';

    protected static ?int $navigationSort = 0;

    protected static ?string $navigationLabel = 'Gold & Silver Rates';

    protected static ?string $title = 'Gold & Silver Rates';

    protected string $view = 'filament.pages.metal-rates';

    public ?array $data = [];

    /** Gold karats shown in the form: key => label. */
    private const GOLD_KARATS = [
        '24k' => '24K',
        '22k' => '22K',
        '21k' => '21K',
        '18k' => '18K',
        'rawa' => 'Rawa',
    ];

    /** Silver units shown in the form: key => label. */
    private const SILVER_UNITS = [
        'tola' => '1 Tola',
        '10_tola' => '10 Tola',
        'kg' => '1 KG',
        '10_gram' => '10 Gram',
        '5_gram' => '5 Gram',
        'gram' => '1 Gram',
    ];

    public function mount(): void
    {
        $state = [
            // Toggle ON means "manual" (admin-set); OFF means "live" (market).
            'rate_mode' => Setting::get('rate_mode', 'manual') !== 'live',
        ];

        foreach (array_keys(self::GOLD_KARATS) as $key) {
            $state['manual_gold_' . $key] = (float) Setting::get('manual_gold_' . $key, 0);
        }

        foreach (array_keys(self::SILVER_UNITS) as $key) {
            $state['manual_silver_' . $key] = (float) Setting::get('manual_silver_' . $key, 0);
        }

        $this->form->fill($state);
    }

    public function form(Schema $form): Schema
    {
        $goldInputs = [];
        foreach (self::GOLD_KARATS as $key => $label) {
            $goldInputs[] = TextInput::make('manual_gold_' . $key)
                ->label($label . ' — per Tola')
                ->numeric()
                ->minValue(0)
                ->prefix('Rs')
                ->required();
        }

        $silverInputs = [];
        foreach (self::SILVER_UNITS as $key => $label) {
            $silverInputs[] = TextInput::make('manual_silver_' . $key)
                ->label($label)
                ->numeric()
                ->minValue(0)
                ->prefix('Rs')
                ->required();
        }

        return $form
            ->schema([
                Section::make('Rate Source')
                    ->description('Where gold & silver rates come from. The international "spot" (USD/oz) rates always stay live regardless of this setting.')
                    ->schema([
                        Toggle::make('rate_mode')
                            ->label('Use manual (admin-set) rates')
                            ->helperText('ON: gold & silver use the rates you enter below. OFF: they follow the live market again. Either way they still visually fluctuate to feel live — control that movement with "Live rate updates" in Site Settings.')
                            ->onColor('success')
                            ->offColor('warning'),
                    ]),
                Section::make('Gold — base price per Tola')
                    ->description('Customer Buy/Sell = this base + the Buy/Sell margin from Price Margins. Per-gram and 10-gram are derived from the per-tola value. Leave a karat at 0 to auto-derive it from 24K by purity.')
                    ->schema($goldInputs)
                    ->columns(2),
                Section::make('Silver — base price per unit')
                    ->description('Each unit is set independently. Customer Buy/Sell = this base + the per-unit Silver margin from Price Margins. Leave a unit at 0 to auto-derive it from the 1 Tola rate.')
                    ->schema($silverInputs)
                    ->columns(3),
            ])
            ->statePath('data');
    }

    /**
     * Prefill the form with the current live-market rates so the admin can start
     * from real values and tweak. Does not persist until "Save Rates" is pressed.
     */
    public function pullLiveRates(): void
    {
        $all = app(PriceCacheManager::class)->getAllPrices();
        $gold = $all['gold'] ?? [];
        $silver = $all['silver'] ?? [];

        foreach (array_keys(self::GOLD_KARATS) as $key) {
            $base = $gold[$key]['tola']['base'] ?? $gold[$key]['tola']['buy'] ?? null;
            if ($base !== null) {
                $this->data['manual_gold_' . $key] = round((float) $base, 2);
            }
        }

        foreach (array_keys(self::SILVER_UNITS) as $key) {
            $base = $silver[$key]['base'] ?? $silver[$key]['buy'] ?? null;
            if ($base !== null) {
                $this->data['manual_silver_' . $key] = round((float) $base, 2);
            }
        }

        Notification::make()
            ->title('Live rates pulled in. Review the numbers, then press "Save Rates" to apply.')
            ->success()
            ->send();
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Setting::set('rate_mode', ($data['rate_mode'] ?? true) ? 'manual' : 'live');

        foreach (array_keys(self::GOLD_KARATS) as $key) {
            Setting::set('manual_gold_' . $key, (string) ($data['manual_gold_' . $key] ?? 0));
        }

        foreach (array_keys(self::SILVER_UNITS) as $key) {
            Setting::set('manual_silver_' . $key, (string) ($data['manual_silver_' . $key] ?? 0));
        }

        Cache::forget('setting.rate_mode');

        // Rebuild the price cache now so the new rates appear immediately on the
        // website and app instead of waiting for the 1-minute scheduler.
        try {
            app(PriceFetcher::class)->fetchAndStore();
        } catch (\Throwable $e) {
            // Non-fatal — the scheduler will pick the new rates up within a minute.
        }

        Notification::make()
            ->title('Gold & silver rates saved.')
            ->body('They are now live on the website and the app.')
            ->success()
            ->send();
    }
}
