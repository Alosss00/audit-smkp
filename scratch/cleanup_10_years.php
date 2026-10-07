<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AuditSesi;
use App\Models\AuditDetail;
use App\Models\Pica;

// Real original sessions are IDs 1, 2, 3, 4. Sample sessions are IDs > 4.
$sampleSesis = AuditSesi::whereNotIn('id', [1, 2, 3, 4])->get();

if ($sampleSesis->isEmpty()) {
    echo "Tidak ada data sampel audit yang ditemukan untuk dihapus.\n";
    exit;
}

$count = 0;
foreach ($sampleSesis as $sesi) {
    // Delete associated details
    $detailIds = AuditDetail::where('audit_sesi_id', $sesi->id)->pluck('id');
    if ($detailIds->isNotEmpty()) {
        Pica::whereIn('audit_detail_id', $detailIds)->delete();
        AuditDetail::where('audit_sesi_id', $sesi->id)->delete();
    }
    
    // Force delete session
    $sesi->forceDelete();
    $count++;
}

echo "Berhasil menghapus total {$count} sesi audit sampel 10 tahun beserta seluruh detailnya!\n";
