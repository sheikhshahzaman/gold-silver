<?php

namespace App\Filament\Resources\NewsTickerResource\Pages;

use App\Filament\Resources\NewsTickerResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNewsTicker extends CreateRecord
{
    protected static string $resource = NewsTickerResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
