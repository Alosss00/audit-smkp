# Dokumentasi Proyek — SMKP Minerba Internal Audit System

Direktori ini (`/docs`) digunakan secara khusus untuk menyimpan seluruh dokumen resmi, modul presentasi, dan panduan operasional proyek **Sistem Informasi Audit Internal SMKP Minerba** (Kepdirjen ESDM No. 185.K/37.04/DJB/2019).

---

## 📂 Struktur Direktori

```text
docs/
├── pdf/
│   ├── user-manual-smkp-minerba.pdf               # Buku Panduan Pengguna Lengkap (9 Halaman, Golden Ratio)
│   └── security-overview-smkp-minerba-client.pdf  # Executive Briefing Keamanan Informasi untuk Klien (5 Halaman)
├── source/
│   ├── user_manual.html                           # Source Template HTML Panduan Pengguna (A4 Print-Ready)
│   └── security_doc.html                          # Source Template HTML Ringkasan Keamanan (A4 Print-Ready)
└── README.md                                      # Indeks & Informasi Dokumentasi
```

---

## 📄 Ringkasan Dokumen Tersedia

### 1. Buku Panduan Pengguna (*User Manual*)
- **File PDF:** [`docs/pdf/user-manual-smkp-minerba.pdf`](pdf/user-manual-smkp-minerba.pdf)
- **File Source:** [`docs/source/user_manual.html`](source/user_manual.html)
- **Deskripsi:** Panduan langkah-demi-langkah pengoperasian sistem untuk **Lead Auditor SMKP** dan **Auditee / PIC Area**, mencakup pembuatan sesi, penilaian 7 elemen, alur siklus PICA, pelaporan resmi ESDM (Form TT-MGT-FRS-026B), hingga troubleshooting.

### 2. Modul Presentasi Keamanan Klien (*Executive Security Overview*)
- **File PDF:** [`docs/pdf/security-overview-smkp-minerba-client.pdf`](pdf/security-overview-smkp-minerba-client.pdf)
- **File Source:** [`docs/source/security_doc.html`](source/security_doc.html)
- **Deskripsi:** Dokumen eksekutif siap presentasi yang merangkum arsitektur keamanan berlapis (*Defense-in-Depth*), tata kelola akses RBAC, jejak audit digital (*Audit Trail*), kepatuhan standar OWASP & regulasi ESDM 185.K, serta jaminan perlindungan data perusahaan.

---

## ⚙️ Cara Re-generate PDF dari Source HTML

Jika di kemudian hari terdapat pembaruan teks pada file HTML di `docs/source/`, file PDF dapat di-generate ulang dengan perintah:

```powershell
# Re-generate User Manual
cmd.exe /c '"C:\Program Files\Google\Chrome\Application\chrome.exe" --headless --disable-gpu --print-to-pdf="C:\laragon\www\audit\docs\pdf\user-manual-smkp-minerba.pdf" "C:\laragon\www\audit\docs\source\user_manual.html"'

# Re-generate Security Overview
cmd.exe /c '"C:\Program Files\Google\Chrome\Application\chrome.exe" --headless --disable-gpu --print-to-pdf="C:\laragon\www\audit\docs\pdf\security-overview-smkp-minerba-client.pdf" "C:\laragon\www\audit\docs\source\security_doc.html"'
```
