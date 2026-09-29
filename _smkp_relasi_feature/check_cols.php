<?php
require 'C:/laragon/www/audit/vendor/autoload.php';
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
