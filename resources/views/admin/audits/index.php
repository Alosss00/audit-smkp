<?php
$title = 'Rekapitulasi Audit System — Administrator';
ob_start();
?>
<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold text-slate-800 mb-1">
            <i class="bi bi-shield-check text-primary me-2"></i>Monitoring Rekap Audit System
        </h2>
        <p class="text-muted mb-0">Pantau dan evaluasi seluruh pelaksanaan audit internal dari seluruh auditor.</p>
    </div>
</div>
<!-- Company Selector Navigation Cards (MSM / TTN / Semua) -->
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <a href="<?php echo e(route('admin.rekap-audit.index', array_merge(request()->except(['perusahaan_id', 'page']), []))); ?>" 
           class="card card-custom border-2 text-decoration-none p-3 h-100 transition-all <?php echo e(!request('perusahaan_id') ? 'border-primary bg-primary bg-opacity-10 shadow-sm' : 'border-light bg-white hover-lift'); ?>">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon-box rounded-3 <?php echo e(!request('perusahaan_id') ? 'bg-primary text-white' : 'bg-light text-primary'); ?>" style="width: 44px; height: 44px;">
                        <i class="bi bi-buildings-fill fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 <?php echo e(!request('perusahaan_id') ? 'text-primary' : 'text-slate-800'); ?>">Semua Perusahaan</h6>
                        <small class="text-muted">Seluruh Area Audit SMKP</small>
                    </div>
                </div>
                <span class="badge <?php echo e(!request('perusahaan_id') ? 'bg-primary text-white' : 'bg-light text-dark'); ?> rounded-pill px-2.5 py-1 fw-bold">
                    All
                </span>
            </div>
        </a>
    </div>

    <?php foreach($perusahaans as $comp): ?>
        <?php
            $isSelected = request('perusahaan_id') == $comp->id;
            $compLower = strtolower($comp->nama_perusahaan);
            $alias = str_contains($compLower, 'soputan') || str_contains($compLower, 'msm') ? 'MSM' : (str_contains($compLower, 'tondano') || str_contains($compLower, 'ttn') ? 'TTN' : 'AREA');
            $iconClass = $alias === 'MSM' ? 'bi-building-fill' : 'bi-geo-alt-fill';
            $badgeColor = $alias === 'MSM' ? 'bg-info text-dark' : 'bg-warning text-dark';
        ?>
        <div class="col-12 col-md-4">
            <a href="<?php echo e(route('admin.rekap-audit.index', array_merge(request()->except(['page']), ['perusahaan_id' => $comp->id]))); ?>" 
               class="card card-custom border-2 text-decoration-none p-3 h-100 transition-all <?php echo e($isSelected ? 'border-primary bg-primary bg-opacity-10 shadow-sm' : 'border-light bg-white hover-lift'); ?>">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon-box rounded-3 <?php echo e($isSelected ? 'bg-primary text-white' : 'bg-light text-primary'); ?>" style="width: 44px; height: 44px;">
                            <i class="bi <?php echo e($iconClass); ?> fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 <?php echo e($isSelected ? 'text-primary' : 'text-slate-800'); ?>"><?php echo e($comp->nama_perusahaan); ?></h6>
                            <small class="text-muted">Perusahaan Audit (<?php echo e($alias); ?>)</small>
                        </div>
                    </div>
                    <span class="badge <?php echo e($isSelected ? 'bg-primary text-white' : $badgeColor); ?> rounded-pill px-2.5 py-1 fw-bold">
                        <?php echo e($alias); ?>

                    </span>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<!-- Filter Card -->
<div class="card card-custom p-3 mb-4">
    <form method="GET" action="<?php echo e(route('admin.rekap-audit.index')); ?>" class="row g-2 align-items-center">
        <div class="col-md-3">
            <label class="form-label text-muted small fw-semibold mb-1"><i class="bi bi-calendar-range me-1"></i>Pilih Periode Audit</label>
            <select name="tahun_periode" class="form-select" data-auto-submit="true">
                <option value="">-- Semua Periode Tahun --</option>
                <?php if(!empty($tahunPeriodes)): ?>
                    <?php foreach($tahunPeriodes as $thn): ?>
                        <option value="<?php echo e($thn); ?>" <?php echo e(request('tahun_periode') == $thn ? 'selected' : ''); ?>>Tahun <?php echo e($thn); ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label text-muted small fw-semibold mb-1"><i class="bi bi-building me-1"></i>Perusahaan / Area</label>
            <select name="perusahaan_id" class="form-select select-searchable" placeholder="-- Pilih Perusahaan --" data-auto-submit="true">
                <option value="">-- Semua Perusahaan --</option>
                <?php foreach($perusahaans as $comp): ?>
                    <option value="<?php echo e($comp->id); ?>" <?php echo e((request('perusahaan_id') == $comp->id || request('area_selection') == 'p:'.$comp->id) ? 'selected' : ''); ?>>
                        <?php echo e($comp->nama_perusahaan); ?> <?php if($comp->kategori): ?> (<?php echo e($comp->kategori); ?>) <?php endif; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label text-muted small fw-semibold mb-1"><i class="bi bi-info-circle me-1"></i>Status Sesi</label>
            <select name="status" class="form-select" data-auto-submit="true">
                <option value="">-- Semua Status --</option>
                <option value="draft" <?php echo e(request('status') == 'draft' ? 'selected' : ''); ?>>Draft</option>
                <option value="berjalan" <?php echo e(request('status') == 'berjalan' ? 'selected' : ''); ?>>Berjalan</option>
                <option value="selesai" <?php echo e(request('status') == 'selesai' ? 'selected' : ''); ?>>Selesai</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label text-muted small fw-semibold mb-1"><i class="bi bi-search me-1"></i>Pencarian</label>
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Cari area/auditor..." value="<?php echo e(request('search')); ?>">
                <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i></button>
                <?php if(request()->filled('search') || request()->filled('status') || request()->filled('tahun_periode') || request()->filled('perusahaan_id') || request()->filled('area_selection')): ?>
                    <a href="<?php echo e(route('admin.rekap-audit.index')); ?>" class="btn btn-outline-secondary" title="Reset Filter"><i class="bi bi-x-lg"></i></a>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<!-- Audit Sessions Table -->
