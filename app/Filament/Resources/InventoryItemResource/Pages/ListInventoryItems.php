<?php

namespace App\Filament\Resources\InventoryItemResource\Pages;

use App\Filament\Resources\InventoryItemResource;
use App\Models\InventoryItem;
use App\Models\Product;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListInventoryItems extends ListRecords
{
    protected static string $resource = InventoryItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('bulkGenerate')
                ->label('Generate Items')
                ->icon('heroicon-o-plus-circle')
                ->color('primary')
                ->form([
                    Select::make('product_id')
                        ->label('Product')
                        ->options(Product::active()->ordered()->pluck('name', 'id'))
                        ->searchable()
                        ->required(),
                    TextInput::make('count')
                        ->label('Number of Items')
                        ->numeric()
                        ->required()
                        ->default(1)
                        ->minValue(1)
                        ->maxValue(100),
                ])
                ->action(function (array $data) {
                    $count = (int) $data['count'];
                    $productId = $data['product_id'];

                    for ($i = 0; $i < $count; $i++) {
                        InventoryItem::create([
                            'product_id' => $productId,
                        ]);
                    }

                    Notification::make()
                        ->title("Generated {$count} inventory item(s) with QR codes")
                        ->success()
                        ->send();
                }),

            Actions\CreateAction::make(),
        ];
    }
}
