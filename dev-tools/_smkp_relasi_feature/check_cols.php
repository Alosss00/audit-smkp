<?php

if (php_sapi_name() !== 'cli' || (function_exists('app') && app()->isProduction())) {
    http_response_code(403);
    exit('Forbidden: Script hanya dapat dijalankan melalui CLI di environment non-production.');
}

require __DIR__ . '/../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
$file = "C:/laragon/www/audit/Matriks_Hubungan_Antar_Elemen_SMKP.xlsx";
$spreadsheet = IOFactory::load($file);

foreach (['Matriks Keterkaitan', 'Hubungan Kunci'] as $sheetName) {
    echo "--- Sheet: $sheetName ---" . PHP_EOL;
    $sheet = $spreadsheet->getSheetByName($sheetName);
    if ($sheet) {
        $row1 = $sheet->rangeToArray('A1:E1')[0];
        $row2 = $sheet->rangeToArray('A2:E2')[0];
        echo "Header: " . implode(" | ", $row1) . PHP_EOL;
        echo "Row 2 : " . implode(" | ", $row2) . PHP_EOL;
    }
}
