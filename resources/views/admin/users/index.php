<?php
$title = 'Master Pengguna — SMKP Minerba';

ob_start();
?>
<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold text-slate-800 mb-1">
            <i class="bi bi-people-fill text-warning me-2"></i>Kelola User & Hak Akses
        </h2>
        <p class="text-muted mb-0">Kelola akun Administrator, Auditor SMKP, Auditor Perusahaan, serta status aktif/nonaktif akun.</p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <button class="btn btn-warning text-dark rounded-3 px-3 py-2 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="bi bi-plus-lg me-1"></i> Tambah User Baru
        </button>
    </div>
</div>

<div class="card card-custom p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nama Lengkap</th>
                    <th>Username</th>
                    <th>Area Kerja</th>
                    <th>Email</th>
                    <th>Role / Akses</th>
                    <th class="text-center">Status Akun</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($users->isNotEmpty()): ?>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td class="fw-bold text-slate-800">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-light rounded-circle p-2 d-flex align-items-center justify-content-center border" style="width: 38px; height: 38px;">
                                        <i class="bi bi-person-fill text-secondary"></i>
                                    </div>
                                    <?php echo e($u->name); ?>
                                </div>
                            </td>
                            <td><code><?php echo e($u->username); ?></code></td>
                            <td><span class="badge bg-light text-dark border"><?php echo e($u->area ?? '-'); ?></span></td>
                            <td class="text-muted"><?php echo e($u->email ?? '-'); ?></td>
                            <td>
                                <?php if ($u->role === 'admin'): ?>
                                    <span class="badge bg-danger badge-role"><i class="bi bi-shield-lock-fill me-1"></i>Administrator</span>
                                <?php elseif ($u->role === 'auditor_smkp'): ?>
                                    <span class="badge bg-primary badge-role"><i class="bi bi-patch-check-fill me-1"></i>Auditor SMKP</span>
                                <?php else: ?>
                                    <span class="badge bg-info text-dark badge-role"><i class="bi bi-clipboard-check-fill me-1"></i>Auditor Perusahaan</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($u->is_active): ?>
                                    <span class="badge bg-success rounded-pill px-3 py-2"><i class="bi bi-check-circle-fill me-1"></i>Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-danger rounded-pill px-3 py-2"><i class="bi bi-x-circle-fill me-1"></i>Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="btn-group gap-1">
                                    <?php if ($u->id !== auth()->id()): ?>
                                        <?php if ($u->is_active): ?>
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-2" data-bs-toggle="modal" data-bs-target="#toggleUserModal<?php echo e($u->id); ?>">
                                                <i class="bi bi-power me-1"></i> Nonaktifkan
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-sm btn-outline-success rounded-2" data-bs-toggle="modal" data-bs-target="#toggleUserModal<?php echo e($u->id); ?>">
                                                <i class="bi bi-power me-1"></i> Aktifkan
                                            </button>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-2" data-bs-toggle="modal" data-bs-target="#editUserModal<?php echo e($u->id); ?>">
                                        <i class="bi bi-pencil-square me-1"></i> Edit
                                    </button>

                                    <?php if ($u->id !== auth()->id()): ?>
                                        <form action="<?php echo route('admin.users.destroy', $u->id); ?>" method="POST" class="d-inline" data-confirm="Hapus user ini?">
                                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="btn btn-sm btn-outline-secondary rounded-2" title="Hapus User">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Belum ada data user.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modals Placed Outside Table to Prevent HTML DOM Ejection & Backdrop Glitch -->
