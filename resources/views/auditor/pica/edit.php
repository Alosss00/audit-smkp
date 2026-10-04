<?php
$title = 'Tindak Lanjut PICA — Auditee / PIC Area';
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-md-9 col-lg-8">
        <div class="mb-3">
            <a href="<?php echo e(route('auditor.pica.index')); ?>" class="text-decoration-none text-muted small">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar PICA Area
            </a>
        </div>

        <div class="card card-custom p-4 p-md-5">
            <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon-box bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-tools"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold text-slate-800 mb-0">Form Respon & Tindak Lanjut PICA</h4>
                        <p class="text-muted small mb-0">
                            Area: <strong><?php echo e($pica->auditDetail->auditSesi->area_audit ?? '-'); ?></strong> | 
                            Kriteria: <strong><?php echo e($pica->auditDetail->kriteria->kode_kriteria ?? '-'); ?></strong>
                        </p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <?php if($pica->kategori_temuan === 'kritikal'): ?>
                        <span class="badge bg-danger badge-role fs-6"><i class="bi bi-shield-exclamation me-1"></i> KRITIKAL</span>
                    <?php elseif($pica->kategori_temuan === 'mayor'): ?>
                        <span class="badge bg-warning text-dark badge-role fs-6"><i class="bi bi-exclamation-triangle-fill me-1"></i> MAYOR</span>
                    <?php else: ?>
                        <span class="badge bg-info text-white badge-role fs-6"><i class="bi bi-info-circle-fill me-1"></i> MINOR</span>
                    <?php endif; ?>

                    <?php if($pica->status === 'open'): ?>
                        <span class="badge bg-danger badge-role fs-6">Status: OPEN</span>
                    <?php elseif($pica->status === 'in_progress'): ?>
                        <span class="badge bg-warning text-dark badge-role fs-6">Status: IN PROGRESS</span>
                    <?php else: ?>
                        <span class="badge bg-success badge-role fs-6">Status: CLOSED</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Finding Description Box -->
            <div class="p-3 bg-light rounded-3 border mb-4">
                <h6 class="fw-bold text-slate-800 mb-1"><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>Deskripsi Temuan Audit (Assessor / Lead Auditor):</h6>
                <p class="text-slate-800 mb-0 small"><?php echo e($pica->deskripsi_temuan); ?></p>
                
                <?php if($pica->deskripsi_ketidaksesuaian): ?>
                    <h6 class="fw-bold text-slate-800 mb-1 mt-3"><i class="bi bi-x-circle-fill text-danger me-2"></i>Deskripsi Ketidaksesuaian:</h6>
                    <p class="text-slate-800 mb-0 small"><?php echo e($pica->deskripsi_ketidaksesuaian); ?></p>
                <?php endif; ?>

                <?php if($pica->justifikasi_kategori): ?>
                    <div class="mt-2 text-muted small">
                        <strong>Justifikasi Kategori:</strong> <?php echo e($pica->justifikasi_kategori); ?>

                    </div>
                <?php endif; ?>
            </div>

            <!-- Audit Trail / Last Log Info -->
            <?php if($lastLog): ?>
                <div class="alert alert-info rounded-3 p-2 px-3 mb-4 small">
                    <i class="bi bi-info-circle-fill me-1"></i>
                    Terakhir diubah oleh <strong><?php echo e($lastLog->user->name ?? 'Sistem'); ?></strong> pada <strong><?php echo e($lastLog->waktu_perubahan->format('d M Y H:i')); ?></strong>
                </div>
            <?php endif; ?>

            <form action="<?php echo e(route('auditor.pica.update', $pica->id)); ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <input type="hidden" name="_method" value="PUT">

                <!-- Section Editable oleh Responden / Auditee -->
                <div class="mb-3">
                    <label for="akar_masalah" class="form-label fw-bold small text-slate-800">
                        Akar Masalah <span class="text-danger">*</span>
                    </label>
                    <textarea name="akar_masalah" id="akar_masalah" rows="3" class="form-control" placeholder="Jelaskan analisis penyebab utama ketidaksesuaian ini..." required><?php echo e(old('akar_masalah', $pica->akar_masalah)); ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="tindakan_koreksi" class="form-label fw-bold small text-slate-800">Tindakan Perbaikan (Koreksi Immediate)</label>
                    <textarea name="tindakan_koreksi" id="tindakan_koreksi" rows="3" class="form-control" placeholder="Tindakan langsung yang telah/akan dilakukan untuk mengatasi temuan..."><?php echo e(old('tindakan_koreksi', $pica->tindakan_koreksi)); ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="tindakan_pencegahan" class="form-label fw-bold small text-slate-800">Tindakan Pencegahan (Preventive Action)</label>
                    <textarea name="tindakan_pencegahan" id="tindakan_pencegahan" rows="3" class="form-control" placeholder="Langkah-langkah jangka panjang agar ketidaksesuaian tidak terulang..."><?php echo e(old('tindakan_pencegahan', $pica->tindakan_pencegahan)); ?></textarea>
                </div>

                <div class="mb-4">
                    <label for="bukti_perbaikan" class="form-label fw-bold small text-slate-800">Upload Bukti Perbaikan (Foto / Dokumen PDF / Zip)</label>
                    <input type="file" name="bukti_perbaikan" id="bukti_perbaikan" class="form-control" accept="image/*,.pdf,.doc,.docx,.zip">
                    <?php if($pica->bukti_perbaikan): ?>
                        <div class="mt-2">
                            <a href="<?php echo e($pica->bukti_perbaikan_url); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-paperclip me-1"></i> Lihat Bukti Terunggah
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <hr class="my-4">

                <!-- Section Read-Only / Disabled -->
                <div class="p-3 bg-slate-50 border rounded-3 mb-4" style="background-color: #f8fafc;">
                    <h6 class="fw-bold text-muted mb-3"><i class="bi bi-lock-fill me-1"></i>Otoritas Lead Auditor / Admin (Read-Only)</h6>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-secondary">Kategori Temuan</label>
                            <input type="text" class="form-control bg-light text-muted text-uppercase fw-bold" value="<?php echo e($pica->kategori_temuan); ?> <?php echo e($pica->kategori_ditetapkan_manual ? '(Manual)' : '(Otomatis)'); ?>" disabled>
                            <small class="text-muted" style="font-size: 0.75rem;">Ditentukan oleh Lead Auditor / Sistem</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-secondary">Tenggat Waktu (Deadline)</label>
                            <input type="text" class="form-control bg-light text-muted" value="<?php echo e($pica->tenggat_waktu ? $pica->tenggat_waktu->format('d M Y') : 'Belum Ditentukan'); ?>" disabled>
                            <small class="text-muted" style="font-size: 0.75rem;">Ditentukan oleh Lead Auditor</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-secondary">Status PICA</label>
                            <input type="text" class="form-control bg-light text-muted text-uppercase fw-bold" value="<?php echo e($pica->status); ?>" disabled>
                            <small class="text-muted" style="font-size: 0.75rem;">Status diubah oleh Lead Auditor saat verifikasi</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small text-secondary">Catatan Verifikasi Auditor</label>
                            <textarea class="form-control bg-light text-muted" rows="2" disabled><?php echo e($pica->catatan_verifikasi_auditor ?? 'Belum ada catatan verifikasi'); ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-end gap-2 pt-2 border-top">
                    <a href="<?php echo e(route('auditor.pica.index')); ?>" class="btn btn-outline-secondary rounded-3 px-4">Batal</a>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 fw-semibold">
                        <i class="bi bi-save me-1"></i> Simpan Respon PICA
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
echo view('layouts.app', array_merge(get_defined_vars(), ['content' => $content, 'title' => $title]))->render();
