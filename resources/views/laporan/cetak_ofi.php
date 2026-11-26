<?php
$namaPerusahaan = $sesi->perusahaan->nama_perusahaan ?? $sesi->area_audit;
$thnPeriode = $sesi->tahun_periode ?? ($sesi->tanggal_mulai ? $sesi->tanggal_mulai->format('Y') : date('Y'));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Formulir OFI <?php echo e($namaPerusahaan); ?> - <?php echo e($noDokumen); ?></title>
    <link href="<?php echo asset('vendor/bootstrap/bootstrap.min.css'); ?>" rel="stylesheet">
    <style>
        @page { size: A4 portrait; margin: 14mm; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 9.5pt; color: #111827; background: #f1f5f9; }
        .sheet { background: #fff; max-width: 210mm; margin: 16px auto; padding: 14mm; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .doc-header { display: flex; justify-content: space-between; align-items: center; border: 1.5px solid #0f172a; padding: 10px 14px; margin-bottom: 14px; }
        .formal-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .formal-table th, .formal-table td { border: 1px solid #334155; padding: 6px 8px; vertical-align: top; }
        .formal-table th { background: #e2e8f0; text-align: center; font-weight: 700; }
        .no-print { max-width: 210mm; margin: 14px auto 0 auto; display: flex; justify-content: flex-end; }
        @media print {
            body { background: #fff; }
            .sheet { margin: 0; padding: 0; box-shadow: none; max-width: none; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button type="button" class="btn btn-dark btn-sm px-3" id="btnPrintOfi">Cetak / Simpan PDF</button>
    </div>
    <div class="sheet">
        <div class="doc-header">
            <div>
                <div style="font-size: 11pt; font-weight: 800;">FORMULIR REKOMENDASI &amp; PELUANG UNTUK PERBAIKAN (OFI)</div>
                <div style="font-size: 9pt; color: #334155;"><?php echo e($namaPerusahaan); ?> &mdash; Periode Tahun <?php echo e($thnPeriode); ?></div>
            </div>
            <div class="text-end">
                <div style="font-weight: 700; font-family: monospace;"><?php echo e($noDokumen); ?></div>
                <div style="font-size: 8pt;">Total OFI: <strong><?php echo e($ofis->count()); ?></strong></div>
            </div>
        </div>

        <table class="formal-table">
            <thead>
                <tr>
                    <th style="width: 65px;">No.</th>
                    <th style="width: 150px;">Sub-Elemen / Referensi</th>
                    <th>Deskripsi Peluang Perbaikan</th>
                    <th>Rekomendasi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($ofis->isEmpty()): ?>
                    <tr><td colspan="4" class="text-center text-muted py-4">Tidak ada catatan Peluang untuk Perbaikan (OFI) pada sesi audit ini.</td></tr>
                <?php else: ?>
                    <?php foreach ($ofis as $ofi): ?>
                        <tr>
                            <td class="text-center fw-bold">OFI-<?php echo e(str_pad((string) $ofi->nomor_urut, 2, '0', STR_PAD_LEFT)); ?></td>
                            <td>
                                <?php if ($ofi->subElemen): ?>
                                    <strong><?php echo e($ofi->subElemen->kode_sub); ?></strong> <?php echo e($ofi->subElemen->nama_sub); ?><br>
                                <?php endif; ?>
                                <?php if ($ofi->referensi): ?>
                                    <small><?php echo e($ofi->referensi); ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo nl2br(e($ofi->deskripsi)); ?></td>
                            <td><?php echo nl2br(e($ofi->rekomendasi)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <script nonce="<?php echo e($cspNonce ?? ''); ?>">
        document.getElementById('btnPrintOfi')?.addEventListener('click', function () { window.print(); });
    </script>
</body>
</html>
