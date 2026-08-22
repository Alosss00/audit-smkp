@extends('layouts.app')

@section('title', 'Detail Rekap Audit — Administrator')

@section('content')
<div class="mb-4 d-flex align-items-center justify-content-between">
    <div>
        <a href="{{ route('admin.rekap-audit.index') }}" class="text-decoration-none text-muted small">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Monitoring Audit
        </a>
        <h2 class="fw-bold text-slate-800 mb-0 mt-1">Detail Rekap Audit Internal SMKP</h2>
        <p class="text-muted small mb-0">Area: <strong>{{ $sesi->area_audit }}</strong> | Auditor: <strong>{{ $sesi->user->name }}</strong></p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.rekap-audit.export-excel', $sesi->id) }}" class="btn btn-success rounded-3 px-3">
            <i class="bi bi-file-earmark-excel me-1"></i> Export Excel (.xlsx)
        </a>
        <a href="{{ route('admin.rekap-audit.cetak', $sesi->id) }}" target="_blank" class="btn btn-dark rounded-3 px-3">
            <i class="bi bi-printer me-1"></i> Cetak Laporan (PDF)
        </a>
    </div>
</div>

<!-- Score Overview Card -->
<div class="row g-4 mb-4">
    <div class="col-md-6 col-lg-8">
        <div class="card card-custom p-4 h-100 border-start border-5 border-primary">
            <h5 class="fw-bold text-slate-800 mb-3">Tingkat Pencapaian Penerapan SMKP</h5>
            <div class="d-flex align-items-center gap-4">
                <div class="display-3 fw-bold text-primary">{{ number_format($skorAkhir, 2) }}%</div>
                <div>
                    @if($skorAkhir >= 85)
                        <span class="badge bg-success p-2 px-3 fs-6 rounded-pill mb-2"><i class="bi bi-shield-check me-1"></i> Pencapaian Baik / Kepatuhan Tinggi</span>
                        <p class="text-muted small mb-0">Penerapan SMKP Minerba Kepdirjen 185 memenuhi standar evaluasi tinggi.</p>
                    @elseif($skorAkhir >= 70)
                        <span class="badge bg-warning text-dark p-2 px-3 fs-6 rounded-pill mb-2"><i class="bi bi-exclamation-triangle me-1"></i> Pencapaian Cukup / Perlu Peningkatan</span>
                        <p class="text-muted small mb-0">Memerlukan tindakan perbaikan pada beberapa kriteria minor.</p>
                    @else
                        <span class="badge bg-danger p-2 px-3 fs-6 rounded-pill mb-2"><i class="bi bi-x-circle me-1"></i> Perbaikan Mayor Dibutuhkan</span>
                        <p class="text-muted small mb-0">Terdapat banyak kriteria kritis yang belum memenuhi standar regulasi.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-4">
        <div class="card card-custom p-4 h-100 bg-slate-900 text-white">
            <h6 class="text-uppercase text-light opacity-75 fw-bold mb-3">Informasi Sesi Audit</h6>
            <div class="mb-2">
                <small class="text-light opacity-50 d-block">Auditor Pelaksana:</small>
                <strong class="text-white">{{ $sesi->user->name }}</strong>
            </div>
            <div class="mb-2">
                <small class="text-light opacity-50 d-block">Status Sesi:</small>
                @if($sesi->status === 'draft')
                    <span class="badge bg-secondary">Draft</span>
                @elseif($sesi->status === 'berjalan')
                    <span class="badge bg-warning text-dark">Berjalan</span>
                @else
                    <span class="badge bg-success">Selesai (Terkunci)</span>
                @endif
            </div>
            <div>
                <small class="text-light opacity-50 d-block">Periode Audit:</small>
                <span class="text-white">{{ $sesi->tanggal_mulai->format('d M Y') }} - {{ $sesi->tanggal_selesai->format('d M Y') }}</span>
            </div>
        </div>
    </div>
</div>

<style>
    .table-kepdirjen-185 {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 0.825rem;
        border-color: #a6a6a6 !important;
    }
    
    .table-kepdirjen-185 th, 
    .table-kepdirjen-185 td {
        border: 1px solid #000000ff !important;
        vertical-align: middle;
    }
    
    .th-vertical {
        white-space: nowrap;
        height: 150px;
        padding: 8px 4px !important;
        text-align: center;
        vertical-align: bottom !important;
    }
    
    .th-vertical > div,
    .th-vertical span.text-vertical {
        writing-mode: vertical-rl;
        transform: scale(-1, -1);
        display: inline-block;
        white-space: nowrap;
        margin: 0 auto;
        font-weight: 700;
        font-size: 0.78rem;
    }

    /* Cell Background Color System */
    .bg-elemen-induk {
        background-color: #d9d9d9 !important; /* Grey background for Elemen row */
        font-weight: 700;
    }

    .bg-sub-elemen {
        background-color: #f2f2f2 !important; /* Light Grey for Sub-Elemen row */
        font-weight: 600;
    }

    .bg-blue-input {
        background-color: #5b9bd5 !important; /* Blue background for Sub-Elemen Actual Score */
        color: #ffffff !important;
        font-weight: 700;
        text-align: center;
    }

    .bg-blue-header {
        background-color: #5b9bd5 !important;
        color: #ffffff !important;
    }

    .bg-green-total {
        background-color: #e2efda !important; /* Light Green for Total Nilai Elemen */
        font-weight: 700;
        text-align: center;
    }

    .bg-green-header {
        background-color: #c6e0b4 !important;
        color: #000000 !important;
    }
