<?php

namespace App\Filament\Resources\PriceMarginResource\Pages;

use App\Filament\Resources\PriceMarginResource;
use App\Services\PriceEngine\PriceFetcher;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        // Recompute and re-cache prices immediately so the new price shows up
        // on the public site without waiting for the next 1-minute cron tick.
        // If the upstream fetch fails (no internet, etc.) we still notify
        // success because the DB row is saved -- prices will catch up on the
        // next scheduled run.
        $upstreamOk = false;
        try {
            $upstreamOk = app(PriceFetcher::class)->fetchAndStore();
        } catch (\Throwable $e) {
            Log::warning('Price save: on-demand price refresh failed', [
                'error' => $e->getMessage(),
            ]);
        }

        Notification::make()
            ->title('Price saved.')
            ->body($upstreamOk
                ? 'Live prices updated. Refresh the public site to see the new values.'
                : 'Price stored, but live prices could not refresh right now. They will update on the next scheduled fetch (within 1 minute).')
            ->success()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
