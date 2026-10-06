<?php
$title = 'Rekap Hasil Audit SMKP — Auditor';
ob_start();
?>
<div class="mb-4 d-flex align-items-center justify-content-between">
    <div>
        <a href="<?php echo e(route('auditor.audit-sesi.index')); ?>" class="text-decoration-none text-muted small">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Audit Saya
        </a>
        <h2 class="fw-bold text-slate-800 mb-0 mt-1">Rekap Hasil Audit Internal SMKP</h2>
        <p class="text-muted small mb-0">Area: <strong><?php echo e($sesi->area_audit); ?></strong> | Tanggal Pelaksanaan: <strong><?php echo e($sesi->tanggal_mulai->format('d M Y')); ?> - <?php echo e($sesi->tanggal_selesai->format('d M Y')); ?></strong></p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="<?php echo e(route('auditor.audit-sesi.laporan-detail', $sesi->id)); ?>" class="btn btn-outline-info text-dark fw-semibold rounded-3 px-3">
            <i class="bi bi-file-earmark-text me-1"></i> Laporan Detail Sesi
        </a>
        <a href="<?php echo e(route('auditor.audit-sesi.export-excel', $sesi->id)); ?>" class="btn btn-success rounded-3 px-3">
            <i class="bi bi-file-earmark-excel me-1"></i> Export Excel (.xlsx)
        </a>
        <a href="<?php echo e(route('auditor.audit-sesi.cetak', $sesi->id)); ?>" target="_blank" class="btn btn-dark rounded-3 px-3">
            <i class="bi bi-printer me-1"></i> Cetak Laporan (PDF)
        </a>
        <a href="<?php echo e(route('auditor.pica.index')); ?>" class="btn btn-outline-primary fw-semibold rounded-3 px-3">
            <i class="bi bi-tools me-1"></i> Tindak Lanjut PICA
        </a>
    </div>
</div>

<!-- Score Overview Card -->
<div class="row g-4 mb-4">
    <div class="col-md-6 col-lg-8">
        <div class="card card-custom p-4 h-100 border-start border-5 border-info">
            <h5 class="fw-bold text-slate-800 mb-3">Tingkat Pencapaian Penerapan SMKP</h5>
            <div class="d-flex align-items-center gap-4">
                <div class="display-3 fw-bold text-primary"><?php echo e(number_format($skorAkhir, 2)); ?>%</div>
                <div>
                    <?php if($skorAkhir >= 85): ?>
                        <span class="badge bg-success p-2 px-3 fs-6 rounded-pill mb-2"><i class="bi bi-shield-check me-1"></i> Pencapaian Baik / Kepatuhan Tinggi</span>
                        <p class="text-muted small mb-0">Penerapan SMKP Minerba Kepdirjen 185 memenuhi standar evaluasi tinggi.</p>
                    <?php elseif($skorAkhir >= 70): ?>
                        <span class="badge bg-warning text-dark p-2 px-3 fs-6 rounded-pill mb-2"><i class="bi bi-exclamation-triangle me-1"></i> Pencapaian Cukup / Perlu Peningkatan</span>
                        <p class="text-muted small mb-0">Memerlukan tindakan perbaikan pada beberapa kriteria minor.</p>
                    <?php else: ?>
                        <span class="badge bg-danger p-2 px-3 fs-6 rounded-pill mb-2"><i class="bi bi-x-circle me-1"></i> Perbaikan Mayor Dibutuhkan</span>
                        <p class="text-muted small mb-0">Terdapat banyak kriteria kritis yang belum memenuhi standar regulasi.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-4">
        <div class="card card-custom p-4 h-100 bg-slate-900 text-white">
            <h6 class="text-uppercase text-light opacity-75 fw-bold mb-3">Informasi Sesi Audit</h6>
            <div class="mb-2">
                <small class="text-light opacity-50 d-block">Auditor Pelaksana:</small>
                <strong class="text-white"><?php echo e($sesi->user->name); ?></strong>
            </div>
            <div class="mb-2">
                <small class="text-light opacity-50 d-block">Status Sesi:</small>
                <?php if($sesi->status === 'draft'): ?>
                    <span class="badge bg-secondary">Draft</span>
                <?php elseif($sesi->status === 'berjalan'): ?>
                    <span class="badge bg-warning text-dark">Berjalan</span>
                <?php else: ?>
                    <span class="badge bg-success">Selesai (Snapshot Locked)</span>
                <?php endif; ?>
            </div>
            <div>
                <small class="text-light opacity-50 d-block">Terakhir Diperbarui:</small>
                <span class="text-white"><?php echo e($sesi->updated_at->format('d M Y H:i')); ?></span>
            </div>
        </div>
    </div>
