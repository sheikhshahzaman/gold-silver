<?php

namespace App\Filament\Resources\InventoryItemResource\Pages;

use App\Filament\Resources\InventoryItemResource;
use App\Models\InventoryItem;
use App\Models\Product;
use App\Services\QrPdfGenerator;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Collection;

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

                    $created = new Collection();
                    for ($i = 0; $i < $count; $i++) {
                        $created->push(InventoryItem::create([
                            'product_id' => $productId,
                        ]));
                    }

                    Notification::make()
                        ->title("Generated {$count} inventory item(s) with QR codes")
                        ->body('Downloading printable QR sheet…')
                        ->success()
                        ->send();

                    $pdf = (new QrPdfGenerator())->generate($created);

                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        'qr-codes-batch-' . now()->format('Y-m-d-His') . '.pdf',
                    );
                }),

            Actions\CreateAction::make(),
        ];
    }
}
