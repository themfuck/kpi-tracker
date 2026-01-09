<?php

namespace App\Exports;

use App\Models\LiveSession;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class LiveSessionExport implements 
    FromQuery, 
    WithHeadings, 
    WithMapping, 
    WithStyles, 
    ShouldAutoSize,
    WithColumnFormatting
{
    protected $query;

    public function __construct($query = null)
    {
        $this->query = $query ?? LiveSession::query();
    }

    public function query()
    {
        return $this->query->with('host');
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Host',
            'Role',
            'Jam Live',
            'GMV',
            'Orders',
            'Viewers',
            'Likes',
            'Errors',
            'GMV per Jam',
            'Conversion Rate',
            'AOV',
        ];
    }

    public function map($session): array
    {
        $gmvPerHour = $session->hours_live > 0 ? $session->gmv / $session->hours_live : 0;
        $conversionRate = $session->viewers > 0 ? $session->orders / $session->viewers : 0;
        $aov = $session->orders > 0 ? $session->gmv / $session->orders : 0;

        return [
            $session->date->format('Y-m-d'),
            $session->host->name,
            $session->host->role,
            $session->hours_live,
            $session->gmv,
            $session->orders,
            $session->viewers,
            $session->likes,
            $session->errors,
            $gmvPerHour,
            $conversionRate,
            $aov,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_NUMBER_00, // Jam Live
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // GMV
            'F' => NumberFormat::FORMAT_NUMBER, // Orders
            'G' => NumberFormat::FORMAT_NUMBER, // Viewers
            'H' => NumberFormat::FORMAT_NUMBER, // Likes
            'I' => NumberFormat::FORMAT_NUMBER, // Errors
            'J' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // GMV per Jam
            'K' => NumberFormat::FORMAT_PERCENTAGE_00, // Conversion Rate
            'L' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // AOV
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
