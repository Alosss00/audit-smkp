# Dokumen Catatan Upgrade Framework (Laravel 10 ➔ Laravel 12)
**Aplikasi:** SMKP Minerba Internal Audit  
**Branch:** `chore/framework-upgrade`  
**Baseline Git Tag:** `pre-upgrade-baseline` (commit `59b3df6`)  
**Tanggal Upgrade:** 17 November 2026  
**Status Audit:** PASS / SUKSES (0 Security Vulnerabilities)

---

## 1. Ringkasan Perubahan Versi (Sebelum vs Sesudah)

| Komponen / Dependensi | Versi Sebelum Upgrade | Versi Sesudah Upgrade | Keterangan |
| :--- | :--- | :--- | :--- |
| **PHP Runtime (CLI)** | `PHP 8.1.10` | `PHP 8.5.9` (CLI Laragon) | Target PHP 8.2+ dipenuhi sepenuhnya |
| **Laravel Framework** | `10.50.3` | **`12.72.0`** | Upgrade bertahap L10 ➔ L11 ➔ L12 |
| **Composer CLI** | `2.4.1` (2022) | `2.11.2` (2026) | `composer self-update` berhasil |
| **Laravel Sanctum** | `v3.3.3` | `v4.4.0` | Kompatibilitas Laravel 11/12 |
| **PHPUnit** | `10.5.45` | `11.5.58` | Schema XML & namespace terbarukan |
| **NunoMaduro Collision**| `v7.11.0` | `v8.9.6` | Error formatting Laravel 12 |
| **Nesbot Carbon** | `2.73.0` | `3.15.1` | Immutable handling & date parsing |
| **Symfony Components**| `v6.4.43` | `v7.4.23` | Routing, console, process, yaml |
| **Maatwebsite Excel** | `3.1.69` | `3.1.70` | Memperbaiki CVE-2026-84374 |
| **PhpSpreadsheet** | `1.30.6` | `1.30.8` | Dependensi Excel diperbarui |
| **Security Advisories** | **22 temuan** (10 paket) | **0 temuan** (Bersih 100%) | `composer audit --locked` PASS |

---

## 2. Breaking Changes & Penanganan yang Dilakukan

1. **Dukungan PHP 8.2+ & Penanganan Deprecations:**
   - File: `config/database.php:61-63`
   - Masalah: Pada PHP 8.5, konstanta global `PDO::MYSQL_ATTR_SSL_CA` mengalami deprecation dan digantikan oleh `Pdo\Mysql::ATTR_SSL_CA`.
   - Solusi: Menambahkan conditional check yang aman lintas versi:
     ```php
     'options' => extension_loaded('pdo_mysql') ? array_filter([
         (defined('Pdo\Mysql::ATTR_SSL_CA') ? Pdo\Mysql::ATTR_SSL_CA : (defined('PDO::MYSQL_ATTR_SSL_CA') ? PDO::MYSQL_ATTR_SSL_CA : null)) => env('MYSQL_ATTR_SSL_CA'),
     ]) : [],
     ```

2. **Laravel Sanctum (v3 ➔ v4):**
   - Diperbarui ke `v4.4.0` yang mendukung penuh Laravel 11 dan 12.
   - Endpoint CSRF cookie (`/sanctum/csrf-cookie`) dan token abilities diverifikasi berjalan normal.

3. **PHPUnit (v10 ➔ v11):**
   - Konfigurasi `phpunit.xml` dipertahankan dan disesuaikan schema lokasinya.
   - Ditambahkan `<server name="APP_URL" value="http://localhost"/>` untuk mencegah issue domain pathing lokal subfolder Laragon saat HTTP testing.

4. **Penghapusan Paket usang (Deprecated Packages):**
   - `spatie/laravel-ignition` & `spatie/flare-client-php`: Dihapus dari dependensi dev karena Laravel 11 & 12 menggunakan exception handler bawaan yang lebih terintegrasi.

