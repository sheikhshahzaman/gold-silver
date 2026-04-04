<?php

namespace App\Filament\Resources\NewsTickerResource\Pages;

use App\Filament\Resources\NewsTickerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNewsTickers extends ListRecords
{
    protected static string $resource = NewsTickerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
