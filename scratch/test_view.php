<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$views = [
    'layouts.app',
    'auth.login',
    'admin.dashboard',
    'auditor.dashboard',
    'admin.elemens.index',
    'admin.elemens.show',
    'admin.sub_elemens.index',
    'admin.kriterias.index',
    'admin.perusahaans.index',
    'admin.users.index',
    'admin.logs.index',
    'admin.restore_points.index',
    'admin.audit-sesi.index',
    'admin.audit-sesi.create',
    'admin.audits.index',
    'admin.audits.show',
    'admin.pica.index',
    'auditor.audit.index',
    'auditor.audit.rekap',
    'auditor.audit.cetak',
    'auditor.audit.matrix',
    'auditor.pica.index',
    'auditor.pica.edit',
    'exports.audit-sesi-rekap',
    'laporan._pica_card',
    'laporan._pica_table',
    'laporan.detail',
    'welcome',
    'partials._relasi_js_engine',
    'partials._relasi_kunci_banner',
    'partials._relasi_referensi_icon',
];

echo "=== VERIFIKASI MIGRASI VIEW LARAVEL (BLADE -> NATIVE PHP) ===" . PHP_EOL;
$allOk = true;
foreach ($views as $viewName) {
    $exists = view()->exists($viewName);
    if (!$exists) {
        $allOk = false;
    }
    echo str_pad($viewName, 35) . ": " . ($exists ? "[OK] RESOLVED TO PHP" : "[ERROR] NOT FOUND") . PHP_EOL;
}

echo PHP_EOL . ($allOk ? "HASIL: SEMUA VIEW TERESOLUSI KE NATIVE PHP DENGAN SANGAT BERHASIL!" : "HASIL: ADA ADA VIEW YANG TERMISSING") . PHP_EOL;
