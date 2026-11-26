<?php
$title = 'Master Faktor Penyesuaian Hari Kerja — SMKP Minerba';
ob_start();
?>

<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h2 class="fw-bold text-slate-800 mb-1">
            <i class="bi bi-sliders text-primary me-2"></i>Master Faktor Penyesuaian Hari Kerja
        </h2>
        <p class="text-muted mb-0 small">Kelola faktor penambah (+) dan pengurang (−) hari kerja audit. Isi nilai hari setelah diverifikasi terhadap dokumen acuan resmi.</p>
    </div>
    <div>
        <button type="button" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCreateFaktor">
            <i class="bi bi-plus-lg me-1"></i> Tambah Faktor
        </button>
    </div>
</div>

<div class="card card-custom p-4 mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-center">
                <tr>
                    <th style="width: 60px;">Urutan</th>
                    <th class="text-start" style="width: 210px;">Kode</th>
                    <th class="text-start">Nama Faktor Penyesuaian</th>
                    <th style="width: 110px;">Tipe</th>
                    <th style="width: 130px;">Nilai Hari</th>
                    <th style="width: 110px;">Status</th>
                    <th style="width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($items->isEmpty()): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            Belum ada data faktor penyesuaian.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td class="text-center fw-bold"><?php echo e($item->urutan); ?></td>
                            <td><code class="small"><?php echo e($item->kode); ?></code></td>
                            <td>
                                <div class="fw-semibold text-slate-800"><?php echo e($item->nama); ?></div>
                                <?php if (!empty($item->keterangan)): ?>
                                    <small class="text-muted"><?php echo e($item->keterangan); ?></small>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($item->tipe === 'tambah'): ?>
                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-1">+ Tambah</span>
                                <?php else: ?>
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">− Kurang</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($item->nilai_hari !== null): ?>
                                    <span class="fw-bold"><?php echo e(number_format((float) $item->nilai_hari, 1)); ?> hari</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark rounded-pill px-2 py-1">Belum diisi</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($item->is_active): ?>
                                    <span class="badge bg-success rounded-pill px-3 py-1">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary rounded-pill px-3 py-1">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex gap-1">
                                    <form action="<?php echo route('admin.master-faktor-penyesuaian.toggle-active', $item->id); ?>" method="POST" class="d-inline">
                                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                        <input type="hidden" name="_method" value="PATCH">
                                        <button type="submit" class="btn btn-sm btn-outline-secondary rounded-3" title="Ubah status aktif">
                                            <i class="bi <?php echo $item->is_active ? 'bi-toggle-on text-success' : 'bi-toggle-off'; ?>"></i>
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-3"
                                            data-bs-toggle="modal" data-bs-target="#modalEditFaktor<?php echo e($item->id); ?>" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <form action="<?php echo route('admin.master-faktor-penyesuaian.destroy', $item->id); ?>" method="POST" class="d-inline"
                                          data-confirm="Pindahkan faktor <?php echo e($item->kode); ?> ke tempat sampah?">
                                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-3" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($trashedItems->isNotEmpty()): ?>
    <div class="card card-custom p-4 border-warning border-opacity-50">
        <h6 class="fw-bold text-slate-800 mb-3">
            <i class="bi bi-trash3 text-warning me-2"></i>Data Terhapus (Soft Delete) — <?php echo e($trashedItems->count()); ?> faktor
        </h6>
        <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle mb-0">
                <thead class="table-light text-center">
                    <tr>
                        <th>Kode</th>
                        <th class="text-start">Nama</th>
                        <th>Tipe</th>
                        <th style="width: 190px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($trashedItems as $t): ?>
                        <tr>
                            <td class="text-center"><code><?php echo e($t->kode); ?></code></td>
                            <td><?php echo e($t->nama); ?></td>
                            <td class="text-center"><?php echo e($t->tipe); ?></td>
                            <td class="text-center">
                                <form action="<?php echo route('admin.master-faktor-penyesuaian.restore', $t->id); ?>" method="POST" class="d-inline">
                                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-2 py-0">Pulihkan</button>
                                </form>
                                <form action="<?php echo route('admin.master-faktor-penyesuaian.force-delete', $t->id); ?>" method="POST" class="d-inline"
                                      data-confirm="Hapus permanen faktor ini?">
                                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-0">Hapus Permanen</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<!-- Modal Edit -->
<?php foreach ($items as $item): ?>
    <div class="modal fade" id="modalEditFaktor<?php echo e($item->id); ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold text-slate-800">Edit Faktor Penyesuaian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?php echo route('admin.master-faktor-penyesuaian.update', $item->id); ?>" method="POST">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <input type="hidden" name="_method" value="PUT">
                    <div class="modal-body py-3">
                        <div class="row g-3">
                            <div class="col-md-7">
                                <label class="form-label small fw-bold">Kode <span class="text-danger">*</span></label>
                                <input type="text" name="kode" class="form-control font-monospace" value="<?php echo e($item->kode); ?>" required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label small fw-bold">Urutan</label>
                                <input type="number" name="urutan" min="0" class="form-control" value="<?php echo e($item->urutan); ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">Nama Faktor <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control" value="<?php echo e($item->nama); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Tipe <span class="text-danger">*</span></label>
                                <select name="tipe" class="form-select" required>
                                    <option value="tambah" <?php echo $item->tipe === 'tambah' ? 'selected' : ''; ?>>Tambah (+)</option>
                                    <option value="kurang" <?php echo $item->tipe === 'kurang' ? 'selected' : ''; ?>>Kurang (−)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Nilai Hari (Opsional)</label>
                                <input type="number" step="0.1" min="0" name="nilai_hari" class="form-control" value="<?php echo e($item->nilai_hari ?? ''); ?>" placeholder="Kosongkan jika belum diverifikasi">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">Keterangan</label>
                                <textarea name="keterangan" class="form-control" rows="2"><?php echo e($item->keterangan ?? ''); ?></textarea>
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="editAktif<?php echo e($item->id); ?>" <?php echo $item->is_active ? 'checked' : ''; ?>>
                                    <label class="form-check-label small fw-semibold" for="editAktif<?php echo e($item->id); ?>">Aktif</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<!-- Modal Create -->
<div class="modal fade" id="modalCreateFaktor" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-slate-800">Tambah Faktor Penyesuaian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo route('admin.master-faktor-penyesuaian.store'); ?>" method="POST">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <div class="modal-body py-3">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label small fw-bold">Kode <span class="text-danger">*</span></label>
                            <input type="text" name="kode" class="form-control font-monospace" placeholder="CONTOH_FAKTOR" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-bold">Urutan</label>
                            <input type="number" name="urutan" min="0" class="form-control" value="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Nama Faktor <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tipe <span class="text-danger">*</span></label>
                            <select name="tipe" class="form-select" required>
                                <option value="tambah">Tambah (+)</option>
                                <option value="kurang">Kurang (−)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nilai Hari (Opsional)</label>
                            <input type="number" step="0.1" min="0" name="nilai_hari" class="form-control" placeholder="Kosongkan jika belum diverifikasi">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="createAktif" checked>
                                <label class="form-check-label small fw-semibold" for="createAktif">Aktif</label>
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

<?php
$content = ob_get_clean();
echo view('layouts.app', array_merge(get_defined_vars(), ['content' => $content, 'title' => $title]))->render();
