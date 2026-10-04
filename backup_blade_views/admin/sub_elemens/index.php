<?php
$title = 'Master Sub-Elemen — SMKP Minerba';

ob_start();
?>
<div class="row align-items-center mb-4">
    <div class="col-md-7">
        <h2 class="fw-bold text-slate-800 mb-1">
            <i class="bi bi-diagram-3-fill text-info me-2"></i>Kelola Master Sub-Elemen & Sub-sub Elemen
        </h2>
        <p class="text-muted mb-0">Kelola hirarki struktur Sub-Elemen dan Sub-sub Elemen (Kriteria Pertanyaan Penilaian SMKP).</p>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0 d-flex gap-2 justify-content-md-end">
        <button class="btn btn-info text-dark rounded-3 px-3 py-2 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#createSubModal">
            <i class="bi bi-plus-lg me-1"></i> Tambah Sub-Elemen
        </button>
        <button class="btn btn-primary rounded-3 px-3 py-2 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#createSubSubModal">
            <i class="bi bi-plus-circle me-1"></i> Tambah Sub-sub Elemen
        </button>
    </div>
</div>

<?php if ($elemens->isNotEmpty()): ?>
    <?php foreach ($elemens as $elemen): ?>
        <div class="card card-custom mb-4 overflow-hidden border-0 shadow-sm">
            <!-- Card Header per Elemen -->
            <div class="card-header bg-white p-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-primary fs-6 px-3 py-2 text-uppercase">Elemen <?php echo e($elemen->kode_elemen); ?></span>
                    <div>
                        <h5 class="fw-bold text-slate-800 mb-0"><?php echo e($elemen->nama_elemen); ?></h5>
                        <div class="small text-muted mt-1">
                            Bobot Elemen: <span class="fw-semibold text-dark"><?php echo number_format($elemen->bobot, 2); ?>%</span>
                            <?php if ($elemen->total_nilai_sub_elemen > 0): ?>
                                <?php
                                    $terpakai = (int) $elemen->total_nilai_sub_terpakai;
                                    $maxSub = (int) $elemen->total_nilai_sub_elemen;
                                    $isOver = $terpakai > $maxSub;
                                ?>
                                <span class="ms-3 border-start ps-3">
                                    Total Sub-Elemen: 
                                    <span class="badge <?php echo $isOver ? 'bg-danger' : 'bg-light text-dark border'; ?> font-monospace ms-1">
                                        <?php echo $terpakai; ?> / <?php echo $maxSub; ?>
                                    </span>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-info text-dark rounded-3 px-3 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#createSubForElemenModal<?php echo e($elemen->id); ?>">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Sub-Elemen
                    </button>
                </div>
            </div>

            <!-- Table for this Elemen -->
            <div class="card-body p-0">
                <?php if ($elemen->subElemens->isEmpty()): ?>
                    <div class="p-4 text-center text-muted fst-italic">
                        Belum ada data Sub-Elemen untuk Elemen <?php echo e($elemen->kode_elemen); ?>. 
                        <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none ms-1" data-bs-toggle="modal" data-bs-target="#createSubForElemenModal<?php echo e($elemen->id); ?>">
                            Klik di sini untuk membuat Sub-Elemen baru.
                        </button>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-uppercase fw-bold text-secondary small border-bottom">
                                <tr>
                                    <th style="width: 140px;" class="text-center">Kode Sub</th>
                                    <th style="width: 140px;" class="text-center">Kode Sub-Sub</th>
                                    <th>Nama / Deskripsi Pertanyaan</th>
                                    <th style="width: 140px;" class="text-center">Nilai Maksimal</th>
                                    <th style="width: 140px;" class="text-center">Jumlah Kriteria</th>
                                    <th style="width: 160px;" class="text-end pe-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($elemen->subElemens as $sub): ?>
                                    <!-- Row Sub-Elemen -->
                                    <tr class="table-light border-bottom <?php echo $sub->is_na ? 'bg-warning bg-opacity-10' : ''; ?>">
                                        <td class="text-center fw-bold align-middle">
                                            <span class="badge bg-info text-dark font-monospace fs-6 py-1 px-2">Sub <?php echo e($sub->kode_sub); ?></span>
                                        </td>
                                        <td class="text-center text-muted align-middle">-</td>
                                        <td class="fw-bold text-slate-800 align-middle">
                                            <?php echo e($sub->nama_sub); ?>

                                            <?php if ($sub->is_na): ?>
                                                <span class="badge bg-warning text-dark ms-2"><i class="bi bi-slash-circle me-1"></i>N/A (Not Applicable)</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center align-middle font-monospace fw-bold <?php echo $sub->is_na ? 'text-muted text-decoration-line-through' : 'text-primary'; ?>">
                                            <?php echo (int) ($sub->nilai_maksimal ?? 0); ?>
                                        </td>
                                        <td class="text-center align-middle">
                                            <?php if ($sub->kriterias->count() === 1 && $sub->kriterias->first()->kode_kriteria === $sub->kode_sub): ?>
                                                <span class="badge bg-info text-dark rounded-pill px-3 py-1 fs-6" title="Sub-Elemen ini dinilai langsung tanpa sub-sub elemen">Penilaian Langsung</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary rounded-pill px-3 py-1 fs-6"><?php echo e($sub->kriterias->count()); ?> Kriteria</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end align-middle pe-3">
                                            <div class="btn-group gap-1">
                                                <form action="<?php echo route('admin.sub-elemens.toggle-na', $sub->id); ?>" method="POST" class="d-inline">
                                                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                                    <input type="hidden" name="_method" value="PATCH">
                                                    <button type="submit" class="btn btn-sm <?php echo $sub->is_na ? 'btn-warning text-dark fw-bold' : 'btn-outline-warning'; ?> rounded-2" title="<?php echo $sub->is_na ? 'Aktifkan Kembali Sub-Elemen ini' : 'Set Sub-Elemen ini menjadi N/A'; ?>">
                                                        <i class="bi bi-slash-circle me-1"></i> <?php echo $sub->is_na ? 'N/A Active' : 'Set N/A'; ?>
                                                    </button>
                                                </form>

                                                <button type="button" class="btn btn-sm btn-outline-success rounded-2" data-bs-toggle="modal" data-bs-target="#createSubSubForSubModal<?php echo e($sub->id); ?>" title="Tambah Sub-sub Elemen di bawah <?php echo e($sub->kode_sub); ?>">
                                                    <i class="bi bi-plus-lg"></i> Sub-sub
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-primary rounded-2" data-bs-toggle="modal" data-bs-target="#editSubModal<?php echo e($sub->id); ?>" title="Edit Sub-Elemen">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                                <form action="<?php echo route('admin.sub-elemens.destroy', $sub->id); ?>" method="POST" class="d-inline" data-confirm="Nonaktifkan Sub-Elemen ini?">
                                                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-2" title="Hapus Sub-Elemen">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Rows Sub-sub Elemen (Kriteria) -->
                                    <?php foreach ($sub->kriterias as $kriteria): ?>
                                        <?php if ($kriteria->kode_kriteria !== $sub->kode_sub): ?>
                                            <tr class="border-bottom <?php echo $kriteria->is_na || $sub->is_na ? 'table-warning bg-opacity-25' : ''; ?>">
                                                <td class="text-center text-muted align-middle">-</td>
                                                <td class="text-center align-middle">
                                                    <span class="badge bg-dark font-monospace fs-6 py-1 px-2"><?php echo e($kriteria->kode_kriteria); ?></span>
                                                </td>
                                                <td class="small text-slate-700 align-middle">
                                                    <div class="fw-semibold">
                                                        <i class="bi bi-arrow-return-right text-primary me-1 ms-2"></i>
                                                        <span class="<?php echo $kriteria->is_na || $sub->is_na ? 'text-muted text-decoration-line-through' : ''; ?>"><?php echo e($kriteria->deskripsi); ?></span>
                                                        <?php if ($kriteria->is_na || $sub->is_na): ?>
                                                            <span class="badge bg-warning text-dark ms-2" style="font-size: 0.7rem;"><i class="bi bi-slash-circle me-1"></i>N/A</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td class="text-center align-middle font-monospace text-muted small <?php echo $kriteria->is_na || $sub->is_na ? 'text-decoration-line-through' : ''; ?>">
                                                    <?php echo (int) ($kriteria->nilai_maksimal ?? 0); ?>
                                                </td>
                                                <td class="text-center text-muted align-middle">-</td>
                                                <td class="text-end align-middle pe-3">
                                                    <div class="btn-group gap-1">
                                                        <form action="<?php echo route('admin.kriterias.toggle-na', $kriteria->id); ?>" method="POST" class="d-inline">
                                                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                                            <input type="hidden" name="_method" value="PATCH">
                                                            <button type="submit" class="btn btn-sm <?php echo $kriteria->is_na ? 'btn-warning text-dark fw-bold' : 'btn-outline-warning'; ?> rounded-2 py-0 px-2" style="font-size: 0.75rem;" title="<?php echo $kriteria->is_na ? 'Aktifkan Kriteria' : 'Set Kriteria menjadi N/A'; ?>">
                                                                <?php echo $kriteria->is_na ? 'N/A' : 'Set N/A'; ?>
                                                            </button>
                                                        </form>
                                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-2 py-0 px-2" style="font-size: 0.75rem;" data-bs-toggle="modal" data-bs-target="#editSubSubModal<?php echo e($kriteria->id); ?>" title="Edit Sub-sub Elemen, Nilai Maksimal, dan Rubrik">
                                                            <i class="bi bi-pencil"></i> Edit
                                                        </button>
                                                        <form action="<?php echo route('admin.kriterias.destroy', $kriteria->id); ?>" method="POST" class="d-inline" data-confirm="Hapus/nonaktifkan Sub-sub Elemen <?php echo e($kriteria->kode_kriteria); ?>?">
                                                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                                            <input type="hidden" name="_method" value="DELETE">
                                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-2 py-0 px-1" style="font-size: 0.75rem;" title="Hapus Sub-sub Elemen">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="card card-custom p-4 text-center text-muted">
        Belum ada data master elemen.
    </div>
