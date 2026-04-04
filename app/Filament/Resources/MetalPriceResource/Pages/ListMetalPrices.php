<?php

namespace App\Filament\Resources\MetalPriceResource\Pages;

use App\Filament\Resources\MetalPriceResource;
use Filament\Resources\Pages\ListRecords;

class ListMetalPrices extends ListRecords
{
    protected static string $resource = MetalPriceResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
