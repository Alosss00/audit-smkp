<?php

namespace Database\Seeders;

use App\Models\Elemen;
use App\Models\SubElemen;
use App\Models\Kriteria;
use Illuminate\Database\Seeder;

class SMKPSeeder extends Seeder
{
    /**
     * Run database seeds for SMKP Minerba elements, sub-elements, and criteria
     * matching official Kepdirjen 185.K/37.04/DJB/2019 (Formulir TT-MGT-FRS-026B).
     * Total: 7 Elemen, 100% Bobot, 102 Sub-sub Kriteria Lengkap dengan Pedoman Penilaian (0-4).
     */
    public function run(): void
    {
        // Clean existing tables to ensure exact 333 total max score mapping
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Kriteria::truncate();
        SubElemen::truncate();
        Elemen::truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // =========================================================================
        // ELEMEN I: KEBIJAKAN (Bobot: 10.00%, Total Nilai Maksimal: 19)
        // =========================================================================
        $e1 = Elemen::updateOrCreate(['kode_elemen' => 'I'], ['nama_elemen' => 'Kebijakan', 'bobot' => 10.00]);

        $s1_1 = SubElemen::updateOrCreate(['elemen_id' => $e1->id, 'kode_sub' => 'I.1'], ['nama_sub' => 'Penyusunan Kebijakan']);
        $k1_1_1 = Kriteria::updateOrCreate(['sub_elemen_id' => $s1_1->id, 'kode_kriteria' => 'I.1.1'], [
            'deskripsi' => 'Penyusunan dan penetapan kebijakan Keselamatan Pertambangan secara tertulis oleh pimpinan tertinggi perusahaan.',
            'nilai_maksimal' => 4.00,
            'persyaratan_dokumen' => 'Dokumen Kebijakan Keselamatan Pertambangan tertulis, ditandatangani oleh KTT / Pimpinan Tertinggi perusahaan, dan mencantumkan tanggal penetapan.',
            'pedoman_nilai_0' => 'Nilai 0: Tidak ada kebijakan tertulis Keselamatan Pertambangan.',
            'pedoman_nilai_1' => 'Nilai 1: Terdapat draft kebijakan tetapi belum ditandatangani oleh Pimpinan Tertinggi.',
            'pedoman_nilai_2' => 'Nilai 2: Kebijakan telah ditandatangani tetapi belum mencakup seluruh prinsip Keselamatan Pertambangan (K3 & KO).',
            'pedoman_nilai_3' => 'Nilai 3: Kebijakan lengkap & ditandatangani tetapi belum ditinjau berkala.',
            'pedoman_nilai_4' => 'Nilai 4: Kebijakan tertulis, ditandatangani pimpinan tertinggi, mencakup K3 & KO, serta ditinjau berkala secara terdokumentasi.',
        ]);

        $s1_2 = SubElemen::updateOrCreate(['elemen_id' => $e1->id, 'kode_sub' => 'I.2'], ['nama_sub' => 'Isi Kebijakan']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s1_2->id, 'kode_kriteria' => 'I.2.1'], [
            'deskripsi' => 'Isi kebijakan memuat komitmen manajemen puncak terhadap pencegahan kecelakaan, kejadian berbahaya, PAK, dan KO pertambangan.',
            'nilai_maksimal' => 4.00,
            'persyaratan_dokumen' => 'Teks kebijakan yang secara eksplisit memuat komitmen pencegahan kecelakaan, kejadian berbahaya, PAK, dan peningkatan kinerja KP.',
            'pedoman_nilai_0' => 'Nilai 0: Kebijakan tidak memuat komitmen Keselamatan Pertambangan.',
            'pedoman_nilai_1' => 'Nilai 1: Komitmen bersifat umum tanpa secara spesifik menyebut K3 & KO Pertambangan.',
            'pedoman_nilai_2' => 'Nilai 2: Komitmen memuat K3 tetapi tidak memuat komitmen Keselamatan Operasional (KO) Pertambangan.',
            'pedoman_nilai_3' => 'Nilai 3: Komitmen memuat K3 & KO tetapi belum menyelaraskan dengan peningkatan berkelanjutan.',
            'pedoman_nilai_4' => 'Nilai 4: Komitmen memuat K3 & KO secara jelas, peningkatan berkelanjutan, dan kepatuhan perundang-undangan.',
        ]);

