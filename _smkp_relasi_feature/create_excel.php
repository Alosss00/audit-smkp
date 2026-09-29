<?php
require 'C:/laragon/www/audit/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();

// --- Sheet 1: Hubungan Kunci ---
$sheet1 = $spreadsheet->getActiveSheet();
$sheet1->setTitle('Hubungan Kunci');
$sheet1->setCellValue('A1', 'Kode Kriteria Asal');
$sheet1->setCellValue('B1', 'Kode Kriteria Tujuan');
$sheet1->setCellValue('C1', 'Deskripsi Keterkaitan');

$dataKunci = [
    ['I.1', 'II.2.1', 'Kritikal: Periksa konsistensi Dokumen Sasaran dan Program di elemen II.2.1.'],
    ['I.2', 'II.2.2', 'Periksa apakah perencanaan terintegrasi dengan RKAB.'],
    ['II.1', 'III.1', 'Jika komitmen kurang, cek implementasi di lapangan.'],
    ['II.2.1', 'IV.1', 'Sasaran yang tidak tercapai harus ditinjau ulang.']
];

$row = 2;
foreach ($dataKunci as $item) {
    $sheet1->setCellValue('A'.$row, $item[0]);
    $sheet1->setCellValue('B'.$row, $item[1]);
    $sheet1->setCellValue('C'.$row, $item[2]);
    $row++;
}

// --- Sheet 2: Matriks Keterkaitan ---
$sheet2 = $spreadsheet->createSheet();
$sheet2->setTitle('Matriks Keterkaitan');
$sheet2->setCellValue('A1', 'Kode Kriteria Asal');
$sheet2->setCellValue('B1', 'Kode Kriteria Tujuan');
$sheet2->setCellValue('C1', 'Deskripsi Keterkaitan');
$sheet2->setCellValue('D1', 'Fokus Bukti Audit (Abaikan)');

$dataRef = [
    ['I.1', 'I.2', 'Kebijakan diturunkan jadi perencanaan.', 'Dokumen RPJMN'],
    ['I.1', 'I.3', 'Kebijakan dikomunikasikan ke pekerja.', 'Daftar Hadir'],
    ['I.1', 'II.1', 'Komitmen dari manajemen.', 'SK Direksi'],
    ['I.2', 'I.4', 'Perencanaan terkait anggaran.', 'RKAB'],
    ['II.2.1', 'II.3', 'Sasaran berkaitan dengan kompetensi.', 'Sertifikat']
];

$row = 2;
foreach ($dataRef as $item) {
    $sheet2->setCellValue('A'.$row, $item[0]);
    $sheet2->setCellValue('B'.$row, $item[1]);
    $sheet2->setCellValue('C'.$row, $item[2]);
    $sheet2->setCellValue('D'.$row, $item[3]);
    $row++;
}

// Save the file
$dir = 'C:/laragon/www/audit/storage/app/seeder';
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}
$path = $dir . '/Matriks_Hubungan_Antar_Elemen_SMKP.xlsx';

$writer = new Xlsx($spreadsheet);
$writer->save($path);

echo "Excel file created at: " . $path . PHP_EOL;
