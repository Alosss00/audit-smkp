@extends('layouts.app')

@section('title', 'Laporan Detail Hasil Audit SMKP — ' . $sesi->area_audit)

@section('content')
<div class="container-fluid px-0">
    <!-- Header Navigation & Action Bar -->
    <div class="card card-custom border-0 mb-4 bg-white p-4">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
            <div>
                <nav aria-label="breadcrumb" class="mb-2">
                    <ol class="breadcrumb mb-0 small">
                        <li class="breadcrumb-item">
                            @if(auth()->user()->role === 'admin')
                                <a href="{{ route('admin.rekap-audit.index') }}" class="text-decoration-none text-muted">Monitoring Audit</a>
                            @else
                                <a href="{{ route('auditor.audit-sesi.index') }}" class="text-decoration-none text-muted">Audit SMKP</a>
                            @endif
                        </li>
                        <li class="breadcrumb-item">
                            @if(auth()->user()->role === 'admin')
                                <a href="{{ route('admin.rekap-audit.show', $sesi->id) }}" class="text-decoration-none text-muted">Detail Rekap</a>
                            @else
                                <a href="{{ route('auditor.audit-sesi.rekap', $sesi->id) }}" class="text-decoration-none text-muted">Rekap Hasil</a>
                            @endif
                        </li>
                        <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Laporan Detail Sesi</li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center gap-2">
                    <div class="stat-icon-box bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center">
                        <i class="bi bi-file-earmark-ruled fs-3"></i>
                    </div>
                    <div>
                        <h2 class="fw-bold text-slate-800 mb-0">Laporan Detail Hasil Audit Internal SMKP</h2>
                        <div class="d-flex flex-wrap align-items-center gap-2 mt-1 text-muted small">
                            <span><i class="bi bi-geo-alt-fill text-danger me-1"></i> Area: <strong>{{ $sesi->area_audit }}</strong></span>
                            <span>&bull;</span>
                            <span><i class="bi bi-calendar3 text-primary me-1"></i> {{ $sesi->tanggal_mulai ? $sesi->tanggal_mulai->format('d M Y') : '-' }} s/d {{ $sesi->tanggal_selesai ? $sesi->tanggal_selesai->format('d M Y') : '-' }}</span>
                            <span>&bull;</span>
                            <span><i class="bi bi-person-badge text-info me-1"></i> Auditor: <strong>{{ $sesi->user->name ?? 'Auditor' }}</strong></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.rekap-audit.show', $sesi->id) }}" class="btn btn-outline-secondary rounded-3 px-3">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Monitoring
                    </a>
                    <a href="{{ route('admin.rekap-audit.export-excel', $sesi->id) }}" class="btn btn-success rounded-3 px-3">
                        <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                    </a>
                    <a href="{{ route('admin.rekap-audit.cetak', $sesi->id) }}" target="_blank" class="btn btn-dark rounded-3 px-3">
                        <i class="bi bi-printer me-1"></i> Cetak Laporan
                    </a>
                @else
                    <a href="{{ route('auditor.audit-sesi.rekap', $sesi->id) }}" class="btn btn-outline-secondary rounded-3 px-3">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Rekap
                    </a>
                    <a href="{{ route('auditor.audit-sesi.export-excel', $sesi->id) }}" class="btn btn-success rounded-3 px-3">
                        <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                    </a>
                    <a href="{{ route('auditor.audit-sesi.cetak', $sesi->id) }}" target="_blank" class="btn btn-dark rounded-3 px-3">
                        <i class="bi bi-printer me-1"></i> Cetak Laporan
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Stat KPI Summary Row -->
    <div class="row g-3 mb-4">
        <!-- Skor Akhir Card -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-custom border-0 p-3 h-100 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Pencapaian SMKP</span>
                    <span class="badge {{ $skorAkhir >= 85 ? 'bg-success' : ($skorAkhir >= 70 ? 'bg-warning text-dark' : 'bg-danger') }} rounded-pill px-2 py-1">
                        {{ $skorAkhir >= 85 ? 'Memuaskan' : ($skorAkhir >= 70 ? 'Cukup' : 'Kurang') }}
                    </span>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <h2 class="fw-bold text-slate-800 mb-0">{{ number_format($skorAkhir, 2) }}%</h2>
                    <small class="text-muted">total tertimbang</small>
                </div>
                <div class="progress mt-3" style="height: 6px;">
                    <div class="progress-bar {{ $skorAkhir >= 85 ? 'bg-success' : ($skorAkhir >= 70 ? 'bg-warning' : 'bg-danger') }}" 
                         role="progressbar" style="width: {{ min(100, $skorAkhir) }}%"></div>
                </div>
            </div>
        </div>

        <!-- Praktik Terbaik Card -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-custom border-0 p-3 h-100 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Praktik Terbaik</span>
                    <div class="stat-icon-box bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; border-radius: 8px;">
                        <i class="bi bi-award-fill"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <h2 class="fw-bold text-success mb-0">{{ count($praktekBaik) }}</h2>
                    <small class="text-muted">sub-elemen 100% patuh</small>
                </div>
                <p class="text-muted small mb-0 mt-2">Memenuhi standar evaluasi penuh</p>
            </div>
        </div>

        <!-- Temuan Mayor & Kritikal Card -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-custom border-0 p-3 h-100 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Temuan Kritis & Mayor</span>
                    <div class="stat-icon-box bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; border-radius: 8px;">
                        <i class="bi bi-exclamation-octagon-fill"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <h2 class="fw-bold text-danger mb-0">{{ count($temuanKategori['kritikal']) + count($temuanKategori['mayor']) }}</h2>
                    <small class="text-muted">temuan ({{ count($temuanKategori['kritikal']) }} Kritikal, {{ count($temuanKategori['mayor']) }} Mayor)</small>
                </div>
                <p class="text-muted small mb-0 mt-2">Prioritas tindak lanjut PICA</p>
            </div>
        </div>

        <!-- Temuan Minor Card -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-custom border-0 p-3 h-100 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Temuan Minor</span>
                    <div class="stat-icon-box bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; border-radius: 8px;">
                        <i class="bi bi-info-circle-fill"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <h2 class="fw-bold text-info mb-0">{{ count($temuanKategori['minor']) }}</h2>
                    <small class="text-muted">ketidaksesuaian minor</small>
                </div>
                <p class="text-muted small mb-0 mt-2">Peluang peningkatan (OFI)</p>
            </div>
        </div>
    </div>

    <!-- Quick Navigation Tab Links -->
    <div class="card card-custom border-0 mb-4 bg-white">
        <div class="card-body p-2">
            <ul class="nav nav-pills nav-fill gap-2" id="reportNavTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active rounded-3 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2" 
                       id="tab-penilaian" data-bs-toggle="tab" href="#section-penilaian" role="tab" aria-selected="true">
                        <i class="bi bi-table"></i> 2.1 Tabel Penilaian Audit
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link rounded-3 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2" 
                       id="tab-praktek" data-bs-toggle="tab" href="#section-praktek" role="tab" aria-selected="false">
                        <i class="bi bi-award"></i> 2.2 Praktik Terbaik ({{ count($praktekBaik) }})
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link rounded-3 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2" 
                       id="tab-temuan" data-bs-toggle="tab" href="#section-temuan" role="tab" aria-selected="false">
                        <i class="bi bi-exclamation-diamond"></i> 2.3 Temuan & Tindak Lanjut PICA ({{ count($temuanKategori['kritikal']) + count($temuanKategori['mayor']) + count($temuanKategori['minor']) }})
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Tab Contents -->
    <div class="tab-content" id="reportNavTabContent">

        <!-- ==========================================
             SECTION 2.1: TABEL HASIL PENILAIAN AUDIT
             ========================================== -->
        <div class="tab-pane fade show active" id="section-penilaian" role="tabpanel" aria-labelledby="tab-penilaian">
            <div class="card card-custom border-0 bg-white mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                    <div>
                        <h4 class="fw-bold text-slate-800 mb-1">2.1 Tabel Hasil Penilaian Audit SMKP</h4>
                        <p class="text-muted small mb-0">Rincian terstruktur per Elemen, Sub-Elemen, dan Kriteria Penilaian Kepdirjen Minerba 185.K/37.04/DJB/2019.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" id="btnExpandAll">
                            <i class="bi bi-arrows-expand me-1"></i> Buka Semua Elemen
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" id="btnCollapseAll">
                            <i class="bi bi-arrows-collapse me-1"></i> Tutup Semua
                        </button>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="accordion" id="accordionElemen">
                        @foreach($hierarki as $idx => $elem)
                            @php
                                $elemPercent = $elem['persentase'];
                                $badgeClass = $elemPercent >= 85 ? 'bg-success' : ($elemPercent >= 70 ? 'bg-warning text-dark' : 'bg-danger');
                            @endphp
                            <div class="accordion-item border rounded-3 mb-3 overflow-hidden shadow-sm">
                                <h2 class="accordion-header" id="heading-{{ $elem['elemen_id'] }}">
                                    <button class="accordion-button {{ $idx > 0 ? 'collapsed' : '' }} bg-light text-slate-800 fw-bold py-3 px-4" 
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $elem['elemen_id'] }}" 
                                            aria-expanded="{{ $idx === 0 ? 'true' : 'false' }}" aria-controls="collapse-{{ $elem['elemen_id'] }}">
                                        <div class="d-flex flex-wrap align-items-center justify-content-between w-100 me-3 gap-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-dark rounded-pill px-3 py-2">Elemen {{ $elem['kode_elemen'] }}</span>
                                                <span class="fs-6 text-slate-900">{{ $elem['nama_elemen'] }}</span>
                                            </div>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="text-end small">
                                                    <span class="text-muted">Bobot:</span> <strong>{{ $elem['bobot'] }}%</strong>
                                                    <span class="text-muted ms-2">Skor:</span> <strong>{{ $elem['nilai_aktual'] }} / {{ $elem['nilai_maks_efektif'] }}</strong>
                                                </div>
                                                <span class="badge {{ $badgeClass }} rounded-pill px-3 py-2 fs-6">
                                                    {{ number_format($elemPercent, 2) }}%
                                                </span>
                                            </div>
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapse-{{ $elem['elemen_id'] }}" 
                                     class="accordion-collapse collapse {{ $idx === 0 ? 'show' : '' }}" 
                                     aria-labelledby="heading-{{ $elem['elemen_id'] }}" 
                                     data-bs-parent="#accordionElemen">
                                    <div class="accordion-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0 matrix-tree-table">
                                                <thead class="table-light text-slate-700 small text-uppercase">
                                                    <tr>
                                                        <th style="width: 120px;" class="ps-4">Kode</th>
                                                        <th>Deskripsi Standar / Kriteria</th>
                                                        <th style="width: 140px;" class="text-center">Skor / Nilai</th>
                                                        <th style="width: 110px;" class="text-center">Pencapaian</th>
                                                        <th style="width: 250px;">Catatan Temuan / Bukti</th>
                                                        <th style="width: 130px;" class="text-center pe-4">PICA / Lampiran</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($elem['sub_elemens'] as $sub)
                                                        @php
                                                            $subPercent = $sub['persentase'];
                                                            $subBadge = $subPercent >= 85 ? 'bg-success' : ($subPercent >= 70 ? 'bg-warning text-dark' : 'bg-danger');
                                                            $isDirect = !empty($sub['is_direct']);
                                                        @endphp

                                                        @if($isDirect)
                                                            @php
                                                                $kri = $sub['direct_detail'] ?? ($sub['kriterias'][0] ?? null);
                                                                $isNa = !empty($kri['is_na']);
                                                                $kriNilai = $kri['nilai_aktual'] ?? ($kri['nilai'] ?? 0);
                                                                $kriMaks = $kri['nilai_maksimal'] ?? 4;
                                                            @endphp
                                                            <!-- Sub-Elemen Penilaian Langsung (1 Baris) -->
                                                            <tr class="{{ $isNa ? 'text-muted bg-light bg-opacity-50' : '' }}">
                                                                <td class="ps-4 text-nowrap">
                                                                    <span class="text-primary fw-semibold">
                                                                        <i class="bi bi-folder2 me-1"></i>{{ $sub['kode_sub_elemen'] }}
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <div class="fw-semibold text-slate-800">{{ $sub['nama_sub_elemen'] }}</div>
                                                                </td>
                                                                <td class="text-center">
                                                                    @if($isNa)
                                                                        <span class="badge bg-secondary rounded-pill px-2 py-1">N/A</span>
                                                                    @else
                                                                        <span class="fw-bold {{ $kriNilai == $kriMaks ? 'text-success' : ($kriNilai == 0 ? 'text-danger' : 'text-primary') }}">
                                                                            {{ $kriNilai }}
                                                                        </span>
                                                                        <span class="text-muted">/ {{ $kriMaks }}</span>
                                                                    @endif
                                                                </td>
                                                                <td class="text-center">
                                                                    @if($isNa)
                                                                        <span class="text-muted small">-</span>
                                                                    @else
                                                                        <span class="badge {{ $subBadge }} rounded-pill px-2 py-1">
                                                                            {{ number_format($subPercent, 1) }}%
                                                                        </span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if(!empty($kri['catatan']))
                                                                        <div class="small text-slate-700" style="max-width: 320px; word-break: break-word;">
                                                                            {!! nl2br(e($kri['catatan'])) !!}
                                                                        </div>
                                                                    @else
                                                                        <span class="text-muted small fst-italic">-</span>
                                                                    @endif
                                                                </td>
                                                                <td class="text-center pe-4">
                                                                    <div class="d-flex flex-column align-items-center gap-1">
                                                                        @if(!empty($kri['has_pica']))
                                                                            @php
                                                                                $kat = $kri['pica_kategori'] ?? 'minor';
                                                                                $picaBadge = $kat === 'kritikal' ? 'bg-danger' : ($kat === 'mayor' ? 'bg-warning text-dark' : 'bg-info text-dark');
                                                                            @endphp
                                                                            <span class="badge {{ $picaBadge }} rounded-pill px-2 py-1 small" title="Temuan PICA">
                                                                                <i class="bi bi-exclamation-triangle-fill me-1"></i> PICA {{ ucfirst($kat) }}
                                                                            </span>
                                                                        @endif

                                                                        @php
                                                                            $lUrls = !empty($kri['lampiran_urls']) 
                                                                                ? $kri['lampiran_urls'] 
                                                                                : (!empty($kri['lampiran_url']) 
                                                                                    ? [$kri['lampiran_url']] 
                                                                                    : (!empty($kri['lampiran']) 
                                                                                        ? (is_array($kri['lampiran']) ? $kri['lampiran'] : (json_decode($kri['lampiran'], true) ?: [$kri['lampiran']])) 
                                                                                        : []));
                                                                        @endphp
                                                                        @foreach($lUrls as $lPath)
                                                                            @php
                                                                                $url = str_starts_with($lPath, 'http') || str_starts_with($lPath, '/storage') ? $lPath : Storage::url($lPath);
                                                                            @endphp
                                                                            <a href="{{ $url }}" target="_blank" class="badge bg-light text-primary border text-decoration-none px-2 py-1 small">
                                                                                <i class="bi bi-paperclip me-1"></i> Bukti Lampiran
                                                                            </a>
                                                                        @endforeach

                                                                        @if(empty($kri['has_pica']) && empty($lUrls))
                                                                            <span class="text-muted small">-</span>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @else
                                                            <!-- Sub-Elemen dengan Beberapa Kriteria (Header Baris Sub-Elemen) -->
                                                            <tr class="table-secondary fw-semibold bg-slate-100">
                                                                <td class="ps-4 text-primary">
                                                                    <i class="bi bi-folder2-open me-1"></i> {{ $sub['kode_sub_elemen'] }}
                                                                </td>
                                                                <td class="text-slate-900">
                                                                    {{ $sub['nama_sub_elemen'] }}
                                                                </td>
                                                                <td class="text-center">
                                                                    <span class="fw-bold">{{ $sub['nilai_aktual'] }}</span> 
                                                                    <span class="text-muted">/ {{ $sub['nilai_maks_efektif'] }}</span>
                                                                </td>
                                                                <td class="text-center">
                                                                    <span class="badge {{ $subBadge }} rounded-pill px-2 py-1">
                                                                        {{ number_format($subPercent, 1) }}%
                                                                    </span>
                                                                </td>
                                                                <td colspan="2" class="text-muted small pe-4">
                                                                    Bobot Sub-Elemen: {{ $sub['bobot'] }}%
                                                                </td>
                                                            </tr>

                                                            <!-- Criteria Child Rows -->
                                                            @foreach($sub['kriterias'] as $kri)
                                                                @php
                                                                    $isNa = !empty($kri['is_na']);
                                                                    $kriNilai = $kri['nilai_aktual'] ?? ($kri['nilai'] ?? 0);
                                                                    $kriMaks = $kri['nilai_maksimal'] ?? 4;
                                                                @endphp
                                                                <tr class="{{ $isNa ? 'text-muted bg-light bg-opacity-50' : '' }}">
                                                                    <td class="ps-4 text-nowrap">
                                                                        <span class="badge bg-light text-dark border px-2 py-1 ms-2">{{ $kri['kode_kriteria'] }}</span>
                                                                    </td>
                                                                    <td>
                                                                        <div class="fw-normal text-slate-800">{{ $kri['nama_kriteria'] }}</div>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        @if($isNa)
                                                                            <span class="badge bg-secondary rounded-pill px-2 py-1">N/A</span>
                                                                        @else
                                                                            <span class="fw-bold {{ $kriNilai == $kriMaks ? 'text-success' : ($kriNilai == 0 ? 'text-danger' : 'text-primary') }}">
                                                                                {{ $kriNilai }}
                                                                            </span>
                                                                            <span class="text-muted">/ {{ $kriMaks }}</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-center">
                                                                        @if($isNa)
                                                                            <span class="text-muted small">-</span>
                                                                        @else
                                                                            @php
                                                                                $kriPct = $kriMaks > 0 ? ($kriNilai / $kriMaks) * 100 : 0;
                                                                            @endphp
                                                                            <span class="badge {{ $kriPct == 100 ? 'bg-success bg-opacity-10 text-success' : ($kriPct == 0 ? 'bg-danger bg-opacity-10 text-danger' : 'bg-warning bg-opacity-10 text-warning') }} rounded-pill px-2">
                                                                                {{ number_format($kriPct, 0) }}%
                                                                            </span>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if(!empty($kri['catatan']))
                                                                            <div class="small text-slate-700" style="max-width: 320px; word-break: break-word;">
                                                                                {!! nl2br(e($kri['catatan'])) !!}
                                                                            </div>
                                                                        @else
                                                                            <span class="text-muted small fst-italic">-</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-center pe-4">
                                                                        <div class="d-flex flex-column align-items-center gap-1">
                                                                            @if(!empty($kri['has_pica']))
                                                                                @php
                                                                                    $kat = $kri['pica_kategori'] ?? 'minor';
                                                                                    $picaBadge = $kat === 'kritikal' ? 'bg-danger' : ($kat === 'mayor' ? 'bg-warning text-dark' : 'bg-info text-dark');
                                                                                @endphp
                                                                                <span class="badge {{ $picaBadge }} rounded-pill px-2 py-1 small" title="Temuan PICA">
                                                                                    <i class="bi bi-exclamation-triangle-fill me-1"></i> PICA {{ ucfirst($kat) }}
                                                                                </span>
                                                                            @endif

                                                                            @php
                                                                                $lUrls = !empty($kri['lampiran_urls']) 
                                                                                    ? $kri['lampiran_urls'] 
                                                                                    : (!empty($kri['lampiran_url']) 
                                                                                        ? [$kri['lampiran_url']] 
                                                                                        : (!empty($kri['lampiran']) 
                                                                                            ? (is_array($kri['lampiran']) ? $kri['lampiran'] : (json_decode($kri['lampiran'], true) ?: [$kri['lampiran']])) 
                                                                                            : []));
                                                                            @endphp
                                                                            @foreach($lUrls as $lPath)
                                                                                @php
                                                                                    $url = str_starts_with($lPath, 'http') || str_starts_with($lPath, '/storage') ? $lPath : Storage::url($lPath);
                                                                                @endphp
                                                                                <a href="{{ $url }}" target="_blank" class="badge bg-light text-primary border text-decoration-none px-2 py-1 small">
                                                                                    <i class="bi bi-paperclip me-1"></i> Bukti Lampiran
                                                                                </a>
                                                                            @endforeach

                                                                            @if(empty($kri['has_pica']) && empty($lUrls))
                                                                                <span class="text-muted small">-</span>
                                                                            @endif
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- ==========================================
             SECTION 2.2: DAFTAR SUB-ELEMEN PRAKTIK TERBAIK
             ========================================== -->
        <div class="tab-pane fade" id="section-praktek" role="tabpanel" aria-labelledby="tab-praktek">
            <div class="card card-custom border-0 bg-white mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <div class="d-flex align-items-center gap-2">
                        <div class="stat-icon-box bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; border-radius: 12px;">
                            <i class="bi bi-award-fill fs-4"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold text-slate-800 mb-1">2.2 Daftar Sub-Elemen Praktik Terbaik (Best Practices)</h4>
                            <p class="text-muted small mb-0">Daftar Sub-Elemen yang telah mencapai tingkat kepatuhan sempurna (100% dari total nilai maksimal efektif).</p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    @if($praktekBaik->isEmpty())
                        <div class="text-center py-5">
                            <div class="stat-icon-box bg-light text-muted mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px; border-radius: 50%;">
                                <i class="bi bi-inbox fs-2"></i>
                            </div>
                            <h5 class="fw-bold text-slate-700">Belum Ada Praktik Terbaik 100%</h5>
                            <p class="text-muted small mb-0">Belum ada sub-elemen yang mencapai kepatuhan 100% pada sesi audit ini.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 matrix-tree-table">
                                <thead class="table-light text-slate-700 small text-uppercase">
                                    <tr>
                                        <th style="width: 50px;" class="ps-3 text-center">No</th>
                                        <th style="width: 130px;">Kode Sub</th>
                                        <th style="min-width: 250px;">Sub-Elemen SMKP</th>
                                        <th style="min-width: 180px;">Elemen Induk</th>
                                        <th style="width: 130px;" class="text-center">Skor / Nilai</th>
                                        <th style="width: 120px;" class="text-center">Pencapaian</th>
                                        <th style="min-width: 280px;" class="pe-3">Catatan Evaluasi / Bukti Praktik Baik</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($praktekBaik as $idx => $item)
                                        <tr>
                                            <td class="ps-3 text-center text-muted small fw-semibold">{{ $idx + 1 }}</td>
                                            <td class="text-nowrap">
                                                <span class="badge bg-light text-primary border px-2 py-1 fw-bold">
                                                    <i class="bi bi-folder2 me-1"></i>{{ $item['kode_sub_elemen'] ?? $item['kode_sub'] }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="fw-semibold text-slate-800">{{ $item['nama_sub_elemen'] ?? $item['nama_sub'] }}</div>
                                            </td>
                                            <td>
                                                <div class="d-inline-flex align-items-center px-2.5 py-1 rounded-2 bg-light border small fw-semibold text-dark">
                                                    <i class="bi bi-layers-fill text-primary me-1.5"></i>
                                                    <span class="text-dark">{{ $item['nama_elemen'] ?? $item['elemen_nama'] }}</span>
                                                </div>
                                            </td>
                                            <td class="text-center font-monospace">
                                                <strong class="text-success">{{ $item['nilai_aktual'] }}</strong> 
                                                <span class="text-muted">/ {{ $item['nilai_maks_efektif'] ?? $item['nilai_maks'] }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-success bg-opacity-10 text-success fw-bold rounded-pill px-3 py-1">
                                                    <i class="bi bi-check2-circle me-1"></i> 100%
                                                </span>
                                            </td>
                                            <td class="pe-3">
                                                @if(is_array($item['catatan']))
                                                    <ul class="ps-3 mb-0 text-slate-700 small">
                                                        @foreach($item['catatan'] as $ct)
                                                            <li>{{ $ct }}</li>
                                                        @endforeach
                                                    </ul>
                                                @elseif(!empty($item['catatan']))
                                                    <div class="small text-slate-700">
                                                        {!! nl2br(e($item['catatan'])) !!}
                                                    </div>
                                                @else
                                                    <span class="text-muted small fst-italic">Kesesuaian penuh memenuhi standar evaluasi SMKP Minerba Kepdirjen 185.</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- ==========================================
             SECTION 2.3: DAFTAR TEMUAN KETIDAKSESUAIAN & PICA
             ========================================== -->
        <div class="tab-pane fade" id="section-temuan" role="tabpanel" aria-labelledby="tab-temuan">
            <div class="card card-custom border-0 bg-white mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <div class="d-flex align-items-center gap-2">
                        <div class="stat-icon-box bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; border-radius: 12px;">
                            <i class="bi bi-tools fs-4"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold text-slate-800 mb-1">2.3 Daftar Temuan Ketidaksesuaian & Tindak Lanjut PICA</h4>
                            <p class="text-muted small mb-0">Rincian ketidaksesuaian hasil audit yang diklasifikasikan berdasarkan tingkat keparahan (Kritikal, Mayor, Minor) serta status rencana tindak lanjut perbaikannya.</p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">

                    <!-- Sub-section 1: Temuan Kritikal -->
                    <div class="mb-5">
                        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                            <h5 class="fw-bold text-danger mb-0 d-flex align-items-center gap-2">
                                <i class="bi bi-exclamation-octagon-fill"></i> Temuan Kritikal
                                <span class="badge bg-danger rounded-pill px-3 py-1 fs-6">{{ count($temuanKategori['kritikal']) }}</span>
                            </h5>
                            <small class="text-muted">Kondisi berbahaya fatal atau pelanggaran regulasi mutlak</small>
                        </div>

                        @if($temuanKategori['kritikal']->isEmpty())
                            <div class="alert alert-light border text-muted small rounded-3 py-3 px-4 d-flex align-items-center gap-2">
                                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                <span>Tidak ditemukan ketidaksesuaian kategori <strong>Kritikal</strong> pada sesi audit ini.</span>
                            </div>
                        @else
                            @include('laporan._pica_table', ['items' => $temuanKategori['kritikal'], 'isReadOnly' => $isReadOnly])
                        @endif
                    </div>

                    <!-- Sub-section 2: Temuan Mayor -->
                    <div class="mb-5">
                        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                            <h5 class="fw-bold text-warning-emphasis mb-0 d-flex align-items-center gap-2">
                                <i class="bi bi-exclamation-triangle-fill text-warning"></i> Temuan Mayor
                                <span class="badge bg-warning text-dark rounded-pill px-3 py-1 fs-6">{{ count($temuanKategori['mayor']) }}</span>
                            </h5>
                            <small class="text-muted">Kepatuhan sub-elemen &lt; 50% atau tidak terpenuhinya klausul wajib</small>
                        </div>

                        @if($temuanKategori['mayor']->isEmpty())
                            <div class="alert alert-light border text-muted small rounded-3 py-3 px-4 d-flex align-items-center gap-2">
                                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                <span>Tidak ditemukan ketidaksesuaian kategori <strong>Mayor</strong> pada sesi audit ini.</span>
                            </div>
                        @else
                            @include('laporan._pica_table', ['items' => $temuanKategori['mayor'], 'isReadOnly' => $isReadOnly])
                        @endif
                    </div>

                    <!-- Sub-section 3: Temuan Minor -->
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                            <h5 class="fw-bold text-info-emphasis mb-0 d-flex align-items-center gap-2">
                                <i class="bi bi-info-circle-fill text-info"></i> Temuan Minor
                                <span class="badge bg-info text-dark rounded-pill px-3 py-1 fs-6">{{ count($temuanKategori['minor']) }}</span>
                            </h5>
                            <small class="text-muted">Ketidaksesuaian administratif atau tidak sistemik</small>
                        </div>

                        @if($temuanKategori['minor']->isEmpty())
                            <div class="alert alert-light border text-muted small rounded-3 py-3 px-4 d-flex align-items-center gap-2">
                                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                <span>Tidak ditemukan ketidaksesuaian kategori <strong>Minor</strong> pada sesi audit ini.</span>
                            </div>
                        @else
                            @include('laporan._pica_table', ['items' => $temuanKategori['minor'], 'isReadOnly' => $isReadOnly])
                        @endif
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<!-- Accordion Toggle Helper Script with CSP Nonce -->
<script nonce="{{ $cspNonce ?? '' }}">
    document.addEventListener('DOMContentLoaded', function() {
        function toggleAllAccordions(open) {
            const accordionItems = document.querySelectorAll('#accordionElemen .accordion-collapse');
            accordionItems.forEach(item => {
                const bsCollapse = bootstrap.Collapse.getOrCreateInstance(item, { toggle: false });
                if (open) {
                    bsCollapse.show();
                } else {
                    bsCollapse.hide();
                }
            });
        }

        const btnExpand = document.getElementById('btnExpandAll');
        if (btnExpand) {
            btnExpand.addEventListener('click', function() {
                toggleAllAccordions(true);
            });
        }

        const btnCollapse = document.getElementById('btnCollapseAll');
        if (btnCollapse) {
            btnCollapse.addEventListener('click', function() {
                toggleAllAccordions(false);
            });
        }
    });
</script>

<style>
    .matrix-tree-table {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 0.85rem;
    }
    .matrix-tree-table th {
        font-weight: 600;
        background-color: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
    }
    .matrix-tree-table td {
        vertical-align: middle;
        padding-top: 0.65rem;
        padding-bottom: 0.65rem;
    }
</style>
@endsection