</div>

<style>
    .table-kepdirjen-185 {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 0.825rem;
        border-color: #a6a6a6 !important;
    }
    
    .table-kepdirjen-185 th, 
    .table-kepdirjen-185 td {
        border: 1px solid #a6a6a6 !important;
        vertical-align: middle;
    }
    
    .th-vertical {
        white-space: nowrap;
        height: 150px;
        padding: 8px 4px !important;
        text-align: center;
        vertical-align: bottom !important;
    }
    
    .th-vertical > div,
    .th-vertical span.text-vertical {
        writing-mode: vertical-rl;
        transform: scale(-1, -1);
        display: inline-block;
        white-space: nowrap;
        margin: 0 auto;
        font-weight: 700;
        font-size: 0.78rem;
    }

    .bg-elemen-induk {
        background-color: #d9d9d9 !important;
        font-weight: 700;
    }

    .bg-sub-elemen {
        background-color: #f2f2f2 !important;
        font-weight: 600;
    }

    .bg-blue-input {
        background-color: #5b9bd5 !important;
        color: #ffffff !important;
        font-weight: 700;
        text-align: center;
    }

    .bg-blue-header {
        background-color: #5b9bd5 !important;
        color: #ffffff !important;
    }

    .bg-green-total {
        background-color: #e2efda !important;
        font-weight: 700;
        text-align: center;
    }

    .bg-green-header {
        background-color: #c6e0b4 !important;
        color: #000000 !important;
    }
</style>

