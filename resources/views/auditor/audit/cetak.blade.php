<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Audit Internal SMKP - {{ $sesi->area_audit }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #ffffff;
            color: #0f172a;
            font-size: 13px;
        }

        .report-header {
            border-bottom: 3px double #0f172a;
            padding-bottom: 12px;
            margin-bottom: 24px;
        }

        .table-report th {
            background-color: #0f172a !important;
            color: #ffffff !important;
            font-size: 12px;
            text-transform: uppercase;
        }

        .table-report td, .table-report th {
            padding: 8px 12px;
            border: 1px solid #cbd5e1;
        }

        .signature-box {
            margin-top: 50px;
        }

        .signature-space {
            height: 70px;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
            }
            .container {
                max-width: 100% !important;
                width: 100% !important;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <!-- Print Action Bar (No Print) -->
    <div class="no-print bg-dark text-white py-2 mb-4">
        <div class="container d-flex align-items-center justify-content-between">
            <span class="fw-bold"><i class="bi bi-printer me-1"></i> Mode Cetak Laporan Audit SMKP</span>
            <div class="d-flex gap-2">
                <button onclick="window.print()" class="btn btn-sm btn-info fw-bold">Cetak / Simpan PDF</button>
                <button onclick="window.close()" class="btn btn-sm btn-outline-light">Tutup Window</button>
            </div>
        </div>
    </div>

    <div class="container my-3">
        <!-- Header Document -->
        <div class="report-header text-center">
            <h4 class="fw-bold mb-1 text-uppercase">LAPORAN AUDIT INTERNAL SMKP MINERBA</h4>
            <h6 class="text-secondary fw-semibold mb-0">Sesuai Keputusan Direktur Jenderal Mineral dan Batubara No. 185.K/37.04/DJB/2019</h6>
        </div>

        <!-- Session Meta Data -->
        <table class="table table-bordered mb-4">
            <tbody>
                <tr>
                    <td class="fw-bold bg-light" style="width: 180px;">Area / Lokasi Audit</td>
                    <td class="fw-bold text-uppercase">{{ $sesi->area_audit }}</td>
                    <td class="fw-bold bg-light" style="width: 180px;">Tanggal Pelaksanaan</td>
                    <td>{{ $sesi->tanggal_audit->format('d F Y') }}</td>
                </tr>
                <tr>
                    <td class="fw-bold bg-light">Auditor Pelaksana</td>
                    <td>{{ $sesi->user->name }}</td>
                    <td class="fw-bold bg-light">Status Sesi Audit</td>
                    <td class="fw-bold text-uppercase">{{ $sesi->status }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Element Summary Table -->
        <h6 class="fw-bold text-uppercase mb-2">I. Rekapitulasi Nilai per Elemen</h6>
        <table class="table table-report w-100 mb-4">
            <thead>
                <tr>
                    <th style="width: 80px;" class="text-center">Elemen</th>
                    <th>Nama Elemen SMKP</th>
                    <th class="text-center" style="width: 110px;">Nilai Aktual</th>
                    <th class="text-center" style="width: 110px;">Nilai Maksimal</th>
                    <th class="text-center" style="width: 100px;">Pencapaian (%)</th>
                    <th class="text-center" style="width: 90px;">Bobot (%)</th>
                    <th class="text-center" style="width: 110px;">Skor Akhir (%)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rekap as $row)
                    <tr>
                        <td class="text-center fw-bold">{{ $row['kode_elemen'] }}</td>
                        <td>{{ $row['nama_elemen'] }}</td>
                        <td class="text-center fw-bold">{{ number_format($row['total_nilai_aktual'], 2) }}</td>
                        <td class="text-center text-muted">{{ number_format($row['total_nilai_maks_efektif'], 2) }}</td>
                        <td class="text-center fw-bold">{{ number_format($row['persentase'], 2) }}%</td>
                        <td class="text-center text-muted">{{ number_format($row['bobot'], 2) }}%</td>
                        <td class="text-center fw-bold text-primary">{{ number_format($row['skor_elemen'], 2) }}%</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="fw-bold bg-light">
                    <td colspan="4" class="text-end text-uppercase">Total Pencapaian Keseluruhan:</td>
                    <td class="text-center">{{ number_format(array_sum(array_column($rekap, 'persentase')) / max(count($rekap), 1), 2) }}%</td>
                    <td class="text-center">{{ number_format(array_sum(array_column($rekap, 'bobot')), 2) }}%</td>
                    <td class="text-center fs-6 text-success">{{ number_format($skorAkhir, 2) }}%</td>
                </tr>
            </tfoot>
        </table>

        <!-- Total Compliance Grade Card -->
        <div class="p-3 border rounded mb-4 bg-light">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="fw-bold mb-1">TINGKAT KEPATUHAN HASIL AUDIT:</h6>
                    @if($skorAkhir >= 85)
                        <span class="badge bg-success fs-6">PENCAPAIAN BAIK / KEPATUHAN TINGGI (Sesuai Standar Kepdirjen 185)</span>
                    @elseif($skorAkhir >= 70)
                        <span class="badge bg-warning text-dark fs-6">PENCAPAIAN CUKUP (Perlu Tindakan Perbaikan Minor)</span>
                    @else
                        <span class="badge bg-danger fs-6">PERBAIKAN MAYOR DIBUTUHKAN (Tidak Memenuhi Standar Minimal)</span>
                    @endif
                </div>
                <div class="text-end">
                    <span class="small text-muted d-block">SKOR AKHIR TOTAL</span>
                    <span class="display-6 fw-bold text-primary">{{ number_format($skorAkhir, 2) }}%</span>
                </div>
            </div>
        </div>

        <!-- Finding Notes & Proof Attachments List -->
        <h6 class="fw-bold text-uppercase mb-2">II. Catatan Temuan Audit</h6>
        @php
            $findings = $sesi->auditDetails->filter(function($d) {
                return !empty($d->catatan) || !empty($d->lampiran);
            });
        @endphp

        @if($findings->isEmpty())
            <p class="text-muted italic border p-3 rounded mb-4">Tidak ada catatan temuan khusus pada sesi audit ini.</p>
        @else
            <table class="table table-bordered mb-4">
                <thead class="bg-light">
                    <tr>
                        <th style="width: 90px;" class="text-center">Kriteria</th>
                        <th>Kriteria Pertanyaan</th>
                        <th style="width: 90px;" class="text-center">Skor</th>
                        <th>Catatan Temuan / Uraian Ketidaksesuaian</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($findings as $item)
                        <tr>
                            <td class="text-center fw-bold">{{ $item->kriteria->kode_kriteria ?? '-' }}</td>
                            <td class="small">{{ $item->kriteria->deskripsi ?? '-' }}</td>
                            <td class="text-center fw-bold">
                                @if($item->is_na)
                                    N/A
                                @else
                                    {{ number_format($item->nilai, 2) }}
                                @endif
                            </td>
                            <td class="small">{{ $item->catatan ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <!-- Signatures -->
        <div class="row signature-box text-center">
            <div class="col-6">
                <p class="mb-1">Auditor Pelaksana,</p>
                <div class="signature-space"></div>
                <p class="fw-bold mb-0"><u>{{ $sesi->user->name }}</u></p>
                <small class="text-muted">NIPP / ID Auditor</small>
            </div>
            <div class="col-6">
                <p class="mb-1">Manajemen Operasional Pertambangan,</p>
                <div class="signature-space"></div>
                <p class="fw-bold mb-0"><u>( ________________________ )</u></p>
                <small class="text-muted">Kepala Teknik Tambang / PTT</small>
            </div>
        </div>
    </div>
</body>
</html>
