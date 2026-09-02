<?php

namespace App\Filament\Resources\MetalPriceResource\Pages;

use App\Filament\Resources\MetalPriceResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Cache;

class EditMetalPrice extends EditRecord
{
    protected static string $resource = MetalPriceResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTitle(): string
    {
        return MetalPriceResource::itemLabel($this->record) . ' Price';
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['source'] = 'admin';
        $data['fetched_at'] = now();

        return $data;
    }

    protected function afterSave(): void
    {
        Cache::forget('prices.all_prices');
        Cache::forget('rates.remote.current');
        Cache::forget('rates.remote.stale');

        Notification::make()
            ->title('Metal price saved.')
            ->body('Website and app spot prices will use this admin value on the next refresh.')
            ->success()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
