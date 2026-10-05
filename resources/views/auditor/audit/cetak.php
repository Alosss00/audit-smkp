<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Audit Internal SMKP — <?php echo e($sesi->area_audit); ?> (<?php echo e($sesi->perusahaan->nama_perusahaan ?? 'SMKP'); ?>)</title>

    <!-- Bootstrap 5.3 CSS & Icons (Local Offline Assets) -->
    <link href="<?php echo asset('vendor/bootstrap/bootstrap.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo asset('vendor/bootstrap-icons/bootstrap-icons.min.css'); ?>" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 10mm 15mm 10mm;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
            font-size: 11px;
            line-height: 1.35;
        }

        .print-container {
            max-width: 1000px;
            margin: 0 auto;
            background: #ffffff;
            padding: 25px 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border-radius: 8px;
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
            font-size: 10.5px;
            border: 1px solid #cbd5e1;
        }

        .meta-table .label-cell {
            background-color: #f1f5f9;
            font-weight: 700;
            color: #334155;
            width: 170px;
        }

        .table-hierarki {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-bottom: 16px;
        }

        .table-hierarki th, .table-hierarki td {
            border: 1px solid #94a3b8;
            padding: 4px 6px;
            vertical-align: middle;
        }

        .table-hierarki thead th {
            background-color: #0f172a !important;
            color: #ffffff !important;
            font-weight: 700;
            text-align: center;
            font-size: 9.5px;
            text-transform: uppercase;
        }

        .th-vertical {
            vertical-align: middle !important;
            text-align: center !important;
            font-size: 9px !important;
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

        .section-title {
            font-size: 11.5px;
            font-weight: 800;
            text-transform: uppercase;
            color: #0f172a;
            margin-top: 18px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .section-title::before {
            content: "";
            display: inline-block;
            width: 4px;
            height: 13px;
            background-color: #0284c7;
            border-radius: 2px;
        }

        .table-findings {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-bottom: 16px;
        }

        .table-findings th, .table-findings td {
            border: 1px solid #94a3b8;
            padding: 5px 8px;
            vertical-align: middle;
        }

        .table-findings thead th {
            background-color: #0f172a !important;
            color: #ffffff !important;
            font-weight: 700;
            font-size: 9.5px;
            text-transform: uppercase;
        }

        .signature-section {
            margin-top: 25px;
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
                background-color: #ffffff;
                color: #000000;
                padding: 0;
                margin: 0;
                font-size: 9.5px;
            }
            .print-container {
                max-width: 100% !important;
                width: 100% !important;
                box-shadow: none !important;
                padding: 0 !important;
                border-radius: 0 !important;
            }
            .table-hierarki, .meta-table, .table-findings {
                font-size: 9px !important;
            }
            .table-hierarki th, .table-hierarki td,
            .meta-table td, .table-findings th, .table-findings td {
                border-color: #475569 !important;
            }
            .tr-elemen {
                background-color: #e2e8f0 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .bg-sub-highlight {
                background-color: #e0f2fe !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .bg-elemen-total {
                background-color: #dcfce7 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .table-hierarki thead th, .table-findings thead th {
                background-color: #0f172a !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

    <!-- Print Action Bar (No Print) -->
    <div class="no-print bg-dark text-white py-2 mb-4 sticky-top shadow-sm">
        <div class="container d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary px-2 py-1">Kepdirjen 185</span>
                <span class="fw-bold small"><i class="bi bi-printer me-1"></i> Mode Cetak Dokumen Audit Internal SMKP</span>
            </div>
            <div class="d-flex gap-2">
                <button type="button" id="btnPrintReport" class="btn btn-sm btn-info fw-bold px-3 shadow-sm">
                    <i class="bi bi-printer-fill me-1"></i> Cetak / Simpan PDF
                </button>
                <a href="<?php echo e((auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'auditor_smkp')) ? route('admin.audit-sesi.export-excel', $sesi->id) : route('auditor.audit-sesi.export-excel', $sesi->id)); ?>" class="btn btn-sm btn-success fw-bold px-3 shadow-sm">
                    <i class="bi bi-file-earmark-excel-fill me-1"></i> Download Excel (.xlsx)
                </a>
                <button type="button" id="btnCloseWindow" class="btn btn-sm btn-outline-light px-3">
                    <i class="bi bi-x-lg me-1"></i> Tutup Window
                </button>
            </div>
        </div>
    </div>

    <div class="print-container">
        <div class="report-header text-center">
            <h4 class="text-uppercase mb-1">LAPORAN AUDIT INTERNAL SISTEM MANAJEMEN KESELAMATAN PERTAMBANGAN (SMKP) MINERBA</h4>
            <h6>Sesuai Keputusan Direktur Jenderal Mineral dan Batubara No. 185.K/37.04/DJB/2019</h6>
        </div>

        <table class="meta-table mb-3">
            <tbody>
                <tr>
                    <td class="label-cell">Area / Lokasi Audit</td>
                    <td class="fw-bold text-uppercase"><?php echo e($sesi->perusahaan->nama_perusahaan ?? $sesi->area_audit); ?></td>
                    <td class="label-cell">Periode Pelaksanaan</td>
                    <td><?php echo e($sesi->tanggal_mulai ? $sesi->tanggal_mulai->format('d F Y') : '-'); ?> s/d <?php echo e($sesi->tanggal_selesai ? $sesi->tanggal_selesai->format('d F Y') : '-'); ?></td>
                </tr>
                <tr>
                    <td class="label-cell">Perusahaan / Auditee</td>
                    <td class="fw-bold text-uppercase"><?php echo e($sesi->perusahaan->nama_perusahaan ?? $sesi->area_audit); ?></td>
                    <td class="label-cell">Tahun / Periode Audit</td>
                    <td class="fw-bold"><?php echo e($sesi->periode ?? date('Y')); ?></td>
                </tr>
                <tr>
                    <td class="label-cell">Auditor Pelaksana</td>
                    <td><?php echo e($sesi->user->name ?? 'Auditor Internal'); ?> (<?php echo e($sesi->user ? $sesi->user->role_label : 'Auditor'); ?>)</td>
                    <td class="label-cell">Status Sesi Audit</td>
                    <td class="fw-bold text-uppercase"><?php echo e($sesi->status); ?></td>
                </tr>
            </tbody>
        </table>

        <div class="section-title">I. Rincian Penilaian & Rekapitulasi Nilai Audit SMKP (Format Kepdirjen 185)</div>
        <table class="table-hierarki mb-3">
            <thead>
                <tr>
                    <th rowspan="2" colspan="3" class="align-middle text-center">KRITERIA / PARAMETER EVALUASI</th>
                    <th rowspan="2" class="th-vertical" style="width: 55px;">Nilai Elemen %</th>
                    <th rowspan="2" class="th-vertical" style="width: 55px;">Nilai Sub Elemen</th>
                    <th rowspan="2" class="th-vertical" style="width: 55px;">Nilai Sub-sub Elemen</th>
                    <th colspan="4" class="text-center align-middle">Nilai Hasil Audit</th>
                    <th rowspan="2" class="align-middle text-center" style="width: 75px;">Keterangan</th>
                </tr>
                <tr>
                    <th class="th-vertical bg-primary text-white" style="width: 55px;">Nilai Sub Elemen</th>
                    <th class="th-vertical" style="width: 55px;">Nilai Sub-sub Elemen</th>
                    <th class="th-vertical bg-success text-white" style="width: 55px;">Total Nilai Elemen</th>
                    <th class="th-vertical" style="width: 55px;">Presentase Elemen</th>
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
                                    <?php if(!empty($sub['direct_detail']['catatan'])): ?>
                                        <div class="small text-danger fw-semibold" style="font-size: 8.5px;">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i>Temuan: <?php echo e($sub['direct_detail']['catatan']); ?>

                                        </div>
                                    <?php endif; ?>
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
                                <td class="text-center small text-secondary">
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
                                        <?php if(!empty($d['catatan'])): ?>
                                            <div class="small text-danger fw-semibold mt-0.5" style="font-size: 8.5px;">
                                                <i class="bi bi-exclamation-triangle-fill me-1"></i>Temuan: <?php echo e($d['catatan']); ?>

                                            </div>
                                        <?php endif; ?>
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
                                    <td class="text-center small text-muted" style="font-size: 8.5px;">
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

        <div class="p-2 px-3 border rounded mb-3 bg-light d-flex justify-content-between align-items-center">
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

        <div class="section-title">II. Catatan Temuan Audit & Uraian Ketidaksesuaian</div>
        <?php
            $findings = $sesi->auditDetails->filter(function($d) {
                return (!empty($d->catatan) || ($d->nilai < ($d->kriteria->nilai_maksimal ?? 4) && !$d->is_na));
            });
        ?>

        <?php if($findings->isEmpty()): ?>
            <div class="p-2 px-3 border rounded mb-3 bg-light text-muted fst-italic text-center" style="font-size: 10px;">
                <i class="bi bi-check2-all text-success me-1"></i> Tidak ada catatan temuan ketidaksesuaian khusus pada sesi audit ini. Seluruh parameter dinilai memenuhi standar.
            </div>
        <?php else: ?>
            <table class="table-findings mb-3">
                <thead>
                    <tr>
                        <th style="width: 80px;" class="text-center">Kriteria</th>
                        <th style="width: 300px;">Deskripsi Kriteria / Sub-Subbab</th>
                        <th style="width: 80px;" class="text-center">Skor</th>
                        <th>Catatan Temuan / Uraian Ketidaksesuaian</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($findings as $item): ?>
                        <tr>
                            <td class="text-center font-monospace fw-bold"><?php echo e($item->kriteria->kode_kriteria ?? '-'); ?></td>
                            <td class="small"><?php echo e($item->kriteria->deskripsi ?? '-'); ?></td>
                            <td class="text-center fw-bold">
                                <?php if($item->is_na): ?>
                                    <span class="badge bg-secondary">N/A</span>
                                <?php else: ?>
                                    <span class="<?php echo e($item->nilai < ($item->kriteria->nilai_maksimal ?? 4) ? 'text-danger' : 'text-success'); ?>">
                                        <?php echo e(number_format($item->nilai, 0)); ?> / <?php echo e(number_format($item->kriteria->nilai_maksimal ?? 4, 0)); ?>

                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="small text-danger fw-semibold">
                                <?php echo e($item->catatan ?: 'Skor belum maksimal (Perlu evaluasi pemenuhan standar dokumen/implementasi).'); ?>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <div class="signature-section">
            <div class="row text-center">
                <div class="col-6">
                    <p class="mb-0 fw-bold">Auditor Pelaksana,</p>
                    <small class="text-muted d-block mb-1">SMKP Minerba Internal Auditor</small>
                    <div class="signature-space"></div>
                    <p class="fw-bold mb-0 text-decoration-underline"><?php echo e($sesi->user->name ?? 'Auditor Pelaksana'); ?></p>
                    <small class="text-muted">ID / NIPP: <?php echo e($sesi->user->username ?? '-'); ?></small>
                </div>
                <div class="col-6">
                    <p class="mb-0 fw-bold">Manajemen / PJO / KTT,</p>
                    <small class="text-muted d-block mb-1"><?php echo e($sesi->perusahaan->nama_perusahaan ?? 'Kepala Teknik Tambang'); ?></small>
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
                    window.close();
                });
            }
        });
    </script>
</body>
</html>
