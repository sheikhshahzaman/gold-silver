<?php

namespace App\Filament\Resources\NewsTickerResource\Pages;

use App\Filament\Resources\NewsTickerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNewsTicker extends EditRecord
{
    protected static string $resource = NewsTickerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
