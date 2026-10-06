<?php
$title = 'Laporan Detail Hasil Audit SMKP — ' . $sesi->area_audit;
ob_start();
?>
<div class="container-fluid px-0">
    <!-- Header Navigation & Action Bar -->
    <div class="card card-custom border-0 mb-4 bg-white p-4">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
            <div>
                <nav aria-label="breadcrumb" class="mb-2">
                    <ol class="breadcrumb mb-0 small">
                        <li class="breadcrumb-item">
                            <?php if(auth()->user()->role === 'admin'): ?>
                                <a href="<?php echo e(route('admin.rekap-audit.index')); ?>" class="text-decoration-none text-muted">Monitoring Audit</a>
                            <?php else: ?>
                                <a href="<?php echo e(route('auditor.audit-sesi.index')); ?>" class="text-decoration-none text-muted">Audit SMKP</a>
                            <?php endif; ?>
                        </li>
                        <li class="breadcrumb-item">
                            <?php if(auth()->user()->role === 'admin'): ?>
                                <a href="<?php echo e(route('admin.rekap-audit.show', $sesi->id)); ?>" class="text-decoration-none text-muted">Detail Rekap</a>
                            <?php else: ?>
                                <a href="<?php echo e(route('auditor.audit-sesi.rekap', $sesi->id)); ?>" class="text-decoration-none text-muted">Rekap Hasil</a>
                            <?php endif; ?>
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
                            <span><i class="bi bi-building text-primary me-1"></i> Perusahaan: <strong><?php echo e($sesi->perusahaan->nama_perusahaan ?? $sesi->area_audit); ?></strong></span>
                            <span>&bull;</span>
                            <span><i class="bi bi-calendar-check text-success me-1"></i> Periode Evaluasi: 
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 fw-bold ms-1">
                                    <?php echo e(($selectedYear ?? 'semua') === 'semua' ? 'Semua Periode Tahun (Akumulasi Total Perusahaan)' : 'Tahun Periode ' . $selectedYear); ?>

                                </span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <form method="GET" action="" class="d-flex align-items-center gap-2 me-lg-2">
                    <label for="selectFilterTahun" class="text-muted small fw-semibold text-nowrap mb-0 d-none d-sm-inline">
                        <i class="bi bi-filter-circle text-primary me-1"></i>Filter Periode Tahun:
                    </label>
                    <select name="tahun_periode" id="selectFilterTahun" class="form-select form-select-sm rounded-3 border-primary-subtle shadow-sm" style="min-width: 240px;" data-auto-submit="true">
                        <option value="semua" <?php echo e(($selectedYear ?? 'semua') === 'semua' ? 'selected' : ''); ?>>
                            Semua Periode Tahun (Akumulasi Total)
                        </option>
                        <?php if(!empty($availableYears)): ?>
                            <?php foreach($availableYears as $yr): ?>
                                <option value="<?php echo e($yr); ?>" <?php echo e(($selectedYear ?? '') == $yr ? 'selected' : ''); ?>>
                                    Tahun Periode <?php echo e($yr); ?>

                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </form>

                <?php if(auth()->user()->role === 'admin'): ?>
                    <a href="<?php echo e(route('admin.rekap-audit.show', $sesi->id)); ?>" class="btn btn-outline-secondary rounded-3 px-3">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Monitoring
                    </a>
                    <a href="<?php echo e(route('admin.rekap-audit.export-excel', $sesi->id)); ?>" class="btn btn-success rounded-3 px-3">
                        <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                    </a>
                    <a href="<?php echo e(route('admin.rekap-audit.cetak', $sesi->id)); ?>" target="_blank" class="btn btn-dark rounded-3 px-3">
                        <i class="bi bi-printer me-1"></i> Cetak Laporan
                    </a>
                <?php else: ?>
                    <a href="<?php echo e(route('auditor.audit-sesi.rekap', $sesi->id)); ?>" class="btn btn-outline-secondary rounded-3 px-3">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Rekap
                    </a>
                    <a href="<?php echo e(route('auditor.audit-sesi.export-excel', $sesi->id)); ?>" class="btn btn-success rounded-3 px-3">
                        <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                    </a>
                    <a href="<?php echo e(route('auditor.audit-sesi.cetak', $sesi->id)); ?>" target="_blank" class="btn btn-dark rounded-3 px-3">
                        <i class="bi bi-printer me-1"></i> Cetak Laporan
                    </a>
                <?php endif; ?>
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
                    <span class="badge <?php echo e($skorAkhir >= 85 ? 'bg-success' : ($skorAkhir >= 70 ? 'bg-warning text-dark' : 'bg-danger')); ?> rounded-pill px-2 py-1">
                        <?php echo e($skorAkhir >= 85 ? 'Memuaskan' : ($skorAkhir >= 70 ? 'Cukup' : 'Kurang')); ?>

                    </span>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <h2 class="fw-bold text-slate-800 mb-0"><?php echo e(number_format($skorAkhir, 2)); ?>%</h2>
                    <small class="text-muted">total tertimbang</small>
                </div>
                <div class="progress mt-3" style="height: 6px;">
                    <div class="progress-bar <?php echo e($skorAkhir >= 85 ? 'bg-success' : ($skorAkhir >= 70 ? 'bg-warning' : 'bg-danger')); ?>" 
                         role="progressbar" style="width: <?php echo e(min(100, $skorAkhir)); ?>%"></div>
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
                    <h2 class="fw-bold text-success mb-0"><?php echo e(count($praktekBaik)); ?></h2>
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
                    <h2 class="fw-bold text-danger mb-0"><?php echo e(($temuanKategori['kritikal_count'] ?? 0) + ($temuanKategori['mayor_count'] ?? 0)); ?></h2>
                    <small class="text-muted">sub-elemen temuan</small>
                </div>
                <p class="text-muted small mb-0 mt-2">
                    <?php echo e(count($temuanKategori['kritikal']) + count($temuanKategori['mayor'])); ?> tindakan koreksi kriteria
                </p>
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
                    <h2 class="fw-bold text-info mb-0"><?php echo e($temuanKategori['minor_count'] ?? 0); ?></h2>
                    <small class="text-muted">sub-elemen temuan</small>
                </div>
                <p class="text-muted small mb-0 mt-2">
                    <?php echo e(count($temuanKategori['minor'])); ?> tindakan koreksi kriteria
                </p>
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
                       id="tab-grafik" data-bs-toggle="tab" href="#section-grafik" role="tab" aria-selected="false">
                        <i class="bi bi-bar-chart-line-fill text-primary"></i> 2.2 Grafik Akumulasi Audit
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link rounded-3 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2" 
                       id="tab-praktek" data-bs-toggle="tab" href="#section-praktek" role="tab" aria-selected="false">
                        <i class="bi bi-award"></i> 2.3 Praktik Terbaik (<?php echo e(count($praktekBaik)); ?>)
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link rounded-3 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2" 
                       id="tab-temuan" data-bs-toggle="tab" href="#section-temuan" role="tab" aria-selected="false">
                        <i class="bi bi-exclamation-diamond"></i> 2.4 Temuan Ketidaksesuaian (<?php echo e($temuanKategori['total_temuan'] ?? count($temuanKategori['kritikal']) + count($temuanKategori['mayor']) + count($temuanKategori['minor'])); ?>)
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
                        <?php foreach($hierarki as $idx => $elem): ?>
                            <?php
                                $elemPercent = $elem['persentase'];
                                $badgeClass = $elemPercent >= 85 ? 'bg-success' : ($elemPercent >= 70 ? 'bg-warning text-dark' : 'bg-danger');
                            ?>
                            <div class="accordion-item border rounded-3 mb-3 overflow-hidden shadow-sm">
                                <h2 class="accordion-header" id="heading-<?php echo e($elem['elemen_id']); ?>">
                                    <button class="accordion-button <?php echo e($idx > 0 ? 'collapsed' : ''); ?> bg-light text-slate-800 fw-bold py-3 px-4" 
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo e($elem['elemen_id']); ?>" 
                                            aria-expanded="<?php echo e($idx === 0 ? 'true' : 'false'); ?>" aria-controls="collapse-<?php echo e($elem['elemen_id']); ?>">
                                        <div class="d-flex flex-wrap align-items-center justify-content-between w-100 me-3 gap-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-dark rounded-pill px-3 py-2">Elemen <?php echo e($elem['kode_elemen']); ?></span>
                                                <span class="fs-6 text-slate-900"><?php echo e($elem['nama_elemen']); ?></span>
                                            </div>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="text-end small">
                                                    <span class="text-muted">Bobot:</span> <strong><?php echo e($elem['bobot']); ?>%</strong>
                                                    <span class="text-muted ms-2">Skor:</span> <strong><?php echo e($elem['nilai_aktual']); ?> / <?php echo e($elem['nilai_maks_efektif']); ?></strong>
                                                </div>
                                                <span class="badge <?php echo e($badgeClass); ?> rounded-pill px-3 py-2 fs-6">
                                                    <?php echo e(number_format($elemPercent, 2)); ?>%
                                                </span>
                                            </div>
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapse-<?php echo e($elem['elemen_id']); ?>" 
                                     class="accordion-collapse collapse <?php echo e($idx === 0 ? 'show' : ''); ?>" 
                                     aria-labelledby="heading-<?php echo e($elem['elemen_id']); ?>" 
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
                                                    <?php foreach($elem['sub_elemens'] as $sub): ?>
                                                        <?php
                                                            $subPercent = $sub['persentase'];
                                                            $subBadge = $subPercent >= 85 ? 'bg-success' : ($subPercent >= 70 ? 'bg-warning text-dark' : 'bg-danger');
                                                            $isDirect = !empty($sub['is_direct']);
                                                        ?>

                                                        <?php if($isDirect): ?>
                                                            <?php
                                                                $kri = $sub['direct_detail'] ?? ($sub['kriterias'][0] ?? null);
                                                                $isNa = !empty($kri['is_na']);
                                                                $kriNilai = $kri['nilai_aktual'] ?? ($kri['nilai'] ?? 0);
                                                                $kriMaks = $kri['nilai_maksimal'] ?? 4;
                                                            ?>
                                                            <!-- Sub-Elemen Penilaian Langsung (1 Baris) -->
                                                            <tr class="<?php echo e($isNa ? 'text-muted bg-light bg-opacity-50' : ''); ?>">
                                                                <td class="ps-4 text-nowrap">
                                                                    <span class="text-primary fw-semibold">
                                                                        <i class="bi bi-folder2 me-1"></i><?php echo e($sub['kode_sub_elemen']); ?>

                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <div class="fw-semibold text-slate-800"><?php echo e($sub['nama_sub_elemen']); ?></div>
                                                                </td>
                                                                <td class="text-center">
                                                                    <?php if($isNa): ?>
                                                                        <span class="badge bg-secondary rounded-pill px-2 py-1">N/A</span>
                                                                    <?php else: ?>
                                                                        <span class="fw-bold <?php echo e($kriNilai == $kriMaks ? 'text-success' : ($kriNilai == 0 ? 'text-danger' : 'text-primary')); ?>">
                                                                            <?php echo e($kriNilai); ?>

                                                                        </span>
                                                                        <span class="text-muted">/ <?php echo e($kriMaks); ?></span>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td class="text-center">
                                                                    <?php if($isNa): ?>
                                                                        <span class="text-muted small">-</span>
                                                                    <?php else: ?>
                                                                        <span class="badge <?php echo e($subBadge); ?> rounded-pill px-2 py-1">
                                                                            <?php echo e(number_format($subPercent, 1)); ?>%
                                                                        </span>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td>
                                                                    <?php if(!empty($kri['catatan'])): ?>
                                                                        <div class="small text-slate-700" style="max-width: 320px; word-break: break-word;">
                                                                            <?php echo nl2br(e($kri['catatan'])); ?>

                                                                        </div>
                                                                    <?php else: ?>
                                                                        <span class="text-muted small fst-italic">-</span>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td class="text-center pe-4">
                                                                    <div class="d-flex flex-column align-items-center gap-1">
                                                                        <?php if(!empty($kri['has_pica'])): ?>
                                                                            <?php
                                                                                $kat = $kri['pica_kategori'] ?? 'minor';
                                                                                $picaBadge = $kat === 'kritikal' ? 'bg-danger' : ($kat === 'mayor' ? 'bg-warning text-dark' : 'bg-info text-dark');
                                                                            ?>
                                                                            <span class="badge <?php echo e($picaBadge); ?> rounded-pill px-2 py-1 small" title="Temuan PICA">
                                                                                <i class="bi bi-exclamation-triangle-fill me-1"></i> PICA <?php echo e(ucfirst($kat)); ?>

                                                                            </span>
                                                                        <?php endif; ?>

                                                                        <?php
                                                                            $lUrls = !empty($kri['lampiran_urls']) 
                                                                                ? $kri['lampiran_urls'] 
                                                                                : (!empty($kri['lampiran_url']) 
                                                                                    ? [$kri['lampiran_url']] 
                                                                                    : (!empty($kri['lampiran']) 
                                                                                        ? (is_array($kri['lampiran']) ? $kri['lampiran'] : (json_decode($kri['lampiran'], true) ?: [$kri['lampiran']])) 
                                                                                        : []));
                                                                        ?>
                                                                        <?php foreach($lUrls as $lPath): ?>
                                                                            <?php
                                                                                $url = str_starts_with($lPath, 'http') || str_starts_with($lPath, '/storage') ? $lPath : Storage::url($lPath);
                                                                            ?>
                                                                            <a href="<?php echo e($url); ?>" target="_blank" class="badge bg-light text-primary border text-decoration-none px-2 py-1 small">
                                                                                <i class="bi bi-paperclip me-1"></i> Bukti Lampiran
                                                                            </a>
                                                                        <?php endforeach; ?>

                                                                        <?php if(empty($kri['has_pica']) && empty($lUrls)): ?>
                                                                            <span class="text-muted small">-</span>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php else: ?>
                                                            <!-- Sub-Elemen dengan Beberapa Kriteria (Header Baris Sub-Elemen) -->
                                                            <tr class="table-secondary fw-semibold bg-slate-100">
                                                                <td class="ps-4 text-primary">
                                                                    <i class="bi bi-folder2-open me-1"></i> <?php echo e($sub['kode_sub_elemen']); ?>

                                                                </td>
                                                                <td class="text-slate-900">
                                                                    <?php echo e($sub['nama_sub_elemen']); ?>

                                                                </td>
                                                                <td class="text-center">
                                                                    <span class="fw-bold"><?php echo e($sub['nilai_aktual']); ?></span> 
                                                                    <span class="text-muted">/ <?php echo e($sub['nilai_maks_efektif']); ?></span>
                                                                </td>
                                                                <td class="text-center">
                                                                    <span class="badge <?php echo e($subBadge); ?> rounded-pill px-2 py-1">
                                                                        <?php echo e(number_format($subPercent, 1)); ?>%
                                                                    </span>
                                                                </td>
                                                                <td colspan="2" class="text-muted small pe-4">
                                                                    Bobot Sub-Elemen: <?php echo e($sub['bobot']); ?>%
                                                                </td>
                                                            </tr>

                                                            <!-- Criteria Child Rows -->
                                                            <?php foreach($sub['kriterias'] as $kri): ?>
                                                                <?php
                                                                    $isNa = !empty($kri['is_na']);
                                                                    $kriNilai = $kri['nilai_aktual'] ?? ($kri['nilai'] ?? 0);
                                                                    $kriMaks = $kri['nilai_maksimal'] ?? 4;
                                                                ?>
                                                                <tr class="<?php echo e($isNa ? 'text-muted bg-light bg-opacity-50' : ''); ?>">
                                                                    <td class="ps-4 text-nowrap">
                                                                        <span class="badge bg-light text-dark border px-2 py-1 ms-2"><?php echo e($kri['kode_kriteria']); ?></span>
                                                                    </td>
                                                                    <td>
                                                                        <div class="fw-normal text-slate-800"><?php echo e($kri['nama_kriteria']); ?></div>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <?php if($isNa): ?>
                                                                            <span class="badge bg-secondary rounded-pill px-2 py-1">N/A</span>
                                                                        <?php else: ?>
                                                                            <span class="fw-bold <?php echo e($kriNilai == $kriMaks ? 'text-success' : ($kriNilai == 0 ? 'text-danger' : 'text-primary')); ?>">
                                                                                <?php echo e($kriNilai); ?>

                                                                            </span>
                                                                            <span class="text-muted">/ <?php echo e($kriMaks); ?></span>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <?php if($isNa): ?>
                                                                            <span class="text-muted small">-</span>
                                                                        <?php else: ?>
                                                                            <?php
                                                                                $kriPct = $kriMaks > 0 ? ($kriNilai / $kriMaks) * 100 : 0;
                                                                            ?>
                                                                            <span class="badge <?php echo e($kriPct == 100 ? 'bg-success bg-opacity-10 text-success' : ($kriPct == 0 ? 'bg-danger bg-opacity-10 text-danger' : 'bg-warning bg-opacity-10 text-warning')); ?> rounded-pill px-2">
                                                                                <?php echo e(number_format($kriPct, 0)); ?>%
                                                                            </span>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php if(!empty($kri['catatan'])): ?>
                                                                            <div class="small text-slate-700" style="max-width: 320px; word-break: break-word;">
                                                                                <?php echo nl2br(e($kri['catatan'])); ?>

                                                                            </div>
                                                                        <?php else: ?>
                                                                            <span class="text-muted small fst-italic">-</span>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                    <td class="text-center pe-4">
                                                                        <div class="d-flex flex-column align-items-center gap-1">
                                                                            <?php if(!empty($kri['has_pica'])): ?>
                                                                                <?php
                                                                                    $kat = $kri['pica_kategori'] ?? 'minor';
                                                                                    $picaBadge = $kat === 'kritikal' ? 'bg-danger' : ($kat === 'mayor' ? 'bg-warning text-dark' : 'bg-info text-dark');
                                                                                ?>
                                                                                <span class="badge <?php echo e($picaBadge); ?> rounded-pill px-2 py-1 small" title="Temuan PICA">
                                                                                    <i class="bi bi-exclamation-triangle-fill me-1"></i> PICA <?php echo e(ucfirst($kat)); ?>

                                                                                </span>
                                                                            <?php endif; ?>

                                                                            <?php
                                                                                $lUrls = !empty($kri['lampiran_urls']) 
                                                                                    ? $kri['lampiran_urls'] 
                                                                                    : (!empty($kri['lampiran_url']) 
                                                                                        ? [$kri['lampiran_url']] 
                                                                                        : (!empty($kri['lampiran']) 
                                                                                            ? (is_array($kri['lampiran']) ? $kri['lampiran'] : (json_decode($kri['lampiran'], true) ?: [$kri['lampiran']])) 
                                                                                            : []));
                                                                            ?>
                                                                            <?php foreach($lUrls as $lPath): ?>
                                                                                <?php
                                                                                    $url = str_starts_with($lPath, 'http') || str_starts_with($lPath, '/storage') ? $lPath : Storage::url($lPath);
                                                                                ?>
                                                                                <a href="<?php echo e($url); ?>" target="_blank" class="badge bg-light text-primary border text-decoration-none px-2 py-1 small">
                                                                                    <i class="bi bi-paperclip me-1"></i> Bukti Lampiran
                                                                                </a>
                                                                            <?php endforeach; ?>

                                                                            <?php if(empty($kri['has_pica']) && empty($lUrls)): ?>
                                                                                <span class="text-muted small">-</span>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==========================================
             SECTION 2.2: GRAFIK AKUMULASI AUDIT
             ========================================== -->
        <div class="tab-pane fade" id="section-grafik" role="tabpanel" aria-labelledby="tab-grafik">
            <div class="card card-custom border-0 bg-white mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                    <div>
                        <h4 class="fw-bold text-slate-800 mb-1">2.2 Grafik Analisis Performance & Temuan Audit Akumulasi</h4>
                        <p class="text-muted small mb-0">Visualisasi data akumulasi dari seluruh perusahaan (MSM & TTN) untuk Periode Audit Tahun <?php echo e($sesi->tahun_periode ?? date('Y')); ?>.</p>
                    </div>
                    <div>
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 rounded-pill fw-bold">
                            <i class="bi bi-pie-chart-fill me-1"></i> Akumulasi Lintas Perusahaan
                        </span>
                    </div>
                </div>

                <div class="card-body p-4">
                    <!-- Row 1: Radar Chart Akumulasi & Top Elemen Temuan Akumulasi -->
                    <div class="row g-4 mb-4">
                        <!-- Radar Chart Akumulasi -->
                        <div class="col-lg-6">
                            <div class="card card-custom p-4 h-100 border-start border-4 border-primary">
                                <div class="d-flex align-items-start justify-content-between mb-2">
                                    <div>
                                        <h5 class="fw-bold mb-1 text-slate-800"><i class="bi bi-heptagon-fill me-2 text-primary"></i>Pencapaian Nilai Akumulasi per Elemen</h5>
                                        <p class="text-muted small mb-0">Persentase rata-rata akumulasi pencapaian nilai per elemen SMKP (Gabungan MSM & TTN).</p>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 text-nowrap ms-2" data-bs-toggle="modal" data-bs-target="#detailRadarChartModal">
                                        <i class="bi bi-arrows-angle-expand me-1"></i>Perbesar
                                    </button>
                                </div>
                                <div style="min-height: 520px; height: 60vh; max-height: 750px; display: flex; justify-content: center; align-items: center;" class="mt-3">
                                    <canvas id="accumulatedRadarChart" style="max-width: 100%; width: 100%; height: 100%;"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Top Elemen Temuan Akumulasi (Swapped to Top Right) -->
                        <div class="col-lg-6">
                            <div class="card card-custom p-4 h-100 border-start border-4 border-warning">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div>
                                        <h5 class="fw-bold mb-1 text-slate-800"><i class="bi bi-trophy-fill me-2 text-warning"></i>Top Elemen Paling Sering Ditemui Temuan (Akumulasi Gabungan)</h5>
                                        <p class="text-muted small mb-0">Peringkat elemen SMKP yang paling banyak memiliki catatan ketidaksesuaian kriteria dari seluruh perusahaan.</p>
                                    </div>
                                </div>

                                <div class="list-group list-group-flush border-0 mt-3">
                                    <?php if(!empty($chartData['topAccumulatedFindings'])): ?>
                                        <?php foreach($chartData['topAccumulatedFindings'] as $index => $top): ?>
                                            <div class="list-group-item d-flex align-items-center justify-content-between px-0 py-2 border-bottom">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge <?php echo $index == 0 ? 'bg-danger' : ($index == 1 ? 'bg-warning text-dark' : 'bg-secondary'); ?> rounded-circle p-2" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;">
                                                        <?php echo $index + 1; ?>
                                                    </span>
                                                    <div>
                                                        <strong class="text-slate-800 d-block small">Elemen <?php echo e($top['kode_elemen']); ?>: <?php echo e($top['nama_elemen']); ?></strong>
                                                    </div>
                                                </div>
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-1 font-monospace fw-bold">
                                                    <?php echo e($top['percentage']); ?>% <small class="text-muted">(<?php echo e($top['total_findings']); ?>/<?php echo e($top['total_assessed']); ?>)</small>
                                                </span>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="text-center py-4 text-muted">Belum ada data temuan audit akumulasi.</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Horizontal Bar Chart Findings Akumulasi (Swapped to Bottom Full Width) -->
                    <div class="row g-4">
                        <div class="col-12">
                            <div class="card card-custom p-4 border-start border-4 border-danger">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div>
                                        <h5 class="fw-bold mb-1 text-slate-800"><i class="bi bi-exclamation-octagon-fill me-2 text-danger"></i>Frekuensi Temuan Audit Akumulasi</h5>
                                        <p class="text-muted small mb-0">Persentase temuan ketidaksesuaian/catatan audit per elemen SMKP (Gabungan MSM & TTN).</p>
                                    </div>
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill fw-bold">
                                        Total: <?php echo e(number_format($chartData['totalAccumulatedFindings'] ?? 0)); ?> Temuan
                                    </span>
                                </div>
                                <div style="height: 320px;">
                                    <canvas id="accumulatedFindingsBarChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Modal Fullscreen / Extra Large Radar Chart (Laporan Detail) -->
        <div class="modal fade" id="detailRadarChartModal" tabindex="-1" aria-labelledby="detailRadarChartModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-fullscreen-lg-down modal-xl modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg style-radius-16">
                    <div class="modal-header text-white border-0 py-3" style="background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);">
                        <h5 class="modal-title fw-bold text-white" id="detailRadarChartModalLabel">
                            <i class="bi bi-heptagon-fill me-2 text-info"></i>Grafik Pencapaian Nilai Akumulasi per Elemen (Ukuran Penuh)
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 bg-light">
                        <p class="text-muted small mb-3">Tampilan grafik radar pencapaian nilai akumulasi per elemen SMKP dengan resolusi besar dan kejelasan maksimal.</p>
                        <div style="height: 720px; display: flex; justify-content: center; align-items: center;">
                            <canvas id="modalDetailRadarChart" style="max-width: 100%; width: 100%; height: 100%;"></canvas>
                        </div>
                    </div>
                    <div class="modal-footer bg-white border-top-0 py-2">
                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==========================================
             SECTION 2.3: DAFTAR SUB-ELEMEN PRAKTIK TERBAIK
             ========================================== -->
        <div class="tab-pane fade" id="section-praktek" role="tabpanel" aria-labelledby="tab-praktek">
            <div class="card card-custom border-0 bg-white mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <div class="d-flex align-items-center gap-2">
                        <div class="stat-icon-box bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; border-radius: 12px;">
                            <i class="bi bi-award-fill fs-4"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold text-slate-800 mb-1">2.3 Daftar Sub-Elemen Praktik Terbaik (Best Practices)</h4>
                            <p class="text-muted small mb-0">Daftar Sub-Elemen yang telah mencapai tingkat kepatuhan sempurna (100% dari total nilai maksimal efektif).</p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <?php if($praktekBaik->isEmpty()): ?>
                        <div class="text-center py-5">
                            <div class="stat-icon-box bg-light text-muted mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px; border-radius: 50%;">
                                <i class="bi bi-inbox fs-2"></i>
                            </div>
                            <h5 class="fw-bold text-slate-700">Belum Ada Praktik Terbaik 100%</h5>
                            <p class="text-muted small mb-0">Belum ada sub-elemen yang mencapai kepatuhan 100% pada sesi audit ini.</p>
                        </div>
                    <?php else: ?>
                        <?php
                            $groupedPraktek = $praktekBaik->groupBy(fn($item) => $item['elemen_kode'] ?? ($item['kode_elemen'] ?? '-'));
                            $uniquePraktekPrefix = 'praktek_' . uniqid();
                            $praktekElemIndex = 0;
                        ?>
                        <div class="accordion mb-3" id="accordion_<?php echo e($uniquePraktekPrefix); ?>">
                            <?php foreach($groupedPraktek as $elemKode => $praktekGroup): ?>
                                <?php
                                    $praktekElemIndex++;
                                    $firstItem = $praktekGroup->first();
                                    $elemNama = $firstItem['nama_elemen'] ?? ($firstItem['elemen_nama'] ?? 'Elemen Non-Terdefinisi');
                                    
                                    $totalPraktekElem = $praktekGroup->count();
                                    $countSubElemen = $praktekGroup->count();
                                    $countSubSubElemen = 0;

                                    foreach($praktekGroup as $item) {
                                        $subObj = \App\Models\SubElemen::with('kriterias')->find($item['sub_elemen_id'] ?? 0);
                                        $kriterias = $subObj ? $subObj->kriterias : collect();
                                        if ($kriterias->count() > 1) {
                                            $countSubSubElemen += $kriterias->count();
                                        }
                                    }
                                ?>
                                <div class="accordion-item border rounded-3 mb-3 overflow-hidden shadow-sm">
                                    <h2 class="accordion-header" id="heading_<?php echo e($uniquePraktekPrefix); ?>_<?php echo e($praktekElemIndex); ?>">
                                        <button class="accordion-button <?php echo e($praktekElemIndex > 1 ? 'collapsed' : ''); ?> bg-light text-slate-800 fw-bold py-3 px-4" 
                                                type="button" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#collapse_<?php echo e($uniquePraktekPrefix); ?>_<?php echo e($praktekElemIndex); ?>" 
                                                aria-expanded="<?php echo e($praktekElemIndex == 1 ? 'true' : 'false'); ?>" 
                                                aria-controls="collapse_<?php echo e($uniquePraktekPrefix); ?>_<?php echo e($praktekElemIndex); ?>">
                                            <div class="d-flex flex-wrap align-items-center justify-content-between w-100 me-3 gap-2">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge bg-dark rounded-pill px-3 py-2">Elemen <?php echo e($elemKode); ?></span>
                                                    <span class="fs-6 text-slate-900"><?php echo e($elemNama); ?></span>
                                                </div>
                                                <div class="d-flex align-items-center gap-3">
                                                    <span class="badge bg-success rounded-pill px-3 py-1.5 small">
                                                        <i class="bi bi-award-fill me-1"></i><?php echo e($totalPraktekElem); ?> Praktik Terbaik 
                                                        (<?php echo e($countSubElemen); ?> Sub-Elemen<?php if($countSubSubElemen > 0): ?>, <?php echo e($countSubSubElemen); ?> Sub-Sub Elemen<?php endif; ?>)
                                                    </span>
                                                </div>
                                            </div>
                                        </button>
                                    </h2>

                                    <div id="collapse_<?php echo e($uniquePraktekPrefix); ?>_<?php echo e($praktekElemIndex); ?>" 
                                         class="accordion-collapse collapse <?php echo e($praktekElemIndex == 1 ? 'show' : ''); ?>" 
                                         aria-labelledby="heading_<?php echo e($uniquePraktekPrefix); ?>_<?php echo e($praktekElemIndex); ?>" 
                                         data-bs-parent="#accordion_<?php echo e($uniquePraktekPrefix); ?>">
                                        <div class="accordion-body p-0 border-top">
                                            <div class="table-responsive">
                                                <table class="table table-hover align-middle mb-0 matrix-tree-table">
                                                    <thead class="table-light text-slate-700 small text-uppercase">
                                                        <tr>
                                                            <th style="width: 110px;" class="ps-4">Kode</th>
                                                            <th style="min-width: 220px;">Deskripsi / Kriteria</th>
                                                            <th style="width: 110px;" class="text-center">Skor / Nilai</th>
                                                            <th style="width: 110px;" class="text-center">Capaian</th>
                                                            <th style="min-width: 280px;" class="pe-4">Catatan Evaluasi / Bukti Praktik Baik</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach($praktekGroup as $item): ?>
                                                            <?php
                                                                $subObj = \App\Models\SubElemen::with('kriterias')->find($item['sub_elemen_id'] ?? 0);
                                                                $kriterias = $subObj ? $subObj->kriterias : collect();
                                                                $hasSubSub = $kriterias->count() > 1;
                                                                $subKode = $item['kode_sub_elemen'] ?? ($item['kode_sub'] ?? '-');
                                                                $subNama = $item['nama_sub_elemen'] ?? ($item['nama_sub'] ?? '-');
                                                                $nilaiAktual = $item['nilai_aktual'] ?? 0;
                                                                $nilaiMaks = $item['nilai_maks_efektif'] ?? ($item['nilai_maks'] ?? 4);
                                                            ?>

                                                            <?php if(!$hasSubSub): ?>
                                                                <!-- Sub-Elemen Penilaian Langsung / Tanpa Sub-Sub Elemen (1 Baris) -->
                                                                <tr>
                                                                    <td class="ps-4 text-nowrap">
                                                                        <span class="text-primary fw-semibold">
                                                                            <i class="bi bi-folder2 me-1"></i><?php echo e($subKode); ?>
                                                                        </span>
                                                                    </td>
                                                                    <td>
                                                                        <div class="fw-semibold text-slate-800" style="max-width: 250px; line-height: 1.3;">
                                                                            <?php echo e($subNama); ?>
                                                                        </div>
                                                                        <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5 mt-1" style="font-size: 0.7rem;">
                                                                            Penilaian Langsung
                                                                        </span>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <strong class="text-success"><?php echo e($nilaiAktual); ?></strong> 
                                                                        <span class="text-muted">/ <?php echo e($nilaiMaks); ?></span>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <span class="badge bg-success bg-opacity-10 text-success fw-bold rounded-pill px-3 py-1">
                                                                            <i class="bi bi-check2-circle me-1"></i> 100%
                                                                        </span>
                                                                    </td>
                                                                    <td class="pe-4">
                                                                        <?php if(is_array($item['catatan'])): ?>
                                                                            <ul class="ps-3 mb-0 text-slate-700 small">
                                                                                <?php foreach($item['catatan'] as $ct): ?>
                                                                                    <li><i class="bi bi-check-circle-fill text-success me-1"></i><?php echo e($ct); ?></li>
                                                                                <?php endforeach; ?>
                                                                            </ul>
                                                                        <?php elseif(!empty($item['catatan'])): ?>
                                                                            <div class="small text-slate-700">
                                                                                <i class="bi bi-check-circle-fill text-success me-1"></i><?php echo nl2br(e($item['catatan'])); ?>
                                                                            </div>
                                                                        <?php else: ?>
                                                                            <span class="text-muted small fst-italic">Kesesuaian penuh memenuhi standar evaluasi SMKP Minerba.</span>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                </tr>
                                                            <?php else: ?>
                                                                <!-- Sub-Elemen Header Row (Memiliki beberapa Sub-Sub Elemen) -->
                                                                <tr class="table-secondary fw-semibold bg-slate-100">
                                                                    <td class="ps-4 text-primary text-nowrap">
                                                                        <i class="bi bi-folder2-open me-1"></i> <?php echo e($subKode); ?>
                                                                    </td>
                                                                    <td class="text-slate-900 fw-bold">
                                                                        <?php echo e($subNama); ?>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <span class="fw-bold text-success"><?php echo e($nilaiAktual); ?></span> 
                                                                        <span class="text-muted">/ <?php echo e($nilaiMaks); ?></span>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <span class="badge bg-success bg-opacity-10 text-success fw-bold rounded-pill px-2 py-1">
                                                                            100%
                                                                        </span>
                                                                    </td>
                                                                    <td class="text-muted small pe-4 text-end">
                                                                        Total Sub-Elemen: <strong><?php echo e($kriterias->count()); ?> Sub-Sub Elemen</strong>
                                                                    </td>
                                                                </tr>

                                                                <!-- Criteria Child Rows -->
                                                                <?php foreach($kriterias as $kri): ?>
                                                                    <?php
                                                                        $kriMaks = $kri->nilai_maksimal ?? 4;
                                                                    ?>
                                                                    <tr>
                                                                        <td class="ps-4 text-nowrap">
                                                                            <span class="badge bg-light text-dark border px-2 py-1 ms-2 font-monospace">
                                                                                <?php echo e($kri->kode_kriteria); ?>
                                                                            </span>
                                                                        </td>
                                                                        <td>
                                                                            <div class="fw-semibold text-slate-800" style="max-width: 250px; line-height: 1.3;">
                                                                                <?php echo e($kri->nama_kriteria ?? ($kri->deskripsi ?? '-')); ?>
                                                                            </div>
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <strong class="text-success"><?php echo e($kriMaks); ?></strong> 
                                                                            <span class="text-muted">/ <?php echo e($kriMaks); ?></span>
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1">
                                                                                100%
                                                                            </span>
                                                                        </td>
                                                                        <td class="pe-4">
                                                                            <div class="small text-slate-700">
                                                                                <i class="bi bi-check-circle-fill text-success me-1"></i>Kriteria memenuhi 100% standar evaluasi SMKP Minerba.
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ==========================================
             SECTION 2.4: DAFTAR TEMUAN KETIDAKSESUAIAN & PICA
             ========================================== -->
        <div class="tab-pane fade" id="section-temuan" role="tabpanel" aria-labelledby="tab-temuan">
            <div class="card card-custom border-0 bg-white mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <div class="d-flex align-items-center gap-2">
                        <div class="stat-icon-box bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; border-radius: 12px;">
                            <i class="bi bi-tools fs-4"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold text-slate-800 mb-1">2.4 Daftar Temuan Ketidaksesuaian</h4>
                            <p class="text-muted small mb-0">Rincian ketidaksesuaian hasil audit yang diklasifikasikan berdasarkan tingkat keparahan (Kritikal, Mayor, Minor) serta status rencana tindak lanjut perbaikannya.</p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <!-- Informative Banner SMKP Sub-element Counting Rule -->
                    <div class="alert alert-primary bg-primary bg-opacity-10 border-0 rounded-3 py-2 px-3 mb-4 d-flex align-items-center gap-2 small">
                        <i class="bi bi-info-circle-fill text-primary fs-5"></i>
                        <div>
                            <strong>Ketentuan Temuan SMKP Minerba:</strong> Akumulasi temuan pada kriteria-kriteria dihitung sebagai <strong>1 unit temuan sub-elemen</strong> (maksimal sejumlah total sub-elemen non-N/A). Untuk tindak lanjut, seluruh <strong><?php echo e($temuanKategori['total_items'] ?? 0); ?> rincian kriteria</strong> di bawah ini wajib dieksekusi dan diverifikasi hingga status closed.
                        </div>
                    </div>

                    <!-- Sub-section 1: Temuan Kritikal -->
                    <div class="mb-5">
                        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                            <h5 class="fw-bold text-danger mb-0 d-flex align-items-center gap-2">
                                <i class="bi bi-exclamation-octagon-fill"></i> Temuan Kritikal
                                <span class="badge bg-danger rounded-pill px-3 py-1 fs-6">
                                    <?php echo e($temuanKategori['kritikal_count'] ?? 0); ?> Sub-Elemen (<?php echo e(count($temuanKategori['kritikal'])); ?> Tindakan)
                                </span>
                            </h5>
                            <small class="text-muted">Kondisi berbahaya fatal atau pelanggaran regulasi mutlak</small>
                        </div>

                        <?php if($temuanKategori['kritikal']->isEmpty()): ?>
                            <div class="alert alert-light border text-muted small rounded-3 py-3 px-4 d-flex align-items-center gap-2">
                                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                <span>Tidak ditemukan ketidaksesuaian kategori <strong>Kritikal</strong> pada sesi audit ini.</span>
                            </div>
                        <?php else: ?>
                            <?php echo view('laporan._pica_table', ['items' => $temuanKategori['kritikal'], 'isReadOnly' => $isReadOnly])->render(); ?>
                        <?php endif; ?>
                    </div>

                    <!-- Sub-section 2: Temuan Mayor -->
                    <div class="mb-5">
                        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                            <h5 class="fw-bold text-warning-emphasis mb-0 d-flex align-items-center gap-2">
                                <i class="bi bi-exclamation-triangle-fill text-warning"></i> Temuan Mayor
                                <span class="badge bg-warning text-dark rounded-pill px-3 py-1 fs-6">
                                    <?php echo e($temuanKategori['mayor_count'] ?? 0); ?> Sub-Elemen (<?php echo e(count($temuanKategori['mayor'])); ?> Tindakan)
                                </span>
                            </h5>
                            <small class="text-muted">Kepatuhan sub-elemen &lt; 50% atau tidak terpenuhinya klausul wajib</small>
                        </div>

                        <?php if($temuanKategori['mayor']->isEmpty()): ?>
                            <div class="alert alert-light border text-muted small rounded-3 py-3 px-4 d-flex align-items-center gap-2">
                                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                <span>Tidak ditemukan ketidaksesuaian kategori <strong>Mayor</strong> pada sesi audit ini.</span>
                            </div>
                        <?php else: ?>
                            <?php echo view('laporan._pica_table', ['items' => $temuanKategori['mayor'], 'isReadOnly' => $isReadOnly])->render(); ?>
                        <?php endif; ?>
                    </div>

                    <!-- Sub-section 3: Temuan Minor -->
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                            <h5 class="fw-bold text-info-emphasis mb-0 d-flex align-items-center gap-2">
                                <i class="bi bi-info-circle-fill text-info"></i> Temuan Minor
                                <span class="badge bg-info text-dark rounded-pill px-3 py-1 fs-6">
                                    <?php echo e($temuanKategori['minor_count'] ?? 0); ?> Sub-Elemen (<?php echo e(count($temuanKategori['minor'])); ?> Tindakan)
                                </span>
                            </h5>
                            <small class="text-muted">Ketidaksesuaian administratif atau tidak sistemik</small>
                        </div>

                        <?php if($temuanKategori['minor']->isEmpty()): ?>
                            <div class="alert alert-light border text-muted small rounded-3 py-3 px-4 d-flex align-items-center gap-2">
                                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                <span>Tidak ditemukan ketidaksesuaian kategori <strong>Minor</strong> pada sesi audit ini.</span>
                            </div>
                        <?php else: ?>
                            <?php echo view('laporan._pica_table', ['items' => $temuanKategori['minor'], 'isReadOnly' => $isReadOnly])->render(); ?>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<!-- Accordion Toggle Helper Script with CSP Nonce -->