</style>

<!-- Detailed Rekap Table per Elemen, Sub-Elemen, & Kriteria (Kepdirjen 185 Format) -->
<div class="card card-custom p-4 mb-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="fw-bold mb-0">
            <i class="bi bi-diagram-3-fill me-2 text-primary"></i>Rekap Rincian Nilai Audit Internal SMKP (Format Kepdirjen 185)
        </h5>
        <button class="btn btn-sm btn-outline-primary rounded-3" id="toggleAllHierarkiBtn">
            <i class="bi bi-arrows-collapse me-1"></i> Buka / Tutup Semua Rincian
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-kepdirjen-185 align-middle mb-0">
            <thead class="table-light align-middle text-uppercase fw-bold text-center">
                <!-- Baris Header 1 -->
                <tr>
                    <th rowspan="2" colspan="3" class="align-middle text-center bg-white text-dark fw-bold">KRITERIA</th>
                    <th rowspan="2" class="th-vertical bg-white text-dark"><div>Nilai Elemen %</div></th>
                    <th rowspan="2" class="th-vertical bg-white text-dark"><div>Nilai Sub Elemen</div></th>
                    <th rowspan="2" class="th-vertical bg-white text-dark"><div>Nilai Sub sub Elemen</div></th>
                    <th colspan="4" class="text-center align-middle bg-white text-dark fw-bold">Nilai Audit</th>
                    <th rowspan="2" class="align-middle text-center bg-white text-dark fw-bold" style="width: 110px;">KETERANGAN</th>
                </tr>
                <!-- Baris Header 2 -->
                <tr>
                    <th class="th-vertical bg-blue-header"><div>Nilai Sub Elemen</div></th>
                    <th class="th-vertical bg-white text-dark"><div>Nilai sub sub elemen</div></th>
                    <th class="th-vertical bg-green-header"><div>Total Nilai Elemen</div></th>
                    <th class="th-vertical bg-white text-dark"><div>Presentase Nilai Elemen</div></th>
                </tr>
            </thead>
            <tbody>
                @foreach($hierarki as $el)
                    <!-- Level 1: Baris Elemen Induk -->
                    <tr class="bg-elemen-induk border-top border-2 border-secondary" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target=".el-collapse-{{ $el['elemen_id'] }}">
                        <td colspan="3" class="text-start">
                            <i class="bi bi-chevron-down toggle-icon me-1 text-primary"></i>
                            ELEMEN {{ $el['kode_elemen'] }} {{ strtoupper($el['nama_elemen']) }}
                        </td>
                        <td class="text-center">{{ number_format($el['bobot'], 2) }}%</td>
                        <td class="text-center">{{ number_format($el['total_nilai_maks_efektif'], 0) }}</td>
                        <td class="text-center text-muted">-</td>
                        <td class="text-center text-muted bg-blue-input" style="opacity: 0.6;">-</td>
                        <td class="text-center text-muted">-</td>
                        <td class="bg-green-total text-center">{{ number_format($el['total_nilai_aktual'], 2) }}</td>
                        <td class="text-center fw-bold">{{ number_format($el['persentase'], 2) }}%</td>
                        <td class="text-center fw-bold small">ELEMEN</td>
                    </tr>

                    <!-- Level 2: Baris Sub-Elemen -->
                    @foreach($el['sub_elemens'] as $sub)
                        <tr class="collapse show el-collapse-{{ $el['elemen_id'] }} bg-sub-elemen" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target=".sub-collapse-{{ $sub['sub_elemen_id'] }}">
                            <td class="text-center font-monospace fw-bold" style="width: 70px;">{{ $sub['kode_sub'] }}</td>
                            <td colspan="2" class="fw-semibold text-slate-800">{{ $sub['nama_sub'] }}</td>
                            <td class="text-center text-muted">-</td>
                            <!-- Cetak Nilai Maksimal HANYA di kolom Nilai Sub Elemen (kolom ke-5) -->
                            <td class="text-center font-monospace fw-bold">{{ number_format($sub['total_nilai_maks_efektif'], 0) }}</td>
                            <td class="text-center text-muted">-</td>
                            <!-- Cetak Nilai Hasil Audit HANYA di kolom Nilai Sub Elemen (kolom ke-7, biru) -->
                            <td class="bg-blue-input font-monospace">{{ number_format($sub['total_nilai_aktual'], 2) }}</td>
                            <td class="text-center text-muted">-</td>
                            <td class="text-center text-muted">-</td>
                            <td class="text-center text-muted">-</td>
                            <td class="text-center small text-secondary fw-semibold">SUB ELEMEN</td>
                        </tr>

                        <!-- Level 3: Baris Sub-sub Elemen / Kriteria Penilaian -->
                        @foreach($sub['details'] as $d)
                            <tr class="collapse show el-collapse-{{ $el['elemen_id'] }} sub-collapse-{{ $sub['sub_elemen_id'] }} bg-white">
                                <td style="width: 70px;"></td>
                                <td class="text-center font-monospace small text-secondary" style="width: 85px;">{{ $d['kode_kriteria'] }}</td>
                                <td class="small text-slate-700 ps-3">
                                    <div>{{ $d['deskripsi'] }}</div>
                                    @if($d['catatan'])
                                        <div class="mt-1 small text-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i>Temuan: {{ $d['catatan'] }}</div>
                                    @endif
                                </td>
                                <td class="text-center text-muted">-</td>
                                <td class="text-center text-muted">-</td>
                                <!-- Cetak Nilai Maksimal HANYA di kolom Nilai Sub-sub Elemen (kolom ke-6) -->
                                <td class="text-center font-monospace small text-muted">{{ number_format($d['nilai_maksimal'], 0) }}</td>
                                <td class="text-center text-muted bg-blue-input" style="opacity: 0.2;">-</td>
                                <!-- Cetak Nilai Hasil Audit HANYA di kolom Nilai Sub-sub Elemen (kolom ke-8) -->
                                <td class="text-center font-monospace fw-bold">
                                    @if($d['is_na'])
                                        <span class="badge bg-secondary">N/A</span>
                                    @else
                                        <span class="{{ $d['nilai'] < $d['nilai_maksimal'] ? 'text-danger' : 'text-success' }}">{{ number_format($d['nilai'], 0) }}</span>
                                    @endif
                                </td>
                                <td class="text-center text-muted">-</td>
                                <td class="text-center text-muted">-</td>
                                <td class="text-center small text-muted">{{ $d['is_na'] ? 'N/A' : 'KRITERIA' }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                @endforeach
            </tbody>
            <tfoot class="table-light">
                <tr class="fw-bold fs-6">
                    <td colspan="8" class="text-end text-uppercase">Total Pencapaian Keseluruhan:</td>
                    <td class="bg-green-total text-center fs-5 text-success">{{ number_format($skorAkhir, 2) }}%</td>
                    <td class="text-center text-primary fs-5">{{ number_format(array_sum(array_column($rekap, 'persentase')) / max(count($rekap), 1), 2) }}%</td>
                    <td class="text-center">TOTAL</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('toggleAllHierarkiBtn');
        let isExpanded = true;

        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                const collapses = document.querySelectorAll('.collapse');
                collapses.forEach(el => {
                    if (isExpanded) {
                        el.classList.remove('show');
                    } else {
                        el.classList.add('show');
                    }
                });
                isExpanded = !isExpanded;
                toggleBtn.innerHTML = isExpanded 
                    ? '<i class="bi bi-arrows-collapse me-1"></i> Tutup Semua Rincian' 
                    : '<i class="bi bi-arrows-expand me-1"></i> Buka Semua Rincian';
            });
        }
    });
