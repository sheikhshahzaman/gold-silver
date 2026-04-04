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
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

class SiteSettings extends Page implements HasForms
{
    use InteractsWithForms;
    use WithFileUploads;

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
            'site_name' => Setting::get('site_name', 'PakGold Rates'),
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
                            ->placeholder('PakGold Rates'),
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
        $data = $this->form->getState();

        if (isset($data['site_name'])) {
            Setting::set('site_name', $data['site_name']);
        }

        Notification::make()
            ->title('Site settings saved successfully.')
            ->success()
            ->send();
    }
}
