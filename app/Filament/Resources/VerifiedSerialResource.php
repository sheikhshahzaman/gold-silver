<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VerifiedSerialResource\Pages;
use App\Models\VerifiedSerial;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class VerifiedSerialResource extends Resource
{
    protected static ?string $model = VerifiedSerial::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-check-badge';
    protected static string | \UnitEnum | null $navigationGroup = 'Inventory';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Verification Serial';
    protected static ?string $modelLabel = 'Verification Serial';
    protected static ?string $pluralModelLabel = 'Verification Serial';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Master Verification Serial')
                ->description('This is the single serial number customers use on /verify to confirm authenticity. Print this same value on every box.')
                ->schema([
                    TextInput::make('serial_number')
                        ->label('Serial Number')
                        ->required()
                        ->maxLength(80)
                        ->placeholder('IBE-MASTER-2026')
                        ->helperText('Stored in uppercase. Customers must enter this exactly to verify.')
                        ->unique(ignoreRecord: true),
                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true)
                        ->helperText('Turn off to temporarily disable verification.'),
                    TextInput::make('label')
                        ->label('Internal Label')
                        ->maxLength(150)
                        ->placeholder('e.g. 2026 season master serial')
                        ->columnSpanFull(),
                ])->columns(2),

            Section::make('Product Info (shown to customer on success)')
                ->description('Optional — appears on the success screen when the serial matches.')
                ->schema([
                    TextInput::make('product_name')->label('Product Name')->maxLength(150),
                    Select::make('metal')->options(['gold' => 'Gold', 'silver' => 'Silver'])->placeholder('—'),
                    TextInput::make('karat')->placeholder('e.g. 24K, 22K'),
                    TextInput::make('weight')->numeric()->suffix('grams'),
                    TextInput::make('purity')->placeholder('e.g. 999.9'),
                    Textarea::make('notes')->rows(2)->columnSpanFull(),
                ])->columns(2)->collapsible()->collapsed(),
        ]);
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageVerifiedSerial::route('/'),
        ];
    }
}
