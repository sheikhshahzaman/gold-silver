<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryItemResource\Pages;
use App\Models\InventoryItem;
use App\Models\Product;
use App\Services\QrPdfGenerator;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class InventoryItemResource extends Resource
{
    protected static ?string $model = InventoryItem::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-qr-code';
    protected static string | \UnitEnum | null $navigationGroup = 'Inventory';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Inventory Items';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Item Details')
                ->schema([
                    Select::make('product_id')
                        ->label('Product')
                        ->options(Product::active()->ordered()->pluck('name', 'id'))
                        ->searchable()
                        ->required(),
                    TextInput::make('serial_number')
                        ->maxLength(50)
                        ->helperText('Auto-generated if empty')
                        ->disabled(fn ($operation) => $operation === 'edit'),
                    TextInput::make('actual_weight')
                        ->numeric()
                        ->suffix('grams')
                        ->placeholder('e.g. 11.6638'),
                    TextInput::make('purity_tested')
                        ->placeholder('e.g. 999.9, 916')
                        ->maxLength(20),
                    Textarea::make('notes')
                        ->rows(2)
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Status')
                ->schema([
                    Select::make('status')
                        ->options([
                            'in_stock' => 'In Stock',
                            'reserved' => 'Reserved',
                            'sold' => 'Sold',
                            'returned' => 'Returned',
                        ])
                        ->default('in_stock')
                        ->required(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
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
                    ->sortable()
                    ->copyable()
                    ->weight('bold')
                    ->size('sm'),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('product.metal')
                    ->label('Metal')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'in_stock' => 'success',
                        'reserved' => 'info',
                        'sold' => 'warning',
                        'returned' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('sold_to_name')
                    ->label('Sold To')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('sold_price')
                    ->money('PKR')
                    ->label('Sale Price')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('scan_count')
                    ->label('Scans')
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'in_stock' => 'In Stock',
                        'reserved' => 'Reserved',
                        'sold' => 'Sold',
                        'returned' => 'Returned',
                    ]),
                Tables\Filters\SelectFilter::make('product_id')
                    ->label('Product')
                    ->options(Product::pluck('name', 'id'))
                    ->searchable(),
            ])
            ->actions([
                Action::make('viewQr')
                    ->label('QR')
                    ->icon('heroicon-o-qr-code')
                    ->color('gray')
                    ->url(fn ($record) => $record->qr_code_url, shouldOpenInNewTab: true)
                    ->visible(fn ($record) => $record->qr_code_path),

                Action::make('markSold')
                    ->label('Mark Sold')
                    ->icon('heroicon-o-check-circle')
                    ->color('warning')
                    ->visible(fn ($record) => $record->status === 'in_stock' || $record->status === 'reserved')
                    ->form([
                        TextInput::make('sold_to_name')
                            ->label('Customer Name')
                            ->required(),
                        TextInput::make('sold_to_phone')
                            ->label('Customer Phone')
                            ->required()
                            ->tel(),
                        TextInput::make('sold_price')
                            ->label('Sale Price (Rs)')
                            ->required()
                            ->numeric()
                            ->prefix('Rs'),
                    ])
                    ->action(function (InventoryItem $record, array $data) {
                        $record->markAsSold($data['sold_to_name'], $data['sold_to_phone'], $data['sold_price']);
                        \Filament\Notifications\Notification::make()
                            ->title('Item marked as sold')
                            ->success()
                            ->send();
                    }),

                ViewAction::make(),
                EditAction::make(),
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
                                'qr-codes-' . now()->format('Y-m-d-His') . '.pdf'
                            );
                        })
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Item Information')
                ->schema([
                    TextEntry::make('serial_number')->weight('bold')->copyable(),
                    TextEntry::make('product.name')->label('Product'),
                    TextEntry::make('product.metal')->label('Metal')->badge(),
                    TextEntry::make('product.karat')->label('Karat'),
                    TextEntry::make('status')->badge()->color(fn ($state) => match ($state) {
                        'in_stock' => 'success', 'reserved' => 'info',
                        'sold' => 'warning', 'returned' => 'danger', default => 'gray',
                    }),
                    TextEntry::make('actual_weight')->suffix(' grams')->placeholder('-'),
                    TextEntry::make('purity_tested')->placeholder('-'),
                    TextEntry::make('notes')->placeholder('-')->columnSpanFull(),
                ])
                ->columns(3),

            Section::make('QR Code & Verification')
                ->schema([
                    TextEntry::make('qr_code_url')
                        ->label('QR Code')
                        ->url(fn ($record) => $record->qr_code_url, shouldOpenInNewTab: true)
                        ->formatStateUsing(fn ($state) => $state ? 'View QR Code' : 'Not generated')
                        ->color('primary'),
                    TextEntry::make('verification_url')
                        ->label('Verification URL')
                        ->copyable()
                        ->url(fn ($record) => $record->verification_url, shouldOpenInNewTab: true),
                    TextEntry::make('verification_token')
                        ->label('Token')
                        ->copyable()
                        ->size('sm'),
                ])
                ->columns(2),

            Section::make('Sale Details')
                ->schema([
                    TextEntry::make('sold_to_name')->placeholder('-'),
                    TextEntry::make('sold_to_phone')->placeholder('-'),
                    TextEntry::make('sold_price')->money('PKR')->placeholder('-'),
                    TextEntry::make('sold_at')->dateTime()->placeholder('-'),
                ])
                ->columns(2)
                ->visible(fn ($record) => $record->status === 'sold'),

            Section::make('Scan Analytics')
                ->schema([
                    TextEntry::make('scan_count')->label('Total Scans'),
                    TextEntry::make('first_scanned_at')->dateTime()->placeholder('Never scanned'),
                    TextEntry::make('claimed_by_phone')->label('Claimed By')->placeholder('Not claimed'),
                ])
                ->columns(3),

            Section::make('Recent Scans')
                ->schema([
                    RepeatableEntry::make('scanLogs')
                        ->label('')
                        ->schema([
                            TextEntry::make('scanned_at')->dateTime(),
                            TextEntry::make('ip_address'),
                            TextEntry::make('user_agent')->limit(60),
                        ])
                        ->columns(3),
                ])
                ->collapsible(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventoryItems::route('/'),
            'create' => Pages\CreateInventoryItem::route('/create'),
            'view' => Pages\ViewInventoryItem::route('/{record}'),
            'edit' => Pages\EditInventoryItem::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'in_stock')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}
