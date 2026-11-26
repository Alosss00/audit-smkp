<?php
$title = 'Master Template Narasi Laporan — SMKP Minerba';
ob_start();
?>

<div class="mb-4">
    <h4 class="fw-bold text-slate-800 mb-1">
        <i class="bi bi-file-earmark-richtext me-2 text-primary"></i>Master Template Narasi Laporan Audit
    </h4>
    <p class="text-muted mb-0 small">Kelola template teks bawaan untuk Latar Belakang, Tujuan Audit, Penutup, dan Respon Manajemen. Hanya placeholder pada daftar putih di bawah ini yang diproses.</p>
</div>

<div class="card card-custom p-3 mb-4 bg-light border">
    <div class="fw-bold small text-slate-800 mb-2"><i class="bi bi-code-square text-primary me-1"></i>Daftar Putih Placeholder yang Diizinkan:</div>
    <div class="d-flex flex-wrap gap-2">
        <?php foreach ($allowedPlaceholders as $pKey => $pDesc): ?>
            <span class="badge bg-white text-dark border rounded-pill px-3 py-1" title="<?php echo e($pDesc); ?>">
                <code>{{<?php echo e($pKey); ?>}}</code>
            </span>
        <?php endforeach; ?>
    </div>
</div>

<div class="d-flex flex-column gap-4">
    <?php foreach ($items as $item): ?>
        <div class="card card-custom p-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 border-bottom pb-2">
                <div>
                    <span class="badge bg-light text-dark border font-monospace me-2"><?php echo e($item->bagian); ?></span>
                    <strong class="text-slate-800"><?php echo e($item->judul); ?></strong>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <?php if ($item->is_verified): ?>
                        <span class="badge bg-primary rounded-pill px-3 py-1"><i class="bi bi-patch-check-fill me-1"></i>Terverifikasi</span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark rounded-pill px-3 py-1"><i class="bi bi-clock me-1"></i>Belum Diverifikasi</span>
                    <?php endif; ?>
                    <form action="<?php echo e(route('admin.master-template-laporan.toggle-verify', $item->id)); ?>" method="POST" class="d-inline">
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                        <input type="hidden" name="_method" value="PATCH">
                        <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            <?php echo $item->is_verified ? 'Batalkan Verifikasi' : 'Verifikasi Template'; ?>
                        </button>
                    </form>
                </div>
            </div>

            <form action="<?php echo e(route('admin.master-template-laporan.update', $item->id)); ?>" method="POST">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <input type="hidden" name="_method" value="PUT">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label small fw-bold">Judul Bagian</label>
                        <input type="text" name="judul" class="form-control" value="<?php echo e($item->judul); ?>" required>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="is_active" id="tplActive<?php echo e($item->id); ?>" value="1" <?php echo $item->is_active ? 'checked' : ''; ?>>
                            <label class="form-check-label small fw-bold" for="tplActive<?php echo e($item->id); ?>">Aktif</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Isi Template Narasi</label>
                        <textarea name="isi_template" class="form-control font-monospace small" rows="5" required><?php echo e($item->isi_template); ?></textarea>
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 btn-sm fw-semibold">
                            <i class="bi bi-save me-1"></i> Simpan Template
                        </button>
                    </div>
                </div>
            </form>
        </div>
    <?php endforeach; ?>
</div>

<?php
$content = ob_get_clean();
echo view('layouts.app', array_merge(get_defined_vars(), ['content' => $content, 'title' => $title]))->render();
