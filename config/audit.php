<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Kewajiban Kelengkapan PLOR Saat Finalisasi Sesi Audit
    |--------------------------------------------------------------------------
    | Jika false (default), temuan dengan PLOR belum lengkap tetap bisa
    | disimpan/difinalkan dengan menampilkan badge peringatan.
    | Jika true, finalisasi sesi menolak temuan yang belum melengkapi PLOR.
    */
    'plor_wajib_saat_finalisasi' => (bool) env('AUDIT_PLOR_WAJIB_SAAT_FINALISASI', false),

    /*
    |--------------------------------------------------------------------------
    | Nomor Dokumen Standar Formulir Perangkat Audit
    |--------------------------------------------------------------------------
    */
    'no_dokumen_ofi'        => env('AUDIT_NO_DOKUMEN_OFI', 'TT-MGT-FRS-026G'),
    'no_dokumen_kesesuaian' => env('AUDIT_NO_DOKUMEN_KESESUAIAN', 'TT-MGT-FRS-026E'),
];
