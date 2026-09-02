<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestimonialResource\Pages;
use App\Models\Testimonial;
use App\Support\StaffAccess;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static string | \UnitEnum | null $navigationGroup = 'Content';
    protected static ?int $navigationSort = 7;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('location')->placeholder('e.g. F-7, Islamabad'),
            TextInput::make('initials')->maxLength(5)->placeholder('e.g. AK'),
            Textarea::make('text')->required()->rows(4)->label('Review Text'),
            Select::make('stars')->options([5 => '5 Stars', 4 => '4 Stars', 3 => '3 Stars', 2 => '2 Stars', 1 => '1 Star'])->default(5),
            Select::make('status')
                ->options([
                    Testimonial::STATUS_PENDING => 'Pending',
                    Testimonial::STATUS_APPROVED => 'Approved',
                    Testimonial::STATUS_REJECTED => 'Rejected',
                ])
                ->default(Testimonial::STATUS_APPROVED)
                ->required(),
            TextInput::make('sort_order')->numeric()->default(0),
            Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->reorderable('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('location'),
                Tables\Columns\TextColumn::make('stars')->badge()->color('warning'),
                Tables\Columns\TextColumn::make('text')->limit(50)->wrap(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Testimonial::STATUS_PENDING => 'warning',
                        Testimonial::STATUS_APPROVED => 'success',
                        Testimonial::STATUS_REJECTED => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Visible'),
                Tables\Columns\TextColumn::make('created_at')->dateTime('M j, Y')->sortable()->label('Submitted'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        Testimonial::STATUS_PENDING => 'Pending',
                        Testimonial::STATUS_APPROVED => 'Approved',
                        Testimonial::STATUS_REJECTED => 'Rejected',
                    ]),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Testimonial $record): bool => $record->status !== Testimonial::STATUS_APPROVED && StaffAccess::can('testimonials', StaffAccess::ACTION_EDIT))
                    ->action(fn (Testimonial $record) => $record->update(['status' => Testimonial::STATUS_APPROVED])),
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Testimonial $record): bool => $record->status !== Testimonial::STATUS_REJECTED && StaffAccess::can('testimonials', StaffAccess::ACTION_EDIT))
                    ->requiresConfirmation()
                    ->action(fn (Testimonial $record) => $record->update(['status' => Testimonial::STATUS_REJECTED])),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('approve')
                        ->label('Approve selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn (): bool => StaffAccess::can('testimonials', StaffAccess::ACTION_EDIT))
                        ->action(fn (Collection $records) => $records->each->update(['status' => Testimonial::STATUS_APPROVED])),
                    BulkAction::make('reject')
                        ->label('Reject selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn (): bool => StaffAccess::can('testimonials', StaffAccess::ACTION_EDIT))
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update(['status' => Testimonial::STATUS_REJECTED])),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTestimonials::route('/'),
            'create' => Pages\CreateTestimonial::route('/create'),
            'edit' => Pages\EditTestimonial::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Testimonial::where('status', Testimonial::STATUS_PENDING)->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
