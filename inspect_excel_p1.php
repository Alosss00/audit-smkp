<?php

require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$file = __DIR__ . '/TT-MGT-FRS-026B Formulir Kriteria Audit SMKP_Rev.xlsx';
$spreadsheet = IOFactory::load($file);
$sheet = $spreadsheet->getActiveSheet();

echo "=== ROWS 1 to 52 ===\n";
for ($r = 1; $r <= 52; $r++) {
    $cells = [];
    for ($col = 'A'; $col <= 'R'; $col++) {
        $val = $sheet->getCell($col . $r)->getValue();
        if ($val !== null && $val !== '') {
            $calc = '';
            if (is_string($val) && strpos($val, '=') === 0) {
                try {
                    $calcVal = $sheet->getCell($col . $r)->getCalculatedValue();
                    $calc = " [calc: $calcVal]";
                } catch (\Throwable $e) {
                    $calc = " [calc err]";
                }
            }
            $cells[] = "$col: " . (is_string($val) ? str_replace("\n", " ", $val) : $val) . $calc;
        }
    }
    if (!empty($cells)) {
        echo "Row " . sprintf("%3d", $r) . " | " . implode(" | ", $cells) . "\n";
    }
}
