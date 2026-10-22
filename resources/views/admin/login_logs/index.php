<?php
$title = 'Log Riwayat Login & Sesi — SMKP Minerba';
ob_start();
?>
<div class="row align-items-center mb-4">
    <div class="col-md-7">
        <h2 class="fw-bold text-slate-800 mb-1">
            <i class="bi bi-shield-lock-fill text-primary me-2"></i>Log Riwayat Login & Sesi
        </h2>
        <p class="text-muted mb-0">Audit log riwayat autentikasi pengguna, deteksi percobaan login gagal, dan pemantauan auto-logout inaktivitas.</p>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0 d-flex gap-2 justify-content-md-end">
        <a href="<?php echo route('admin.audit-logs.index'); ?>" class="btn btn-outline-secondary rounded-3 px-3 py-2 fw-semibold shadow-xs">
            <i class="bi bi-clock-history me-1"></i> Log Aktivitas CRUD
        </a>
        <button type="button" class="btn btn-outline-danger rounded-3 px-3 py-2 fw-semibold shadow-xs" data-bs-toggle="modal" data-bs-target="#cleanupModal">
            <i class="bi bi-trash3 me-1"></i> Bersihkan Log
        </button>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl">
        <div class="card card-custom p-3 h-100 border-start border-4 border-primary">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase d-block mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Login Hari Ini</span>
                    <h3 class="fw-bold text-slate-800 mb-0"><?php echo number_format($totalToday); ?></h3>
                </div>
                <div class="stat-icon-box bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-box-arrow-in-right"></i>
                </div>
            </div>
            <div class="mt-2 text-muted" style="font-size: 0.75rem;">
                <span class="text-primary fw-bold"><?php echo $activeSessions; ?></span> sesi aktif saat ini
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl">
        <div class="card card-custom p-3 h-100 border-start border-4 border-success">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase d-block mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Login Berhasil</span>
                    <h3 class="fw-bold text-success mb-0"><?php echo number_format($successToday); ?></h3>
                </div>
                <div class="stat-icon-box bg-success bg-opacity-10 text-success">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
            </div>
            <div class="mt-2 text-muted" style="font-size: 0.75rem;">
                Tercatat pada hari ini
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl">
        <div class="card card-custom p-3 h-100 border-start border-4 border-danger">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase d-block mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Login Gagal</span>
                    <h3 class="fw-bold text-danger mb-0"><?php echo number_format($failedToday); ?></h3>
                </div>
                <div class="stat-icon-box bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-shield-x"></i>
                </div>
            </div>
            <div class="mt-2 text-muted" style="font-size: 0.75rem;">
                <?php if ($failedToday > 0): ?>
                    <span class="text-danger fw-semibold"><i class="bi bi-exclamation-triangle-fill me-1"></i>Periksa indikasi anomali</span>
                <?php else: ?>
                    <span class="text-success"><i class="bi bi-shield-check me-1"></i>Tidak ada anomali</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl">
        <div class="card card-custom p-3 h-100 border-start border-4 border-warning">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase d-block mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Auto-Logout (Idle)</span>
                    <h3 class="fw-bold text-warning mb-0"><?php echo number_format($timeoutTotal); ?></h3>
                </div>
                <div class="stat-icon-box bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-hourglass-split"></i>
                </div>
            </div>
            <div class="mt-2 text-muted" style="font-size: 0.75rem;">
                Sesi ditutup karena inaktivitas
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl">
        <div class="card card-custom p-3 h-100 border-start border-4 border-info">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase d-block mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Batas Inaktivitas</span>
                    <h3 class="fw-bold text-info mb-0"><?php echo $idleTimeoutMinutes; ?> Menit</h3>
                </div>
                <div class="stat-icon-box bg-info bg-opacity-10 text-info">
                    <i class="bi bi-clock-history"></i>
                </div>
            </div>
            <div class="mt-2 text-muted" style="font-size: 0.75rem;">
                Auto logout jika tidak ada akses
            </div>
        </div>
    </div>
</div>

