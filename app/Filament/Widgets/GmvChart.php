<?php

namespace App\Filament\Widgets;

use App\Services\KpiCalculatorService;
use Filament\Widgets\ChartWidget;

class GmvChart extends ChartWidget
{
    protected static ?string $heading = 'GMV Harian (Bulan Ini)';
    
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $kpiService = new KpiCalculatorService();
        $dailyGmv = $kpiService->getDailyGmv();

        $labels = [];
        $data = [];

        foreach ($dailyGmv as $date => $gmv) {
            $labels[] = date('d M', strtotime($date));
            $data[] = $gmv;
        }

        return [
            'datasets' => [
                [
                    'label' => 'GMV',
                    'data' => $data,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
