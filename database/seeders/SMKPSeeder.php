<?php

namespace Database\Seeders;

use App\Models\Elemen;
use App\Models\SubElemen;
use App\Models\Kriteria;
use Illuminate\Database\Seeder;

class SMKPSeeder extends Seeder
{
    /**
     * Run the database seeds for SMKP Minerba elements, sub-elements, and criteria
     * based on Kepdirjen 185.K/37.04/DJB/2019.
     */
    public function run(): void
    {
        // Elemen I: Kebijakan Keselamatan Pertambangan (Bobot 20.00)
        $elemen1 = Elemen::updateOrCreate(
            ['kode_elemen' => 'I'],
            [
                'nama_elemen' => 'Kebijakan Keselamatan Pertambangan',
                'bobot' => 20.00,
            ]
        );

        $sub1_1 = SubElemen::updateOrCreate(
            ['elemen_id' => $elemen1->id, 'kode_sub' => 'I.1'],
            ['nama_sub' => 'Penyusunan Kebijakan Keselamatan Pertambangan']
        );

        Kriteria::updateOrCreate(
            ['sub_elemen_id' => $sub1_1->id, 'kode_kriteria' => 'I.1.1'],
            [
                'deskripsi' => 'Penyusunan dan penetapan kebijakan Keselamatan Pertambangan secara tertulis oleh pimpinan tertinggi.',
                'nilai_maksimal' => 4.00,
                'persyaratan_dokumen' => 'Dokumen Kebijakan Keselamatan Pertambangan tertulis, ditandatangani oleh KTT / Pimpinan Tertinggi perusahaan, dan mencantumkan tanggal penetapan.',
                'pedoman_nilai_0' => 'Nilai 0: Tidak ada kebijakan tertulis Keselamatan Pertambangan.',
                'pedoman_nilai_1' => 'Nilai 1: Terdapat draft kebijakan tetapi belum ditandatangani oleh Pimpinan Tertinggi.',
                'pedoman_nilai_2' => 'Nilai 2: Kebijakan telah ditandatangani tetapi belum mencakup seluruh prinsip Keselamatan Pertambangan (K3 & KO).',
                'pedoman_nilai_3' => 'Nilai 3: Kebijakan lengkap & ditandatangani tetapi belum ditinjau berkala.',
                'pedoman_nilai_4' => 'Nilai 4: Kebijakan tertulis, ditandatangani pimpinan tertinggi, mencakup K3 & KO, serta ditinjau berkala secara terdokumentasi.',
            ]
        );

        Kriteria::updateOrCreate(
            ['sub_elemen_id' => $sub1_1->id, 'kode_kriteria' => 'I.1.2'],
            [
                'deskripsi' => 'Isi kebijakan memuat komitmen manajemen puncak terhadap pencegahan kecelakaan dan penyakit akibat kerja.',
                'nilai_maksimal' => 4.00,
                'persyaratan_dokumen' => 'Teks kebijakan yang secara eksplisit memuat komitmen pencegahan kecelakaan, kejadian berbahaya, PAK, dan pencapaian kinerja Keselamatan Pertambangan.',
                'pedoman_nilai_0' => 'Nilai 0: Kebijakan tidak memuat komitmen Keselamatan Pertambangan.',
                'pedoman_nilai_1' => 'Nilai 1: Komitmen bersifat umum tanpa secara spesifik menyebut K3 & KO Pertambangan.',
                'pedoman_nilai_2' => 'Nilai 2: Komitmen memuat K3 tetapi tidak memuat komitmen Keselamatan Operasional (KO) Pertambangan.',
                'pedoman_nilai_3' => 'Nilai 3: Komitmen memuat K3 & KO tetapi belum menyelaraskan dengan peningkatan berkelanjutan.',
                'pedoman_nilai_4' => 'Nilai 4: Komitmen memuat K3 & KO secara jelas, peningkatan berkelanjutan, dan kepatuhan perundang-undangan.',
            ]
        );

        $sub1_2 = SubElemen::updateOrCreate(
            ['elemen_id' => $elemen1->id, 'kode_sub' => 'I.2'],
            ['nama_sub' => 'Sosialisasi dan Komunikasi Kebijakan']
        );

        Kriteria::updateOrCreate(
            ['sub_elemen_id' => $sub1_2->id, 'kode_kriteria' => 'I.2.1'],
            [
                'deskripsi' => 'Kebijakan disosialisasikan dan dikomunikasikan kepada seluruh pekerja pertambangan dan mitra kerja.',
                'nilai_maksimal' => 4.00,
                'persyaratan_dokumen' => 'Bukti sosialisasi kebijakan (Daftar hadir induksi/safety briefing, foto papan pengumuman, spanduk, booklet, dan catatan evaluasi pemahaman pekerja).',
                'pedoman_nilai_0' => 'Nilai 0: Tidak ada sosialisasi kebijakan kepada pekerja.',
                'pedoman_nilai_1' => 'Nilai 1: Kebijakan dipasang di papan pengumuman tanpa ada sosialisasi tatap muka/induksi.',
                'pedoman_nilai_2' => 'Nilai 2: Sosialisasi dilakukan hanya kepada pekerja internal, mitra kerja belum terpapar.',
                'pedoman_nilai_3' => 'Nilai 3: Sosialisasi dilakukan kepada seluruh pekerja internal & mitra kerja tetapi belum diuji pemahamannya.',
                'pedoman_nilai_4' => 'Nilai 4: Sosialisasi merata ke seluruh pekerja & kontraktor, terpajang di area kerja, dan hasil uji pemahaman menunjukkan tingkat pemahaman >85%.',
            ]
        );


        // Elemen II: Perencanaan Keselamatan Pertambangan (Bobot 20.00)
        $elemen2 = Elemen::updateOrCreate(
            ['kode_elemen' => 'II'],
            [
                'nama_elemen' => 'Perencanaan Keselamatan Pertambangan',
                'bobot' => 20.00,
            ]
        );

        $sub2_1 = SubElemen::updateOrCreate(
            ['elemen_id' => $elemen2->id, 'kode_sub' => 'II.1'],
            ['nama_sub' => 'Manajemen Risiko Pertambangan']
        );

        Kriteria::updateOrCreate(
            ['sub_elemen_id' => $sub2_1->id, 'kode_kriteria' => 'II.1.1'],
            [
                'deskripsi' => 'Prosedur manajemen risiko (Identifikasi Bahaya, Penilaian Risiko, & Pengendalian Risiko / IBPR) terdokumentasi dan dilaksanakan.',
                'nilai_maksimal' => 4.00,
                'persyaratan_dokumen' => 'SOP Manajemen Risiko, Dokumen Matriks HIRADC / IBPR terupdate untuk seluruh aktivitas rutin dan non-rutin operasional tambang.',
                'pedoman_nilai_0' => 'Nilai 0: Tidak ada Prosedur & Dokumen IBPR / Manajemen Risiko.',
                'pedoman_nilai_1' => 'Nilai 1: Ada Prosedur Manajemen Risiko tetapi belum memuat dokumen IBPR area kerja.',
                'pedoman_nilai_2' => 'Nilai 2: Dokumen IBPR ada tetapi hanya mencakup aktivitas rutin, aktivitas berbahaya/non-rutin belum tercakup.',
                'pedoman_nilai_3' => 'Nilai 3: IBPR mencakup seluruh aktivitas rutin & non-rutin tetapi penetapan hierarki pengendalian belum spesifik.',
                'pedoman_nilai_4' => 'Nilai 4: IBPR komprehensif (rutin & non-rutin), menerapkan hierarki pengendalian (Eliminasi, Subtitusi, Rekayasa, Adm, APD), dan ditinjau rutin berkala.',
            ]
        );

        Kriteria::updateOrCreate(
            ['sub_elemen_id' => $sub2_1->id, 'kode_kriteria' => 'II.1.2'],
            [
                'deskripsi' => 'Penetapan penanggung jawab dan batas waktu pelaksanaan rekomendasi pengendalian risiko.',
                'nilai_maksimal' => 4.00,
                'persyaratan_dokumen' => 'Form Action Plan IBPR memuat PIC nama/jabatan, target tanggal selesainya pengendalian, dan bukti tindak lanjut.',
                'pedoman_nilai_0' => 'Nilai 0: Tidak ada penetapan PIC & batas waktu pada IBPR.',
                'pedoman_nilai_1' => 'Nilai 1: Terdapat PIC tetapi tidak ada batas waktu target perbaikan.',
                'pedoman_nilai_2' => 'Nilai 2: Terdapat PIC dan target waktu tetapi progres tindak lanjut tidak dipantau.',
                'pedoman_nilai_3' => 'Nilai 3: Dipantau berkala tetapi penyelesaian tindakan pengendalian masih tertunda >20%.',
                'pedoman_nilai_4' => 'Nilai 4: PIC & target waktu ditetapkan jelas, dipantau berkala, dan penyelesaian tindakan pengendalian >95% tepat waktu.',
            ]
        );

        $sub2_2 = SubElemen::updateOrCreate(
            ['elemen_id' => $elemen2->id, 'kode_sub' => 'II.2'],
            ['nama_sub' => 'Peraturan Perundang-undangan dan Persyaratan Lainnya']
        );

        Kriteria::updateOrCreate(
            ['sub_elemen_id' => $sub2_2->id, 'kode_kriteria' => 'II.2.1'],
            [
                'deskripsi' => 'Inventarisasi, pembaruan, dan evaluasi kepatuhan terhadap peraturan perundang-undangan KP.',
                'nilai_maksimal' => 4.00,
                'persyaratan_dokumen' => 'Daftar Matriks Regulasi Keselamatan Pertambangan (UU, PP, Permen ESDM, Kepdirjen 185, dll) dan Berita Acara Evaluasi Kepatuhan Hukum.',
                'pedoman_nilai_0' => 'Nilai 0: Tidak ada daftar inventarisasi peraturan perundang-undangan KP.',
                'pedoman_nilai_1' => 'Nilai 1: Ada daftar peraturan tetapi tidak pernah di-update atau dievaluasi kepatuhannya.',
                'pedoman_nilai_2' => 'Nilai 2: Daftar peraturan ter-update tetapi evaluasi pemenuhan baru mencakup parsial regulasi.',
                'pedoman_nilai_3' => 'Nilai 3: Evaluasi kepatuhan dilakukan rutin tetapi tindak lanjut ketidaksesuaian belum tuntas.',
                'pedoman_nilai_4' => 'Nilai 4: Inventarisasi lengkap, di-update rutin, evaluasi kepatuhan terdokumentasi 100%, dan ketidaksesuaian ditindaklanjuti.',
            ]
        );

        // Elemen III: Organisasi dan Personel (Bobot 15.00)
        $elemen3 = Elemen::updateOrCreate(
            ['kode_elemen' => 'III'],
            [
                'nama_elemen' => 'Organisasi dan Personel',
                'bobot' => 15.00,
            ]
        );

        $sub3_1 = SubElemen::updateOrCreate(
            ['elemen_id' => $elemen3->id, 'kode_sub' => 'III.1'],
            ['nama_sub' => 'Penunjukan KTT, PTT, dan Komite Keselamatan Pertambangan']
        );

        Kriteria::updateOrCreate(
            ['sub_elemen_id' => $sub3_1->id, 'kode_kriteria' => 'III.1.1'],
            [
                'deskripsi' => 'Penunjukan Kepala Teknik Tambang (KTT) / Penanggung Jawab Operasional (PJO) dan Komite KP resmi.',
                'nilai_maksimal' => 4.00,
                'persyaratan_dokumen' => 'Surat Pengesahan KTT dari KaIT/KAPIT, Surat Penunjukan PJO, Struktur Organisasi Komite KP, dan Notulen Rapat Bulanan Komite KP.',
                'pedoman_nilai_0' => 'Nilai 0: KTT / PJO belum disahkan KaIT dan tidak ada Komite KP.',
                'pedoman_nilai_1' => 'Nilai 1: KTT sudah disahkan tetapi Komite KP belum dibentuk resmi.',
                'pedoman_nilai_2' => 'Nilai 2: Komite KP dibentuk tetapi rapat komite tidak berjalan rutin setiap bulan.',
                'pedoman_nilai_3' => 'Nilai 3: Rapat komite berjalan rutin bulanan tetapi rekomendasi tidak ditindaklanjuti.',
                'pedoman_nilai_4' => 'Nilai 4: Pengesahan KTT/PJO lengkap, Komite rapat rutin bulanan terdokumentasi, dan seluruh rekomendasi ditindaklanjuti.',
            ]
        );


        // Elemen IV: Implementasi (Bobot 20.00)
        $elemen4 = Elemen::updateOrCreate(
            ['kode_elemen' => 'IV'],
            [
                'nama_elemen' => 'Implementasi Keselamatan Pertambangan',
                'bobot' => 20.00,
            ]
        );

        $sub4_1 = SubElemen::updateOrCreate(
            ['elemen_id' => $elemen4->id, 'kode_sub' => 'IV.1'],
            ['nama_sub' => 'Pengelolaan Operasional dan Lingkungan Kerja']
        );

        Kriteria::updateOrCreate(
            ['sub_elemen_id' => $sub4_1->id, 'kode_kriteria' => 'IV.1.1'],
            [
                'deskripsi' => 'Pelaksanaan Petunjuk Operasional (SOP/JSA) pada pekerjaan berisiko tinggi di tambang.',
                'nilai_maksimal' => 4.00,
                'persyaratan_dokumen' => 'Dokumen SOP / JSA pekerjaan kritis (peledakan, pit slope, hauling, kelistrikan), Izin Kerja Khusus (Work Permit), dan Lembar Pemantauan Lapangan.',
                'pedoman_nilai_0' => 'Nilai 0: Tidak ada SOP / JSA untuk pekerjaan berisiko tinggi.',
                'pedoman_nilai_1' => 'Nilai 1: Ada SOP tetapi pekerja tidak dibekali sosialisasi SOP/JSA di lapangan.',
                'pedoman_nilai_2' => 'Nilai 2: SOP/JSA ada dan disosialisasikan tetapi pengawasan kepatuhan di lapangan lemah.',
                'pedoman_nilai_3' => 'Nilai 3: Pekerja patuh SOP/JSA tetapi Work Permit belum diterapkan konsisten.',
                'pedoman_nilai_4' => 'Nilai 4: SOP/JSA terupdate, Work Permit aktif, dan inspeksi lapangan konsisten 100%.',
            ]
        );


        // Elemen V: Evaluasi dan Kinerja (Bobot 15.00)
        $elemen5 = Elemen::updateOrCreate(
            ['kode_elemen' => 'V'],
            [
                'nama_elemen' => 'Evaluasi dan Kinerja Keselamatan Pertambangan',
                'bobot' => 15.00,
            ]
        );

        $sub5_1 = SubElemen::updateOrCreate(
            ['elemen_id' => $elemen5->id, 'kode_sub' => 'V.1'],
            ['nama_sub' => 'Inspeksi Keselamatan Pertambangan dan Audit Internal']
        );

        Kriteria::updateOrCreate(
            ['sub_elemen_id' => $sub5_1->id, 'kode_kriteria' => 'V.1.1'],
            [
                'deskripsi' => 'Pelaksanaan inspeksi Keselamatan Pertambangan berkala dan penanganan temuan ketidaksesuaian.',
                'nilai_maksimal' => 4.00,
                'persyaratan_dokumen' => 'Jadwal Inspeksi KP, Laporan Hasil Inspeksi Lapangan, Form CAPA (Corrective and Preventive Action), dan Bukti Closing Temuan.',
                'pedoman_nilai_0' => 'Nilai 0: Tidak ada jadwal dan pelaksanaan inspeksi KP.',
                'pedoman_nilai_1' => 'Nilai 1: Inspeksi dilakukan incidental tanpa jadwal rutin dan tanpa Laporan Resmi.',
                'pedoman_nilai_2' => 'Nilai 2: Inspeksi rutin berjalan tetapi temuan tidak dikategorikan (Kritikal/Mayor/Minor).',
                'pedoman_nilai_3' => 'Nilai 3: Laporan inspeksi lengkap tetapi penutupan temuan (closing rate) <80%.',
                'pedoman_nilai_4' => 'Nilai 4: Inspeksi terjadwal rutin, Laporan lengkap, CAPA aktif, dan closing rate temuan >95%.',
            ]
        );


        // Elemen VI: Dokumentasi (Bobot 5.00)
        $elemen6 = Elemen::updateOrCreate(
            ['kode_elemen' => 'VI'],
            [
                'nama_elemen' => 'Dokumentasi Keselamatan Pertambangan',
                'bobot' => 5.00,
            ]
        );

        $sub6_1 = SubElemen::updateOrCreate(
            ['elemen_id' => $elemen6->id, 'kode_sub' => 'VI.1'],
            ['nama_sub' => 'Pengendalian Dokumen dan Rekaman KP']
        );

        Kriteria::updateOrCreate(
            ['sub_elemen_id' => $sub6_1->id, 'kode_kriteria' => 'VI.1.1'],
            [
                'deskripsi' => 'Prosedur pengendalian dokumen dan rekaman Keselamatan Pertambangan teridentifikasi dan tersimpan rapi.',
                'nilai_maksimal' => 4.00,
                'persyaratan_dokumen' => 'SOP Pengendalian Dokumen, Masterlist Dokumen KP ter-update, dan sistem penyimpanan arsip (Fisik/Digital).',
                'pedoman_nilai_0' => 'Nilai 0: Tidak ada SOP Pengendalian Dokumen & Masterlist.',
                'pedoman_nilai_1' => 'Nilai 1: Dokumen disimpan tanpa penomoran dan masterlist baku.',
                'pedoman_nilai_2' => 'Nilai 2: Terdapat masterlist dokumen tetapi dokumen kedaluwarsa masih beredar.',
                'pedoman_nilai_3' => 'Nilai 3: Dokumen terkendali rapi tetapi retensi rekaman belum diatur jelas.',
                'pedoman_nilai_4' => 'Nilai 4: SOP Pengendalian Dokumen aktif, Masterlist ter-update, bebas dari dokumen kadaluwarsa, dan retensi teratur.',
            ]
        );


        // Elemen VII: Tinjauan Manajemen (Bobot 5.00)
        $elemen7 = Elemen::updateOrCreate(
            ['kode_elemen' => 'VII'],
            [
                'nama_elemen' => 'Tinjauan Manajemen dan Peningkatan Kinerja',
                'bobot' => 5.00,
            ]
        );

        $sub7_1 = SubElemen::updateOrCreate(
            ['elemen_id' => $elemen7->id, 'kode_sub' => 'VII.1'],
            ['nama_sub' => 'Pelaksanaan Tinjauan Manajemen']
        );

        Kriteria::updateOrCreate(
            ['sub_elemen_id' => $sub7_1->id, 'kode_kriteria' => 'VII.1.1'],
            [
                'deskripsi' => 'Pelaksanaan rapat Tinjauan Manajemen SMKP secara berkala oleh manajemen puncak.',
                'nilai_maksimal' => 4.00,
                'persyaratan_dokumen' => 'Jadwal Tinjauan Manajemen, Notulen Rapat Management Review, Daftar Hadir Pimpinan, dan Dokumen Risalah Rencana Tindak Lanjut.',
                'pedoman_nilai_0' => 'Nilai 0: Tidak pernah melaksanakan Tinjauan Manajemen.',
                'pedoman_nilai_1' => 'Nilai 1: Tinjauan Manajemen dilaksanakan hanya saat terjadi insiden fatal.',
                'pedoman_nilai_2' => 'Nilai 2: Tinjauan Manajemen dilaksanakan setahun sekali tanpa dihadiri Pimpinan Puncak.',
                'pedoman_nilai_3' => 'Nilai 3: Dihadiri pimpinan puncak tetapi pembahasan belum mencakup hasil audit & kinerja K3KO.',
                'pedoman_nilai_4' => 'Nilai 4: Tinjauan Manajemen berkala, dihadiri pimpinan puncak, membahas seluruh masukan SMKP, dan menghasilkan rencana perbaikan berkelanjutan.',
            ]
        );
    }
}
