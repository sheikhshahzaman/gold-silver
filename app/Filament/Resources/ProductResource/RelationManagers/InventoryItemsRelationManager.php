<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use App\Models\InventoryItem;
use App\Services\QrPdfGenerator;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class InventoryItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'inventoryItems';
    protected static ?string $title = 'Inventory Items';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('actual_weight')
                ->numeric()
                ->suffix('grams'),
            TextInput::make('purity_tested')
                ->maxLength(20),
            Textarea::make('notes')
                ->rows(2)
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('qr_code_path')
                    ->label('QR')
                    ->formatStateUsing(fn ($state) => $state ? '✓' : '-')
                    ->url(fn ($record) => $record->qr_code_url, shouldOpenInNewTab: true)
                    ->color('success')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('serial_number')
                    ->searchable()
                    ->weight('bold')
                    ->size('sm'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'in_stock' => 'success', 'reserved' => 'info',
                        'sold' => 'warning', 'returned' => 'danger', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('sold_price')
                    ->money('PKR')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('scan_count')
                    ->label('Scans')
                    ->alignCenter(),
            ])
            ->headerActions([
                CreateAction::make(),
                Action::make('generateBatch')
                    ->label('Generate Batch')
                    ->icon('heroicon-o-plus-circle')
                    ->form([
                        TextInput::make('count')
                            ->label('Number of Items')
                            ->numeric()
                            ->required()
                            ->default(5)
                            ->minValue(1)
                            ->maxValue(100),
                    ])
                    ->action(function (array $data) {
                        $count = (int) $data['count'];
                        $productId = $this->getOwnerRecord()->id;

                        for ($i = 0; $i < $count; $i++) {
                            InventoryItem::create(['product_id' => $productId]);
                        }

                        Notification::make()
                            ->title("Generated {$count} items with QR codes")
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                Action::make('viewQr')
                    ->label('QR')
                    ->icon('heroicon-o-qr-code')
                    ->color('gray')
                    ->url(fn ($record) => $record->qr_code_url, shouldOpenInNewTab: true)
                    ->visible(fn ($record) => $record->qr_code_path),
                Action::make('markSold')
                    ->label('Sell')
                    ->icon('heroicon-o-check-circle')
                    ->color('warning')
                    ->visible(fn ($record) => in_array($record->status, ['in_stock', 'reserved']))
                    ->form([
                        TextInput::make('sold_to_name')->label('Customer Name')->required(),
                        TextInput::make('sold_to_phone')->label('Phone')->required()->tel(),
                        TextInput::make('sold_price')->label('Price (Rs)')->required()->numeric()->prefix('Rs'),
                    ])
                    ->action(function (InventoryItem $record, array $data) {
                        $record->markAsSold($data['sold_to_name'], $data['sold_to_phone'], $data['sold_price']);
                        Notification::make()->title('Marked as sold')->success()->send();
                    }),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('printQrCodes')
                        ->label('Print QR Codes')
                        ->icon('heroicon-o-printer')
                        ->action(function (Collection $records) {
                            $pdf = (new QrPdfGenerator())->generate($records);
                            return response()->streamDownload(
                                fn () => print($pdf->output()),
                                'qr-codes-batch.pdf'
                            );
                        })
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
