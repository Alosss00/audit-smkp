<?php

namespace Database\Seeders;

use App\Models\Elemen;
use App\Models\SubElemen;
use App\Models\Kriteria;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SMKPSeeder extends Seeder
{
    /**
     * Run database seeds for SMKP Minerba elements, sub-elements, and criteria
     * matching EXACTLY the official Kepdirjen 185 user provided text.
     * Total: 7 Elemen, 100% Bobot, Total Nilai Maksimal: 333 Poin.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Kriteria::truncate();
        SubElemen::truncate();
        Elemen::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Data Structure matching user input 100%
        $data = [
            [
                'kode_elemen' => 'I',
                'nama_elemen' => 'Kebijakan',
                'bobot'       => 10.00,
                'sub_elemens' => [
                    [
                        'kode_sub' => 'I.1',
                        'nama_sub' => 'Penyusunan Kebijakan',
                        'kriterias' => [
                            ['kode_kriteria' => 'I.1.1', 'deskripsi' => 'Penyusunan Kebijakan', 'nilai_maksimal' => 4.00],
                        ]
                    ],
                    [
                        'kode_sub' => 'I.2',
                        'nama_sub' => 'Isi Kebijakan',
                        'kriterias' => [
                            ['kode_kriteria' => 'I.2.1', 'deskripsi' => 'Isi Kebijakan', 'nilai_maksimal' => 4.00],
                        ]
                    ],
                    [
                        'kode_sub' => 'I.3',
                        'nama_sub' => 'Penetapan Kebijakan',
                        'kriterias' => [
                            ['kode_kriteria' => 'I.3.1', 'deskripsi' => 'Penetapan Kebijakan', 'nilai_maksimal' => 3.00],
                        ]
                    ],
                    [
                        'kode_sub' => 'I.4',
                        'nama_sub' => 'Komunikasi Kebijakan',
                        'kriterias' => [
                            ['kode_kriteria' => 'I.4.1', 'deskripsi' => 'Komunikasi Kebijakan', 'nilai_maksimal' => 4.00],
                        ]
                    ],
                    [
                        'kode_sub' => 'I.5',
                        'nama_sub' => 'Tinjauan Kebijakan',
                        'kriterias' => [
                            ['kode_kriteria' => 'I.5.1', 'deskripsi' => 'Tinjauan Kebijakan', 'nilai_maksimal' => 4.00],
                        ]
                    ],
                ]
            ],
            [
                'kode_elemen' => 'II',
                'nama_elemen' => 'Perencanaan',
                'bobot'       => 15.00,
                'sub_elemens' => [
                    [
                        'kode_sub' => 'II.1',
                        'nama_sub' => 'Penelaahan Awal',
                        'kriterias' => [
                            ['kode_kriteria' => 'II.1.1', 'deskripsi' => 'Penelaahan Awal', 'nilai_maksimal' => 4.00],
                        ]
                    ],
                    [
                        'kode_sub' => 'II.2',
                        'nama_sub' => 'Manajemen Risiko',
                        'kriterias' => [
                            ['kode_kriteria' => 'II.2.1', 'deskripsi' => 'Komunikasi dan konsultasi risiko', 'nilai_maksimal' => 4.00],
                            ['kode_kriteria' => 'II.2.2', 'deskripsi' => 'Penetapan konteks risiko', 'nilai_maksimal' => 3.00],
                            ['kode_kriteria' => 'II.2.3', 'deskripsi' => 'Identifikasi bahaya', 'nilai_maksimal' => 2.00],
                            ['kode_kriteria' => 'II.2.4', 'deskripsi' => 'Penilaian dan pengendalian risiko', 'nilai_maksimal' => 3.00],
                            ['kode_kriteria' => 'II.2.5', 'deskripsi' => 'Pemantauan dan peninjauan', 'nilai_maksimal' => 3.00],
                        ]
                    ],
                    [
                        'kode_sub' => 'II.3',
                        'nama_sub' => 'Identifikasi dan Kepatuhan Terhadap Ketentuan Peraturan Perundang-undangan dan Persyaratan Lainnya yang Terkait',
                        'kriterias' => [
                            ['kode_kriteria' => 'II.3.1', 'deskripsi' => 'Identifikasi dan Kepatuhan Terhadap Ketentuan Peraturan Perundang-undangan dan Persyaratan Lainnya yang Terkait', 'nilai_maksimal' => 3.00],
                        ]
                    ],
                    [
                        'kode_sub' => 'II.4',
                        'nama_sub' => 'Penetapan Tujuan, Sasaran, dan Program',
                        'kriterias' => [
                            ['kode_kriteria' => 'II.4.1', 'deskripsi' => 'Penetapan Tujuan, Sasaran, dan Program', 'nilai_maksimal' => 4.00],
                        ]
                    ],
                    [
                        'kode_sub' => 'II.5',
                        'nama_sub' => 'Rencana Kerja dan Anggaran Keselamatan Pertambangan',
                        'kriterias' => [
                            ['kode_kriteria' => 'II.5.1', 'deskripsi' => 'Rencana Kerja dan Anggaran Keselamatan Pertambangan', 'nilai_maksimal' => 3.00],
                        ]
                    ],
                ]
            ],
            [
                'kode_elemen' => 'III',
                'nama_elemen' => 'Organisasi dan Personel',
                'bobot'       => 17.00,
                'sub_elemens' => [
                    [
                        'kode_sub' => 'III.1',
                        'nama_sub' => 'Penyusunan dan Penetapan Struktur Organisasi, Tugas, Tanggung Jawab, dan Wewenang',
                        'kriterias' => [
                            ['kode_kriteria' => 'III.1.1', 'deskripsi' => 'Penyusunan dan Penetapan Struktur Organisasi, Tugas, Tanggung Jawab, dan Wewenang', 'nilai_maksimal' => 4.00],
                        ]
                    ],
                    [
                        'kode_sub' => 'III.2',
                        'nama_sub' => 'Penunjukan KTT, Kepala Tambang Bawah Tanah, dan/atau Kepala Kapal Keruk untuk Perusahaan Pertambangan',
                        'kriterias' => [
                            ['kode_kriteria' => 'III.2.1', 'deskripsi' => 'Penunjukan KTT', 'nilai_maksimal' => 0.00],
                            ['kode_kriteria' => 'III.2.2', 'deskripsi' => 'Penunjukan Kepala Tambang Bawah Tanah', 'nilai_maksimal' => 0.00],
                            ['kode_kriteria' => 'III.2.3', 'deskripsi' => 'Penunjukan Kepala Kapal Keruk', 'nilai_maksimal' => 0.00],
                        ]
                    ],
                    [
                        'kode_sub' => 'III.3',
                        'nama_sub' => 'Penunjukan PJO Untuk Perusahaan Jasa Pertambangan',
                        'kriterias' => [
                            ['kode_kriteria' => 'III.3.1', 'deskripsi' => 'Penunjukan PJO Untuk Perusahaan Jasa Pertambangan', 'nilai_maksimal' => 2.00],
                        ]
                    ],
                    [
                        'kode_sub' => 'III.4',
                        'nama_sub' => 'Pembentukan dan Penetapan Bagian K3 Pertambangan dan KO',
                        'kriterias' => [
                            ['kode_kriteria' => 'III.4.1', 'deskripsi' => 'Pembentukan dan Penetapan Bagian K3 Pertambangan dan KO', 'nilai_maksimal' => 4.00],
                        ]
                    ],
                    [
                        'kode_sub' => 'III.5',
                        'nama_sub' => 'Penunjukan Pengawas Operasional dan Pengawas Teknik',
                        'kriterias' => [
                            ['kode_kriteria' => 'III.5.1', 'deskripsi' => 'Penunjukan Pengawas Operasional dan Pengawas Teknik', 'nilai_maksimal' => 4.00],
                        ]
                    ],
                    [
                        'kode_sub' => 'III.6',
                        'nama_sub' => 'Penunjukan Tenaga Teknik Khusus Pertambangan',
                        'kriterias' => [
                            ['kode_kriteria' => 'III.6.1', 'deskripsi' => 'Penunjukan Tenaga Teknik Khusus Pertambangan', 'nilai_maksimal' => 4.00],
                        ]
                    ],
                    [
                        'kode_sub' => 'III.7',
                        'nama_sub' => 'Pembentukan dan Penetapan Komite Keselamatan Pertambangan',
                        'kriterias' => [
                            ['kode_kriteria' => 'III.7.1', 'deskripsi' => 'Pembentukan dan Penetapan Komite Keselamatan Pertambangan', 'nilai_maksimal' => 4.00],
                        ]
                    ],
                    [
                        'kode_sub' => 'III.8',
                        'nama_sub' => 'Penunjukan Tim Tanggap Darurat',
                        'kriterias' => [
                            ['kode_kriteria' => 'III.8.1', 'deskripsi' => 'Penunjukan Tim Tanggap Darurat', 'nilai_maksimal' => 4.00],
                        ]
                    ],
                    [
                        'kode_sub' => 'III.9',
                        'nama_sub' => 'Seleksi dan Penempatan Personel',
                        'kriterias' => [
                            ['kode_kriteria' => 'III.9.1', 'deskripsi' => 'Seleksi dan Penempatan Personel', 'nilai_maksimal' => 4.00],
                        ]
                    ],
                    [
                        'kode_sub' => 'III.10',
                        'nama_sub' => 'Penyelenggaraan dan Pelaksanaan Pendidikan dan Pelatihan Serta Kompetensi Kerja',
                        'kriterias' => [
                            ['kode_kriteria' => 'III.10.1', 'deskripsi' => 'Pendidikan dan pelatihan pekerja tambang', 'nilai_maksimal' => 4.00],
                            ['kode_kriteria' => 'III.10.2', 'deskripsi' => 'Kompetensi Kerja', 'nilai_maksimal' => 4.00],
                        ]
                    ],
                    [
                        'kode_sub' => 'III.11',
                        'nama_sub' => 'Penyusunan, Penetapan, dan Penerapan Komunikasi Keselamatan',
                        'kriterias' => [
                            ['kode_kriteria' => 'III.11.1', 'deskripsi' => 'Penyusunan, Penetapan, dan Penerapan Komunikasi Keselamatan', 'nilai_maksimal' => 4.00],
                        ]
                    ],
                    [
                        'kode_sub' => 'III.12',
                        'nama_sub' => 'Pengelolaan Administrasi Keselamatan Pertambangan',
                        'kriterias' => [
                            ['kode_kriteria' => 'III.12.1', 'deskripsi' => 'Buku tambang', 'nilai_maksimal' => 4.00],
                            ['kode_kriteria' => 'III.12.2', 'deskripsi' => 'Buku daftar kecelakaan tambang', 'nilai_maksimal' => 3.00],
                            ['kode_kriteria' => 'III.12.3', 'deskripsi' => 'Pelaporan pengelolaan Keselamatan Pertambangan', 'nilai_maksimal' => 3.00],
                            ['kode_kriteria' => 'III.12.4', 'deskripsi' => 'Dokumentasi Kejadian Berbahaya, kejadian akibat penyakit tenaga kerja dan penyakit akibat kerja', 'nilai_maksimal' => 3.00],
                            ['kode_kriteria' => 'III.12.5', 'deskripsi' => 'Dokumen dan Laporan Pemenuhan Kompetensi dan Persyaratan Lainnya', 'nilai_maksimal' => 3.00],
                        ]
                    ],
                    [
                        'kode_sub' => 'III.13',
                        'nama_sub' => 'Penyusunan, Penerapan, dan Pendokumentasian Prosedur Partisipasi, Konsultasi, Motivasi, dan Kesadaran Penerapan SMKP Minerba',
                        'kriterias' => [
                            ['kode_kriteria' => 'III.13.1', 'deskripsi' => 'Penyusunan, Penerapan, dan Pendokumentasian Prosedur Partisipasi, Konsultasi, Motivasi, dan Kesadaran Penerapan SMKP Minerba', 'nilai_maksimal' => 4.00],
                        ]
                    ],
                ]
            ],
            [
                'kode_elemen' => 'IV',
                'nama_elemen' => 'Implementasi',
                'bobot'       => 35.00,
                'sub_elemens' => [
                    [
                        'kode_sub' => 'IV.1',
                        'nama_sub' => 'Pelaksanaan Pengelolaan Operasional',
                        'kriterias' => [
                            ['kode_kriteria' => 'IV.1.1', 'deskripsi' => 'Penyusunan, Penetapan, Penerapan, Pendokumentasian, dan Evaluasi Prosedur Operasi / Kerja', 'nilai_maksimal' => 4.00],
                            ['kode_kriteria' => 'IV.1.2', 'deskripsi' => 'Penyusunan, Penetapan, Penerapan, Pendokumentasian, dan Evaluasi Izin kerja khusus', 'nilai_maksimal' => 3.00],
                            ['kode_kriteria' => 'IV.1.3', 'deskripsi' => 'Penyusunan, Penetapan, Penerapan, Pendokumentasian, dan Evaluasi Prosedur Operasi / Kerja Alat pelindung diri dan alat keselamatan', 'nilai_maksimal' => 4.00],
                        ]
                    ],
                    [
                        'kode_sub' => 'IV.2',
                        'nama_sub' => 'Pelaksanaan Pengelolaan Lingkungan Kerja',
                        'kriterias' => [
                            ['kode_kriteria' => 'IV.2.1', 'deskripsi' => 'Pelaksanaan pengelolaan Bahaya Debu', 'nilai_maksimal' => 4.00],
                            ['kode_kriteria' => 'IV.2.2', 'deskripsi' => 'Pelaksanaan pengelolaan Bahaya Kebisingan', 'nilai_maksimal' => 4.00],
                            ['kode_kriteria' => 'IV.2.3', 'deskripsi' => 'Pelaksanaan pengelolaan Bahaya Getaran', 'nilai_maksimal' => 4.00],
                            ['kode_kriteria' => 'IV.2.4', 'deskripsi' => 'Pelaksanaan pengelolaan Bahaya Pencahayaan', 'nilai_maksimal' => 4.00],
                            ['kode_kriteria' => 'IV.2.5', 'deskripsi' => 'Pelaksanaan pengelolaan Kuantitas dan Kualitas Udara Kerja', 'nilai_maksimal' => 4.00],
                            ['kode_kriteria' => 'IV.2.6', 'deskripsi' => 'Pelaksanaan pengelolaan Iklim Kerja', 'nilai_maksimal' => 4.00],
                            ['kode_kriteria' => 'IV.2.7', 'deskripsi' => 'Pelaksanaan pengelolaan Bahaya Radiasi', 'nilai_maksimal' => 4.00],
                            ['kode_kriteria' => 'IV.2.8', 'deskripsi' => 'Pelaksanaan pengelolaan Faktor Kimia', 'nilai_maksimal' => 4.00],
                            ['kode_kriteria' => 'IV.2.9', 'deskripsi' => 'Pelaksanaan pengelolaan Faktor Biologi', 'nilai_maksimal' => 4.00],
                            ['kode_kriteria' => 'IV.2.10', 'deskripsi' => 'Pelaksanaan Kebersihan Lingkungan Kerja', 'nilai_maksimal' => 4.00],
                        ]
                    ],
                    [
                        'kode_sub' => 'IV.3',
                        'nama_sub' => 'Pelaksanaan Pengelolaan Kesehatan Kerja',
                        'kriterias' => [
                            ['kode_kriteria' => 'IV.3.1', 'deskripsi' => 'Pemeriksaan Kesehatan', 'nilai_maksimal' => 4.00],
                            ['kode_kriteria' => 'IV.3.2', 'deskripsi' => 'Pelayanan Kesehatan Kerja', 'nilai_maksimal' => 2.00],
                            ['kode_kriteria' => 'IV.3.3', 'deskripsi' => 'Pertolongan Pertama pada Kecelakaan', 'nilai_maksimal' => 2.00],
                            ['kode_kriteria' => 'IV.3.4', 'deskripsi' => 'Pengelolaan Kelelahan Kerja (Fatigue)', 'nilai_maksimal' => 3.00],
                            ['kode_kriteria' => 'IV.3.5', 'deskripsi' => 'Pengelolaan Pekerja pada Tempat yang Memiliki Risiko Kesehatan Tinggi', 'nilai_maksimal' => 2.00],
                            ['kode_kriteria' => 'IV.3.6', 'deskripsi' => 'Pengelolaan Rekaman Data Kesehatan Kerja', 'nilai_maksimal' => 4.00],
                            ['kode_kriteria' => 'IV.3.7', 'deskripsi' => 'Pengelolaan Higiene dan Sanitasi', 'nilai_maksimal' => 2.00],
                            ['kode_kriteria' => 'IV.3.8', 'deskripsi' => 'Pengelolaan Ergonomi', 'nilai_maksimal' => 3.00],
                            ['kode_kriteria' => 'IV.3.9', 'deskripsi' => 'Pengelolaan Makanan, Minuman dan Gizi Pekerja', 'nilai_maksimal' => 2.00],
                            ['kode_kriteria' => 'IV.3.10', 'deskripsi' => 'Diagnosis dan Pemeriksaan Penyakit Akibat Kerja', 'nilai_maksimal' => 4.00],
                        ]
                    ],
                    [
                        'kode_sub' => 'IV.4',
                        'nama_sub' => 'Pelaksanaan Pengelolaan KO Pertambangan',
                        'kriterias' => [
                            ['kode_kriteria' => 'IV.4.1', 'deskripsi' => 'Sistem dan pelaksanaan pemeliharaan / perawatan sarana, prasarana, instalasi, dan peralatan pertambangan', 'nilai_maksimal' => 4.00],
                            ['kode_kriteria' => 'IV.4.2', 'deskripsi' => 'Pengamanan instalasi', 'nilai_maksimal' => 4.00],
                            ['kode_kriteria' => 'IV.4.3', 'deskripsi' => 'Kelayakan sarana, prasarana, instalasi, dan peralatan pertambangan', 'nilai_maksimal' => 4.00],
                            ['kode_kriteria' => 'IV.4.4', 'deskripsi' => 'Kompetensi tenaga teknik', 'nilai_maksimal' => 4.00],
                            ['kode_kriteria' => 'IV.4.5', 'deskripsi' => 'Evaluasi Laporan Hasil Kajian Teknis Pertambangan', 'nilai_maksimal' => 4.00],
                        ]
                    ],
                    [
                        'kode_sub' => 'IV.5',
                        'nama_sub' => 'Pelaksanaan Pengelolaan Bahan Peledak dan Peledakan',
                        'kriterias' => [
                            ['kode_kriteria' => 'IV.5.1', 'deskripsi' => 'Gudang bahan peledak', 'nilai_maksimal' => 2.00],
                            ['kode_kriteria' => 'IV.5.2', 'deskripsi' => 'Penyimpanan bahan peledak', 'nilai_maksimal' => 4.00],
                            ['kode_kriteria' => 'IV.5.3', 'deskripsi' => 'Pengangkutan bahan peledak', 'nilai_maksimal' => 4.00],
                            ['kode_kriteria' => 'IV.5.4', 'deskripsi' => 'Pekerjaan peledakan', 'nilai_maksimal' => 4.00],
                        ]
                    ],
                    [
                        'kode_sub' => 'IV.6',
                        'nama_sub' => 'Penetapan Sistem Perancangan dan Rekayasa',
                        'kriterias' => [
                            ['kode_kriteria' => 'IV.6.1', 'deskripsi' => 'Perancangan dan rekayasa', 'nilai_maksimal' => 3.00],
                            ['kode_kriteria' => 'IV.6.2', 'deskripsi' => 'Perubahan', 'nilai_maksimal' => 3.00],
                        ]
                    ],
                    [
                        'kode_sub' => 'IV.7',
                        'nama_sub' => 'Penetapan Sistem Pembelian',
                        'kriterias' => [
                            ['kode_kriteria' => 'IV.7.1', 'deskripsi' => 'Penetapan Sistem Pembelian', 'nilai_maksimal' => 4.00],
                        ]
                    ],
                    [
                        'kode_sub' => 'IV.8',
                        'nama_sub' => 'Pemantauan dan Pengelolaan Perusahaan Jasa Pertambangan',
                        'kriterias' => [
                            ['kode_kriteria' => 'IV.8.1', 'deskripsi' => 'Persyaratan, seleksi dan penetapan perusahaan jasa pertambangan', 'nilai_maksimal' => 2.00],
                            ['kode_kriteria' => 'IV.8.2', 'deskripsi' => 'Tanggung jawab, pemantauan dan pelaporan perusahaan jasa', 'nilai_maksimal' => 2.00],
                            ['kode_kriteria' => 'IV.8.3', 'deskripsi' => 'Evaluasi perusahaan jasa pertambangan', 'nilai_maksimal' => 2.00],
                        ]
                    ],
                    [
                        'kode_sub' => 'IV.9',
                        'nama_sub' => 'Pengelolaan Keadaan Darurat',
                        'kriterias' => [
                            ['kode_kriteria' => 'IV.9.1', 'deskripsi' => 'Pengelolaan Keadaan Darurat', 'nilai_maksimal' => 3.00],
                        ]
                    ],
                    [
                        'kode_sub' => 'IV.10',
                        'nama_sub' => 'Penyediaan dan Penyiapan P3K',
                        'kriterias' => [
                            ['kode_kriteria' => 'IV.10.1', 'deskripsi' => 'Penyediaan dan Penyiapan P3K', 'nilai_maksimal' => 3.00],
                        ]
                    ],
                    [
                        'kode_sub' => 'IV.11',
                        'nama_sub' => 'Pelaksanaan keselamatan di luar pekerjaan (off the job safety)',
                        'kriterias' => [
                            ['kode_kriteria' => 'IV.11.1', 'deskripsi' => 'Pelaksanaan keselamatan di luar pekerjaan (off the job safety)', 'nilai_maksimal' => 3.00],
                        ]
                    ],
                ]
            ],
            [
                'kode_elemen' => 'V',
                'nama_elemen' => 'Pemantauan, Evaluasi dan Tindak Lanjut',
                'bobot'       => 15.00,
                'sub_elemens' => [
                    [
                        'kode_sub' => 'V.1',
                        'nama_sub' => 'Pemantauan dan pengukuran kinerja',
                        'kriterias' => [
                            ['kode_kriteria' => 'V.1.1', 'deskripsi' => 'Pemantauan dan Pengukuran Pencapaian Tujuan, Sasaran, dan program', 'nilai_maksimal' => 4.00],
                            ['kode_kriteria' => 'V.1.2', 'deskripsi' => 'Pemantauan dan Pengukuran Kinerja Pengelolaan lingkungan kerja', 'nilai_maksimal' => 4.00],
                            ['kode_kriteria' => 'V.1.3', 'deskripsi' => 'Pemantauan dan Pengukuran Kinerja Pengelolaan kesehatan kerja', 'nilai_maksimal' => 4.00],
                            ['kode_kriteria' => 'V.1.4', 'deskripsi' => 'Pemantauan dan Pengukuran Kinerja Pengelolaan Keselamatan Operasi', 'nilai_maksimal' => 4.00],
                            ['kode_kriteria' => 'V.1.5', 'deskripsi' => 'Pemantauan dan Pengukuran Kinerja Pengelolaan Bahan Peledak', 'nilai_maksimal' => 4.00],
                        ]
                    ],
                    [
                        'kode_sub' => 'V.2',
                        'nama_sub' => 'Inspeksi Pelaksanaan Keselamatan Pertambangan',
                        'kriterias' => [
                            ['kode_kriteria' => 'V.2.1', 'deskripsi' => 'Inspeksi Pelaksanaan Keselamatan Pertambangan', 'nilai_maksimal' => 4.00],
                        ]
                    ],
                    [
                        'kode_sub' => 'V.3',
                        'nama_sub' => 'Evaluasi kepatuhan Terhadap Ketentuan Peraturan Perundang-Undangan',
                        'kriterias' => [
                            ['kode_kriteria' => 'V.3.1', 'deskripsi' => 'Evaluasi kepatuhan Terhadap Ketentuan Peraturan Perundang-Undangan', 'nilai_maksimal' => 4.00],
                        ]
                    ],
                    [
                        'kode_sub' => 'V.4',
                        'nama_sub' => 'Penyelidikan Kecelakaan, Kejadian Berbahaya, dan Penyakit Akibat Kerja',
                        'kriterias' => [
                            ['kode_kriteria' => 'V.4.1', 'deskripsi' => 'Penyelidikan Kecelakaan, Kejadian Berbahaya, dan Penyakit Akibat Kerja', 'nilai_maksimal' => 4.00],
                        ]
                    ],
                    [
                        'kode_sub' => 'V.5',
                        'nama_sub' => 'Evaluasi Pengelolaan Administrasi Keselamatan Pertambangan',
                        'kriterias' => [
                            ['kode_kriteria' => 'V.5.1', 'deskripsi' => 'Buku tambang', 'nilai_maksimal' => 4.00],
                            ['kode_kriteria' => 'V.5.2', 'deskripsi' => 'Buku daftar kecelakaan tambang', 'nilai_maksimal' => 4.00],
                            ['kode_kriteria' => 'V.5.3', 'deskripsi' => 'Pelaporan pengelolaan keselamatan pertambangan', 'nilai_maksimal' => 4.00],
                            ['kode_kriteria' => 'V.5.4', 'deskripsi' => 'Dokumentasi Kejadian Berbahaya, kejadian akibat penyakit tenaga kerja dan penyakit akibat kerja', 'nilai_maksimal' => 4.00],
                            ['kode_kriteria' => 'V.5.5', 'deskripsi' => 'Dokumentasi dan Laporan pemenuhan Kompetensi serta Persyaratan', 'nilai_maksimal' => 4.00],
                        ]
                    ],
                    [
                        'kode_sub' => 'V.6',
                        'nama_sub' => 'Audit Internal Penerapan SMKP Minerba atau SMKP Khusus untuk pengolahan dan/ atau pemurnian',
                        'kriterias' => [
                            ['kode_kriteria' => 'V.6.1', 'deskripsi' => 'Audit Internal Penerapan SMKP Minerba atau SMKP Khusus untuk pengolahan dan/ atau pemurnian', 'nilai_maksimal' => 4.00],
                        ]
                    ],
                    [
                        'kode_sub' => 'V.7',
                        'nama_sub' => 'Rencana Perbaikan dan Tindak Lanjut',
                        'kriterias' => [
                            ['kode_kriteria' => 'V.7.1', 'deskripsi' => 'Rencana Perbaikan dan Tindak Lanjut', 'nilai_maksimal' => 4.00],
                        ]
                    ],
                ]
            ],
            [
                'kode_elemen' => 'VI',
                'nama_elemen' => 'Dokumentasi',
                'bobot'       => 3.00,
                'sub_elemens' => [
                    [
                        'kode_sub' => 'VI.1',
                        'nama_sub' => 'Penyusunan Penetapan dan Pendokumentasian Manual SMKP Minerba',
                        'kriterias' => [
                            ['kode_kriteria' => 'VI.1.1', 'deskripsi' => 'Penyusunan Penetapan dan Pendokumentasian Manual SMKP Minerba', 'nilai_maksimal' => 4.00],
                        ]
                    ],
                    [
                        'kode_sub' => 'VI.2',
                        'nama_sub' => 'Penyusunan Penetapan, Penerapan dan Pendokumentasian Prosedur pengendalian Dokumen Keselamatan Pertambangan',
                        'kriterias' => [
                            ['kode_kriteria' => 'VI.2.1', 'deskripsi' => 'Penyusunan Penetapan, Penerapan dan Pendokumentasian Prosedur pengendalian Dokumen Keselamatan Pertambangan', 'nilai_maksimal' => 3.00],
                        ]
                    ],
                    [
                        'kode_sub' => 'VI.3',
                        'nama_sub' => 'Penyusunan Penetapan, Penerapan dan Pendokumentasian Prosedur pengendalian Rekaman Keselamatan Pertambangan',
                        'kriterias' => [
                            ['kode_kriteria' => 'VI.3.1', 'deskripsi' => 'Penyusunan Penetapan, Penerapan dan Pendokumentasian Prosedur pengendalian Rekaman Keselamatan Pertambangan', 'nilai_maksimal' => 3.00],
                        ]
                    ],
                    [
                        'kode_sub' => 'VI.4',
                        'nama_sub' => 'Penetapan Jenis Dokumen dan Rekaman',
                        'kriterias' => [
                            ['kode_kriteria' => 'VI.4.1', 'deskripsi' => 'Penetapan Jenis Dokumen dan Rekaman', 'nilai_maksimal' => 2.00],
                        ]
                    ],
                ]
            ],
            [
                'kode_elemen' => 'VII',
                'nama_elemen' => 'Tinjauan Manajemen dan Peningkatan Kinerja',
                'bobot'       => 5.00,
                'sub_elemens' => [
                    [
                        'kode_sub' => 'VII.1',
                        'nama_sub' => 'Pelaksanaan Tinjauan Manajemen Penerapan SMKP Minerba oleh Manajemen Tertinggi Perusahaan',
                        'kriterias' => [
                            ['kode_kriteria' => 'VII.1.1', 'deskripsi' => 'Pelaksanaan Tinjauan Manajemen Penerapan SMKP Minerba oleh Manajemen Tertinggi Perusahaan', 'nilai_maksimal' => 4.00],
                        ]
                    ],
                    [
                        'kode_sub' => 'VII.2',
                        'nama_sub' => 'Pendokumentasian Catatan Hasil Tinjauan Manajemen',
                        'kriterias' => [
                            ['kode_kriteria' => 'VII.2.1', 'deskripsi' => 'Pendokumentasian Catatan Hasil Tinjauan Manajemen', 'nilai_maksimal' => 2.00],
                        ]
                    ],
                    [
                        'kode_sub' => 'VII.3',
                        'nama_sub' => 'Keluaran dari Tinjauan Manajemen Keselamatan Pertambangan',
                        'kriterias' => [
                            ['kode_kriteria' => 'VII.3.1', 'deskripsi' => 'Keluaran dari Tinjauan Manajemen Keselamatan Pertambangan', 'nilai_maksimal' => 2.00],
                        ]
                    ],
                    [
                        'kode_sub' => 'VII.4',
                        'nama_sub' => 'Pencatatan, Pendokumentasian, dan Pelaporan Hasil Tinjauan Manajemen',
                        'kriterias' => [
                            ['kode_kriteria' => 'VII.4.1', 'deskripsi' => 'Pencatatan, Pendokumentasian, dan Pelaporan Hasil Tinjauan Manajemen', 'nilai_maksimal' => 2.00],
                        ]
                    ],
                    [
                        'kode_sub' => 'VII.5',
                        'nama_sub' => 'Pelaksanaan Peningkatan Kinerja',
                        'kriterias' => [
                            ['kode_kriteria' => 'VII.5.1', 'deskripsi' => 'Pelaksanaan Peningkatan Kinerja', 'nilai_maksimal' => 1.00],
                        ]
                    ],
                    [
                        'kode_sub' => 'VII.6',
                        'nama_sub' => 'Penggunaan Tinjauan Hasil dari Tindak Lanjut Rencana Perbaikan dalam Penentuan Kebijakan',
                        'kriterias' => [
                            ['kode_kriteria' => 'VII.6.1', 'deskripsi' => 'Penggunaan Tinjauan Hasil dari Tindak Lanjut Rencana Perbaikan dalam Penentuan Kebijakan', 'nilai_maksimal' => 2.00],
                        ]
                    ],
                ]
            ],
        ];

        foreach ($data as $eData) {
            $elemen = Elemen::create([
                'kode_elemen' => $eData['kode_elemen'],
                'nama_elemen' => $eData['nama_elemen'],
                'bobot'       => $eData['bobot'],
            ]);

            foreach ($eData['sub_elemens'] as $subData) {
                $subElemen = SubElemen::create([
                    'elemen_id' => $elemen->id,
                    'kode_sub'  => $subData['kode_sub'],
                    'nama_sub'  => $subData['nama_sub'],
                ]);

                foreach ($subData['kriterias'] as $kData) {
                    Kriteria::create([
                        'sub_elemen_id'  => $subElemen->id,
                        'kode_kriteria'  => $kData['kode_kriteria'],
                        'deskripsi'      => $kData['deskripsi'],
                        'nilai_maksimal' => $kData['nilai_maksimal'],
                    ]);
                }
            }
        }
    }
}
