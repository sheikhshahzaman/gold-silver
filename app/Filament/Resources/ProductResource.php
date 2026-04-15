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
                    TextInput::make('price_key')
                        ->placeholder('e.g. gold.24k.tola')
                        ->helperText('Format: metal.karat.unit (e.g. gold.24k.tola, silver.kg)')
                        ->visible(fn ($get) => $get('price_type') === 'live'),
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
                Tables\Columns\TextColumn::make('fixed_price')->money('PKR')->label('Price')
                    ->visible(fn ($record) => $record?->price_type === 'fixed'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
