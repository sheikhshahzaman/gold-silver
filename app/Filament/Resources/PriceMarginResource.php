<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PriceMarginResource\Pages;
use App\Models\PriceMargin;
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

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Price Margins';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Margin Settings')
                    ->description('Current Market Price + Your Margin = Displayed Price. Margins are stored per tola and automatically converted for other units.')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        TextInput::make('metal')
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('karat')
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('buy_margin')
                            ->label('Buy Margin (per Tola)')
                            ->numeric()
                            ->prefix('Rs')
                            ->required()
                            ->step(0.01)
                            ->minValue(0),
                        TextInput::make('sell_margin')
                            ->label('Sell Margin (per Tola)')
                            ->numeric()
                            ->prefix('Rs')
                            ->required()
                            ->step(0.01)
                            ->minValue(0),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
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
                Tables\Columns\TextColumn::make('karat')
                    ->label('Karat')
                    ->sortable(),
                Tables\Columns\TextColumn::make('buy_margin')
                    ->label('Buy Margin (Rs)')
                    ->money('PKR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sell_margin')
                    ->label('Sell Margin (Rs)')
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
