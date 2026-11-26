<?php

namespace App\Services;

class LaporanTemplateRenderer
{
    /**
     * Daftar putih placeholder yang diizinkan pada template narasi laporan.
     * Tidak menggunakan Blade dan tidak menggunakan eval.
     *
     * @var array<string, string>
     */
    public const ALLOWED_PLACEHOLDERS = [
        'nama_perusahaan'        => 'Nama perusahaan auditi',
        'tahun_periode'          => 'Tahun periode audit',
        'tanggal_mulai'          => 'Tanggal mulai pelaksanaan audit',
        'tanggal_selesai'        => 'Tanggal selesai pelaksanaan audit',
        'nomor_laporan'          => 'Nomor laporan audit',
        'tanggal_laporan'        => 'Tanggal penerbitan laporan',
        'tempat_penerbitan'      => 'Tempat penerbitan laporan',
        'nama_ktt'               => 'Nama KTT / PTL / PJO',
        'jenis_komoditas'        => 'Jenis komoditas perusahaan',
        'skor_akhir'             => 'Persentase skor akhir pencapaian SMKP',
        'jumlah_temuan_kritikal' => 'Jumlah temuan kategori Kritikal',
        'jumlah_temuan_mayor'    => 'Jumlah temuan kategori Mayor',
        'jumlah_temuan_minor'    => 'Jumlah temuan kategori Minor',
        'total_temuan'           => 'Total seluruh temuan ketidaksesuaian',
        'daftar_dasar_hukum'     => 'Daftar dasar hukum aktif',
        'nama_lead_auditor'      => 'Nama Ketua Tim (Lead Auditor)',
    ];

    /**
     * Render teks template dengan mengganti HANYA placeholder yang ada di daftar putih.
     * Seluruh output template dan nilai placeholder di-escape dengan e() agar aman terhadap XSS.
     *
     * @param string $template
     * @param array<string, scalar|null> $context
     * @return string HTML-safe escaped text (newline preserved as \n; view dapat menggunakan nl2br jika diperlukan)
     */
    public function renderEscaped(string $template, array $context): string
    {
        $escapedTemplate = e($template);

        return (string) preg_replace_callback('/\{\{\s*([a-z0-9_]+)\s*\}\}/i', function (array $matches) use ($context): string {
            $key = strtolower($matches[1]);
            if (! array_key_exists($key, self::ALLOWED_PLACEHOLDERS)) {
                // Placeholder di luar daftar putih dibiarkan apa adanya (sudah di-escape), tidak dieksekusi
                return $matches[0];
            }

            $rawValue = $context[$key] ?? '-';
            if ($rawValue === null || $rawValue === '') {
                $rawValue = '-';
            }

            return e((string) $rawValue);
        }, $escapedTemplate);
    }

    /**
     * Render plain text (tanpa HTML escaping, misalnya untuk penyimpanan JSON snapshot kanonik
     * yang nantinya selalu di-escape dengan e() saat ditampilkan di view).
     *
     * @param string $template
     * @param array<string, scalar|null> $context
     * @return string
     */
    public function renderPlain(string $template, array $context): string
    {
        return (string) preg_replace_callback('/\{\{\s*([a-z0-9_]+)\s*\}\}/i', function (array $matches) use ($context): string {
            $key = strtolower($matches[1]);
            if (! array_key_exists($key, self::ALLOWED_PLACEHOLDERS)) {
                return $matches[0];
            }

            $rawValue = $context[$key] ?? '-';
            if ($rawValue === null || $rawValue === '') {
                $rawValue = '-';
            }

            return (string) $rawValue;
        }, $template);
    }
}
