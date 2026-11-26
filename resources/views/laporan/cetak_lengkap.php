<?php
/**
 * Cetak Laporan Lengkap Hasil Audit Internal SMKP Minerba
 * Mencakup:
 *   I.   Tabel Penilaian Audit SMKP (Format Kepdirjen 185)
 *   II.  Tabel Praktik Terbaik (Best Practices 100%)
 *   III. Tabel Temuan Ketidaksesuaian & Rencana Tindak Lanjut (PICA)
 */

$namaPerusahaan = $sesi->perusahaan->nama_perusahaan ?? $sesi->area_audit;
$kodePerusahaan = $sesi->perusahaan->kode_perusahaan ?? '';
$searchStr = strtolower($namaPerusahaan . ' ' . $kodePerusahaan . ' ' . ($sesi->area_audit ?? ''));
if (str_contains($searchStr, 'tondano') || str_contains($searchStr, 'ttn')) {
    $companyLogo = 'logo_ttn.jpg';
} else {
    $companyLogo = 'logo_msm.jpg';
}
$periodeLabel = ($selectedYear ?? 'semua') === 'semua'
    ? 'Semua Periode Tahun (Akumulasi Total)'
    : 'Tahun ' . ($selectedYear ?: ($sesi->tahun_periode ?? date('Y')));

