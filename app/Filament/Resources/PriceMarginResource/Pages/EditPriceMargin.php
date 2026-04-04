<?php

namespace App\Filament\Resources\PriceMarginResource\Pages;

use App\Filament\Resources\PriceMarginResource;
use App\Models\MarginLog;
use App\Services\PriceEngine\PriceFetcher;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditPriceMargin extends EditRecord
{
    protected static string $resource = PriceMarginResource::class;

    protected array $oldMarginData = [];

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = Auth::id();

        // Capture old values before save for audit log
        $this->oldMarginData = [
            'buy_margin' => $this->record->buy_margin,
            'sell_margin' => $this->record->sell_margin,
        ];

        return $data;
    }

    protected function afterSave(): void
    {
        $record = $this->record;

        MarginLog::create([
            'metal' => $record->metal,
            'karat' => $record->karat,
            'old_buy_margin' => $this->oldMarginData['buy_margin'] ?? 0,
            'new_buy_margin' => $record->buy_margin,
            'old_sell_margin' => $this->oldMarginData['sell_margin'] ?? 0,
            'new_sell_margin' => $record->sell_margin,
            'changed_by' => Auth::id(),
            'created_at' => now(),
        ]);

        // Re-fetch prices with updated margins so they take effect immediately
        try {
            app(PriceFetcher::class)->fetchAndStore();
            Notification::make()
                ->title('Prices recalculated with new margins.')
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Margin saved but price refresh failed: ' . $e->getMessage())
                ->warning()
                ->send();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
