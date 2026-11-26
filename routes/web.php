<?php

use App\Http\Controllers\Admin\AuditLampiranLaporanController;
use App\Http\Controllers\Admin\AuditLaporanController;
use App\Http\Controllers\Admin\AuditLaporanProfilController;
use App\Http\Controllers\Admin\AuditOfiController;
use App\Http\Controllers\Admin\AuditOversightController;
use App\Http\Controllers\Admin\AuditRencanaController;
use App\Http\Controllers\Admin\AuditSesiAdminController;
use App\Http\Controllers\Admin\AuditTahapAwalController;
use App\Http\Controllers\Admin\ElemenController;
use App\Http\Controllers\Admin\KriteriaController;
use App\Http\Controllers\Admin\MasterAturanPelaporanController;
use App\Http\Controllers\Admin\MasterDasarHukumController;
use App\Http\Controllers\Admin\MasterFaktorPenyesuaianController;
use App\Http\Controllers\Admin\MasterHariKerjaAuditController;
use App\Http\Controllers\Admin\MasterTemplateLaporanController;
use App\Http\Controllers\Admin\PicaController as AdminPicaController;
use App\Http\Controllers\Admin\PerusahaanController;
use App\Http\Controllers\Admin\SubElemenController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auditor\AuditSesiController;
use App\Http\Controllers\Auditor\PicaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - SMKP Minerba Internal Audit System
|--------------------------------------------------------------------------
*/

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Guest Routes (Authentication)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware('throttle:login');
});

