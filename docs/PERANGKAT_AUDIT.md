# Dokumentasi Modul Perangkat Audit SMKP Minerba

Dokumen ini menjelaskan alur **7 langkah Perangkat Audit SMKP**, pemetaan sumber aturan (Kepdirjen ESDM 185.K/37.04/DJB/2019 Lampiran II vs materi pelatihan Praktek Penyusunan Perangkat Audit SMKP), daftar asumsi yang perlu diverifikasi, serta panduan rollback tiap fase.

---

## 1. Alur 7 Langkah Perangkat Audit SMKP

Setiap sesi audit memiliki panel navigasi **Perangkat Audit SMKP (7 Langkah)** dengan status (`Belum Diisi`, `Draft`, `Final`):

| Langkah | Nama Modul | Nomor Dokumen / Acuan | Route Admin / Auditor SMKP | Route Auditor Perusahaan (Read-Only) |
|---|---|---|---|---|
| 1 | **Tahap Awal Audit** | `TT-MGT-FRS-026L` | `/admin/audit-sesi/{id}/tahap-awal` | `/auditor/audit-sesi/{id}/cetak-tahap-awal` |
| 2 | **Rencana Audit** | `TT-MGT-FRS-026A` | `/admin/audit-sesi/{id}/rencana` | `/auditor/audit-sesi/{id}/rencana` |
| 3 | **Matriks Penilaian** | Kepdirjen 185 Lamp. II | `/admin/audit-sesi/{id}/matrix` | `/auditor/audit-sesi/{id}/rekap` |
| 4 | **Formulir Kesesuaian** | `TT-MGT-FRS-026E` | `/admin/audit-sesi/{id}/cetak-kesesuaian` | `/auditor/audit-sesi/{id}/cetak-kesesuaian` |
| 5 | **Ketidaksesuaian & PICA** | `TT-MGT-FRS-026M` | `/admin/pica` & `/admin/audit-sesi/{id}/cetak-ketidaksesuaian` | `/auditor/pica` & `/auditor/audit-sesi/{id}/cetak-ketidaksesuaian` |
| 6 | **Formulir OFI (Opsional)** | `TT-MGT-FRS-026G` | `/admin/audit-sesi/{id}/ofi` | `/auditor/audit-sesi/{id}/ofi` |
| 7 | **Rekap & Laporan Lengkap** | Laporan Audit SMKP | `/admin/audit-sesi/{id}/rekap` & `/cetak-lengkap` | `/auditor/audit-sesi/{id}/rekap` & `/cetak-lengkap` |

---

## 2. Sumber Aturan per Komponen

| Komponen | Aturan di Aplikasi | Sumber |
|---|---|---|
| Perhitungan Hari Kerja Audit | Tabel acuan rentang pekerja (`master_hari_kerja_audit`) + faktor penambah/pengurang (`master_faktor_penyesuaian`) via `HariKerjaAuditService`. Impor CSV 2 langkah (pratinjau → konfirmasi) dengan `is_verified = false`. | Kepdirjen 185 Lampiran II (struktur) & kebijakan anti-angka-karangan (impor CSV terverifikasi). |
| Klasifikasi Jenis Usaha | Kolom `perusahaans.jenis_usaha` (`tambang`, `pengolahan_pemurnian`, `jasa_pertambangan_inti`, `jasa_konsultasi_perencanaan`, `jasa_non_inti`) + saran `kelasRisikoDefault()` & `kelompokPenandatangan()`. | Materi pelatihan (slide 8 & slide 37). |
| Rencana Audit (026A) | Tepat 1 `lead_auditor` saat finalisasi; hari pertama memuat `rapat_pembukaan`, hari terakhir memuat `rapat_penutupan`; tanggal dalam rentang sesi; `jam_selesai > jam_mulai`. | Materi pelatihan & praktik audit SMKP. |
| Matriks Metode & Sampel | Jika metode/teknik dipilih maka `deskripsi_sampel` wajib & `jumlah_sampel >= 1`; rumus Slovin `ceil(N / (1 + N * e^2))` dengan `0 < e < 1` tanpa nilai bawaan `e`. | Materi pelatihan (slide 29) & rumus Slovin baku. |
| Temuan PLOR & Penomoran | `nomor_urut` unik per sesi (tidak pernah dipakai ulang meski dihapus via `SoftDeletes`), 4 field PLOR (`Problem`, `Location`, `Objective Evidence`, `Reference`), persetujuan Lead Auditor & Wakil Auditi. | Materi pelatihan & kaidah PLOR SMKP. |
| Rekapitulasi & Penilaian | Memakai `AuditSesi::getTemuanPerKategori()`, `getRekapPerElemen()`, `hitungSkorAkhir()`, dan `hitungKategoriMayorPath1()` tanpa mengubah logika yang sudah ada. | Kepdirjen 185 Lampiran II (kode eksisting). |
| Formulir OFI (026G) | Opsional (tidak memblokir finalisasi), nomor urut unik per sesi yang tidak dipakai ulang, CRUD untuk `admin`/`auditor_smkp`, read-only scoped untuk `auditor`. | Materi pelatihan. |
| Formulir Kesesuaian (026E) | Sub-elemen dengan 100% skor maksimal pada seluruh kriteria aktif non-N/A (`getSubElemenPraktekTerbaik()`), peringatan bila tanggal tanda tangan < `tanggal_selesai` sesi. | Kepdirjen 185 & materi pelatihan. |

