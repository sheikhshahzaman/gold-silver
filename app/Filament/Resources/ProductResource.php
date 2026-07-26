<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers\InventoryItemsRelationManager;
use App\Models\Product;
use App\Models\ProductCategory;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-shopping-bag';
    protected static string | \UnitEnum | null $navigationGroup = 'Content';
    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Basic Info')
                ->schema([
                    TextInput::make('name')->required()->maxLength(255),
                    TextInput::make('slug')->maxLength(255)->helperText('Auto-generated if empty'),
                    Textarea::make('description')->rows(3)->columnSpanFull(),
                    TextInput::make('weight')->placeholder('e.g. 1 Tola · 11.66g · 999.9 Pure'),
                ])
                ->columns(2),

            Section::make('Category & Type')
                ->schema([
                    Select::make('category_id')
                        ->label('Category')
                        ->options(ProductCategory::active()->ordered()->pluck('name', 'id'))
                        ->searchable()
                        ->required()
                        ->createOptionForm([
                            TextInput::make('name')->required(),
                            TextInput::make('icon')->placeholder('e.g. 💍'),
                        ])
                        ->createOptionUsing(function (array $data) {
                            return ProductCategory::create($data)->id;
                        }),
                    Select::make('metal')
                        ->options(['gold' => 'Gold', 'silver' => 'Silver'])
                        ->default('gold'),
                    TextInput::make('karat')->placeholder('e.g. 24K, 22K'),
                ])
                ->columns(3),

            Section::make('Images')
                ->schema([
                    FileUpload::make('image')
                        ->label('Main Image')
                        ->image()
                        ->disk('public')
                        ->directory('products')
                        ->nullable()
                        ->imageEditor()
                        ->columnSpanFull(),
                    FileUpload::make('gallery')
                        ->label('Gallery Images')
                        ->image()
                        ->multiple()
                        ->reorderable()
                        ->disk('public')
                        ->directory('products/gallery')
                        ->nullable()
                        ->columnSpanFull(),
                ]),

            Section::make('Pricing')
                ->schema([
                    Select::make('price_type')
                        ->options([
                            'live' => 'Live Price (from market rates)',
                            'fixed' => 'Fixed Price (set manually)',
                            'custom_quote' => 'Custom Quote (enquiry only)',
                        ])
                        ->default('live')
                        ->reactive(),
                    TextInput::make('fixed_price')
                        ->numeric()
                        ->prefix('Rs')
                        ->nullable()
                        ->visible(fn ($get) => $get('price_type') === 'fixed'),
                    Select::make('price_key')
                        ->label('Market rate')
                        ->options(static::priceKeyOptions())
                        ->searchable()
                        ->helperText('Which metal, karat and weight this product is priced from. Weights without their own board rate are derived from the tola rate.')
                        ->visible(fn ($get) => $get('price_type') === 'live')
                        ->required(fn ($get) => $get('price_type') === 'live'),
                    TextInput::make('packaging_charge')
                        ->label('Packaging Charge (per unit)')
                        ->helperText('Shown to the customer as a separate line when buying this product.')
                        ->numeric()
                        ->minValue(0)
                        ->default(0)
                        ->prefix('Rs'),
                ])
                ->columns(2),

            Section::make('Discount')
                ->schema([
                    Select::make('discount_type')
                        ->options([
                            'percent' => 'Percentage (%)',
                            'flat' => 'Flat Amount (Rs)',
                        ])
                        ->placeholder('No discount')
                        ->nullable()
                        ->reactive(),
                    TextInput::make('discount_value')
                        ->numeric()
                        ->nullable()
                        ->suffix(fn ($get) => $get('discount_type') === 'percent' ? '%' : 'Rs')
                        ->placeholder(fn ($get) => $get('discount_type') === 'percent' ? 'e.g. 10' : 'e.g. 500')
                        ->visible(fn ($get) => $get('discount_type') !== null),
                    DateTimePicker::make('discount_starts_at')
                        ->label('Starts At')
                        ->nullable()
                        ->visible(fn ($get) => $get('discount_type') !== null),
                    DateTimePicker::make('discount_ends_at')
                        ->label('Ends At')
                        ->nullable()
                        ->visible(fn ($get) => $get('discount_type') !== null),
                ])
                ->columns(2)
                ->collapsible(),

            Section::make('Display')
                ->schema([
                    TextInput::make('sort_order')->numeric()->default(0),
                    Toggle::make('is_active')->default(true)->label('Active (visible on website)'),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\ImageColumn::make('image')->circular()->label(''),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('productCategory.name')->label('Category')->badge()->color('gray'),
                Tables\Columns\TextColumn::make('metal')->badge(),
                Tables\Columns\TextColumn::make('karat')->label('Karat'),
                Tables\Columns\TextColumn::make('price_type')->badge()->color(fn ($state) => match ($state) {
                    'live' => 'success', 'fixed' => 'info', default => 'warning',
                }),
                // One price column for every price_type: live rows compute the
                // current live price, fixed rows show fixed_price. A per-record
                // visible() closure can't do this — column visibility is
                // table-wide in Filament, so $record is always null there.
                Tables\Columns\TextColumn::make('live_price_preview')->money('PKR')->label('Current Price')
                    ->getStateUsing(fn ($record) => match ($record->price_type) {
                        'live' => app(\App\Services\Cart::class)->unitPriceFor($record),
                        'fixed' => $record->fixed_price,
                        default => null,
                    })
                    ->placeholder(fn ($record) => $record?->price_type === 'live' ? 'No price key set' : '—'),
                Tables\Columns\TextColumn::make('packaging_charge')->money('PKR')->label('Packaging')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock_count')
                    ->label('Stock')
                    ->getStateUsing(fn ($record) => $record->stock_count)
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'gray'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('metal')->options(['gold' => 'Gold', 'silver' => 'Silver']),
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Category')
                    ->options(ProductCategory::pluck('name', 'id')),
                Tables\Filters\SelectFilter::make('price_type')->options([
                    'live' => 'Live Price', 'fixed' => 'Fixed Price', 'custom_quote' => 'Custom Quote',
                ]),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array
    {
        return [
            InventoryItemsRelationManager::class,
        ];
    }

    /**
     * Every valid live price key, grouped for the picker. Weights map to
     * Cart::UNIT_GRAMS — anything without an exact board row is derived
     * from the tola rate at lookup time.
     */
    public static function priceKeyOptions(): array
    {
        $unitLabels = [
            'gram' => '1 Gram',
            '2.5_gram' => '2.5 Gram',
            '5_gram' => '5 Gram',
            '10_gram' => '10 Gram',
            '50_gram' => '50 Gram',
            '100_gram' => '100 Gram',
            'ounce' => '1 Ounce (31.1g)',
            'half_tola' => 'Half Tola',
            'tola' => '1 Tola',
            '2_tola' => '2 Tola',
            '5_tola' => '5 Tola',
            '10_tola' => '10 Tola',
            'kg' => '1 KG',
        ];

        $karats = ['24k' => '24K', 'rawa' => 'Rawa', '22k' => '22K', '21k' => '21K', '18k' => '18K'];

        $options = [];
        foreach ($karats as $karatKey => $karatLabel) {
            $group = [];
            foreach ($unitLabels as $unitKey => $unitLabel) {
                if ($unitKey === 'kg') continue;
                $group["gold.{$karatKey}.{$unitKey}"] = "Gold {$karatLabel} — {$unitLabel}";
            }
            $options["Gold {$karatLabel}"] = $group;
        }

        $silverUnitLabels = [
            '10_tola_qr' => '10 Tola (QR Packaging)',
            '10_tola' => '10 Tola (999)',
            'kg' => '1 KG',
            '5_tola' => '5 Tola (Bar)',
            'tola' => '1 Tola (Bar)',
        ];

        $silver = [];
        foreach ($silverUnitLabels as $unitKey => $unitLabel) {
            $silver["silver.{$unitKey}"] = "Silver — {$unitLabel}";
        }
        $options['Silver'] = $silver;

        return $options;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
