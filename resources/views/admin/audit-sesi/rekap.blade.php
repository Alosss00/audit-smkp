@extends('layouts.app')

@section('title', 'Rekap Hasil Audit SMKP — Admin')

@section('content')
<div class="mb-4 d-flex align-items-center justify-content-between">
    <div>
        <a href="{{ route('admin.audit-sesi.index') }}" class="text-decoration-none text-muted small">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Audit Saya
        </a>
        <h2 class="fw-bold text-slate-800 mb-0 mt-1">Rekap Hasil Audit Internal SMKP</h2>
        <p class="text-muted small mb-0">Area: <strong>{{ $sesi->area_audit }}</strong> | Periode: <strong>{{ $sesi->tanggal_mulai->format('d M Y') }} - {{ $sesi->tanggal_selesai->format('d M Y') }}</strong></p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.audit-sesi.export-excel', $sesi->id) }}" class="btn btn-success rounded-3 px-3">
            <i class="bi bi-file-earmark-excel me-1"></i> Export Excel (.xlsx)
        </a>
        <a href="{{ route('admin.audit-sesi.cetak', $sesi->id) }}" target="_blank" class="btn btn-dark rounded-3 px-3">
            <i class="bi bi-printer me-1"></i> Cetak Laporan (PDF)
        </a>
        <a href="{{ route('admin.audit-sesi.matrix', $sesi->id) }}" class="btn btn-outline-primary rounded-3">
            <i class="bi bi-pencil-square me-1"></i> Form Matriks
        </a>
        @if($sesi->status !== 'selesai')
            <form action="{{ route('admin.audit-sesi.finalisasi', $sesi->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin memfinalisasi sesi audit ini? Sesi yang sudah selesai tidak dapat diubah lagi.')">
                @csrf
                <button type="submit" class="btn btn-success rounded-3 fw-bold">
                    <i class="bi bi-check-circle-fill me-1"></i> Finalisasi Sesi Audit
                </button>
            </form>
        @else
            <span class="badge bg-success p-2 rounded-3 fs-6"><i class="bi bi-lock-fill me-1"></i> Sesi Selesai (Terkunci)</span>
        @endif
    </div>
</div>

<!-- Score Overview Card -->
<div class="row g-4 mb-4">
    <div class="col-md-6 col-lg-8">
        <div class="card card-custom p-4 h-100 border-start border-5 border-info">
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
                    <span class="badge bg-success">Selesai (Snapshot Locked)</span>
                @endif
            </div>
            <div>
                <small class="text-light opacity-50 d-block">Terakhir Diperbarui:</small>
                <span class="text-white">{{ $sesi->updated_at->format('d M Y H:i') }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Rekap Table per Elemen -->
<div class="card card-custom p-4 mb-4">
    <h5 class="fw-bold mb-3"><i class="bi bi-table me-2 text-primary"></i>Rincian Nilai per Elemen</h5>

    <div class="table-responsive">
        <table class="table table-bordered align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th style="width: 100px;">Elemen</th>
                    <th>Nama Elemen</th>
                    <th class="text-center">Total Aktual</th>
                    <th class="text-center">Total Maks Efektif</th>
                    <th class="text-center">Persentase (%)</th>
                    <th class="text-center">Bobot Elemen</th>
                    <th class="text-center">Skor Akhir Elemen</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rekap as $row)
                    <tr>
                        <td class="fw-bold text-center">Elemen {{ $row['kode_elemen'] }}</td>
                        <td class="fw-semibold">{{ $row['nama_elemen'] }}</td>
                        <td class="text-center fw-bold text-slate-800">{{ number_format($row['total_nilai_aktual'], 2) }}</td>
                        <td class="text-center text-muted">{{ number_format($row['total_nilai_maks_efektif'], 2) }}</td>
                        <td class="text-center">
                            <span class="badge bg-info text-dark font-monospace fs-6 py-1 px-3">
                                {{ number_format($row['persentase'], 2) }}%
                            </span>
                        </td>
                        <td class="text-center text-muted">{{ number_format($row['bobot'], 2) }}%</td>
                        <td class="text-center fw-bold text-primary fs-6">{{ number_format($row['skor_elemen'], 2) }}%</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="table-light">
                <tr class="fw-bold fs-6">
                    <td colspan="4" class="text-end text-uppercase">Total Pencapaian Keseluruhan:</td>
                    <td class="text-center text-primary fs-5">{{ number_format(array_sum(array_column($rekap, 'persentase')) / max(count($rekap), 1), 2) }}%</td>
                    <td class="text-center">{{ number_format(array_sum(array_column($rekap, 'bobot')), 2) }}%</td>
                    <td class="text-center text-success fs-5">{{ number_format($skorAkhir, 2) }}%</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

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

