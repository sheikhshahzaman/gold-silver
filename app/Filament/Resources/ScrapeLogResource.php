<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScrapeLogResource\Pages;
use App\Models\ScrapeLog;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ScrapeLogResource extends Resource
{
    protected static ?string $model = ScrapeLog::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-server-stack';

    protected static string | \UnitEnum | null $navigationGroup = 'Monitoring';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Scrape Logs';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Scrape Log Details')
                    ->schema([
                        TextEntry::make('source')
                            ->label('Source'),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'success' => 'success',
                                'failed' => 'danger',
                                'partial' => 'warning',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                        TextEntry::make('error_message')
                            ->label('Error Message')
                            ->placeholder('No error')
                            ->columnSpanFull(),
                        TextEntry::make('response_time_ms')
                            ->label('Response Time')
                            ->suffix(' ms')
                            ->placeholder('-'),
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('source')
                    ->label('Source')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'success' => 'success',
                        'failed' => 'danger',
                        'partial' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('error_message')
                    ->label('Error Message')
                    ->limit(50)
                    ->tooltip(fn ($record): ?string => $record->error_message)
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('response_time_ms')
                    ->label('Response Time')
                    ->suffix(' ms')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'success' => 'Success',
                        'failed' => 'Failed',
                        'partial' => 'Partial',
                    ]),
                Tables\Filters\SelectFilter::make('source')
                    ->options(fn () => ScrapeLog::query()
                        ->distinct()
                        ->pluck('source', 'source')
                        ->toArray()),
            ])
            ->actions([
                ViewAction::make(),
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
            'index' => Pages\ListScrapeLogs::route('/'),
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