<!-- Detailed Rekap Table per Elemen, Sub-Elemen, & Kriteria (Format Kepdirjen 185) -->
<div class="card card-custom p-4 mb-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="fw-bold mb-0">
            <i class="bi bi-diagram-3-fill me-2 text-primary"></i>Rekap Rincian Nilai Audit Internal SMKP (Format Kepdirjen 185)
        </h5>
        <button class="btn btn-sm btn-outline-primary rounded-3" id="toggleAllHierarkiBtn">
            <i class="bi bi-arrows-collapse me-1"></i> Buka / Tutup Semua Rincian
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-kepdirjen-185 align-middle mb-0">
            <thead class="table-light align-middle text-uppercase fw-bold text-center">
                <tr>
                    <th rowspan="2" colspan="3" class="align-middle text-center bg-white text-dark fw-bold">KRITERIA</th>
                    <th rowspan="2" class="th-vertical bg-white text-dark"><div>Nilai Elemen %</div></th>
                    <th rowspan="2" class="th-vertical bg-white text-dark"><div>Nilai Sub Elemen</div></th>
                    <th rowspan="2" class="th-vertical bg-white text-dark"><div>Nilai Sub sub Elemen</div></th>
                    <th colspan="4" class="text-center align-middle bg-white text-dark fw-bold">Nilai Audit</th>
                    <th rowspan="2" class="align-middle text-center bg-white text-dark fw-bold" style="width: 110px;">KETERANGAN</th>
                </tr>
                <tr>
                    <th class="th-vertical bg-blue-header"><div>Nilai Sub Elemen</div></th>
                    <th class="th-vertical bg-white text-dark"><div>Nilai sub sub elemen</div></th>
                    <th class="th-vertical bg-green-header"><div>Total Nilai Elemen</div></th>
                    <th class="th-vertical bg-white text-dark"><div>Presentase Nilai Elemen</div></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($hierarki as $el): ?>
                    <!-- Level 1: Baris Elemen Induk -->
                    <tr class="bg-elemen-induk border-top border-2 border-secondary" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target=".el-collapse-<?php echo e($el['elemen_id']); ?>">
                        <td colspan="3" class="text-start">
                            <i class="bi bi-chevron-down toggle-icon me-1 text-primary"></i>
                            ELEMEN <?php echo e($el['kode_elemen']); ?> <?php echo e(strtoupper($el['nama_elemen'])); ?>

                        </td>
                        <td class="text-center"><?php echo e(number_format($el['bobot'], 2)); ?>%</td>
                        <td class="text-center"><?php echo e(number_format($el['total_nilai_maks_efektif'], 0)); ?></td>
                        <td class="text-center text-muted">-</td>
                        <td class="text-center text-muted bg-blue-input" style="opacity: 0.6;">-</td>
                        <td class="text-center text-muted">-</td>
                        <td class="bg-green-total text-center"><?php echo e(number_format($el['total_nilai_aktual'], 2)); ?></td>
                        <td class="text-center fw-bold"><?php echo e(number_format($el['persentase'], 2)); ?>%</td>
                        <td class="text-center small"></td>
                    </tr>

                    <!-- Level 2: Baris Sub-Elemen -->
                    <?php foreach($el['sub_elemens'] as $sub): ?>
                        <?php if(!empty($sub['is_direct'])): ?>
                            <!-- Sub-Elemen Penilaian Langsung (1 Baris) -->
                            <tr class="collapse show el-collapse-<?php echo e($el['elemen_id']); ?> bg-sub-elemen">
                                <td class="text-center font-monospace fw-bold" style="width: 70px;"><?php echo e($sub['kode_sub']); ?></td>
                                <td colspan="2" class="fw-semibold text-slate-800">
                                    <div><?php echo e($sub['nama_sub']); ?></div>
                                    <?php if(!empty($sub['direct_detail']['catatan'])): ?>
                                        <div class="mt-1 small text-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i>Temuan: <?php echo e($sub['direct_detail']['catatan']); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center text-muted">-</td>
                                <td class="text-center font-monospace fw-bold"><?php echo e(number_format($sub['total_nilai_maks_efektif'], 0)); ?></td>
                                <td class="text-center text-muted">-</td>
                                <td class="bg-blue-input font-monospace">
                                    <?php if(!empty($sub['direct_detail']['is_na'])): ?>
                                        <span class="badge bg-secondary">N/A</span>
                                    <?php else: ?>
                                        <span class="<?php echo e($sub['total_nilai_aktual'] < $sub['total_nilai_maks_efektif'] ? 'text-warning' : 'text-white'); ?> fw-bold">
                                            <?php echo e(number_format($sub['total_nilai_aktual'], 2)); ?>

                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center text-muted">-</td>
                                <td class="text-center text-muted">-</td>
                                <td class="text-center text-muted">-</td>
                                <td class="text-center small text-secondary fw-semibold">
                                    <?php echo e(!empty($sub['direct_detail']['is_na']) ? 'N/A' : ($sub['direct_detail']['catatan'] ?? '')); ?>

                                </td>
                            </tr>
                        <?php else: ?>
                            <!-- Sub-Elemen dengan Beberapa Sub-sub Elemen / Kriteria -->
                            <tr class="collapse show el-collapse-<?php echo e($el['elemen_id']); ?> bg-sub-elemen" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target=".sub-collapse-<?php echo e($sub['sub_elemen_id']); ?>">
                                <td class="text-center font-monospace fw-bold" style="width: 70px;">
                                    <i class="bi bi-chevron-down toggle-icon me-1 text-secondary small"></i><?php echo e($sub['kode_sub']); ?>

                                </td>
                                <td colspan="2" class="fw-semibold text-slate-800"><?php echo e($sub['nama_sub']); ?></td>
                                <td class="text-center text-muted">-</td>
                                <td class="text-center font-monospace fw-bold"><?php echo e(number_format($sub['total_nilai_maks_efektif'], 0)); ?></td>
                                <td class="text-center text-muted">-</td>
                                <td class="bg-blue-input font-monospace"><?php echo e(number_format($sub['total_nilai_aktual'], 2)); ?></td>
                                <td class="text-center text-muted">-</td>
                                <td class="text-center text-muted">-</td>
                                <td class="text-center text-muted">-</td>
                                <td class="text-center small text-secondary fw-semibold"></td>
                            </tr>

                            <!-- Level 3: Baris Sub-sub Elemen / Kriteria Penilaian -->
                            <?php foreach($sub['details'] as $d): ?>
                                <tr class="collapse show el-collapse-<?php echo e($el['elemen_id']); ?> sub-collapse-<?php echo e($sub['sub_elemen_id']); ?> bg-white">
                                    <td style="width: 70px;"></td>
                                    <td class="text-center font-monospace small text-secondary" style="width: 85px;"><?php echo e($d['kode_kriteria']); ?></td>
                                    <td class="small text-slate-700 ps-3">
                                        <div><?php echo e($d['deskripsi']); ?></div>
                                        <?php if($d['catatan']): ?>
                                            <div class="mt-1 small text-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i>Temuan: <?php echo e($d['catatan']); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center text-muted">-</td>
                                    <td class="text-center text-muted">-</td>
                                    <td class="text-center font-monospace small text-muted"><?php echo e(number_format($d['nilai_maksimal'], 0)); ?></td>
                                    <td class="text-center text-muted bg-blue-input" style="opacity: 0.2;">-</td>
                                    <td class="text-center font-monospace fw-bold">
                                        <?php if($d['is_na']): ?>
                                            <span class="badge bg-secondary">N/A</span>
                                        <?php else: ?>
                                            <span class="<?php echo e($d['nilai'] < $d['nilai_maksimal'] ? 'text-danger' : 'text-success'); ?>"><?php echo e(number_format($d['nilai'], 0)); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center text-muted">-</td>
                                    <td class="text-center text-muted">-</td>
                                    <td class="text-center small text-muted"><?php echo e($d['is_na'] ? 'N/A' : ($d['catatan'] ?? '')); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
            <tfoot class="table-light">
                <tr class="fw-bold fs-6">
                    <td colspan="8" class="text-end text-uppercase">Total Pencapaian Keseluruhan:</td>
                    <td class="bg-green-total text-center fs-5 text-success"><?php echo e(number_format($skorAkhir, 2)); ?>%</td>
                    <td class="text-center text-primary fs-5"><?php echo e(number_format(array_sum(array_column($rekap, 'persentase')) / max(count($rekap), 1), 2)); ?>%</td>
                    <td class="text-center"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<script nonce="<?php echo e($cspNonce ?? ''); ?>">
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('toggleAllHierarkiBtn');
        let isExpanded = true;

        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                const collapses = document.querySelectorAll('.collapse');
                collapses.forEach(el => {
                    if (isExpanded) {
                        el.classList.remove('show');
                    } else {
                        el.classList.add('show');
                    }
                });
                isExpanded = !isExpanded;
                toggleBtn.innerHTML = isExpanded 
                    ? '<i class="bi bi-arrows-collapse me-1"></i> Tutup Semua Rincian' 
                    : '<i class="bi bi-arrows-expand me-1"></i> Buka Semua Rincian';
            });
        }
    });
