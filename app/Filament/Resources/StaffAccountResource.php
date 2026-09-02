<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StaffAccountResource\Pages;
use App\Models\User;
use App\Support\StaffAccess;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class StaffAccountResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';

    protected static string | \UnitEnum | null $navigationGroup = 'System';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Staff Accounts';

    protected static ?string $modelLabel = 'Staff Account';

    protected static ?string $pluralModelLabel = 'Staff Accounts';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Account Details')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->tel()
                            ->maxLength(50),
                        TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->autocomplete('new-password')
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->maxLength(255),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->onColor('success')
                            ->offColor('danger'),
                    ])
                    ->columns(2),
                Section::make('Feature Permissions')
                    ->description('Give only the features and actions this staff member should use.')
                    ->schema([
                        CheckboxList::make('staff_permissions')
                            ->label('')
                            ->options(StaffAccess::permissionOptions())
                            ->columns(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->placeholder('-')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('staff_permissions')
                    ->label('Permissions')
                    ->formatStateUsing(function ($state): string {
                        if (is_string($state)) {
                            $decoded = json_decode($state, true);
                            $state = is_array($decoded) ? $decoded : [];
                        }

                        return number_format(count(is_array($state) ? $state : [])) . ' assigned';
                    })
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('role', 'staff');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStaffAccounts::route('/'),
            'create' => Pages\CreateStaffAccount::route('/create'),
            'edit' => Pages\EditStaffAccount::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }
}
