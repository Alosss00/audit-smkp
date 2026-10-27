<?php
/**
 * Cetak Laporan Temuan Ketidaksesuaian (PICA)
 * Formulir Rencana Tindak Lanjut Ketidaksesuaian Audit Penerapan SMKP
 * Mengacu pada Kepdirjen Minerba No. 185.K/37.04/DJB/2019
 */

$namaPerusahaan = $sesi->perusahaan->nama_perusahaan ?? ($sesi->area_audit ?? 'PT Tambang Tondano Nusajaya');
$namaPerusahaanUpper = strtoupper($namaPerusahaan);
$areaAudit = $sesi->area_audit ?? 'Toka Tindung Project';
$projectTitle = 'TOKA TINDUNG PROJECT';

$namaLower = strtolower($namaPerusahaan);
if (str_contains($namaLower, 'meares') || str_contains($namaLower, 'msm')) {
    $companyLogo = 'logo_msm.jpg';
} elseif (str_contains($namaLower, 'tondano') || str_contains($namaLower, 'ttn')) {
    $companyLogo = 'logo_ttn.jpg';
} else {
    $companyLogo = 'logo_ttn.jpg';
}

if (!function_exists('formatTanggalAuditIndo')) {
    function formatTanggalAuditIndo($mulai, $selesai, $tahunFallback = null) {
        if (!$mulai && !$selesai) {
            return $tahunFallback ? 'Tahun Periode ' . $tahunFallback : '-';
        }
        $bulanIndo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        if ($mulai && !$selesai) {
            return $mulai->format('d') . ' ' . ($bulanIndo[(int)$mulai->format('n')] ?? '') . ' ' . $mulai->format('Y');
        }
        if (!$mulai && $selesai) {
            return $selesai->format('d') . ' ' . ($bulanIndo[(int)$selesai->format('n')] ?? '') . ' ' . $selesai->format('Y');
        }
        $d1 = (int)$mulai->format('d');
        $m1 = (int)$mulai->format('n');
        $y1 = (int)$mulai->format('Y');

        $d2 = (int)$selesai->format('d');
        $m2 = (int)$selesai->format('n');
        $y2 = (int)$selesai->format('Y');

        if ($y1 === $y2 && $m1 === $m2) {
            return sprintf('%02d - %02d %s %d', $d1, $d2, $bulanIndo[$m1], $y1);
        } elseif ($y1 === $y2) {
            return sprintf('%02d %s - %02d %s %d', $d1, $bulanIndo[$m1], $d2, $bulanIndo[$m2], $y1);
        } else {
            return sprintf('%02d %s %d - %02d %s %d', $d1, $bulanIndo[$m1], $y1, $d2, $bulanIndo[$m2], $y2);
        }
    }
}

if (!function_exists('renderPicaListFormatted')) {
    function renderPicaListFormatted($text) {
        if (empty($text) || trim($text) === '') {
            return '-';
        }
        $lines = preg_split('/\r\n|\r|\n/', trim($text));
        $items = [];
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed !== '') {
                $cleaned = preg_replace('/^(\d+[\.\)]|\-|\*|\•)\s*/u', '', $trimmed);
                $items[] = !empty($cleaned) ? $cleaned : $trimmed;
            }
        }
        if (empty($items)) {
            return '-';
        }
        $html = '<ul class="pica-bullets">';
        foreach ($items as $item) {
            $html .= '<li>' . htmlspecialchars($item, ENT_QUOTES, 'UTF-8') . '</li>';
        }
        $html .= '</ul>';
        return $html;
    }
}

if (!function_exists('formatBatasWaktu')) {
    function formatBatasWaktu($tenggat) {
        if (empty($tenggat)) {
            return '-';
        }
        if ($tenggat instanceof \DateTimeInterface) {
            return $tenggat->format('d/m/Y');
        }
        $str = trim((string)$tenggat);
        if (strtolower($str) === 'berkelanjutan') {
            return 'Berkelanjutan';
        }
        try {
            $c = \Carbon\Carbon::parse($str);
            return $c->format('d/m/Y');
        } catch (\Exception $e) {
            return $str;
        }
    }
}

