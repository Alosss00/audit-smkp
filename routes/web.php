<?php

use App\Http\Controllers\Admin\AuditOversightController;
use App\Http\Controllers\Admin\AuditSesiAdminController;
use App\Http\Controllers\Admin\ElemenController;
use App\Http\Controllers\Admin\KriteriaController;
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
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
});

// Authenticated Routes (Protected with Anti-Back-History Cache)
Route::middleware(['auth', 'prevent-back-history'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Administrator & Auditor SMKP Routes (Dashboard, Penilaian & Monitoring)
    Route::middleware('role:admin,auditor_smkp')->prefix('admin')->as('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
        
        // Audit System Monitoring & Oversight
        Route::get('/rekap-audit', [AuditOversightController::class, 'index'])->name('rekap-audit.index');
        Route::get('/rekap-audit/{id}', [AuditOversightController::class, 'show'])->name('rekap-audit.show');
        Route::get('/rekap-audit/{id}/cetak', [AuditSesiAdminController::class, 'cetak'])->name('rekap-audit.cetak');
        Route::get('/rekap-audit/{id}/export-excel', [AuditSesiAdminController::class, 'exportExcel'])->name('rekap-audit.export-excel');

        // Monitoring PICA (Problem Identification and Corrective Action) — Oversight
        Route::get('/pica', [AdminPicaController::class, 'index'])->name('pica.index');
        Route::put('/pica/{id}', [AdminPicaController::class, 'update'])->name('pica.update');

        // Audit Sesi & Matrix Scoring
        Route::get('/audit-sesi/{id}/matrix', [AuditSesiAdminController::class, 'matrix'])->name('audit-sesi.matrix');
        Route::post('/audit-sesi/{id}/matrix', [AuditSesiAdminController::class, 'updateMatrix'])->name('audit-sesi.matrix.update');
        Route::get('/audit-sesi/{id}/rekap', [AuditSesiAdminController::class, 'rekap'])->name('audit-sesi.rekap');
        Route::get('/audit-sesi/{id}/cetak', [AuditSesiAdminController::class, 'cetak'])->name('audit-sesi.cetak');
        Route::get('/audit-sesi/{id}/export-excel', [AuditSesiAdminController::class, 'exportExcel'])->name('audit-sesi.export-excel');
        Route::post('/audit-sesi/{id}/finalisasi', [AuditSesiAdminController::class, 'finalisasi'])->name('audit-sesi.finalisasi');
        Route::post('/audit-sesi/{id}/restore', [AuditSesiAdminController::class, 'restore'])->name('audit-sesi.restore');
        Route::delete('/audit-sesi/{id}/force-delete', [AuditSesiAdminController::class, 'forceDelete'])->name('audit-sesi.force-delete');
        Route::resource('audit-sesi', AuditSesiAdminController::class);

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
            Route::post('/perusahaans/{id}/restore', [PerusahaanController::class, 'restore'])->name('perusahaans.restore');
            Route::delete('/perusahaans/{id}/force-delete', [PerusahaanController::class, 'forceDelete'])->name('perusahaans.force-delete');
            Route::resource('perusahaans', PerusahaanController::class);

            // Log Aktivitas User & Audit Trail Perubahan File
            Route::get('/audit-logs', [\App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('audit-logs.index');

            // Pusat Pemulihan Data Terhapus (Recycle Bin / Soft Delete Recovery)
            Route::get('/restore-points', [\App\Http\Controllers\Admin\RestorePointController::class, 'index'])->name('restore-points.index');
            Route::post('/restore-points/trash/{type}/{id}/restore', [\App\Http\Controllers\Admin\RestorePointController::class, 'restoreItem'])->name('restore-points.trash.restore');
            Route::delete('/restore-points/trash/{type}/{id}/force-delete', [\App\Http\Controllers\Admin\RestorePointController::class, 'forceDeleteItem'])->name('restore-points.trash.force-delete');
            Route::post('/restore-points/trash/restore-all', [\App\Http\Controllers\Admin\RestorePointController::class, 'restoreAll'])->name('restore-points.trash.restore-all');
            Route::post('/restore-points/trash/empty', [\App\Http\Controllers\Admin\RestorePointController::class, 'emptyTrash'])->name('restore-points.trash.empty');

            // User Management CRUD with Rate Limiting
            Route::middleware('throttle:30,1')->group(function () {
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
        Route::get('/audit-sesi/{id}/cetak', [AuditSesiController::class, 'cetak'])->name('audit-sesi.cetak');
        Route::get('/audit-sesi/{id}/export-excel', [AuditSesiController::class, 'exportExcel'])->name('audit-sesi.export-excel');

        // Modul PICA (Tindak Lanjut Perbaikan Temuan Audit oleh Auditee / PIC Area)
        Route::get('/pica', [PicaController::class, 'index'])->name('pica.index');
        Route::get('/pica/{id}/edit', [PicaController::class, 'edit'])->name('pica.edit');
        Route::put('/pica/{id}', [PicaController::class, 'update'])->name('pica.update');
    });
});