<?php endif; ?>

<!-- Modal General: Tambah Sub-sub Elemen Baru (Multi-Baris Dinamis) -->
<div class="modal fade" id="createSubSubModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content card-custom border-0">
            <form action="<?php echo route('admin.kriterias.store'); ?>" method="POST" id="formGeneralBatchSubSub">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle text-primary me-2"></i>Tambah Sub-sub Elemen (Bisa Tambah Lebih dari 1 Sekaligus)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- 1. Pilih Induk Sub-Elemen -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih Induk Sub-Elemen <span class="text-danger">*</span></label>
                        <select name="sub_elemen_id" id="generalSubElemenSelect" class="form-select select-searchable" required>
                            <option value="">-- Pilih Induk Sub-Elemen --</option>
                            <?php foreach ($subElemens as $s): ?>
                                <option value="<?php echo e($s->id); ?>" 
                                    data-kode="<?php echo e($s->kode_sub); ?>" 
                                    data-nama="<?php echo e($s->nama_sub); ?>" 
                                    data-max="<?php echo (int) ($s->nilai_maksimal ?? 4); ?>"
                                    data-count="<?php echo e($s->kriterias->where('kode_kriteria', '!=', $s->kode_sub)->count()); ?>">
                                    Sub <?php echo e($s->kode_sub); ?> - <?php echo e($s->nama_sub); ?> (Nilai Max: <?php echo (int) ($s->nilai_maksimal ?? 4); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text text-muted">Setelah memilih Sub-Elemen induk, Anda dapat menambahkan 1 atau beberapa Sub-sub Elemen sekaligus di bawah.</div>
                    </div>

                    <!-- 2. Banner Informasi Induk Sub-Elemen -->
                    <div id="generalSubElemenInfo" class="alert alert-info bg-info bg-opacity-10 border-info border-opacity-25 rounded-3 d-none mb-4">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div>
                                <span class="badge bg-info text-dark font-monospace fs-6 me-2" id="infoKodeSub">Sub -</span>
                                <strong class="text-slate-800 fs-6" id="infoNamaSub">Nama Sub Elemen</strong>
                            </div>
                            <div>
                                <span class="badge bg-white text-dark border font-monospace px-3 py-2">
                                    Nilai Max Induk: <span class="fw-bold text-primary" id="infoMaxScore">4</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Dynamic Sub-sub Items Container -->
                    <div id="generalSubSubRowsContainer" class="d-none">
                        <div class="d-flex align-items-center justify-content-between mb-3 bg-light p-3 rounded-3 border">
                            <div>
                                <h6 class="fw-bold text-slate-800 mb-0">
                                    <i class="bi bi-list-task text-primary me-2"></i>Daftar Sub-sub Elemen yang akan Ditambahkan
                                </h6>
                                <small class="text-muted">Klik tombol <strong>+ Tambah Baris</strong> untuk menambah lebih dari 1 sub-sub elemen.</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-primary fw-semibold rounded-pill px-3 shadow-sm btn-add-general-row">
                                <i class="bi bi-plus-lg me-1"></i> Tambah Baris Sub-sub
                            </button>
                        </div>

                        <!-- Rows Wrapper -->
                        <div id="generalSubSubItemsWrapper" class="d-flex flex-column gap-3 mb-3">
                            <!-- Injected dynamically by JS -->
                        </div>

                        <div class="text-center p-3 border border-dashed rounded-3 bg-light bg-opacity-50">
                            <button type="button" class="btn btn-sm btn-outline-primary fw-semibold rounded-pill px-4 btn-add-general-row">
                                <i class="bi bi-plus-circle me-1"></i> Tambah Baris Sub-sub Elemen (+ Baris)
                            </button>
                        </div>
                    </div>

                    <!-- Placeholder when no sub-elemen is selected -->
                    <div id="generalSelectPlaceholder" class="p-4 text-center text-muted bg-light rounded-3 border">
                        <i class="bi bi-diagram-3 fs-2 d-block text-secondary mb-2"></i>
                        Silakan pilih <strong>Induk Sub-Elemen</strong> di atas terlebih dahulu untuk memunculkan formulir input Sub-sub Elemen.
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 fw-semibold" id="btnSubmitGeneralBatch" disabled>
                        <i class="bi bi-check-lg me-1"></i> Simpan Semua Sub-sub Elemen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Per Sub-Elemen: Tambah Sub-sub Elemen di bawah Sub Tersebut (Multi-Baris Dinamis) -->
<?php foreach ($subElemens as $sub): ?>
    <?php
        $maxInt = (int) ($sub->nilai_maksimal ?? 4);
        $existingCount = $sub->kriterias->where('kode_kriteria', '!=', $sub->kode_sub)->count();
    ?>
    <div class="modal fade" id="createSubSubForSubModal<?php echo e($sub->id); ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content card-custom border-0">
                <form action="<?php echo route('admin.kriterias.store'); ?>" method="POST">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <input type="hidden" name="sub_elemen_id" value="<?php echo e($sub->id); ?>">
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-plus-lg text-success me-2"></i>Tambah Sub-sub Elemen di bawah <span class="badge bg-info text-dark font-monospace">Sub <?php echo e($sub->kode_sub); ?></span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-light border small mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div>
                                <strong>Induk Sub-Elemen:</strong> Sub <?php echo e($sub->kode_sub); ?> - <?php echo e($sub->nama_sub); ?>

                            </div>
                            <div>
                                <span class="badge bg-light text-dark border font-monospace">Nilai Max Induk: <?php echo (int) ($sub->nilai_maksimal ?? 4); ?></span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between mb-3 bg-light p-3 rounded-3 border">
                            <div>
                                <h6 class="fw-bold text-slate-800 mb-0">
                                    <i class="bi bi-list-task text-primary me-2"></i>Daftar Sub-sub Elemen yang akan Ditambahkan
                                </h6>
                                <small class="text-muted">Tambahkan satu atau lebih Sub-sub Elemen sekaligus di bawah ini.</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-success text-white fw-semibold rounded-pill px-3 shadow-sm btn-add-sub-modal-row" data-target="subSubItemsWrapper<?php echo e($sub->id); ?>" data-kode-sub="<?php echo e($sub->kode_sub); ?>" data-max-score="<?php echo (int)($sub->nilai_maksimal ?? 4); ?>" data-existing-count="<?php echo e($existingCount); ?>">
                                <i class="bi bi-plus-lg me-1"></i> Tambah Baris Sub-sub
                            </button>
                        </div>

                        <!-- Rows Wrapper -->
                        <div id="subSubItemsWrapper<?php echo e($sub->id); ?>" class="sub-sub-items-container d-flex flex-column gap-3 mb-3" data-kode-sub="<?php echo e($sub->kode_sub); ?>" data-max-score="<?php echo (int)($sub->nilai_maksimal ?? 4); ?>" data-existing-count="<?php echo e($existingCount); ?>">
                            <!-- Injected dynamically by JS on modal open -->
                        </div>

                        <div class="text-center p-3 border border-dashed rounded-3 bg-light bg-opacity-50">
                            <button type="button" class="btn btn-sm btn-outline-success fw-semibold rounded-pill px-4 btn-add-sub-modal-row" data-target="subSubItemsWrapper<?php echo e($sub->id); ?>" data-kode-sub="<?php echo e($sub->kode_sub); ?>" data-max-score="<?php echo (int)($sub->nilai_maksimal ?? 4); ?>" data-existing-count="<?php echo e($existingCount); ?>">
                                <i class="bi bi-plus-circle me-1"></i> Tambah Baris Sub-sub Elemen (+ Baris)
                            </button>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success text-white rounded-3 px-4 fw-semibold"><i class="bi bi-check-lg me-1"></i>Simpan Semua Sub-sub Elemen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<!-- Modal Edit Sub-sub Elemen / Kriteria -->
<?php foreach ($subElemens as $sub): ?>
    <?php foreach ($sub->kriterias as $kriteria): ?>
        <?php if ($kriteria->kode_kriteria !== $sub->kode_sub): ?>
            <div class="modal fade" id="editSubSubModal<?php echo e($kriteria->id); ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content card-custom border-0">
                        <form action="<?php echo route('admin.kriterias.update', $kriteria->id); ?>" method="POST">
                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                            <input type="hidden" name="_method" value="PUT">
                            <input type="hidden" name="from_edit_modal" value="1">
                            <input type="hidden" name="sub_elemen_id" value="<?php echo e($kriteria->sub_elemen_id); ?>">
                            <div class="modal-header border-bottom">
                                <h5 class="modal-title fw-bold">
                                    <i class="bi bi-pencil-square text-primary me-2"></i>Edit Sub-sub Elemen — <span class="badge bg-dark font-monospace"><?php echo e($kriteria->kode_kriteria); ?></span>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Summary Card Info -->
                                <div class="p-3 bg-light rounded-3 border mb-3">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="badge bg-secondary font-monospace">Induk: Sub <?php echo e($sub->kode_sub); ?></span>
                                        <span class="badge bg-success font-monospace" id="maxBadge_<?php echo e($kriteria->id); ?>">Nilai Max: <?php echo (int) $kriteria->nilai_maksimal; ?></span>
                                    </div>
                                    <div class="fw-bold text-slate-800 small"><?php echo e($sub->nama_sub); ?></div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold small text-dark">Kode Sub-sub <span class="text-danger">*</span></label>
                                        <input type="text" name="kode_kriteria" class="form-control font-monospace" value="<?php echo e($kriteria->kode_kriteria); ?>" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold small text-dark">Nilai Maksimal <span class="text-danger">*</span></label>
                                        <input type="number" step="1" min="0" max="1000" name="nilai_maksimal" 
                                            class="form-control font-monospace edit-kriteria-nilai-max fw-bold text-primary" 
                                            value="<?php echo (int) $kriteria->nilai_maksimal; ?>" 
                                            data-kriteria-id="<?php echo e($kriteria->id); ?>" 
                                            data-target-container="editRubrikContainer_<?php echo e($kriteria->id); ?>" 
                                            required>
                                        <div class="form-text text-muted" style="font-size: 0.75rem;">Mengubah nilai ini akan otomatis menyesuaikan rubrik & total nilai Sub-Elemen.</div>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <div class="form-check form-switch pb-2">
                                            <input class="form-check-input" type="checkbox" name="is_na" id="edit_is_na_kriteria_<?php echo e($kriteria->id); ?>" value="1" <?php echo $kriteria->is_na ? 'checked' : ''; ?>>
                                            <label class="form-check-label fw-semibold small text-dark" for="edit_is_na_kriteria_<?php echo e($kriteria->id); ?>">Set N/A Default</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold small text-dark">Deskripsi / Pertanyaan Sub-sub Elemen <span class="text-danger">*</span></label>
                                        <textarea name="deskripsi" class="form-control" rows="2" required><?php echo e($kriteria->deskripsi); ?></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold small text-secondary">
                                            <i class="bi bi-file-earmark-text text-primary me-1"></i>Dokumen Wajib / Acuan Persyaratan (Opsional)
                                        </label>
                                        <input type="text" name="persyaratan_dokumen" class="form-control" value="<?php echo e($kriteria->persyaratan_dokumen); ?>" placeholder="Contoh: SOP, SK KTT, Buku Catatan...">
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <h6 class="fw-bold text-slate-800 small mb-2"><i class="bi bi-bookmark-star-fill text-warning me-1"></i>Rubrik Pedoman Penilaian</h6>
                                </div>

                                <?php
                                    $maxValEdit = (int) ceil($kriteria->nilai_maksimal);
                                    $pedomanArr = $kriteria->pedoman_array ?? [];
                                ?>
                                <div class="row g-3" id="editRubrikContainer_<?php echo e($kriteria->id); ?>">
                                    <?php for ($i = 0; $i <= $maxValEdit; $i++): ?>
                                        <?php
                                            $pctEdit = $maxValEdit > 0 ? round(($i / $maxValEdit) * 100) : 0;
                                            $colSizeEdit = ($maxValEdit > 4) ? 'col-md-6' : 'col-12';
                                        ?>
                                        <div class="<?php echo $colSizeEdit; ?>">
                                            <label class="form-label fw-semibold small text-dark">Pedoman Nilai <?php echo $i; ?> (<?php echo $pctEdit; ?>% dari Max <?php echo (int) $kriteria->nilai_maksimal; ?>)</label>
                                            <textarea name="pedoman_nilai[<?php echo $i; ?>]" class="form-control" rows="2" placeholder="Acuan pemberian Nilai <?php echo $i; ?>..."><?php echo e($pedomanArr[(string)$i] ?? ($kriteria->{"pedoman_nilai_$i"} ?? '')); ?></textarea>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <div class="modal-footer border-top">
                                <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary rounded-3"><i class="bi bi-check-lg me-1"></i>Simpan Perubahan Sub-sub Elemen</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endforeach; ?>

<!-- Edit Sub-Elemen Modals -->
<?php foreach ($subElemens as $sub): ?>
    <?php
        $childKriteriasCount = $sub->kriterias->where('kode_kriteria', '!=', $sub->kode_sub)->count();
    ?>
    <div class="modal fade" id="editSubModal<?php echo e($sub->id); ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content card-custom border-0">
                <form action="<?php echo route('admin.sub-elemens.update', $sub->id); ?>" method="POST">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <input type="hidden" name="_method" value="PUT">
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-bold">Edit Sub-Elemen <?php echo e($sub->kode_sub); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Induk Elemen</label>
                            <select name="elemen_id" class="form-select" required>
                                <?php foreach ($elemens as $el): ?>
                                    <option value="<?php echo e($el->id); ?>" <?php echo $sub->elemen_id == $el->id ? 'selected' : ''; ?>>
                                        Elemen <?php echo e($el->kode_elemen); ?> - <?php echo e($el->nama_elemen); ?>

                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Kode Sub-Elemen (Contoh: I.1)</label>
                            <input type="text" name="kode_sub" class="form-control" value="<?php echo e($sub->kode_sub); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Nama Sub-Elemen</label>
                            <input type="text" name="nama_sub" class="form-control" value="<?php echo e($sub->nama_sub); ?>" required>
                        </div>

                        <?php if ($childKriteriasCount > 0): ?>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Nilai Maksimal <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="1" min="0" max="1000" name="nilai_maksimal" class="form-control font-monospace bg-light fw-bold text-primary" value="<?php echo (int) ($sub->nilai_maksimal ?? 0); ?>" readonly>
                                    <span class="input-group-text bg-light text-muted small"><i class="bi bi-lock-fill me-1"></i>Akumulasi Sub-sub</span>
                                </div>
                                <div class="form-text text-primary small mt-1">
                                    <i class="bi bi-info-circle-fill me-1"></i>Sub-Elemen ini memiliki <strong><?php echo $childKriteriasCount; ?> Sub-sub Elemen</strong>. Nilai maksimal dihitung otomatis dari akumulasi seluruh Sub-sub Elemen di bawahnya. Untuk mengubah nilainya, silakan edit Nilai Maksimal pada masing-masing Sub-sub Elemen.
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Nilai Maksimal <span class="text-danger">*</span></label>
                                <input type="number" step="1" min="0" max="1000" name="nilai_maksimal" class="form-control font-monospace fw-bold text-primary" value="<?php echo (int) ($sub->nilai_maksimal ?? 4); ?>" required>
                                <div class="form-text text-muted">Sub-Elemen ini dinilai langsung (Penilaian Standalone). Nilai maksimal dapat diubah langsung di sini.</div>
                            </div>
                        <?php endif; ?>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_na" id="edit_is_na_sub_<?php echo e($sub->id); ?>" value="1" <?php echo $sub->is_na ? 'checked' : ''; ?>>
                            <label class="form-check-label fw-semibold small text-dark" for="edit_is_na_sub_<?php echo e($sub->id); ?>">Set N/A (Not Applicable) secara default</label>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-3">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<!-- Create Sub-Elemen Modal -->
<div class="modal fade" id="createSubModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content card-custom border-0">
            <form action="<?php echo route('admin.sub-elemens.store'); ?>" method="POST">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold">Tambah Sub-Elemen Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Induk Elemen</label>
                        <select name="elemen_id" class="form-select select-searchable" required>
                            <option value="">-- Pilih Induk Elemen --</option>
                            <?php foreach ($elemens as $el): ?>
                                <option value="<?php echo e($el->id); ?>">Elemen <?php echo e($el->kode_elemen); ?> - <?php echo e($el->nama_elemen); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Kode Sub-Elemen (Contoh: I.1, I.2)</label>
                        <input type="text" name="kode_sub" class="form-control" placeholder="Contoh: I.1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nama Sub-Elemen</label>
                        <input type="text" name="nama_sub" class="form-control" placeholder="Contoh: Kebijakan Keselamatan Pertambangan" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nilai Maksimal <span class="text-danger">*</span></label>
                        <input type="number" step="1" min="0" max="1000" name="nilai_maksimal" class="form-control" value="4" required>
                        <div class="form-text text-muted">Akumulasi total nilai maksimal sub-elemen tidak boleh melebihi Total Nilai Sub-Elemen pada Elemen induk.</div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3">Simpan Sub-Elemen</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Per-Elemen: Tambah Sub-Elemen -->
<?php foreach ($elemens as $elemen): ?>
    <div class="modal fade" id="createSubForElemenModal<?php echo e($elemen->id); ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content card-custom border-0">
                <form action="<?php echo route('admin.sub-elemens.store'); ?>" method="POST">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <input type="hidden" name="elemen_id" value="<?php echo e($elemen->id); ?>">
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-bold">Tambah Sub-Elemen pada Elemen <?php echo e($elemen->kode_elemen); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-light border small mb-3">
                            <strong>Induk Elemen:</strong> Elemen <?php echo e($elemen->kode_elemen); ?> - <?php echo e($elemen->nama_elemen); ?>

                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Kode Sub-Elemen (Contoh: <?php echo e($elemen->kode_elemen); ?>.1)</label>
                            <input type="text" name="kode_sub" class="form-control" placeholder="Contoh: <?php echo e($elemen->kode_elemen); ?>.1" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Nama Sub-Elemen</label>
                            <input type="text" name="nama_sub" class="form-control" placeholder="Contoh: Nama Sub-Elemen Baru" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Nilai Maksimal <span class="text-danger">*</span></label>
                            <input type="number" step="1" min="0" max="1000" name="nilai_maksimal" class="form-control" value="4" required>
                            <div class="form-text text-muted">Akumulasi total nilai maksimal sub-elemen tidak boleh melebihi Total Nilai Sub-Elemen pada Elemen induk.</div>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-info text-dark rounded-3 fw-semibold">Simpan Sub-Elemen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?php
$content = ob_get_clean();

ob_start();
?>
<script nonce="<?php echo e($cspNonce ?? ''); ?>">
document.addEventListener('DOMContentLoaded', function() {
    let currentGeneralData = null;

    // Helper to generate Rubrik Pedoman Nilai inputs (0..N)
    function generateRubrikInputs(index, maxScore) {
        const maxScoreInt = Math.max(1, parseInt(maxScore, 10) || 4);
        let html = '';

        for (let i = 0; i <= maxScoreInt; i++) {
            const pct = Math.round((i / maxScoreInt) * 100);
            let colorClass = 'text-danger';
            if (pct >= 100) colorClass = 'text-success';
            else if (pct >= 75) colorClass = 'text-primary';
            else if (pct >= 50) colorClass = 'text-info';
            else if (pct >= 25) colorClass = 'text-warning';

            html += `
                <div class="col-md-6">
                    <label class="form-label small fw-semibold ${colorClass} mb-1">
                        Pedoman Nilai ${i} (${pct}% dari Max ${maxScoreInt})
                    </label>
                    <textarea name="sub_subs[${index}][pedoman_nilai][${i}]" class="form-control form-control-sm" rows="1" placeholder="Acuan penilaian untuk skor ${i}..."></textarea>
                </div>
            `;
        }
        return html;
    }

    // Helper to generate a single Sub-sub Elemen Card
    function createSubSubCardHtml(index, kodeSub, maxScore, existingCount) {
        const suggestedKode = (kodeSub && kodeSub !== '-') 
            ? `${kodeSub}.${parseInt(existingCount || 0) + index + 1}` 
            : '';
        const uniqueId = 'rubrik_' + Math.random().toString(36).substring(2, 9) + '_' + index;
        const formattedMax = parseInt(maxScore, 10) || 4;

        return `
            <div class="card card-custom border p-3 shadow-none bg-white sub-sub-item-card position-relative" data-index="${index}">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary rounded-pill px-3 py-1 fs-6 item-index-badge">#${index + 1}</span>
                        <span class="fw-bold text-slate-800">Sub-sub Elemen</span>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger border-0 btn-remove-sub-row rounded-2 px-2 py-1" title="Hapus Baris Ini">
                        <i class="bi bi-trash3-fill me-1"></i> <span class="small">Hapus Baris</span>
                    </button>
                </div>

                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-slate-700 mb-1">Kode Sub-sub <span class="text-danger">*</span></label>
                        <input type="text" name="sub_subs[${index}][kode_kriteria]" class="form-control font-monospace input-kode-kriteria" value="${suggestedKode}" placeholder="Contoh: ${kodeSub || 'I.1'}.1" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-slate-700 mb-1">Deskripsi / Pertanyaan Sub-sub Elemen <span class="text-danger">*</span></label>
                        <input type="text" name="sub_subs[${index}][deskripsi]" class="form-control input-deskripsi" placeholder="Tulis deskripsi / pertanyaan audit..." required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-slate-700 mb-1">Nilai Maksimal <span class="text-danger">*</span></label>
                        <input type="number" step="1" min="0" max="1000" name="sub_subs[${index}][nilai_maksimal]" class="form-control font-monospace input-nilai-max" value="${formattedMax}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold text-secondary mb-1">
                            <i class="bi bi-file-earmark-text text-primary me-1"></i>Dokumen Wajib / Acuan Persyaratan (Opsional)
                        </label>
                        <input type="text" name="sub_subs[${index}][persyaratan_dokumen]" class="form-control form-control-sm" placeholder="Contoh: SK KTT, SOP Inspeksi Terkait, Matriks Kompetensi...">
                    </div>

                    <!-- Accordion Pedoman Nilai -->
                    <div class="col-12">
                        <div class="accordion border rounded-3 overflow-hidden" id="acc_${uniqueId}">
                            <div class="accordion-item border-0">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed py-2 px-3 small bg-light text-slate-700" type="button" data-bs-toggle="collapse" data-bs-target="#col_${uniqueId}">
                                        <i class="bi bi-bookmark-star text-warning me-2 fs-6"></i>
                                        <strong>Rubrik Pedoman Penilaian (Opsional - Klik untuk mengisi pedoman nilai 0 s/d ${formattedMax})</strong>
                                    </button>
                                </h2>
                                <div id="col_${uniqueId}" class="accordion-collapse collapse" data-bs-parent="#acc_${uniqueId}">
                                    <div class="accordion-body p-3 bg-white">
                                        <div class="row g-2 rubrik-inputs-box">
                                            ${generateRubrikInputs(index, formattedMax)}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Helper to re-index cards inside a container
    function reindexContainer(container) {
        if (!container) return;
        const cards = container.querySelectorAll('.sub-sub-item-card');
        cards.forEach((card, newIdx) => {
            card.dataset.index = newIdx;
            const badge = card.querySelector('.item-index-badge');
            if (badge) badge.textContent = `#${newIdx + 1}`;

            // Update inputs name prefixes
            card.querySelectorAll('[name^="sub_subs["]').forEach(input => {
                const name = input.getAttribute('name');
                const updatedName = name.replace(/^sub_subs\[\d+\]/, `sub_subs[${newIdx}]`);
                input.setAttribute('name', updatedName);
            });
        });
    }

    // Helper to append a new card to a wrapper
    function appendCardToWrapper(wrapper, kodeSub, maxScore, existingCount) {
        if (!wrapper) return;
        const currentCount = wrapper.querySelectorAll('.sub-sub-item-card').length;
        const cardHtml = createSubSubCardHtml(currentCount, kodeSub, maxScore, existingCount);
        wrapper.insertAdjacentHTML('beforeend', cardHtml);
    }

    // 1. Logic for General Modal (#createSubSubModal)
    const generalSelect = document.getElementById('generalSubElemenSelect');
    const generalInfo = document.getElementById('generalSubElemenInfo');
    const generalRowsContainer = document.getElementById('generalSubSubRowsContainer');
    const generalItemsWrapper = document.getElementById('generalSubSubItemsWrapper');
    const generalPlaceholder = document.getElementById('generalSelectPlaceholder');
    const generalSubmitBtn = document.getElementById('btnSubmitGeneralBatch');

    function handleGeneralSelectChange() {
        if (!generalSelect) return;
        const selectedVal = generalSelect.value;

        if (!selectedVal) {
            currentGeneralData = null;
            if (generalInfo) generalInfo.classList.add('d-none');
            if (generalRowsContainer) generalRowsContainer.classList.add('d-none');
            if (generalPlaceholder) generalPlaceholder.classList.remove('d-none');
            if (generalSubmitBtn) generalSubmitBtn.disabled = true;
            if (generalItemsWrapper) generalItemsWrapper.innerHTML = '';
            return;
        }

        const selectedOpt = generalSelect.querySelector(`option[value="${selectedVal}"]`) || generalSelect.options[generalSelect.selectedIndex];
        if (!selectedOpt) return;

        const kode = selectedOpt.dataset.kode || '';
        const nama = selectedOpt.dataset.nama || '';
        const maxScore = parseInt(selectedOpt.dataset.max, 10) || 4;
        const count = parseInt(selectedOpt.dataset.count, 10) || 0;

        currentGeneralData = { kode, nama, maxScore, count };

        // Update Info Banner
        const infoKode = document.getElementById('infoKodeSub');
        const infoNama = document.getElementById('infoNamaSub');
        const infoMax = document.getElementById('infoMaxScore');

        if (infoKode) infoKode.textContent = `Sub ${kode}`;
        if (infoNama) infoNama.textContent = nama;
        if (infoMax) infoMax.textContent = maxScore;

        if (generalInfo) generalInfo.classList.remove('d-none');
        if (generalPlaceholder) generalPlaceholder.classList.add('d-none');
        if (generalRowsContainer) generalRowsContainer.classList.remove('d-none');
        if (generalSubmitBtn) generalSubmitBtn.disabled = false;

        // Reset and add 1 initial row
        if (generalItemsWrapper) {
            generalItemsWrapper.innerHTML = '';
            appendCardToWrapper(generalItemsWrapper, kode, maxScore, count);
        }
    }

    if (generalSelect) {
        generalSelect.addEventListener('change', handleGeneralSelectChange);

        // If TomSelect is initialized on generalSelect
        if (generalSelect.tomselect) {
            generalSelect.tomselect.on('change', handleGeneralSelectChange);
        }
    }

    // Click on "+ Tambah Baris" in General Modal
    document.querySelectorAll('.btn-add-general-row').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!currentGeneralData || !generalItemsWrapper) return;
            appendCardToWrapper(generalItemsWrapper, currentGeneralData.kode, currentGeneralData.maxScore, currentGeneralData.count);
        });
    });

    // 2. Logic for Per-Sub Modals
    document.querySelectorAll('.btn-add-sub-modal-row').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const targetWrapper = document.getElementById(targetId);
            if (!targetWrapper) return;

            const kodeSub = this.dataset.kodeSub || '';
            const maxScore = parseInt(this.dataset.maxScore, 10) || 4;
            const existingCount = parseInt(this.dataset.existingCount, 10) || 0;

            appendCardToWrapper(targetWrapper, kodeSub, maxScore, existingCount);
        });
    });

    // Auto initialize per-sub modal when opened if empty
    document.querySelectorAll('[id^="createSubSubForSubModal"]').forEach(modalEl => {
        modalEl.addEventListener('shown.bs.modal', function() {
            const wrapper = this.querySelector('.sub-sub-items-container');
            if (wrapper && wrapper.querySelectorAll('.sub-sub-item-card').length === 0) {
                const kodeSub = wrapper.dataset.kodeSub || '';
                const maxScore = parseInt(wrapper.dataset.maxScore, 10) || 4;
                const existingCount = parseInt(wrapper.dataset.existingCount, 10) || 0;
                appendCardToWrapper(wrapper, kodeSub, maxScore, existingCount);
            }
        });
    });

    // 3. Global Event Delegation for "Hapus Baris"
    document.addEventListener('click', function(e) {
        const removeBtn = e.target.closest('.btn-remove-sub-row');
        if (!removeBtn) return;

        const card = removeBtn.closest('.sub-sub-item-card');
        if (!card) return;

        const container = card.closest('.d-flex.flex-column');
        if (!container) return;

        const allCards = container.querySelectorAll('.sub-sub-item-card');
        if (allCards.length <= 1) {
            alert('Minimal harus ada 1 baris Sub-sub Elemen.');
            return;
        }

        card.remove();
        reindexContainer(container);
    });

    // 4. Dynamic Rubric Re-renderer on Nilai Maksimal change in Edit Sub-sub Modal
    document.querySelectorAll('.edit-kriteria-nilai-max').forEach(input => {
        input.addEventListener('input', function() {
            const targetContainerId = this.dataset.targetContainer;
            const container = document.getElementById(targetContainerId);
            if (!container) return;

            const maxScoreInt = Math.max(0, parseInt(this.value, 10) || 0);

            // Read current values
            const currentValues = {};
            container.querySelectorAll('textarea[name^="pedoman_nilai["]').forEach(textarea => {
                const match = textarea.getAttribute('name').match(/pedoman_nilai\[(\d+)\]/);
                if (match) {
                    currentValues[match[1]] = textarea.value;
                }
            });

            let html = '';
            for (let i = 0; i <= maxScoreInt; i++) {
                const pct = maxScoreInt > 0 ? Math.round((i / maxScoreInt) * 100) : 0;
                let colorClass = 'text-danger';
                if (pct >= 100) colorClass = 'text-success';
                else if (pct >= 75) colorClass = 'text-primary';
                else if (pct >= 50) colorClass = 'text-info';
                else if (pct >= 25) colorClass = 'text-warning';

                const colSize = (maxScoreInt > 4) ? 'col-md-6' : 'col-12';
                const val = currentValues[i] || '';

                html += `
                    <div class="${colSize}">
                        <label class="form-label fw-semibold small text-dark ${colorClass}">
                            Pedoman Nilai ${i} (${pct}% dari Max ${maxScoreInt})
                        </label>
                        <textarea name="pedoman_nilai[${i}]" class="form-control" rows="2" placeholder="Acuan pemberian Nilai ${i}...">${val}</textarea>
                    </div>
                `;
            }

            container.innerHTML = html;
            const kriteriaId = this.dataset.kriteriaId;
            const maxBadge = document.getElementById('maxBadge_' + kriteriaId);
            if (maxBadge) {
                maxBadge.textContent = 'Nilai Max: ' + maxScoreInt;
            }
        });
    });
});
</script>
<?php
$scripts = ob_get_clean();

echo view('layouts.app', get_defined_vars())->render();
