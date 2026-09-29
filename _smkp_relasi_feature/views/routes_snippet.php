<?php
/**
 * SNIPPET ROUTE - Tambahkan ke dalam file routes/web.php proyek audit.
 *
 * Tambahkan di dalam blok middleware(['auth', 'prevent-back-history'])
 * yang sudah ada, di dalam kelompok admin atau sebagai standalone API endpoint.
 *
 * OPSI A: Sebagai Web Route internal (rekomendasi — tidak perlu Sanctum)
 * Tambahkan di dalam group: Route::middleware('role:admin,auditor_smkp')->...
 */

// Di dalam blok middleware(['auth', 'prevent-back-history']) -> middleware('role:admin,auditor_smkp'):
Route::get('/api/kriteria/{kode}/relasi',
    [\App\Http\Controllers\Api\KriteriaRelasiController::class, 'show']
)->name('api.kriteria.relasi');

/**
 * OPSI B: Di routes/api.php dengan Sanctum (jika frontend terpisah)
 */
// Route::middleware('auth:sanctum')->get('/kriteria/{kode}/relasi',
//     [\App\Http\Controllers\Api\KriteriaRelasiController::class, 'show']
// )->name('api.kriteria.relasi');
