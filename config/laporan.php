<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Gaya Cetak Bawaan Laporan Audit Internal SMKP
    |--------------------------------------------------------------------------
    | Merujuk pedoman format huruf laporan (Arial 18pt tebal kapital untuk
    | judul, Arial 14pt untuk nama & tanggal, Arial 12pt rata kiri-kanan untuk
    | isi). Dapat disesuaikan melalui konfigurasi tanpa mengubah view.
    */
    'font_family'       => env('LAPORAN_FONT_FAMILY', 'Arial, Helvetica, sans-serif'),
    'font_size_judul'   => env('LAPORAN_FONT_SIZE_JUDUL', '18pt'),
    'font_size_subjudul'=> env('LAPORAN_FONT_SIZE_SUBJUDUL', '14pt'),
    'font_size_isi'     => env('LAPORAN_FONT_SIZE_ISI', '12pt'),
    'margin_halaman'    => env('LAPORAN_MARGIN_HALAMAN', '20mm 18mm'),

    /*
    |--------------------------------------------------------------------------
    | Skema Kode Temuan Tunggal untuk Seluruh Kategori
    |--------------------------------------------------------------------------
    | Menghasilkan NC-KRT-001, NC-MYR-001, NC-MNR-001.
    */
    'kode_temuan_prefix' => [
        'kritikal' => 'NC-KRT',
        'mayor'    => 'NC-MYR',
        'minor'    => 'NC-MNR',
    ],
    'kode_temuan_padding' => 3,
];