<!-- Filter Box -->
<div class="card card-custom p-4 mb-4">
    <form method="GET" action="<?php echo route('admin.login-logs.index'); ?>" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label small fw-bold text-slate-700">Pencarian Kata Kunci</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Username, Nama, IP..." value="<?php echo e(request('search')); ?>">
            </div>
        </div>

        <div class="col-md-2">
            <label class="form-label small fw-bold text-slate-700">Status Autentikasi</label>
            <select name="status" class="form-select">
                <option value="all" <?php echo request('status', 'all') === 'all' ? 'selected' : ''; ?>>— Semua Status —</option>
                <option value="success" <?php echo request('status') === 'success' ? 'selected' : ''; ?>>Login Berhasil</option>
                <option value="failed" <?php echo request('status') === 'failed' ? 'selected' : ''; ?>>Login Gagal</option>
                <option value="timeout" <?php echo request('status') === 'timeout' ? 'selected' : ''; ?>>Auto-Logout (Idle)</option>
                <option value="logout" <?php echo request('status') === 'logout' ? 'selected' : ''; ?>>Logout Manual</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label small fw-bold text-slate-700">Pengguna (User)</label>
            <select name="user_id" class="form-select select-searchable" data-searchable="true">
                <option value="all">— Semua Pengguna —</option>
                <?php foreach($users as $u): ?>
                    <option value="<?php echo $u->id; ?>" <?php echo request('user_id') == $u->id ? 'selected' : ''; ?>>
                        <?php echo e($u->name); ?> (<?php echo e($u->username); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label small fw-bold text-slate-700">Periode Waktu</label>
            <select name="period" class="form-select" id="periodSelect" onchange="toggleCustomDate(this.value)">
                <option value="all" <?php echo request('period', 'all') === 'all' ? 'selected' : ''; ?>>Semua Waktu</option>
                <option value="today" <?php echo request('period') === 'today' ? 'selected' : ''; ?>>Hari Ini</option>
                <option value="yesterday" <?php echo request('period') === 'yesterday' ? 'selected' : ''; ?>>Kemarin</option>
                <option value="this_week" <?php echo request('period') === 'this_week' ? 'selected' : ''; ?>>Minggu Ini</option>
                <option value="this_month" <?php echo request('period') === 'this_month' ? 'selected' : ''; ?>>Bulan Ini</option>
                <option value="custom" <?php echo request('period') === 'custom' ? 'selected' : ''; ?>>Rentang Tanggal</option>
            </select>
        </div>

        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary rounded-3 w-100 fw-semibold shadow-xs" style="background: linear-gradient(135deg, #0284c7, #0369a1); border: none;">
                <i class="bi bi-funnel me-1"></i> Filter
            </button>
            <a href="<?php echo route('admin.login-logs.index'); ?>" class="btn btn-light border rounded-3 px-3 shadow-xs" title="Reset Filter">
                <i class="bi bi-arrow-counterclockwise"></i>
            </a>
        </div>

        <!-- Custom Date Range Row -->
        <div class="col-12 <?php echo request('period') === 'custom' ? '' : 'd-none'; ?>" id="customDateRow">
            <div class="row g-2 pt-2 border-top">
                <div class="col-md-3">
                    <label class="form-label small text-muted">Tanggal Mulai</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" value="<?php echo e(request('start_date')); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">Tanggal Akhir</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" value="<?php echo e(request('end_date')); ?>">
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Data Table -->
<div class="card card-custom p-4">
    <?php if ($logs->isEmpty()): ?>
        <div class="text-center py-5">
            <div class="bg-light rounded-circle p-4 d-inline-flex mb-3 text-muted">
                <i class="bi bi-shield-lock display-4"></i>
            </div>
            <h5 class="fw-bold text-slate-800">Tidak ada riwayat log login</h5>
            <p class="text-muted small">Belum ada aktivitas login yang sesuai dengan kriteria filter yang Anda pilih.</p>
            <a href="<?php echo route('admin.login-logs.index'); ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
            </a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 175px;">Waktu & Tanggal</th>
                        <th style="width: 220px;">Akun / Pengguna</th>
                        <th>Perangkat & IP</th>
                        <th style="width: 160px;">Status</th>
                        <th>Durasi / Keterangan</th>
                        <th class="text-end" style="width: 90px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($logs as $log): ?>
                        <tr>
                            <td>
                                <div class="small fw-bold text-slate-800">
                                    <?php echo e($log->login_at ? $log->login_at->format('d M Y') : $log->created_at->format('d M Y')); ?>
                                </div>
                                <small class="text-muted font-monospace" style="font-size: 0.75rem;">
                                    <i class="bi bi-clock me-1"></i><?php echo e($log->login_at ? $log->login_at->format('H:i:s') : $log->created_at->format('H:i:s')); ?>
                                </small>
                            </td>

                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle border p-1 d-flex align-items-center justify-content-center <?php echo $log->status === 'failed' ? 'bg-danger bg-opacity-10 text-danger border-danger' : 'bg-light text-secondary'; ?>" style="width: 36px; height: 36px;">
                                        <i class="bi <?php echo $log->status === 'failed' ? 'bi-shield-x' : 'bi-person-fill'; ?>"></i>
                                    </div>
                                    <div class="overflow-hidden">
                                        <div class="fw-bold text-slate-800 small text-truncate" style="max-width: 180px;">
                                            <?php echo e($log->user ? $log->user->name : $log->username); ?>
                                        </div>
                                        <div class="d-flex align-items-center gap-1">
                                            <code class="text-muted small" style="font-size: 0.72rem;"><?php echo e($log->username); ?></code>
                                            <?php if ($log->user): ?>
                                                <span class="badge bg-light text-dark border px-1.5 py-0.5 rounded" style="font-size: 0.65rem;">
                                                    <?php echo e($log->user->role_label); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-1.5 py-0.5 rounded" style="font-size: 0.65rem;">
                                                    Tidak Terdaftar
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div class="small fw-semibold text-slate-800">
                                    <i class="bi bi-laptop me-1 text-primary"></i><?php echo e($log->device ?? 'Perangkat Web'); ?>
                                </div>
                                <small class="text-muted font-monospace" style="font-size: 0.75rem;">
                                    <i class="bi bi-geo-alt me-1 text-secondary"></i><?php echo e($log->ip_address ?? '127.0.0.1'); ?>
                                </small>
                            </td>

                            <td>
                                <?php echo $log->status_badge; ?>
                            </td>

                            <td>
                                <div class="small text-slate-700 fw-medium">
                                    <?php echo e($log->keterangan ?? '-'); ?>
                                </div>
                                <?php if ($log->duration !== '—'): ?>
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">
                                        <i class="bi bi-stopwatch me-1"></i>Durasi: <?php echo $log->duration; ?>
                                        <?php if ($log->logout_at): ?>
                                            (Keluar: <?php echo e($log->logout_at->format('H:i')); ?>)
                                        <?php endif; ?>
                                    </small>
                                <?php endif; ?>
                            </td>

                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-3 px-2 py-1 shadow-xs" data-bs-toggle="modal" data-bs-target="#detailModal<?php echo $log->id; ?>" title="Lihat Rincian">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="small text-muted">
                Menampilkan <?php echo e($logs->firstItem() ?? 0); ?> - <?php echo e($logs->lastItem() ?? 0); ?> dari total <?php echo e($logs->total()); ?> catatan login
            </div>
            <div>
                <?php echo $logs->links('pagination::bootstrap-5'); ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Modals Detail for Each Log Item -->
<?php foreach($logs as $log): ?>
    <div class="modal fade" id="detailModal<?php echo $log->id; ?>" tabindex="-1" aria-labelledby="detailModalLabel<?php echo $log->id; ?>" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 card-custom overflow-hidden">
                <div class="modal-header border-bottom bg-slate-900 text-white p-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-shield-check text-info fs-5"></i>
                        <h6 class="modal-title fw-bold text-white mb-0">Rincian Log Autentikasi #<?php echo $log->id; ?></h6>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle border p-2 bg-light text-secondary d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                <i class="bi bi-person-circle fs-4"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-slate-800 mb-0"><?php echo e($log->user ? $log->user->name : $log->username); ?></h6>
                                <small class="text-muted"><?php echo e($log->username); ?> <?php if ($log->user): ?> &bull; <?php echo e($log->user->role_label); ?> <?php endif; ?></small>
                            </div>
                        </div>
                        <div>
                            <?php echo $log->status_badge; ?>
                        </div>
                    </div>

                    <div class="row g-3 small">
                        <div class="col-6">
                            <span class="text-muted d-block mb-1">Waktu Percobaan / Login:</span>
                            <span class="fw-bold text-slate-800 font-monospace">
                                <i class="bi bi-calendar-event me-1 text-primary"></i>
                                <?php echo e($log->login_at ? $log->login_at->format('d M Y, H:i:s') : '-'); ?>
                            </span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block mb-1">Waktu Selesai / Logout:</span>
                            <span class="fw-bold text-slate-800 font-monospace">
                                <i class="bi bi-clock me-1 text-secondary"></i>
                                <?php echo e($log->logout_at ? $log->logout_at->format('d M Y, H:i:s') : '-'); ?>
                            </span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block mb-1">Durasi Sesi:</span>
                            <span class="fw-bold text-slate-800">
                                <?php echo $log->duration; ?>
                            </span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block mb-1">Alamat IP:</span>
                            <span class="fw-bold text-slate-800 font-monospace">
                                <i class="bi bi-geo-alt me-1 text-danger"></i><?php echo e($log->ip_address ?? '-'); ?>
                            </span>
                        </div>
                        <div class="col-12">
                            <span class="text-muted d-block mb-1">Perangkat / Browser Terdeteksi:</span>
                            <span class="fw-bold text-slate-800">
                                <i class="bi bi-laptop me-1 text-info"></i><?php echo e($log->device ?? 'Tidak Diketahui'); ?>
                            </span>
                        </div>
                        <div class="col-12">
                            <span class="text-muted d-block mb-1">Keterangan Aktivitas:</span>
                            <div class="p-2.5 rounded-3 bg-light border text-slate-800 fw-medium">
                                <?php echo e($log->keterangan ?? '-'); ?>
                            </div>
                        </div>
                        <div class="col-12">
                            <span class="text-muted d-block mb-1">User Agent Header Lengkap:</span>
                            <div class="p-2.5 rounded-3 bg-light border font-monospace text-muted text-break" style="font-size: 0.72rem; max-height: 85px; overflow-y: auto;">
                                <?php echo e($log->user_agent ?: 'Tidak ada header User-Agent'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light py-2 px-4">
                    <button type="button" class="btn btn-secondary btn-sm rounded-3" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<!-- Cleanup Modal -->
<div class="modal fade" id="cleanupModal" tabindex="-1" aria-labelledby="cleanupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content border-0 card-custom overflow-hidden">
            <form action="<?php echo route('admin.login-logs.cleanup'); ?>" method="POST" data-confirm="Apakah Anda yakin ingin membersihkan riwayat log login sesuai kriteria yang dipilih?">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <input type="hidden" name="_method" value="DELETE">

                <div class="modal-header border-bottom bg-danger text-white p-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-trash3-fill fs-5"></i>
                        <h6 class="modal-title fw-bold text-white mb-0">Pembersihan Log Login</h6>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">
                        Pembersihan log lama secara berkala dapat menghemat kapasitas database sistem audit internal SMKP Minerba. Tindakan ini akan dicatat ke dalam log aktivitas admin.
                    </p>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-slate-800">Pilih Kriteria Pembersihan:</label>
                        <select name="days" class="form-select rounded-3">
                            <option value="30">Hapus log yang lebih lama dari 30 Hari</option>
                            <option value="60">Hapus log yang lebih lama dari 60 Hari</option>
                            <option value="90" selected>Hapus log yang lebih lama dari 90 Hari (Rekomendasi)</option>
                            <option value="180">Hapus log yang lebih lama dari 180 Hari</option>
                            <option value="all_failed">Hapus hanya seluruh riwayat percobaan login GAGAL</option>
                        </select>
                    </div>

                    <div class="alert alert-warning p-2.5 rounded-3 mb-0 small">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Data yang telah dihapus tidak dapat dipulihkan kembali.
                    </div>
                </div>

                <div class="modal-footer border-top bg-light p-3">
                    <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-3 fw-semibold">
                        <i class="bi bi-trash3 me-1"></i> Ya, Bersihkan Log
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script nonce="<?php echo e($cspNonce ?? ''); ?>">
    function toggleCustomDate(val) {
        const row = document.getElementById('customDateRow');
        if (!row) return;
        if (val === 'custom') {
            row.classList.remove('d-none');
        } else {
            row.classList.add('d-none');
        }
    }
</script>
<?php
$content = ob_get_clean();

echo view('layouts.app', get_defined_vars())->render();