$allPicas = collect()
    ->concat($temuanKategori['kritikal'] ?? [])
    ->concat($temuanKategori['mayor'] ?? [])
    ->concat($temuanKategori['minor'] ?? []);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Lengkap Audit Internal SMKP — <?php echo e($namaPerusahaan); ?></title>

    <!-- Bootstrap 5.3 CSS & Icons -->
    <link href="<?php echo asset('vendor/bootstrap/bootstrap.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo asset('vendor/bootstrap-icons/bootstrap-icons.min.css'); ?>" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- html2pdf for direct client-side PDF download -->
    <script src="<?php echo asset('vendor/html2pdf/html2pdf.bundle.min.js'); ?>" nonce="<?php echo e($cspNonce ?? ''); ?>"></script>

    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 10mm 15mm 10mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: #f1f5f9;
            color: #0f172a;
            font-size: 10.5px;
            line-height: 1.35;
            margin: 0;
            padding: 0;
        }

        .print-container {
            max-width: 1040px;
            margin: 0 auto 40px auto;
            background: #ffffff;
            padding: 25px 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border-radius: 8px;
        }

        /* 3-Box Header Table */
        .header-box-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            border: 1px solid #71717a;
        }

        .header-box-table td {
            border: 1px solid #71717a;
            vertical-align: middle;
            padding: 8px 12px;
        }

        .header-box-table .logo-col-left {
            width: 180px;
            text-align: center;
        }

        .header-box-table .title-col-center {
            text-align: center;
            font-size: 13px;
            font-weight: 800;
            color: #0f172a;
        }

        .header-box-table .logo-col-right {
            width: 180px;
            text-align: center;
        }

        .header-logo-img {
            max-height: 52px;
            max-width: 155px;
            object-fit: contain;
            display: inline-block;
        }

        .report-header {
            border-bottom: 2.5px solid #0f172a;
            padding-bottom: 10px;
            margin-bottom: 16px;
        }

        .report-header h4 {
            font-size: 15px;
            font-weight: 800;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }

        .report-header h6 {
            font-size: 10.5px;
            font-weight: 600;
            color: #475569;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        .meta-table td {
            padding: 5px 8px;
            font-size: 10px;
            border: 1px solid #cbd5e1;
        }

        .meta-table .label-cell {
            background-color: #f1f5f9;
            font-weight: 700;
            color: #334155;
            width: 165px;
        }

        .section-title {
            font-size: 11.5px;
            font-weight: 800;
            text-transform: uppercase;
            color: #0f172a;
            margin-top: 22px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .section-title::before {
            content: "";
            display: inline-block;
            width: 4px;
            height: 14px;
            background-color: #0284c7;
            border-radius: 2px;
        }

        .section-title.section-success::before {
            background-color: #16a34a;
        }

        .section-title.section-danger::before {
            background-color: #dc2626;
        }

        .table-hierarki,
        .table-praktek,
        .table-pica {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5px;
            margin-bottom: 16px;
        }

        .table-hierarki th, .table-hierarki td,
        .table-praktek th, .table-praktek td,
        .table-pica th, .table-pica td {
            border: 1px solid #94a3b8;
            padding: 4px 6px;
            vertical-align: middle;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .table-hierarki thead th,
        .table-praktek thead th,
        .table-pica thead th {
            background-color: #0f172a !important;
            color: #ffffff !important;
            font-weight: 700;
            text-align: center;
            font-size: 9px;
            text-transform: uppercase;
        }

        .th-vertical {
            vertical-align: middle !important;
            text-align: center !important;
            font-size: 8.5px !important;
            padding: 4px 2px !important;
        }

        .tr-elemen {
            background-color: #e2e8f0 !important;
            font-weight: 800;
            color: #0f172a;
            border-top: 2px solid #334155 !important;
        }

        .tr-sub-elemen {
            background-color: #f8fafc !important;
            font-weight: 700;
            color: #1e293b;
        }

        .tr-kriteria {
            background-color: #ffffff !important;
            color: #334155;
        }

        .bg-sub-highlight {
            background-color: #e0f2fe !important;
            font-weight: 700;
            color: #0369a1;
            text-align: center;
        }

        .bg-elemen-total {
            background-color: #dcfce7 !important;
            font-weight: 800;
            color: #15803d;
            text-align: center;
        }

        .page-break-section {
            page-break-before: always;
            break-before: page;
        }

        .signature-section {
            margin-top: 28px;
            page-break-inside: avoid;
        }

        .signature-space {
            height: 60px;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background-color: #ffffff !important;
                color: #000000 !important;
                padding: 0;
                margin: 0;
                font-size: 9px;
            }
            .print-container {
                max-width: 100% !important;
                width: 100% !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                border-radius: 0 !important;
            }
            .table-hierarki, .meta-table, .table-praktek, .table-pica, .header-box-table {
                font-size: 8.5px !important;
            }
            .table-hierarki th, .table-hierarki td,
            .meta-table td, .table-praktek th, .table-praktek td,
            .table-pica th, .table-pica td, .header-box-table td {
                border-color: #475569 !important;
            }
            tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

    <!-- Print Action Bar (No Print) -->
    <div class="no-print bg-dark text-white py-2.5 mb-4 sticky-top shadow-sm">
        <div class="container d-flex align-items-center justify-content-between flex-wrap gap-2 py-1">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary px-2 py-1">Laporan Lengkap</span>
                <span class="fw-bold small">
                    <i class="bi bi-file-earmark-richtext me-1"></i> Mode Cetak Laporan Lengkap (Penilaian Audit + Praktik Terbaik + PICA)
                </span>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" id="btnPrintReport" class="btn btn-sm btn-info fw-bold px-3 text-dark shadow-sm">
                    <i class="bi bi-printer-fill me-1"></i> Cetak / Simpan PDF
                </button>
                <button type="button" id="btnDownloadPdfDirect" class="btn btn-sm btn-primary fw-bold px-3 shadow-sm">
                    <i class="bi bi-download me-1"></i> Unduh File PDF
                </button>
                <a href="<?php echo e((auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'auditor_smkp')) ? route('admin.rekap-audit.export-excel', $sesi->id) : route('auditor.audit-sesi.export-excel', $sesi->id)); ?>" class="btn btn-sm btn-success fw-bold px-3 shadow-sm">
                    <i class="bi bi-file-earmark-excel-fill me-1"></i> Download Excel (.xlsx)
                </a>
                <button type="button" id="btnCloseWindow" class="btn btn-sm btn-outline-light px-3">
                    <i class="bi bi-x-lg me-1"></i> Tutup Window
                </button>
            </div>
        </div>
    </div>

    <div class="print-container" id="printableFullReport">
        <!-- 3-Box Header Table -->
        <table class="header-box-table">
            <tr>
                <td class="logo-col-left">
                    <img src="<?php echo asset('images/' . $companyLogo); ?>" alt="Company Logo" class="header-logo-img">
                </td>
                <td class="title-col-center">
                    <div style="font-size: 15px; font-weight: 800; letter-spacing: 0.5px; color: #0f172a;">TOKA TINDUNG PROJECT</div>
                    <div style="font-size: 11px; font-weight: 700; color: #334155; margin-top: 2px;">LAPORAN LENGKAP HASIL AUDIT INTERNAL SMKP MINERBA</div>
                    <div style="font-size: 9.5px; font-weight: 500; color: #64748b; margin-top: 2px;">Sesuai Keputusan Direktur Jenderal Mineral dan Batubara No. 185.K/37.04/DJB/2019</div>
                </td>
                <td class="logo-col-right">
                    <img src="<?php echo asset('images/logo_archi.png'); ?>" alt="Archi Indonesia" class="header-logo-img">
                </td>
            </tr>
        </table>

        <!-- TABEL METADATA -->
        <table class="meta-table mb-3">
            <tbody>
                <tr>
                    <td class="label-cell">Perusahaan / Auditee</td>
                    <td class="fw-bold text-uppercase"><?php echo e($namaPerusahaan); ?></td>
                    <td class="label-cell">Periode Pelaksanaan</td>
                    <td><?php echo e($sesi->tanggal_mulai ? $sesi->tanggal_mulai->format('d F Y') : '-'); ?> s/d <?php echo e($sesi->tanggal_selesai ? $sesi->tanggal_selesai->format('d F Y') : '-'); ?></td>
                </tr>
                <tr>
                    <td class="label-cell">Area / Lokasi Audit</td>
                    <td class="fw-bold text-uppercase"><?php echo e($sesi->area_audit); ?></td>
                    <td class="label-cell">Tahun / Periode Evaluasi</td>
                    <td class="fw-bold"><?php echo e($periodeLabel); ?></td>
                </tr>
                <tr>
                    <td class="label-cell">Auditor Pelaksana</td>
                    <td><?php echo e($sesi->user->name ?? 'Auditor Internal'); ?> (<?php echo e($sesi->user ? $sesi->user->role_label : 'Auditor'); ?>)</td>
                    <td class="label-cell">Ringkasan Hasil Audit</td>
                    <td>
                        Skor: <strong class="text-primary"><?php echo e(number_format($skorAkhir, 2)); ?>%</strong> &bull;
                        Praktik Terbaik: <strong class="text-success"><?php echo e(count($praktekBaik)); ?></strong> &bull;
                        Temuan PICA: <strong class="text-danger"><?php echo e($allPicas->count()); ?></strong>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- =========================================================================
             BAGIAN I: TABEL PENILAIAN AUDIT SMKP (FORMAT KEPDIRJEN 185)
             ========================================================================= -->
        <div class="section-title">I. Tabel Penilaian & Rekapitulasi Nilai Audit SMKP (Format Kepdirjen 185)</div>
        <table class="table-hierarki mb-3">
            <thead>
                <tr>
                    <th rowspan="2" colspan="3" class="align-middle text-center" style="width: 38%;">KRITERIA / PARAMETER EVALUASI</th>
                    <th rowspan="2" class="th-vertical" style="width: 48px;">Nilai Elemen %</th>
                    <th rowspan="2" class="th-vertical" style="width: 48px;">Nilai Sub Elemen</th>
                    <th rowspan="2" class="th-vertical" style="width: 48px;">Nilai Sub-sub Elemen</th>
                    <th colspan="4" class="text-center align-middle">Nilai Hasil Audit</th>
                    <th rowspan="2" class="align-middle text-center" style="width: 24%;">Keterangan</th>
                </tr>
                <tr>
                    <th class="th-vertical bg-primary text-white" style="width: 48px;">Nilai Sub Elemen</th>
                    <th class="th-vertical" style="width: 48px;">Nilai Sub-sub Elemen</th>
                    <th class="th-vertical bg-success text-white" style="width: 48px;">Total Nilai Elemen</th>
                    <th class="th-vertical" style="width: 52px;">Presentase Elemen</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($hierarki as $el): ?>
                    <tr class="tr-elemen">
                        <td colspan="3" class="text-start">
                            ELEMEN <?php echo e($el['kode_elemen']); ?>: <?php echo e(strtoupper($el['nama_elemen'])); ?>
                        </td>
                        <td class="text-center"><?php echo e(number_format($el['bobot'], 2)); ?>%</td>
                        <td class="text-center"><?php echo e(number_format($el['total_nilai_maks_efektif'], 0)); ?></td>
                        <td class="text-center text-muted">-</td>
                        <td class="text-center text-muted">-</td>
                        <td class="text-center text-muted">-</td>
                        <td class="bg-elemen-total"><?php echo e(number_format($el['total_nilai_aktual'], 2)); ?></td>
                        <td class="text-center fw-bold"><?php echo e(number_format($el['persentase'], 2)); ?>%</td>
                        <td class="text-center small"></td>
                    </tr>

                    <?php foreach($el['sub_elemens'] as $sub): ?>
                        <?php if(!empty($sub['is_direct'])): ?>
                            <tr class="tr-sub-elemen">
                                <td class="text-center font-monospace fw-bold" style="width: 48px;"><?php echo e($sub['kode_sub']); ?></td>
                                <td colspan="2">
                                    <div><?php echo e($sub['nama_sub']); ?></div>
                                </td>
                                <td class="text-center text-muted">-</td>
                                <td class="text-center font-monospace fw-bold"><?php echo e(number_format($sub['total_nilai_maks_efektif'], 0)); ?></td>
                                <td class="text-center text-muted">-</td>
                                <td class="bg-sub-highlight font-monospace">
                                    <?php if(!empty($sub['direct_detail']['is_na'])): ?>
                                        <span class="badge bg-secondary">N/A</span>
                                    <?php else: ?>
                                        <span class="<?php echo e($sub['total_nilai_aktual'] < $sub['total_nilai_maks_efektif'] ? 'text-danger fw-bold' : 'text-primary fw-bold'); ?>">
                                            <?php echo e(number_format($sub['total_nilai_aktual'], 2)); ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center text-muted">-</td>
                                <td class="text-center text-muted">-</td>
                                <td class="text-center text-muted">-</td>
                                <td class="<?php echo !empty($sub['direct_detail']['is_na']) ? 'text-center' : 'text-start'; ?> small text-secondary" style="white-space: pre-line; word-break: break-word;">
                                    <?php echo e(!empty($sub['direct_detail']['is_na']) ? 'N/A' : ($sub['direct_detail']['catatan'] ?? '')); ?>
                                </td>
                            </tr>
                        <?php else: ?>
                            <tr class="tr-sub-elemen">
                                <td class="text-center font-monospace fw-bold" style="width: 48px;"><?php echo e($sub['kode_sub']); ?></td>
                                <td colspan="2" class="fw-bold"><?php echo e($sub['nama_sub']); ?></td>
                                <td class="text-center text-muted">-</td>
                                <td class="text-center font-monospace fw-bold"><?php echo e(number_format($sub['total_nilai_maks_efektif'], 0)); ?></td>
                                <td class="text-center text-muted">-</td>
                                <td class="bg-sub-highlight font-monospace">
                                    <span class="<?php echo e($sub['total_nilai_aktual'] < $sub['total_nilai_maks_efektif'] ? 'text-danger fw-bold' : 'text-primary fw-bold'); ?>">
                                        <?php echo e(number_format($sub['total_nilai_aktual'], 2)); ?>
                                    </span>
                                </td>
                                <td class="text-center text-muted">-</td>
                                <td class="text-center text-muted">-</td>
                                <td class="text-center text-muted">-</td>
                                <td class="text-center small text-secondary"></td>
                            </tr>

                            <?php foreach($sub['details'] as $d): ?>
                                <tr class="tr-kriteria">
                                    <td style="width: 48px;"></td>
                                    <td class="text-center font-monospace small text-secondary" style="width: 60px;"><?php echo e($d['kode_kriteria']); ?></td>
                                    <td class="small ps-2">
                                        <div><?php echo e($d['deskripsi']); ?></div>
                                    </td>
                                    <td class="text-center text-muted">-</td>
                                    <td class="text-center text-muted">-</td>
                                    <td class="text-center font-monospace small text-muted"><?php echo e(number_format($d['nilai_maksimal'], 0)); ?></td>
                                    <td class="text-center text-muted">-</td>
                                    <td class="text-center font-monospace fw-bold">
                                        <?php if($d['is_na']): ?>
                                            <span class="badge bg-secondary" style="font-size: 8px;">N/A</span>
                                        <?php else: ?>
                                            <span class="<?php echo e($d['nilai'] < $d['nilai_maksimal'] ? 'text-danger' : 'text-success'); ?>">
                                                <?php echo e(number_format($d['nilai'], 0)); ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center text-muted">-</td>
                                    <td class="text-center text-muted">-</td>
                                    <td class="<?php echo !empty($d['is_na']) ? 'text-center' : 'text-start'; ?> small text-muted" style="font-size: 8.5px; white-space: pre-line; word-break: break-word;">
                                        <?php echo e($d['is_na'] ? 'N/A' : ($d['catatan'] ?? '')); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="fw-bold bg-light" style="border-top: 2px solid #0f172a;">
                    <td colspan="8" class="text-end text-uppercase">Total Pencapaian Keseluruhan:</td>
                    <td class="bg-elemen-total text-center fs-6 text-success"><?php echo e(number_format($skorAkhir, 2)); ?>%</td>
                    <td class="text-center text-primary fs-6"><?php echo e(number_format(array_sum(array_column($rekap, 'persentase')) / max(count($rekap), 1), 2)); ?>%</td>
                    <td class="text-center"></td>
                </tr>
            </tfoot>
        </table>

        <div class="p-2 px-3 border rounded mb-4 bg-light d-flex justify-content-between align-items-center">
            <div>
                <span class="small text-muted fw-bold d-block" style="font-size: 9.5px;">TINGKAT KEPATUHAN HASIL AUDIT:</span>
                <?php if($skorAkhir >= 85): ?>
                    <span class="badge bg-success fs-6"><i class="bi bi-check-circle-fill me-1"></i> PENCAPAIAN BAIK / KEPATUHAN TINGGI (Sesuai Standar Kepdirjen 185)</span>
                <?php elseif($skorAkhir >= 70): ?>
                    <span class="badge bg-warning text-dark fs-6"><i class="bi bi-exclamation-circle-fill me-1"></i> PENCAPAIAN CUKUP (Perlu Tindakan Perbaikan Minor)</span>
                <?php else: ?>
                    <span class="badge bg-danger fs-6"><i class="bi bi-x-circle-fill me-1"></i> PERBAIKAN MAYOR DIBUTUHKAN (Tidak Memenuhi Standar Minimal)</span>
                <?php endif; ?>
            </div>
            <div class="text-end">
                <span class="small text-muted d-block" style="font-size: 9px;">SKOR AKHIR TOTAL</span>
                <span class="h4 fw-bold text-primary mb-0"><?php echo e(number_format($skorAkhir, 2)); ?>%</span>
            </div>
        </div>

        <!-- =========================================================================
             BAGIAN II: TABEL PRAKTIK TERBAIK (BEST PRACTICES — KEPATUHAN 100%)
             ========================================================================= -->
        <div class="section-title section-success page-break-section">
            II. Tabel Sub-Elemen Praktik Terbaik (Best Practices — Kepatuhan 100%)
        </div>
        <p class="small text-muted mb-2">
            Daftar Sub-Elemen yang telah mencapai tingkat kepatuhan sempurna (100% dari total nilai maksimal efektif):
            <strong><?php echo e(count($praktekBaik)); ?> Sub-Elemen</strong>.
        </p>

        <table class="table-praktek mb-4">
            <thead>
                <tr>
                    <th style="width: 24%;">Elemen</th>
                    <th style="width: 34%;">Sub Elemen</th>
                    <th style="width: 42%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php if($praktekBaik->isEmpty()): ?>
                    <tr>
                        <td colspan="3" class="text-center py-3 text-muted fst-italic">
                            Belum ada sub-elemen yang mencapai kepatuhan 100% pada periode audit ini.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($praktekBaik as $item): ?>
                        <?php
                            $elemKode = $item['elemen_kode'] ?? ($item['kode_elemen'] ?? '-');
                            $elemNama = $item['nama_elemen'] ?? ($item['elemen_nama'] ?? '-');
                            $subKode  = $item['kode_sub_elemen'] ?? ($item['kode_sub'] ?? '-');
                            $subNama  = $item['nama_sub_elemen'] ?? ($item['nama_sub'] ?? '-');
                        ?>
                        <tr>
                            <td class="fw-bold text-slate-800">
                                Elemen <?php echo e($elemKode); ?>: <?php echo e($elemNama); ?>
                            </td>
                            <td>
                                <span class="font-monospace fw-bold text-primary me-1"><?php echo e($subKode); ?></span>
                                <span class="fw-semibold"><?php echo e($subNama); ?></span>
                            </td>
                            <td>
                                <?php if(is_array($item['catatan'])): ?>
                                    <ul class="mb-0 ps-3 small">
                                        <?php foreach($item['catatan'] as $ct): ?>
                                            <li style="white-space: pre-line;"><?php echo e($ct); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php elseif(!empty($item['catatan'])): ?>
                                    <div class="small"><?php echo nl2br(e($item['catatan'])); ?></div>
                                <?php else: ?>
                                    <span class="text-muted small fst-italic">Kesesuaian penuh memenuhi standar evaluasi SMKP Minerba.</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- =========================================================================
             BAGIAN III: TABEL TEMUAN KETIDAKSESUAIAN & RENCANA TINDAK LANJUT (PICA)
             ========================================================================= -->
        <div class="section-title section-danger page-break-section">
            III. Tabel Temuan Ketidaksesuaian & Rencana Tindak Lanjut Perbaikan (PICA)
        </div>
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2 small">
            <span class="text-muted">
                Rincian ketidaksesuaian hasil audit beserta akar masalah, tindakan koreksi & pencegahan, penanggung jawab, batas waktu, dan status.
            </span>
            <div class="d-flex gap-2">
                <span class="badge bg-danger">Kritikal: <?php echo e($temuanKategori['kritikal_count'] ?? 0); ?> Sub-Elemen (<?php echo e(count($temuanKategori['kritikal'] ?? [])); ?> Tindakan)</span>
                <span class="badge bg-warning text-dark">Mayor: <?php echo e($temuanKategori['mayor_count'] ?? 0); ?> Sub-Elemen (<?php echo e(count($temuanKategori['mayor'] ?? [])); ?> Tindakan)</span>
                <span class="badge bg-info text-dark">Minor: <?php echo e($temuanKategori['minor_count'] ?? 0); ?> Sub-Elemen (<?php echo e(count($temuanKategori['minor'] ?? [])); ?> Tindakan)</span>
            </div>
        </div>

        <table class="table-pica mb-4">
            <thead>
                <tr>
                    <th style="width: 4%;">No</th>
                    <th style="width: 11%;">Elemen</th>
                    <th style="width: 15%;">Sub Elemen</th>
                    <th style="width: 7%;">Kategori</th>
                    <th style="width: 21%;">Deskripsi Ketidaksesuaian / PLOR</th>
                    <th style="width: 13%;">Akar Masalah</th>
                    <th style="width: 14%;">Tindakan Koreksi &amp; Pencegahan</th>
                    <th style="width: 7%;">PIC</th>
                    <th style="width: 4%;">Tenggat</th>
                    <th style="width: 4%;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if($allPicas->isEmpty()): ?>
                    <tr>
                        <td colspan="10" class="text-center py-3 text-muted fst-italic">
                            Tidak ditemukan ketidaksesuaian (PICA) pada sesi audit ini.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $rowIdxPica = 1; ?>
                    <?php foreach($allPicas as $pica): ?>
                        <?php
                            $detail = $pica->auditDetail ?? null;
                            $kri = $detail->kriteria ?? null;
                            $sub = $kri->subElemen ?? null;
                            $elemen = $sub->elemen ?? null;

                            $nomorTemuan = $pica->nomor_urut ?: $rowIdxPica;
                            $rowIdxPica++;
                            $katLabel = ucfirst((string) ($pica->kategori_temuan ?: 'minor'));
                            $hasPlor = !empty($pica->plor_problem) || !empty($pica->plor_location) || !empty($pica->plor_objective_evidence) || !empty($pica->plor_reference);

                            $elemKode = $elemen->kode_elemen ?? '-';
                            $elemNama = $elemen->nama_elemen ?? '-';

                            $subKode = $sub->kode_sub ?? ($kri->kode_kriteria ?? '-');
                            $subNama = $sub->nama_sub ?? ($kri->deskripsi ?? '-');
                            $kodeKri = $kri->kode_kriteria ?? $subKode;
                            $namaKri = $kri->deskripsi ?? $subNama;

                            $deskripsiTemuan = !empty($pica->deskripsi_ketidaksesuaian)
                                ? $pica->deskripsi_ketidaksesuaian
                                : (!empty($pica->deskripsi_temuan) ? $pica->deskripsi_temuan : ($detail->catatan ?? '-'));

                            $akarMasalah = !empty($pica->akar_masalah) ? $pica->akar_masalah : '-';

                            $pic = !empty($pica->pic_perbaikan) ? $pica->pic_perbaikan : (!empty($pica->pj_nama) ? $pica->pj_nama : '-');

                            $batasWaktu = '-';
                            if (!empty($pica->tenggat_waktu)) {
                                if ($pica->tenggat_waktu instanceof \DateTimeInterface) {
                                    $batasWaktu = $pica->tenggat_waktu->format('d/m/Y');
                                } else {
                                    try {
                                        $batasWaktu = \Carbon\Carbon::parse($pica->tenggat_waktu)->format('d/m/Y');
                                    } catch (\Exception $e) {
                                        $batasWaktu = (string) $pica->tenggat_waktu;
                                    }
                                }
                            }

                            $status = $pica->status ?? 'open';
                            $stBadge = $status === 'closed' ? 'bg-success' : ($status === 'in_progress' ? 'bg-warning text-dark' : 'bg-danger');
                            $stLabel = $status === 'closed' ? 'Closed' : ($status === 'in_progress' ? 'In Progress' : 'Open');
                        ?>
                        <tr>
                            <td class="text-center small fw-bold"><?php echo e($nomorTemuan); ?></td>
                            <td class="small fw-bold">
                                Elemen <?php echo e($elemKode); ?>: <?php echo e($elemNama); ?>
                            </td>
                            <td class="small">
                                <div class="fw-bold text-primary font-monospace"><?php echo e($subKode); ?> — <?php echo e($subNama); ?></div>
                                <?php if($kodeKri !== $subKode): ?>
                                    <div class="text-muted mt-1" style="font-size: 8.5px;">
                                        <strong><?php echo e($kodeKri); ?>:</strong> <?php echo e($namaKri); ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="text-center small fw-bold"><?php echo e($katLabel); ?></td>
                            <td class="small">
                                <?php if ($hasPlor): ?>
                                    <div><strong>P:</strong> <?php echo nl2br(e($pica->plor_problem ?: '-')); ?></div>
                                    <div><strong>L:</strong> <?php echo e($pica->plor_location ?: '-'); ?></div>
                                    <div><strong>O:</strong> <?php echo nl2br(e($pica->plor_objective_evidence ?: '-')); ?></div>
                                    <div><strong>R:</strong> <?php echo e($pica->plor_reference ?: '-'); ?></div>
                                <?php else: ?>
                                    <?php echo nl2br(e($deskripsiTemuan)); ?>
                                <?php endif; ?>
                            </td>
                            <td class="small"><?php echo nl2br(e($akarMasalah)); ?></td>
                            <td class="small">
                                <div class="mb-1">
                                    <strong>Koreksi:</strong> <?php echo nl2br(e($pica->tindakan_koreksi ?? '-')); ?>
                                </div>
                                <?php if(!empty($pica->tindakan_pencegahan)): ?>
                                    <div>
                                        <strong>Pencegahan:</strong> <?php echo nl2br(e($pica->tindakan_pencegahan)); ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="text-center small fw-semibold"><?php echo nl2br(e($pic)); ?></td>
                            <td class="text-center small"><?php echo e($batasWaktu); ?></td>
                            <td class="text-center">
                                <span class="badge <?php echo e($stBadge); ?>" style="font-size: 8px;"><?php echo e($stLabel); ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- =========================================================================
             BAGIAN PENUTUP / TANDA TANGAN
             ========================================================================= -->
        <div class="signature-section">
            <div class="row text-center">
                <div class="col-6">
                    <p class="mb-0 fw-bold">Auditor Pelaksana,</p>
                    <small class="text-muted d-block mb-1">SMKP Minerba Internal Auditor</small>
                    <div class="signature-space"></div>
                    <p class="fw-bold mb-0 text-decoration-underline">( ________________________________ )</p>
                    <small class="text-muted">ID / NIPP: ........................................</small>
                </div>
                <div class="col-6">
                    <p class="mb-0 fw-bold">Manajemen / PJO / KTT,</p>
                    <small class="text-muted d-block mb-1"><?php echo e($namaPerusahaan); ?></small>
                    <div class="signature-space"></div>
                    <p class="fw-bold mb-0 text-decoration-underline">( ________________________________ )</p>
                    <small class="text-muted">Kepala Teknik Tambang / Penanggung Jawab Operasional</small>
                </div>
            </div>
        </div>

    </div>

    <script nonce="<?php echo e($cspNonce ?? ''); ?>">
        document.addEventListener('DOMContentLoaded', function() {
            const btnPrint = document.getElementById('btnPrintReport');
            if (btnPrint) {
                btnPrint.addEventListener('click', function() {
                    window.print();
                });
            }

            const btnClose = document.getElementById('btnCloseWindow');
            if (btnClose) {
                btnClose.addEventListener('click', function() {
                    if (window.history.length > 1) {
                        window.history.back();
                    } else {
                        window.close();
                    }
                });
            }

            const btnDownload = document.getElementById('btnDownloadPdfDirect');
            if (btnDownload) {
                btnDownload.addEventListener('click', function() {
                    const originalText = btnDownload.innerHTML;
                    btnDownload.disabled = true;
                    btnDownload.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mengunduh PDF...';

                    const element = document.getElementById('printableFullReport');
                    const opt = {
                        margin:       [10, 8, 12, 8],
                        filename:     'Laporan_Lengkap_Audit_SMKP_<?php echo e(preg_replace('/[^A-Za-z0-9_\-]/', '_', $namaPerusahaan)); ?>_<?php echo e($sesi->tahun_periode ?? date("Y")); ?>.pdf',
                        image:        { type: 'jpeg', quality: 0.98 },
                        html2canvas:  { scale: 2, useCORS: true, logging: false },
                        jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' },
                        pagebreak:    { mode: ['css', 'legacy'] }
                    };

                    html2pdf().set(opt).from(element).save().then(function() {
                        btnDownload.disabled = false;
                        btnDownload.innerHTML = originalText;
                    }).catch(function(err) {
                        console.error('html2pdf error:', err);
                        btnDownload.disabled = false;
                        btnDownload.innerHTML = originalText;
                        window.print();
                    });
                });
            }
        });
    </script>
</body>
</html>

