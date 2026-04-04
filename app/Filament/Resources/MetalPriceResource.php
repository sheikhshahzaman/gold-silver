<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MetalPriceResource\Pages;
use App\Models\MetalPrice;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class MetalPriceResource extends Resource
{
    protected static ?string $model = MetalPrice::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chart-bar';

    protected static string | \UnitEnum | null $navigationGroup = 'Price Management';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Metal Prices';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Price Details')
                    ->schema([
                        TextEntry::make('metal')
                            ->label('Metal')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'gold' => 'warning',
                                'silver' => 'gray',
                                default => 'primary',
                            })
                            ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                        TextEntry::make('type')
                            ->label('Type')
                            ->formatStateUsing(fn (?string $state): string => $state ? ucwords(str_replace('_', ' ', $state)) : '-'),
                        TextEntry::make('karat')
                            ->label('Karat')
                            ->placeholder('-'),
                        TextEntry::make('unit')
                            ->label('Unit')
                            ->formatStateUsing(fn (?string $state): string => $state ? ucwords(str_replace('_', ' ', $state)) : '-'),
                        TextEntry::make('buy_price')
                            ->label('Buy Price')
                            ->money('PKR'),
                        TextEntry::make('sell_price')
                            ->label('Sell Price')
                            ->money('PKR'),
                        TextEntry::make('high')
                            ->label('High')
                            ->money('PKR')
                            ->placeholder('-'),
                        TextEntry::make('low')
                            ->label('Low')
                            ->money('PKR')
                            ->placeholder('-'),
                        TextEntry::make('currency')
                            ->label('Currency'),
                        TextEntry::make('source')
                            ->label('Source'),
                        TextEntry::make('fetched_at')
                            ->label('Fetched At')
                            ->dateTime(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('fetched_at', 'desc')
            ->columns([
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
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->formatStateUsing(fn (?string $state): string => $state ? ucfirst(str_replace('_', ' ', $state)) : '-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('karat')
                    ->label('Karat')
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit')
                    ->label('Unit')
                    ->formatStateUsing(fn (?string $state): string => $state ? ucfirst(str_replace('_', ' ', $state)) : '-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('buy_price')
                    ->label('Buy Price')
                    ->money('PKR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sell_price')
                    ->label('Sell Price')
                    ->money('PKR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('source')
                    ->label('Source')
                    ->sortable(),
                Tables\Columns\TextColumn::make('fetched_at')
                    ->label('Fetched At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('metal')
                    ->options([
                        'gold' => 'Gold',
                        'silver' => 'Silver',
                    ]),
                Tables\Filters\SelectFilter::make('karat')
                    ->options(fn () => MetalPrice::query()
                        ->distinct()
                        ->whereNotNull('karat')
                        ->pluck('karat', 'karat')
                        ->toArray()),
            ])
            ->actions([
                ViewAction::make(),
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
            'index' => Pages\ListMetalPrices::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
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