</script>

<!-- Finding Notes & Proof Attachments List -->
<div class="card card-custom p-4">
    <h5 class="fw-bold mb-3"><i class="bi bi-card-checklist me-2 text-warning"></i>Catatan Temuan & Bukti Lampiran Audit</h5>

    <?php
        $findings = $sesi->auditDetails->filter(function($d) {
            return !empty($d->catatan) || !empty($d->lampiran);
        });
    ?>

    <?php if($findings->isEmpty()): ?>
        <div class="text-center py-4 text-muted">
            <i class="bi bi-check-all fs-2 d-block mb-1 opacity-50"></i>
            Tidak ada catatan temuan atau lampiran khusus pada sesi audit ini.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 100px;">Kriteria</th>
                        <th>Pertanyaan / Kriteria</th>
                        <th class="text-center" style="width: 90px;">Skor</th>
                        <th>Catatan Temuan</th>
                        <th style="width: 160px;">Bukti Lampiran</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($findings as $item): ?>
                        <tr>
                            <td><span class="badge bg-secondary"><?php echo e($item->kriteria->kode_kriteria ?? '-'); ?></span></td>
                            <td class="small"><?php echo e($item->kriteria->deskripsi ?? '-'); ?></td>
                            <td class="text-center font-monospace fw-bold">
                                <?php if($item->is_na): ?>
                                    <span class="badge bg-secondary">N/A</span>
                                <?php else: ?>
                                    <?php echo e(number_format($item->nilai, 2)); ?> / <?php echo e(number_format($item->kriteria->nilai_maksimal ?? 4, 2)); ?>

                                <?php endif; ?>
                            </td>
                            <td class="small text-slate-800" style="white-space: pre-line;"><?php echo e($item->catatan ?? '-'); ?></td>
                            <td>
                                <?php if(!empty($item->lampiran_urls)): ?>
                                    <div class="d-flex flex-column gap-1">
                                        <?php foreach($item->lampiran_urls as $lFile): ?>
                                            <a href="<?php echo e($lFile['url']); ?>" target="_blank" class="btn btn-sm btn-outline-info text-dark py-0 px-2 text-start text-truncate" style="max-width: 170px;" title="<?php echo e($lFile['name']); ?>">
                                                <i class="bi bi-paperclip me-1"></i> <?php echo e($lFile['name']); ?>

                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted small">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
echo view('layouts.app', array_merge(get_defined_vars(), ['content' => $content, 'title' => $title]))->render();
