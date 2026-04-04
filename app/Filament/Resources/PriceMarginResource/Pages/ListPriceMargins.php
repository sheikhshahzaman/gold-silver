<?php

namespace App\Filament\Resources\PriceMarginResource\Pages;

use App\Filament\Resources\PriceMarginResource;
use Filament\Resources\Pages\ListRecords;

class ListPriceMargins extends ListRecords
{
    protected static string $resource = PriceMarginResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
