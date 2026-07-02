<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PriceMarginResource\Pages;
use App\Models\PriceMargin;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PriceMarginResource extends Resource
{
    protected static ?string $model = PriceMargin::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static string | \UnitEnum | null $navigationGroup = 'Price Management';

    protected static ?int $navigationSort = 0;

    protected static ?string $navigationLabel = 'Gold & Silver Prices';

    public static function getModelLabel(): string
    {
        return 'Gold & Silver Price';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Gold & Silver Prices';
    }

    /**
     * Human-readable label for a row: "Gold — 24K" or "Silver — 1 Tola".
     * Used by both the list badge and the edit form heading.
     */
    public static function itemLabel(?PriceMargin $record): string
    {
        if (!$record) {
            return '—';
        }
        $metal = ucfirst($record->metal);
        if ($record->karat) {
            return $metal . ' — ' . strtoupper($record->karat);
        }
        if ($record->unit) {
            return $metal . ' — ' . self::unitLabel($record->unit);
        }
        return $metal;
    }

    /**
     * Friendly label for a unit key.
     */
    public static function unitLabel(string $unit): string
    {
        return match ($unit) {
            'tola'    => '1 Tola',
            '10_tola' => '10 Tola',
            'kg'      => '1 KG',
            '10_gram' => '10 Gram',
            '5_gram'  => '5 Gram',
            'gram'    => '1 Gram',
            default   => ucwords(str_replace('_', ' ', $unit)),
        };
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Price Settings')
                    ->description('Type the final price customers will see — no market rate or margin involved. Gold prices are entered per tola and auto-converted to other units. Silver prices are entered per the selected weight and used exactly as typed.')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Placeholder::make('item')
                            ->label('You are editing')
                            ->content(fn ($record) => self::itemLabel($record))
                            ->columnSpanFull(),
                        TextInput::make('manual_buy_price')
                            ->label(fn ($record) => 'Buy Price (' . self::marginUnitLabel($record) . ')')
                            ->helperText('What you pay the customer when they sell to you')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('Rs')
                            ->required()
                            ->step(0.01),
                        TextInput::make('manual_sell_price')
                            ->label(fn ($record) => 'Sell Price (' . self::marginUnitLabel($record) . ')')
                            ->helperText('What the customer pays when they buy from you')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('Rs')
                            ->required()
                            ->step(0.01),
                    ])
                    ->columns(2),
            ]);
    }

    /**
     * Text shown after "Buy/Sell Price" in the form: "per Tola" for gold,
     * "per 10 Gram" / "per 1 KG" / ... for silver (matches the row's unit).
     */
    public static function marginUnitLabel(?PriceMargin $record): string
    {
        if ($record && $record->metal === 'silver' && $record->unit) {
            return 'per ' . self::unitLabel($record->unit);
        }
        return 'per Tola';
    }

    public static function table(Table $table): Table
    {
        return $table
            // Gold first (sorted by karat), then silver per unit in display order.
            ->defaultSort('id')
            ->columns([
                Tables\Columns\TextColumn::make('item')
                    ->label('Item')
                    ->badge()
                    ->state(fn ($record) => self::itemLabel($record))
                    ->color(fn ($record): string => $record->metal === 'gold' ? 'warning' : 'gray')
                    ->sortable(['metal', 'karat', 'unit']),
                Tables\Columns\TextColumn::make('manual_buy_price')
                    ->label('Buy Price')
                    ->money('PKR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('manual_sell_price')
                    ->label('Sell Price')
                    ->money('PKR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Last Updated By')
                    ->default('System')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
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
            'index' => Pages\ListPriceMargins::route('/'),
            'edit' => Pages\EditPriceMargin::route('/{record}/edit'),
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
