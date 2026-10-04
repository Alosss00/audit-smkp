<?php
$title = 'Daftar Sesi Audit Area Kerja — SMKP Minerba';
ob_start();
?>
<div class="row align-items-center justify-content-between mb-4">
    <div class="col-md-12">
        <h2 class="fw-bold text-slate-800 mb-1">
            <i class="bi bi-journal-text text-info me-2"></i>Sesi Audit Area Kerja
        </h2>
        <p class="text-muted mb-0">Daftar dan rekapitulasi sesi penilaian SMKP yang dilaksanakan pada area kerja: <strong><?php echo e($userArea ?? 'Semua Area'); ?></strong></p>
    </div>
</div>

<!-- Filter Card -->
<div class="card card-custom p-3 mb-4">
    <form method="GET" action="<?php echo e(route('auditor.audit-sesi.index')); ?>" class="row g-2 align-items-center">
        <div class="col-md-4">
            <select name="status" class="form-select rounded-3" data-auto-submit="true">
                <option value="">-- Semua Status Sesi --</option>
                <option value="draft" <?php echo e(request('status') == 'draft' ? 'selected' : ''); ?>>Draft</option>
                <option value="berjalan" <?php echo e(request('status') == 'berjalan' ? 'selected' : ''); ?>>Berjalan</option>
                <option value="selesai" <?php echo e(request('status') == 'selesai' ? 'selected' : ''); ?>>Selesai</option>
            </select>
        </div>
        <?php if(request()->filled('status')): ?>
            <div class="col-md-2">
                <a href="<?php echo e(route('auditor.audit-sesi.index')); ?>" class="btn btn-outline-secondary rounded-3 w-100">Reset</a>
            </div>
        <?php endif; ?>
    </form>
</div>

<!-- Sessions Table -->
<div class="card card-custom p-4">
    <?php if($auditSesis->isEmpty()): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-inbox display-4 d-block mb-3 opacity-50"></i>
            <p class="mb-0 fs-5 fw-semibold">Belum ada sesi audit pada area kerja Anda.</p>
            <small class="text-muted">Sesi audit akan diinisialisasi dan dinilai oleh Administrator / Tim Lead Auditor.</small>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Periode Audit</th>
                        <th>Perusahaan (Area Audit)</th>
                        <th>Auditor Pelaksana</th>
                        <th>Status</th>
                        <th>Progres Penilaian</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($auditSesis as $index => $sesi): ?>
                        <tr>
                            <td><?php echo e($auditSesis->firstItem() + $index); ?></td>
                            <td>
                                <i class="bi bi-calendar-event me-1 text-muted"></i>
                                <?php echo e($sesi->tanggal_mulai->format('d M Y')); ?> - <?php echo e($sesi->tanggal_selesai->format('d M Y')); ?>

                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill small fw-semibold">
                                        <i class="bi bi-building me-1"></i>Perusahaan
                                    </span>
                                    <span class="fw-bold text-slate-800"><?php echo e($sesi->perusahaan->nama_perusahaan ?? $sesi->area_audit); ?></span>
                                </div>
                            </td>
                            <td>
                                <small class="text-muted"><i class="bi bi-person me-1"></i><?php echo e($sesi->user->name ?? '-'); ?></small>
                            </td>
                            <td>
                                <?php if($sesi->status === 'draft'): ?>
                                    <span class="badge bg-secondary badge-role">Draft</span>
                                <?php elseif($sesi->status === 'berjalan'): ?>
                                    <span class="badge bg-warning text-dark badge-role">Berjalan</span>
                                <?php else: ?>
                                    <span class="badge bg-success badge-role">Selesai</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                    $progress = $sesi->hitungProgressPenilaian();
                                ?>
                                <div class="d-flex align-items-center gap-2" style="min-width: 110px;">
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        <div class="progress-bar <?php echo e($progress == 100 ? 'bg-success' : ($progress >= 50 ? 'bg-warning' : 'bg-danger')); ?>" 
                                             role="progressbar" style="width: <?php echo e($progress); ?>%"></div>
                                    </div>
                                    <span class="fs-6 fw-bold <?php echo e($progress == 100 ? 'text-success' : ($progress >= 50 ? 'text-warning' : 'text-danger')); ?>">
                                        <?php echo e(number_format($progress, 1)); ?>%
                                    </span>
                                </div>
                            </td>
                            <td class="text-end">
                                <a href="<?php echo e(route('auditor.audit-sesi.rekap', $sesi->id)); ?>" class="btn btn-sm btn-outline-info text-dark rounded-2" title="Lihat Rekapitulasi Nilai">
                                    <i class="bi bi-bar-chart-line me-1"></i> Lihat Rekap
                                </a>
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
