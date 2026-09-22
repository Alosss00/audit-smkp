@extends('layouts.app')

@section('title', 'Buat Sesi Audit Baru — Admin SMKP Minerba')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-7">
        <div class="mb-3">
            <a href="{{ route('admin.audit-sesi.index') }}" class="text-decoration-none text-muted small">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Sesi Audit
            </a>
        </div>
        <div class="card card-custom p-4 p-md-5">
            <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-3">
                <div class="stat-icon-box bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-journal-plus"></i>
                </div>
                <div>
                    <h4 class="fw-bold text-slate-800 mb-0">Buat Sesi Audit Baru</h4>
                    <p class="text-muted small mb-0">Inisialisasi form matriks penilaian SMKP Kepdirjen 185</p>
                </div>
            </div>

            @if($errors->any())
                <div class="alert alert-danger rounded-3 mb-4">
                    <ul class="mb-0 small ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.audit-sesi.store') }}" method="POST">
                @csrf

                <!-- 1. Pilihan Tahun Periode Audit (Tahunan) -->
                <div class="mb-4">
                    <label for="tahun_periode" class="form-label fw-semibold small text-secondary">
                        Tahun Periode Audit <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-calendar-check text-primary"></i></span>
                        @php
                            $currentYear = (int) date('Y');
                            $selectedYear = (int) old('tahun_periode', $currentYear);
                            $yearRange = range($currentYear - 3, $currentYear + 4);
                        @endphp
                        <select name="tahun_periode" id="tahun_periode" class="form-select fw-semibold" required>
                            @foreach($yearRange as $year)
                                <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                    Tahun {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex align-items-center gap-2 mt-2 p-2 px-3 bg-light rounded-3 border">
                        <i class="bi bi-info-circle text-primary"></i>
                        <span class="small text-muted">
                            Masa Periode Evaluasi: <strong id="periode_range_text" class="text-slate-800">1 Januari {{ $selectedYear }} – 31 Desember {{ $selectedYear }}</strong>
                        </span>
                    </div>
                </div>

                <!-- 2. Pilih Perusahaan Area Audit -->
                <div class="mb-4">
                    <label for="perusahaan_id" class="form-label fw-semibold small text-secondary">
                        Pilih Perusahaan Area Audit <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-building text-primary"></i></span>
                        <select name="perusahaan_id" id="perusahaan_id" class="form-select select-searchable fw-semibold" placeholder="-- Cari nama perusahaan --" required>
                            <option value="">-- Pilih Perusahaan Ter-audit --</option>
                            @foreach($perusahaans as $comp)
                                <option value="{{ $comp->id }}" {{ (old('perusahaan_id') == $comp->id || old('area_selection') == 'p:'.$comp->id) ? 'selected' : '' }}>
                                    {{ $comp->nama_perusahaan }} @if($comp->kategori) ({{ $comp->kategori }}) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- 3. Jadwal Pelaksanaan Sesi Audit -->
                <div class="p-3 bg-light rounded-3 border mb-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-calendar-event text-danger"></i>
                        <span class="fw-bold small text-slate-800">Jadwal Pelaksanaan Sesi Audit</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="tanggal_mulai" class="form-label fw-semibold small text-secondary">
                                Tanggal Mulai Sesi <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-calendar3"></i></span>
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" value="{{ old('tanggal_mulai', date('Y-m-d')) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="tanggal_selesai" class="form-label fw-semibold small text-secondary">
                                Tanggal Selesai Sesi <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-calendar3"></i></span>
                                <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" value="{{ old('tanggal_selesai', date('Y-m-d')) }}" required>
                            </div>
                        </div>
                    </div>
                    <small class="text-muted d-block mt-2">
                        * Rentang tanggal pelaksanaan verifikasi lapangan / tatap muka audit oleh tim auditor.
                    </small>
                </div>

                <!-- Tombol Aksi -->
                <div class="d-flex align-items-center justify-content-end gap-2 mt-4 pt-3 border-top">
                    <a href="{{ route('admin.audit-sesi.index') }}" class="btn btn-outline-secondary rounded-3 px-4">Batal</a>
                    <button type="submit" class="btn btn-danger rounded-3 px-4">
                        <i class="bi bi-arrow-right-circle me-1"></i> Lanjut Isi Matriks
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tahunSelect = document.getElementById('tahun_periode');
    const rangeText = document.getElementById('periode_range_text');

    function updateRangeText() {
        if (tahunSelect && rangeText) {
            const year = tahunSelect.value;
            rangeText.textContent = `1 Januari ${year} – 31 Desember ${year}`;
        }
    }

    if (tahunSelect) {
        tahunSelect.addEventListener('change', updateRangeText);
    }
});
</script>
@endsection
