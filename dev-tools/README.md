# Development Tools & Diagnostic Scripts

Direktori ini berisi skrip utilitas diagnostik, inspeksi data pengujian, dan modul pengembangan fitur relasi kriteria SMKP.

## Peringatan Keamanan
1. **Khusus Lingkungan Lokal/CLI**: Seluruh skrip di direktori ini hanya boleh dijalankan melalui antarmuka baris perintah (CLI) di environment lokal/pengembangan.
2. **Tidak untuk Production**: Direktori ini dan seluruh isinya **DILARANG** di-deploy ke server production atau diekspos melalui web server (HTTP).
3. **Proteksi Akses**: Berkas `.htaccess` di direktori ini secara default memblokir seluruh permintaan HTTP (`Require all denied`). Selain itu, setiap skrip PHP dilengkapi pemeriksaan `php_sapi_name() === 'cli'` pada baris pertama.
