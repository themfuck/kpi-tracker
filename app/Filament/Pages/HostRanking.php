<?php

namespace App\Filament\Pages;

use App\Services\KpiCalculatorService;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\HostRankingExport;

class HostRanking extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    
    protected static ?string $navigationLabel = 'Ranking Host';
    
    protected static ?string $title = 'Ranking Host';
    
    protected static ?string $navigationGroup = 'Laporan';

    protected static string $view = 'filament.pages.host-ranking';
    
    public $month;
    public $year;
    public $rankings = [];
    
    public function mount(): void
    {
        $this->month = now()->month;
        $this->year = now()->year;
        $this->loadRankings();
    }
    
    public function loadRankings(): void
    {
        $kpiService = new KpiCalculatorService();
        $this->rankings = $kpiService->getHostRankings($this->month, $this->year)->toArray();
    }
    
    public function updatedMonth(): void
    {
        $this->loadRankings();
    }
    
    public function updatedYear(): void
    {
        $this->loadRankings();
    }
    
    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Export Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    return Excel::download(
                        new HostRankingExport($this->rankings),
                        'ranking-host-' . $this->year . '-' . str_pad($this->month, 2, '0', STR_PAD_LEFT) . '.xlsx'
                    );
                }),
        ];
    }
}
