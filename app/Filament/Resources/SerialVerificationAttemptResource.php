<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SerialVerificationAttemptResource\Pages;
use App\Models\SerialVerificationAttempt;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class SerialVerificationAttemptResource extends Resource
{
    protected static ?string $model = SerialVerificationAttempt::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-finger-print';
    protected static string | \UnitEnum | null $navigationGroup = 'Inventory';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Serial Verifications';
    protected static ?string $modelLabel = 'Verification Attempt';
    protected static ?string $pluralModelLabel = 'Verification Attempts';

    public static function getNavigationBadge(): ?string
    {
        $today = static::getModel()::whereDate('attempted_at', today())->count();
        return $today > 0 ? (string) $today : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('attempted_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('attempted_at')
                    ->label('When')
                    ->dateTime('M j, H:i')
                    ->description(fn ($record) => $record->attempted_at->diffForHumans())
                    ->sortable(),

                Tables\Columns\TextColumn::make('entered_serial')
                    ->label('Serial Entered')
                    ->searchable()
                    ->fontFamily('mono')
                    ->copyable()
                    ->weight('semibold'),

                Tables\Columns\IconColumn::make('found')
                    ->label('Result')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('inventoryItem.product.name')
                    ->label('Matched Product')
                    ->placeholder('—')
                    ->limit(30),

                Tables\Columns\TextColumn::make('status_at_lookup')
                    ->label('Status')
                    ->badge()
                    ->color(fn (?string $state) => match ($state) {
                        'in_stock' => 'success',
                        'reserved' => 'info',
                        'sold' => 'warning',
                        'returned' => 'danger',
                        default => 'gray',
                    })
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Customer')
                    ->description(fn ($record) => $record->customer_phone)
                    ->placeholder('—')
                    ->searchable(),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('found')
                    ->label('Match Status')
                    ->trueLabel('Verified')
                    ->falseLabel('Not Found')
                    ->placeholder('All'),
                Tables\Filters\Filter::make('today')
                    ->label('Today only')
                    ->query(fn ($query) => $query->whereDate('attempted_at', today())),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Attempt')
                ->schema([
                    TextEntry::make('entered_serial')->label('Serial Entered')->copyable(),
                    TextEntry::make('found')->label('Result')->badge()
                        ->color(fn ($state) => $state ? 'success' : 'danger')
                        ->formatStateUsing(fn ($state) => $state ? 'Verified' : 'Not Found'),
                    TextEntry::make('status_at_lookup')->label('Item Status')->placeholder('—'),
                    TextEntry::make('attempted_at')->label('When')->dateTime(),
                ])->columns(2),

            Section::make('Customer')
                ->schema([
                    TextEntry::make('customer_name')->placeholder('Not provided'),
                    TextEntry::make('customer_phone')->placeholder('Not provided'),
                ])->columns(2)
                ->visible(fn ($record) => $record->customer_name || $record->customer_phone),

            Section::make('Matched Item')
                ->schema([
                    TextEntry::make('inventoryItem.serial_number')->label('Serial'),
                    TextEntry::make('inventoryItem.product.name')->label('Product'),
                    TextEntry::make('inventoryItem.verification_token')->label('Token')->copyable(),
                ])->columns(2)
                ->visible(fn ($record) => $record->inventory_item_id),

            Section::make('Technical')
                ->schema([
                    TextEntry::make('ip_address'),
                    TextEntry::make('user_agent')->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSerialVerificationAttempts::route('/'),
            'view' => Pages\ViewSerialVerificationAttempt::route('/{record}'),
        ];
    }
}
