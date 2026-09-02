<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\RequiresStaffPermission;
use App\Models\Setting;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

class SiteSettings extends Page implements HasForms
{
    use RequiresStaffPermission;
    use InteractsWithForms;
    use WithFileUploads;

    protected static string $staffFeature = 'site_settings';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-paint-brush';

    protected static string | \UnitEnum | null $navigationGroup = 'System';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Site Settings';

    protected static ?string $title = 'Site Settings';

    protected string $view = 'filament.pages.site-settings';

    public ?array $data = [];

    public $logoUpload = null;

    public function mount(): void
    {
        $this->form->fill([
            'site_name' => Setting::get('site_name', 'Islamabad Bullion Exchange'),
            'live_rates_enabled' => Setting::get('live_rates_enabled', '1') === '1',
            'international_spread_gold_pct' => (float) Setting::get('international_spread_gold_pct', 0.05),
            'international_spread_silver_pct' => (float) Setting::get('international_spread_silver_pct', 0.1),
            'silver_note_active' => Setting::get('silver_note_active', '0') === '1',
            'silver_note_en' => Setting::get('silver_note_en', ''),
            'silver_note_ur' => Setting::get('silver_note_ur', ''),
            'delivery_charge' => (float) Setting::get('delivery_charge', 0),
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Site Identity')
                    ->schema([
                        TextInput::make('site_name')
                            ->label('Site Name')
                            ->placeholder('Islamabad Bullion Exchange'),
                    ]),
                Section::make('Live Rates')
                    ->description('Control the live ticking/fluctuation shown on BOTH the website and the mobile app.')
                    ->schema([
                        Toggle::make('live_rates_enabled')
                            ->label('Live rate updates (website + app)')
                            ->helperText('Controls the on-screen ticking/fluctuation on BOTH the website and the mobile app. ON: prices tick and refresh in real time. OFF: prices freeze at their last values and all live indicators pause until you switch this back on. (Gold/silver values come from the "Gold & Silver Rates" page; this only controls the live movement effect.)')
                            ->onColor('success')
                            ->offColor('danger'),
                    ]),
                Section::make('International Spread')
                    ->description('Margin applied to international BID prices to compute the displayed ASK. Only affects the International Metal Rates table (USD/oz).')
                    ->schema([
                        TextInput::make('international_spread_gold_pct')
                            ->label('Gold Spread')
                            ->helperText('ASK = BID × (1 + spread / 100). Default: 0.05%')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(5)
                            ->step(0.01)
                            ->suffix('%'),
                        TextInput::make('international_spread_silver_pct')
                            ->label('Silver Spread')
                            ->helperText('ASK = BID × (1 + spread / 100). Default: 0.1%')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(5)
                            ->step(0.01)
                            ->suffix('%'),
                    ]),
                Section::make('Delivery')
                    ->description('One flat delivery charge applied to every order that chooses delivery. Pickup is always free. Set this to 0 to offer free delivery — customers will see a "Free Delivery" message instead of a charge.')
                    ->schema([
                        TextInput::make('delivery_charge')
                            ->label('Delivery Charge')
                            ->helperText('Shown to the customer when they choose delivery at checkout. 0 = free delivery.')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('Rs'),
                    ]),
                Section::make('Silver Note (mobile app)')
                    ->description('Optional note shown under the silver table in the app. Turn it on and add text to display it; leave it off to hide it.')
                    ->schema([
                        Toggle::make('silver_note_active')
                            ->label('Show silver note in the app')
                            ->onColor('success')
                            ->offColor('gray'),
                        Textarea::make('silver_note_en')
                            ->label('Note (English)')
                            ->rows(2)
                            ->maxLength(500),
                        Textarea::make('silver_note_ur')
                            ->label('Note (Urdu)')
                            ->rows(2)
                            ->maxLength(500),
                    ]),
            ])
            ->statePath('data');
    }

    public function updatedLogoUpload(): void
    {
        $this->validate([
            'logoUpload' => 'image|max:1024',
        ]);
    }

    public function saveLogo(): void
    {
        $this->ensureCanEdit();

        $this->validate([
            'logoUpload' => 'required|image|max:1024',
        ]);

        // Remove old logo
        $oldLogo = Setting::get('site_logo');
        if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
            Storage::disk('public')->delete($oldLogo);
        }

        $path = $this->logoUpload->store('branding', 'public');
        Setting::set('site_logo', $path);
        $this->logoUpload = null;

        Notification::make()
            ->title('Logo updated successfully.')
            ->success()
            ->send();
    }

    public function removeLogo(): void
    {
        $this->ensureCanEdit();

        $oldLogo = Setting::get('site_logo');
        if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
            Storage::disk('public')->delete($oldLogo);
        }
        Setting::set('site_logo', null);

        Notification::make()
            ->title('Logo removed.')
            ->success()
            ->send();
    }

    public function save(): void
    {
        $this->ensureCanEdit();

        $data = $this->form->getState();

        if (isset($data['site_name'])) {
            Setting::set('site_name', $data['site_name']);
        }

        if (array_key_exists('live_rates_enabled', $data)) {
            Setting::set('live_rates_enabled', $data['live_rates_enabled'] ? '1' : '0');
            Cache::forget('setting.live_rates_enabled');
        }

        if (array_key_exists('international_spread_gold_pct', $data)) {
            Setting::set('international_spread_gold_pct', (float) $data['international_spread_gold_pct']);
            Cache::forget('setting.intl_spread_gold');
        }

        if (array_key_exists('international_spread_silver_pct', $data)) {
            Setting::set('international_spread_silver_pct', (float) $data['international_spread_silver_pct']);
            Cache::forget('setting.intl_spread_silver');
        }

        if (array_key_exists('silver_note_active', $data)) {
            Setting::set('silver_note_active', $data['silver_note_active'] ? '1' : '0');
        }
        if (array_key_exists('silver_note_en', $data)) {
            Setting::set('silver_note_en', (string) ($data['silver_note_en'] ?? ''));
        }
        if (array_key_exists('silver_note_ur', $data)) {
            Setting::set('silver_note_ur', (string) ($data['silver_note_ur'] ?? ''));
        }
        Cache::forget('api.silver_note');

        if (array_key_exists('delivery_charge', $data)) {
            Setting::set('delivery_charge', (float) ($data['delivery_charge'] ?? 0));
            Cache::forget('setting.delivery_charge');
        }

        Notification::make()
            ->title('Site settings saved successfully.')
            ->success()
            ->send();
    }
}
