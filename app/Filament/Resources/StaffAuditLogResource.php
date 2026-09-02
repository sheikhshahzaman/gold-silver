<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StaffAuditLogResource\Pages;
use App\Models\StaffAuditLog;
use App\Models\User;
use App\Support\StaffAccess;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class StaffAuditLogResource extends Resource
{
    protected static ?string $model = StaffAuditLog::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string | \UnitEnum | null $navigationGroup = 'System';

    protected static ?int $navigationSort = 11;

    protected static ?string $navigationLabel = 'Staff Audit Logs';

    protected static ?string $modelLabel = 'Staff Audit Log';

    protected static ?string $pluralModelLabel = 'Staff Audit Logs';

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Action')
                    ->schema([
                        TextEntry::make('created_at')->dateTime(),
                        TextEntry::make('staff_name')->label('User'),
                        TextEntry::make('staff_email')->label('Email'),
                        TextEntry::make('feature')->formatStateUsing(fn (?string $state): string => StaffAccess::features()[$state] ?? ucfirst((string) $state)),
                        TextEntry::make('action')->badge(),
                        TextEntry::make('auditable_label')->label('Record')->placeholder('-'),
                    ])
                    ->columns(3),
                Section::make('Request')
                    ->schema([
                        TextEntry::make('ip_address')->label('IP Address')->placeholder('-'),
                        TextEntry::make('user_agent')->label('User Agent')->placeholder('-')->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Changes')
                    ->schema([
                        KeyValueEntry::make('changes')->label('')->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('staff_name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('staff_email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('feature')
                    ->formatStateUsing(fn (?string $state): string => StaffAccess::features()[$state] ?? ucfirst((string) $state))
                    ->badge()
                    ->color('warning')
                    ->sortable(),
                Tables\Columns\TextColumn::make('action')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        StaffAccess::ACTION_CREATE => 'success',
                        StaffAccess::ACTION_EDIT => 'info',
                        StaffAccess::ACTION_DELETE => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('auditable_label')
                    ->label('Record')
                    ->searchable()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP')
                    ->placeholder('-'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('feature')
                    ->options(StaffAccess::features()),
                Tables\Filters\SelectFilter::make('action')
                    ->options(StaffAccess::actions()),
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('User')
                    ->options(fn () => User::query()
                        ->whereIn('role', ['admin', 'staff'])
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->toArray()),
            ])
            ->actions([
                ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStaffAuditLogs::route('/'),
            'view' => Pages\ViewStaffAuditLog::route('/{record}'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canView(Model $record): bool
    {
        return auth()->user()?->isAdmin() ?? false;
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