// Authenticated Routes (Protected with Anti-Back-History Cache & Auto-Logout Inactivity)
Route::middleware(['auth', 'prevent-back-history', 'session.idle'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/session/keep-alive', [AuthController::class, 'keepAlive'])->name('session.keep-alive');
    Route::post('/session/auto-logout', [AuthController::class, 'autoLogout'])->name('session.auto-logout');

    // Secure Authenticated Media & Attachment Delivery
    Route::get('/attachments/{type}/{id}/{filename?}', [\App\Http\Controllers\AttachmentController::class, 'show'])->name('attachments.show');

    // Administrator & Auditor SMKP Routes (Dashboard, Penilaian & Monitoring)
    Route::middleware('role:admin,auditor_smkp')->prefix('admin')->as('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
        
        // Audit System Monitoring & Oversight
        Route::get('/rekap-audit', [AuditOversightController::class, 'index'])->name('rekap-audit.index');
        Route::get('/rekap-audit/{id}', [AuditOversightController::class, 'show'])->name('rekap-audit.show');
        Route::get('/rekap-audit/{id}/laporan-detail', [AuditSesiAdminController::class, 'laporanDetail'])->name('rekap-audit.laporan-detail');
        Route::get('/rekap-audit/{id}/cetak-lengkap', [AuditSesiAdminController::class, 'cetakLengkap'])->name('rekap-audit.cetak-lengkap');
        Route::get('/rekap-audit/{id}/cetak', [AuditSesiAdminController::class, 'cetak'])->name('rekap-audit.cetak');
        Route::get('/rekap-audit/{id}/cetak-tahap-awal', [AuditTahapAwalController::class, 'cetak'])->name('rekap-audit.cetak-tahap-awal');
        Route::get('/rekap-audit/{id}/cetak-ketidaksesuaian', [AuditSesiAdminController::class, 'cetakKetidaksesuaian'])->name('rekap-audit.cetak-ketidaksesuaian');
        Route::get('/rekap-audit/{id}/export-excel', [AuditSesiAdminController::class, 'exportExcel'])->name('rekap-audit.export-excel');

        // Monitoring PICA (Problem Identification and Corrective Action) — Oversight
        Route::get('/pica', [AdminPicaController::class, 'index'])->name('pica.index');
        Route::post('/pica/batch-update', [AdminPicaController::class, 'batchUpdate'])->name('pica.batch-update');
        Route::put('/pica/{id}', [AdminPicaController::class, 'update'])->name('pica.update');

        // Audit Sesi & Matrix Scoring & Tahap Awal Audit (TT-MGT-FRS-026L)
        Route::get('/audit-sesi/{id}/tahap-awal', [AuditTahapAwalController::class, 'edit'])->name('audit-sesi.tahap-awal');
        Route::put('/audit-sesi/{id}/tahap-awal', [AuditTahapAwalController::class, 'update'])->name('audit-sesi.tahap-awal.update');
        Route::post('/audit-sesi/{id}/tahap-awal/hitung-hari', [AuditTahapAwalController::class, 'hitungHariKerja'])
            ->middleware('throttle:30,1')
            ->name('audit-sesi.tahap-awal.hitung-hari');
        Route::post('/audit-sesi/{id}/tahap-awal/reset', [AuditTahapAwalController::class, 'resetPreset'])->name('audit-sesi.tahap-awal.reset');
        Route::get('/audit-sesi/{id}/tahap-awal/cetak', [AuditTahapAwalController::class, 'cetak'])->name('audit-sesi.tahap-awal.cetak');

        // Rencana Audit (Formulir TT-MGT-FRS-026A)
        Route::get('/audit-sesi/{id}/rencana', [AuditRencanaController::class, 'edit'])->name('audit-sesi.rencana.edit');
        Route::get('/audit-sesi/{id}/rencana/cetak', [AuditRencanaController::class, 'cetak'])->name('audit-sesi.rencana.cetak');
        Route::middleware('throttle:30,1')->group(function () {
            Route::put('/audit-sesi/{id}/rencana', [AuditRencanaController::class, 'update'])->name('audit-sesi.rencana.update');
            Route::post('/audit-sesi/{id}/rencana/finalisasi', [AuditRencanaController::class, 'finalize'])->name('audit-sesi.rencana.finalize');
            Route::post('/audit-sesi/{id}/rencana/salin-tim-tahap-2', [AuditRencanaController::class, 'copyTimTahap2'])->name('audit-sesi.rencana.copy-tim');
        });
        Route::get('/audit-sesi/{id}/matrix', [AuditSesiAdminController::class, 'matrix'])->name('audit-sesi.matrix');
        Route::post('/audit-sesi/{id}/matrix', [AuditSesiAdminController::class, 'updateMatrix'])->name('audit-sesi.matrix.update');
        Route::get('/audit-sesi/{id}/rekap', [AuditSesiAdminController::class, 'rekap'])->name('audit-sesi.rekap');
        Route::get('/audit-sesi/{id}/laporan-detail', [AuditSesiAdminController::class, 'laporanDetail'])->name('audit-sesi.laporan-detail');
        Route::get('/audit-sesi/{id}/cetak-lengkap', [AuditSesiAdminController::class, 'cetakLengkap'])->name('audit-sesi.cetak-lengkap');
        Route::get('/audit-sesi/{id}/cetak', [AuditSesiAdminController::class, 'cetak'])->name('audit-sesi.cetak');
        Route::get('/audit-sesi/{id}/cetak-ketidaksesuaian', [AuditSesiAdminController::class, 'cetakKetidaksesuaian'])->name('audit-sesi.cetak-ketidaksesuaian');
        Route::get('/audit-sesi/{id}/cetak-kesesuaian', [AuditSesiAdminController::class, 'cetakKesesuaian'])->name('audit-sesi.cetak-kesesuaian');

        // Formulir OFI (TT-MGT-FRS-026G)
        Route::get('/audit-sesi/{id}/ofi', [AuditOfiController::class, 'index'])->name('audit-sesi.ofi.index');
        Route::get('/audit-sesi/{id}/ofi/cetak', [AuditOfiController::class, 'cetak'])->name('audit-sesi.ofi.cetak');
        Route::middleware('throttle:30,1')->group(function () {
            Route::post('/audit-sesi/{id}/ofi', [AuditOfiController::class, 'store'])->name('audit-sesi.ofi.store');
            Route::put('/audit-sesi/{id}/ofi/{ofiId}', [AuditOfiController::class, 'update'])->name('audit-sesi.ofi.update');
            Route::delete('/audit-sesi/{id}/ofi/{ofiId}', [AuditOfiController::class, 'destroy'])->name('audit-sesi.ofi.destroy');
        });

        // Profil & Informasi Umum Laporan Audit Internal SMKP (Fase L1)
        Route::get('/audit-sesi/{id}/laporan/profil', [AuditLaporanProfilController::class, 'edit'])->name('audit-sesi.laporan.profil');
        Route::middleware('throttle:30,1')->group(function () {
            Route::put('/audit-sesi/{id}/laporan/profil', [AuditLaporanProfilController::class, 'update'])->name('audit-sesi.laporan.profil.update');
            Route::post('/audit-sesi/{id}/laporan/profil/salin-tahap-awal', [AuditLaporanProfilController::class, 'copyFromTahapAwal'])->name('audit-sesi.laporan.profil.copy-tahap-awal');
            Route::post('/audit-sesi/{id}/laporan/profil/salin-periode-sebelumnya', [AuditLaporanProfilController::class, 'copyFromPreviousPeriod'])->name('audit-sesi.laporan.profil.copy-prev-period');
        });

        // Alur Pengesahan, Snapshot, & Distribusi Laporan Audit Internal SMKP (Fase L2 & L3)
        Route::get('/audit-sesi/{id}/laporan', [AuditLaporanController::class, 'show'])->name('audit-sesi.laporan.show');
        Route::get('/audit-sesi/{id}/laporan/cetak', [AuditLaporanController::class, 'cetak'])->name('audit-sesi.laporan.cetak');
        Route::middleware('throttle:30,1')->group(function () {
            Route::put('/audit-sesi/{id}/laporan/metadata', [AuditLaporanController::class, 'updateMetadata'])->name('audit-sesi.laporan.metadata');
            Route::put('/audit-sesi/{id}/laporan/teks-override', [AuditLaporanController::class, 'updateTeksOverride'])->name('audit-sesi.laporan.teks-override');
            Route::post('/audit-sesi/{id}/laporan/verifikasi-hash', [AuditLaporanController::class, 'verifyHash'])->name('audit-sesi.laporan.verify-hash');
            Route::post('/audit-sesi/{id}/laporan/ajukan-tinjauan', [AuditLaporanController::class, 'submitReview'])->name('audit-sesi.laporan.submit-review');
            Route::post('/audit-sesi/{id}/laporan/sahkan', [AuditLaporanController::class, 'approve'])->name('audit-sesi.laporan.approve');
            Route::post('/audit-sesi/{id}/laporan/distribusi', [AuditLaporanController::class, 'storeDistribusi'])->name('audit-sesi.laporan.distribusi.store');
            Route::delete('/audit-sesi/{id}/laporan/distribusi/{distId}', [AuditLaporanController::class, 'destroyDistribusi'])->name('audit-sesi.laporan.distribusi.destroy');
            Route::post('/audit-sesi/{id}/laporan/dilaporkan', [AuditLaporanController::class, 'markReported'])->name('audit-sesi.laporan.mark-reported');
            Route::patch('/audit-sesi/{id}/laporan/jadwal-status', [AuditLaporanController::class, 'updateJadwalStatus'])->name('audit-sesi.laporan.jadwal-status');
        });

        // Formulir Lampiran Laporan (Fase L4: Daftar Hadir, Respon Manajemen, Hasil Audit IUJP)
        Route::get('/audit-sesi/{id}/laporan/lampiran', [AuditLampiranLaporanController::class, 'index'])->name('audit-sesi.laporan.lampiran');
        Route::get('/audit-sesi/{id}/laporan/daftar-hadir/{jenis}/cetak', [AuditLampiranLaporanController::class, 'cetakDaftarHadir'])->name('audit-sesi.laporan.daftar-hadir.cetak');
        Route::middleware('throttle:30,1')->group(function () {
            Route::put('/audit-sesi/{id}/laporan/daftar-hadir/{jenis}', [AuditLampiranLaporanController::class, 'saveDaftarHadir'])->name('audit-sesi.laporan.daftar-hadir.save');
            Route::post('/audit-sesi/{id}/laporan/daftar-hadir/{jenis}/prefill', [AuditLampiranLaporanController::class, 'prefillDaftarHadir'])->name('audit-sesi.laporan.daftar-hadir.prefill');
            Route::put('/audit-sesi/{id}/laporan/respon-manajemen', [AuditLampiranLaporanController::class, 'saveResponManajemen'])->name('audit-sesi.laporan.respon-manajemen.save');
            Route::post('/audit-sesi/{id}/laporan/respon-manajemen/prefill', [AuditLampiranLaporanController::class, 'prefillResponManajemen'])->name('audit-sesi.laporan.respon-manajemen.prefill');
            Route::post('/audit-sesi/{id}/laporan/iujp', [AuditLampiranLaporanController::class, 'storeIujp'])->name('audit-sesi.laporan.iujp.store');
            Route::delete('/audit-sesi/{id}/laporan/iujp/{iujpId}', [AuditLampiranLaporanController::class, 'destroyIujp'])->name('audit-sesi.laporan.iujp.destroy');
        });
        Route::get('/audit-sesi/{id}/export-excel', [AuditSesiAdminController::class, 'exportExcel'])->name('audit-sesi.export-excel');
        Route::post('/audit-sesi/{id}/finalisasi', [AuditSesiAdminController::class, 'finalisasi'])->name('audit-sesi.finalisasi');
        Route::post('/audit-sesi/{id}/restore', [AuditSesiAdminController::class, 'restore'])->name('audit-sesi.restore');
        Route::delete('/audit-sesi/{id}/force-delete', [AuditSesiAdminController::class, 'forceDelete'])->name('audit-sesi.force-delete');
        Route::resource('audit-sesi', AuditSesiAdminController::class);

        // Hubungan Antar Elemen SMKP � Advisory Relasi API
        Route::get('/api/kriteria/{kode}/relasi', [\App\Http\Controllers\Api\KriteriaRelasiController::class, 'show'])->name('api.kriteria.relasi');

        // Master Data CRUD & Restore Points & User Management (Administrator Only)
        Route::middleware('role:admin')->group(function () {
            Route::post('/elemens/{id}/restore', [ElemenController::class, 'restore'])->name('elemens.restore');
            Route::delete('/elemens/{id}/force-delete', [ElemenController::class, 'forceDelete'])->name('elemens.force-delete');
            Route::resource('elemens', ElemenController::class);

            Route::patch('/sub-elemens/{id}/toggle-na', [SubElemenController::class, 'toggleNa'])->name('sub-elemens.toggle-na');
            Route::post('/sub-elemens/{id}/restore', [SubElemenController::class, 'restore'])->name('sub-elemens.restore');
            Route::delete('/sub-elemens/{id}/force-delete', [SubElemenController::class, 'forceDelete'])->name('sub-elemens.force-delete');
            Route::resource('sub-elemens', SubElemenController::class);

            Route::patch('/kriterias/{id}/toggle-na', [KriteriaController::class, 'toggleNa'])->name('kriterias.toggle-na');
            Route::post('/kriterias/{id}/restore', [KriteriaController::class, 'restore'])->name('kriterias.restore');
            Route::delete('/kriterias/{id}/force-delete', [KriteriaController::class, 'forceDelete'])->name('kriterias.force-delete');
            Route::resource('kriterias', KriteriaController::class);
            
            Route::patch('/perusahaans/{id}/toggle-status', [PerusahaanController::class, 'toggleStatus'])->name('perusahaans.toggle-status');
            Route::put('/perusahaans/{id}/legalitas', [PerusahaanController::class, 'updateLegalitas'])
                ->middleware('throttle:30,1')
                ->name('perusahaans.legalitas.update');
            Route::post('/perusahaans/{id}/restore', [PerusahaanController::class, 'restore'])->name('perusahaans.restore');
            Route::delete('/perusahaans/{id}/force-delete', [PerusahaanController::class, 'forceDelete'])->name('perusahaans.force-delete');
            Route::resource('perusahaans', PerusahaanController::class);

            // Master Hari Kerja Audit & Faktor Penyesuaian (Fase 1 Perangkat Audit SMKP)
            Route::get('/master-hari-kerja', [MasterHariKerjaAuditController::class, 'index'])->name('master-hari-kerja.index');
            Route::get('/master-hari-kerja/template', [MasterHariKerjaAuditController::class, 'downloadTemplate'])->name('master-hari-kerja.template');
            Route::get('/master-faktor-penyesuaian', [MasterFaktorPenyesuaianController::class, 'index'])->name('master-faktor-penyesuaian.index');

            // Master Dasar Hukum, Template Laporan, & Aturan Pelaporan + Revisi Laporan (Fase L2)
            Route::get('/master-dasar-hukum', [MasterDasarHukumController::class, 'index'])->name('master-dasar-hukum.index');
            Route::get('/master-template-laporan', [MasterTemplateLaporanController::class, 'index'])->name('master-template-laporan.index');
            Route::get('/master-aturan-pelaporan', [MasterAturanPelaporanController::class, 'index'])->name('master-aturan-pelaporan.index');

            Route::middleware('throttle:30,1')->group(function () {
                Route::post('/audit-sesi/{id}/laporan/revisi', [AuditLaporanController::class, 'reopenRevision'])->name('audit-sesi.laporan.reopen-revision');

                Route::post('/master-dasar-hukum', [MasterDasarHukumController::class, 'store'])->name('master-dasar-hukum.store');
                Route::put('/master-dasar-hukum/{id}', [MasterDasarHukumController::class, 'update'])->name('master-dasar-hukum.update');
                Route::patch('/master-dasar-hukum/{id}/toggle-verify', [MasterDasarHukumController::class, 'toggleVerify'])->name('master-dasar-hukum.toggle-verify');
                Route::delete('/master-dasar-hukum/{id}', [MasterDasarHukumController::class, 'destroy'])->name('master-dasar-hukum.destroy');

                Route::put('/master-template-laporan/{id}', [MasterTemplateLaporanController::class, 'update'])->name('master-template-laporan.update');
                Route::patch('/master-template-laporan/{id}/toggle-verify', [MasterTemplateLaporanController::class, 'toggleVerify'])->name('master-template-laporan.toggle-verify');

                Route::post('/master-aturan-pelaporan', [MasterAturanPelaporanController::class, 'store'])->name('master-aturan-pelaporan.store');
                Route::put('/master-aturan-pelaporan/{id}', [MasterAturanPelaporanController::class, 'update'])->name('master-aturan-pelaporan.update');
                Route::patch('/master-aturan-pelaporan/{id}/toggle-verify', [MasterAturanPelaporanController::class, 'toggleVerify'])->name('master-aturan-pelaporan.toggle-verify');
                Route::delete('/master-aturan-pelaporan/{id}', [MasterAturanPelaporanController::class, 'destroy'])->name('master-aturan-pelaporan.destroy');
            });
            Route::middleware('throttle:30,1')->group(function () {
                Route::post('/master-hari-kerja', [MasterHariKerjaAuditController::class, 'store'])->name('master-hari-kerja.store');
                Route::put('/master-hari-kerja/{id}', [MasterHariKerjaAuditController::class, 'update'])->name('master-hari-kerja.update');
                Route::patch('/master-hari-kerja/{id}/toggle-verify', [MasterHariKerjaAuditController::class, 'toggleVerify'])->name('master-hari-kerja.toggle-verify');
                Route::delete('/master-hari-kerja/{id}', [MasterHariKerjaAuditController::class, 'destroy'])->name('master-hari-kerja.destroy');
                Route::post('/master-hari-kerja/{id}/restore', [MasterHariKerjaAuditController::class, 'restore'])->name('master-hari-kerja.restore');
                Route::delete('/master-hari-kerja/{id}/force-delete', [MasterHariKerjaAuditController::class, 'forceDelete'])->name('master-hari-kerja.force-delete');
                Route::post('/master-hari-kerja/import-preview', [MasterHariKerjaAuditController::class, 'importPreview'])->name('master-hari-kerja.import-preview');
                Route::post('/master-hari-kerja/import-confirm', [MasterHariKerjaAuditController::class, 'importConfirm'])->name('master-hari-kerja.import-confirm');

                Route::post('/master-faktor-penyesuaian', [MasterFaktorPenyesuaianController::class, 'store'])->name('master-faktor-penyesuaian.store');
                Route::put('/master-faktor-penyesuaian/{id}', [MasterFaktorPenyesuaianController::class, 'update'])->name('master-faktor-penyesuaian.update');
                Route::patch('/master-faktor-penyesuaian/{id}/toggle-active', [MasterFaktorPenyesuaianController::class, 'toggleActive'])->name('master-faktor-penyesuaian.toggle-active');
                Route::delete('/master-faktor-penyesuaian/{id}', [MasterFaktorPenyesuaianController::class, 'destroy'])->name('master-faktor-penyesuaian.destroy');
                Route::post('/master-faktor-penyesuaian/{id}/restore', [MasterFaktorPenyesuaianController::class, 'restore'])->name('master-faktor-penyesuaian.restore');
                Route::delete('/master-faktor-penyesuaian/{id}/force-delete', [MasterFaktorPenyesuaianController::class, 'forceDelete'])->name('master-faktor-penyesuaian.force-delete');
            });

            // Log Aktivitas User & Audit Trail Perubahan File
            Route::get('/audit-logs', [\App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('audit-logs.index');

            // Log Riwayat Login & Sesi Pengguna
            Route::get('/login-logs', [\App\Http\Controllers\Admin\LoginLogController::class, 'index'])->name('login-logs.index');
            Route::delete('/login-logs/cleanup', [\App\Http\Controllers\Admin\LoginLogController::class, 'cleanup'])->name('login-logs.cleanup');

            // Pusat Pemulihan Data Terhapus (Recycle Bin / Soft Delete Recovery)
            Route::get('/restore-points', [\App\Http\Controllers\Admin\RestorePointController::class, 'index'])->name('restore-points.index');
            Route::post('/restore-points/trash/{type}/{id}/restore', [\App\Http\Controllers\Admin\RestorePointController::class, 'restoreItem'])->name('restore-points.trash.restore');
            Route::delete('/restore-points/trash/{type}/{id}/force-delete', [\App\Http\Controllers\Admin\RestorePointController::class, 'forceDeleteItem'])->name('restore-points.trash.force-delete');
            Route::post('/restore-points/trash/restore-all', [\App\Http\Controllers\Admin\RestorePointController::class, 'restoreAll'])->name('restore-points.trash.restore-all');
            Route::post('/restore-points/trash/empty', [\App\Http\Controllers\Admin\RestorePointController::class, 'emptyTrash'])->name('restore-points.trash.empty');

            // User Management CRUD with Rate Limiting
            Route::middleware('throttle:30,1')->group(function () {
                Route::post('/users/{id}/unlock', [UserController::class, 'unlock'])->name('users.unlock');
                Route::patch('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
                Route::post('/users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
                Route::delete('/users/{id}/force-delete', [UserController::class, 'forceDelete'])->name('users.force-delete');
                Route::resource('users', UserController::class);
            });
        });
    });

    // Auditor Routes (Auditee / PIC Area — Read-Only Rekap & Tindak Lanjut PICA)
    Route::middleware('role:auditor')->prefix('auditor')->as('auditor.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'auditor'])->name('dashboard');

        // Audit Session Read-Only Views
        Route::get('/audit-sesi', [AuditSesiController::class, 'index'])->name('audit-sesi.index');
        Route::get('/audit-sesi/{id}/rekap', [AuditSesiController::class, 'rekap'])->name('audit-sesi.rekap');
        Route::get('/audit-sesi/{id}/laporan-detail', [AuditSesiController::class, 'laporanDetail'])->name('audit-sesi.laporan-detail');
        Route::get('/audit-sesi/{id}/cetak-lengkap', [AuditSesiController::class, 'cetakLengkap'])->name('audit-sesi.cetak-lengkap');
        Route::get('/audit-sesi/{id}/cetak', [AuditSesiController::class, 'cetak'])->name('audit-sesi.cetak');
        Route::get('/audit-sesi/{id}/cetak-tahap-awal', [AuditTahapAwalController::class, 'cetak'])->name('audit-sesi.cetak-tahap-awal');
        Route::get('/audit-sesi/{id}/rencana', [AuditRencanaController::class, 'showForAuditor'])->name('audit-sesi.rencana.show');
        Route::get('/audit-sesi/{id}/rencana/cetak', [AuditRencanaController::class, 'cetak'])->name('audit-sesi.rencana.cetak');
        Route::get('/audit-sesi/{id}/cetak-ketidaksesuaian', [AuditSesiController::class, 'cetakKetidaksesuaian'])->name('audit-sesi.cetak-ketidaksesuaian');
        Route::get('/audit-sesi/{id}/cetak-kesesuaian', [AuditSesiController::class, 'cetakKesesuaian'])->name('audit-sesi.cetak-kesesuaian');
        Route::get('/audit-sesi/{id}/ofi', [AuditOfiController::class, 'indexForAuditor'])->name('audit-sesi.ofi.index');
        Route::get('/audit-sesi/{id}/ofi/cetak', [AuditOfiController::class, 'cetak'])->name('audit-sesi.ofi.cetak');
        Route::get('/audit-sesi/{id}/laporan/profil', [AuditLaporanProfilController::class, 'showForAuditor'])->name('audit-sesi.laporan.profil');
        Route::get('/audit-sesi/{id}/laporan', [AuditLaporanController::class, 'showForAuditor'])->name('audit-sesi.laporan.show');
        Route::get('/audit-sesi/{id}/laporan/cetak', [AuditLaporanController::class, 'cetak'])->name('audit-sesi.laporan.cetak');
        Route::get('/audit-sesi/{id}/laporan/lampiran', [AuditLampiranLaporanController::class, 'showForAuditor'])->name('audit-sesi.laporan.lampiran');
        Route::get('/audit-sesi/{id}/laporan/daftar-hadir/{jenis}/cetak', [AuditLampiranLaporanController::class, 'cetakDaftarHadir'])->name('audit-sesi.laporan.daftar-hadir.cetak');
        Route::get('/audit-sesi/{id}/export-excel', [AuditSesiController::class, 'exportExcel'])->name('audit-sesi.export-excel');

        // Modul PICA (Tindak Lanjut Perbaikan Temuan Audit oleh Auditee / PIC Area)
        Route::get('/pica', [PicaController::class, 'index'])->name('pica.index');
        Route::get('/pica/{id}/edit', [PicaController::class, 'edit'])->name('pica.edit');
        Route::put('/pica/{id}', [PicaController::class, 'update'])->name('pica.update');
    });
});