---

## 3. Daftar Asumsi yang Memerlukan Konfirmasi / Verifikasi Admin

1. **Tabel Hari Kerja Audit (`master_hari_kerja_audit`)**: Tidak ada angka bawaan yang ditanam. Admin wajib mengisi/mengimpor CSV berdasarkan dokumen resmi Kepdirjen 185 Lampiran II menggunakan template `database/data/hari_kerja_audit.template.csv`, lalu memverifikasi setiap baris (`is_verified = true`).
2. **Nilai Faktor Penyesuaian (`master_faktor_penyesuaian`)**: Seeder `MasterFaktorPenyesuaianSeeder` hanya menanam 7 definisi dengan `nilai_hari = null`. Admin wajib mengisi `nilai_hari` sebelum faktor dapat dipilih pada kalkulator.
3. **Pemetaan `jenis_usaha` → Kelas Risiko**: `tambang`, `pengolahan_pemurnian`, `jasa_pertambangan_inti` → `Tinggi`; `jasa_konsultasi_perencanaan` → `Menengah`; `jasa_non_inti` → `Rendah` (bersifat saran default, dapat ditimpa manual di Form I.B).
4. **Matriks Penandatangan Rencana Audit (`master_penandatangan_rencana`)**: Disalin dari slide 37 materi pelatihan (`jasa_pertambangan`: Ketua Tim, PJO, KTT atau PTL, KaIT; `tambang`: Ketua Tim, KTT, KaIT; `pengolahan_pemurnian`: Ketua Tim, PTL, KaIT). Dapat disesuaikan pada tab Pengesahan.
5. **Kewajiban PLOR saat Finalisasi**: Default `config('audit.plor_wajib_saat_finalisasi') = false`. Dapat diaktifkan dengan `AUDIT_PLOR_WAJIB_SAAT_FINALISASI=true` pada environment bila perusahaan ingin mewajibkan PLOR sebelum finalisasi sesi.

---

## 4. Panduan Rollback per Fase

- **Rollback Fase 4**: `git revert <commit_fase_4>` (tidak ada migrasi DB pada Fase 4).
- **Rollback Fase 3**: `php artisan migrate:rollback --step=1` (menghapus `audit_ofis` dan kolom baru di `picas`), lalu `git revert <commit_fase_3>`.
- **Rollback Fase 2**: `php artisan migrate:rollback --step=1` (menghapus 6 tabel rencana audit), lalu `git revert <commit_fase_2>`.
- **Rollback Fase 1**: `php artisan migrate:rollback --step=1` (menghapus `master_hari_kerja_audit`, `master_faktor_penyesuaian`, `perusahaans.jenis_usaha`, `audit_tahap_awals.sumber_perhitungan`), lalu `git revert <commit_fase_1>`.