<script nonce="<?php echo e($cspNonce ?? ''); ?>">
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

        // Audit Period Switcher listener
        const selectPeriodeSesi = document.getElementById('selectPeriodeSesi');
        if (selectPeriodeSesi) {
            selectPeriodeSesi.addEventListener('change', function() {
                if (this.value) {
                    window.location.href = this.value;
                }
            });
        }

        // Consolidated Charts Initialization
        const chartData = <?php echo json_encode($chartData ?? []); ?>;
        
        if (chartData && chartData.elementFullNames) {
            function wrapText(str, maxLength) {
                let res = [];
                while (str.length > maxLength) {
                    let found = false;
                    for (let i = maxLength - 1; i >= 0; i--) {
                        if (str.charAt(i) === ' ' || str.charAt(i) === '-') {
                            res.push(str.slice(0, i));
                            str = str.slice(i + 1);
                            found = true;
                            break;
                        }
                    }
                    if (!found) {
                        res.push(str.slice(0, maxLength));
                        str = str.slice(maxLength);
                    }
                }
                res.push(str);
                return res;
            }

            const rawElementNames = chartData.elementFullNames || [];
            const accumulatedScores = chartData.accumulatedScores || [];
            
            const radarLabels = rawElementNames.map((name, i) => {
                let lines = wrapText(name, 26);
                lines.push('(' + (accumulatedScores[i] || 0) + '%)');
                return lines;
            });

            // 1. Radar Chart Akumulasi
            const ctxRadar = document.getElementById('accumulatedRadarChart');
            let accumulatedRadarChart = null;
            if (ctxRadar) {
                accumulatedRadarChart = new Chart(ctxRadar.getContext('2d'), {
                    type: 'radar',
                    data: {
                        labels: radarLabels,
                        datasets: [{
                            label: 'Pencapaian Akumulasi (%)',
                            data: accumulatedScores,
                            backgroundColor: 'rgba(2, 132, 199, 0.28)',
                            borderColor: '#0284c7',
                            pointBackgroundColor: '#ef4444',
                            pointBorderColor: '#fff',
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: '#ef4444',
                            pointRadius: 5,
                            pointHoverRadius: 8,
                            borderWidth: 2.5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            r: {
                                beginAtZero: true,
                                max: 100,
                                pointLabels: {
                                    font: { size: 13, weight: 'bold', family: "'Plus Jakarta Sans', sans-serif" },
                                    color: '#1e293b'
                                },
                                ticks: {
                                    stepSize: 10,
                                    backdropColor: 'rgba(255, 255, 255, 0.85)',
                                    font: { size: 10, weight: 'bold' },
                                    callback: function(value) { return value + '%'; }
                                }
                            }
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) { return 'Pencapaian Akumulasi: ' + context.raw + '%'; }
                                }
                            }
                        }
                    }
                });
            }

            // Modal Fullscreen Radar Chart Initialization (Detail Laporan)
            const detailRadarModalEl = document.getElementById('detailRadarChartModal');
            if (detailRadarModalEl) {
                let modalDetailRadarChartInstance = null;
                const modalLabels = rawElementNames.map((name, i) => {
                    let lines = wrapText(name, 35);
                    lines.push('(' + (accumulatedScores[i] || 0) + '%)');
                    return lines;
                });

                detailRadarModalEl.addEventListener('shown.bs.modal', function () {
                    const ctxModal = document.getElementById('modalDetailRadarChart');
                    if (ctxModal && !modalDetailRadarChartInstance) {
                        modalDetailRadarChartInstance = new Chart(ctxModal.getContext('2d'), {
                            type: 'radar',
                            data: {
                                labels: modalLabels,
                                datasets: [{
                                    label: 'Pencapaian Akumulasi (%)',
                                    data: accumulatedScores,
                                    backgroundColor: 'rgba(2, 132, 199, 0.28)',
                                    borderColor: '#0284c7',
                                    pointBackgroundColor: '#ef4444',
                                    pointBorderColor: '#fff',
                                    pointHoverBackgroundColor: '#fff',
                                    pointHoverBorderColor: '#ef4444',
                                    pointRadius: 6,
                                    pointHoverRadius: 9,
                                    borderWidth: 3
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    r: {
                                        beginAtZero: true,
                                        max: 100,
                                        pointLabels: {
                                            font: { size: 14, weight: 'bold', family: "'Plus Jakarta Sans', sans-serif" },
                                            color: '#0f172a'
                                        },
                                        ticks: {
                                            stepSize: 10,
                                            backdropColor: 'rgba(255, 255, 255, 0.9)',
                                            font: { size: 11, weight: 'bold' },
                                            callback: function(value) { return value + '%'; }
                                        }
                                    }
                                },
                                plugins: {
                                    legend: { display: false },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) { return 'Pencapaian Akumulasi: ' + context.raw + '%'; }
                                        }
                                    }
                                }
                            }
                        });
                    } else if (modalDetailRadarChartInstance) {
                        modalDetailRadarChartInstance.resize();
                    }
                });
            }

            // 2. Horizontal Bar Chart Accumulated Findings
            const hBarTextPlugin = {
                id: 'hBarTextPlugin',
                afterDatasetsDraw(chart, args, pluginOptions) {
                    const { ctx, data } = chart;
                    chart.getDatasetMeta(0).data.forEach((bar, index) => {
                        const value = data.datasets[0].data[index];
                        ctx.save();
                        ctx.font = 'bold 12px sans-serif';
                        ctx.textBaseline = 'middle';
                        
                        if (value > 0) {
                            let xPos = bar.x - 10;
                            ctx.fillStyle = '#ffffff'; 
                            ctx.textAlign = 'right';
                            
                            if (bar.width < 35) {
                                xPos = bar.x + 10;
                                ctx.fillStyle = '#1e293b';
                                ctx.textAlign = 'left';
                            }
                            ctx.fillText(value + '%', xPos, bar.y);
                        } else {
                            ctx.fillStyle = '#1e293b';
                            ctx.textAlign = 'left';
                            ctx.fillText('0%', bar.x + 10, bar.y);
                        }
                        ctx.restore();
                    });
                }
            };

            const ctxFindings = document.getElementById('accumulatedFindingsBarChart');
            let accumulatedFindingsChart = null;
            if (ctxFindings) {
                const rawFindingCounts = chartData.accumulatedFindingCounts || [];
                const rawFindingTotals = chartData.accumulatedFindingTotalsPerElemen || [];

                accumulatedFindingsChart = new Chart(ctxFindings.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: chartData.findingLabels || [],
                        datasets: [{
                            label: 'Persentase Temuan Akumulasi (%)',
                            data: chartData.accumulatedFindingPercentages || [],
                            backgroundColor: 'rgba(239, 68, 68, 0.75)',
                            borderColor: '#ef4444',
                            borderWidth: 2,
                            borderRadius: 6
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                beginAtZero: true,
                                max: 100,
                                ticks: { stepSize: 10, callback: function(val) { return val + '%'; } }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const count = rawFindingCounts[context.dataIndex] || 0;
                                        const total = rawFindingTotals[context.dataIndex] || 0;
                                        return `Temuan Akumulasi: ${context.parsed.x}% (${count}/${total} Kriteria)`;
                                    }
                                }
                            }
                        }
                    },
                    plugins: [hBarTextPlugin]
                });
            }

            // Resize charts on tab activation
            const tabGrafikEl = document.getElementById('tab-grafik');
            if (tabGrafikEl) {
                tabGrafikEl.addEventListener('shown.bs.tab', function() {
                    if (accumulatedRadarChart) accumulatedRadarChart.resize();
                    if (accumulatedFindingsChart) accumulatedFindingsChart.resize();
                });
            }
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
<?php
$content = ob_get_clean();
echo view('layouts.app', array_merge(get_defined_vars(), [
    'title' => $title,
    'content' => $content
]))->render();
?>
