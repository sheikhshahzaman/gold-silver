<?php

namespace App\Filament\Widgets;

use App\Models\InventoryItem;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InventoryStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = -1;

    protected ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $inStock = InventoryItem::where('status', 'in_stock')->count();

        $estimatedValue = InventoryItem::where('status', 'in_stock')
            ->join('products', 'inventory_items.product_id', '=', 'products.id')
            ->whereNotNull('products.fixed_price')
            ->sum('products.fixed_price');

        $soldToday = InventoryItem::where('status', 'sold')
            ->whereDate('sold_at', today())
            ->count();

        $revenueToday = InventoryItem::where('status', 'sold')
            ->whereDate('sold_at', today())
            ->sum('sold_price');

        $soldThisWeek = InventoryItem::where('status', 'sold')
            ->where('sold_at', '>=', now()->startOfWeek())
            ->count();

        $revenueThisWeek = InventoryItem::where('status', 'sold')
            ->where('sold_at', '>=', now()->startOfWeek())
            ->sum('sold_price');

        return [
            Stat::make('In Stock', $inStock)
                ->description($estimatedValue > 0 ? 'Est. value: Rs ' . number_format($estimatedValue) : 'Items available')
                ->descriptionIcon('heroicon-m-cube')
                ->color('success')
                ->icon('heroicon-o-cube'),

            Stat::make('Sold Today', $soldToday)
                ->description($revenueToday > 0 ? 'Revenue: Rs ' . number_format($revenueToday) : 'No sales yet')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($soldToday > 0 ? 'warning' : 'gray')
                ->icon('heroicon-o-shopping-bag'),

            Stat::make('Sold This Week', $soldThisWeek)
                ->description($revenueThisWeek > 0 ? 'Revenue: Rs ' . number_format($revenueThisWeek) : 'No sales yet')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($soldThisWeek > 0 ? 'info' : 'gray')
                ->icon('heroicon-o-chart-bar'),
        ];
    }
}