$tanggalAuditFormatted = formatTanggalAuditIndo($sesi->tanggal_mulai, $sesi->tanggal_selesai, $sesi->tahun_periode);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Temuan Ketidaksesuaian (PICA) — <?php echo e($namaPerusahaan); ?></title>

    <!-- Google Font Plus Jakarta Sans & Local Bootstrap Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?php echo asset('vendor/bootstrap/bootstrap.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo asset('vendor/bootstrap-icons/bootstrap-icons.min.css'); ?>" rel="stylesheet">

    <!-- html2pdf for direct client-side PDF download -->
    <script src="<?php echo asset('vendor/html2pdf/html2pdf.bundle.min.js'); ?>" nonce="<?php echo e($cspNonce ?? ''); ?>"></script>

    <style>
        /* A4 Landscape Page Setup */
        @page {
            size: A4 landscape;
            margin: 10mm 12mm 14mm 12mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: 'Plus Jakarta Sans', Arial, Helvetica, sans-serif;
            background-color: #f1f5f9;
            color: #0f172a;
            font-size: 10.5px;
            line-height: 1.35;
            margin: 0;
            padding: 0;
        }

        /* Screen Wrapper */
        .page-container {
            width: 100%;
            max-width: 1180px;
            margin: 20px auto 40px auto;
            background: #ffffff;
            padding: 28px 32px 35px 32px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            position: relative;
        }

        /* 3-Box Header Table */
        .header-box-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            border: 1px solid #71717a;
        }

        .header-box-table td {
            border: 1px solid #71717a;
            vertical-align: middle;
            padding: 8px 12px;
        }

        .header-box-table .logo-col-left {
            width: 190px;
            text-align: center;
        }

        .header-box-table .title-col-center {
            text-align: center;
            font-size: 14px;
            font-weight: 800;
            letter-spacing: 1px;
            color: #000000;
            text-transform: uppercase;
        }

        .header-box-table .logo-col-right {
            width: 190px;
            text-align: center;
        }

        .header-logo-img {
            max-height: 52px;
            max-width: 160px;
            object-fit: contain;
            display: inline-block;
        }

        /* Title & Meta Section */
        .doc-title-box {
            margin-top: 10px;
            margin-bottom: 12px;
        }

        .doc-title {
            font-size: 12.5px;
            font-weight: 800;
            text-transform: uppercase;
            line-height: 1.4;
            color: #000000;
            margin: 0 0 8px 0;
        }

        .meta-table {
            border-collapse: collapse;
            font-size: 11px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .meta-table td {
            padding: 2px 0;
            vertical-align: top;
            color: #000000;
        }

        .meta-table .label {
            width: 140px;
        }

        .meta-table .colon {
            width: 18px;
            text-align: center;
        }

        /* Main Data Table */
        .pica-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5px;
            line-height: 1.35;
            table-layout: fixed;
            margin-top: 6px;
        }

        .pica-table th,
        .pica-table td {
            border: 1px solid #000000;
            padding: 6px 8px;
            vertical-align: top;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .pica-table thead {
            display: table-header-group;
        }

        .pica-table thead th {
            background-color: #ffffff;
            color: #000000;
            font-weight: 800;
            font-size: 9.5px;
            vertical-align: middle;
            text-align: left;
        }

        .pica-table thead th.text-center {
            text-align: center;
        }

        .pica-table tbody tr {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        /* Column Specific Styling */
        .col-no {
            width: 36px;
            text-align: center;
            font-weight: 600;
        }

        .col-deskripsi {
            width: 33%;
            text-align: justify;
        }

        .col-akar {
            width: 19%;
        }

        .col-koreksi {
            width: 23%;
        }

        .col-pic {
            width: 13%;
            text-align: center;
        }

        .col-waktu {
            width: 9%;
            text-align: center;
            white-space: nowrap;
        }

        /* Bullets */
        ul.pica-bullets {
            margin: 0;
            padding-left: 14px;
            list-style-type: disc;
        }

        ul.pica-bullets li {
            margin-bottom: 4px;
            line-height: 1.32;
        }

        ul.pica-bullets li:last-child {
            margin-bottom: 0;
        }

        /* Footer */
        .report-footer {
            margin-top: 14px;
            font-size: 8.5px;
            color: #334155;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #94a3b8;
            padding-top: 4px;
        }

        /* Print Specific CSS */
        @media print {
            body {
                background: #ffffff !important;
                color: #000000 !important;
                font-size: 9pt;
            }

            .no-print {
                display: none !important;
            }

            .page-container {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                background: transparent !important;
            }

            .header-box-table,
            .header-box-table td,
            .pica-table,
            .pica-table th,
            .pica-table td {
                border-color: #000000 !important;
            }

            .pica-table thead {
                display: table-header-group !important;
            }

            .pica-table tbody tr {
                page-break-inside: avoid !important;
            }

            .report-footer-print {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                display: flex;
                justify-content: space-between;
                font-size: 8pt;
                color: #000000;
                border-top: 1px solid #000000;
                padding-top: 3px;
                background: #ffffff;
            }
        }

        /* Top Action Bar for Screen Preview */
        .action-bar-top {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: gap-3;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .action-bar-title {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .badge-doc {
            background-color: #0284c7;
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 6px;
            text-transform: uppercase;
        }

        .action-btns {
            display: flex;
            align-items: center;
            gap: 8px;
        }
    </style>
</head>
<body>

    <div class="container-fluid px-3 pt-3">
        <!-- Top Action Bar (Screen Only) -->
        <div class="action-bar-top no-print">
            <div class="action-bar-title">
                <span class="badge-doc"><i class="bi bi-file-earmark-pdf me-1"></i> Mode Cetak PICA</span>
                <div>
                    <strong class="d-block" style="font-size: 13.5px;">Formulir Rencana Tindak Lanjut Ketidaksesuaian Audit Penerapan SMKP</strong>
                    <small class="text-light opacity-75">Format Standar Lampiran 2 PICA — Kepdirjen 185 Minerba (A4 Landscape)</small>
                </div>
            </div>
            <div class="action-btns">
                <button type="button" id="btnPrintLandscape" class="btn btn-sm btn-info fw-bold px-3 text-dark shadow-sm">
                    <i class="bi bi-printer-fill me-1"></i> Cetak / Simpan PDF (Landscape)
                </button>
                <button type="button" id="btnDownloadPdfDirect" class="btn btn-sm btn-success fw-bold px-3 shadow-sm">
                    <i class="bi bi-download me-1"></i> Unduh File PDF
                </button>
                <button type="button" id="btnClosePage" class="btn btn-sm btn-outline-light px-3">
                    <i class="bi bi-x-lg me-1"></i> Tutup
                </button>
            </div>
        </div>

        <!-- Printable Document Container -->
        <div class="page-container" id="printableReportContent">
            <!-- 3-Box Header Table -->
            <table class="header-box-table">
                <tr>
                    <td class="logo-col-left">
                        <img src="<?php echo asset('images/' . $companyLogo); ?>" alt="Company Logo" class="header-logo-img">
                    </td>
                    <td class="title-col-center">
                        <?php echo e($projectTitle); ?>
                    </td>
                    <td class="logo-col-right">
                        <img src="<?php echo asset('images/logo_archi.png'); ?>" alt="Archi Indonesia" class="header-logo-img">
                    </td>
                </tr>
            </table>

            <!-- Document Title & Header Metadata -->
            <div class="doc-title-box">
                <h4 class="doc-title">FORMULIR RENCANA TINDAK LANJUT KETIDAKSESUAIAN AUDIT PENERAPAN SISTEM MANAJEMEN KESELAMATAN PERTAMBANGAN</h4>
                <table class="meta-table">
                    <tr>
                        <td class="label">Nama Perusahaan</td>
                        <td class="colon">:</td>
                        <td><?php echo e($namaPerusahaanUpper); ?></td>
                    </tr>
                    <tr>
                        <td class="label">Tanggal Audit</td>
                        <td class="colon">:</td>
                        <td><?php echo e($tanggalAuditFormatted); ?></td>
                    </tr>
                </table>
            </div>

            <!-- Table of Nonconformity / PICA -->
            <table class="pica-table">
                <thead>
                    <tr>
                        <th class="col-no text-center">No.</th>
                        <th class="col-deskripsi">Deskripsi Ketidaksesuaian</th>
                        <th class="col-akar">Akar Permasalahan</th>
                        <th class="col-koreksi">Tindakan Koreksi</th>
                        <th class="col-pic text-center">Penanggung<br>jawab</th>
                        <th class="col-waktu text-center">Batas Waktu<br>Perbaikan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($picas->isEmpty()): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted fst-italic">
                                Tidak ada temuan ketidaksesuaian yang tercatat untuk sesi audit ini.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $itemNo = 1; ?>
                        <?php foreach($picas as $pica): ?>
                            <?php
                                $detail = $pica->auditDetail ?? null;
                                $kriteria = $detail->kriteria ?? null;
                                
                                // Deskripsi Ketidaksesuaian
                                $deskripsi = !empty($pica->deskripsi_ketidaksesuaian)
                                    ? $pica->deskripsi_ketidaksesuaian
                                    : (!empty($pica->deskripsi_temuan)
                                        ? $pica->deskripsi_temuan
                                        : ($detail->catatan ?? 'Ketidaksesuaian kriteria ' . ($kriteria->kode_kriteria ?? '-')));

                                // PIC Perbaikan
                                $pic = !empty($pica->pic_perbaikan) 
                                    ? $pica->pic_perbaikan 
                                    : (!empty($pica->pj_nama) ? $pica->pj_nama : '-');

                                // Batas Waktu
                                $batasWaktu = formatBatasWaktu($pica->tenggat_waktu);
                            ?>
                            <tr>
                                <td class="col-no"><?php echo e($itemNo++); ?>.</td>
                                <td class="col-deskripsi"><?php echo nl2br(e($deskripsi)); ?></td>
                                <td class="col-akar"><?php echo renderPicaListFormatted($pica->akar_masalah); ?></td>
                                <td class="col-koreksi"><?php echo renderPicaListFormatted($pica->tindakan_koreksi); ?></td>
                                <td class="col-pic"><?php echo nl2br(e($pic)); ?></td>
                                <td class="col-waktu"><?php echo e($batasWaktu); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Footer for screen & print -->
            <div class="report-footer d-flex d-print-none">
                <div>
                    Laporan Tindak Lanjut Hasil Audit Internal SMKP - <?php echo e($namaPerusahaan); ?>
                </div>
                <div>
                    Total Temuan: <strong><?php echo e($picas->count()); ?></strong> Ketidaksesuaian
                </div>
            </div>

            <!-- Print Fixed Footer -->
            <div class="report-footer-print d-none d-print-flex">
                <div>
                    Laporan Tindak Lanjut Hasil Audit Internal SMKP - <?php echo e($namaPerusahaan); ?>
                </div>
                <div>
                    Halaman &bull; Cetak Resmi SMKP
                </div>
            </div>
        </div>
    </div>

    <!-- Interactive Scripts for Printing and PDF Export -->
    <script nonce="<?php echo e($cspNonce ?? ''); ?>">
        document.addEventListener('DOMContentLoaded', function() {
            // Trigger browser print dialog (Landscape format)
            const btnPrint = document.getElementById('btnPrintLandscape');
            if (btnPrint) {
                btnPrint.addEventListener('click', function() {
                    window.print();
                });
            }

            // Close Window button
            const btnClose = document.getElementById('btnClosePage');
            if (btnClose) {
                btnClose.addEventListener('click', function() {
                    if (window.history.length > 1) {
                        window.history.back();
                    } else {
                        window.close();
                    }
                });
            }

            // Direct PDF Download via html2pdf
            const btnDownload = document.getElementById('btnDownloadPdfDirect');
            if (btnDownload) {
                btnDownload.addEventListener('click', function() {
                    const originalText = btnDownload.innerHTML;
                    btnDownload.disabled = true;
                    btnDownload.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mengunduh PDF...';

                    const element = document.getElementById('printableReportContent');
                    const opt = {
                        margin:       [8, 10, 10, 10],
                        filename:     'Lampiran_2_Tindak_Lanjut_Audit_PICA_<?php echo e(preg_replace('/[^A-Za-z0-9_\-]/', '_', $namaPerusahaan)); ?>_<?php echo e($sesi->tahun_periode ?? date("Y")); ?>.pdf',
                        image:        { type: 'jpeg', quality: 0.98 },
                        html2canvas:  { scale: 2, useCORS: true, logging: false },
                        jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' },
                        pagebreak:    { mode: ['avoid-all', 'css', 'legacy'] }
                    };

                    html2pdf().set(opt).from(element).save().then(function() {
                        btnDownload.disabled = false;
                        btnDownload.innerHTML = originalText;
                    }).catch(function(err) {
                        console.error('html2pdf error:', err);
                        btnDownload.disabled = false;
                        btnDownload.innerHTML = originalText;
                        // Fallback to window.print()
                        window.print();
                    });
                });
            }
        });
    </script>
</body>
</html>
