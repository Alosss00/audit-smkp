<?php
$title = 'Detail Struktur Elemen — SMKP Minerba';
ob_start();
?>
<div class="mb-4">
    <a href="<?php echo e(route('admin.elemens.index')); ?>" class="text-decoration-none text-muted small">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Elemen
    </a>
    <div class="d-flex align-items-center justify-content-between mt-2">
        <div>
            <h2 class="fw-bold text-slate-800 mb-0">
                <span class="badge bg-primary me-2">ELEMEN <?php echo e($elemen->kode_elemen); ?></span>
                <?php echo e($elemen->nama_elemen); ?>

            </h2>
            <p class="text-muted small mb-0">Struktur Pohon Sub-Elemen dan Kriteria Pertanyaan Audit</p>
        </div>
        <div>
            <span class="badge bg-light text-primary border font-monospace fs-5 py-2 px-3">
                Bobot Elemen: <?php echo e(number_format($elemen->bobot, 2)); ?>%
            </span>
        </div>
    </div>
</div>

<?php if($elemen->subElemens->isEmpty()): ?>
    <div class="card card-custom p-5 text-center text-muted">
        <i class="bi bi-diagram-3 display-4 d-block mb-3 opacity-50"></i>
        <p class="mb-2 fs-5 fw-semibold">Belum ada Sub-Elemen pada elemen ini.</p>
        <p class="small text-muted mb-3">Tambahkan Sub-Elemen melalui menu Master Sub-Elemen.</p>
        <a href="<?php echo e(route('admin.sub-elemens.index')); ?>" class="btn btn-info text-dark rounded-3 px-4 mx-auto" style="max-width: 250px;">
            <i class="bi bi-plus-lg me-1"></i> Tambah Sub-Elemen
        </a>
    </div>
<?php else: ?>
    <?php foreach($elemen->subElemens as $sub): ?>
        <div class="card card-custom p-4 mb-4">
            <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3 flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-info text-dark font-monospace fs-6 px-3 py-2">SUB <?php echo e($sub->kode_sub); ?></span>
                    <h5 class="fw-bold text-slate-800 mb-0"><?php echo e($sub->nama_sub); ?></h5>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-primary border font-monospace px-3 py-2">
                        Nilai Max: <?php echo e((int) ($sub->nilai_maksimal ?? 0)); ?>

                    </span>
                    <span class="badge bg-secondary rounded-pill px-3 py-2"><?php echo e($sub->kriterias->count()); ?> Kriteria</span>
                    <a href="<?php echo e(route('admin.sub-elemens.index')); ?>" class="btn btn-sm btn-outline-primary rounded-2 px-3 py-1" title="Kelola / Ubah Nilai Sub-Elemen ini">
                        <i class="bi bi-pencil-square me-1"></i> Kelola
                    </a>
                </div>
            </div>

            <?php if($sub->kriterias->isEmpty()): ?>
                <div class="text-muted small italic py-2 ps-3 border-start border-3">
                    Belum ada kriteria pada sub-elemen ini.
                </div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach($sub->kriterias as $kriteria): ?>
                        <div class="col-md-12">
                            <div class="p-3 bg-light rounded-3 border border-start border-4 border-success">
                                <div class="d-flex align-items-start justify-content-between gap-3">
                                    <div class="d-flex align-items-start gap-2">
                                        <span class="badge bg-secondary px-2 py-1"><?php echo e($kriteria->kode_kriteria); ?></span>
                                        <div>
                                            <p class="mb-1 text-slate-800 fw-semibold"><?php echo e($kriteria->deskripsi); ?></p>
                                            <?php if($kriteria->persyaratan_dokumen): ?>
                                                <small class="text-muted"><i class="bi bi-file-earmark-text me-1"></i><?php echo e($kriteria->persyaratan_dokumen); ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="badge bg-white text-success border font-monospace px-3 py-2 text-nowrap">
                                            Nilai Max: <?php echo e((int) $kriteria->nilai_maksimal); ?>

                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php
$content = ob_get_clean();
echo view('layouts.app', array_merge(get_defined_vars(), [
    'title' => $title,
    'content' => $content
]))->render();
?>
