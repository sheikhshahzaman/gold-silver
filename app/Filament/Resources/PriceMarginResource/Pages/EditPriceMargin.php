<?php

namespace App\Filament\Resources\PriceMarginResource\Pages;

use App\Filament\Resources\PriceMarginResource;
use App\Services\Rates\PriceMarginSyncService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class EditPriceMargin extends EditRecord
{
    protected static string $resource = PriceMarginResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTitle(): string
    {
        $record = $this->record;
        if (!$record) {
            return 'Edit Price';
        }
        // Gold: "Gold 24K Price", "Gold Rawa Price", etc.
        // Silver: "Silver 1 Tola Price", "Silver 1 KG Price", etc.
        $metal = ucfirst($record->metal);
        if ($record->karat) {
            return $metal . ' ' . strtoupper($record->karat) . ' Price';
        }
        if ($record->unit) {
            return $metal . ' ' . PriceMarginResource::unitLabel($record->unit) . ' Price';
        }
        return $metal . ' Price';
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = Auth::id();

        return $data;
    }

    protected function afterSave(): void
    {
        Cache::forget('prices.all_prices');
        Cache::forget('rates.remote.current');
        Cache::forget('rates.remote.stale');

        app(PriceMarginSyncService::class)->sync();

        Notification::make()
            ->title('Price saved.')
            ->body('Website and app prices will use this admin value on the next refresh.')
            ->success()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