<div class="card card-custom p-4">
    <?php if($auditSesis->isEmpty()): ?>
        <div class="text-center py-4 text-muted">Belum ada sesi audit yang ditemukan.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Perusahaan (Area Audit)</th>
                        <th>Tahun Periode</th>
                        <th>Pelaksanaan Audit</th>
                        <th>Auditor Pelaksana</th>
                        <th>Status</th>
                        <th class="text-center">Progres Audit</th>
                        <th class="text-center">Nilai Audit</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($auditSesis as $index => $sesi): ?>
                        <?php
                            $thn = $sesi->tahun_periode ?? $sesi->tanggal_mulai->format('Y');
                        ?>
                        <tr>
                            <td><?php echo e($auditSesis->firstItem() + $index); ?></td>
                            <td>
                                <span class="fw-bold text-primary">
                                    <i class="bi bi-building me-1"></i><?php echo e($sesi->perusahaan->nama_perusahaan ?? $sesi->area_audit); ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded-pill small fw-semibold w-fit mb-1">
                                        <i class="bi bi-calendar-check me-1"></i>Tahun <?php echo e($thn); ?>
                                    </span>
                                    <span class="text-muted small" style="font-size: 0.75rem;">
                                        1 Jan – 31 Des <?php echo e($thn); ?>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <?php
                                    $thnPelaksanaan = $sesi->tanggal_mulai ? $sesi->tanggal_mulai->format('Y') : date('Y');
                                ?>
                                <div class="d-flex flex-column">
                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 rounded-pill small fw-semibold w-fit mb-1">
                                        <i class="bi bi-calendar-event me-1"></i>Tahun <?php echo e($thnPelaksanaan); ?>
                                    </span>
                                    <span class="small text-slate-800" style="font-size: 0.78rem;">
                                        <?php echo e($sesi->tanggal_mulai->format('d M Y')); ?> - <?php echo e($sesi->tanggal_selesai->format('d M Y')); ?>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-person-circle text-secondary"></i>
                                    <?php echo e($sesi->user->name ?? '-'); ?>
                                </div>
                            </td>
                            <td>
                                <?php if($sesi->status === 'draft'): ?>
                                    <span class="badge bg-secondary">Draft</span>
                                <?php elseif($sesi->status === 'berjalan'): ?>
                                    <span class="badge bg-warning text-dark">Berjalan</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Selesai</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php
                                    $progress = $sesi->hitungProgressPenilaian();
                                    $progBarClass = $progress == 100 ? 'bg-success' : ($progress >= 50 ? 'bg-warning' : 'bg-danger');
                                    $progTextClass = $progress == 100 ? 'text-success' : ($progress >= 50 ? 'text-warning' : 'text-danger');
                                ?>
                                <div class="d-flex align-items-center justify-content-center gap-2 mx-auto" style="max-width: 130px;">
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        <div class="progress-bar <?php echo e($progBarClass); ?>" role="progressbar" style="width: <?php echo e($progress); ?>%"></div>
                                    </div>
                                    <span class="small fw-bold <?php echo e($progTextClass); ?>">
                                        <?php echo e(number_format($progress, 1)); ?>%
                                    </span>
                                </div>
                            </td>
                            <td class="text-center">
                                <?php
                                    $skorAkhir = $sesi->hitungSkorAkhir();
                                    $skorClass = $skorAkhir >= 80 ? 'bg-success-subtle text-success border-success-subtle' : ($skorAkhir >= 70 ? 'bg-warning-subtle text-warning-emphasis border-warning-subtle' : 'bg-danger-subtle text-danger border-danger-subtle');
                                ?>
                                <span class="badge <?php echo e($skorClass); ?> border px-2.5 py-1.5 rounded-pill fw-bold fs-6">
                                    <?php echo e(number_format($skorAkhir, 2)); ?>%
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group gap-1">
                                    <a href="<?php echo e(route('admin.rekap-audit.laporan-detail', $sesi->id)); ?>" class="btn btn-sm btn-outline-info text-dark fw-semibold rounded-2">
                                        <i class="bi bi-file-earmark-text me-1"></i> Laporan Detail
                                    </a>
                                    <a href="<?php echo e(route('admin.rekap-audit.show', $sesi->id)); ?>" class="btn btn-sm btn-outline-primary rounded-2">
                                        <i class="bi bi-eye me-1"></i> Detail Rekap
                                    </a>
                                    <a href="<?php echo e(route('admin.rekap-audit.cetak', $sesi->id)); ?>" target="_blank" class="btn btn-sm btn-dark rounded-2">
                                        <i class="bi bi-printer me-1"></i> Cetak
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="small text-muted">
                Menampilkan <?php echo e($auditSesis->firstItem() ?? 0); ?> - <?php echo e($auditSesis->lastItem() ?? 0); ?> dari total <?php echo e($auditSesis->total()); ?> sesi audit
            </div>
            <div>
                <?php echo $auditSesis->links('pagination::bootstrap-5'); ?>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
echo view('layouts.app', array_merge(get_defined_vars(), ['content' => $content, 'title' => $title]))->render();
