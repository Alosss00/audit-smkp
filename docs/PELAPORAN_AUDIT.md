# Dokumentasi Teknis — Modul Pelaporan Audit Internal SMKP

Modul ini melengkapi aplikasi Audit Internal SMKP Minerba (Laravel 12) untuk menyusun, mengesahkan, mengunci integritas (snapshot SHA-256), mendistribusikan, mencetak, dan memantau tenggat penyampaian **Laporan Audit Internal SMKP**.

---

## 1. Arsitektur & Fase Implementasi (L1–L5)
- **Fase L1 — Profil, Legalitas, Lingkup, & Tim**:
  - `perusahaan_legalitas` (1-to-1 per perusahaan, dikelola Admin di Master Perusahaan).
  - `audit_laporans`, `audit_laporan_profils`, `audit_laporan_peralatans` (total per kelompok & grand total dihitung otomatis), `audit_laporan_narasi_elemens`.
  - `audit_rencana_areas` (daftar area terstruktur pada Rencana Audit) & penambahan peran `pemandu` pada `audit_rencana_tims`.
  - Tombol bantu **Salin Pekerja dari Tahap Awal** dan **Salin dari Periode Sebelumnya** (`tahun_periode - 1`).
- **Fase L2 — Master Regulasi/Template/Aturan & Alur Pengesahan**:
  - 3 tabel master editable: `master_dasar_hukum`, `master_template_laporan`, `master_aturan_pelaporan`. Seluruh data awal dari seeder ditandai `is_verified = false` agar diverifikasi oleh Administrator terhadap regulasi terbaru.
  - `LaporanTemplateRenderer`: penggantian placeholder ketat berbasis daftar putih (`ALLOWED_PLACEHOLDERS`), *escaping* `e()`, tanpa Blade dan tanpa `eval`.
  - Alur status: `draft` → `ditinjau` → `disahkan` → `didistribusikan` → `dilaporkan`, serta fitur **Revisi** (khusus Admin, menaikkan `revisi_ke` dan mempertahankan seluruh snapshot lama).
  - Saat disahkan, `LaporanAuditWorkflowService` membuat snapshot JSON kanonik + hash SHA-256 pada `audit_laporan_snapshots`.
  - `AuditLaporan::auditDinyatakanSelesai()` memvalidasi keterlaksanaan seluruh jadwal (termasuk Rapat Penutupan) dan kelengkapan penilaian matriks.
- **Fase L3 — Generator Laporan Cetak & Grafik Radar SVG**:
  - Konfigurasi tipografi cetak & skema kode temuan tunggal (`NC-KRT-001`, `NC-MYR-001`, `NC-MNR-001`) di `config/laporan.php`.
  - `App\Support\Terbilang` untuk konversi angka bulat & desimal (`koma`).
  - `App\Services\RadarChartSvg` menghasilkan SVG grafik radar 7 sumbu di sisi server (patuh CSP ketat, tanpa pustaka JS & tanpa inline style).
  - `resources/views/laporan/cetak_laporan_audit.php`:
    - Status `draft`/`ditinjau`: dirender dari data hidup dengan watermark **`DRAFT — BELUM DISAHKAN`**.
    - Status `disahkan` ke atas: dirender **sepenuhnya dari snapshot JSON** beserta footer SHA-256.
- **Fase L4 — Formulir Lampiran (A–F)**:
  - `audit_daftar_hadirs` & `audit_daftar_hadir_pesertas` (judul kegiatan otomatis sesuai jenis Pembukaan/Penutupan, nama hari Indonesia otomatis, prefill dari Rencana Audit, peringatan tanggal, cetak A4, upload scan).
  - `audit_respon_manajemens` (prefill dari template `respon_manajemen`, peringatan tanggal, upload dokumen).
  - `audit_laporan_iujps` (khusus perusahaan `tambang` / `pengolahan_pemurnian`, tarik otomatis dari sesi IUJP selesai atau input manual).
  - Penambahan blok tanda tangan persetujuan Ketua Tim Auditor & Perwakilan Auditi pada `cetak_ketidaksesuaian.php`.
- **Fase L5 — Navigasi, Dokumen Wajib, Dashboard, & Restore Point**:
  - `audit_dokumen_acuans` & checklist 5 dokumen wajib (RKAB, SOP Audit, Manual SMKP, Rencana Audit, Formulir Audit).
  - Langkah ke-8 (`Laporan`) pada panel navigasi Perangkat Audit, tautan sidebar Admin & Auditor, widget monitoring tenggat pada Dashboard Admin, menu Laporan Disahkan untuk Auditor, serta pendaftaran seluruh 16 tabel baru di `RestorePointService`.

---

## 2. Keamanan & Integritas
- **CSP Ketat**: Seluruh skrip inline memakai `nonce="<?php echo e($cspNonce ?? ''); ?>"`, nol *inline event handler*, grafik dibuat sebagai SVG server-side.
- **Role & Area Scoping**:
  - Master data & buka revisi: khusus `role:admin`.
  - Penyusunan & pengesahan: `role:admin,auditor_smkp`.
  - Role `auditor`: hanya baca & dibatasi area melalui `AuditSesi::forUserArea()` / `matchesUserArea()`; menu `auditor/laporan-audit` hanya menampilkan laporan berstatus `disahkan` ke atas.
- **Unggahan Berkas**: Hanya menerima `pdf,jpg,jpeg,png` maksimal 5 MB ke disk privat `attachments`, disajikan melalui `AttachmentController` dengan validasi autentikasi & area.
- **Audit Trail**: Seluruh aksi buat, ubah, hapus, verifikasi, pengesahan, dan revisi dicatat pada `audit_logs`.
- **Verifikasi Integritas**: Tombol **Verifikasi Integritas Hash** menghitung ulang SHA-256 dari `payload_json` snapshot terbaru dan membandingkannya menggunakan `hash_equals()`.

---

## 3. Operasional & Pemeliharaan
1. Setelah menjalankan `php artisan migrate`, jalankan 3 seeder master:
   - `php artisan db:seed --class=MasterDasarHukumSeeder`
   - `php artisan db:seed --class=MasterTemplateLaporanSeeder`
   - `php artisan db:seed --class=MasterAturanPelaporanSeeder`
2. Administrator wajib meninjau dan memverifikasi (**Toggle Verifikasi**) seluruh entri dasar hukum, template, dan aturan tenggat pelaporan sebelum digunakan pada laporan resmi.
3. Untuk menyesuaikan ukuran huruf atau margin cetak, ubah nilai pada `config/laporan.php` atau variabel lingkungan (`LAPORAN_FONT_FAMILY`, `LAPORAN_FONT_SIZE_JUDUL`, dll).
