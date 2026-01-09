<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class HostRankingExport implements 
    FromCollection, 
    WithHeadings, 
    WithMapping, 
    WithStyles, 
    ShouldAutoSize,
    WithColumnFormatting
{
    protected $rankings;

    public function __construct($rankings)
    {
        $this->rankings = collect($rankings);
    }

    public function collection()
    {
        return $this->rankings;
    }

    public function headings(): array
    {
        return [
            'Rank',
            'Nama Host',
            'Role',
            'Score',
            'Status KPI',
            'Total GMV',
            'GMV per Jam',
            'Total Jam Live',
            'Conversion Rate',
            'AOV',
            'Like per Menit',
        ];
    }

    public function map($ranking): array
    {
        static $rank = 0;
        $rank++;

        return [
            $rank,
            $ranking['host']->name,
            $ranking['host']->role,
            $ranking['score'],
            $ranking['status'],
            $ranking['total_gmv'],
            $ranking['gmv_per_hour'],
            $ranking['total_hours'],
            $ranking['conversion_rate'],
            $ranking['aov'],
            $ranking['likes_per_minute'],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_NUMBER_00, // Score
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Total GMV
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // GMV per Jam
            'H' => NumberFormat::FORMAT_NUMBER_00, // Total Jam
            'I' => NumberFormat::FORMAT_PERCENTAGE_00, // Conversion Rate
            'J' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // AOV
            'K' => NumberFormat::FORMAT_NUMBER_00, // Like per Menit
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E8F0'],
                ],
            ],
        ];
    }
}
