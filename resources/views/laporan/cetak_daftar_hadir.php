<?php
/**
 * Cetak Daftar Hadir Rapat Pembukaan / Penutupan Audit Internal SMKP (A4 Portrait)
 */
$namaPerusahaan = $sesi->perusahaan->nama_perusahaan ?? $sesi->area_audit;
$judulOtomatis = $dh->judul_otomatis;
$hariIndonesia = $dh->nama_hari_indonesia;
$tglFormatted = $dh->tanggal ? $dh->tanggal->format('d/m/Y') : '-';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Hadir — <?php echo e($judulOtomatis); ?> — <?php echo e($namaPerusahaan); ?></title>
    <link href="<?php echo asset('vendor/bootstrap/bootstrap.min.css'); ?>" rel="stylesheet">
    <style>
        @page { size: A4 portrait; margin: 18mm 16mm; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11pt; color: #111827; background: #e2e8f0; margin: 0; }
        .toolbar { background: #0f172a; color: #fff; padding: 10px 20px; display: flex; justify-content: space-between; align-items: center; }
        .sheet { background: #fff; max-width: 210mm; margin: 16px auto; padding: 18mm 16mm; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .dh-table { width: 100%; border-collapse: collapse; margin-top: 12px; font-size: 10pt; }
        .dh-table th, .dh-table td { border: 1px solid #334155; padding: 6px 8px; vertical-align: middle; }
        .dh-table th { background: #e2e8f0; text-align: center; font-weight: 700; }
        @media print {
            body { background: #fff; }
            .toolbar { display: none !important; }
            .sheet { margin: 0; padding: 0; max-width: none; box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <div class="small fw-bold">Daftar Hadir — <?php echo e($judulOtomatis); ?></div>
        <div class="d-flex gap-2">
            <button type="button" id="btnPrintDh" class="btn btn-sm btn-primary fw-semibold">Cetak</button>
            <button type="button" id="btnCloseDh" class="btn btn-sm btn-outline-light">Tutup</button>
        </div>
    </div>

    <div class="sheet">
        <div class="text-center mb-3">
            <div class="fw-bold text-uppercase" style="font-size: 14pt;">DAFTAR HADIR</div>
            <div class="fw-bold text-uppercase" style="font-size: 12pt;"><?php echo e($judulOtomatis); ?></div>
            <div class="small text-muted"><?php echo e($namaPerusahaan); ?> &bull; Periode <?php echo e($sesi->tahun_periode); ?></div>
        </div>

        <table class="dh-table mb-3">
            <tbody>
                <tr><th style="width: 28%; text-align: left;">Kegiatan</th><td><?php echo e($judulOtomatis); ?></td></tr>
                <tr><th style="text-align: left;">Hari / Tanggal</th><td><?php echo e($hariIndonesia . ', ' . $tglFormatted); ?></td></tr>
                <tr><th style="text-align: left;">Tempat / Lokasi</th><td><?php echo e($dh->lokasi ?: '-'); ?></td></tr>
            </tbody>
        </table>

        <table class="dh-table">
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>Nama</th>
                    <th>Jabatan</th>
                    <th>Unit / Instansi</th>
                    <th style="width: 110px;">Peran</th>
                    <th style="width: 110px;">Tanda Tangan</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($dh->pesertas->isNotEmpty()): ?>
                    <?php foreach ($dh->pesertas as $idx => $p): ?>
                        <tr>
                            <td class="text-center"><?php echo e($idx + 1); ?></td>
                            <td><?php echo e($p->nama); ?></td>
                            <td><?php echo e($p->jabatan ?: '-'); ?></td>
                            <td><?php echo e($p->unit_instansi ?: '-'); ?></td>
                            <td class="text-center"><?php echo e($p->peran_label); ?></td>
                            <td style="height: 36px;"></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada daftar peserta yang dicatat.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script nonce="<?php echo e($cspNonce ?? ''); ?>">
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('btnPrintDh')?.addEventListener('click', function () { window.print(); });
        document.getElementById('btnCloseDh')?.addEventListener('click', function () { window.close(); });
    });
    </script>
</body>
</html>
