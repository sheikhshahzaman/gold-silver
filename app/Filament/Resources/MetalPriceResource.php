<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MetalPriceResource\Pages;
use App\Models\MetalPrice;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MetalPriceResource extends Resource
{
    protected static ?string $model = MetalPrice::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chart-bar';

    protected static string | \UnitEnum | null $navigationGroup = 'Price Management';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Metal Prices';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function getModelLabel(): string
    {
        return 'Metal Price';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Metal Prices';
    }

    public static function itemLabel(?MetalPrice $record): string
    {
        if (! $record) {
            return '—';
        }

        $metal = ucfirst((string) $record->metal);

        if ($record->type === 'international') {
            return $metal . ' (' . strtoupper($record->metal === 'gold' ? 'XAU/USD' : 'XAG/USD') . ')';
        }

        $parts = array_filter([
            $metal,
            $record->karat ? strtoupper($record->karat) : null,
            $record->unit ? ucwords(str_replace('_', ' ', $record->unit)) : null,
        ]);

        return implode(' — ', $parts) ?: 'Metal Price';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Price Settings')
                    ->description('Update the metal spot bid/buy and ask/sell values shown on website and app.')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Placeholder::make('item')
                            ->label('You are editing')
                            ->content(fn ($record) => self::itemLabel($record))
                            ->columnSpanFull(),
                        TextInput::make('buy_price')
                            ->label(fn ($record) => 'Bid / Buy Price (' . ($record?->currency ?: 'USD') . ')')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->step(0.0001),
                        TextInput::make('sell_price')
                            ->label(fn ($record) => 'Ask / Sell Price (' . ($record?->currency ?: 'USD') . ')')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->step(0.0001),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->whereIn('id', MetalPrice::query()
                    ->selectRaw('MAX(id)')
                    ->groupBy('metal', 'type', 'karat', 'unit'))
                ->orderByRaw("CASE metal WHEN 'gold' THEN 0 WHEN 'silver' THEN 1 ELSE 9 END")
                ->orderByRaw("CASE type WHEN 'international' THEN 0 ELSE 1 END")
                ->orderBy('id'))
            ->columns([
                Tables\Columns\TextColumn::make('item')
                    ->label('Item')
                    ->badge()
                    ->state(fn ($record) => self::itemLabel($record))
                    ->color(fn ($record): string => match ($record->metal) {
                        'gold' => 'warning',
                        'silver' => 'gray',
                        default => 'primary',
                    })
                    ->sortable(['metal', 'type', 'karat', 'unit']),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->formatStateUsing(fn (?string $state): string => $state ? ucfirst(str_replace('_', ' ', $state)) : '-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('karat')
                    ->label('Karat')
                    ->formatStateUsing(fn (?string $state): string => $state ?: '—')
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit')
                    ->label('Unit')
                    ->formatStateUsing(fn (?string $state): string => $state ? ucfirst(str_replace('_', ' ', $state)) : '-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('buy_price')
                    ->label('Bid / Buy')
                    ->formatStateUsing(fn ($state, MetalPrice $record): string => self::formatMoney($state, $record->currency))
                    ->sortable(),
                Tables\Columns\TextColumn::make('sell_price')
                    ->label('Ask / Sell')
                    ->formatStateUsing(fn ($state, MetalPrice $record): string => self::formatMoney($state, $record->currency))
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency')
                    ->label('Currency')
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
                EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function formatMoney(mixed $value, ?string $currency): string
    {
        if (! is_numeric($value)) {
            return '—';
        }

        $amount = number_format((float) $value, strtoupper((string) $currency) === 'USD' ? 4 : 2);

        return strtoupper((string) $currency) === 'USD' ? '$' . $amount : 'PKR ' . $amount;
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMetalPrices::route('/'),
            'edit' => Pages\EditMetalPrice::route('/{record}/edit'),
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
