<?php
$title = 'Master Dasar Hukum Laporan Audit — SMKP Minerba';
ob_start();
?>

<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <h4 class="fw-bold text-slate-800 mb-1">
            <i class="bi bi-journal-text me-2 text-primary"></i>Master Dasar Hukum Laporan Audit
        </h4>
        <p class="text-muted mb-0 small">Kelola dan verifikasi daftar peraturan perundang-undangan yang dicantumkan pada Bab 1 Latar Belakang Laporan Audit SMKP.</p>
    </div>
    <div>
        <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#modalCreateDasarHukum">
            <i class="bi bi-plus-circle me-1"></i> Tambah Dasar Hukum
        </button>
    </div>
</div>

<div class="alert alert-warning rounded-4 shadow-sm d-flex align-items-center gap-2 mb-4">
    <i class="bi bi-shield-exclamation fs-5 text-warning"></i>
    <div class="small">
        Data dasar hukum awal ditandai <strong>Belum Diverifikasi</strong>. Administrator wajib meninjau kesesuaian nomor dan status berlaku peraturan sebelum memverifikasi.
    </div>
</div>

<div class="card card-custom p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="text-secondary small fw-bold text-uppercase">
                    <th style="width: 60px;">Urutan</th>
                    <th>Kode &amp; Nomor Peraturan</th>
                    <th>Judul &amp; Ringkasan</th>
                    <th>Status</th>
                    <th>Verifikasi</th>
                    <th style="width: 150px;" class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($items->isNotEmpty()): ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td class="text-center fw-bold text-muted"><?php echo e($item->urutan); ?></td>
                            <td>
                                <span class="badge bg-light text-dark border font-monospace mb-1"><?php echo e($item->kode); ?></span>
                                <div class="fw-bold text-slate-800 small"><?php echo e($item->nomor_peraturan); ?></div>
                            </td>
                            <td>
                                <div class="fw-semibold text-slate-800 small"><?php echo e($item->judul); ?></div>
                                <?php if ($item->ringkasan): ?>
                                    <div class="text-muted small mt-1"><?php echo e($item->ringkasan); ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($item->is_active): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-1">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($item->is_verified): ?>
                                    <span class="badge bg-primary rounded-pill px-3 py-1"><i class="bi bi-patch-check-fill me-1"></i>Terverifikasi</span>
                                    <?php if ($item->verified_at): ?>
                                        <div class="text-muted small mt-1"><?php echo e($item->verified_at->format('d/m/Y')); ?></div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark rounded-pill px-3 py-1"><i class="bi bi-clock me-1"></i>Belum Diverifikasi</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <form action="<?php echo e(route('admin.master-dasar-hukum.toggle-verify', $item->id)); ?>" method="POST" class="d-inline">
                                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                        <input type="hidden" name="_method" value="PATCH">
                                        <button type="submit" class="btn btn-sm btn-light rounded-circle" title="<?php echo $item->is_verified ? 'Batalkan Verifikasi' : 'Tandai Terverifikasi'; ?>">
                                            <i class="bi <?php echo $item->is_verified ? 'bi-patch-check-fill text-primary' : 'bi-patch-check text-muted'; ?>"></i>
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-light rounded-circle" data-bs-toggle="modal" data-bs-target="#modalEditDH<?php echo e($item->id); ?>" title="Edit">
                                        <i class="bi bi-pencil-fill text-warning"></i>
                                    </button>
                                    <form action="<?php echo e(route('admin.master-dasar-hukum.destroy', $item->id)); ?>" method="POST" class="d-inline" data-confirm="Hapus dasar hukum ini?">
                                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-sm btn-light rounded-circle" title="Hapus">
                                            <i class="bi bi-trash-fill text-danger"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">Belum ada data dasar hukum.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modals Edit -->
<?php foreach ($items as $item): ?>
<div class="modal fade" id="modalEditDH<?php echo e($item->id); ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-slate-800"><i class="bi bi-pencil-square text-warning me-2"></i>Edit Dasar Hukum</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo e(route('admin.master-dasar-hukum.update', $item->id)); ?>" method="POST">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <input type="hidden" name="_method" value="PUT">
                <div class="modal-body py-3">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label small fw-bold">Kode <span class="text-danger">*</span></label>
                            <input type="text" name="kode" class="form-control font-monospace" value="<?php echo e($item->kode); ?>" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-bold">Nomor Peraturan <span class="text-danger">*</span></label>
                            <input type="text" name="nomor_peraturan" class="form-control" value="<?php echo e($item->nomor_peraturan); ?>" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Urutan</label>
                            <input type="number" min="0" name="urutan" class="form-control" value="<?php echo e($item->urutan); ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Judul Peraturan <span class="text-danger">*</span></label>
                            <input type="text" name="judul" class="form-control" value="<?php echo e($item->judul); ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Ringkasan / Catatan</label>
                            <textarea name="ringkasan" class="form-control" rows="3"><?php echo e($item->ringkasan ?? ''); ?></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="editDHActive<?php echo e($item->id); ?>" value="1" <?php echo $item->is_active ? 'checked' : ''; ?>>
                                <label class="form-check-label small fw-bold" for="editDHActive<?php echo e($item->id); ?>">Aktif digunakan di laporan</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<!-- Modal Create -->
<div class="modal fade" id="modalCreateDasarHukum" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-slate-800"><i class="bi bi-plus-circle-fill text-primary me-2"></i>Tambah Dasar Hukum</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo e(route('admin.master-dasar-hukum.store')); ?>" method="POST">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <div class="modal-body py-3">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label small fw-bold">Kode <span class="text-danger">*</span></label>
                            <input type="text" name="kode" class="form-control font-monospace" placeholder="Contoh: PERMEN_ESDM_26_2018" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-bold">Nomor Peraturan <span class="text-danger">*</span></label>
                            <input type="text" name="nomor_peraturan" class="form-control" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Urutan</label>
                            <input type="number" min="0" name="urutan" class="form-control" value="1">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Judul Peraturan <span class="text-danger">*</span></label>
                            <input type="text" name="judul" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Ringkasan / Catatan</label>
                            <textarea name="ringkasan" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
echo view('layouts.app', array_merge(get_defined_vars(), ['content' => $content, 'title' => $title]))->render();
