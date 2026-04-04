<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-shopping-cart';

    protected static string | \UnitEnum | null $navigationGroup = 'Orders & Payments';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Orders';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Customer Information')
                    ->schema([
                        TextInput::make('customer_name')
                            ->label('Customer Name')
                            ->disabled(),
                        TextInput::make('customer_phone')
                            ->label('Customer Phone')
                            ->disabled(),
                        TextInput::make('customer_email')
                            ->label('Customer Email')
                            ->disabled(),
                    ])
                    ->columns(3),
                Section::make('Order Details')
                    ->schema([
                        TextInput::make('order_number')
                            ->disabled(),
                        TextInput::make('metal')
                            ->disabled(),
                        TextInput::make('karat')
                            ->disabled(),
                        TextInput::make('quantity')
                            ->disabled(),
                        TextInput::make('unit')
                            ->disabled(),
                        TextInput::make('type')
                            ->disabled(),
                        TextInput::make('locked_price')
                            ->disabled()
                            ->prefix('Rs'),
                        TextInput::make('total_amount')
                            ->disabled()
                            ->prefix('Rs'),
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'awaiting_verification' => 'Awaiting Verification',
                                'confirmed' => 'Confirmed',
                                'processing' => 'Processing',
                                'delivered' => 'Delivered',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Customer Information')
                    ->schema([
                        TextEntry::make('customer_name')
                            ->label('Name')
                            ->placeholder('-'),
                        TextEntry::make('customer_phone')
                            ->label('Phone')
                            ->placeholder('-'),
                        TextEntry::make('customer_email')
                            ->label('Email')
                            ->placeholder('-'),
                    ])
                    ->columns(3),
                Section::make('Order Information')
                    ->schema([
                        TextEntry::make('order_number')
                            ->label('Order Number'),
                        TextEntry::make('user.name')
                            ->label('Registered User')
                            ->default('Guest'),
                        TextEntry::make('metal')
                            ->label('Metal')
                            ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                        TextEntry::make('karat')
                            ->label('Karat'),
                        TextEntry::make('quantity')
                            ->label('Quantity'),
                        TextEntry::make('unit')
                            ->label('Unit')
                            ->formatStateUsing(fn (?string $state): string => $state ? ucfirst(str_replace('_', ' ', $state)) : '-'),
                        TextEntry::make('type')
                            ->label('Type')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'buy' => 'success',
                                'sell' => 'danger',
                                default => 'primary',
                            })
                            ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                        TextEntry::make('locked_price')
                            ->label('Locked Price')
                            ->money('PKR'),
                        TextEntry::make('total_amount')
                            ->label('Total Amount')
                            ->money('PKR'),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'awaiting_verification' => 'info',
                                'confirmed' => 'success',
                                'processing' => 'primary',
                                'delivered' => 'success',
                                'cancelled' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state))),
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->dateTime(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Order Number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Customer')
                    ->searchable()
                    ->placeholder('Guest')
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer_phone')
                    ->label('Phone')
                    ->searchable()
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('metal')
                    ->label('Metal')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'gold' => 'warning',
                        'silver' => 'gray',
                        default => 'primary',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('karat')
                    ->label('Karat')
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Quantity')
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit')
                    ->label('Unit')
                    ->formatStateUsing(fn (?string $state): string => $state ? ucfirst(str_replace('_', ' ', $state)) : '-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'buy' => 'success',
                        'sell' => 'danger',
                        default => 'primary',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total Amount')
                    ->money('PKR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'awaiting_verification' => 'info',
                        'confirmed' => 'success',
                        'processing' => 'primary',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state)))
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'awaiting_verification' => 'Awaiting Verification',
                        'confirmed' => 'Confirmed',
                        'processing' => 'Processing',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('metal')
                    ->options([
                        'gold' => 'Gold',
                        'silver' => 'Silver',
                    ]),
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'buy' => 'Buy',
                        'sell' => 'Sell',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }
}
