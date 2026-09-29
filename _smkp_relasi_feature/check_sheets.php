<?php
require 'C:/laragon/www/audit/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
$file = "C:/laragon/www/audit/Matriks_Hubungan_Antar_Elemen_SMKP.xlsx";
$spreadsheet = IOFactory::load($file);
$sheets = $spreadsheet->getSheetNames();
echo "Sheets in file: " . implode(", ", $sheets) . PHP_EOL;
