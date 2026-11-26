<?php
$namaPerusahaan = $sesi->perusahaan->nama_perusahaan ?? $sesi->area_audit;
$thnPeriode = $sesi->tahun_periode ?? ($sesi->tanggal_mulai ? $sesi->tanggal_mulai->format('Y') : date('Y'));
$tglTtdDisplay = isset($tanggalTtdObj) && $tanggalTtdObj
    ? $tanggalTtdObj->format('d/m/Y')
    : ($sesi->tanggal_selesai ? $sesi->tanggal_selesai->format('d/m/Y') : date('d/m/Y'));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Formulir Kesesuaian Audit <?php echo e($namaPerusahaan); ?> - <?php echo e($noDokumen); ?></title>
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
        <button type="button" class="btn btn-dark btn-sm px-3" id="btnPrintKesesuaian">Cetak / Simpan PDF</button>
    </div>
    <div class="sheet">
        <?php if (!empty($peringatanTanggal)): ?>
            <div class="alert alert-warning py-2 px-3 small mb-3">
                <strong><?php echo e($peringatanTanggal); ?></strong>
            </div>
        <?php endif; ?>

        <div class="doc-header">
            <div>
                <div style="font-size: 11pt; font-weight: 800;">FORMULIR KESESUAIAN AUDIT INTERNAL SMKP MINERBA</div>
                <div style="font-size: 9pt; color: #334155;"><?php echo e($namaPerusahaan); ?> &mdash; Periode Tahun <?php echo e($thnPeriode); ?></div>
            </div>
            <div class="text-end">
                <div style="font-weight: 700; font-family: monospace;"><?php echo e($noDokumen); ?></div>
                <div style="font-size: 8pt;">Sub-Elemen Sesuai Penuh: <strong><?php echo e($sesuaiList->count()); ?></strong></div>
            </div>
        </div>

        <p style="font-size: 8.5pt; color: #334155; margin-bottom: 10px;">
            Daftar sub-elemen yang memenuhi kesesuaian penuh (100% seluruh kriteria aktif non-N/A memperoleh nilai maksimal) berdasarkan hasil verifikasi lapangan dan bukti objektif pada sesi audit tanggal <?php echo e($sesi->tanggal_mulai ? $sesi->tanggal_mulai->format('d/m/Y') : '-'); ?> s/d <?php echo e($sesi->tanggal_selesai ? $sesi->tanggal_selesai->format('d/m/Y') : '-'); ?>.
        </p>

        <table class="formal-table">
            <thead>
                <tr>
                    <th style="width: 45px;">No</th>
                    <th style="width: 110px;">Elemen</th>
                    <th style="width: 190px;">Sub-Elemen</th>
                    <th style="width: 75px;">Skor</th>
                    <th>Bukti Objektif / Catatan Verifikasi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($sesuaiList->isEmpty()): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada sub-elemen yang mencapai kesesuaian penuh (100%).</td></tr>
                <?php else: ?>
                    <?php foreach ($sesuaiList as $idx => $item): ?>
                        <tr>
                            <td class="text-center"><?php echo e($idx + 1); ?></td>
                            <td><strong><?php echo e($item['elemen_kode'] ?? '-'); ?></strong><br><small><?php echo e($item['elemen_nama'] ?? ''); ?></small></td>
                            <td><strong><?php echo e($item['kode_sub'] ?? '-'); ?></strong> <?php echo e($item['nama_sub'] ?? '-'); ?></td>
                            <td class="text-center fw-bold"><?php echo e($item['nilai_label'] ?? '100%'); ?></td>
                            <td>
                                <?php if (is_array($item['catatan'] ?? null)): ?>
                                    <ul class="mb-0 ps-3">
                                        <?php foreach ($item['catatan'] as $c): ?>
                                            <li><?php echo e($c); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <?php echo e($item['keterangan'] ?? 'Kesesuaian penuh.'); ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <table class="formal-table" style="margin-top: 18px;">
            <thead>
                <tr>
                    <th style="width: 50%;">Ketua Tim Audit</th>
                    <th style="width: 50%;">Perwakilan Auditi</th>
                </tr>
            </thead>
            <tbody>
                <tr style="height: 80px;">
                    <td class="text-center" style="vertical-align: bottom;">
                        <div style="font-size: 8.5pt;">Tanggal: <?php echo e($tglTtdDisplay); ?></div>
                    </td>
                    <td class="text-center" style="vertical-align: bottom;">
                        <div style="font-size: 8.5pt;">Tanggal: <?php echo e($tglTtdDisplay); ?></div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <script nonce="<?php echo e($cspNonce ?? ''); ?>">
        document.getElementById('btnPrintKesesuaian')?.addEventListener('click', function () { window.print(); });
    </script>
</body>
</html>
