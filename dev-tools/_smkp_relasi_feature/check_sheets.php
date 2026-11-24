<?php

if (php_sapi_name() !== 'cli' || (function_exists('app') && app()->isProduction())) {
    http_response_code(403);
    exit('Forbidden: Script hanya dapat dijalankan melalui CLI di environment non-production.');
}

require __DIR__ . '/../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
$file = "C:/laragon/www/audit/Matriks_Hubungan_Antar_Elemen_SMKP.xlsx";
$spreadsheet = IOFactory::load($file);
$sheets = $spreadsheet->getSheetNames();
echo "Sheets in file: " . implode(", ", $sheets) . PHP_EOL;
