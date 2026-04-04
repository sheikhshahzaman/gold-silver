<?php

namespace App\Filament\Widgets;

use App\Models\MetalPrice;
use App\Models\Order;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PriceDashboardWidget extends StatsOverviewWidget
{
    protected static ?int $sort = -2;

    protected ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        // Get latest Gold 24K price per tola
        $gold24k = MetalPrice::query()
            ->where('metal', 'gold')
            ->where('karat', '24k')
            ->where('unit', 'tola')
            ->orderByDesc('fetched_at')
            ->first();

        // Get latest Silver price per tola
        $silver = MetalPrice::query()
            ->where('metal', 'silver')
            ->where('unit', 'tola')
            ->orderByDesc('fetched_at')
            ->first();

        // Today's orders count
        $todayOrders = Order::whereDate('created_at', today())->count();

        // Pending payments count
        $pendingPayments = Payment::where('status', 'pending')->count();

        return [
            Stat::make('Gold 24K (per Tola)', $gold24k ? 'Rs ' . number_format((float) $gold24k->sell_price, 0) : 'N/A')
                ->description($gold24k ? 'Fetched: ' . $gold24k->fetched_at->diffForHumans() : 'No data')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->icon('heroicon-o-fire'),

            Stat::make('Silver (per Tola)', $silver ? 'Rs ' . number_format((float) $silver->sell_price, 0) : 'N/A')
                ->description($silver ? 'Fetched: ' . $silver->fetched_at->diffForHumans() : 'No data')
                ->descriptionIcon('heroicon-m-clock')
                ->color('gray')
                ->icon('heroicon-o-sparkles'),

            Stat::make("Today's Orders", $todayOrders)
                ->description('Orders placed today')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('success')
                ->icon('heroicon-o-shopping-cart'),

            Stat::make('Pending Payments', $pendingPayments)
                ->description('Awaiting verification')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($pendingPayments > 0 ? 'danger' : 'success')
                ->icon('heroicon-o-banknotes'),
        ];
    }
}
