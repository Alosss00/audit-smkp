==========================================================================
  README — Fitur: Hubungan Antar Elemen SMKP
  Folder: C:\laragon\www\audit\_smkp_relasi_feature\
==========================================================================

DESKRIPSI
---------
Folder ini berisi semua file implementasi fitur "Hubungan Antar Elemen"
pada Aplikasi Audit SMKP. File-file ini HARUS dipindahkan secara manual
ke lokasi yang tepat di dalam proyek.


LANGKAH INTEGRASI (URUTKAN SESUAI NOMOR)
-----------------------------------------

TAHAP 1: DATABASE (Jalankan terlebih dahulu)
--------------------------------------------
1. Salin file MIGRATION ke folder database/migrations/:
   SUMBER : _smkp_relasi_feature\migrations\2026_09_24_000001_refactor_kriteria_relasi_many_to_many.php
   TUJUAN : database\migrations\2026_09_24_000001_refactor_kriteria_relasi_many_to_many.php

2. Jalankan migrasi:
   php artisan migrate

   EFEK YANG TERJADI:
   a. Kolom dependency_id & dependency_note di-DROP dari tabel kriterias
   b. Tabel baru kriteria_relasi dibuat

TAHAP 2: MODEL (Update setelah migrasi)
-----------------------------------------
3. Salin file MODEL baru:
   SUMBER : _smkp_relasi_feature\models\KriteriaRelasi.php
   TUJUAN : app\Models\KriteriaRelasi.php

4. Update file EXISTING: app\Models\Kriteria.php
   Lihat file: _smkp_relasi_feature\models\Kriteria_Updated.php
   Perubahan yang perlu dilakukan:
   a. Hapus 'dependency_id' dan 'dependency_note' dari $fillable
   b. Hapus 'dependency_id' => 'integer' dari $casts
   c. Hapus method dependency() dan dependents()
   d. TAMBAHKAN tiga method baru: relasiKeluar(), relasiKunci(), relasiReferensi()

TAHAP 3: BACKEND API
-----------------------
5. Salin file CONTROLLER baru:
   SUMBER : _smkp_relasi_feature\controllers\KriteriaRelasiController.php
   TUJUAN : app\Http\Controllers\Api\KriteriaRelasiController.php
   (Buat folder Api/ jika belum ada)

6. Tambahkan ROUTE di routes\web.php:
   Lihat file: _smkp_relasi_feature\views\routes_snippet.php
   Tambahkan di dalam blok middleware(['auth', 'prevent-back-history'])
   -> middleware('role:admin,auditor_smkp'):

   Route::get('/admin/api/kriteria/{kode}/relasi',
       [\App\Http\Controllers\Api\KriteriaRelasiController::class, 'show']
   )->name('api.kriteria.relasi');

TAHAP 4: SEEDER (Isi data relasi)
-----------------------------------
7. Salin file SEEDER:
   SUMBER : _smkp_relasi_feature\seeders\KriteriaRelasiSeeder.php
   TUJUAN : database\seeders\KriteriaRelasiSeeder.php

8. Letakkan file Excel di:
   storage\app\seeder\Matriks_Hubungan_Antar_Elemen_SMKP.xlsx

9. Jalankan seeder:
   php artisan db:seed --class=KriteriaRelasiSeeder

TAHAP 5: FRONTEND VIEWS
--------------------------
10. Buat folder partials di views (jika belum ada):
    resources\views\partials\

11. Salin tiga file Blade partial:
    SUMBER : _smkp_relasi_feature\views\_relasi_kunci_banner.php
    TUJUAN : resources\views\partials\_relasi_kunci_banner.php

    SUMBER : _smkp_relasi_feature\views\_relasi_referensi_icon.php
    TUJUAN : resources\views\partials\_relasi_referensi_icon.php

    SUMBER : _smkp_relasi_feature\views\_relasi_js_engine.php
    TUJUAN : resources\views\partials\_relasi_js_engine.php

12. Update file: resources\views\auditor\audit\matrix.php
    Lihat instruksi detail di SECTION MODIFIKASI MATRIX BLADE di bawah.


MODIFIKASI MATRIX BLADE (resources\views\auditor\audit\matrix.php)
--------------------------------------------------------------------------

PERUBAHAN A: Controller method matrix() — eager-load relasi (di AuditSesiAdminController.php)
Tambahkan ->with('subElemens.kriterias.relasiKunci', 'subElemens.kriterias.relasiReferensi')
pada query eager-load Elemen di method matrix():

  $elemens = Elemen::with([
      'subElemens.kriterias.auditDetails' => fn($q) => $q->where('audit_sesi_id', $id),
      'subElemens.kriterias.relasiKunci',      // <-- TAMBAHKAN
      'subElemens.kriterias.relasiReferensi',  // <-- TAMBAHKAN
  ])->orderBy('kode_elemen')->get();

PERUBAHAN B: Kolom kode kriteria — tambahkan icon referensi
Cari blok <td class="fw-bold align-top pt-3"> dan tambahkan @include setelah badge:

  <td class="fw-bold align-top pt-3">
      <span class="badge bg-dark font-monospace fs-6 py-1 px-2">
          {{ $kriteria->kode_kriteria }}
      </span>
      {{-- TAMBAHKAN BARIS INI: --}}
      @include('partials._relasi_referensi_icon', ['kriteria' => $kriteria])
  </td>

PERUBAHAN C: Banner kunci — tambahkan setelah closing </tr> kriteria-row
Cari @endforeach dari @foreach($sub->kriterias as $kriteria) dan tambahkan
@include tepat sebelum closing </tr> baris kriteria:

  </tr>
  {{-- TAMBAHKAN SETELAH </tr> SETIAP KRITERIA ROW: --}}
  @include('partials._relasi_kunci_banner', ['kriteria' => $kriteria])

PERUBAHAN D: JS Engine — tambahkan sebelum </body>
Di bagian paling bawah matrix.php, sebelum @endsection atau akhir file:

  @include('partials._relasi_js_engine')


ARSITEKTUR KEPUTUSAN DESAIN
-----------------------------
- Kode kunci (String) dipilih sebagai join key, bukan ID integer, karena
  tabel kriteria bisa di-re-seed dan ID bisa berubah, sedangkan kode seperti
  "I.1.1" bersifat stabil dan merupakan identifier bisnis SMKP.
- Tidak ada foreign key constraint antara kriteria_relasi dan kriterias karena
  relasi bisa merujuk ke node induk (Sub-Elemen, bukan hanya leaf Kriteria).
- Komponen UI bersifat NON-BLOCKING — semua relasi hanya advisory.
- KriteriaGatingRule TIDAK diubah — itu adalah sistem yang berbeda (business rule).

==========================================================================
