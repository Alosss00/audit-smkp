<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KriteriaRelasi;
use Illuminate\Http\JsonResponse;

/**
 * KriteriaRelasiController
 *
 * Menyediakan endpoint API untuk mengambil data relasi antar kriteria SMKP.
 * Digunakan oleh frontend JavaScript pada halaman matrix penilaian untuk
 * merender dua komponen UI:
 *   1. Banner Peringatan Konsistensi (jenis_relasi = 'kunci')
 *   2. Panel Referensi Pasif / Popover (jenis_relasi = 'referensi')
 *
 * REGISTRASI ROUTE (tambahkan di routes/api.php atau routes/web.php):
 *
 *   // Di routes/api.php — akses via /api/kriteria/{kode}/relasi
 *   Route::get('/kriteria/{kode}/relasi', [KriteriaRelasiController::class, 'show'])
 *       ->middleware('auth:sanctum')
 *       ->name('api.kriteria.relasi');
 *
 *   // ATAU di routes/web.php sebagai internal API endpoint
 *   Route::get('/admin/api/kriteria/{kode}/relasi', [KriteriaRelasiController::class, 'show'])
 *       ->middleware(['auth', 'prevent-back-history'])
 *       ->name('api.kriteria.relasi');
 */
class KriteriaRelasiController extends Controller
{
    /**
     * GET /api/kriteria/{kode}/relasi
     *
     * Mengembalikan daftar relasi untuk suatu kriteria, dikelompokkan
     * berdasarkan jenis_relasi ('kunci' dan 'referensi') agar mudah
     * dikonsumsi oleh komponen frontend JavaScript.
     *
     * @param  string $kode  Kode kriteria, e.g. "I.1.1" (URL-encoded: "I.1.1")
     * @return JsonResponse
     *
     * @example Response:
     * {
     *   "kode": "I.1.1",
     *   "kunci": [
     *     {
     *       "kriteria_tujuan_kode": "II.4.2",
     *       "deskripsi_keterkaitan": "Ketidaksesuaian di I.1.1 berpotensi memengaruhi..."
     *     }
     *   ],
     *   "referensi": [
     *     { "kriteria_tujuan_kode": "I.2.1", "deskripsi_keterkaitan": null },
     *     { "kriteria_tujuan_kode": "III.1.3", "deskripsi_keterkaitan": "..." }
     *   ],
     *   "total": 15
     * }
     */
    public function show(string $kode): JsonResponse
    {
        // Decode URL encoding (e.g. "I%2E1%2E1" -> "I.1.1")
        $kodeDecoded = urldecode($kode);

        // Ambil semua relasi dari kode ini dalam satu query, lalu group di PHP
        // (lebih efisien daripada dua query terpisah karena memanfaatkan indeks komposit)
        $relasi = KriteriaRelasi::where('kriteria_asal_kode', $kodeDecoded)
            ->orderBy('jenis_relasi')         // 'kunci' < 'referensi' secara alfabetis
            ->orderBy('kriteria_tujuan_kode')
            ->get(['kriteria_tujuan_kode', 'jenis_relasi', 'deskripsi_keterkaitan']);

        $grouped = $relasi->groupBy('jenis_relasi');

        return response()->json([
            'kode'      => $kodeDecoded,
            'kunci'     => $grouped->get('kunci', collect())
                ->map(fn ($r) => [
                    'kriteria_tujuan_kode'  => $r->kriteria_tujuan_kode,
                    'deskripsi_keterkaitan' => $r->deskripsi_keterkaitan,
                ])->values(),
            'referensi' => $grouped->get('referensi', collect())
                ->map(fn ($r) => [
                    'kriteria_tujuan_kode'  => $r->kriteria_tujuan_kode,
                    'deskripsi_keterkaitan' => $r->deskripsi_keterkaitan,
                ])->values(),
            'total'     => $relasi->count(),
        ]);
    }
}
