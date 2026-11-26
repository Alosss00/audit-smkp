<?php
$title = 'Formulir OFI (' . config('audit.no_dokumen_ofi', 'TT-MGT-FRS-026G') . ') — SMKP Minerba';
ob_start();

$isAuditorRole = auth()->check() && auth()->user()->role === 'auditor';
$cetakRoute = $isAuditorRole
    ? route('auditor.audit-sesi.ofi.cetak', $sesi->id)
    : route('admin.audit-sesi.ofi.cetak', $sesi->id);
$backRoute = $isAuditorRole
    ? route('auditor.audit-sesi.index')
    : route('admin.audit-sesi.index');
?>

<div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
    <div>
        <a href="<?php echo e($backRoute); ?>" class="text-decoration-none text-muted small">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Sesi Audit
        </a>
        <div class="d-flex align-items-center flex-wrap gap-2 mt-1">
            <h2 class="fw-bold text-slate-800 mb-0">
                <i class="bi bi-lightbulb-fill text-warning me-2"></i>Peluang untuk Perbaikan (OFI)
            </h2>
            <span class="badge bg-primary rounded-pill px-3 py-2"><?php echo e(config('audit.no_dokumen_ofi', 'TT-MGT-FRS-026G')); ?></span>
            <span class="badge bg-light text-secondary border rounded-pill px-3 py-2">Opsional</span>
        </div>
        <p class="text-muted small mb-0 mt-1">
            Auditi: <strong><?php echo e($sesi->perusahaan->nama_perusahaan ?? $sesi->area_audit); ?></strong> &bull;
            Periode: <strong>Tahun <?php echo e($sesi->tahun_periode ?? '-'); ?></strong>
        </p>
    </div>

    <div class="d-flex flex-wrap gap-2">
        <a href="<?php echo e($cetakRoute); ?>" target="_blank" class="btn btn-dark rounded-3 px-3 fw-semibold shadow-sm">
            <i class="bi bi-printer-fill me-1"></i> Cetak Formulir OFI
        </a>
        <?php if (! $isReadOnly): ?>
            <button type="button" class="btn btn-primary rounded-3 px-3 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCreateOfi">
                <i class="bi bi-plus-lg me-1"></i> Tambah OFI
            </button>
        <?php endif; ?>
    </div>
</div>

<div class="card card-custom p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-center">
                <tr>
                    <th style="width: 75px;">No. OFI</th>
                    <th class="text-start" style="width: 190px;">Sub-Elemen / Kriteria</th>
                    <th class="text-start">Deskripsi Peluang Perbaikan</th>
                    <th class="text-start">Rekomendasi</th>
                    <th class="text-start" style="width: 160px;">Referensi</th>
                    <?php if (! $isReadOnly): ?><th style="width: 110px;">Aksi</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if ($ofis->isEmpty()): ?>
                    <tr>
                        <td colspan="<?php echo $isReadOnly ? '5' : '6'; ?>" class="text-center text-muted py-5">
                            Belum ada catatan Peluang untuk Perbaikan (OFI) pada sesi audit ini.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($ofis as $ofi): ?>
                        <tr>
                            <td class="text-center fw-bold text-primary">OFI-<?php echo e(str_pad((string) $ofi->nomor_urut, 2, '0', STR_PAD_LEFT)); ?></td>
                            <td class="small">
                                <?php if ($ofi->subElemen): ?>
                                    <strong><?php echo e($ofi->subElemen->kode_sub); ?></strong> <?php echo e($ofi->subElemen->nama_sub); ?>
                                <?php else: ?>
                                    <span class="text-muted">Umum</span>
                                <?php endif; ?>
                                <?php if ($ofi->kriteria): ?>
                                    <div class="text-muted">Kriteria: <?php echo e($ofi->kriteria->kode_kriteria); ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="small"><?php echo nl2br(e($ofi->deskripsi)); ?></td>
                            <td class="small"><?php echo nl2br(e($ofi->rekomendasi)); ?></td>
                            <td class="small text-muted"><?php echo e($ofi->referensi ?: '-'); ?></td>
                            <?php if (! $isReadOnly): ?>
                                <td class="text-center">
                                    <div class="d-inline-flex gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-3" data-bs-toggle="modal" data-bs-target="#modalEditOfi<?php echo e($ofi->id); ?>">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <form action="<?php echo e(route('admin.audit-sesi.ofi.destroy', [$sesi->id, $ofi->id])); ?>" method="POST" class="d-inline"
                                              data-confirm="Hapus OFI #<?php echo e($ofi->nomor_urut); ?>? Nomor urut tidak akan dipakai ulang.">
                                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-3"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (! $isReadOnly): ?>
    <?php foreach ($ofis as $ofi): ?>
        <div class="modal fade" id="modalEditOfi<?php echo e($ofi->id); ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold text-slate-800">Edit OFI #<?php echo e($ofi->nomor_urut); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="<?php echo e(route('admin.audit-sesi.ofi.update', [$sesi->id, $ofi->id])); ?>" method="POST">
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                        <input type="hidden" name="_method" value="PUT">
                        <div class="modal-body py-3">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Sub-Elemen (Opsional)</label>
                                    <select name="sub_elemen_id" class="form-select">
                                        <option value="">-- Umum / Tidak Spesifik --</option>
                                        <?php foreach ($subElemens as $sub): ?>
                                            <option value="<?php echo e($sub->id); ?>" <?php echo (int) $ofi->sub_elemen_id === (int) $sub->id ? 'selected' : ''; ?>>
                                                <?php echo e($sub->kode_sub); ?> - <?php echo e($sub->nama_sub); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Referensi (Opsional)</label>
                                    <input type="text" name="referensi" class="form-control" maxlength="255" value="<?php echo e($ofi->referensi ?? ''); ?>">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold">Deskripsi Peluang Perbaikan <span class="text-danger">*</span></label>
                                    <textarea name="deskripsi" class="form-control" rows="3" required><?php echo e($ofi->deskripsi); ?></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold">Rekomendasi <span class="text-danger">*</span></label>
                                    <textarea name="rekomendasi" class="form-control" rows="3" required><?php echo e($ofi->rekomendasi); ?></textarea>
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

    <div class="modal fade" id="modalCreateOfi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold text-slate-800">Tambah Peluang untuk Perbaikan (OFI)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?php echo e(route('admin.audit-sesi.ofi.store', $sesi->id)); ?>" method="POST">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <div class="modal-body py-3">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Sub-Elemen (Opsional)</label>
                                <select name="sub_elemen_id" class="form-select">
                                    <option value="">-- Umum / Tidak Spesifik --</option>
                                    <?php foreach ($subElemens as $sub): ?>
                                        <option value="<?php echo e($sub->id); ?>"><?php echo e($sub->kode_sub); ?> - <?php echo e($sub->nama_sub); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Referensi (Opsional)</label>
                                <input type="text" name="referensi" class="form-control" maxlength="255" placeholder="Contoh: Kepdirjen 185 Elemen II.2">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">Deskripsi Peluang Perbaikan <span class="text-danger">*</span></label>
                                <textarea name="deskripsi" class="form-control" rows="3" required></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">Rekomendasi <span class="text-danger">*</span></label>
                                <textarea name="rekomendasi" class="form-control" rows="3" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan OFI</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
echo view('layouts.app', array_merge(get_defined_vars(), ['content' => $content, 'title' => $title]))->render();
