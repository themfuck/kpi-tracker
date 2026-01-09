<?php

namespace App\Filament\Widgets;

use App\Services\KpiCalculatorService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class KpiStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $kpiService = new KpiCalculatorService();
        $stats = $kpiService->getMonthlyStats();

        return [
            Stat::make('Total GMV Bulan Ini', 'Rp ' . number_format($stats['total_gmv'], 0, ',', '.'))
                ->description('Target: Rp 1.300.000.000')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color($stats['achievement_percentage'] >= 100 ? 'success' : ($stats['achievement_percentage'] >= 80 ? 'warning' : 'danger')),
            
            Stat::make('Achievement', number_format($stats['achievement_percentage'], 2) . '%')
                ->description($stats['achievement_percentage'] >= 100 ? 'Target tercapai!' : 'Dari target bulanan')
                ->descriptionIcon($stats['achievement_percentage'] >= 100 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($stats['achievement_percentage'] >= 100 ? 'success' : ($stats['achievement_percentage'] >= 80 ? 'warning' : 'danger')),
            
            Stat::make('GMV per Jam (Rata-rata)', 'Rp ' . number_format($stats['avg_gmv_per_hour'], 0, ',', '.'))
                ->description('Target: Rp 2.700.000/jam')
                ->descriptionIcon('heroicon-m-clock')
                ->color($stats['avg_gmv_per_hour'] >= 2700000 ? 'success' : 'warning'),
        ];
    }
}
