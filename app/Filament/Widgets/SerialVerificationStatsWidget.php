<?php

namespace App\Filament\Widgets;

use App\Models\SerialVerificationAttempt;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SerialVerificationStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 0;

    protected ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $today = SerialVerificationAttempt::whereDate('attempted_at', today())->count();
        $foundToday = SerialVerificationAttempt::whereDate('attempted_at', today())->where('found', true)->count();
        $totalAll = SerialVerificationAttempt::count();
        $failedAll = SerialVerificationAttempt::where('found', false)->count();

        return [
            Stat::make('Verifications Today', $today)
                ->description($foundToday . ' verified / ' . max(0, $today - $foundToday) . ' failed')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color($today > 0 ? 'success' : 'gray')
                ->icon('heroicon-o-finger-print')
                ->chart(self::chartData(true)),

            Stat::make('Total Verifications', number_format($totalAll))
                ->description('All-time serial lookups')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('info')
                ->icon('heroicon-o-shield-check'),

            Stat::make('Failed Attempts', number_format($failedAll))
                ->description($failedAll > 0 ? 'Potential counterfeits / typos' : 'No failures yet')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($failedAll > 0 ? 'warning' : 'gray')
                ->icon('heroicon-o-x-circle'),
        ];
    }

    private static function chartData(bool $foundOnly): array
    {
        $days = collect(range(6, 0))->map(fn ($d) => now()->subDays($d)->toDateString());
        $counts = $days->map(function ($date) use ($foundOnly) {
            $q = SerialVerificationAttempt::whereDate('attempted_at', $date);
            if ($foundOnly) $q->where('found', true);
            return $q->count();
        });
        return $counts->toArray();
    }
}
