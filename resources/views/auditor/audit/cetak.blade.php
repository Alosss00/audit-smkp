<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Audit Internal SMKP — {{ $sesi->area_audit }} ({{ $sesi->perusahaan->nama_perusahaan ?? 'SMKP' }})</title>

    <!-- Bootstrap 5.3 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
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

        /* Header Document */
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

        /* Meta Table */
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

        /* Hierarki Matrix Formatting (Kepdirjen 185) */
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

        /* Hierarchy Row Styling */
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

        /* Section Title */
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

        /* Custom Table for Findings */
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

        /* Signature Box */
        .signature-section {
            margin-top: 25px;
            page-break-inside: avoid;
        }

        .signature-space {
            height: 60px;
        }

        /* Print Media Queries */
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
                <a href="{{ (auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'auditor_smkp')) ? route('admin.audit-sesi.export-excel', $sesi->id) : route('auditor.audit-sesi.export-excel', $sesi->id) }}" class="btn btn-sm btn-success fw-bold px-3 shadow-sm">
                    <i class="bi bi-file-earmark-excel-fill me-1"></i> Download Excel (.xlsx)
                </a>
                <button type="button" id="btnCloseWindow" class="btn btn-sm btn-outline-light px-3">
                    <i class="bi bi-x-lg me-1"></i> Tutup Window
                </button>
            </div>
        </div>
    </div>

    <div class="print-container">
        
        <!-- Header Document -->
        <div class="report-header text-center">
            <h4 class="text-uppercase mb-1">LAPORAN AUDIT INTERNAL SISTEM MANAJEMEN KESELAMATAN PERTAMBANGAN (SMKP) MINERBA</h4>
            <h6>Sesuai Keputusan Direktur Jenderal Mineral dan Batubara No. 185.K/37.04/DJB/2019</h6>
        </div>

        <!-- Session Meta Table -->
        <table class="meta-table mb-3">
            <tbody>
                <tr>
                    <td class="label-cell">Area / Lokasi Audit</td>
                    <td class="fw-bold text-uppercase">{{ $sesi->perusahaan->nama_perusahaan ?? $sesi->area_audit }}</td>
                    <td class="label-cell">Periode Pelaksanaan</td>
                    <td>{{ $sesi->tanggal_mulai ? $sesi->tanggal_mulai->format('d F Y') : '-' }} s/d {{ $sesi->tanggal_selesai ? $sesi->tanggal_selesai->format('d F Y') : '-' }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Perusahaan / Auditee</td>
                    <td class="fw-bold text-uppercase">{{ $sesi->perusahaan->nama_perusahaan ?? $sesi->area_audit }}</td>
                    <td class="label-cell">Tahun / Periode Audit</td>
                    <td class="fw-bold">{{ $sesi->periode ?? date('Y') }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Auditor Pelaksana</td>
                    <td>{{ $sesi->user->name ?? 'Auditor Internal' }} ({{ $sesi->user ? $sesi->user->role_label : 'Auditor' }})</td>
                    <td class="label-cell">Status Sesi Audit</td>
                    <td class="fw-bold text-uppercase">{{ $sesi->status }}</td>
                </tr>
            </tbody>
        </table>

        <!-- ==================== TABEL UTAMA: RINCIAN PENILAIAN LENGKAP HIERARKI KEPDIRJEN 185 ==================== -->
        <div class="section-title">I. Rincian Penilaian & Rekapitulasi Nilai Audit SMKP (Format Kepdirjen 185)</div>
        <table class="table-hierarki mb-3">
            <thead>
                <!-- Baris Header 1 -->
                <tr>
                    <th rowspan="2" colspan="3" class="align-middle text-center">KRITERIA / PARAMETER EVALUASI</th>
                    <th rowspan="2" class="th-vertical" style="width: 55px;">Nilai Elemen %</th>
                    <th rowspan="2" class="th-vertical" style="width: 55px;">Nilai Sub Elemen</th>
                    <th rowspan="2" class="th-vertical" style="width: 55px;">Nilai Sub-sub Elemen</th>
                    <th colspan="4" class="text-center align-middle">Nilai Hasil Audit</th>
                    <th rowspan="2" class="align-middle text-center" style="width: 75px;">Keterangan</th>
                </tr>
                <!-- Baris Header 2 -->
                <tr>
                    <th class="th-vertical bg-primary text-white" style="width: 55px;">Nilai Sub Elemen</th>
                    <th class="th-vertical" style="width: 55px;">Nilai Sub-sub Elemen</th>
                    <th class="th-vertical bg-success text-white" style="width: 55px;">Total Nilai Elemen</th>
                    <th class="th-vertical" style="width: 55px;">Presentase Elemen</th>
                </tr>
            </thead>
            <tbody>
                @foreach($hierarki as $el)
                    <!-- Level 1: Elemen Induk -->
                    <tr class="tr-elemen">
                        <td colspan="3" class="text-start">
                            ELEMEN {{ $el['kode_elemen'] }}: {{ strtoupper($el['nama_elemen']) }}
                        </td>
                        <td class="text-center">{{ number_format($el['bobot'], 2) }}%</td>
                        <td class="text-center">{{ number_format($el['total_nilai_maks_efektif'], 0) }}</td>
                        <td class="text-center text-muted">-</td>
                        <td class="text-center text-muted">-</td>
                        <td class="text-center text-muted">-</td>
                        <td class="bg-elemen-total">{{ number_format($el['total_nilai_aktual'], 2) }}</td>
                        <td class="text-center fw-bold">{{ number_format($el['persentase'], 2) }}%</td>
                        <td class="text-center small"></td>
                    </tr>

                    <!-- Level 2: Sub-Elemen (Sub-Bab) -->
                    @foreach($el['sub_elemens'] as $sub)
                        @if(!empty($sub['is_direct']))
                            <!-- Sub-Elemen Penilaian Tunggal (Direct) -->
                            <tr class="tr-sub-elemen">
                                <td class="text-center font-monospace fw-bold" style="width: 48px;">{{ $sub['kode_sub'] }}</td>
                                <td colspan="2">
                                    <div>{{ $sub['nama_sub'] }}</div>
                                    @if(!empty($sub['direct_detail']['catatan']))
                                        <div class="small text-danger fw-semibold" style="font-size: 8.5px;">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i>Temuan: {{ $sub['direct_detail']['catatan'] }}
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center text-muted">-</td>
                                <td class="text-center font-monospace fw-bold">{{ number_format($sub['total_nilai_maks_efektif'], 0) }}</td>
                                <td class="text-center text-muted">-</td>
                                <td class="bg-sub-highlight font-monospace">
                                    @if(!empty($sub['direct_detail']['is_na']))
                                        <span class="badge bg-secondary">N/A</span>
                                    @else
                                        <span class="{{ $sub['total_nilai_aktual'] < $sub['total_nilai_maks_efektif'] ? 'text-danger fw-bold' : 'text-primary fw-bold' }}">
                                            {{ number_format($sub['total_nilai_aktual'], 2) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center text-muted">-</td>
                                <td class="text-center text-muted">-</td>
                                <td class="text-center text-muted">-</td>
                                <td class="text-center small text-secondary">
                                    {{ !empty($sub['direct_detail']['is_na']) ? 'N/A' : ($sub['direct_detail']['catatan'] ?? '') }}
                                </td>
                            </tr>
                        @else
                            <!-- Sub-Elemen dengan Beberapa Sub-Subbab (Kriteria) -->
                            <tr class="tr-sub-elemen">
                                <td class="text-center font-monospace fw-bold" style="width: 48px;">{{ $sub['kode_sub'] }}</td>
                                <td colspan="2" class="fw-bold">{{ $sub['nama_sub'] }}</td>
                                <td class="text-center text-muted">-</td>
                                <td class="text-center font-monospace fw-bold">{{ number_format($sub['total_nilai_maks_efektif'], 0) }}</td>
                                <td class="text-center text-muted">-</td>
                                <td class="bg-sub-highlight font-monospace">
                                    <span class="{{ $sub['total_nilai_aktual'] < $sub['total_nilai_maks_efektif'] ? 'text-danger fw-bold' : 'text-primary fw-bold' }}">
                                        {{ number_format($sub['total_nilai_aktual'], 2) }}
                                    </span>
                                </td>
                                <td class="text-center text-muted">-</td>
                                <td class="text-center text-muted">-</td>
                                <td class="text-center text-muted">-</td>
                                <td class="text-center small text-secondary"></td>
                            </tr>

                            <!-- Level 3: Sub-Subbab / Kriteria Penilaian -->
                            @foreach($sub['details'] as $d)
                                <tr class="tr-kriteria">
                                    <td style="width: 48px;"></td>
                                    <td class="text-center font-monospace small text-secondary" style="width: 60px;">{{ $d['kode_kriteria'] }}</td>
                                    <td class="small ps-2">
                                        <div>{{ $d['deskripsi'] }}</div>
                                        @if(!empty($d['catatan']))
                                            <div class="small text-danger fw-semibold mt-0.5" style="font-size: 8.5px;">
                                                <i class="bi bi-exclamation-triangle-fill me-1"></i>Temuan: {{ $d['catatan'] }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-center text-muted">-</td>
                                    <td class="text-center text-muted">-</td>
                                    <td class="text-center font-monospace small text-muted">{{ number_format($d['nilai_maksimal'], 0) }}</td>
                                    <td class="text-center text-muted">-</td>
                                    <td class="text-center font-monospace fw-bold">
                                        @if($d['is_na'])
                                            <span class="badge bg-secondary" style="font-size: 8px;">N/A</span>
                                        @else
                                            <span class="{{ $d['nilai'] < $d['nilai_maksimal'] ? 'text-danger' : 'text-success' }}">
                                                {{ number_format($d['nilai'], 0) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center text-muted">-</td>
                                    <td class="text-center text-muted">-</td>
                                    <td class="text-center small text-muted" style="font-size: 8.5px;">
                                        {{ $d['is_na'] ? 'N/A' : ($d['catatan'] ?? '') }}
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    @endforeach
                @endforeach
            </tbody>
            <tfoot>
                <tr class="fw-bold bg-light" style="border-top: 2px solid #0f172a;">
                    <td colspan="8" class="text-end text-uppercase">Total Pencapaian Keseluruhan:</td>
                    <td class="bg-elemen-total text-center fs-6 text-success">{{ number_format($skorAkhir, 2) }}%</td>
                    <td class="text-center text-primary fs-6">{{ number_format(array_sum(array_column($rekap, 'persentase')) / max(count($rekap), 1), 2) }}%</td>
                    <td class="text-center"></td>
                </tr>
            </tfoot>
        </table>

        <!-- Compliance Grade Card -->
        <div class="p-2 px-3 border rounded mb-3 bg-light d-flex justify-content-between align-items-center">
            <div>
                <span class="small text-muted fw-bold d-block" style="font-size: 9.5px;">TINGKAT KEPATUHAN HASIL AUDIT:</span>
                @if($skorAkhir >= 85)
                    <span class="badge bg-success fs-6"><i class="bi bi-check-circle-fill me-1"></i> PENCAPAIAN BAIK / KEPATUHAN TINGGI (Sesuai Standar Kepdirjen 185)</span>
                @elseif($skorAkhir >= 70)
                    <span class="badge bg-warning text-dark fs-6"><i class="bi bi-exclamation-circle-fill me-1"></i> PENCAPAIAN CUKUP (Perlu Tindakan Perbaikan Minor)</span>
                @else
                    <span class="badge bg-danger fs-6"><i class="bi bi-x-circle-fill me-1"></i> PERBAIKAN MAYOR DIBUTUHKAN (Tidak Memenuhi Standar Minimal)</span>
                @endif
            </div>
            <div class="text-end">
                <span class="small text-muted d-block" style="font-size: 9px;">SKOR AKHIR TOTAL</span>
                <span class="h4 fw-bold text-primary mb-0">{{ number_format($skorAkhir, 2) }}%</span>
            </div>
        </div>

        <!-- ==================== BAGIAN II: CATATAN TEMUAN AUDIT ==================== -->
        <div class="section-title">II. Catatan Temuan Audit & Uraian Ketidaksesuaian</div>
        @php
            $findings = $sesi->auditDetails->filter(function($d) {
                return (!empty($d->catatan) || ($d->nilai < ($d->kriteria->nilai_maksimal ?? 4) && !$d->is_na));
            });
        @endphp

        @if($findings->isEmpty())
            <div class="p-2 px-3 border rounded mb-3 bg-light text-muted fst-italic text-center" style="font-size: 10px;">
                <i class="bi bi-check2-all text-success me-1"></i> Tidak ada catatan temuan ketidaksesuaian khusus pada sesi audit ini. Seluruh parameter dinilai memenuhi standar.
            </div>
        @else
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
                    @foreach($findings as $item)
                        <tr>
                            <td class="text-center font-monospace fw-bold">{{ $item->kriteria->kode_kriteria ?? '-' }}</td>
                            <td class="small">{{ $item->kriteria->deskripsi ?? '-' }}</td>
                            <td class="text-center fw-bold">
                                @if($item->is_na)
                                    <span class="badge bg-secondary">N/A</span>
                                @else
                                    <span class="{{ $item->nilai < ($item->kriteria->nilai_maksimal ?? 4) ? 'text-danger' : 'text-success' }}">
                                        {{ number_format($item->nilai, 0) }} / {{ number_format($item->kriteria->nilai_maksimal ?? 4, 0) }}
                                    </span>
                                @endif
                            </td>
                            <td class="small text-danger fw-semibold">
                                {{ $item->catatan ?: 'Skor belum maksimal (Perlu evaluasi pemenuhan standar dokumen/implementasi).' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <!-- ==================== BAGIAN III: LEMBAR PENGESAHAN ==================== -->
        <div class="signature-section">
            <div class="row text-center">
                <div class="col-6">
                    <p class="mb-0 fw-bold">Auditor Pelaksana,</p>
                    <small class="text-muted d-block mb-1">SMKP Minerba Internal Auditor</small>
                    <div class="signature-space"></div>
                    <p class="fw-bold mb-0 text-decoration-underline">{{ $sesi->user->name ?? 'Auditor Pelaksana' }}</p>
                    <small class="text-muted">ID / NIPP: {{ $sesi->user->username ?? '-' }}</small>
                </div>
                <div class="col-6">
                    <p class="mb-0 fw-bold">Manajemen / PJO / KTT,</p>
                    <small class="text-muted d-block mb-1">{{ $sesi->perusahaan->nama_perusahaan ?? 'Kepala Teknik Tambang' }}</small>
                    <div class="signature-space"></div>
                    <p class="fw-bold mb-0 text-decoration-underline">( ________________________________ )</p>
                    <small class="text-muted">Kepala Teknik Tambang / Penanggung Jawab Operasional</small>
                </div>
            </div>
        </div>

    </div>
    
    <script nonce="{{ $cspNonce ?? '' }}">
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
