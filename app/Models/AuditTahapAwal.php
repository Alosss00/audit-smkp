<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditTahapAwal extends Model
{
    use HasFactory;

    protected $table = 'audit_tahap_awals';

    protected $fillable = [
        'audit_sesi_id',
        'no_dokumen_header',
        'no_dokumen_footer',
        'no_revisi',
        'tanggal_terbit',
        'tanggal_tinjau_ulang',
        'nama_perusahaan_auditi',
        'perusahaan_induk',
        'jenis_perizinan',
        'jenis_komoditas',
        'alamat_kantor_site',
        'alamat_lokasi_tambang',
        'alamat_kantor_pusat',
        'periode_audit_label',
        'kinerja_kp',
        'faktor_penyesuaian',
        'jumlah_pekerja',
        'kelas_risiko',
        'mandays_awal',
        'faktor_penyesuaian_hari',
        'total_mandays',
        'alokasi_tahap_1',
        'alokasi_tahap_2',
        'sumber_perhitungan',
        'no_surat_tugas',
        'tanggal_surat_tugas',
        'tanggal_surat_tugas_tahap_2',
        'tim_audit_tahap_1',
        'kontak_awal',
        'kelayakan_audit',
        'kesimpulan_kelayakan',
        'kecukupan_dokumen',
        'kesimpulan_kecukupan',
        'tim_audit_tahap_2',
        'top_risks_present',
        'top_risks_future',
        'trend_data',
        'unjuk_kerja_peralatan',
        'capaian_kli',
        'data_audit_sebelumnya',
        'harapan_pemangku',
        'tanggal_berita_acara',
        'ketua_tim_audit',
        'nama_pjo',
        'nama_ktt',
    ];

    protected $casts = [
        'tanggal_terbit'              => 'date',
        'tanggal_tinjau_ulang'        => 'date',
        'tanggal_surat_tugas'         => 'date',
        'tanggal_surat_tugas_tahap_2' => 'date',
        'tanggal_berita_acara'        => 'date',
        'kinerja_kp'            => 'array',
        'faktor_penyesuaian'    => 'array',
        'tim_audit_tahap_1'     => 'array',
        'kontak_awal'           => 'array',
        'kelayakan_audit'       => 'array',
        'kecukupan_dokumen'     => 'array',
        'tim_audit_tahap_2'     => 'array',
        'top_risks_present'     => 'array',
        'top_risks_future'      => 'array',
        'trend_data'            => 'array',
        'unjuk_kerja_peralatan' => 'array',
        'capaian_kli'           => 'array',
        'data_audit_sebelumnya' => 'array',
        'harapan_pemangku'      => 'array',
        'jumlah_pekerja'        => 'integer',
        'mandays_awal'          => 'decimal:1',
        'faktor_penyesuaian_hari' => 'decimal:1',
        'total_mandays'         => 'decimal:1',
        'alokasi_tahap_1'       => 'decimal:1',
    ];

    /**
     * Get the attributes that should be cast (Laravel 11/12 standard).
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return $this->casts;
    }

    public function auditSesi(): BelongsTo
    {
        return $this->belongsTo(AuditSesi::class, 'audit_sesi_id');
    }

    /**
     * Get the 7 standard adjustment factor condition descriptions (Kepdirjen 185).
     */
    public static function getDaftarKondisiPenyesuaian(): array
    {
        return [
            1 => 'Jarak antar objek audit yang saling berjauhan, dengan waktu tempuh ≥ 4 jam',
            2 => 'Perusahaan pertambangan menggunakan lebih dari satu metode penambangan',
            3 => 'Perusahaan pertambangan memiliki fasilitas pengolahan dan pemurnian',
            4 => 'Tingkat keparahan (severity rate) dan tingkat kekerapan (frequency rate) kecelakaan perusahaan tahun terakhir lebih tinggi dari tingkat rata-rata nasional*',
            5 => 'Tingkat keparahan penyakit berdasarkan absensi (absence severity rate) dan tingkat kekerapan kesakitan (morbidity frequency rate) perusahaan tahun terakhir lebih tinggi dari tingkat rata-rata nasional',
            6 => 'Terjadi kejadian berbahaya serupa dan berulang dalam satu tahun terakhir; dan/atau',
            7 => 'Terjadi kejadian akibat penyakit tenaga kerja, dan/atau penyakit akibat kerja dalam satu tahun terakhir.',
        ];
    }

    /**
     * Get the 7 standard audit feasibility indicators.
     */
    public static function getDaftarIndikatorKelayakan(): array
    {
        return [
            1 => 'Informasi untuk Pengembangan Program Audit: Profil Organisasi',
            2 => 'Informasi untuk Pengembangan Program Audit: Profil Risiko',
            3 => 'Informasi untuk Pengembangan Program Audit: Data Kinerja Keselamatan Pertambangan pada Periode Audit',
            4 => 'Kerjasama dari Auditi',
            5 => 'Ketersediaan Waktu',
            6 => 'Ketersediaan Sumberdaya lainnya',
            7 => 'Persyaratan Keamanan dan Keselamatan',
        ];
    }

    /**
     * Get the 7 SMKP elements for document adequacy review.
     */
    public static function getDaftarElemenDokumentasi(): array
    {
        return [
            1 => 'Elemen I Kebijakan',
            2 => 'Elemen II Perencanaan',
            3 => 'Elemen III Organisasi dan Personel',
            4 => 'Elemen IV Implementasi',
            5 => 'Elemen V Pemantauan, Evaluasi, dan Tindak Lanjut',
            6 => 'Elemen VI Dokumentasi',
            7 => 'Elemen VII Tinjauan Manajemen dan Peningkatan Kinerja',
        ];
    }

    /**
     * Build empty attributes for a newly created AuditSesi so all form fields start blank.
     */
    public static function buildEmptyAttributes(AuditSesi $sesi): array
    {
        $perusahaan = $sesi->perusahaan;
        if (!$perusahaan && $sesi->perusahaan_id) {
            $perusahaan = Perusahaan::find($sesi->perusahaan_id);
        }
        $tahun = $sesi->tahun_periode ?? ($sesi->tanggal_mulai ? $sesi->tanggal_mulai->format('Y') : date('Y'));
        $periodeLabel = $tahun ? "1 Januari {$tahun} s/d 31 Desember {$tahun}" : null;

        return [
            'audit_sesi_id'           => $sesi->id,
            'no_dokumen_header'       => null,
            'no_dokumen_footer'       => null,
            'no_revisi'               => null,
            'tanggal_terbit'          => null,
            'tanggal_tinjau_ulang'    => null,
            'nama_perusahaan_auditi'  => $perusahaan?->nama_perusahaan ?? $sesi->area_audit,
            'perusahaan_induk'        => null,
            'jenis_perizinan'         => $perusahaan?->jenis_perizinan,
            'jenis_komoditas'         => $perusahaan?->jenis_komoditas,
            'alamat_kantor_site'      => $perusahaan?->alamat_kantor_site,
            'alamat_lokasi_tambang'   => $perusahaan?->alamat_lokasi_tambang,
            'alamat_kantor_pusat'     => $perusahaan?->alamat_kantor_pusat,
            'periode_audit_label'     => $periodeLabel,
            'kinerja_kp'              => [],
            'faktor_penyesuaian'      => [],
            'jumlah_pekerja'          => null,
            'kelas_risiko'            => null,
            'mandays_awal'            => null,
            'faktor_penyesuaian_hari' => null,
            'total_mandays'           => null,
            'alokasi_tahap_1'         => null,
            'alokasi_tahap_2'         => null,
            'sumber_perhitungan'      => null,
            'no_surat_tugas'          => null,
            'tanggal_surat_tugas'     => null,
            'tanggal_surat_tugas_tahap_2' => null,
            'tim_audit_tahap_1'       => [],
            'kontak_awal'             => [],
            'kelayakan_audit'         => [],
            'kesimpulan_kelayakan'    => null,
            'kecukupan_dokumen'       => [],
            'kesimpulan_kecukupan'    => null,
            'tim_audit_tahap_2'       => [],
            'top_risks_present'       => [],
            'top_risks_future'        => [],
            'trend_data'              => [],
            'unjuk_kerja_peralatan'   => [],
            'capaian_kli'             => [],
            'data_audit_sebelumnya'   => [],
            'harapan_pemangku'        => [],
            'tanggal_berita_acara'    => null,
            'ketua_tim_audit'         => null,
            'nama_pjo'                => null,
            'nama_ktt'                => null,
        ];
    }

    /**
     * Create an empty AuditTahapAwal record specifically when a new AuditSesi is created.
     */
    public static function createEmptyForSesi(AuditSesi $sesi): self
    {
        self::ensureTableExists();

        return self::updateOrCreate(
            ['audit_sesi_id' => $sesi->id],
            self::buildEmptyAttributes($sesi)
        );
    }

    /**
     * Build default/preset attributes for existing AuditSesi records (matching Tahap Awal Audit_PT MSM-2025.pdf).
     */
    public static function buildDefaultAttributes(AuditSesi $sesi): array
    {
        $perusahaan = $sesi->perusahaan;
        if (!$perusahaan && $sesi->area_audit) {
            $perusahaan = Perusahaan::where('nama_perusahaan', $sesi->area_audit)->first();
        }

        $namaPerusahaan = $perusahaan ? $perusahaan->nama_perusahaan : $sesi->area_audit;
        $tahunPeriode = (int) ($sesi->tahun_periode ?? ($sesi->tanggal_mulai ? $sesi->tanggal_mulai->format('Y') : 2025));
        $tahunSebelumnya = $tahunPeriode - 1;
        $tahunDepan = $tahunPeriode + 1;

        $isTtn = str_contains(strtolower($namaPerusahaan), 'tondano') || str_contains(strtolower($namaPerusahaan), 'ttn');
        $kodeSingkat = $isTtn ? 'TTN' : 'MSM';

        $prevSesi = AuditSesi::where('id', '!=', $sesi->id)
            ->where(function ($q) use ($sesi) {
                if ($sesi->perusahaan_id) {
                    $q->where('perusahaan_id', $sesi->perusahaan_id);
                } else {
                    $q->where('area_audit', $sesi->area_audit);
                }
            })
            ->where('tahun_periode', $tahunSebelumnya)
            ->latest()
            ->first();

        $nilaiAuditPrev = '79.50 %';
        if ($prevSesi) {
            $skorPrev = $prevSesi->hitungSkorAkhir();
            if ($skorPrev > 0) {
                $nilaiAuditPrev = number_format($skorPrev, 2) . ' %';
            }
        }

        $timTahap1 = [
            ['nama' => 'Fiska Polii', 'jabatan' => 'Ketua Tim Audit', 'no_registrasi' => '401/AUD-SMKP/37.04/DBT/2021'],
            ['nama' => 'Pingkan Mandang', 'jabatan' => 'Auditor', 'no_registrasi' => '397/AUD-SMKP/37.04/DBT/2021'],
            ['nama' => 'Veriman Rongko', 'jabatan' => 'Auditor', 'no_registrasi' => '046/AUD-SMKP/37.04/DBT/2023'],
            ['nama' => 'Ronald Timbuleng', 'jabatan' => 'Auditor', 'no_registrasi' => '049/AUD-SMKP/37.04/DBT/2023'],
            ['nama' => 'Jenry Tolu', 'jabatan' => 'Auditor', 'no_registrasi' => '047/AUD-SMKP/37.04/DBT/2023'],
        ];

        $timTahap2 = [
            ['nama' => 'Fiska Polii', 'jabatan' => 'Ketua Tim Audit', 'no_registrasi' => '401/AUD-SMKP/37.04/DBT/2021'],
            ['nama' => 'Veriman Rongko', 'jabatan' => 'Auditor', 'no_registrasi' => '046/AUD-SMKP/37.04/DBT/2023'],
            ['nama' => 'Pingkan Mandang', 'jabatan' => 'Auditor', 'no_registrasi' => '397/AUD-SMKP/37.04/DBT/2021'],
            ['nama' => 'Jenry Tolu', 'jabatan' => 'Auditor', 'no_registrasi' => '047/AUD-SMKP/37.04/DBT/2023'],
            ['nama' => 'Ronald Timbuleng', 'jabatan' => 'Auditor', 'no_registrasi' => '049/AUD-SMKP/37.04/DBT/2023'],
        ];

        return [
            'audit_sesi_id'          => $sesi->id,
            'no_dokumen_header'      => 'TT-MGT-FRS-026L',
            'no_dokumen_footer'      => 'TT-MGT-FRS-026K',
            'no_revisi'              => '01',
            'tanggal_terbit'         => '2025-05-07',
            'tanggal_tinjau_ulang'   => '2028-05-07',
            'nama_perusahaan_auditi' => $namaPerusahaan,
            'jenis_perizinan'        => ($perusahaan && $perusahaan->jenis_perizinan)
                ? $perusahaan->jenis_perizinan
                : ($isTtn ? 'B-143/Pres/3/1997' : "B-43/Pres/11/1986\n323.K/30/DJB/2015"),
            'jenis_komoditas'        => ($perusahaan && $perusahaan->jenis_komoditas)
                ? $perusahaan->jenis_komoditas
                : 'Emas DMP',
            'alamat_kantor_site'     => ($perusahaan && $perusahaan->alamat_kantor_site)
                ? $perusahaan->alamat_kantor_site
                : "Toka Tindung Gold Project Site Via. Ds. Winuri\nLikupang Timur.",
            'alamat_lokasi_tambang'  => ($perusahaan && $perusahaan->alamat_lokasi_tambang)
                ? $perusahaan->alamat_lokasi_tambang
                : 'Kab Minahasa Utara dan Kota Bitung.',
            'alamat_kantor_pusat'    => ($perusahaan && $perusahaan->alamat_kantor_pusat)
                ? $perusahaan->alamat_kantor_pusat
                : "Rajawali Place Lt. 27, Jalan Hj. Rangkayo\nRasuna Said Kav B/4 Setiabudi\nJakarta Selatan 12910",
            'periode_audit_label'    => "1 Januari {$tahunPeriode} s/d 31 Desember {$tahunPeriode}",

            'kinerja_kp' => [
                'cidera_ringan'      => '0',
                'cidera_berat'       => '0',
                'mati'               => '2',
                'fr'                 => '0.33',
                'sr'                 => '1,973.19',
                'kejadian_berbahaya' => '1',
                'asr'                => '16.97',
                'mfr'                => '16.97',
                'penyakit_tk'        => '0',
                'frekuensi_pak'      => '0',
            ],

            'faktor_penyesuaian' => [
                1 => 'tidak',
                2 => 'tidak',
                3 => 'ya',
                4 => 'ya',
                5 => 'tidak',
                6 => 'ya',
                7 => 'tidak',
            ],

            'jumlah_pekerja'          => 3086,
            'kelas_risiko'            => 'Tinggi',
            'mandays_awal'            => 25.0,
            'faktor_penyesuaian_hari' => 2.5,
            'total_mandays'           => 27.5,
            'alokasi_tahap_1'         => 3.0,
            'alokasi_tahap_2'         => '24,5 Hari ( 4.9 Hari, 5 Auditor)',

            'no_surat_tugas'          => "0074/{$kodeSingkat}/9/2024/I",
            'tanggal_surat_tugas'     => '2024-09-25',
            'tim_audit_tahap_1'       => $timTahap1,

            'kontak_awal' => [
                'hari_tanggal'       => "Jumat s/d Minggu, 2- 4 Januari {$tahunDepan}",
                'media'              => 'Meeting',
                'perwakilan_auditi'  => ($perusahaan && $perusahaan->nama_ktt_default) ? $perusahaan->nama_ktt_default : ($isTtn ? 'Agung Praptono' : 'Tejo Prihantoro'),
                'jabatan_perwakilan' => 'Kepala Teknik Tambang',
            ],

            'kelayakan_audit' => [
                1 => 'Tersedia dan Layak',
                2 => 'Tersedia dan Layak',
                3 => 'Tersedia dan Layak',
                4 => 'Baik',
                5 => 'Tersedia',
                6 => 'Cukup',
                7 => 'Baik',
            ],
            'kesimpulan_kelayakan' => 'layak',

            'kecukupan_dokumen' => [
                1 => 'Baik',
                2 => 'Cukup',
                3 => 'Baik',
                4 => 'Baik',
                5 => 'Baik',
                6 => 'Baik',
                7 => 'Baik',
            ],
            'kesimpulan_kecukupan' => 'dapat_dilanjutkan',

            'tim_audit_tahap_2' => $timTahap2,

            'top_risks_present' => [
                [
                    'kegiatan'   => 'Pengoperasian Peralatan Bergerak di Tambang (Heavy Duty Truck, Water Truck, Fuel truck, Service Truck , Grader, Dozer, Loader, Lowboy, Flatbad truck, MMU truck and light vehicle',
                    'risiko'     => 'Kematian/Fatality/ Permanen disbility',
                    'nilai_awal' => 'Extreme',
                    'nilai_sisa' => 'Tinggi',
                ],
                [
                    'kegiatan'   => 'Penggalian, Pengangkutan dan Penimbunan material (ore/waste)',
                    'risiko'     => 'Kematian/Fatality/ Permanen disbility',
                    'nilai_awal' => 'Extreme',
                    'nilai_sisa' => 'Tinggi',
                ],
                [
                    'kegiatan'   => 'Kegiatan Peledakan (Blasting)',
                    'risiko'     => 'Kematian/Fatality/ Permanen disbility',
                    'nilai_awal' => 'Extreme',
                    'nilai_sisa' => 'Sedang',
                ],
                [
                    'kegiatan'   => 'Perawatan dan Pengoprasian Pompa beserta jalur air panas',
                    'risiko'     => 'Kematian/Fatality/ Permanen disbility',
                    'nilai_awal' => 'Extreme',
                    'nilai_sisa' => 'Rendah',
                ],
                [
                    'kegiatan'   => 'Bekerja di atas Ketinggian',
                    'risiko'     => 'Kematian/Fatality/ Permanen disbility',
                    'nilai_awal' => 'Tinggi',
                    'nilai_sisa' => 'Sedang',
                ],
                [
                    'kegiatan'   => 'Pekerjaan listrik (Pemasangan instalasi/electrical PM check, Commisioning)',
                    'risiko'     => 'Kematian/Fatality/ Permanen disbility',
                    'nilai_awal' => 'Tinggi',
                    'nilai_sisa' => 'Rendah',
                ],
                [
                    'kegiatan'   => 'Pekerjaan Pengakatan Beban dan rigging',
                    'risiko'     => 'Kematian/Fatality/ Permanen disbility',
                    'nilai_awal' => 'Tinggi',
                    'nilai_sisa' => 'Sedang',
                ],
                [
                    'kegiatan'   => 'Bekerja di dalam ruang terbatas',
                    'risiko'     => 'Kematian/Fatality/ Permanen disbility',
                    'nilai_awal' => 'Tinggi',
                    'nilai_sisa' => 'Rendah',
                ],
                [
                    'kegiatan'   => 'Pengoperasian Pabrik Pengolahan (Crushing, Milling, Leaching, Mixing dan Smelting)',
                    'risiko'     => 'Kematian/Fatality/ Permanen disbility',
                    'nilai_awal' => 'Tinggi',
                    'nilai_sisa' => 'Rendah',
                ],
                [
                    'kegiatan'   => 'Penanganan Bahan Berbahaya Beracun',
                    'risiko'     => 'Kematian/Fatality/ Permanen disbility',
                    'nilai_awal' => 'Tinggi',
                    'nilai_sisa' => 'Rendah',
                ],
            ],

            'top_risks_future' => [
                [
                    'kegiatan'   => 'Pengoperasian Peralatan Bergerak di Tambang (Heavy Duty Truck, Water Truck, Fuel truck, Service Truck , Grader, Dozer, Loader, Lowboy, Flatbad truck, MMU truck and light vehicle',
                    'risiko'     => 'Kematian/Fatality/ Permanen disbility',
                    'nilai_awal' => 'Extreme',
                    'nilai_sisa' => 'Tinggi',
                ],
                [
                    'kegiatan'   => 'Penggalian dan Penimbunan material (ore/waste)',
                    'risiko'     => 'Kematian/Fatality/ Permanen disbility',
                    'nilai_awal' => 'Extreme',
                    'nilai_sisa' => 'Tinggi',
                ],
                [
                    'kegiatan'   => 'Kegiatan Peledakan (Blasting)',
                    'risiko'     => 'Kematian/Fatality/ Permanen disbility',
                    'nilai_awal' => 'Extreme',
                    'nilai_sisa' => 'Rendah',
                ],
                [
                    'kegiatan'   => 'Pengoprasian Pompa air panas dan maintenance',
                    'risiko'     => 'Kematian/Fatality/ Permanen disbility',
                    'nilai_awal' => 'Extreme',
                    'nilai_sisa' => 'Sedang',
                ],
                [
                    'kegiatan'   => 'Bekerja di atas Ketinggian',
                    'risiko'     => 'Kematian/Fatality/ Permanen disbility',
                    'nilai_awal' => 'Tinggi',
                    'nilai_sisa' => 'Tinggi',
                ],
                [
                    'kegiatan'   => 'Pekerjaan listrik (Pemasangan instalasi/electrical PM check, Commisioning)',
                    'risiko'     => 'Kematian/Fatality/ Permanen disbility',
                    'nilai_awal' => 'Tinggi',
                    'nilai_sisa' => 'Rendah',
                ],
                [
                    'kegiatan'   => 'Pekerjaan Pengakatan Beban dan rigging',
                    'risiko'     => 'Kematian/Fatality/ Permanen disbility',
                    'nilai_awal' => 'Tinggi',
                    'nilai_sisa' => 'Sedang',
                ],
                [
                    'kegiatan'   => 'Bekerja di dalam ruang terbatas',
                    'risiko'     => 'Kematian/Fatality/ Permanen disbility',
                    'nilai_awal' => 'Tinggi',
                    'nilai_sisa' => 'Rendah',
                ],
                [
                    'kegiatan'   => 'Pengeoprasian Crasher and Mill',
                    'risiko'     => 'Kematian/Fatality/ Permanen disbility',
                    'nilai_awal' => 'Tinggi',
                    'nilai_sisa' => 'Rendah',
                ],
                [
                    'kegiatan'   => 'Penanganan Bahan Berbahaya Beracun',
                    'risiko'     => 'Kematian/Fatality/ Permanen disbility',
                    'nilai_awal' => 'Tinggi',
                    'nilai_sisa' => 'Rendah',
                ],
            ],

            'trend_data' => [
                'faktor_penyebab' => [
                    'Identifikasi Bahaya',
                    'Pengawasan',
                    'Komunikasi',
                    'Motivasi',
                ],
                'lokasi' => [
                    'Pit',
                    'Workshop',
                    'TSF',
                ],
                'jenis_kegiatan' => [
                    'Aktifitas Loading, hauling dan damping',
                    'Lifting and Rigging',
                    'Pekerjaan Pemeliharaan Peralatan',
                ],
                'jenis_jabatan' => [
                    'Operator',
                    'Helper',
                    'Mekanik',
                ],
                'deviasi_inspeksi' => [
                    'Tatagraha',
                    'Perilaku Tidak Aman (Penggunaan APD)',
                    'Perilaku Tidak Aman (Penggunaan Alat Kerja yang tidak standard)',
                    'Kepatuhan Terhadap SWI dan JSEA',
                ],
                'faktor_pak' => [
                    'Tidak Ada',
                ],
            ],

            'unjuk_kerja_peralatan' => [
                ['jenis_peralatan' => 'Excavator Volvo EC750', 'pa' => '90', 'ma' => '87'],
                ['jenis_peralatan' => 'ADT Truck Volvo ADT A40F/A45G/A60H', 'pa' => '90', 'ma' => '87'],
                ['jenis_peralatan' => 'Dozer D9R & D8R', 'pa' => '90', 'ma' => '87'],
                ['jenis_peralatan' => 'Motor Grader, Compactor, Fuel Truck, Water Truck', 'pa' => '90', 'ma' => '87'],
                ['jenis_peralatan' => 'Excavator Hitachi EX1200', 'pa' => '85', 'ma' => '83'],
                ['jenis_peralatan' => 'Excavator Hitachi 870', 'pa' => '96', 'ma' => '95'],
                ['jenis_peralatan' => 'Haul Dump Truck Komatsu 785-7', 'pa' => '89', 'ma' => '85'],
                ['jenis_peralatan' => 'ADT Truck Volvo ADT A40F', 'pa' => '90', 'ma' => '87'],
                ['jenis_peralatan' => 'Dozer Komatsu D375 & D155', 'pa' => '54', 'ma' => '54'],
                ['jenis_peralatan' => 'Motor Grader, Fuel Truck, Water Truck', 'pa' => '91', 'ma' => '89'],
            ],

            'capaian_kli' => [
                ['indikator' => 'Pelaporan Bahaya', 'capaian' => '60 %'],
                ['indikator' => 'PTO', 'capaian' => '75%'],
                ['indikator' => 'WPI', 'capaian' => '100%'],
                ['indikator' => 'Pertemuan Komite KPLH', 'capaian' => '100%'],
            ],

            'data_audit_sebelumnya' => [
                'tahun'           => (string) $tahunSebelumnya,
                'nilai_audit'     => $nilaiAuditPrev,
                'status_kritikal' => 'Tidak ada temuan kritikal',
                'status_mayor'    => 'Tidak ada temuan Mayor',
                'status_minor'    => '98% temuan sudah ditindaklanjuti',
                'catatan_instansi' => [
                    'Tindaklanjut hasil audit belum diselesaikan sesuai dengan batas waktu penyelesaian',
                    'Perbaikan temuan ketidaksesuaian audit belum sesuai dengan kaidah PLOR.',
                    'Pembuatan tindakan koreksi dan rrot cause (akar maslah ketidaksesuaian)belum mempertimbangkan tindakan pencegahan.',
                ],
            ],

            'harapan_pemangku' => [
                'Hasil audit dapat memverifikasi capaian kinerja keselamatan pertambangan yang telah dilakukan sebelumnya',
                'Terlaksananya audit yang independen',
                'Mendapatkan hasil audit berdasarkan kondisi lapangan yang sesungguhnya',
                'Terlaksananya SMKP yang efektif sesuai dengan kondisi perusahaan',
                'SMKP dapat tersosialisasikan kepada seluruh pekerja tambang guna mendukung terciptanya budaya keselamatan pertambangan',
            ],

            'tanggal_berita_acara' => "{$tahunDepan}-01-04",
            'ketua_tim_audit'      => 'Fiska Polii',
            'nama_ktt'             => ($perusahaan && $perusahaan->nama_ktt_default) ? $perusahaan->nama_ktt_default : ($isTtn ? 'Agung Praptono' : 'Tejo Prihantoro'),
        ];
    }

    /**
     * Get or initialize AuditTahapAwal for an AuditSesi:
     * Always initializes with blank fields except Data Perusahaan Auditi.
     */
    public static function getOrCreateForSesi(AuditSesi $sesi): self
    {
        self::ensureTableExists();

        $record = self::where('audit_sesi_id', $sesi->id)->first();
        if (!$record) {
            $record = self::create(self::buildEmptyAttributes($sesi));
        }

        return $record;
    }

    /**
     * Safeguard to ensure the table exists even before manual CLI migration.
     */
    public static function ensureTableExists(): void
    {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('audit_tahap_awals')) {
                $migration = include base_path('database/migrations/2026_11_01_000001_create_audit_tahap_awals_table.php');
                $migration->up();
            }
        } catch (\Throwable $e) {
            // Ignore if DB connection is unavailable
        }
    }
}

