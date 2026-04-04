<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactResource\Pages;
use App\Models\Contact;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-envelope';

    protected static string | \UnitEnum | null $navigationGroup = 'Communications';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Contact Messages';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Contact Details')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Name'),
                        TextEntry::make('email')
                            ->label('Email'),
                        TextEntry::make('phone')
                            ->label('Phone')
                            ->placeholder('-'),
                        TextEntry::make('subject')
                            ->label('Subject'),
                        TextEntry::make('created_at')
                            ->label('Received At')
                            ->dateTime(),
                        IconEntry::make('is_read')
                            ->label('Read')
                            ->boolean(),
                    ])
                    ->columns(2),
                Section::make('Message')
                    ->schema([
                        TextEntry::make('message')
                            ->label('')
                            ->prose()
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
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('subject')
                    ->label('Subject')
                    ->limit(40)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_read')
                    ->label('Read')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_read')
                    ->label('Read Status')
                    ->trueLabel('Read')
                    ->falseLabel('Unread'),
            ])
            ->actions([
                ViewAction::make(),
                Action::make('markAsRead')
                    ->label('Mark as Read')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (Contact $record): bool => ! $record->is_read)
                    ->action(function (Contact $record): void {
                        $record->update(['is_read' => true]);
                    }),
                Action::make('markAsUnread')
                    ->label('Mark as Unread')
                    ->icon('heroicon-o-envelope')
                    ->color('warning')
                    ->visible(fn (Contact $record): bool => $record->is_read)
                    ->action(function (Contact $record): void {
                        $record->update(['is_read' => false]);
                    }),
            ])
            ->bulkActions([
                BulkAction::make('markAsRead')
                    ->label('Mark as Read')
                    ->icon('heroicon-o-check')
                    ->action(fn ($records) => $records->each->update(['is_read' => true])),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContacts::route('/'),
            'view' => Pages\ViewContact::route('/{record}'),
        ];
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
