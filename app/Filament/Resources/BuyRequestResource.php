<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BuyRequestResource\Pages;
use App\Models\BuyRequest;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

/**
 * "Request to buy gold/silver" leads, kept in their own sidebar section so they
 * never mix with real orders or contact messages.
 */
class BuyRequestResource extends Resource
{
    protected static ?string $model = BuyRequest::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-hand-raised';

    protected static string | \UnitEnum | null $navigationGroup = 'Buy Requests';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Buy Requests';

    protected static ?string $modelLabel = 'Buy Request';

    protected static ?string $pluralModelLabel = 'Buy Requests';

    /** Unhandled requests are the point of this screen, so badge them. */
    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', BuyRequest::STATUS_NEW)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Customer')
                ->schema([
                    TextInput::make('customer_name')->label('Name')->disabled(),
                    TextInput::make('customer_phone')->label('Phone')->disabled(),
                    TextInput::make('reference')->label('Reference')->disabled(),
                ])
                ->columns(3),

            Section::make('What they asked for')
                ->schema([
                    TextInput::make('metal')
                        ->label('Metal')
                        ->formatStateUsing(fn (?string $state): string => ucfirst((string) $state))
                        ->disabled(),
                    TextInput::make('category')
                        ->label('Category')
                        ->formatStateUsing(fn (?string $state): string => BuyRequest::categoryOptions()[$state] ?? (string) $state)
                        ->disabled(),
                    TextInput::make('selection_label')->label('Selection')->disabled(),
                    TextInput::make('unit_price')->label('Rate')->prefix('Rs')->disabled(),
                    TextInput::make('packaging_charge')->label('Packaging')->prefix('Rs')->disabled(),
                    TextInput::make('total_amount')->label('Estimated Total')->prefix('Rs')->disabled(),
                ])
                ->columns(3),

            Section::make('Follow up')
                ->schema([
                    Select::make('status')
                        ->label('Status')
                        ->options(BuyRequest::statusOptions())
                        ->required(),
                    Textarea::make('admin_notes')
                        ->label('Notes')
                        ->rows(4)
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->label('Reference')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('source')
                    ->label('Placed From')
                    ->badge()
                    ->icon(fn (?string $state): string => $state === 'app'
                        ? 'heroicon-m-device-phone-mobile'
                        : 'heroicon-m-globe-alt')
                    ->color(fn (?string $state): string => $state === 'app' ? 'info' : 'gray')
                    ->formatStateUsing(fn (?string $state): string => BuyRequest::sourceOptions()[$state] ?? 'Website'),
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer_phone')
                    ->label('Phone')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('metal')
                    ->label('Metal')
                    ->badge()
                    ->color(fn (?string $state): string => $state === 'silver' ? 'gray' : 'warning')
                    ->formatStateUsing(fn (?string $state): string => ucfirst((string) $state)),
                Tables\Columns\TextColumn::make('category')
                    ->label('Category')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => BuyRequest::categoryOptions()[$state] ?? (string) $state),
                Tables\Columns\TextColumn::make('selection_label')
                    ->label('Selection')
                    ->wrap(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Estimated Total')
                    ->money('PKR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        BuyRequest::STATUS_NEW => 'warning',
                        BuyRequest::STATUS_CONTACTED => 'info',
                        BuyRequest::STATUS_CLOSED => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => BuyRequest::statusOptions()[$state] ?? 'New')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(BuyRequest::statusOptions()),
                Tables\Filters\SelectFilter::make('metal')->options(['gold' => 'Gold', 'silver' => 'Silver']),
                Tables\Filters\SelectFilter::make('category')->options(BuyRequest::categoryOptions()),
                Tables\Filters\SelectFilter::make('source')
                    ->label('Placed From')
                    ->options(BuyRequest::sourceOptions()),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkAction::make('markContacted')
                    ->label('Mark as contacted')
                    ->icon('heroicon-o-phone')
                    ->requiresConfirmation()
                    ->action(fn (Collection $records) => $records->each->update(['status' => BuyRequest::STATUS_CONTACTED])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBuyRequests::route('/'),
            'view' => Pages\ViewBuyRequest::route('/{record}'),
            'edit' => Pages\EditBuyRequest::route('/{record}/edit'),
        ];
    }
}