</script>
@endpush

<!-- Finding Notes & Proof Attachments List -->
<div class="card card-custom p-4">
    <h5 class="fw-bold mb-3"><i class="bi bi-card-checklist me-2 text-warning"></i>Catatan Temuan & Bukti Lampiran Audit</h5>

    @php
        $findings = $sesi->auditDetails->filter(function($d) {
            return !empty($d->catatan) || !empty($d->lampiran);
        });
    @endphp

    @if($findings->isEmpty())
        <div class="text-center py-4 text-muted">
            <i class="bi bi-check-all fs-2 d-block mb-1 opacity-50"></i>
            Tidak ada catatan temuan atau lampiran khusus pada sesi audit ini.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 100px;">Kriteria</th>
                        <th>Pertanyaan / Kriteria</th>
                        <th class="text-center" style="width: 90px;">Skor</th>
                        <th>Catatan Temuan</th>
                        <th style="width: 160px;">Bukti Lampiran</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($findings as $item)
                        <tr>
                            <td><span class="badge bg-secondary">{{ $item->kriteria->kode_kriteria ?? '-' }}</span></td>
                            <td class="small">{{ $item->kriteria->deskripsi ?? '-' }}</td>
                            <td class="text-center font-monospace fw-bold">
                                @if($item->is_na)
                                    <span class="badge bg-secondary">N/A</span>
                                @else
                                    {{ number_format($item->nilai, 2) }} / {{ number_format($item->kriteria->nilai_maksimal ?? 4, 2) }}
                                @endif
                            </td>
                            <td class="small text-slate-800">{{ $item->catatan ?? '-' }}</td>
                            <td>
                                @if($item->lampiran_url)
                                    <a href="{{ $item->lampiran_url }}" target="_blank" class="btn btn-sm btn-outline-info text-dark py-1 px-2">
                                        <i class="bi bi-paperclip me-1"></i> Lihat Bukti
                                    </a>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
