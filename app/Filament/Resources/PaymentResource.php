<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-banknotes';

    protected static string | \UnitEnum | null $navigationGroup = 'Orders & Payments';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Payments';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Payment Details')
                    ->schema([
                        TextInput::make('order.order_number')
                            ->label('Order Number')
                            ->disabled(),
                        TextInput::make('method')
                            ->disabled(),
                        TextInput::make('amount')
                            ->disabled()
                            ->prefix('Rs'),
                        TextInput::make('reference_number')
                            ->disabled(),
                        TextInput::make('transaction_id')
                            ->disabled(),
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'verified' => 'Verified',
                                'rejected' => 'Rejected',
                                'refunded' => 'Refunded',
                            ])
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Payment Information')
                    ->schema([
                        TextEntry::make('order.order_number')
                            ->label('Order Number'),
                        TextEntry::make('method')
                            ->label('Payment Method')
                            ->formatStateUsing(fn (?string $state): string => $state ? ucwords(str_replace('_', ' ', $state)) : '-'),
                        TextEntry::make('amount')
                            ->label('Amount')
                            ->money('PKR'),
                        TextEntry::make('reference_number')
                            ->label('Reference Number')
                            ->placeholder('-'),
                        TextEntry::make('transaction_id')
                            ->label('Transaction ID')
                            ->placeholder('-'),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'verified' => 'success',
                                'rejected' => 'danger',
                                'refunded' => 'info',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state))),
                        TextEntry::make('verifier.name')
                            ->label('Verified By')
                            ->placeholder('-'),
                        TextEntry::make('verified_at')
                            ->label('Verified At')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),
                    ])
                    ->columns(2),
                Section::make('Payment Proof')
                    ->schema([
                        ImageEntry::make('proof_image')
                            ->label('Proof Image')
                            ->disk('public')
                            ->visibility('public'),
                    ])
                    ->visible(fn ($record): bool => filled($record->proof_image)),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('order.order_number')
                    ->label('Order Number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('method')
                    ->label('Method')
                    ->formatStateUsing(fn (?string $state): string => $state ? ucwords(str_replace('_', ' ', $state)) : '-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('PKR')
                    ->sortable(),
                Tables\Columns\ImageColumn::make('proof_image')
                    ->label('Proof')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(fn () => null)
                    ->visibility('public'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'verified' => 'success',
                        'rejected' => 'danger',
                        'refunded' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state)))
                    ->sortable(),
                Tables\Columns\TextColumn::make('verifier.name')
                    ->label('Verified By')
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'verified' => 'Verified',
                        'rejected' => 'Rejected',
                        'refunded' => 'Refunded',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('verify')
                    ->label('Verify Payment')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Verify Payment')
                    ->modalDescription('Are you sure you want to verify this payment?')
                    ->visible(fn (Payment $record): bool => $record->status === 'pending')
                    ->action(function (Payment $record): void {
                        $record->update([
                            'status' => 'verified',
                            'verified_by' => Auth::id(),
                            'verified_at' => now(),
                        ]);
                        $record->order->update(['status' => 'confirmed']);
                    }),
                Action::make('reject')
                    ->label('Reject Payment')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Reject Payment')
                    ->modalDescription('Are you sure you want to reject this payment?')
                    ->visible(fn (Payment $record): bool => $record->status === 'pending')
                    ->action(function (Payment $record): void {
                        $record->update([
                            'status' => 'rejected',
                        ]);
                        $record->order->update(['status' => 'cancelled']);
                    }),
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
            'index' => Pages\ListPayments::route('/'),
            'view' => Pages\ViewPayment::route('/{record}'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
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
}
