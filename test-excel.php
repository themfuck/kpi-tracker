#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\KpiCalculatorService;
use App\Exports\HostRankingExport;
use Maatwebsite\Excel\Facades\Excel;

echo "Testing Excel Export...\n\n";

try {
    $kpiService = new KpiCalculatorService();
    $rankings = $kpiService->getHostRankings()->toArray();

    echo "Total rankings: " . count($rankings) . "\n";

    $filename = 'test-ranking-' . date('Y-m-d-His') . '.xlsx';
    $filepath = storage_path('app/' . $filename);

    echo "Attempting to create file: $filepath\n";
    
    Excel::store(new HostRankingExport($rankings), $filename);

    if (file_exists($filepath)) {
        $size = filesize($filepath);
        $type = mime_content_type($filepath);
        
        echo "\n✅ File created successfully!\n";
        echo "Path: $filepath\n";
        echo "Size: " . number_format($size) . " bytes\n";
        echo "MIME Type: $type\n";
        
        // Check if it's a valid XLSX file
        if (strpos($type, 'zip') !== false || strpos($type, 'spreadsheet') !== false) {
            echo "\n✅ File is a valid XLSX format!\n";
        } else {
            echo "\n⚠️ Warning: File might not be in XLSX format\n";
        }
        
        // Try to read first few bytes
        $handle = fopen($filepath, 'rb');
        $header = fread($handle, 4);
        fclose($handle);
        
        // XLSX files are ZIP files, should start with PK
        if (substr($header, 0, 2) === 'PK') {
            echo "✅ File header confirms ZIP/XLSX format (starts with 'PK')\n";
        }
        
    } else {
        echo "\n❌ File was not created!\n";
        echo "Checking storage directory permissions...\n";
        $storageDir = storage_path('app');
        echo "Storage dir: $storageDir\n";
        echo "Writable: " . (is_writable($storageDir) ? 'Yes' : 'No') . "\n";
    }
} catch (\Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\nTest completed.\n";