        $s1_3 = SubElemen::updateOrCreate(['elemen_id' => $e1->id, 'kode_sub' => 'I.3'], ['nama_sub' => 'Penetapan Kebijakan']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s1_3->id, 'kode_kriteria' => 'I.3.1'], [
            'deskripsi' => 'Penetapan Kebijakan Keselamatan Pertambangan disahkan secara resmi oleh pimpinan tertinggi.',
            'nilai_maksimal' => 3.00,
            'persyaratan_dokumen' => 'SK Penetapan Kebijakan KP yang tertanggal dan tertanda pimpinan tertinggi.',
            'pedoman_nilai_0' => 'Nilai 0: Tidak ada pengesahan resmi.',
            'pedoman_nilai_1' => 'Nilai 1: Pengesahan tidak tertanggal.',
            'pedoman_nilai_2' => 'Nilai 2: Pengesahan oleh pejabat di bawah pimpinan tertinggi.',
            'pedoman_nilai_3' => 'Nilai 3: Pengesahan resmi dan tertanggal oleh pimpinan tertinggi.',
        ]);

        $s1_4 = SubElemen::updateOrCreate(['elemen_id' => $e1->id, 'kode_sub' => 'I.4'], ['nama_sub' => 'Komunikasi Kebijakan']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s1_4->id, 'kode_kriteria' => 'I.4.1'], [
            'deskripsi' => 'Kebijakan disosialisasikan dan dikomunikasikan kepada seluruh pekerja pertambangan dan mitra kerja.',
            'nilai_maksimal' => 4.00,
            'persyaratan_dokumen' => 'Bukti sosialisasi kebijakan (Daftar hadir induksi/briefing, foto papan pengumuman, spanduk, booklet, dan catatan evaluasi pemahaman).',
            'pedoman_nilai_0' => 'Nilai 0: Tidak ada sosialisasi kebijakan kepada pekerja.',
            'pedoman_nilai_1' => 'Nilai 1: Kebijakan dipasang di papan pengumuman tanpa sosialisasi tatap muka/induksi.',
            'pedoman_nilai_2' => 'Nilai 2: Sosialisasi dilakukan hanya kepada pekerja internal, mitra kerja belum terpapar.',
            'pedoman_nilai_3' => 'Nilai 3: Sosialisasi dilakukan kepada seluruh pekerja internal & mitra kerja tetapi belum diuji pemahamannya.',
            'pedoman_nilai_4' => 'Nilai 4: Sosialisasi merata ke seluruh pekerja & kontraktor, terpajang di area kerja, dan hasil uji pemahaman >85%.',
        ]);

        $s1_5 = SubElemen::updateOrCreate(['elemen_id' => $e1->id, 'kode_sub' => 'I.5'], ['nama_sub' => 'Tinjauan Kebijakan']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s1_5->id, 'kode_kriteria' => 'I.5.1'], [
            'deskripsi' => 'Tinjauan Kebijakan Keselamatan Pertambangan dilakukan secara berkala untuk menjamin kesesuaian dan efektivitasnya.',
            'nilai_maksimal' => 4.00,
            'persyaratan_dokumen' => 'Notulen dan Berita Acara Tinjauan Kebijakan KP berkala oleh Manajemen Puncak.',
            'pedoman_nilai_0' => 'Nilai 0: Tidak ada tinjauan kebijakan.',
            'pedoman_nilai_1' => 'Nilai 1: Tinjauan kebijakan dilakukan tanpa dokumentasi resmi.',
            'pedoman_nilai_2' => 'Nilai 2: Tinjauan kebijakan dilakukan bila terjadi insiden fatal saja.',
            'pedoman_nilai_3' => 'Nilai 3: Tinjauan kebijakan berkala terdokumentasi tetapi belum menghasilkan pembaruan.',
            'pedoman_nilai_4' => 'Nilai 4: Tinjauan kebijakan berkala terdokumentasi rapi dan diperbarui sesuai perubahan operasional/regulasi.',
        ]);

        // =========================================================================
        // ELEMEN II: PERENCANAAN (Bobot: 15.00%, Total Nilai Maksimal: 29)
        // =========================================================================
        $e2 = Elemen::updateOrCreate(['kode_elemen' => 'II'], ['nama_elemen' => 'Perencanaan', 'bobot' => 15.00]);

        $s2_1 = SubElemen::updateOrCreate(['elemen_id' => $e2->id, 'kode_sub' => 'II.1'], ['nama_sub' => 'Penelaahan Awal']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s2_1->id, 'kode_kriteria' => 'II.1.1'], [
            'deskripsi' => 'Penelaahan awal dilakukan untuk mengidentifikasi tingkat pemenuhan kinerja dan kepatuhan Keselamatan Pertambangan.',
            'nilai_maksimal' => 4.00,
            'persyaratan_dokumen' => 'Dokumen hasil Penelaahan Awal / Initial Review SMKP Minerba.',
            'pedoman_nilai_0' => 'Nilai 0: Tidak ada penelaahan awal.',
            'pedoman_nilai_1' => 'Nilai 1: Penelaahan awal dilakukan tanpa metode baku.',
            'pedoman_nilai_2' => 'Nilai 2: Penelaahan awal mencakup sebagian kecil aspek operasional.',
            'pedoman_nilai_3' => 'Nilai 3: Penelaahan awal komprehensif tetapi belum dijadikan dasar penyusunan perencanaan.',
            'pedoman_nilai_4' => 'Nilai 4: Penelaahan awal komprehensif, terdokumentasi, dan dijadikan acuan dasar perencanaan.',
        ]);

        $s2_2 = SubElemen::updateOrCreate(['elemen_id' => $e2->id, 'kode_sub' => 'II.2'], ['nama_sub' => 'Manajemen Risiko']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s2_2->id, 'kode_kriteria' => 'II.2.1'], [
            'deskripsi' => 'Komunikasi dan konsultasi risiko kepada seluruh pihak yang berkepentingan.',
            'nilai_maksimal' => 4.00,
            'persyaratan_dokumen' => 'Bukti sosialisasi & konsultasi IBPR / Manajemen Risiko kepada pekerja dan mitra kerja.',
            'pedoman_nilai_0' => 'Nilai 0: Tidak ada komunikasi & konsultasi risiko.',
            'pedoman_nilai_1' => 'Nilai 1: Komunikasi risiko dilakukan sebatas menempelkan pengumuman.',
            'pedoman_nilai_2' => 'Nilai 2: Komunikasi dilakukan sebatas internal tanpa melibatkan pekerja mitra kerja.',
            'pedoman_nilai_3' => 'Nilai 3: Komunikasi & konsultasi dilaksanakan tetapi belum dievaluasi pemahamannya.',
            'pedoman_nilai_4' => 'Nilai 4: Komunikasi & konsultasi terstruktur, melibatkan seluruh pihak, dan dievaluasi berkala.',
        ]);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s2_2->id, 'kode_kriteria' => 'II.2.2'], [
            'deskripsi' => 'Penetapan konteks risiko mencakup konteks internal dan eksternal operasional pertambangan.',
            'nilai_maksimal' => 3.00,
            'persyaratan_dokumen' => 'Dokumen Penetapan Konteks Risiko SMKP (Internal, Eksternal, Hukum, Operasional).',
            'pedoman_nilai_0' => 'Nilai 0: Tidak ada penetapan konteks risiko.',
            'pedoman_nilai_1' => 'Nilai 1: Konteks risiko hanya mencakup aspek internal.',
            'pedoman_nilai_2' => 'Nilai 2: Konteks risiko mencakup internal & eksternal tetapi belum disahkan.',
            'pedoman_nilai_3' => 'Nilai 3: Konteks risiko ditetapkan lengkap, disahkan, dan ditinjau berkala.',
        ]);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s2_2->id, 'kode_kriteria' => 'II.2.3'], [
            'deskripsi' => 'Identifikasi bahaya pada seluruh aktivitas operasional rutin dan non-rutin.',
            'nilai_maksimal' => 2.00,
            'persyaratan_dokumen' => 'Dokumen Matriks Identifikasi Bahaya / IBPR.',
            'pedoman_nilai_0' => 'Nilai 0: Tidak ada dokumen identifikasi bahaya.',
            'pedoman_nilai_1' => 'Nilai 1: Identifikasi bahaya hanya mencakup aktivitas rutin.',
            'pedoman_nilai_2' => 'Nilai 2: Identifikasi bahaya mencakup seluruh aktivitas rutin & non-rutin secara komprehensif.',
        ]);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s2_2->id, 'kode_kriteria' => 'II.2.4'], [
            'deskripsi' => 'Penilaian dan pengendalian risiko berdasarkan hierarki pengendalian risiko.',
            'nilai_maksimal' => 3.00,
            'persyaratan_dokumen' => 'Matriks Penilaian Risiko & Action Plan Pengendalian (Eliminasi, Substitusi, Rekayasa, Adm, APD).',
            'pedoman_nilai_0' => 'Nilai 0: Tidak ada penilaian dan pengendalian risiko.',
            'pedoman_nilai_1' => 'Nilai 1: Penilaian risiko ada tetapi pengendalian tidak menerapkan hierarki.',
            'pedoman_nilai_2' => 'Nilai 2: Pengendalian risiko menerapkan hierarki tetapi penetapan PIC belum jelas.',
            'pedoman_nilai_3' => 'Nilai 3: Penilaian & pengendalian menerapkan hierarki lengkap dengan PIC & target waktu.',
        ]);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s2_2->id, 'kode_kriteria' => 'II.2.5'], [
            'deskripsi' => 'Pemantauan dan peninjauan berkala terhadap pelaksanaan manajemen risiko.',
            'nilai_maksimal' => 3.00,
            'persyaratan_dokumen' => 'Laporan Evaluasi dan Pemantauan Progres Pengendalian IBPR Berkala.',
            'pedoman_nilai_0' => 'Nilai 0: Tidak ada pemantauan & peninjauan IBPR.',
            'pedoman_nilai_1' => 'Nilai 1: Pemantauan dilakukan insidental tanpa laporan resmi.',
            'pedoman_nilai_2' => 'Nilai 2: Pemantauan dilakukan berkala tetapi progres perbaikan tertunda.',
            'pedoman_nilai_3' => 'Nilai 3: Pemantauan berkala terdokumentasi dan penyelesaian perbaikan >90% tepat waktu.',
        ]);

        $s2_3 = SubElemen::updateOrCreate(['elemen_id' => $e2->id, 'kode_sub' => 'II.3'], ['nama_sub' => 'Identifikasi dan Kepatuhan Terhadap Ketentuan Peraturan Perundang-undangan dan Persyaratan Lainnya yang Terkait']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s2_3->id, 'kode_kriteria' => 'II.3.1'], [
            'deskripsi' => 'Identifikasi, inventarisasi, dan evaluasi kepatuhan terhadap peraturan perundang-undangan KP.',
            'nilai_maksimal' => 3.00,
            'persyaratan_dokumen' => 'Matriks Inventarisasi Peraturan Perundang-undangan dan Evaluasi Kepatuhan Hukum KP.',
            'pedoman_nilai_0' => 'Nilai 0: Tidak ada inventarisasi regulasi.',
            'pedoman_nilai_1' => 'Nilai 1: Inventarisasi regulasi ada tetapi tidak di-update.',
            'pedoman_nilai_2' => 'Nilai 2: Regukasi di-update tetapi evaluasi kepatuhan belum dilakukan.',
            'pedoman_nilai_3' => 'Nilai 3: Regukasi di-update rutin, evaluasi kepatuhan terdokumentasi 100%, dan temuan ditindaklanjuti.',
        ]);

        $s2_4 = SubElemen::updateOrCreate(['elemen_id' => $e2->id, 'kode_sub' => 'II.4'], ['nama_sub' => 'Penetapan Tujuan, Sasaran, dan Program']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s2_4->id, 'kode_kriteria' => 'II.4.1'], [
            'deskripsi' => 'Penetapan Tujuan, Sasaran, dan Program Keselamatan Pertambangan yang terukur.',
            'nilai_maksimal' => 4.00,
            'persyaratan_dokumen' => 'Dokumen Sasaran & Program K3KO Pertambangan Tahunan (SMART).',
            'pedoman_nilai_0' => 'Nilai 0: Tidak ada tujuan, sasaran, dan program KP.',
            'pedoman_nilai_1' => 'Nilai 1: Sasaran ada tetapi tidak spesifik dan tidak terukur.',
            'pedoman_nilai_2' => 'Nilai 2: Sasaran terukur tetapi program kerja tidak dilengkapi alokasi sumber daya.',
            'pedoman_nilai_3' => 'Nilai 3: Sasaran & program lengkap tetapi pemantauan progres belum berkala.',
            'pedoman_nilai_4' => 'Nilai 4: Sasaran & program SMART, disahkan pimpinan, alokasi sumber daya jelas, dan dipantau berkala.',
        ]);

        $s2_5 = SubElemen::updateOrCreate(['elemen_id' => $e2->id, 'kode_sub' => 'II.5'], ['nama_sub' => 'Rencana Kerja dan Anggaran Keselamatan Pertambangan']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s2_5->id, 'kode_kriteria' => 'II.5.1'], [
            'deskripsi' => 'Penyusunan Rencana Kerja dan Anggaran Keselamatan Pertambangan (RKAB KP) yang memadai.',
            'nilai_maksimal' => 3.00,
            'persyaratan_dokumen' => 'Dokumen RKAB Bagian Keselamatan Pertambangan yang disetujui Manajemen Puncak.',
            'pedoman_nilai_0' => 'Nilai 0: Tidak ada RKAB KP.',
            'pedoman_nilai_1' => 'Nilai 1: Anggaran KP digabung secara tidak jelas dalam operasional umum.',
            'pedoman_nilai_2' => 'Nilai 2: Anggaran KP terpisah tetapi tidak mencukupi kebutuhan program.',
            'pedoman_nilai_3' => 'Nilai 3: Anggaran KP terencana, disetujui pimpinan, dan terrealisasi dengan baik.',
        ]);

        // =========================================================================
        // ELEMEN III: ORGANISASI DAN PERSONEL (Bobot: 17.00%, Total Nilai Maksimal: 62)
        // =========================================================================
        $e3 = Elemen::updateOrCreate(['kode_elemen' => 'III'], ['nama_elemen' => 'Organisasi dan Personel', 'bobot' => 17.00]);

        $s3_1 = SubElemen::updateOrCreate(['elemen_id' => $e3->id, 'kode_sub' => 'III.1'], ['nama_sub' => 'Penyusunan dan Penetapan Struktur Organisasi, Tugas, Tanggung Jawab, dan Wewenang']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s3_1->id, 'kode_kriteria' => 'III.1.1'], [
            'deskripsi' => 'Penetapan Struktur Organisasi Keselamatan Pertambangan beserta uraian tugas dan wewenang.',
            'nilai_maksimal' => 4.00,
            'persyaratan_dokumen' => 'Struktur Organisasi Perusahaan & Bagan Organisasi K3KO, Uraian Jabatan (Job Description).',
            'pedoman_nilai_0' => 'Nilai 0: Tidak ada struktur organisasi KP.',
            'pedoman_nilai_1' => 'Nilai 1: Ada struktur tetapi tidak ada job description.',
            'pedoman_nilai_2' => 'Nilai 2: Ada job description tetapi wewenang K3KO belum jelas.',
            'pedoman_nilai_3' => 'Nilai 3: Struktur & wewenang jelas tetapi sosialisasi belum menyeluruh.',
            'pedoman_nilai_4' => 'Nilai 4: Struktur disahkan, wewenang tegas, disosialisasikan, dan dipahami seluruh staf.',
        ]);

        $s3_2 = SubElemen::updateOrCreate(['elemen_id' => $e3->id, 'kode_sub' => 'III.2'], ['nama_sub' => 'Penunjukan KTT, Kepala Tambang Bawah Tanah, dan/atau Kepala Kapal Keruk untuk Perusahaan Pertambangan']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s3_2->id, 'kode_kriteria' => 'III.2.1'], [
            'deskripsi' => 'Penunjukan Kepala Teknik Tambang (KTT) yang disahkan oleh Kepala Inspektur Tambang (KaIT).',
            'nilai_maksimal' => 0.00,
            'persyaratan_dokumen' => 'Surat Pengesahan KTT dari KaIT ESDM. (N/A pada sampel ini)',
            'pedoman_nilai_0' => 'Nilai 0: Belum ada penunjukan KTT.',
            'pedoman_nilai_1' => 'Nilai 1: KTT ditunjuk internal tetapi belum diajukan pengesahan ke KaIT.',
            'pedoman_nilai_2' => 'Nilai 2: Dalam proses pengesahan KaIT.',
            'pedoman_nilai_3' => 'Nilai 3: KTT telah disahkan KaIT tetapi administratif belum lengkap.',
            'pedoman_nilai_4' => 'Nilai 4: Surat Pengesahan KTT dari KaIT lengkap dan sah berlaku.',
        ]);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s3_2->id, 'kode_kriteria' => 'III.2.2'], [
            'deskripsi' => 'Penunjukan Kepala Tambang Bawah Tanah (bila beroperasi tambang bawah tanah).',
            'nilai_maksimal' => 0.00,
            'persyaratan_dokumen' => 'Surat Pengesahan Kepala Tambang Bawah Tanah. (N/A pada sampel ini)',
            'pedoman_nilai_0' => 'Nilai 0: Tidak ditunjuk.',
            'pedoman_nilai_1' => 'Nilai 1: Ditunjuk tanpa pengesahan KaIT.',
            'pedoman_nilai_2' => 'Nilai 2: Pengesahan dalam proses.',
            'pedoman_nilai_3' => 'Nilai 3: Pengesahan lengkap.',
            'pedoman_nilai_4' => 'Nilai 4: Pengesahan resmi KaIT sah dan sesuai kualifikasi.',
        ]);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s3_2->id, 'kode_kriteria' => 'III.2.3'], [
            'deskripsi' => 'Penunjukan Kepala Kapal Keruk / Isap (bila beroperasi kapal keruk/isap).',
            'nilai_maksimal' => 0.00,
            'persyaratan_dokumen' => 'Surat Pengesahan Kepala Kapal Keruk/Isap. (N/A pada sampel ini)',
            'pedoman_nilai_0' => 'Nilai 0: Tidak ditunjuk.',
            'pedoman_nilai_1' => 'Nilai 1: Ditunjuk tanpa pengesahan.',
            'pedoman_nilai_2' => 'Nilai 2: Pengesahan dalam proses.',
            'pedoman_nilai_3' => 'Nilai 3: Pengesahan lengkap.',
            'pedoman_nilai_4' => 'Nilai 4: Pengesahan resmi KaIT sah.',
        ]);

        $s3_3 = SubElemen::updateOrCreate(['elemen_id' => $e3->id, 'kode_sub' => 'III.3'], ['nama_sub' => 'Penunjukan PJO Untuk Perusahaan Jasa Pertambangan']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s3_3->id, 'kode_kriteria' => 'III.3.1'], [
            'deskripsi' => 'Penunjukan Penanggung Jawab Operasional (PJO) untuk perusahaan jasa pertambangan yang disetujui KTT.',
            'nilai_maksimal' => 2.00,
            'persyaratan_dokumen' => 'Surat Penunjukan & Pengesahan PJO dari KTT.',
            'pedoman_nilai_0' => 'Nilai 0: PJO tidak ditunjuk.',
            'pedoman_nilai_1' => 'Nilai 1: PJO ditunjuk tetapi belum disahkan KTT.',
            'pedoman_nilai_2' => 'Nilai 2: PJO ditunjuk resmi dan disahkan oleh KTT.',
        ]);

        $s3_4 = SubElemen::updateOrCreate(['elemen_id' => $e3->id, 'kode_sub' => 'III.4'], ['nama_sub' => 'Pembentukan dan Penetapan Bagian K3 Pertambangan dan KO Pertambangan']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s3_4->id, 'kode_kriteria' => 'III.4.1'], [
            'deskripsi' => 'Pembentukan Bagian K3 Pertambangan dan Bagian KO Pertambangan.',
            'nilai_maksimal' => 4.00,
            'persyaratan_dokumen' => 'SK Pembentukan Bagian K3 & KO Pertambangan.',
            'pedoman_nilai_0' => 'Nilai 0: Tidak dibentuk.',
            'pedoman_nilai_1' => 'Nilai 1: Bagian K3 dibentuk tetapi KO belum dibentuk.',
            'pedoman_nilai_2' => 'Nilai 2: K3 & KO dibentuk tetapi personel belum mencukupi.',
            'pedoman_nilai_3' => 'Nilai 3: Dibentuk lengkap dengan personel kompeten.',
            'pedoman_nilai_4' => 'Nilai 4: Dibentuk lengkap, personel independen, dan berfasilitas memadai.',
        ]);

        $s3_5 = SubElemen::updateOrCreate(['elemen_id' => $e3->id, 'kode_sub' => 'III.5'], ['nama_sub' => 'Penunjukan Pengawas Operasional dan Pengawas Teknik']);
        $k3_5_1 = Kriteria::updateOrCreate(['sub_elemen_id' => $s3_5->id, 'kode_kriteria' => 'III.5.1'], [
            'deskripsi' => 'Penunjukan Pengawas Operasional (POP/POM/POU) dan Pengawas Teknik yang kompeten.',
            'nilai_maksimal' => 4.00,
            'persyaratan_dokumen' => 'Surat Penunjukan Pengawas Operasional & sertifikat kompetensi POP/POM/POU.',
            'pedoman_nilai_0' => 'Nilai 0: Pengawas tidak ditunjuk.',
            'pedoman_nilai_1' => 'Nilai 1: Ditunjuk tanpa memiliki sertifikat kompetensi.',
            'pedoman_nilai_2' => 'Nilai 2: Sebagian pengawas bersertifikat POP/POM.',
            'pedoman_nilai_3' => 'Nilai 3: Seluruh pengawas bersertifikat & ditunjuk resmi KTT.',
            'pedoman_nilai_4' => 'Nilai 4: Seluruh pengawas bersertifikat, ditunjuk KTT, dan dievaluasi kinerjanya rutin.',
        ]);

        $s3_6 = SubElemen::updateOrCreate(['elemen_id' => $e3->id, 'kode_sub' => 'III.6'], ['nama_sub' => 'Penunjukan Tenaga Teknik Khusus Pertambangan']);
        $k3_6_1 = Kriteria::updateOrCreate(['sub_elemen_id' => $s3_6->id, 'kode_kriteria' => 'III.6.1'], [
            'deskripsi' => 'Penunjukan Tenaga Teknik Khusus Pertambangan (Juru Ledak, Juru Ukur, Pengawas Kelistrikan, dll).',
            'nilai_maksimal' => 4.00,
            'persyaratan_dokumen' => 'Surat Penunjukan Tenaga Teknik Khusus & Sertifikat KTT/KIM/Kompetensi.',
            'pedoman_nilai_0' => 'Nilai 0: Tidak ditunjuk.',
            'pedoman_nilai_1' => 'Nilai 1: Ditunjuk tanpa sertifikat kompetensi.',
            'pedoman_nilai_2' => 'Nilai 2: Memiliki sertifikat tetapi belum ada surat penunjukan resmi KTT.',
            'pedoman_nilai_3' => 'Nilai 3: Bersertifikat dan ditunjuk resmi KTT.',
            'pedoman_nilai_4' => 'Nilai 4: Bersertifikat lengkap, ditunjuk KTT, dan memenuhi rasio kecukupan area kerja.',
        ]);

        $s3_7 = SubElemen::updateOrCreate(['elemen_id' => $e3->id, 'kode_sub' => 'III.7'], ['nama_sub' => 'Pembentukan dan Penetapan Komite Keselamatan Pertambangan']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s3_7->id, 'kode_kriteria' => 'III.7.1'], [
            'deskripsi' => 'Pembentukan dan pelaksanaan Komite Keselamatan Pertambangan (Safety Committee).',
            'nilai_maksimal' => 4.00,
            'persyaratan_dokumen' => 'SK Komite KP, Notulen Rapat Bulanan Komite KP, & Bukti Tindak Lanjut.',
            'pedoman_nilai_0' => 'Nilai 0: Komite KP tidak dibentuk.',
            'pedoman_nilai_1' => 'Nilai 1: Dibentuk tetapi tidak pernah rapat.',
            'pedoman_nilai_2' => 'Nilai 2: Rapat dilakukan tetapi tidak rutin bulanan.',
            'pedoman_nilai_3' => 'Nilai 3: Rapat rutin bulanan terdokumentasi notulen.',
            'pedoman_nilai_4' => 'Nilai 4: Rapat rutin bulanan, notulen terdistribusi, dan rekomendasi ditindaklanjuti 100%.',
        ]);

        $s3_8 = SubElemen::updateOrCreate(['elemen_id' => $e3->id, 'kode_sub' => 'III.8'], ['nama_sub' => 'Penunjukan Tim Tanggap Darurat']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s3_8->id, 'kode_kriteria' => 'III.8.1'], [
            'deskripsi' => 'Penunjukan Tim Tanggap Darurat (Emergency Response Team / ERT) pertambangan.',
            'nilai_maksimal' => 4.00,
            'persyaratan_dokumen' => 'SK Tim Tanggap Darurat, Jadwal Piket ERT, & Sertifikat Pelatihan Rescue.',
            'pedoman_nilai_0' => 'Nilai 0: Tim ERT tidak ditunjuk.',
            'pedoman_nilai_1' => 'Nilai 1: Tim ditunjuk tanpa struktur dan jadwal piket.',
            'pedoman_nilai_2' => 'Nilai 2: Struktur & jadwal ada tetapi belum dilatih penanggulangan darurat.',
            'pedoman_nilai_3' => 'Nilai 3: Tim ERT dilatih berkala dan siap siaga.',
            'pedoman_nilai_4' => 'Nilai 4: Tim ERT bersertifikat rescue, jadwal siaga 24/7, dan melakukan simulasi rutin.',
        ]);

        $s3_9 = SubElemen::updateOrCreate(['elemen_id' => $e3->id, 'kode_sub' => 'III.9'], ['nama_sub' => 'Seleksi dan Penempatan Personel']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s3_9->id, 'kode_kriteria' => 'III.9.1'], [
            'deskripsi' => 'Prosedur seleksi dan penempatan personel sesuai standar kompetensi dan kesehatan kerja.',
            'nilai_maksimal' => 4.00,
            'persyaratan_dokumen' => 'SOP Rekrutmen, Hasil MCU Prakerja, & Matriks Kompetensi Jabatan.',
            'pedoman_nilai_0' => 'Nilai 0: Tidak ada prosedur seleksi & penempatan.',
            'pedoman_nilai_1' => 'Nilai 1: Seleksi dilakukan tanpa MCU pra-kerja.',
            'pedoman_nilai_2' => 'Nilai 2: MCU dilakukan tetapi penempatan belum memperhatikan kompetensi.',
            'pedoman_nilai_3' => 'Nilai 3: Seleksi & penempatan sesuai standar kesehatan & kompetensi.',
            'pedoman_nilai_4' => 'Nilai 4: Prosedur seleksi ketat, MCU komprehensif, dan penempatan 100% matrik kompetensi.',
        ]);

        $s3_10 = SubElemen::updateOrCreate(['elemen_id' => $e3->id, 'kode_sub' => 'III.10'], ['nama_sub' => 'Penyelenggaraan dan Pelaksanaan Pendidikan dan Pelatihan Serta Kompetensi Kerja']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s3_10->id, 'kode_kriteria' => 'III.10.1'], [
            'deskripsi' => 'Pendidikan dan pelatihan keselamatan pertambangan bagi pekerja tambang.',
            'nilai_maksimal' => 4.00,
            'persyaratan_dokumen' => 'Matriks Diklat KP, Jadwal & Rekaman Pelaksanaan Diklat Induksi / Refresher.',
            'pedoman_nilai_0' => 'Nilai 0: Tidak ada program diklat KP.',
            'pedoman_nilai_1' => 'Nilai 1: Diklat dilakukan insidental tanpa analisis kebutuhan (TNA).',
            'pedoman_nilai_2' => 'Nilai 2: Ada matrik diklat tetapi realisasi <70%.',
            'pedoman_nilai_3' => 'Nilai 3: Realisasi diklat >85% dan terdokumentasi.',
            'pedoman_nilai_4' => 'Nilai 4: Diklat berbasis TNA, realisasi 100%, dan dievaluasi efektivitasnya.',
        ]);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s3_10->id, 'kode_kriteria' => 'III.10.2'], [
            'deskripsi' => 'Pemenuhan standar kompetensi kerja pekerja pertambangan.',
            'nilai_maksimal' => 4.00,
            'persyaratan_dokumen' => 'Sertifikat Kompetensi Kerja SKKNI / Standard BNSP Pekerja.',
            'pedoman_nilai_0' => 'Nilai 0: Tidak ada pemenuhan sertifikat kompetensi.',
            'pedoman_nilai_1' => 'Nilai 1: Pemenuhan kompetensi <50%.',
            'pedoman_nilai_2' => 'Nilai 2: Pemenuhan kompetensi 50%-80%.',
            'pedoman_nilai_3' => 'Nilai 3: Pemenuhan kompetensi >80%.',
            'pedoman_nilai_4' => 'Nilai 4: 100% pekerja memenuhi sertifikasi standar kompetensi kerja.',
        ]);

        $s3_11 = SubElemen::updateOrCreate(['elemen_id' => $e3->id, 'kode_sub' => 'III.11'], ['nama_sub' => 'Penyusunan, Penetapan, dan Penerapan Komunikasi Keselamatan Pertambangan']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s3_11->id, 'kode_kriteria' => 'III.11.1'], [
            'deskripsi' => 'Penerapan prosedur komunikasi Keselamatan Pertambangan (Safety Talk, Briefing, Papan Informasi).',
            'nilai_maksimal' => 4.00,
            'persyaratan_dokumen' => 'Notulen Safety Talk / P5M, Media Komunikasi K3KO, Rekaman Safety Meeting.',
            'pedoman_nilai_0' => 'Nilai 0: Tidak ada komunikasi KP.',
            'pedoman_nilai_1' => 'Nilai 1: Komunikasi insidental.',
            'pedoman_nilai_2' => 'Nilai 2: Safety talk harian berjalan tetapi tidak terdokumentasi.',
            'pedoman_nilai_3' => 'Nilai 3: Safety talk berjalan rutin harian dan terdokumentasi.',
            'pedoman_nilai_4' => 'Nilai 4: Komunikasi dua arah aktif, teratur harian, dan dievaluasi pemahamannya.',
        ]);

        $s3_12 = SubElemen::updateOrCreate(['elemen_id' => $e3->id, 'kode_sub' => 'III.12'], ['nama_sub' => 'Pengelolaan Administrasi Keselamatan Pertambangan']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s3_12->id, 'kode_kriteria' => 'III.12.1'], [
            'deskripsi' => 'Pengelolaan dan pengisian Buku Tambang secara tertib.',
            'nilai_maksimal' => 4.00,
            'persyaratan_dokumen' => 'Fisik Buku Tambang yang disahkan KaIT dan terisi instruksi KTT/Inspector.',
            'pedoman_nilai_0' => 'Nilai 0: Tidak ada Buku Tambang.',
            'pedoman_nilai_1' => 'Nilai 1: Buku Tambang ada tetapi tidak disahkan KaIT.',
            'pedoman_nilai_2' => 'Nilai 2: Disahkan KaIT tetapi instruksi tidak rutin dicatat.',
            'pedoman_nilai_3' => 'Nilai 3: Disahkan KaIT, instruksi tercatat rapi.',
            'pedoman_nilai_4' => 'Nilai 4: Disahkan KaIT, instruksi terisi rapi, dan tindak lanjut instruksi dicatat lengkap.',
        ]);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s3_12->id, 'kode_kriteria' => 'III.12.2'], [
            'deskripsi' => 'Pengelolaan Buku Daftar Kecelakaan Tambang.',
            'nilai_maksimal' => 3.00,
            'persyaratan_dokumen' => 'Buku Daftar Kecelakaan Tambang sesuai format Kepdirjen 185.',
            'pedoman_nilai_0' => 'Nilai 0: Tidak ada Buku Daftar Kecelakaan.',
            'pedoman_nilai_1' => 'Nilai 1: Format tidak sesuai aturan Kepdirjen 185.',
            'pedoman_nilai_2' => 'Nilai 2: Format sesuai tetapi pengisian tidak ter-update.',
            'pedoman_nilai_3' => 'Nilai 3: Format sesuai Kepdirjen 185, terisi lengkap, dan di-update 100%.',
        ]);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s3_12->id, 'kode_kriteria' => 'III.12.3'], [
            'deskripsi' => 'Pelaporan berkala pengelolaan Keselamatan Pertambangan kepada instansi pemerintah.',
            'nilai_maksimal' => 3.00,
            'persyaratan_dokumen' => 'Tanda Terima Laporan Bulanan/Triwulanan/Tahunan KP ke KaIT ESDM.',
            'pedoman_nilai_0' => 'Nilai 0: Tidak ada pelaporan berkala.',
            'pedoman_nilai_1' => 'Nilai 1: Laporan dibuat tetapi terlambat diserahkan.',
            'pedoman_nilai_2' => 'Nilai 2: Laporan rutin tetapi bukti tanda terima tidak lengkap.',
            'pedoman_nilai_3' => 'Nilai 3: Laporan rutin diserahkan tepat waktu dengan tanda terima resmi lengkap.',
        ]);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s3_12->id, 'kode_kriteria' => 'III.12.4'], [
            'deskripsi' => 'Dokumentasi Kejadian Berbahaya, Kejadian Akibat Penyakit Tenaga Kerja, dan PAK.',
            'nilai_maksimal' => 3.00,
            'persyaratan_dokumen' => 'Laporan Investigasi Kejadian Berbahaya dan Kasus PAK.',
            'pedoman_nilai_0' => 'Nilai 0: Tidak ter-dokumentasi.',
            'pedoman_nilai_1' => 'Nilai 1: Dokumentasi tidak memuat investigasi penyebab.',
            'pedoman_nilai_2' => 'Nilai 2: Investigasi ada tetapi tindakan pencegahan belum selesai.',
            'pedoman_nilai_3' => 'Nilai 3: Dokumentasi & laporan investigasi komprehensif serta perbaikan selesai.',
        ]);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s3_12->id, 'kode_kriteria' => 'III.12.5'], [
            'deskripsi' => 'Dokumen dan Laporan Pemenuhan Kompetensi serta Persyaratan Lainnya.',
            'nilai_maksimal' => 3.00,
            'persyaratan_dokumen' => 'Laporan Rekapitulasi Pemenuhan Kompetensi Tenaga Kerja.',
            'pedoman_nilai_0' => 'Nilai 0: Tidak ada dokumen rekapitulasi.',
            'pedoman_nilai_1' => 'Nilai 1: Dokumentasi parsial.',
            'pedoman_nilai_2' => 'Nilai 2: Laporan lengkap tetapi tidak dilaporkan ke KTT.',
            'pedoman_nilai_3' => 'Nilai 3: Dokumen & laporan pemenuhan kompetensi lengkap dan ter-update.',
        ]);

        $s3_13 = SubElemen::updateOrCreate(['elemen_id' => $e3->id, 'kode_sub' => 'III.13'], ['nama_sub' => 'Penyusunan, Penerapan, dan Pendokumentasian Prosedur Partisipasi, Konsultasi, Motivasi, dan Kesadaran Penerapan SMKP Minerba']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s3_13->id, 'kode_kriteria' => 'III.13.1'], [
            'deskripsi' => 'Pelaksanaan program konsultasi, partisipasi, dan penghargaan/motivasi KP.',
            'nilai_maksimal' => 4.00,
            'persyaratan_dokumen' => 'Program Safety Reward & Punishment, Kotak Saran K3, & Rekaman Pelaksanaan.',
            'pedoman_nilai_0' => 'Nilai 0: Tidak ada program partisipasi & motivasi.',
            'pedoman_nilai_1' => 'Nilai 1: Ada masukan pekerja tetapi tidak pernah direspon.',
            'pedoman_nilai_2' => 'Nilai 2: Program berjalan tetapi reward/punishment belum transparan.',
            'pedoman_nilai_3' => 'Nilai 3: Program partisipasi & reward berjalan rutin dan transparan.',
            'pedoman_nilai_4' => 'Nilai 4: Partisipasi pekerja tinggi, program motivasi berkala, dan berdampak positif pada budaya K3.',
        ]);

        // =========================================================================
        // ELEMEN IV: IMPLEMENTASI (Bobot: 35.00%, Total Nilai Maksimal: 138)
        // =========================================================================
        $e4 = Elemen::updateOrCreate(['kode_elemen' => 'IV'], ['nama_elemen' => 'Implementasi', 'bobot' => 35.00]);

        $s4_1 = SubElemen::updateOrCreate(['elemen_id' => $e4->id, 'kode_sub' => 'IV.1'], ['nama_sub' => 'Pelaksanaan Pengelolaan Operasional']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s4_1->id, 'kode_kriteria' => 'IV.1.1'], [
            'deskripsi' => 'Penyusunan, Penetapan, Penerapan, Pendokumentasian, dan Evaluasi Prosedur Operasi/Kerja (SOP/JSA).',
            'nilai_maksimal' => 4.00,
            'persyaratan_dokumen' => 'Masterlist SOP/JSA Operasional Tambang & Hasil Evaluasi Berkala.',
            'pedoman_nilai_0' => 'Nilai 0: Tidak ada SOP/JSA.',
            'pedoman_nilai_1' => 'Nilai 1: SOP ada tetapi tidak disosialisasi.',
            'pedoman_nilai_2' => 'Nilai 2: SOP disosialisasi tetapi tidak dievaluasi.',
            'pedoman_nilai_3' => 'Nilai 3: SOP diterapkan dan dievaluasi berkala.',
            'pedoman_nilai_4' => 'Nilai 4: SOP/JSA lengkap, disosialisasikan, dipatuhi 100%, dan dievaluasi rutin.',
        ]);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s4_1->id, 'kode_kriteria' => 'IV.1.2'], [
            'deskripsi' => 'Penyusunan, Penetapan, Penerapan, Pendokumentasian, dan Evaluasi Izin Kerja Khusus (Work Permit).',
            'nilai_maksimal' => 3.00,
            'persyaratan_dokumen' => 'SOP & Form Izin Kerja Khusus (Panas, Ruang Terbatas, Ketinggian, Kelistrikan).',
            'pedoman_nilai_0' => 'Nilai 0: Tidak ada sistem Izin Kerja Khusus.',
            'pedoman_nilai_1' => 'Nilai 1: Permohonan izin kerja ada tetapi pengawasan di lapangan tidak ada.',
            'pedoman_nilai_2' => 'Nilai 2: Izin kerja diterapkan untuk sebagian pekerjaan tinggi risiko.',
            'pedoman_nilai_3' => 'Nilai 3: Izin Kerja Khusus diterapkan 100% untuk seluruh pekerjaan risiko tinggi.',
        ]);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s4_1->id, 'kode_kriteria' => 'IV.1.3'], [
            'deskripsi' => 'Penyusunan, Penetapan, Penerapan, Pendokumentasian, dan Evaluasi APD dan Alat Keselamatan.',
            'nilai_maksimal' => 4.00,
            'persyaratan_dokumen' => 'SOP Matriks Standar APD, Berita Acara Distribusi, & Inspeksi Kelayakan APD.',
            'pedoman_nilai_0' => 'Nilai 0: APD tidak disediakan perusahaan.',
            'pedoman_nilai_1' => 'Nilai 1: APD disediakan tetapi tidak sesuai standar matriks risiko.',
            'pedoman_nilai_2' => 'Nilai 2: APD standar disediakan tetapi pengawasan pemakaian di lapangan kurang.',
            'pedoman_nilai_3' => 'Nilai 3: APD standar terdistribusi 100% dan dipatuhi pekerja.',
            'pedoman_nilai_4' => 'Nilai 4: APD standar lengkap, terdistribusi gratis, inspeksi kelayakan rutin, dan kepatuhan 100%.',
        ]);

        $s4_2 = SubElemen::updateOrCreate(['elemen_id' => $e4->id, 'kode_sub' => 'IV.2'], ['nama_sub' => 'Pelaksanaan Pengelolaan Lingkungan Kerja']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s4_2->id, 'kode_kriteria' => 'IV.2.1'], ['deskripsi' => 'Pelaksanaan pengelolaan Bahaya Debu di area tambang.', 'nilai_maksimal' => 4.00, 'persyaratan_dokumen' => 'Laporan Pengukuran Kadar Debu & Program Penyiraman Jalan Tambang.']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s4_2->id, 'kode_kriteria' => 'IV.2.2'], ['deskripsi' => 'Pelaksanaan pengelolaan Bahaya Kebisingan.', 'nilai_maksimal' => 4.00, 'persyaratan_dokumen' => 'Dokumen Pemetaan Kebisingan (Noise Mapping) & Ear Protection Zone.']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s4_2->id, 'kode_kriteria' => 'IV.2.3'], ['deskripsi' => 'Pelaksanaan pengelolaan Bahaya Getaran alat berat & unit.', 'nilai_maksimal' => 4.00, 'persyaratan_dokumen' => 'Hasil Pengukuran Getaran Lengan-Tangan / Seluruh Tubuh.']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s4_2->id, 'kode_kriteria' => 'IV.2.4'], ['deskripsi' => 'Pelaksanaan pengelolaan Bahaya Pencahayaan di area kerja & malam hari.', 'nilai_maksimal' => 4.00, 'persyaratan_dokumen' => 'Hasil Pengukuran Lux Meter Area Operasional & Tower Lamp.']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s4_2->id, 'kode_kriteria' => 'IV.2.5'], ['deskripsi' => 'Pelaksanaan pengelolaan Kuantitas dan Kualitas Udara Kerja.', 'nilai_maksimal' => 4.00, 'persyaratan_dokumen' => 'Laporan Hasil Pengujian Udara Kerja / Ventilasi Tambang.']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s4_2->id, 'kode_kriteria' => 'IV.2.6'], ['deskripsi' => 'Pelaksanaan pengelolaan Iklim Kerja (Tekanan Panas / Heat Stress).', 'nilai_maksimal' => 4.00, 'persyaratan_dokumen' => 'Hasil Pengukuran ISBB & Penyediaan Water Station.']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s4_2->id, 'kode_kriteria' => 'IV.2.7'], ['deskripsi' => 'Pelaksanaan pengelolaan Bahaya Radiasi (bila ada).', 'nilai_maksimal' => 4.00, 'persyaratan_dokumen' => 'Pengujian Dosimeter & Proteksi Radiasi.']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s4_2->id, 'kode_kriteria' => 'IV.2.8'], ['deskripsi' => 'Pelaksanaan pengelolaan Faktor Kimia (MSDS / LDKB & Tempat B3).', 'nilai_maksimal' => 4.00, 'persyaratan_dokumen' => 'Pemasangan MSDS, Label B3, & Tempat Penyimpanan B3 Berizin.']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s4_2->id, 'kode_kriteria' => 'IV.2.9'], ['deskripsi' => 'Pelaksanaan pengelolaan Faktor Biologi (Vektor penyakit/sanitasi).', 'nilai_maksimal' => 4.00, 'persyaratan_dokumen' => 'Program Pest Control & Disinfeksi Lingkungan Kerja.']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s4_2->id, 'kode_kriteria' => 'IV.2.10'], ['deskripsi' => 'Pelaksanaan Kebersihan Lingkungan Kerja (Housekeeping / 5S).', 'nilai_maksimal' => 4.00, 'persyaratan_dokumen' => 'Jadwal & Checklist Inspeksi Housekeeping 5S.']);

        $s4_3 = SubElemen::updateOrCreate(['elemen_id' => $e4->id, 'kode_sub' => 'IV.3'], ['nama_sub' => 'Pelaksanaan Pengelolaan Kesehatan Kerja']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s4_3->id, 'kode_kriteria' => 'IV.3.1'], ['deskripsi' => 'Pemeriksaan Kesehatan Pekerja (Awal, Berkala, Khusus, Akhir).', 'nilai_maksimal' => 4.00, 'persyaratan_dokumen' => 'Laporan Rekapitulasi MCU Pekerja & Fit to Work.']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s4_3->id, 'kode_kriteria' => 'IV.3.2'], ['deskripsi' => 'Penyelenggaraan Pelayanan Kesehatan Kerja dan Klinik Tambang.', 'nilai_maksimal' => 2.00, 'persyaratan_dokumen' => 'Izin Operasional Klinik Tambang & Tenaga Medis Iperkes.']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s4_3->id, 'kode_kriteria' => 'IV.3.3'], ['deskripsi' => 'Pelaksanaan Pertolongan Pertama pada Kecelakaan (P3K).', 'nilai_maksimal' => 2.00, 'persyaratan_dokumen' => 'Checklist Isian Kotak P3K & Lisensi Petugas P3K.']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s4_3->id, 'kode_kriteria' => 'IV.3.4'], ['deskripsi' => 'Pengelolaan Kelelahan Kerja (Fatigue Management System).', 'nilai_maksimal' => 3.00, 'persyaratan_dokumen' => 'Prosedur Fatigue Test, Rest Area, & Pengaturan Jam Kerja.']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s4_3->id, 'kode_kriteria' => 'IV.3.5'], ['deskripsi' => 'Pengelolaan Pekerja pada Tempat Risiko Kesehatan Tinggi.', 'nilai_maksimal' => 2.00, 'persyaratan_dokumen' => 'Prosedur Rotasi Kerja Area Risiko Kesehatan Tinggi.']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s4_3->id, 'kode_kriteria' => 'IV.3.6'], ['deskripsi' => 'Pengelolaan Rekaman Data Kesehatan Kerja Pekerja.', 'nilai_maksimal' => 4.00, 'persyaratan_dokumen' => 'Arsip Data Medis Pekerja yang Rahasia & Terjaga.']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s4_3->id, 'kode_kriteria' => 'IV.3.7'], ['deskripsi' => 'Pengelolaan Higiene dan Sanitasi di Area Kerja & Mess.', 'nilai_maksimal' => 2.00, 'persyaratan_dokumen' => 'Hasil Pengujian Air Minum & Inspeksi Sanitasi Mess.']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s4_3->id, 'kode_kriteria' => 'IV.3.8'], ['deskripsi' => 'Pengelolaan Ergonomi Tempat Kerja.', 'nilai_maksimal' => 3.00, 'persyaratan_dokumen' => 'Asesmen Ergonomi Kursi Operator & Posisi Kerja.']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s4_3->id, 'kode_kriteria' => 'IV.3.9'], ['deskripsi' => 'Pengelolaan Makanan, Minuman, dan Gizi Pekerja Tambang.', 'nilai_maksimal' => 2.00, 'persyaratan_dokumen' => 'Sertifikat Laik Higiene Catering & Inspeksi Sampel Makanan.']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s4_3->id, 'kode_kriteria' => 'IV.3.10'], ['deskripsi' => 'Diagnosis dan Pemeriksaan Penyakit Akibat Kerja (PAK).', 'nilai_maksimal' => 4.00, 'persyaratan_dokumen' => 'Prosedur Diagnosis PAK oleh Dokter Spesialis Okupasi.']);

        $s4_4 = SubElemen::updateOrCreate(['elemen_id' => $e4->id, 'kode_sub' => 'IV.4'], ['nama_sub' => 'Pelaksanaan Pengelolaan KO Pertambangan']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s4_4->id, 'kode_kriteria' => 'IV.4.1'], ['deskripsi' => 'Sistem pemeliharaan/perawatan sarana, prasarana, instalasi, dan peralatan pertambangan.', 'nilai_maksimal' => 4.00, 'persyaratan_dokumen' => 'Jadwal Preventif Maintenance (PM) & Kartu Perawatan Unit.']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s4_4->id, 'kode_kriteria' => 'IV.4.2'], ['deskripsi' => 'Pengamanan instalasi dan proteksi peralatan pertambangan.', 'nilai_maksimal' => 4.00, 'persyaratan_dokumen' => 'Inspeksi Grounding, Protection System, & Loto.']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s4_4->id, 'kode_kriteria' => 'IV.4.3'], ['deskripsi' => 'Pengujian kelayakan (Commissioning) sarana, prasarana, instalasi, dan peralatan.', 'nilai_maksimal' => 4.00, 'persyaratan_dokumen' => 'Stiker & Sertifikat Commissioning Kelayakan Alat/Unit.']);
        $k4_4_4 = Kriteria::updateOrCreate(['sub_elemen_id' => $s4_4->id, 'kode_kriteria' => 'IV.4.4'], ['deskripsi' => 'Kompetensi tenaga teknik pemeliharaan & pengoperasian KO.', 'nilai_maksimal' => 4.00, 'persyaratan_dokumen' => 'Sertifikat KIM / SIO Operator & Mekanik.']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s4_4->id, 'kode_kriteria' => 'IV.4.5'], ['deskripsi' => 'Evaluasi Laporan Hasil Kajian Teknis Pertambangan.', 'nilai_maksimal' => 4.00, 'persyaratan_dokumen' => 'Dokumen Kajian Teknis Kestabilan Lereng/Kelistrikan/Geoteknik.']);

        $s4_5 = SubElemen::updateOrCreate(['elemen_id' => $e4->id, 'kode_sub' => 'IV.5'], ['nama_sub' => 'Pelaksanaan Pengelolaan Bahan Peledak dan Peledakan']);
        $k4_5_1 = Kriteria::updateOrCreate(['sub_elemen_id' => $s4_5->id, 'kode_kriteria' => 'IV.5.1'], ['deskripsi' => 'Kelayakan dan pengamanan Gudang Bahan Peledak.', 'nilai_maksimal' => 2.00, 'persyaratan_dokumen' => 'Izin Gudang Handak dari KaIT & Kepolisian.']);
        $k4_5_2 = Kriteria::updateOrCreate(['sub_elemen_id' => $s4_5->id, 'kode_kriteria' => 'IV.5.2'], ['deskripsi' => 'Tata cara penyimpanan bahan peledak.', 'nilai_maksimal' => 4.00, 'persyaratan_dokumen' => 'Buku Stok Handak & Pengaturan Temperatur Gudang.']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s4_5->id, 'kode_kriteria' => 'IV.5.3'], ['deskripsi' => 'Pengangkutan bahan peledak di area pertambangan.', 'nilai_maksimal' => 4.00, 'persyaratan_dokumen' => 'Izin Mobil Angkut Handak & Pengawalan Khusus.']);
        $k4_5_4 = Kriteria::updateOrCreate(['sub_elemen_id' => $s4_5->id, 'kode_kriteria' => 'IV.5.4'], [
            'deskripsi' => 'Pelaksanaan pekerjaan peledakan dan keahlian Juru Ledak.',
            'nilai_maksimal' => 4.00,
            'persyaratan_dokumen' => 'KIM Juru Ledak & Blasting Clearance Logbook.',
            'dependency_id' => $k3_6_1->id,
            'dependency_note' => 'Pelaksanaan peledakan bergantung pada ketersediaan Tenaga Teknik Khusus (Juru Ledak / KIM) yang sah.',
        ]);

        $s4_6 = SubElemen::updateOrCreate(['elemen_id' => $e4->id, 'kode_sub' => 'IV.6'], ['nama_sub' => 'Penetapan Sistem Perancangan dan Rekayasa']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s4_6->id, 'kode_kriteria' => 'IV.6.1'], ['deskripsi' => 'Prosedur Perancangan dan Rekayasa Teknis Pertambangan.', 'nilai_maksimal' => 3.00, 'persyaratan_dokumen' => 'SOP Desain Tambang & Feasibility Study.']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s4_6->id, 'kode_kriteria' => 'IV.6.2'], ['deskripsi' => 'Manajemen Perubahan (Management of Change / MOC).', 'nilai_maksimal' => 3.00, 'persyaratan_dokumen' => 'Form Pengajuan MOC & Analisis Risiko Perubahan Desain.']);

        $s4_7 = SubElemen::updateOrCreate(['elemen_id' => $e4->id, 'kode_sub' => 'IV.7'], ['nama_sub' => 'Penetapan Sistem Pembelian']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s4_7->id, 'kode_kriteria' => 'IV.7.1'], ['deskripsi' => 'Penetapan Sistem Pembelian barang/jasa yang memenuhi standar KP.', 'nilai_maksimal' => 4.00, 'persyaratan_dokumen' => 'SOP Procurement & Spesifikasi K3KO Pembelian Barang.']);

        $s4_8 = SubElemen::updateOrCreate(['elemen_id' => $e4->id, 'kode_sub' => 'IV.8'], ['nama_sub' => 'Pemantauan dan Pengelolaan Perusahaan Jasa Pertambangan']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s4_8->id, 'kode_kriteria' => 'IV.8.1'], ['deskripsi' => 'Persyaratan, seleksi, dan penetapan perusahaan jasa pertambangan (Kontraktor/Subkon).', 'nilai_maksimal' => 2.00, 'persyaratan_dokumen' => 'Dokumen CSMS (Contractor Safety Management System).']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s4_8->id, 'kode_kriteria' => 'IV.8.2'], ['deskripsi' => 'Tanggung jawab, pemantauan, dan pelaporan perusahaan jasa pertambangan.', 'nilai_maksimal' => 2.00, 'persyaratan_dokumen' => 'Laporan Kinerja K3KO Kontraktor Bulanan.']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s4_8->id, 'kode_kriteria' => 'IV.8.3'], ['deskripsi' => 'Evaluasi kinerja perusahaan jasa pertambangan berkala.', 'nilai_maksimal' => 2.00, 'persyaratan_dokumen' => 'Dokumen KPI & Evaluasi Kontraktor Tahunan.']);

        $s4_9 = SubElemen::updateOrCreate(['elemen_id' => $e4->id, 'kode_sub' => 'IV.9'], ['nama_sub' => 'Pengelolaan Keadaan Darurat']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s4_9->id, 'kode_kriteria' => 'IV.9.1'], ['deskripsi' => 'Kesiapsiagaan dan Pengelolaan Keadaan Darurat Pertambangan.', 'nilai_maksimal' => 3.00, 'persyaratan_dokumen' => 'Pedoman Emergency Response Plan (ERP) & Laporan Drill/Simulasi.']);

        $s4_10 = SubElemen::updateOrCreate(['elemen_id' => $e4->id, 'kode_sub' => 'IV.10'], ['nama_sub' => 'Penyediaan dan Penyiapan P3K']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s4_10->id, 'kode_kriteria' => 'IV.10.1'], ['deskripsi' => 'Penyediaan Kotak P3K, Petugas P3K, dan Fasilitas Pertolongan Pertama.', 'nilai_maksimal' => 3.00, 'persyaratan_dokumen' => 'Checklist Ketersediaan Obat P3K & Fasilitas Ambulans Tambang.']);

        $s4_11 = SubElemen::updateOrCreate(['elemen_id' => $e4->id, 'kode_sub' => 'IV.11'], ['nama_sub' => 'Pelaksanaan keselamatan di luar pekerjaan (off the job safety)']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s4_11->id, 'kode_kriteria' => 'IV.11.1'], ['deskripsi' => 'Program Pelaksanaan Keselamatan di Luar Pekerjaan (Off the Job Safety).', 'nilai_maksimal' => 3.00, 'persyaratan_dokumen' => 'Poster / Pamphlet Safety Driving & Off the Job Campaign.']);

        // =========================================================================
        // ELEMEN V: PEMANTAUAN, EVALUASI DAN TINDAK LANJUT (Bobot: 15.00%, Total Nilai Maksimal: 60)
        // =========================================================================
        $e5 = Elemen::updateOrCreate(['kode_elemen' => 'V'], ['nama_elemen' => 'Pemantauan, Evaluasi dan Tindak Lanjut', 'bobot' => 15.00]);

        $s5_1 = SubElemen::updateOrCreate(['elemen_id' => $e5->id, 'kode_sub' => 'V.1'], ['nama_sub' => 'Pemantauan dan pengukuran kinerja']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s5_1->id, 'kode_kriteria' => 'V.1.1'], ['deskripsi' => 'Pemantauan dan Pengukuran Pencapaian Tujuan, Sasaran, dan Program KP.', 'nilai_maksimal' => 4.00, 'persyaratan_dokumen' => 'Dashboard KPI K3KO & Laporan Progres Program Tahunan.']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s5_1->id, 'kode_kriteria' => 'V.1.2'], ['deskripsi' => 'Pemantauan dan Pengukuran Kinerja Pengelolaan Lingkungan Kerja.', 'nilai_maksimal' => 4.00, 'persyaratan_dokumen' => 'Laporan Hasil Pengujian Lingkungan Kerja dari Lab Terakreditasi.']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s5_1->id, 'kode_kriteria' => 'V.1.3'], ['deskripsi' => 'Pemantauan dan Pengukuran Kinerja Pengelolaan Kesehatan Kerja.', 'nilai_maksimal' => 4.00, 'persyaratan_dokumen' => 'Laporan Tren Kesehatan Pekerja & Kasus Morbiditas.']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s5_1->id, 'kode_kriteria' => 'V.1.4'], ['deskripsi' => 'Pemantauan dan Pengukuran Kinerja Pengelolaan KO Pertambangan.', 'nilai_maksimal' => 4.00, 'persyaratan_dokumen' => 'Laporan Physical Availability (PA) & Failure Analysis Unit.']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s5_1->id, 'kode_kriteria' => 'V.1.5'], ['deskripsi' => 'Pemantauan dan Pengukuran Kinerja Pengelolaan Bahan Peledak dan Peledakan.', 'nilai_maksimal' => 4.00, 'persyaratan_dokumen' => 'Laporan Evaluasi Vibration & Flyrock Blasting Monitor.']);

        $s5_2 = SubElemen::updateOrCreate(['elemen_id' => $e5->id, 'kode_sub' => 'V.2'], ['nama_sub' => 'Inspeksi Pelaksanaan Keselamatan Pertambangan']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s5_2->id, 'kode_kriteria' => 'V.2.1'], ['deskripsi' => 'Pelaksanaan Inspeksi Keselamatan Pertambangan berkala.', 'nilai_maksimal' => 4.00, 'persyaratan_dokumen' => 'Jadwal & Laporan Inspeksi Manajemen / Pengawas Lapangan.']);

        $s5_3 = SubElemen::updateOrCreate(['elemen_id' => $e5->id, 'kode_sub' => 'V.3'], ['nama_sub' => 'Evaluasi kepatuhan Terhadap Ketentuan Peraturan Perundang-Undangan dan Persyaratan Lainnya Yang Terkait']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s5_3->id, 'kode_kriteria' => 'V.3.1'], ['deskripsi' => 'Evaluasi Kepatuhan Peraturan Perundang-undangan dan Persyaratan Lainnya.', 'nilai_maksimal' => 4.00, 'persyaratan_dokumen' => 'Risalah Evaluasi Kepatuhan Peraturan ESDM & Ketenagakerjaan.']);

        $s5_4 = SubElemen::updateOrCreate(['elemen_id' => $e5->id, 'kode_sub' => 'V.4'], ['nama_sub' => 'Penyelidikan Kecelakaan, Kejadian Berbahaya, dan Penyakit Akibat Kerja']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s5_4->id, 'kode_kriteria' => 'V.4.1'], ['deskripsi' => 'Prosedur dan pelaksanaan penyelidikan kecelakaan, kejadian berbahaya, dan PAK.', 'nilai_maksimal' => 4.00, 'persyaratan_dokumen' => 'Laporan Investigasi Kecelakaan (Model NTS) & Root Cause Analysis (RCA).']);

        $s5_5 = SubElemen::updateOrCreate(['elemen_id' => $e5->id, 'kode_sub' => 'V.5'], ['nama_sub' => 'Evaluasi Pengelolaan Administrasi Keselamatan Pertambangan']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s5_5->id, 'kode_kriteria' => 'V.5.1'], ['deskripsi' => 'Evaluasi Pengelolaan Buku Tambang.', 'nilai_maksimal' => 4.00, 'persyaratan_dokumen' => 'Checklist Evaluasi Pelaksanaan Instruksi Buku Tambang.']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s5_5->id, 'kode_kriteria' => 'V.5.2'], ['deskripsi' => 'Evaluasi Pengelolaan Buku Daftar Kecelakaan Tambang.', 'nilai_maksimal' => 4.00, 'persyaratan_dokumen' => 'Rekapitulasi Frequence Rate (FR) & Severity Rate (SR) Kecelakaan.']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s5_5->id, 'kode_kriteria' => 'V.5.3'], ['deskripsi' => 'Evaluasi Pelaporan Pengelolaan Keselamatan Pertambangan.', 'nilai_maksimal' => 4.00, 'persyaratan_dokumen' => 'Arsip Tanda Terima Laporan Resmi ke KaIT.']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s5_5->id, 'kode_kriteria' => 'V.5.4'], ['deskripsi' => 'Evaluasi Dokumentasi Kejadian Berbahaya dan PAK.', 'nilai_maksimal' => 4.00, 'persyaratan_dokumen' => 'Laporan Evaluasi Tren Kejadian Berbahaya Tahunan.']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s5_5->id, 'kode_kriteria' => 'V.5.5'], ['deskripsi' => 'Evaluasi Dokumentasi dan Laporan Pemenuhan Kompetensi.', 'nilai_maksimal' => 4.00, 'persyaratan_dokumen' => 'Dokumen Pemenuhan Matrix Sertifikasi Kompetensi Perusahaan.']);

        $s5_6 = SubElemen::updateOrCreate(['elemen_id' => $e5->id, 'kode_sub' => 'V.6'], ['nama_sub' => 'Audit Internal Penerapan SMKP Minerba atau SMKP Khusus untuk pengolahan dan/atau pemurnian']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s5_6->id, 'kode_kriteria' => 'V.6.1'], ['deskripsi' => 'Pelaksanaan Audit Internal Penerapan SMKP Minerba secara berkala.', 'nilai_maksimal' => 4.00, 'persyaratan_dokumen' => 'SK Auditor Internal Terdaftar ESDM & Laporan Audit Internal SMKP.']);

        $s5_7 = SubElemen::updateOrCreate(['elemen_id' => $e5->id, 'kode_sub' => 'V.7'], ['nama_sub' => 'Rencana Perbaikan dan Tindak Lanjut']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s5_7->id, 'kode_kriteria' => 'V.7.1'], ['deskripsi' => 'Penyusunan Rencana Perbaikan dan Tindak Lanjut (CAPA / PICA) dari hasil pemantauan & audit.', 'nilai_maksimal' => 4.00, 'persyaratan_dokumen' => 'Matriks PICA / CAPA Audit & Status Closure Temuan.']);

        // =========================================================================
        // ELEMEN VI: DOKUMENTASI (Bobot: 3.00%, Total Nilai Maksimal: 12)
        // =========================================================================
        $e6 = Elemen::updateOrCreate(['kode_elemen' => 'VI'], ['nama_elemen' => 'Dokumentasi', 'bobot' => 3.00]);

        $s6_1 = SubElemen::updateOrCreate(['elemen_id' => $e6->id, 'kode_sub' => 'VI.1'], ['nama_sub' => 'Penyusunan Penetapan dan Pendokumentasian Manual SMKP Minerba atau SMKP Khusus pada Pengolahan dan/atau Pemurnian']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s6_1->id, 'kode_kriteria' => 'VI.1.1'], ['deskripsi' => 'Manual SMKP Minerba terdokumentasi dan ditetapkan pimpinan puncak.', 'nilai_maksimal' => 4.00, 'persyaratan_dokumen' => 'Dokumen Manual SMKP Minerba Perusahaan yang disahkan KTT.']);

        $s6_2 = SubElemen::updateOrCreate(['elemen_id' => $e6->id, 'kode_sub' => 'VI.2'], ['nama_sub' => 'Penyusunan Penetapan, Penerapan dan Pendokumentasian Prosedur Pengendalian Dokumen Keselamatan Pertambangan']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s6_2->id, 'kode_kriteria' => 'VI.2.1'], ['deskripsi' => 'Prosedur Pengendalian Dokumen Keselamatan Pertambangan.', 'nilai_maksimal' => 3.00, 'persyaratan_dokumen' => 'SOP Pengendalian Dokumen & Masterlist Dokumen Terkendali.']);

        $s6_3 = SubElemen::updateOrCreate(['elemen_id' => $e6->id, 'kode_sub' => 'VI.3'], ['nama_sub' => 'Penyusunan Penetapan, Penerapan dan Pendokumentasian Prosedur Pengendalian Rekaman Keselamatan Pertambangan']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s6_3->id, 'kode_kriteria' => 'VI.3.1'], ['deskripsi' => 'Prosedur Pengendalian Rekaman Keselamatan Pertambangan.', 'nilai_maksimal' => 3.00, 'persyaratan_dokumen' => 'SOP Pengendalian Rekaman & Jadwal Retensi Arsip KP.']);

        $s6_4 = SubElemen::updateOrCreate(['elemen_id' => $e6->id, 'kode_sub' => 'VI.4'], ['nama_sub' => 'Penetapan Jenis Dokumen dan Rekaman']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s6_4->id, 'kode_kriteria' => 'VI.4.1'], ['deskripsi' => 'Penetapan Jenis Dokumen dan Rekaman Keselamatan Pertambangan.', 'nilai_maksimal' => 2.00, 'persyaratan_dokumen' => 'Daftar Matriks Identifikasi Jenis Dokumen & Rekaman Wajib.']);

        // =========================================================================
        // ELEMEN VII: TINJAUAN MANAJEMEN DAN PENINGKATAN KINERJA (Bobot: 5.00%, Total Nilai Maksimal: 13)
        // =========================================================================
        $e7 = Elemen::updateOrCreate(['kode_elemen' => 'VII'], ['nama_elemen' => 'Tinjauan Manajemen dan Peningkatan Kinerja', 'bobot' => 5.00]);

        $s7_1 = SubElemen::updateOrCreate(['elemen_id' => $e7->id, 'kode_sub' => 'VII.1'], ['nama_sub' => 'Pelaksanaan Tinjauan Manajemen Penerapan SMKP Minerba atau SMKP Khusus pada Pengolahan dan/atau Pemurnian oleh Manajemen Tertinggi Perusahaan']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s7_1->id, 'kode_kriteria' => 'VII.1.1'], ['deskripsi' => 'Pelaksanaan Tinjauan Manajemen berkala oleh Manajemen Tertinggi.', 'nilai_maksimal' => 4.00, 'persyaratan_dokumen' => 'Notulen Rapat Tinjauan Manajemen Puncak (Management Review).']);

        $s7_2 = SubElemen::updateOrCreate(['elemen_id' => $e7->id, 'kode_sub' => 'VII.2'], ['nama_sub' => 'Pendokumentasian Catatan Hasil Tinjauan Manajemen']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s7_2->id, 'kode_kriteria' => 'VII.2.1'], ['deskripsi' => 'Pendokumentasian Catatan dan Risalah Hasil Tinjauan Manajemen.', 'nilai_maksimal' => 2.00, 'persyaratan_dokumen' => 'Dokumen Risalah Catatan Tinjauan Manajemen yang Disahkan.']);

        $s7_3 = SubElemen::updateOrCreate(['elemen_id' => $e7->id, 'kode_sub' => 'VII.3'], ['nama_sub' => 'Keluaran dari Tinjauan Manajemen Keselamatan Pertambangan']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s7_3->id, 'kode_kriteria' => 'VII.3.1'], ['deskripsi' => 'Penetapan Keluaran (Output) dan Tindak Lanjut Tinjauan Manajemen.', 'nilai_maksimal' => 2.00, 'persyaratan_dokumen' => 'Dokumen Rencana Aksi Output Tinjauan Manajemen Puncak.']);

        $s7_4 = SubElemen::updateOrCreate(['elemen_id' => $e7->id, 'kode_sub' => 'VII.4'], ['nama_sub' => 'Pencatatan, Pendokumentasian, dan Pelaporan Hasil Tinjauan Manajemen']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s7_4->id, 'kode_kriteria' => 'VII.4.1'], ['deskripsi' => 'Pencatatan dan Pelaporan Hasil Tinjauan Manajemen kepada Pihak Terkait.', 'nilai_maksimal' => 2.00, 'persyaratan_dokumen' => 'Bukti Sosialisasi Output Tinjauan Manajemen ke Seluruh Divisi.']);

        $s7_5 = SubElemen::updateOrCreate(['elemen_id' => $e7->id, 'kode_sub' => 'VII.5'], ['nama_sub' => 'Pelaksanaan Peningkatan Kinerja']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s7_5->id, 'kode_kriteria' => 'VII.5.1'], ['deskripsi' => 'Pelaksanaan Peningkatan Kinerja Keselamatan Pertambangan Berkelanjutan.', 'nilai_maksimal' => 1.00, 'persyaratan_dokumen' => 'Laporan Inovasi / Continual Improvement Program K3KO.']);

        $s7_6 = SubElemen::updateOrCreate(['elemen_id' => $e7->id, 'kode_sub' => 'VII.6'], ['nama_sub' => 'Penggunaan Tinjauan Hasil dari Tindak Lanjut Rencana Perbaikan dalam Penentuan Kebijakan']);
        Kriteria::updateOrCreate(['sub_elemen_id' => $s7_6->id, 'kode_kriteria' => 'VII.6.1'], ['deskripsi' => 'Penggunaan Tinjauan Hasil Perbaikan sebagai Masukan Pembaharuan Kebijakan KP.', 'nilai_maksimal' => 2.00, 'persyaratan_dokumen' => 'Revisi Kebijakan KP berdasarkan Hasil Tinjauan Manajemen.']);
    }
}
