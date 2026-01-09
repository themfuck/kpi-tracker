<?php

namespace App\Filament\Widgets;

use App\Models\Host;
use App\Services\KpiCalculatorService;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class HostRankingTable extends BaseWidget
{
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $kpiService = new KpiCalculatorService();
        $rankings = $kpiService->getHostRankings();
        
        // Get top 8 host IDs
        $topHostIds = $rankings->take(8)->pluck('host.id')->toArray();

        return $table
            ->heading('🏆 Ranking Host (Top 8)')
            ->query(
                Host::query()->whereIn('id', $topHostIds)
            )
            ->columns([
                Tables\Columns\TextColumn::make('rank')
                    ->label('Rank')
                    ->state(function ($record) use ($rankings) {
                        $rank = $rankings->search(function ($item) use ($record) {
                            return $item['host']->id === $record->id;
                        }) + 1;
                        
                        return match($rank) {
                            1 => '🥇',
                            2 => '🥈',
                            3 => '🥉',
                            default => "#$rank"
                        };
                    })
                    ->size('lg')
                    ->weight('bold'),
                
                Tables\Columns\ImageColumn::make('photo_path')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(url('/images/default-avatar.png')),
                
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Host')
                    ->searchable()
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('score')
                    ->label('Score')
                    ->state(function ($record) use ($rankings) {
                        $ranking = $rankings->firstWhere('host.id', $record->id);
                        return $ranking ? number_format($ranking['score'], 2) : '0.00';
                    })
                    ->badge()
                    ->color(function ($record) use ($rankings) {
                        $ranking = $rankings->firstWhere('host.id', $record->id);
                        if (!$ranking) return 'gray';
                        
                        $score = $ranking['score'];
                        if ($score >= 100) return 'success';
                        if ($score >= 80) return 'warning';
                        return 'danger';
                    }),
                
                Tables\Columns\TextColumn::make('total_gmv')
                    ->label('Total GMV')
                    ->state(function ($record) use ($rankings) {
                        $ranking = $rankings->firstWhere('host.id', $record->id);
                        return $ranking ? $ranking['total_gmv'] : 0;
                    })
                    ->money('IDR'),
                
                Tables\Columns\TextColumn::make('gmv_per_hour')
                    ->label('GMV/Jam')
                    ->state(function ($record) use ($rankings) {
                        $ranking = $rankings->firstWhere('host.id', $record->id);
                        return $ranking ? 'Rp ' . number_format($ranking['gmv_per_hour'], 0, ',', '.') : '-';
                    }),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('Status KPI')
                    ->state(function ($record) use ($rankings) {
                        $ranking = $rankings->firstWhere('host.id', $record->id);
                        return $ranking ? $ranking['status'] : 'N/A';
                    })
                    ->badge()
                    ->color(function ($record) use ($rankings) {
                        $ranking = $rankings->firstWhere('host.id', $record->id);
                        if (!$ranking) return 'gray';
                        
                        return match($ranking['status']) {
                            'OK' => 'success',
                            'WARNING' => 'warning',
                            'DROP' => 'danger',
                            default => 'gray'
                        };
                    }),
            ])
            ->paginated(false);
    }
}