<?php foreach ($users as $u): ?>
    <!-- Modal Konfirmasi Aktif / Nonaktif -->
    <?php if ($u->id !== auth()->id()): ?>
        <div class="modal fade" id="toggleUserModal<?php echo e($u->id); ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content card-custom border-0">
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-bold">
                            <?php if ($u->is_active): ?>
                                <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Konfirmasi Penonaktifan Akun
                            <?php else: ?>
                                <i class="bi bi-check-circle-fill text-success me-2"></i>Konfirmasi Pengaktifan Akun
                            <?php endif; ?>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body py-4">
                        <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3 border mb-3">
                            <div class="bg-white rounded-circle p-2 border">
                                <i class="bi bi-person-badge fs-3 text-secondary"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-slate-800 mb-0"><?php echo e($u->name); ?></h6>
                                <small class="text-muted">Username: <code><?php echo e($u->username); ?></code> | Role: <strong><?php echo e(strtoupper($u->role)); ?></strong></small>
                            </div>
                        </div>

                        <?php if ($u->is_active): ?>
                            <p class="text-secondary mb-0">
                                Apakah Anda yakin ingin <strong>menonaktifkan</strong> akun ini? 
                                Pengguna ini <strong class="text-danger">tidak akan dapat melakukan login</strong> ke dalam sistem setelah akun dinonaktifkan.
                            </p>
                        <?php else: ?>
                            <p class="text-secondary mb-0">
                                Apakah Anda yakin ingin <strong>mengaktifkan kembali</strong> akun ini? 
                                Pengguna ini akan <strong class="text-success">diberikan hak akses login kembali</strong> ke dalam sistem.
                            </p>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                        <form action="<?php echo route('admin.users.toggle-status', $u->id); ?>" method="POST" class="d-inline">
                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                            <input type="hidden" name="_method" value="PATCH">
                            <?php if ($u->is_active): ?>
                                <button type="submit" class="btn btn-danger rounded-3 px-4 fw-semibold">
                                    <i class="bi bi-power me-1"></i> Ya, Nonaktifkan Akun
                                </button>
                            <?php else: ?>
                                <button type="submit" class="btn btn-success rounded-3 px-4 fw-semibold">
                                    <i class="bi bi-power me-1"></i> Ya, Aktifkan Akun
                                </button>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Edit Modal -->
    <div class="modal fade" id="editUserModal<?php echo e($u->id); ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content card-custom border-0">
                <form action="<?php echo route('admin.users.update', $u->id); ?>" method="POST">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <input type="hidden" name="_method" value="PUT">
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-bold">Edit User <?php echo e($u->name); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" value="<?php echo e($u->name); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Username</label>
                            <input type="text" name="username" class="form-control" value="<?php echo e($u->username); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Email (Opsional)</label>
                            <input type="email" name="email" class="form-control" value="<?php echo e($u->email); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Role / Hak Akses</label>
                            <select name="role" class="form-select" required>
                                <option value="admin" <?php echo $u->role === 'admin' ? 'selected' : ''; ?>>Administrator (Akses Penuh Master Data & Penilaian)</option>
                                <option value="auditor_smkp" <?php echo $u->role === 'auditor_smkp' ? 'selected' : ''; ?>>Auditor SMKP (Dashboard, Penilaian & Monitoring)</option>
                                <option value="auditor" <?php echo $u->role === 'auditor' ? 'selected' : ''; ?>>Auditor Perusahaan (Auditee / PIC Area)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Area Kerja Perusahaan (Khusus Auditor Perusahaan)</label>
                            <select name="area" class="form-select select-searchable" data-allow-create="true" placeholder="-- Ketik untuk mencari perusahaan --">
                                <option value="">-- Pilih Perusahaan Area Kerja --</option>
                                <?php $areaFound = false; ?>
                                <?php foreach ($perusahaans as $p): ?>
                                    <?php
                                         $isSelected = ($u->area === $p->nama_perusahaan);
                                        if ($isSelected) $areaFound = true;
                                    ?>
                                    <option value="<?php echo e($p->nama_perusahaan); ?>" <?php echo $isSelected ? 'selected' : ''; ?>>
                                        <?php echo e($p->nama_perusahaan); ?> <?php if ($p->kode_perusahaan): ?>(<?php echo e($p->kode_perusahaan); ?>)<?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                                <?php if (!empty($u->area) && !$areaFound): ?>
                                    <optgroup label="⚠️ AREA KERJA TERDAFTAR SAAT INI">
                                        <option value="<?php echo e($u->area); ?>" selected><?php echo e($u->area); ?></option>
                                    </optgroup>
                                <?php endif; ?>
                            </select>
                            <small class="text-muted" style="font-size: 0.75rem;">Wajib untuk Auditor Perusahaan. Mengisolasi akses temuan PICA & sesi audit sesuai area perusahaan.</small>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="edit_active_<?php echo e($u->id); ?>" <?php echo $u->is_active ? 'checked' : ''; ?>>
                                <label class="form-check-label fw-semibold small" for="edit_active_<?php echo e($u->id); ?>">
                                    Status Akun Aktif
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Password Baru (Biarkan kosong jika tidak diubah)</label>
                            <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter">
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

<!-- Create Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content card-custom border-0">
            <form action="<?php echo route('admin.users.store'); ?>" method="POST">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold">Tambah User Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Budi Santoso" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Username</label>
                        <input type="text" name="username" class="form-control" placeholder="Contoh: budi_auditor" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Email (Opsional)</label>
                        <input type="email" name="email" class="form-control" placeholder="budi@smkp.id">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Role / Hak Akses</label>
                        <select name="role" class="form-select" required>
                            <option value="auditor_smkp">Auditor SMKP (Dashboard, Penilaian & Monitoring)</option>
                            <option value="admin">Administrator (Akses Penuh Master Data & Penilaian)</option>
                            <option value="auditor">Auditor Perusahaan (Auditee / PIC Area)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Area Kerja Perusahaan (Khusus Auditor Perusahaan)</label>
                        <select name="area" class="form-select select-searchable" data-allow-create="true" placeholder="-- Ketik untuk mencari perusahaan --">
                            <option value="">-- Pilih Perusahaan Area Kerja --</option>
                            <?php foreach ($perusahaans as $p): ?>
                                <option value="<?php echo e($p->nama_perusahaan); ?>" <?php echo old('area') == $p->nama_perusahaan ? 'selected' : ''; ?>>
                                    <?php echo e($p->nama_perusahaan); ?> <?php if ($p->kode_perusahaan): ?>(<?php echo e($p->kode_perusahaan); ?>)<?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted" style="font-size: 0.75rem;">Wajib untuk Auditor Perusahaan. Mengisolasi akses temuan PICA & sesi audit sesuai area perusahaan.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3">Simpan User</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();

echo view('layouts.app', get_defined_vars())->render();
