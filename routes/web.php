<?php

use App\Http\Controllers\Admin\AuditOversightController;
use App\Http\Controllers\Admin\ElemenController;
use App\Http\Controllers\Admin\KriteriaController;
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

    // Administrator Routes
    Route::middleware('role:admin')->prefix('admin')->as('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
        
        // Audit System Monitoring & Oversight
        Route::get('/rekap-audit', [AuditOversightController::class, 'index'])->name('rekap-audit.index');
        Route::get('/rekap-audit/{id}', [AuditOversightController::class, 'show'])->name('rekap-audit.show');
        Route::get('/rekap-audit/{id}/cetak', [AuditSesiController::class, 'cetak'])->name('rekap-audit.cetak');
        Route::get('/rekap-audit/{id}/export-csv', [AuditSesiController::class, 'exportCsv'])->name('rekap-audit.export-csv');

        // Master Data CRUD
        Route::post('/elemens/{id}/restore', [ElemenController::class, 'restore'])->name('elemens.restore');
        Route::delete('/elemens/{id}/force-delete', [ElemenController::class, 'forceDelete'])->name('elemens.force-delete');
        Route::resource('elemens', ElemenController::class);
        Route::resource('sub-elemens', SubElemenController::class);
        Route::resource('kriterias', KriteriaController::class);
        Route::patch('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::resource('users', UserController::class);
    });

    // Auditor Routes
    Route::middleware('role:auditor')->prefix('auditor')->as('auditor.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'auditor'])->name('dashboard');

        // Audit Session & Matrix Assessment Lifecycle
        Route::get('/audit-sesi/{id}/matrix', [AuditSesiController::class, 'matrix'])->name('audit-sesi.matrix');
        Route::post('/audit-sesi/{id}/matrix', [AuditSesiController::class, 'updateMatrix'])->name('audit-sesi.matrix.update');
        Route::get('/audit-sesi/{id}/rekap', [AuditSesiController::class, 'rekap'])->name('audit-sesi.rekap');
        Route::get('/audit-sesi/{id}/cetak', [AuditSesiController::class, 'cetak'])->name('audit-sesi.cetak');
        Route::get('/audit-sesi/{id}/export-csv', [AuditSesiController::class, 'exportCsv'])->name('audit-sesi.export-csv');
        Route::post('/audit-sesi/{id}/finalisasi', [AuditSesiController::class, 'finalisasi'])->name('audit-sesi.finalisasi');
        Route::resource('audit-sesi', AuditSesiController::class);

        // Modul PICA (Problem Identification and Corrective Action)
        Route::get('/pica', [PicaController::class, 'index'])->name('pica.index');
        Route::put('/pica/{id}', [PicaController::class, 'update'])->name('pica.update');
    });
});