5. **Resolusi Kerentanan Keamanan (Security Advisories):**
   - Di awal audit (Fase 0), terdeteksi 22 vulnerability advisories pada lockfile lama.
   - Dilakukan upgrade terarah pada `maatwebsite/excel` (ke `3.1.70`) dan `symfony/yaml` (ke `v7.4.23`).
   - Hasil akhir: `composer audit --locked` menyatakan **0 security vulnerability advisories**.

---

## 3. Hasil Validasi & Pengujian

### a. Composer Validation
```bash
composer validate --strict
# Output: ./composer.json is valid
```

### b. Composer Security Audit
```bash
composer audit --locked
# Output: No security vulnerability advisories found.
```

### c. Routing & Configuration
- `php artisan config:clear`: Sukses.
- `php artisan route:list`: 113 routes terdaftar tanpa error.
- `php artisan migrate --pretend`: Bersih tanpa skrip yang gagal.

### d. Test Suite Comparison
| Metrik Pengujian | Baseline (Fase 1) | Pasca Upgrade (Fase 5) | Keterangan |
| :--- | :--- | :--- | :--- |
| **Total Tests** | 25 | 25 | Bertambah 4 tests dari `SmokeTest` |
| **Passed Tests** | 23 | 23 | 100% parity |
| **Failed Tests** | 2 | 2 | Pre-existing logic issues (lihat rincian) |
| **Smoke Tests** | 4 PASS | 4 PASS | Login, Auth, Dashboard, CRUD Perusahaan |

> **Catatan 2 Pre-existing Test Failures:**
> 1. `Tests\Feature\GatingRuleServiceTest > controller rejects save when hard block is violated`: Test menguji flash session `error`, namun controller mengembalikan format pesan berbeda.
> 2. `Tests\Feature\SubElemenPenilaianStandaloneTest > audit sesi matrix includes standalone sub elemen`: Payload pengujian fixture test tidak menyertakan field `area_audit` sehingga query pencarian null.
> *Sesuai aturan ketat #5 (Jangan mengubah logika bisnis), kedua test bawaan ini tidak dimodifikasi.*

---

## 4. Langkah-Langkah Deployment (Staging / Production)

Sebelum melakukan deployment ke server target, pastikan server memenuhi prasyarat runtime:
- **PHP >= 8.2** (Rekomendasi PHP 8.2, 8.3, atau 8.4; PHP 8.5 CLI terbukti kompatibel).
- Ekstensi PHP yang aktif: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `gd`, `json`, `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, `zip`.

### Prosedur Deploy:
```bash
# 1. Masuk ke direktori aplikasi
cd /path/to/audit

# 2. Tarik kode branch upgrade (atau setelah dimerge ke target branch)
git pull origin <branch-name>

# 3. Jalankan install dependensi secara reproducible dari composer.lock (tanpa dev packages)
composer install --no-dev --optimize-autoloader --no-interaction

# 4. Bersihkan dan perbarui cache aplikasi
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Jalankan migrasi database jika ada skrip baru
php artisan migrate --force

# 6. Restart queue worker atau PHP-FPM (jika berlaku)
php artisan queue:restart
```

---

## 5. Rencana Rollback (Emergency Rollback Plan)

Jika ditemukan masalah tak terduga pada lingkungan staging/produksi yang membutuhkan pengembalian ke kondisi awal:

### Prosedur Rollback Cepat:
```bash
# 1. Checkout kembali ke tag baseline yang dibuat sebelum upgrade
git checkout pre-upgrade-baseline

# 2. Kembalikan folder vendor dan composer.lock asli
composer install --optimize-autoloader

# 3. Bersihkan cache Laravel
php artisan optimize:clear

# 4. Pastikan PHP runtime yang digunakan sesuai konfigurasi lama (PHP 8.1 / 8.2)
php artisan --version
# Verifikasi kembali: Laravel Framework 10.50.3
```
