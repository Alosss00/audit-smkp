CREATE TABLE tb_elemen (
    kode_elemen VARCHAR(10) PRIMARY KEY,
    nama_elemen VARCHAR(255) NOT NULL,
    bobot_persen DECIMAL(5,2) NOT NULL
);

CREATE TABLE tb_sub_elemen (
    kode_sub_elemen VARCHAR(20) PRIMARY KEY,
    kode_elemen VARCHAR(10) NOT NULL,
    nama_sub_elemen VARCHAR(500) NOT NULL,
    FOREIGN KEY (kode_elemen) REFERENCES tb_elemen(kode_elemen) ON DELETE CASCADE
);

CREATE TABLE tb_sub_sub_elemen (
    kode_sub_sub_elemen VARCHAR(20) PRIMARY KEY,
    kode_sub_elemen VARCHAR(20) NOT NULL,
    nama_sub_sub_elemen VARCHAR(500) NOT NULL,
    FOREIGN KEY (kode_sub_elemen) REFERENCES tb_sub_elemen(kode_sub_elemen) ON DELETE CASCADE
);

CREATE TABLE tb_kriteria_penilaian (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_item_audit VARCHAR(20) NOT NULL, -- Merujuk ke kode_sub_elemen atau kode_sub_sub_elemen
    skor VARCHAR(5) NOT NULL,
    deskripsi TEXT NOT NULL
);


-- ==============================================================================
-- DML: INSERT DATA
-- ==============================================================================


-- INSERT DATA ELEMEN
INSERT INTO tb_elemen (kode_elemen, nama_elemen, bobot_persen) VALUES ('I', 'KEBIJAKAN', 10.0);
INSERT INTO tb_elemen (kode_elemen, nama_elemen, bobot_persen) VALUES ('II', 'PERENCANAAN', 15.0);
INSERT INTO tb_elemen (kode_elemen, nama_elemen, bobot_persen) VALUES ('III', 'ORGANISASI DAN PERSONEL', 17.0);
INSERT INTO tb_elemen (kode_elemen, nama_elemen, bobot_persen) VALUES ('IV', 'IMPLEMENTASI', 35.0);
INSERT INTO tb_elemen (kode_elemen, nama_elemen, bobot_persen) VALUES ('V', 'PEMANTAUAN, EVALUASI DAN TINDAK LANJUT', 15.0);
INSERT INTO tb_elemen (kode_elemen, nama_elemen, bobot_persen) VALUES ('VI', 'DOKUMENTASI', 3.0);
INSERT INTO tb_elemen (kode_elemen, nama_elemen, bobot_persen) VALUES ('VII', 'TINJAUAN MANAJEMEN DAN PENINGKATAN KINERJA', 5.0);

-- INSERT DATA SUB-ELEMEN
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('I.1', 'I', 'Penyusunan Kebijakan                                                                                 ');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('I.2', 'I', 'Isi Kebijakan');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('I.3', 'I', 'Penetapan Kebijakan');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('I.4', 'I', 'Komunikasi Kebijakan');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('I.5', 'I', 'Tinjauan Kebijakan');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('II.1', 'II', 'Penelaahan Awal');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('II.2', 'II', 'Manajemen Risiko');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('II.3', 'II', 'Identifikasi dan Kepatuhan Terhadap Ketentuan Peraturan Perundang-undangan dan Persyaratan Lainnya yang Terkait');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('II.4', 'II', 'Penetapan Tujuan, Sasaran, dan Program');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('II.5', 'II', 'Rencana Kerja dan Anggaran Keselamatan Pertambangan');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('III.1', 'III', 'Penyusunan dan Penetapan Struktur Organisasi, Tugas, Tanggung Jawab, dan Wewenang');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('III.2', 'III', 'Penunjukan KTT, Kepala Tambang Bawah Tanah, dan/atau Kepala Kapal Keruk untuk Perusahaan Pertambangan');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('III.3', 'III', 'Penunjukan PJO Untuk Perusahaan Jasa Pertambangan');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('III.4', 'III', 'Pembentukan dan Penetapan Bagian K3 Pertambangan dan KO Pertambangan');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('III.5', 'III', 'Penunjukan Pengawas Operasional dan Pengawas Teknik');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('III.6', 'III', 'Penunjukan Tenaga Teknik Khusus Pertambangan');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('III.7', 'III', 'Pembentukan dan Penetapan Komite Keselamatan Pertambangan');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('III.8', 'III', 'Penunjukan Tim Tanggap Darurat');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('III.9', 'III', 'Seleksi dan Penempatan Personel');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('III.10', 'III', 'Penyelenggaraan dan Pelaksanaan Pendidikan dan Pelatihan Serta Kompetensi Kerja');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('III.11', 'III', 'Penyusunan, Penetapan, dan Penerapan Komunikasi Keselamatan Pertambangan');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('III.12', 'III', 'Pengelolaan Administrasi Keselamatan Pertambangan');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('III.13', 'III', 'Penyusunan, Penerapan, dan Pendokumentasian Prosedur Partisipasi, Konsultasi, Motivasi, dan Kesadaran Penerapan SMKP Minerba');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('IV.1', 'IV', 'Pelaksanaan Pengelolaan Operasional');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('IV.2', 'IV', 'Pelaksanaan Pengelolaan Lingkungan Kerja');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('IV.3', 'IV', 'Pelaksanaan Pengelolaan Kesehatan Kerja');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('IV.4', 'IV', 'Pelaksanaan Pengelolaan KO Pertambangan');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('IV.5', 'IV', 'Pelaksanaan Pengelolaan Bahan Peledak dan Peledakan');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('IV.6', 'IV', 'Penetapan Sistem Perancangan dan Rekayasa');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('IV.7', 'IV', 'Penetapan Sistem Pembelian');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('IV.8', 'IV', 'Pemantauan dan Pengelolaan Perusahaan Jasa Pertambangan');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('IV.9', 'IV', 'Pengelolaan Keadaan Darurat ');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('IV.10', 'IV', 'Penyediaan dan Penyiapan P3K');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('IV.11', 'IV', 'Pelaksanaan keselamatan di luar pekerjaan (off the job safety)');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('V.1', 'V', 'Pemantauan dan pengukuran kinerja');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('V.2', 'V', 'Inspeksi Pelaksanaan Keselamatan Pertambangan');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('V.3', 'V', 'Evaluasi kepatuhan Terhadap Ketentuan Peraturan Perundang-Undangan dan Persyaratan Lainnya Yang Terkait');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('V.4', 'V', 'Penyelidikan Kecelakaan, Kejadian Berbahaya, dan Penyakit Akibat Kerja');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('V.5', 'V', 'Evaluasi Pengelolaan Administrasi Keselamatan Pertambangan');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('V.6', 'V', 'Audit Internal Penerapan SMKP Minerba atau SMKP Khusus untuk pengolahan dan/ atau pemurnian');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('V.7', 'V', 'Rencana Perbaikan dan Tindak Lanjut ');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('VI.1', 'VI', 'Penyusunan Penetapan dan Pendokumentasian Manual SMKP Minerba atau SMKP Khusus pada Pengolahan dan/atau Pemurnian');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('VI.2', 'VI', 'Penyusunan Penetapan, Penerapan dan Pendokumentasian Prosedur pengendalian Dokumen Keselamatan Pertambangan');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('VI.3', 'VI', 'Penyusunan Penetapan, Penerapan dan Pendokumentasian Prosedur pengendalian Rekaman Keselamatan Pertambangan');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('VI.4', 'VI', 'Penetapan Jenis Dokumen dan Rekaman');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('VII.1', 'VII', 'Pelaksanaan Tinjauan Manajemen Penerapan SMKP Minerba atau SMKP Khusus pada pengolahan dan/atau Pemurnian oleh Manajemen Tertinggi Perusahaan');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('VII.2', 'VII', 'Pendokumentasian Catatan Hasil Tinjauan Manajemen');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('VII.3', 'VII', 'Keluaran dari Tinjauan Manajemen Keselamatan Pertambangan ');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('VII.4', 'VII', 'Pencatatan, Pendokumentasian, dan Pelaporan Hasil Tinjauan Manajemen');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('VII.5', 'VII', 'Pelaksanaan Peningkatan Kinerja');
INSERT INTO tb_sub_elemen (kode_sub_elemen, kode_elemen, nama_sub_elemen) VALUES ('VII.6', 'VII', 'Penggunaan Tinjauan Hasil dari Tindak Lanjut Rencana Perbaikan dalam Penentuan Kebijakan');

-- INSERT DATA SUB-SUB-ELEMEN
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('II.2.1', 'II.2', 'Komunikasi dan konsultasi risiko');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('II.2.2', 'II.2', 'Penetapan konteks risiko');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('II.2.3', 'II.2', 'Identifikasi bahaya');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('II.2.4', 'II.2', 'Penilaian dan pengendalian risiko');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('II.2.5', 'II.2', 'Pemantauan dan peninjauan');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('III.2.1', 'III.2', 'Penunjukan KTT');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('III.2.2', 'III.2', 'Penunjukan Kepala Tambang Bawah Tanah');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('III.2.3', 'III.2', 'Penunjukan Kepala Kapal Keruk');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('III.10.1', 'III.10', 'Pendidikan dan pelatihan pekerja tambang');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('III.10.2', 'III.10', 'Kompetensi Kerja');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('III.12.1', 'III.12', 'Buku tambang');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('III.12.2', 'III.12', 'Buku daftar kecelakaan tambang');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('III.12.3', 'III.12', 'Pelaporan pengelolaan Keselamatan Pertambangan');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('III.12.4', 'III.12', 'Dokumentasi Kejadian Berbahaya, kejadian akibat penyakit tenaga kerja dan penyakit akibat kerja');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('III.12.5', 'III.12', 'Dokumen dan Laporan Pemenuhan Kompetensi dan Persyaratan Lainnya');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('IV.1.1', 'IV.1', 'Penyusunan, Penetapan, Penerapan, Pendokumentasian, dan Evaluasi Prosedur Operasi / Kerja');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('IV.1.2', 'IV.1', 'Penyusunan, Penetapan, Penerapan, Pendokumentasian, dan Evaluasi Izin kerja khusus');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('IV.1.3', 'IV.1', 'Penyusunan, Penetapan, Penerapan, Pendokumentasian, dan Evaluasi Prosedur Operasi / Kerja Alat pelindung diri dan alat keselamatan');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('IV.2.1', 'IV.2', 'Pelaksanaan pengelolaan Bahaya Debu');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('IV.2.2', 'IV.2', 'Pelaksanaan pengelolaan Bahaya Kebisingan');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('IV.2.3', 'IV.2', 'Pelaksanaan pengelolaan Bahaya Getaran');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('IV.2.4', 'IV.2', 'Pelaksanaan pengelolaan Bahaya Pencahayaan');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('IV.2.5', 'IV.2', 'Pelaksanaan pengelolaan Kuantitas dan Kualitas Udara Kerja');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('IV.2.6', 'IV.2', 'Pelaksanaan pengelolaan Iklim Kerja');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('IV.2.7', 'IV.2', 'Pelaksanaan pengelolaan Bahaya Radiasi');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('IV.2.8', 'IV.2', 'Pelaksanaan pengelolaan Faktor Kimia');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('IV.2.9', 'IV.2', 'Pelaksanaan pengelolaan Faktor Biologi');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('IV.2.10', 'IV.2', 'Pelaksanaan Kebersihan Lingkungan Kerja');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('IV.3.1', 'IV.3', 'Pemeriksaan Kesehatan');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('IV.3.2', 'IV.3', 'Pelayanan Kesehatan Kerja');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('IV.3.3', 'IV.3', 'Pertolongan Pertama pada Kecelakaan');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('IV.3.4', 'IV.3', 'Pengelolaan Kelelahan Kerja (Fatigue)');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('IV.3.5', 'IV.3', 'Pengelolaan Pekerja pada Tempat yang Memiliki Risiko Kesehatan Tinggi');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('IV.3.6', 'IV.3', 'Pengelolaan Rekaman Data Kesehatan Kerja');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('IV.3.7', 'IV.3', 'Pengelolaan Higiene dan Sanitasi');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('IV.3.8', 'IV.3', 'Pengelolaan Ergonomi');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('IV.3.9', 'IV.3', 'Pengelolaan Makanan, Minuman dan Gizi Pekerja');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('IV.3.10', 'IV.3', 'Diagnosis dan Pemeriksaan Penyakit Akibat Kerja');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('IV.4.1', 'IV.4', 'Sistem dan pelaksanaan pemeliharaan / perawatan sarana, prasarana, instalasi, dan peralatan pertambangan');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('IV.4.2', 'IV.4', 'Pengamanan instalasi');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('IV.4.3', 'IV.4', 'Kelayakan sarana, prasarana, instalasi, dan peralatan pertambangan');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('IV.4.4', 'IV.4', 'Kompetensi tenaga teknik');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('IV.4.5', 'IV.4', 'Evaluasi Laporan Hasil Kajian Teknis Pertambangan');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('IV.5.1', 'IV.5', 'Gudang bahan peledak');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('IV.5.2', 'IV.5', 'Penyimpanan bahan peledak');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('IV.5.3', 'IV.5', 'Pengangkutan bahan peledak');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('IV.5.4', 'IV.5', 'Pekerjaan peledakan');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('IV.6.1', 'IV.6', 'Perancangan dan rekayasa');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('IV.6.2', 'IV.6', 'Perubahan');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('IV.8.1', 'IV.8', 'Persyaratan, seleksi dan penetapan perusahaan jasa pertambangan');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('IV.8.2', 'IV.8', 'Tanggung jawab, pemantauan dan pelaporan perusahaan jasa pertambangan');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('IV.8.3', 'IV.8', 'Evaluasi perusahaan jasa pertambangan');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('V.1.1', 'V.1', 'Pemantauan dan Pengukuran Pencapaian Tujuan, Sasaran, dan program Keselamatan Pertambangan');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('V.1.2', 'V.1', 'Pemantauan dan Pengukuran Kinerja Pengelolaan lingkungan kerja');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('V.1.3', 'V.1', 'Pemantauan dan Pengukuran Kinerja Pengelolaan kesehatan kerja');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('V.1.4', 'V.1', 'Pemantauan dan Pengukuran Kinerja Pengelolaan Keselamatan Operasi pertambangan');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('V.1.5', 'V.1', 'Pemantauan dan Pengukuran Kinerja Pengelolaan Bahan Peledak dan Peledakan');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('V.5.1', 'V.5', 'Buku tambang');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('V.5.2', 'V.5', 'Buku daftar kecelakaan tambang');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('V.5.3', 'V.5', 'Pelaporan pengelolaan keselamatan pertambangan');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('V.5.4', 'V.5', 'Dokumentasi Kejadian Berbahaya, kejadian akibat penyakit tenaga kerja dan penyakit akibat kerja');
INSERT INTO tb_sub_sub_elemen (kode_sub_sub_elemen, kode_sub_elemen, nama_sub_sub_elemen) VALUES ('V.5.5', 'V.5', 'Dokumentasi dan Laporan pemenuhan Kompetensi serta Persyaratan Lainnya');

-- INSERT DATA KRITERIA PENILAIAN
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('I.1', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan jatau Pemurnian, IPR, atau IUJP telah melakukan penyusunan kebijakan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('I.1', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan jatau Pemurnian, IPR, atau IUJP telah melakukan tinjauan awal kondisi Keselamatan Pertambangan, namun belum memenuhi secara menyeluruh ketiga syarat penyusunan tinjauan awal dan belum melibatkan seluruh departemen/bagian dari Pekerja atau serikat Pekerja dalam penyusunan kebijakan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('I.1', '2', 'Terdapat bukti yang menunjukkan:

a) pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan tinjauan awal kondisi Keselamatan Pertambangan dan telah memenuhi 3 (tiga) syarat penyusunan tinjauan awal, namun belum melibatkan seluruh departemen/bagian dari Pekerja atau serikat Pekerja dalam penyusunan kebijakan; atau 

b) pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan tinjauan awal kondisi Keselamatan Pertambangan yang telah melibatkan seluruh departemen/bagian dari Pekerja atau serikat Pekerja dalam penyusunan kebijakan, namun belum memenuhi 3 (tiga) syarat penyusunan tinjauan awal.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('I.1', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan tinjauan awal kondisi Keselamatan Pertambangan dan telah memenuhi 3 (tiga) syarat penyusunan tinjauan awal, serta telah melibatkan seluruh departemen/bagian dari Pekerja atau serikat Pekerja dalam penyusunan kebijakan, namun belum dilakukan evaluasi terhadap penyusunan kebijakan tersebut.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('I.1', '4', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan tinjauan awal kondisi Keselamatan Pertambangan dan telah memenuhi 3 (tiga) syarat penyusunan tinjauan awal, serta telah melibatkan seluruh departemen / bagian dari Pekerja atau serikat Pekerja dalam penyusunan kebijakan, dan telah dilakukan evaluasiterhadap penyusunan kebijakan tersebut.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('I.2', '0', 'Perusahaan tidak memiliki isi kebijakan');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('I.2', '1', 'perusahaan telah memiliki isi kebijakan, namun belum terdapat visi, misi, dan tujuan, dan belum terdapat komitmen dalam melaksanakan Keselamatan Pertambangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('I.2', '2', 'perusahaan telah memiliki isi kebijakan yang terdapat visi, misi, dan tujuan, serta komitmen dalam melaksanakan Keselamatan Pertambangan, namun tidak ada isi kebijakan Keselamatan Pertambangan yang telah diturunkan menjadi program kerja Keselamatan Pertambangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('I.2', '3', 'perusahaan telah memiliki isi kebijakan yang terdapat visi, misi, dan tujuan, serta komitmen dalam melaksanakan Keselamatan Pertambangan, namun belum semua isi kebijakan Keselamatan Pertambangan telah diturunkan menjadi program kerja Keselamatan Pertambangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('I.2', '4', 'perusahaan telah memiliki isi kebijakan yang terdapat visi, misi, dan tujuan, serta komitmen dalam melaksanakan Keselamatan Pertambangan, dan semua isi kebijakan Keselamatan Pertambangan telah diturunkan menjadi program kerja Keselamatan Pertambangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('I.3', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan penetapan kebijakan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('I.3', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan penetapan kebijakan secara tertulis, namun belum disahkan oleh pimpinan tertinggi pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('I.3', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan penetapan kebijakan secara tertulis, dan telah disahkan oleh pimpinan tertinggi pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP, namun belum bersifat dinamis.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('I.3', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan penetapan kebijakan secara tertulis, telah disahkan oleh pimpinan tertinggi pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP, dan bersifat dinamis, yaitu menyesuaikan perubahan yang ada di pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('I.4', '0', 'perusahaan tidak melakukan komunikasi kebijakan');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('I.4', '1', 'perusahaan telah melakukan komunikasi kebijakan namun belum menggunakan bahasa yang dapat dipahami oleh Pekerja Tambang; dan belum menggunakan beberapa media seperti papan pengumuman, brosur, verbal dalam apel (briefing), dan/atau media lainnya');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('I.4', '2', 'perusahaan telah melakukan komunikasi kebijakan dengan kondisi:

a) telah menggunakan bahasa yang dapat dipahami oleh Pekerja; namun belum menggunakan beberapa media seperti papan pengumuman, brosur, verbal dalam apel (briefing), dan/atau media lainnya, atau

b) telah menggunakan beberapa media seperti papan pengumuman, brosur, verbal dalam apel (briefing), dan/atau media lainnya, namun belum menggunakan bahasa yang dapat dipahami oleh Pekerja');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('I.4', '3', 'perusahaan telah melakukan komunikasi kebijakan dan telah menggunakan bahasa yang dapat dipahami oleh Pekerja Tambang; dan telah menggunakan beberapa media seperti papan pengumuman, brosur, verbal dalam apel (briefing), dan/atau media lainnya, namun belum melakukan evaluasi ketersampaian informasi kepada seluruh departemen/bagian dari Pekerja Tambang.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('I.4', '4', 'perusahaan telah melakukan komunikasi kebijakan dan telah menggunakan bahasa yang dapat dipahami oleh Pekerja Tambang; dan telah menggunakan beberapa media seperti papan pengumuman, brosur, verbal dalam apel (briefing), dan/atau media lainnya, serta telah melakukan evaluasi ketersampaian informasi kepada seluruh departemen/bagian dari Pekerja Tambang.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('I.5', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan tinjauan kebijakan oleh manajemen secara berkala.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('I.5', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan jatau Pemurnian, IPR, atau IUJP telah melakukan tinjauan kebijakan secara berkala dengan menyesuaikan kondisi perubahan yang terjadi di dalam pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP (internal), namun belum menyesuaikan dengan perubahan yang terjadi di luar pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP (eksternal) seperti ketentuan peraturan perundang-undangan dan standar.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('I.5', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan tinjauan kebijakan secara berkala dengan menyesuaikan kondisi perubahan yang terjadi di dalam pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP (internal), dan telah menyesuaikan dengan perubahan yang terjadi di luar pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP (eksternal) seperti ketentuan peraturan perundang-undangan dan standar.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('I.5', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan tinjauan kebijakan secara berkala dengan menyesuaikan kondisi perubahan yang terjadi di dalam pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP (internal) dan di luar pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP (eksternal) seperti ketentuan peraturan perundang-undangan dan standar, serta sebagian hasil tinjauan kebijakan ditindaklanjuti sebagai masukan dalam penyusunan kebijakan baru.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('I.5', '4', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan tinjauan kebijakan secara berkala dengan menyesuaikan kondisi perubahan yang terjadi di dalam pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP (internal) dan di luar Pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP (eksternal) seperti ketentuan peraturan perundang-undangan dan standar, serta seluruh basil tinjauan kebijakan ditindaklanjuti sebagai masukan dalam penyusunan kebijakan baru.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('II.1', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan penelaahan awal dalam perencanaan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('II.1', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan penelaahan awal dalam perencanaan, namun belum menentukan tingkat pencapaian kinerja Keselamatan Pertambangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('II.1', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan penelaahan awal dalam perencanaan, dan telah menentukan tingkat pencapaian kinerja Keselamatan Pertambangan, namun tidak sesuai dengan kondisi.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('II.1', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan penelaahan awal dalam perencanaan, menentukan tingkat pencapaian kinerja Keselamatan Pertambangan yang telah sesuai dengan kondisi, namun belum sinkron dengan program Keselamatan Pertambangan yang ditetapkan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('II.1', '4', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan penelaahan awal dalam perencanaan, menentukan tingkat pencapa1an kinerja Keselamatan Pertambangan yang telah sesuai dengan kondisi, dan telah sinkron dengan program Keselamatan Pertambangan yang ditetapkan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('II.2.1', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan komunikasi dan konsultasi risiko.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('II.2.1', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan komunikasi dan konsultasi risiko, namun baru dengan sebagian pemangku kepentingan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('II.2.1', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan komunikasi dan konsultasi risiko dengan seluruh pemangku kepentingan, namun hasil dari komunikasi dan konsultasi risiko tidak menjadi bahan pertimbangan dalam Manajemen Risiko.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('II.2.1', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan komunikasi dan konsultasi risiko dengan seluruh pemangku kepentingan, namun baru sebagian dari hasil dari komunikasi dan konsultasi risiko menjadi bahan pertimbangan dalam Manajemen Risiko.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('II.2.1', '4', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan komunikasi dan konsultasi risiko dengan seluruh pemangku kepentingan, serta seluruh hasil dari komunikasi dan konsultasi risiko menjadi bahan pertimbangan dalam Manajemen Risiko.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('II.2.2', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan penetapan konteks risiko.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('II.2.2', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan penetapan konteks risiko, namun baru mencakup faktor internal atau faktor eksternal.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('II.2.2', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan penetapan konteks risiko yang telah mencakup sebagian faktor internal dan sebagian faktor eksternal (belum seluruhnya).');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('II.2.2', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan penetapan konteks risiko yang telah mencakup seluruh faktor internal dan faktor eksternal.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('II.2.3', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan identifikasi bahaya.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('II.2.3', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan identifikasi bahaya, namun belum seluruh bahaya diidentifikasi.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('II.2.3', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan identifikasi bahaya, dan telah seluruh bahaya diidentifikasi.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('II.2.4', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan penilaian dan pengendalian risiko.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('II.2.4', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan penilaian risiko atau pengendalian risiko, namun belum seluruh bahaya yang teridentifikasi telah dinilai atau belum dikendalikan sesuai dengan hirarki pengendalian.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('II.2.4', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan penilaian dan pengendalian risiko yang sesuai dengan hirarki pengendalian, namun implementasi pengendaliannya belum memadai.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('II.2.4', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengendalian risiko yang sesuai dengan hirarki pengendalian, dan implementasi penendaliannya telah memadai.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('II.2.5', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pemantauan dan peninjauan risiko.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('II.2.5', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pemantauan dan peninjauan risiko, namun belum secara periodik atau apabila terjadi kecelakaan atau kejadian berbahaya, Penyakit Akibat Kerja, perubahan dalam peralatan, instalasi, dan/atau proses serta kegiatan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP, dan ada proses serta kegiatan baru dalam pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP belum dilakukan pemantauan dan peninjauan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('II.2.5', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pemantauan dan peninjauan risiko secara periodik atau apabila terjadi kecelakaan atau kejadian berbahaya, Penyakit Akibat Kerja, perubahan dalam peralatan, instalasi, dan/atau proses serta kegiatan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP, dan ada proses serta kegiatan baru dalam pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP sudah dilakukan pemantauan dan peninjauan, namun hasilnya belum memadai.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('II.2.5', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pemantauan dan peninjauan risiko secara periodik atau apabila terjadi kecelakaan atau kejadian berbahaya, Penyakit Akibat Kerja, perubahan dalam peralatan, instalasi, dan/atau proses serta kegiatan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP, dan ada proses serta kegiatan baru dalam pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP sudah dilakukan pemantauan dan peninjauan, serta hasilnya telah memadai.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('II.3', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan identifikasi dan kepatuhan ketentuan peraturan perundangundangan dan persyaratan lainnya yang terkait.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('II.3', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan identifikasi dan pemantauan terhadap ketentuan peraturan perundang-undangan dan persyaratan lainnya, namun belum melakukan evaluasi kepatuhan terhadap ketentuan peraturan perundang-undangan dan persyaratan perizinan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('II.3', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan identifikasi dan pemantauan terhadap ketentuan peraturan perundang-undangan dan persyaratan lainnya, namun berdasarkan hasil evaluasi masih terdapat beberapa peraturan perundang-undangan dan/atau persyaratan perizinan dipatuhi.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('II.3', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan identifikasi dan pemantauan terhadap ketentuan peraturan perundang-undangan dan persyaratan lainnya, dan berdasarkan evaluasi telah mematuhi ketentuan peraturan perundang-undangan dan persyaratan perizinan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('II.4', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan penetapan tujuan,sasaran, dan program.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('II.4', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan penetapan tujuan, sasaran, dan program, namun belum selaras dengan kebijakan, belum terukur, dan belum disahkan oleh Komite Keselamatan Pertambangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('II.4', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, 
atau IUJP telah melakukan penetapan tujuan, sasaran, dan program dengan kondisi:
• telah disahkan oleh Komite Keselamatan Pertambangan;
• sebagian besar tujuan, sasaran, program, yang ditetapkan belum selaras dengan kebijakan dan belum terukur; dan
• penyusunan program belum mempertimbangkan seluruh ketentuan penyusunan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('II.4', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, 
atau IUJP telah melakukan penetapan tujuan, sasaran, dan program dengan kondisi:
• telah disahkan oleh Komite Keselamatan Pertambangan;
• seluruh besar tujuan, sasaran, program, yang ditetapkan telah selaras dengan kebijakan dan telah terukur; dan
• penyusunan program belum seluruhnya mempertimbangkan seluruh ketentuan penyusunan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('II.4', '4', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, 
atau IUJP telah melakukan penetapan tujuan, sasaran, dan program dengan kondisi:
• telah disahkan oleh Komite Keselamatan Pertambangan;
• seluruh besar tujuan, sasaran, program, yang ditetapkan telah selaras dengan kebijakan dan telah terukur; dan
• penyusunan program telah seluruhnya mempertimbangkan seluruh ketentuan penyusunan.
');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('II.5', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR telah melakukan penetapan rencana kerja anggaran dan biaya aspek Keselamatan Pertambangan yang mendapat persetujuan dari Direktur Jenderal atas nama Menteri atau Gubernur sesuai kewenangannya.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('II.5', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR telah melakukan penetapan rencana kerja anggaran dan biaya aspek Keselamatan Pertambangan, namun penyusunannya belum mempertimbangkan skala prioritas sasaran dan program Keselamatan Pertambangan dan kebutuhan untuk perbaikan dan peningkatan Keselamatan Pertambangan yang berkelanjutan, dan pemenuhan terhadap peraturan perundang-undangan dan persyaratan lainnya yang terkait dan belum mendapat persetujuan dari Direktur Jenderal atas nama Menteri atau Gubernursesuai kewenangannya.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('II.5', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR telah melakukan penetapan rencana kerja anggaran dan biaya aspek Keselamatan Pertambangan yang penyusunannya telah mempertimbangkan skala prioritas sasaran dan program Keselamatan Pertambangan dan kebutuhan untuk perbaikan dan peningkatan Keselamatan Pertambangan yang berkelanjutan, dan pemenuhan terhadap peraturan perundang-undangan dan persyaratan lainnya yang terkait, namun belum mendapat persetujuan dari Direktur Jenderal atas nama Menteri atau Gubernur sesuai kewenangannya.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('II.5', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR telah melakukan penetapan rencana kerja anggaran dan biaya aspek Keselamatan Pertambangan yang mendapat persetujuan dari Direktur Jenderal atas nama Menteri atau Gubernur sesuai kewenangannya.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.1', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memilikistruktur organisasi yang menggambarkan posisi KTT atau PTL, PJO, Pengawas Operasional, Pengawas Teknis, dan Pengelola Keselamatan Pertambangan, serta Kepala Tambang Bawah Tanah dalam hal kegiatan penambangan menggunakan metode tambang bawah tanah, dan/atau Kepala Kapal Keruk dalam hal kegiatan penambangan mengoperasikan Kapal Keruk, sesuai dengan ketentuan peraturan perundangundangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.1', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memilikistruktur organisasi yang menggambarkan posisi KTT atau PTL, PJO, Pengawas Operasional, Pengawas Teknis, dan Pengelola Keselamatan Pertambangan, serta Kepala Tambang Bawah Tanah dalam hal kegiatan penambangan menggunakan metode tambang bawah tanah, dan/atau Kepala Kapal Keruk dalam hal kegiatan penambangan mengoperasikan Kapal Keruk, sesuai dengan ketentuan peraturan perundangundangan, namun belum terintegrasi dalam struktur organisasi Pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, dan IUJP.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.1', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memilikistruktur organisasi yang menggambarkan posisi KTT atau PTL, PJO, Pengawas Operasional, Pengawas Teknis, dan Pengelola Keselamatan Pertambangan, serta Kepala Tambang Bawah Tanah dalam hal kegiatan penambangan menggunakan metode tambang bawah tanah, dan/atau Kepala Kapal Keruk dalam hal kegiatan penambangan mengoperasikan Kapal Keruk, sesuai dengan ketentuan peraturan perundangundangan, yang terintegrasi dalam struktur organisasi Pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, dan IUJP, namun penyusunan struktur organisasi pengelolaan Keselamatan Pertambangan belum memenuhi ketentuan yang dipersyaratkan, dan belum dikomunikasikan kepada Pekerja dan pihak-pihak terkait.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.1', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memilikistruktur organisasi yang menggambarkan posisi KTT atau PTL, PJO, Pengawas Operasional, Pengawas Teknis, dan Pengelola Keselamatan Pertambangan, serta Kepala Tambang Bawah Tanah dalam hal kegiatan penambangan menggunakan metode tambang bawah tanah, dan/atau Kepala Kapal Keruk dalam hal kegiatan penambangan mengoperasikan Kapal Keruk, sesuai dengan ketentuan peraturan perundangundangan, yang terintegrasi dalam struktur organisasi Pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, dan IUJP, dengan kondisi: 
 
•  penyusunan struktur organisasi pengelolaan Keselamatan Pertambangan telah memenuhi ketentuan yang dipersyaratkan namun belum dikomunikasikan kepada seluruh Pekerja dan pihak-pihak terkait; atau 
 
•  telah dikomunikasikan kepada seluruh Pekerja dan pihak-pihak terkait namun penyusunan struktur organisasi belum memenuhi ketentuan yang dipersyaratkan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.1', '4', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memilikistruktur organisasi yang menggambarkan posisi KTT atau PTL, PJO, Pengawas Operasional, Pengawas Teknis, dan Pengelola Keselamatan Pertambangan, serta Kepala Tambang Bawah Tanah dalam hal kegiatan penambangan menggunakan metode tambang bawah tanah, dan/atau Kepala Kapal Keruk dalam hal kegiatan penambangan mengoperasikan Kapal Keruk, sesuai dengan ketentuan peraturan perundangundangan, yang terintegrasi dalam struktur organisasi Pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahandan/atau Pemurnian, IPR, dan IUJP, dan penyusunan struktur organisasi pengelolaan Keselamatan Pertambangan telah memenuhi ketentuan yang dipersyaratkan dan telah dikomunikasikan kepada Pekerja dan pihak-pihak terkait.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.2.1', 'N/A', 'N/A (Kriteria Khusus / Sesuai Ketentuan)');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.2.2', 'N/A', 'N/A (Kriteria Khusus / Sesuai Ketentuan)');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.2.3', 'N/A', 'N/A (Kriteria Khusus / Sesuai Ketentuan)');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.3', '0', 'Tidak ada bukti yang menunjukkan telah terdapat PJO yang mendapat pengesahan dari KTT atau PTL.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.3', '1', 'Terdapat bukti yang menunjukkan telah terdapat PJO yang mendapatkan pengesahan dari KTT atau PTL, dengan kondisi: 
 
•  belum seluruh perusahaan jasa Pertambangan yang dipersyaratkan telah memiliki PJO yang mendapatkan pengesahan dari KTT atau PTL, dengan kualifikasi persyaratan administratif dan persyaratan teknis sesuai kriteria peraturan perundang-undangan; atau 
 
•   masih terdapat perusahaan jasa Pertambangan yang dipersyaratkan yang belum memiliki PJO.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.3', '2', 'Terdapat bukti yang menunjukkan seluruh perusahaan jasa Pertambangan yang dipersyaratkan telah memiliki PJO yang mendapatkan pengesahan dari KTT atau PTL, dan seluruh PJO tersebut memenuhi kualifikasi persyaratan administratif dan persyaratan teknis sesuai kriteria peraturan perundang-undangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.4', '0', 'perusahaan tidak memiliki isi kebijakan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.4', '1', 'Sudah dibentuk, tetapi ADA SALAH SATU kondisi berikut:
 
•  Belum dibentuk berdasarkan pertimbangan jumlah Pekerja/luas pekerjaan.
 
•  Belum berada langsung di bawah KTT/PTL atau PJO.
 
•  Tugas dan tanggung jawab belum mencakup seluruh ruang lingkup K3 dan KO sesuai regulasi.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.4', '2', 'Sudah dibentuk berdasarkan pertimbangan Pekerja/luas kerja DAN sudah berada langsung di bawah KTT/PTL/PJO, tetapi:
 
•  Tugas dan tanggung jawab belum mencakup seluruh ruang lingkup K3 dan KO sesuai regulasi; DAN/ATAU
 
•  Tugas dan tanggung jawab belum sepenuhnya dijalankan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.4', '3', 'Sudah dibentuk, berada langsung di bawah KTT/PTL/PJO, DAN tugas/tanggung jawab telah mencakup seluruh ruang lingkup K3 dan KO sesuai regulasi, TAPI tugas dan tanggung jawab belum sepenuhnya dijalankan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.4', '4', 'Sudah dibentuk, berada langsung di bawah KTT/PTL/PJO, tugas/tanggung jawab mencakup seluruh ruang lingkup K3/KO, DAN telah dijalankan sepenuhnya (Lengkap, Sesuai Regulasi, dan Terimplementasi Penuh).');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.5', '0', 'Tidak ada bukti yang menunjukkan KTT atau PTL telah mengangkat pengawas operasional dan pengawas teknis dengan Surat Penunjukan Pengawas Operasional atau Surat Pengesahan Pengawas Teknis.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.5', '1', 'Terdapat bukti yang menunjukkan:
 
•  KTT atau PTL telah mengangkat sebagian pengawas operasional di lapangan dengan Surat Penunjukan Pengawas Operasional; atau
 
•  KTT atau PTL telah mengangkat sebagian pengawas teknis di lapangan dengan Surat Pengesahan Pengawas Teknis.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.5', '2', 'Terdapat bukti yang menunjukkan:
 
•  KTT atau PTL telah mengangkat seluruh pengawas operasional di lapangan dengan Surat Penunjukan Pengawas Operasional, namun masih terdapat sebagian pengawas operasional yang belum memiliki Kartu Pengawas Operasional yang disahkan oleh KaIT atau Kadis atas nama KaIT; dan
 
•  KTT atau PTL telah mengangkat seluruh pengawas teknis di lapangan dengan Surat Pengesahan Pengawas Teknis.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.5', '3', 'Terdapat bukti yang menunjukkan:
 
•  KTT atau PTL telah mengangkat seluruh pengawas operasional dan pengawas teknis di lapangan dengan Surat Penunjukan Pengawas Operasional atau Surat Pengesahan Pengawas Teknis dan seluruh pengawas operasional telah memiliki Kartu Pengawas Operasional yang disahkan oleh KaIT; dan
 
•  pengawas operasional dan pengawas teknis belum menjalankan seluruh tugas dan tanggung jawab sesuai ketentuan peraturan perundang-undangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.5', '4', 'Terdapat bukti yang menunjukkan:
 
•  KTT atau PTL telah mengangkat seluruh pengawas operasional dan pengawas teknis di lapangan dengan Surat Penunjukan Pengawas Operasional atau Surat Pengesahan Pengawas Teknis dan seluruh pengawas operasional telah memiliki Kartu Pengawas Operasional yang disahkan oleh KaIT; dan
 
•  pengawas operasional dan pengawas teknis sudah menjalankan seluruh tugas dan tanggung jawab sesuai ketentuan peraturan perundang-undangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.6', '0', 'Tidak ada bukti yang menunjukkan Kepala Teknik Tambang (KTT) atau Penanggung Jawab Teknik dan Lingkungan (PTL) telah menunjuk Tenaga Teknis Pertambangan yang Berkompeten, yang telah memiliki sertifikasi sesuai standar kompetensi kerja yang berlaku yang ditetapkan oleh Pemerintah.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.6', '1', 'Terdapat bukti yang menunjukkan KTT atau PTL telah menunjuk Tenaga Teknis Pertambangan yang Berkompeten dengan kondisi:
 
•  KTT atau PTL belum membuat Daftar Tenaga Teknis Pertambangan yang Berkompeten;
 
•  sebagian Tenaga Teknis Pertambangan yang Berkompeten belum memiliki Surat Penunjukan dari KTT atau PTL; atau
 
•  sebagian Tenaga Teknis Pertambangan yang Berkompeten tersebut belum memiliki sertifikasi sesuai standar kompetensi kerja yang berlaku yang ditetapkan oleh pemerintah atau oleh KTT atau PTL bagi yang standar kompetensi kerjanya belum ditetapkan oleh Pemerintah.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.6', '2', 'Terdapat bukti yang menunjukkan KTT atau PTL telah menunjuk Tenaga Teknis Pertambangan yang Berkompeten dengan kondisi:
 
•  KTT atau PTL sudah membuat Daftar Tenaga Teknis Pertambangan yang Berkompeten dan seluruh Tenaga Teknis Pertambangan yang Berkompeten telah memiliki Surat Penunjukan dari KTT atau PTL; dan
 
•  sebagian Tenaga Teknis Pertambangan yang Berkompeten tersebut belum memiliki sertifikasi sesuai standar kompetensi kerja yang berlaku yang ditetapkan oleh pemerintah atau oleh KTT atau PTL bagi yang standar kompetensi kerjanya belum ditetapkan oleh Pemerintah.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.6', '3', 'Terdapat bukti yang menunjukkan KTT atau PTL telah menunjuk Tenaga Teknis Pertambangan yang Berkompeten dengan kondisi:
 
•  KTT atau PTL sudah membuat Daftar Tenaga Teknis Pertambangan yang Berkompeten dan seluruh Tenaga Teknis Pertambangan yang Berkompeten telah memiliki Surat Penunjukan dari KTT atau PTL, serta memiliki sertifikasi sesuai standar kompetensi kerja yang berlaku yang ditetapkan oleh pemerintah atau oleh KTT atau PTL bagi yang standar kompetensi kerjanya belum ditetapkan oleh Pemerintah; dan
 
•  belum seluruh Tenaga Teknis Pertambangan menjalankan tugas dan tanggung jawab sesuai ketentuan peraturan perundang-undangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.6', '4', 'Terdapat bukti yang menunjukkan KTT atau PTL telah menunjuk Tenaga Teknis Pertambangan yang Berkompeten dengan kondisi KTT atau PTL sudah membuat Daftar Tenaga Teknis Pertambangan yang Berkompeten dan seluruh Tenaga Teknis Pertambangan yang Berkompeten telah memiliki Surat Penunjukan dari KTT atau PTL, memiliki sertifikasi sesuai standar kompetensi kerja yang berlaku yang ditetapkan oleh pemerintah atau oleh KTT atau PTL bagi yang standar kompetensi kerjanya belum ditetapkan oleh Pemerintah, dan seluruh Tenaga Teknis Pertambangan yang Berkompeten sudah menjalankan tugas dan tanggung jawab sesuai ketentuan peraturan perundang-undangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.7', '0', 'Tidak ada bukti yang menunjukkan Pemegang Izin atau IUJP telah membentuk dan menetapkan Komite Keselamatan Pertambangan yang disahkan oleh KTT, PTL, atau PJO sesuai kewenangannya.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.7', '1', 'KTerdapat bukti yang menunjukkan KTT, PTL, atau PJO telah membentuk dan menetapkan Komite Keselamatan Pertambangan, dengan kondisi:
 
•  belum disahkan oleh KTT, PTL, atau PJO sesuai kewenangannya;
 
•  keanggotaannya belum terdapat seluruhnya perwakilan dari Bagian Keselamatan dan Kesehatan Kerja Pertambangan/Keselamatan dan Kesehatan Kerja Pengolahan dan/atau Pemurnian, Bagian Keselamatan Operasi Pertambangan/Pengolahan dan/atau Pemurnian, bagian operasional Pertambangan, dan juga wakil dari Pekerja; dan
 
•  belum seluruh anggota mendapatkan pendidikan dan pelatihan yang disyaratkan sesuai dengan kebutuhan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.7', '2', 'Terdapat bukti yang menunjukkan KTT, PTL, atau PJO telah membentuk dan menetapkan Komite Keselamatan Pertambangan, dengan kondisi:
 
•  telah disahkan oleh KTT, PTL, atau PJO sesuai kewenangannya;
 
•  keanggotaannya belum terdapat seluruhnya perwakilan dari Bagian Keselamatan dan Kesehatan Kerja Pertambangan / Keselamatan dan Kesehatan Kerja Pengolahan dan/atau Pemurnian, Bagian Keselamatan Operasi Pertambangan / Pengolahan dan/atau Pemurnian, bagian operasional Pertambangan, dan juga wakil dari Pekerja; dan
 
•  belum seluruh anggota mendapatkan pendidikan dan pelatihan yang disyaratkan sesuai dengan kebutuhan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.7', '3', 'Terdapat bukti yang menunjukkan KTT atau PTL telah membentuk dan menetapkan Komite Keselamatan Pertambangan, dengan kondisi:
 
•  telah disahkan oleh KTT, PTL, atau PJO sesuai kewenangannya;
 
•  keanggotaannya telah terdapat seluruhnya perwakilan dari Bagian Keselamatan dan Kesehatan Kerja Pertambangan/Keselamatan dan Kesehatan Kerja Pengolahan dan/atau Pemurnian, Bagian Keselamatan Operasi Pertambangan/Pengolahan dan/atau Pemurnian, bagian operasional Pertambangan, dan juga wakil dari Pekerja;
 
•  belum seluruh anggota mendapatkan pendidikan dan pelatihan yang disyaratkan sesuai dengan kebutuhan; dan
 
•  belum sepenuhnya menjalankan tugas dan tanggung jawab sesuai ketentuan peraturan perundang-undangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.7', '4', 'Terdapat bukti yang menunjukkan KTT, PTL, atau PJO telah membentuk dan menetapkan Komite Keselamatan Pertambangan, dengan kondisi:
 
•  telah disahkan oleh KTT, PTL, atau PJO sesuai kewenangannya;
 
•  keanggotaannya telah terdapat seluruhnya perwakilan dari Bagian Keselamatan dan Kesehatan Kerja Pertambangan/Keselamatan dan Kesehatan Kerja Pengolahan dan/atau Pemurnian, Bagian Keselamatan Operasi Pertambangan/Pengolahan dan/atau Pemurnian, bagian operasional Pertambangan, dan juga wakil dari Pekerja;
 
•  seluruh anggota mendapatkan pendidikan dan pelatihan yang disyaratkan sesuai dengan kebutuhan; dan
 
•  telah sepenuhnya menjalankan tugas dan tanggung jawab sesuai ketentuan peraturan perundang-undangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.8', '0', 'Tidak ada bukti yang menunjukkan KTT atau PTL telah menunjuk Tenaga Teknis Pertambangan yang Berkompeten, yang telah memiliki sertifikasi sesuai standar kompetensi kerja yang berlaku yang ditetapkan oleh Pemerintah.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.8', '1', 'Terdapat bukti yang menunjukan KTT atau PTL telah menunjuk tim tanggap darurat, dengan kondisi :
 
•  belum dilaporkan kepada KaIT atau Kepala Dinas atas nama KaIT;
•  Belum memadai, belum mencakup seluruh area kerja, dan/ atau belum selalu siaga setiap saat;
•  belum memiliki keterampilan dan kompetensi yang diperlukan untuk memberikan layanan terhadap keadaan darurat; dan
•  belum mendapat pendidikan dan pelatihan untuk menjaga dan meningkatkan keterampilan yang diperlukan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.8', '2', 'Terdapat bukti yang menunjukan KTT atau PTL telah menunjuk tim tanggap darurat, dengan kondisi :
 
•  telah dilaporkan kepada KaIT atau Kepala Dinas atas nama KaIT;
•  Belum memadai, belum mencakup seluruh area kerja, dan/atau belum selalu siaga setiap saat;
•  belum memiliki keterampilan dari kompetensi yang diperlakukan untuk memberikan layanan terhadap keadaan darurat; dan
•  belum mendapat pendidikan dan pelatihan untuk menjaga dan meningkatkan keterampilan yang diperlukan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.8', '3', 'Terdapat bukti yang menunjukan KTT atau PTL telah menunjuk tim tanggap darrat, dengan kondisi:
 
•  telah dilaporkan kepada KaIT atau Kepala dinas atas nama KaIT;
•  telah memadai, mencakuo seluruh area kerja, dan siaga setiap saat;
•  Telah memiliki keterampilan dan kompetensi yang diperlukan untuk memberikan layanan terhadap keadaan darurat; dan
•  Berlum mendapat pendidikan dan pelatihan untuk menjaga  dan menungkatkan ketermapilan yang diperlukan');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.8', '4', 'Terdapat bukti menunjukan KTT atau PTL telah menunjuk tim tanggap darurat, dengan kondisi;
 
•  telah dilaportkan kepada KaIT atau Kepala dianas atas nama KaIT;
•  Telah memadai, mencakup seluruh area kerja, dan selalu siap setiap saat
•  Telah memiliki keterampilan dan kompetensi yang diperuntukan untuk memberikan layanan terhadap keadaan darurat; dan
•  Telah mendapat pendidikan dan pelatihan untuk menjaga dan meningkatkan keterampilan yang diperlukan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.9', '0', 'Tidak ada bukti yang menunjukan pemegang izin atau IUJP telah mengatur sistem seleksi dan penempatan personek dakam aturan tertulis, memasukan persyaratan aspek keselamatan pertambangan di dalamnya, dan setiap personel memiliki tugas dan tanggung jawab yang jelas dan didalamnya mencakup aspek keselamatan pertambangan');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.9', '1', 'terdapat bukti yang menunjukan pemegang izin atau IUJP telah mengatur sistem seleksi dan penempatan personel dalam aturan tertulis, dengan kondisi:
 
•  Belum mempertimbangkan hasil identifikasi kompetensi kerja;
•  Belum memasukan persyaratan aspek keselamatan pertambangan di dalamnya; dan
•  setiap personel belum memiliki tugas dan tanggung jawab yang jelas dan di dalamnya mencakup aspek keselamatan pertambangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.9', '2', 'Terdapat bukti yang menunjukan pemegang IUP, IUPK, IUP Operasi Produksi Khusus untuk pengolahan dan/atau pemurnian, IPR, atau IUJP telah mengatur sistem seleksi penempatan personel dalam aturan tertulis, degan kondisi
 
•  Belum mempertimbangkan hasil identifikasi kompetensi kerja;
•  belum memasukan persyaratan aspek keselamatn pertambangan di dalamnya; dan
•  setiap personel telah memiliki tugas dan tanggung jawab yang jelas dan didalamnya mencakup aspek keselamatan pertambangan.  ');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.9', '3', 'Sudah ada sistem tertulis yang mempertimbangkan kompetensi, memasukkan persyaratan KP, DAN menetapkan tugas/tanggung jawab KP yang jelas, TAPI belum setiap personel memahami dan menjalankan tugas dan tanggung jawab KP tersebut.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.9', '4', 'Sudah ada sistem tertulis yang mempertimbangkan kompetensi, memasukkan persyaratan KP, menetapkan tugas/tanggung jawab KP yang jelas, DAN setiap personel memahami serta menjalankan tugas dan tanggung jawab KP (Sistem Tertulis, Sesuai Kompetensi, dan Terimplementasi Penuh).');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.10.1', '0', 'Tidak ada bukti yang menunjukan pemegang izin atau IUJP telah menyelenggarakan dan melaksanakan pendidikan dan pelatihan kepada setiap pekerja, pengawas operasional, dan pengawas teknik.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.10.1', '1', 'terdapat bukti yang menunjukan pemegang izin atau IUJP telah menyelenggarakan dan melaksanakan pendidikan dan pelatihan kepada setiap pekerja, pengawas operasional, dan pengawas teknik, dengan kondisi :
 
•  Pengumpulan data dan informasi yang mencakup identifikasi pekerjaan dan identifikasi pekerja belum dilakukan atau belum dilakukan secara memadai;
•  penyusunan analisis kebutuhan pendidikan dan pelatihan (traning need analysis) belum dilakukan atau belum dilakukan secara memadai
•  program pendidikan dan pelatihan belum direncanakan berdasarkan analisis kebutuhan pendidikan dan pelatihan (traning need analysis);
•  pelaksanaan monitoring dan evaluasi program pendidikan dan pelatihan belum dilakukan secara memadai; dan
•  hasil monitoring dan evaluasi program pendidikan dan pelatihan belum ditindaklanjuti untuk menjamun perbaikan berkelanjutan');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.10.1', '2', 'terdapat bukti yang menunjukan pemegang izin atau IUJP telah menyelengkarakan dan melaksanakan pendidikan dan pelatihan kepada setiap pekerja, pengawas operasional, dan pengawas teknik, dengan kondisi:
 
•  pengumpulan data dan informasi yang mencakup identifikasi pekerjaan telah dilakukan secara memadai;
•  penyusunan analisis kebutuhan pendidikan dan pelatihan (traning need analysis) telah dilakukan secara memadai;
•  program pendidikan dan pelatihan telah direncanakan berdasarkan analisis kebutuhan pendidikan dan pelatihan (traning need analysis);
•  pelaksanaan monitoring dan evaluasi prohram pendidikan dan pelatihan telah dilakukan secara memadai; dan 
•  hasil monitoring dan evaluasi program pendidikan dan pelatihan sebelum ditindaklanjuti untuk menjamin perbaikan berkelanjutan');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.10.1', '3', 'Terdapat bukti yng menunjukan pemegang izin atau IUJP telah menyelenggarakan dan melaksanakan pendidikan dan pelatihan kepada setiap pekerja, pengawas teknik dengan kondisi:
 
•  pengumpulan data dan informasi yang mencakup identifikasi pekerjaan dan identifikasi pekerja telah dilakukan secara memadai;
•  penyusunan analisi kebutuhan pendidikan pelatihan (traning need analysis) telah dilakukan secara memadai;
•  program pendidikan dan pelatihan telah direncanakan berdasarkan analisis kebutuhan pendidikan dan pelatihan(traning need analysis) 
•  pelaksanaan monitoring dan evaluasi program pendidikan dan pelatihan telah dilakukan secara memadai;
•  hasil monitoring dan evaluasi program pendidikan dan pelatihan telah dilakukan secara memadai;
•  hasil monitoring dan evaluasi program pendidikan dan pelatihan belum mencapai tingkat keterca[aoam target, dan sasaran yang diharapkan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.10.1', '4', 'terdapat bukti yang menunjukkan pemegang izin atau IUJP telah menyelenggarakan dan melaksanakan pendidikan dan pelatihan kepada setiap pekerja, pengawas operasional, dan pengawas teknik, dengan kondisi:
 
•  Pengumpulan data dan informasi yang mencakup identifikasi pekerjaan dan identifikasi pekerja telah dilakukan secara memadai;
•  penyusunan analisis kebutuhan pendidikan dan pelatihan (traning need analysis) telah dilakukan secara memadai;
•  program pendidikan dan pelatihan telah direncanakan berdasarkan analisis kebutuhan pendidikan dan pelatihan (traning need analysis)
•  pelaksanaan monitoring dan evaluasi program pendidikan dan pelatihan dan dilakukan secara memadai;
•  hasil monitoring dan evaluasi program pendidikan dan pelatihan telah ditindaklanjuti untuk menjamun perbaikan berkelanjutan; dan
•  program pendidikan dan pelatihan telah mencapai tingkat ketercapaian target, dan sasaran yang diharapkan');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.10.2', '0', 'tidak ada bukti yang menunjukan pemegang izin atau IUJP telah mengidentidikasi dan mengembangkan standar kompetensi kerja keselamatan pertambangan sesuai kebutuhan, menggunakan hasil identifikasi kompetensi kerja digunakan sebagai dasar petimbangan dalam penerimaan, seleksi, promosi, dan penelitia kinerja, dan memastikan pengawas operasional memiliki kompetensi.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.10.2', '1', 'terdapat bukti yang menunjukan pemegang izin atau IUJP telah mengidentifikasi standar kompetensi kerja keselamatan pertambangan, dengan kondisi:
 
•  belumseluruh pekerja, pengawas operasional, dan pengawas teknik memiliki kompetensi yang sesuai dengan ketentuan peraturan perundangan, standar nasional, standar internasional, dan/atau standar kompetensi kerja keselamatan pertambangan yang dikembangkan oleh pemegang izin atau IUJP;
•  hasil identidikasi kompetensi kerja belum digunakan sebagai dasar penetuan program pendidikan dan penelitan, dan pertimbangan dalam penerimaan, seleksi, promosi, dan penilaian kinerja.
•  hasil identifikasi kopetensi kerja belum digunakan sebagai dasar pertimbangan dalam penerimaan, seleksi, promosi, dan penilaian kinerja; dan
•  standar kompetensi kerja keselamatan pertambangan belum dikembangkan sesuai kebutuhan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.10.2', '2', 'terdapat bukti yang menunjukan pemegang izin atau IUJP telah mengidentifikasi standar kompetensi kerja keselamatan pertambangan, dengan kondisi:
 
•  sebagai pekerja, pengawas operasional, dan pengawas teknik memiliki kompetensi yang sesuai dengan ketentuan peraturan perundangan, standar nasional, standar internasional, dan/atau standar kompetensi kerja keselamatan pertambangan yang dikembangkan oleh pemegang izin atau IUJP;
•  hasil identifikasi kompetensi kerja telah digunakan sebagai dasar penentuan program pendidikan dan pelatihan;
•  hasil identifikasi kompetensi kerja belum digunakan sebagai dasar pertimbangan dalam penerimaan, seleksi, promosi, dan penilaian kinerja; dan
•  standar kompetensi kerja keselamatan kerja pertambangan belum dikembangkan sesuai kebutuhan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.10.2', '3', 'terdapat bukti yang menunjukan pemegang izin atau IUJP telah mengidentifikasi standar kompetensi kerja keselamatan pertambangan dengan kondisi;
 
•  seluruhpekerja, pengawas operasional, dan pengawas teknik memiliki kompetensi yang sesuai dengan ketentuan peraturan perundangan, standar nasional, standar internasional, dan/atau standar kompetensi kerja keselamatan pertambangan yang dikembangkan oleh pemegang izin atau IUJP;
•  hasil identifikasi kompetensi kerja telah digunakan sebagai dasar penetuan program pendidikan dan pelatihan;
•  hasil idebtifikasi kompetensi kerja telah digunakan sebagai dasar pertimbangan dalam penerimaan, seleksi, promosi, dan penilaian kinerja;dan
•  standar kompetensi kerja keselamatan pertambangan belum dikembangkan sesuai kebutuhan');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.10.2', '4', 'terbukti yang menunjukan pemegang zin atau IUJP telah mengidentifikasi standar kompetensi keselamatan pertambangan, dengan kondisi:
 
•  seluruh pekerja, pengawas operasional, dan pengawas teknik memiliki kompetensi yang sesuai dengan ketentuan peraturan perundangan, standar nasional, standar internasional, dan/atau standar kompetensi kerja keselamatan pertambangan yang dikembangkan oleh pemegang izin atau IUJP memiliki kompetensi yang dipersyaratkan;
•  hasil identifikasi kompetensi kerja telah digunakan sebagai dasar penentuan program pendidikan dan pelatihan;
•  hasil identifikasi kopetensi kerja digunakan sebagai dasar pertimbangan dalam penerimaan, seleksi, promosi, dan penilaian kinerja; dan
•  standar kompetensi kerja keselamatan pertambangan telah dikembangkan sesuai kebutuhan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.11', '0', 'tidak ada bukti yang menunjukan pemegang izin atau IUJP telah menyusun, menetapkan, dan menerapkan mekanisme untuk mengkomunikasikan hal-hal yang memiliki dampak terhadap keselamatan pertambangan kepada pihak-pihak terkait.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.11', '1', 'terdapat bukti yang menunjukan:
 
•  pemegang izin atau IUJP telah menyusun, dan menetapkan mekanisme untuk mengkomunikasikan hal-hal yang memiliki dampak terhadap keselamatan pertambangan kepda pihak-pihak terkait; 
•  pemegang izin atau IUJP belum menerapkan mekanisme untuk mengkomunikasikan hal-hal yang memiliki dampak terhadap keselamatan pertambangan kepada pihak-pihak terkait; pemegang izin atau IUJP belum melakukan evaluasi penyampaian informasi kepada pihak-pihak terkait tersebut;
•  pemegang izin atau IUJP belum melakukan evaluasi penyiampaian informasi kepada pihak-pihak terkait tersebut;
•  pemegang izin atau IUJP telah menyusun dan menetapkan mekanisme mengkomunikasikan apabila ada informasi kecelakaan tambang, kejadian berbahaya, kejadian akibat penyakit tenaga kerja, penyakit akibat kerja, kondisi darurat lainnya yang terjadi, dan hal-hal yang memiliki dampak terhadap keselamatan pertambangan, baik di dalam pemegang izin atau IUJP; dan
•  pemegang izin atau IUJP belum menerapkan mekanisme untuk mengkomunikasikan apabila ada informasi kecelakaan tambang, kejadian berbahaya, kejadian akibat penyakit tenaga kerja, penyakit akibat kerja, kondisi darurat lainnya yang terjadi, dan hal-hal yang memiliki dampak terhadap keselamatan pertambangan, baik di dalam pemegang izin atau IUJP');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.11', '2', 'terdapat bukti yang menunjukan :
 
•  pemegan izin atau IUJP telah menyusun, dan menetap kan mekanisme untuk mengkomunikasikan hal-hal yang memiliki dampak terhadap keselamatan pertambangan kepada pihak- pihak terkait
•  pemegang izin atau IUJP telah menerapkan mekanisme untuk mengkomunikasikan hal-hal yang memiliki dampak terhadap keselamatan pertambangan kepada pihak-pihak terkait;
•  pemegang izin atau IUJP telah menyusun dan menetapkan mekanisme untuk mengkomunikasikan apabila ada informasi kecelakaan tambnag , kejadian berbahaya, kejadian akibat penyakit tenaga kerja, penyakit akibat kerja, kondisi darurat lainnya yang terjadi, dan hal-hal yang memiliki dampak terhadap keselamatan pertambangan, baik di dalampemegang IUP, IUPK, IUP Operasi Produksi Khusus untuk pengelolahan dan/atau pemurnian, IPR, atau IUJP; dan
•  pemegang izin atau IUJP belum melakukan evaluasi penyampaian informasi kepada pihak-pihak terkait tersebut.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.11', '3', 'terdapat bukti yang menunjukan:
 
•  pemegang izin atau IUJP telah menyusun, dan menetapkan mekanisme untuk mengkomunikasikan hal-hal yang memiliki dampak terhadap keselamatan pertambangan kepda pihak-pihak terkait; 
•  pemegang izin atau IUJP telah menerapkan mekanisme untuk mengkomunikasikan hal-hal yang memiliki dampak terhadap keselamatan pertambangan kepada pihak-pihak terkait
•  pemegang izin atau IUJP telah menerapkan mekanisme untuk mengkomunikasikan apabila ada informasi kecelakaan tambang, kejadian berbahaya, kejadian akibat penyakit tenaga kerja, penyakit akibat kerja, kondisi darurat lainnya yang terjadi, dan hal-hal yang memiliki dampak terhadap keselamatan pertambangan, baik didalam pemegang izin atau IUJP;
•  pemegang izin atau IUJP telah melakukan evaluasi penyampaian informasi kepada pihak-pihak terkait tersebut; dan
•  informasi yang disampaikan belum ditindaklanjuti oleh pihak-pihak terkait yang dapat dikontrol oleh KTT atau PTL');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.11', '4', 'terdapat bukti yang menunjukan:
 
•  pemegang izin atau IUJP telah menyusun, dan menetapkan mekanisme untuk mengkomunikasikan hal-hal yang memiliki dampak terhadap keselamatan pertambangankepada pihaj-pihak terkait
•  pemegang izin atau IUJP telah menerapkan mekanisme untuk mengkomunikasikan hal-hal yang memiliki dampak terhadap keselamatan pertambangan kepada pihak-pihak terkait
•  pemegang  izin atau IUJP telah menyusun dan menetapkan mekanisme untuk mengkomunikasikan apabila ada informasi kecelakaan tambang , kejadian berbahaya, kejadin akibat kerja, kejadian akibat penyakit tenaga kerja, kondisi darurat lainnya yang terjadi dan hal-hal yang memiliki dampak terhadap keselamatan pertambangan baik di dalam pemegang izin atau IUJP;
•  pemegang izin atau IUJP telah melakukan evaluasi penyampaian informasi kepada pihak-pihak terkait tersebut; dan
•  informasi yang disampaikan telah ditindaklanjuti oleh pihak- pihaj terkait yang dapat dikontrol oleh KTT atau PTL.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.12.1', '0', 'tidak ada bukti yang menunjukan pemegan IUP, IUPKm IUP Operasi produksu khusus telah memiliki buku tambang');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.12.1', '1', 'terdapat bukti yang menunjukan pemegang IUP, IUPK, IUP Operasi Produksi Khusus telah memiliki buku tambang yang tersedia di kantor KTT atau PTL, dengan kondisi:
 
•  buku tambang belum dapat dibaca dan dipelajari oleh pekerja
•  KTT atau PTL belum melaksanakan larangan, perintah, penynjuk inspektur tambang dalam buku tambang; atau
•  KTT atau PTL belum mencatan hal-hal yang diwajibkan untuk didaftarkan di buku tambang berdasarkan ketentuan peraturan perundangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.12.1', '2', 'terdapat bukti yang menunjukan pemegang IUP, IUPK IUP Operasi Produksi khusus yang tersedia di kantor KTT atau PTL, dengan kondisi:
 
•  buku tambang dapat dibaca dan dipelajari pekerja
•  KTT atau PTL telah melaksanakan sebagian larangan, perintah dan petunjuka inspektur tambang dalam buku tambang; atau
•  KTT atau PTL telah mencatat sebagian hal-hal yang diwajibkan untuk didaftarkan di buku tambang berdasarkan ketentuan peraturan perundangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.12.1', '3', 'terdapat buktu yang menunjukan pemegang IUP, IUPKm IUP Operasi Produksi khusus telah memiliki buku tambang yang tersedia di kantor KTT atau PTL, dengan kondisi:
•  buku tambang dapat dibaca dan dipelajari oleh pekerja;
•  KTT atau PTL belum memastikan bahwa memahami isi dari buku tambang;
•  KTT atau PTL telah melaksanakan seluruh larangan, perintah dan petunjuk inspektur tambang dalam buku tambang; atau
•  KTT atau PTL telah mencatat seluruh hal-hal yang diwajibkan untuk didaftarkan di buku tambang berdasarkan ketentuan peraturan perundangan');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.12.1', '4', 'terdapat buktu yang menunjukan pemegang IUP, IUPKm IUP Operasi Produksi khusus telah memiliki buku tambang yang tersedia di kantor KTT atau PTL, dengan kondisi:
•  buku tambang dapat dibaca dan dipelajari oleh pekerja;
•  KTT atau PTL telah memastikan bahwa pekerja memahami isi dari buku tambang;
•  KTT atau PTL telah melaksanakan seluruh larangan, perintah, petunjuk inspektur tambang dalam buku tambang; atau
•  KTT atau PTL telah mencatat seluruh hal-hal yang diwajibkan untuk didaftarkan dibuku tambang berdasarkan peraturan perundangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.12.2', '0', 'tidak ada bukti yang menunjukan pemegang IUP, IUPK, IUP Operasi Produksi khusus telah memiliki buku daftar kecelakaan tambang');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.12.2', '1', 'Terdapat bukti yang menunjukan pemegang IUP, IUPK IUP operasi produksi telah memiliki buku daftar kecelakaan tambang, namun KTT atau PTL belum mendaftarkan setiap kecelakaan tambang yang berakibat cedera ringan, berat, dan mati (jika ada) dalam buku daftar kecelakaan tambang.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.12.2', '2', 'terdapat bukti yang menunjukan pemegang IUP, IUPK IUP Operasi Produksi khusus telah memiliki  buku daftar kecelakanaan tambang, namun KTT atau PTL belum seluruhnya mendaftarkan setiap kecelakaan tambang yang berakibat cedera ringan, berat, dan mati (jika ada) dalam buku daftar kecelakaan tambang.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.12.2', '3', 'terdapat bukti yang menunjukan pemegang IUP, IUPK, IUP Operasi produksi khusus telah memiliki bukuku daftar kecelakaan tambang, dan KTT atau PTL telah mendaftarkan setiap kecelakaan tambang yang berakibat cedera ringan, berat, dan mati (jika ada) dalam buku daftar kecelakaan tambang.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.12.3', '0', 'tidak ada bukti yang menunjukan pemegang IUP, IUPK, IUP Operasi produksi khusus telah melakukan pelaporan aspek keselamatan pertambangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.12.3', '1', 'terdapat bukti yang menunjukan pemegang IUP, IUPK, IUP Operasi Produksi Khusus telah melakukan pelaporan aspek keselamatan pertambangan, dengan kondisi:
 
•  pemegang IUP, IUPK, IUP Operasi Produksi Khusus untuk pengelolah dan/atau Pemurnian, IPR, atau IUJP belum menyampaikan seluruh laporan tertulis aspek keselamatan pertambangan kepada KaIT;
•  Pelaporan belum sesuai format yang ditetapkan  ketentuan peraturan perundang-undangan; dan
•  penyampaian laporan tidak memenuhi tata waktu yang menetapkan sesuai ketentua peraturan perundang-undangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.12.3', '2', 'terdapat bukti yang menunjukan pemegang IUP, IUPK, IUP Operasi Produksi Khusus telah melakukan pelaporan aspek keselamatan pertambangan, dengan kondisi:
 
•  pemegang IUP, IUPK, IUP Operasi Produksi Khusus untuk pengelolah dan/atau Pemurnian, IPR, atau IUJP telah menyampaikan seluruh laporan tertulis aspek keselamatan pertambangan kepada KaIT; pelaporan telah sesuai format yang ditentukan peraturan perundang-undangan; dan penyampaian laporan belum memenuhi tata waktu yang ditetapka  sesuai ketentuan peraturan perundang-undangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.12.3', '3', 'terdapat bukti yang menunjukan pemegang IUP, IUPK, IUP Operasi produksi khusus telah  melakukan pelaporan aspek keselamatan pertambangan, dengan kondisi:
 
•  Pemegang izin atau IUJP telah menyampaikan seluruh laporan tertulis aspek keselamatan pertambangan kepada KaIT;
•  pelaporan telah sesuai format ditetapkan ketentuan peraturan perundang-undangan; dan
•  penyampaian laporan telah memenuhi tata waktu yang ditentukan sesuai ketentuan perundang-undangan');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.12.4', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah mendokumentasikan Kejadian Berbahaya, kejadian akibat penyakit tenaga kerja, dan Penyakit Akibat Kerja secara khusus oleh KTT atau PTL sesuai format khusus yang ditentukan oleh KaIT.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.12.4', '1', 'Terdapat bukti yang menunjukkan:

KTT atau PTL telah mendokumentasikan sebagian Kejadian Berbahaya, kejadian akibat penyakit tenaga kerja, dan Penyakit Akibat Kerja secara khusus; dan

dokumentasi Kejadian Berbahaya, kejadian akibat penyakit tenaga kerja, dan Penyakit Akibat Kerja belum menggunakan format khusus yang ditentukan oleh KaIT.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.12.4', '2', 'Terdapat bukti yang menunjukkan:

KTT atau PTL telah mendokumentasikan sebagian Kejadian Berbahaya, kejadian akibat penyakit tenaga kerja, dan Penyakit Akibat Kerja secara khusus; dan

dokumentasi Kejadian Berbahaya, kejadian akibat penyakit tenaga kerja, dan Penyakit Akibat Kerja telah menggunakan format khusus yang ditentukan oleh KaIT.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.12.4', '3', 'Terdapat bukti yang menunjukkan:

KTT atau PTL telah mendokumentasikan seluruh Kejadian Berbahaya, kejadian akibat penyakit tenaga kerja, dan Penyakit Akibat Kerja secara khusus; dan

dokumentasi Kejadian Berbahaya, kejadian akibat penyakit tenaga kerja, dan Penyakit Akibat Kerja telah menggunakan format khusus yang ditentukan oleh KaIT.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.12.5', '0', 'Tidak ada bukti yang menunjukkan Pemegang Izin atau IUJP telah mendokumentasikan, memantau, dan/atau melaporkan dokumen dan laporan pemenuhan kompetensi sesuai ketentuan peraturan perundang-undangan serta persyaratan lainnya.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.12.5', '1', 'Terdapat bukti yang menunjukkan:

Pemegang Izin atau IUJP telah mendokumentasikan sebagian dokumen kelayakan sarana, prasarana, dan instalasi Pertambangan; sertifikat dan laporan kompetensi tenaga kerja; lisensi antara lain Kartu Izin Meledakkan, Kartu Pekerja Peledakan, Kartu Pengawas Operasional, dan/atau surat izin mengoperasikan unit yang dikeluarkan oleh KTT, PTL, atau orang yang ditunjuk oleh KTT atau PTL; pengesahan KTT, PTL, wakil KTT, wakil PTL, dan/atau Kepala Tambang Bawah Tanah; dan izin kerja khusus antara lain Izin Kerja Ruang Terbatas, Izin Kerja di Ketinggian, Izin Kerja Panas, Izin Kerja Terpapar Radioaktif; dan

Pemegang Izin atau IUJP belum melakukan pemantauan dan pelaporan pemenuhan kompetensi sesuai ketentuan peraturan perundang-undangan serta persyaratan lainnya.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.12.5', '2', 'Terdapat bukti yang menunjukkan:

Pemegang Izin atau IUJP telah mendokumentasikan seluruh dokumen kelayakan sarana, prasarana, dan instalasi Pertambangan; sertifikat dan laporan kompetensi tenaga kerja; lisensi antara lain Kartu Izin Meledakkan, Kartu Pekerja Peledakan, Kartu Pengawas Operasional, dan/atau surat izin mengoperasikan unit yang dikeluarkan oleh KTT, PTL, atau orang yang ditunjuk oleh KTT atau PTL; pengesahan KTT, PTL, wakil KTT, wakil PTL, dan/atau Kepala Tambang Bawah Tanah; dan izin kerja khusus antara lain Izin Kerja Ruang Terbatas, Izin Kerja di Ketinggian, Izin Kerja Panas, Izin Kerja Terpapar Radioaktif; dan

Pemegang Izin atau IUJP belum melakukan pemantauan dan pelaporan pemenuhan kompetensi sesuai ketentuan peraturan perundang-undangan serta persyaratan lainnya.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.12.5', '3', 'Terdapat bukti yang menunjukkan:

Pemegang Izin atau IUJP telah mendokumentasikan seluruh dokumen kelayakan sarana, prasarana, dan instalasi Pertambangan; sertifikat dan laporan kompetensi tenaga kerja; lisensi antara lain Kartu Izin Meledakkan, Kartu Pekerja Peledakan, Kartu Pengawas Operasional, dan/atau surat izin mengoperasikan unit yang dikeluarkan oleh KTT, PTL, atau orang yang ditunjuk oleh KTT atau PTL; pengesahan KTT, PTL, wakil KTT, wakil PTL, dan/atau Kepala Tambang Bawah Tanah; dan izin kerja khusus antara lain Izin Kerja Ruang Terbatas, Izin Kerja di Ketinggian, Izin Kerja Panas, Izin Kerja Terpapar Radioaktif; dan

Pemegang Izin atau IUJP telah melakukan pemantauan dan pelaporan pemenuhan kompetensi sesuai ketentuan peraturan perundang-undangan serta persyaratan lainnya.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.13', '0', 'Tidak ada bukti yang menunjukkan Pemegang Izin atau IUJP telah melakukan penyusunan, penerapan, dan pendokumentasian partisipasi, konsultasi, motivasi, dan kesadaran penerapan SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.13', '1', 'Terdapat bukti yang menunjukkan Pemegang Izin atau IUJP telah melakukan penyusunan mekanisme partisipasi, konsultasi, motivasi, dan kesadaran penerapan SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian, namun belum menerapkan dan mendokumentasikan partisipasi, konsultasi, motivasi, dan kesadaran penerapan SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.13', '2', 'Terdapat bukti yang menunjukkan Pemegang Izin atau IUJP telah melakukan penyusunan, penerapan, dan pendokumentasian partisipasi, konsultasi, motivasi, dan kesadaran penerapan SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian, namun belum melibatkan seluruh departemen/bagian dari Pekerja maupun pihak lain yang terkait di dalam penerapan dan pengembangan SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.13', '3', 'Terdapat bukti yang menunjukkan Pemegang Izin atau IUJP telah melakukan penyusunan, penerapan, dan pendokumentasian partisipasi, konsultasi, motivasi, dan kesadaran penerapan SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian, dan telah melibatkan seluruh departemen/bagian dari Pekerja maupun pihak lain yang terkait di dalam penerapan dan pengembangan SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('III.13', '4', 'Terdapat bukti yang menunjukkan Pemegang Izin atau IUJP telah melakukan penyusunan, penerapan, dan pendokumentasian partisipasi, konsultasi, motivasi, dan kesadaran penerapan SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian, dan telah melibatkan seluruh departemen/bagian dari Pekerja maupun pihak lain yang terkait di dalam penerapan dan pengembangan SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian. Proses dari partisipasi, konsultasi, motivasi, dan kesadaran dengan seluruh Pekerja dan pihak lain yang terkait menjadi masukan dalam peningkatan penerapan Keselamatan Pertambangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.1.1', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun, menetapkan, menerapkan, mendokumentasikan, dan mengevaluasi prosedur operasi/kerja dengan mempertimbangkan hasil pemetaan behavior based safety.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.1.1', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan prosedur operasi/kerja yang terdokumentasikan, dengan kondisi:

prosedur telah disahkan oleh KTT atau PTL untuk Pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IPR, atau PJO untuk Pemegang IUJP dan diberi nomor

prosedur belum untuk setiap pekerjaan

penyusunan prosedur belum mempertimbangkan hasil pemetaan behavior based safety

prosedur belum dikomunikasikan kepada pihak-pihak terkait

prosedur belum dievaluasi dan ditinjau ulang secara berkala dan apabila terjadi kecelakaan, perubahan peralatan, perubahan proses, dan/atau perubahan bahan,

belum secara konsisten diterapkan oleh seluruh Pekerja dalam melaksanakan pekerjaannya.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.1.1', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan prosedur operasi/kerja yang terdokumentasikan, dengan kondisi:

prosedur telah disahkan oleh KTT atau PTL untuk Pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IPR, atau PJO untuk Pemegang IUJP dan diberi nomor,

prosedur telah terdapat untuk setiap pekerjaan,

penyusunan prosedur telah mempertimbangkan hasil pemetaan behavior based safety,

prosedur telah dikomunikasikan kepada pihak-pihak terkait,

prosedur belum dievaluasi dan ditinjau ulang secara berkala dan apabila terjadi kecelakaan, perubahan peralatan, perubahan proses, dan/atau perubahan bahan,

belum secara konsisten diterapkan oleh seluruh Pekerja dalam melaksanakan pekerjaannya.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.1.1', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan prosedur operasi/kerja yang terdokumentasikan, dengan kondisi:

prosedur telah disahkan oleh KTT atau PTL untuk Pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IPR, atau PJO untuk Pemegang IUJP dan diberi nomor,

prosedur telah terdapat untuk setiap pekerjaan

penyusunan prosedur telah mempertimbangkan hasil pemetaan behavior based safety,

prosedur telah dikomunikasikan kepada pihak-pihak terkait,

prosedur telah dievaluasi dan ditinjau ulang secara berkala dan apabila terjadi kecelakaan, perubahan peralatan, perubahan proses, dan/atau perubahan bahan,

belum secara konsisten diterapkan oleh seluruh Pekerja dalam melaksanakan pekerjaannya.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.1.1', '4', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan prosedur operasi/kerja yang terdokumentasikan, dengan kondisi:

prosedur telah disahkan oleh KTT atau PTL untuk Pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IPR, atau PJO untuk Pemegang IUJP dan diberi nomor,

prosedur telah terdapat untuk setiap pekerjaan,

penyusunan prosedur telah mempertimbangkan hasil pemetaan behavior based safety,

prosedur telah dikomunikasikan kepada pihak-pihak terkait,

prosedur telah dievaluasi dan ditinjau ulang secara berkala dan apabila terjadi kecelakaan, perubahan peralatan, perubahan proses, dan/atau perubahan bahan,

telah secara konsisten diterapkan oleh seluruh Pekerja dalam melaksanakan pekerjaannya.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.1.2', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun, menetapkan, menerapkan, mendokumentasikan, dan mengevaluasi izin kerja khusus dengan mempertimbangkan hasil pemetaan behavior based safety.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.1.2', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan izin kerja khusus yang terdokumentasikan, dengan kondisi:

penyusunan izin kerja khusus belum mempertimbangkan hasil pemetaan behavior based safety,

izin kerja khusus belum dievaluasi secara berkala,

izin kerja khusus belum secara konsisten diterapkan oleh seluruh Pekerja.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.1.2', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan Izin Kerja Khusus yang terdokumentasikan, dengan kondisi:

penyusunan izin kerja khusus telah mempertimbangkan hasil pemetaan behavior based safety,

izin kerja khusus belum dievaluasi secara berkala,

izin kerja khusus belum secara konsisten diterapkan oleh seluruh Pekerja.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.1.2', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan izin kerja khusus yang terdokumentasikan, dengan kondisi:

penyusunan izin kerja khusus telah mempertimbangkan hasil pemetaan behavior based safety,

izin kerja khusus telah dievaluasi secara berkala,

izin kerja khusus telah secara konsisten diterapkan oleh seluruh Pekerja.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.1.3', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun, menetapkan, menerapkan, mendokumentasikan, dan mengevaluasi pengelolaan Alat Pelindung Diri/Alat Keselamatan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.1.3', '1', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menilai kebutuhan Alat Pelindung Diri/Alat Keselamatan yang sesuai dengan jenis pekerjaan dan bahaya yang timbul, menentukan dan menyediakan Alat Pelindung Diri/Alat Keselamatan dengan jumlah yang memadai secara cuma-cuma;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP belum melaksanakan pelatihan untuk Pekerja yang terkait dengan fungsi, manfaat, penggunaan, dan perawatan Alat Pelindung Diri/Alat Keselamatan; dan

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP belum melakukan evaluasi kepatuhan terhadap penggunaan dan perawatan Alat Pelindung Diri/Alat Keselamatan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.1.3', '2', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menilai kebutuhan Alat Pelindung Diri/Alat Keselamatan yang sesuai dengan jenis pekerjaan dan bahaya yang timbul, menentukan dan menyediakan Alat Pelindung Diri/Alat Keselamatan dengan jumlah yang memadai secara cuma-cuma;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melaksanakan pelatihan untuk Pekerja yang terkait dengan fungsi, manfaat, penggunaan, dan perawatan Alat Pelindung Diri/Alat Keselamatan; dan

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP belum melakukan evaluasi kepatuhan terhadap penggunaan dan perawatan Alat Pelindung Diri/Alat Keselamatan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.1.3', '3', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menilai kebutuhan Alat Pelindung Diri/Alat Keselamatan yang sesuai dengan jenis pekerjaan dan bahaya yang timbul, menentukan dan menyediakan Alat Pelindung Diri/Alat Keselamatan dengan jumlah yang memadai secara cuma-cuma;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melaksanakan pelatihan untuk Pekerja yang terkait dengan fungsi, manfaat, penggunaan, dan perawatan Alat Pelindung Diri/Alat Keselamatan;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan evaluasi kepatuhan terhadap penggunaan dan perawatan Alat Pelindung Diri/Alat Keselamatan; dan

berdasarkan hasil evaluasi kepatuhan ditemukan bahwa belum seluruh Pekerja patuh dalam penggunaan dan perawatan Alat Pelindung Diri/Alat Keselamatan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.1.3', '4', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menilai kebutuhan Alat Pelindung Diri/Alat Keselamatan yang sesuai dengan jenis pekerjaan dan bahaya yang timbul, menentukan dan menyediakan Alat Pelindung Diri/Alat Keselamatan dengan jumlah yang memadai secara cuma-cuma;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melaksanakan pelatihan untuk Pekerja yang terkait dengan fungsi, manfaat, penggunaan, dan perawatan Alat Pelindung Diri/Alat Keselamatan;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan evaluasi kepatuhan terhadap penggunaan dan perawatan Alat Pelindung Diri/Alat Keselamatan; dan

berdasarkan hasil evaluasi kepatuhan ditemukan bahwa seluruh Pekerja telah patuh dalam penggunaan dan perawatan Alat Pelindung Diri/Alat Keselamatan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.1', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan bahaya debu sesuai prosedur yang ditetapkan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.1', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan bahaya debu dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasikan mengenai pengelolaan bahaya debu;

antisipasi dan pengenalan bahaya debu dan karakteristiknya termasuk jenis, bentuk, dan ukurannya telah dilakukan;

pengukuran dan penilaian (evaluasi) belum dilakukan secara berkala yang terdokumentasikan;

pengukuran dan penilaian (evaluasi) belum dilakukan oleh Tenaga Teknis Pertambangan yang Berkompeten yang mengacu kepada ketentuan peraturan perundang-undangan;

pengukuran dan penilaian (evaluasi) belum menggunakan alat pemeriksaan yang terbukti telah dikalibrasi dan dipelihara sesuai prosedur; dan

pengendalian risiko terkait debu belum dilakukan mengacu kepada hierarki pengendalian.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.1', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan bahaya debu dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasikan mengenai pengelolaan bahaya debu;

antisipasi dan pengenalan bahaya debu dan karakteristiknya termasuk jenis, bentuk, dan ukurannya telah dilakukan;

pengukuran dan penilaian (evaluasi) telah dilakukan secara berkala yang terdokumentasikan;

pengukuran dan penilaian (evaluasi) telah dilakukan oleh Tenaga Teknis Pertambangan yang Berkompeten yang mengacu kepada ketentuan peraturan perundang-undangan;

pengukuran dan penilaian (evaluasi) telah menggunakan alat pemeriksaan yang terbukti telah dikalibrasi dan dipelihara sesuai prosedur; dan

hasil pengukuran dan penilaian (evaluasi) belum ditindaklanjuti untuk pengendalian risiko terkait sesuai hirarki pengendalian.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.1', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan bahaya debu dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasikan mengenai pengelolaan bahaya debu;

antisipasi dan pengenalan bahaya debu dan karakteristiknya termasuk jenis, bentuk, dan ukurannya telah dilakukan;

pengukuran dan penilaian (evaluasi) telah dilakukan secara berkala yang terdokumentasikan;

pengukuran dan penilaian (evaluasi) telah dilakukan oleh Tenaga Teknis Pertambangan yang Berkompeten yang mengacu kepada ketentuan peraturan perundang-undangan;

pengukuran dan penilaian (evaluasi) telah menggunakan alat pemeriksaan yang terbukti telah dikalibrasi dan dipelihara sesuai prosedur; dan

sebagian hasil pengukuran dan penilaian (evaluasi) telah ditindaklanjuti untuk pengendalian risiko terkait debu sesuai hirarki pengendalian.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.1', '4', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan debu dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasikan mengenai pengelolaan bahaya debu;

antisipasi dan pengenalan bahaya debu dan karakteristiknya termasuk jenis, bentuk, dan ukurannya telah dilakukan;

pengukuran dan penilaian (evaluasi) telah dilakukan secara berkala yang terdokumentasikan;

pengukuran dan penilaian (evaluasi) telah dilakukan oleh Tenaga Teknis Pertambangan yang Berkompeten yang mengacu kepada ketentuan peraturan perundang-undangan;

pengukuran dan penilaian (evaluasi) telah menggunakan alat pemeriksaan yang terbukti telah dikalibrasi dan dipelihara sesuai prosedur; dan

seluruh hasil pengukuran dan penilaian (evaluasi) telah ditindaklanjuti untuk pengendalian risiko terkait debu sesuai hirarki pengendalian.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.2', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan bahaya kebisingan sesuai prosedur yang ditetapkan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.2', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan bahaya kebisingan dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasikan mengenai pengelolaan bahaya kebisingan;

antisipasi dan pengenalan bahaya kebisingan pada setiap area kerja telah dilakukan;

pengukuran dan penilaian (evaluasi) belum dilakukan secara berkala yang terdokumentasikan;

pengukuran dan penilaian (evaluasi) belum dilakukan oleh Tenaga Teknis Pertambangan yang Berkompeten yang mengacu kepada ketentuan peraturan perundang-undangan;

pengukuran dan penilaian (evaluasi) belum menggunakan alat pemeriksaan yang terbukti telah dikalibrasi dan dipelihara sesuai prosedur; dan

pengendalian risiko terkait kebisingan belum dilakukan mengacu kepada hierarki pengendalian.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.2', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan bahaya kebisingan dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasikan mengenai pengelolaan bahaya kebisingan;

antisipasi dan pengenalan bahaya kebisingan pada setiap area kerja telah dilakukan;

pengukuran dan penilaian (evaluasi) telah dilakukan secara berkala yang terdokumentasikan;

pengukuran dan penilaian (evaluasi) telah dilakukan oleh Tenaga Teknis Pertambangan yang Berkompeten yang mengacu kepada ketentuan peraturan perundang-undangan;

pengukuran dan penilaian (evaluasi) telah menggunakan alat pemeriksaan yang terbukti telah dikalibrasi dan dipelihara sesuai prosedur; dan

hasil pengukuran dan penilaian (evaluasi) belum ditindaklanjuti untuk pengendalian risiko terkait kebisingan sesuai hirarki pengendalian.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.2', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan bahaya kebisingan dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasikan mengenai pengelolaan bahaya kebisingan;

antisipasi dan pengenalan bahaya kebisingan pada setiap area kerja telah dilakukan;

pengukuran dan penilaian (evaluasi) telah dilakukan secara berkala yang terdokumentasikan;

pengukuran dan penilaian (evaluasi) telah dilakukan oleh Tenaga Teknis Pertambangan yang Berkompeten yang mengacu kepada ketentuan peraturan perundang-undangan;

pengukuran dan penilaian (evaluasi) telah menggunakan alat pemeriksaan yang terbukti telah dikalibrasi dan dipelihara sesuai prosedur; dan

sebagian hasil pengukuran dan penilaian (evaluasi) telah ditindaklanjuti untuk pengendalian risiko terkait kebisingan sesuai hirarki pengendalian.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.2', '4', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan bahaya kebisingan dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasikan mengenai pengelolaan bahaya kebisingan;

antisipasi dan pengenalan bahaya kebisingan pada setiap area kerja telah dilakukan;

pengukuran dan penilaian (evaluasi) telah dilakukan secara berkala yang terdokumentasikan;

pengukuran dan penilaian (evaluasi) telah dilakukan oleh Tenaga Teknis Pertambangan yang Berkompeten yang mengacu kepada ketentuan peraturan perundang-undangan;

pengukuran dan penilaian (evaluasi) telah menggunakan alat pemeriksaan yang terbukti telah dikalibrasi dan dipelihara sesuai prosedur; dan

seluruh hasil pengukuran dan penilaian (evaluasi) telah ditindaklanjuti untuk pengendalian risiko terkait kebisingan sesuai hirarki pengendalian untuk memenuhi Nilai Ambang Batas, paling sedikit mencakup:

tindakan untuk menghilangkan atau mengurangi kebisingan sampai pada batas yang dapat diterima;

pelaksanaan hearing conservation program;

pembatasan jam kerja pekerja yang disesuaikan dengan tingkat kebisingan yang ada pada tempat kerja;

pemasangan rambu yang menginformasikan tingkat kebisingan dan instruksi pengendaliannya;

pembuatan peraturan Pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP dalam upaya mengelola kebisingan di setiap area kerja; dan

penyediaan alat pelindung diri yang sesuai dengan tingkat kebisingan di area kerja.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.3', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengendalian getaran sesuai prosedur yang ditetapkan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.3', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan bahaya getaran dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasikan mengenai pengelolaan bahaya getaran;

antisipasi dan pengenalan bahaya getaran baik pada getaran seluruh tubuh (whole body vibration) maupun getaran tangan dan lengan (hand-arm vibration) pada setiap area kerja telah dilakukan;

pengukuran dan penilaian (evaluasi) belum dilakukan secara berkala yang terdokumentasikan;

pengukuran dan penilaian (evaluasi) belum dilakukan oleh Tenaga Teknis Pertambangan yang Berkompeten yang mengacu kepada ketentuan peraturan perundang-undangan;

pengukuran dan penilaian (evaluasi) belum menggunakan alat pemeriksaan yang terbukti telah dikalibrasi dan dipelihara sesuai prosedur; dan

pengendalian risiko terkait getaran belum dilakukan mengacu kepada hierarki pengendalian.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.3', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan bahaya getaran dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasikan mengenai pengelolaan bahaya getaran;

antisipasi dan pengenalan bahaya getaran baik pada getaran seluruh tubuh (whole body vibration) maupun getaran tangan dan lengan (hand-arm vibration) pada setiap area kerja telah dilakukan;

pengukuran dan penilaian (evaluasi) telah dilakukan secara berkala yang terdokumentasikan;

pengukuran dan penilaian (evaluasi) telah dilakukan oleh Tenaga Teknis Pertambangan yang Berkompeten yang mengacu kepada ketentuan peraturan perundang-undangan;

pengukuran dan penilaian (evaluasi) telah menggunakan alat pemeriksaan yang terbukti telah dikalibrasi dan dipelihara sesuai prosedur; dan

hasil pengukuran dan penilaian (evaluasi) belum ditindaklanjuti untuk pengendalian risiko terkait getaran sesuai hirarki pengendalian.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.3', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan bahaya getaran dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasikan mengenai pengelolaan bahaya getaran;

antisipasi dan pengenalan bahaya getaran baik pada getaran seluruh tubuh (whole body vibration) maupun getaran tangan dan lengan (hand-arm vibration) pada setiap area kerja telah dilakukan;

pengukuran dan penilaian (evaluasi) telah dilakukan secara berkala yang terdokumentasikan;

pengukuran dan penilaian (evaluasi) telah dilakukan oleh Tenaga Teknis Pertambangan yang Berkompeten yang mengacu kepada ketentuan peraturan perundang-undangan;

pengukuran dan penilaian (evaluasi) telah menggunakan alat pemeriksaan yang terbukti telah dikalibrasi dan dipelihara sesuai prosedur; dan

sebagian hasil pengukuran dan penilaian (evaluasi) telah ditindaklanjuti untuk pengendalian risiko terkait getaran sesuai hirarki pengendalian.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.3', '4', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan bahaya terkait getaran dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasikan mengenai pengelolaan bahaya getaran;

antisipasi dan pengenalan bahaya getaran baik pada getaran seluruh tubuh (whole body vibration) maupun getaran tangan dan lengan (hand-arm vibration) pada setiap area kerja telah dilakukan;

pengukuran dan penilaian (evaluasi) telah dilakukan secara berkala yang terdokumentasikan;

pengukuran dan penilaian (evaluasi) telah dilakukan oleh Tenaga Teknis Pertambangan yang Berkompeten yang mengacu kepada ketentuan peraturan perundang-undangan;

pengukuran dan penilaian (evaluasi) telah menggunakan alat pemeriksaan yang terbukti telah dikalibrasi dan dipelihara sesuai prosedur; dan

seluruh hasil pengukuran dan penilaian (evaluasi) telah ditindaklanjuti untuk pengendalian risiko kebisingan sesuai hirarki pengendalian, paling sedikit mencakup:

tindakan untuk mengurangi getaran sampai pada batas yang dapat diterima;

pengaturan pembatasan jam kerja Pekerja yang disesuaikan dengan tingkat getaran pada lengan dan tangan atau seluruh tubuh Pekerja; dan

penyediaan alat pelindung diri.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.4', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan bahaya pencahayaan sesuai prosedur yang ditetapkan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.4', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan bahaya pencahayaan dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasikan mengenai pengelolaan bahaya pencahayaan;

antisipasi dan pengenalan bahaya pencahayaan pada setiap area kerja telah dilakukan;

pengukuran dan penilaian (evaluasi) belum dilakukan secara berkala yang terdokumentasikan;

pengukuran dan penilaian (evaluasi) belum dilakukan oleh Tenaga Teknis Pertambangan yang Berkompeten yang mengacu kepada ketentuan peraturan perundang-undangan;

pengukuran dan penilaian (evaluasi) belum menggunakan alat pemeriksaan yang terbukti telah dikalibrasi dan dipelihara sesuai prosedur; dan

pengendalian risiko terkait pencahayaan belum dilakukan mengacu kepada hierarki pengendalian.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.4', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan bahaya pencahayaan dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasikan mengenai pengelolaan bahaya pencahayaan;

antisipasi dan pengenalan bahaya pencahayaan pada setiap area kerja telah dilakukan;

pengukuran dan penilaian (evaluasi) telah dilakukan secara berkala yang terdokumentasikan;

pengukuran dan penilaian (evaluasi) telah dilakukan oleh Tenaga Teknis Pertambangan yang Berkompeten yang mengacu kepada ketentuan peraturan perundang-undangan;

pengukuran dan penilaian (evaluasi) telah menggunakan alat pemeriksaan yang terbukti telah dikalibrasi dan dipelihara sesuai prosedur; dan

hasil pengukuran dan penilaian (evaluasi) belum ditindaklanjuti untuk pengendalian risiko terkait pencahayaan sesuai hirarki pengendalian.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.4', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan bahaya pencahayaan dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasikan mengenai pengelolaan bahaya pencahayaan;

antisipasi dan pengenalan bahaya pencahayaan pada setiap area kerja telah dilakukan;

pengukuran dan penilaian (evaluasi) telah dilakukan secara berkala yang terdokumentasikan;

pengukuran dan penilaian (evaluasi) telah dilakukan oleh Tenaga Teknis Pertambangan yang Berkompeten yang mengacu kepada ketentuan peraturan perundang-undangan;

pengukuran dan penilaian (evaluasi) telah menggunakan alat pemeriksaan yang terbukti telah dikalibrasi dan dipelihara sesuai prosedur; dan

sebagian hasil pengukuran dan penilaian (evaluasi) telah ditindaklanjuti untuk pengendalian risiko terkait pencahayaan sesuai hirarki pengendalian.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.4', '4', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan bahaya pencahayaan dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasikan mengenai pengelolaan bahaya pencahayaan;

antisipasi dan pengenalan bahaya pencahayaan pada setiap area kerja telah dilakukan;

pengukuran dan penilaian (evaluasi) telah dilakukan secara berkala yang terdokumentasikan;

pengukuran dan penilaian (evaluasi) telah dilakukan oleh Tenaga Teknis Pertambangan yang Berkompeten yang mengacu kepada ketentuan peraturan perundang-undangan;

pengukuran dan penilaian (evaluasi) telah menggunakan alat pemeriksaan yang terbukti telah dikalibrasi dan dipelihara sesuai prosedur; dan

seluruh hasil pengukuran dan penilaian (evaluasi) telah ditindaklanjuti untuk pengendalian risiko terkait pencahayaan sesuai hirarki pengendalian dengan menyesuaikan pencahayaan terhadap persyaratan pencahayaan lingkungan kerja sesuai area kerja dan aktivitas pekerjaan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.5', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan kuantitas dan kualitas udara kerja sesuai prosedur yang ditetapkan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.5', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan bahaya kuantitas dan kualitas udara kerja dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasikan mengenai pengelolaan bahaya kuantitas dan kualitas udara kerja;

antisipasi dan pengenalan bahaya terkait kuantitas dan kualitas udara kerja pada setiap area kerja telah dilakukan;

pengukuran dan penilaian (evaluasi) belum dilakukan secara berkala yang terdokumentasikan;

pengukuran dan penilaian (evaluasi) belum dilakukan oleh Tenaga Teknis Pertambangan yang Berkompeten yang mengacu kepada ketentuan peraturan perundang-undangan;

pengukuran dan penilaian (evaluasi) belum menggunakan alat pemeriksaan yang terbukti telah dikalibrasi dan dipelihara sesuai prosedur; dan

pengendalian risiko terkait kuantitas dan kualitas udara kerja belum dilakukan mengacu kepada hierarki pengendalian.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.5', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan bahaya kuantitas dan kualitas udara kerja dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasikan mengenai pengelolaan bahaya kuantitas dan kualitas udara kerja;

antisipasi dan pengenalan bahaya terkait kuantitas dan kualitas udara kerja pada setiap area kerja telah dilakukan;

pengukuran dan penilaian (evaluasi) telah dilakukan secara berkala yang terdokumentasikan;

pengukuran dan penilaian (evaluasi) telah dilakukan oleh Tenaga Teknis Pertambangan yang Berkompeten yang mengacu kepada ketentuan peraturan perundang-undangan;

pengukuran dan penilaian (evaluasi) telah menggunakan alat pemeriksaan yang terbukti telah dikalibrasi dan dipelihara sesuai prosedur; dan

hasil pengukuran dan penilaian (evaluasi) belum ditindaklanjuti untuk pengendalian risiko terkait kuantitas dan kualitas udara kerja sesuai hirarki pengendalian.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.5', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan bahaya kuantitas dan kualitas udara kerja dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasikan mengenai pengelolaan bahaya kuantitas dan kualitas udara kerja;

antisipasi dan pengenalan bahaya terkait kuantitas dan kualitas udara kerja pada setiap area kerja telah dilakukan;

pengukuran dan penilaian (evaluasi) telah dilakukan secara berkala yang terdokumentasikan;

pengukuran dan penilaian (evaluasi) telah dilakukan oleh Tenaga Teknis Pertambangan yang Berkompeten yang mengacu kepada ketentuan peraturan perundang-undangan;

pengukuran dan penilaian (evaluasi) telah menggunakan alat pemeriksaan yang terbukti telah dikalibrasi dan dipelihara sesuai prosedur; dan

sebagian hasil pengukuran dan penilaian (evaluasi) telah ditindaklanjuti untuk pengendalian risiko terkait kuantitas dan kualitas udara kerja sesuai hirarki pengendalian.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.5', '4', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan bahaya kuantitas dan kualitas udara kerja dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasikan mengenai pengelolaan bahaya kuantitas dan kualitas udara kerja;

antisipasi dan pengenalan bahaya terkait kuantitas dan kualitas udara kerja pada setiap area kerja telah dilakukan;

pengukuran dan penilaian (evaluasi) telah dilakukan secara berkala yang terdokumentasikan;

pengukuran dan penilaian (evaluasi) telah dilakukan oleh Tenaga Teknis Pertambangan yang Berkompeten yang mengacu kepada ketentuan peraturan perundang-undangan;

pengukuran dan penilaian (evaluasi) telah menggunakan alat pemeriksaan yang terbukti telah dikalibrasi dan dipelihara sesuai prosedur; dan

seluruh hasil pengukuran dan penilaian (evaluasi) telah ditindaklanjuti untuk pengendalian risiko terkait kuantitas dan kualitas udara kerja sesuai hirarki pengendalian, paling sedikit mencakup:

penyesuaian kuantitas dan kualitas udara kerja terhadap persyaratan kuantitas dan kualitas udara kerja;

penyediaan ventilasi yang memadai;

pemasangan rambu peringatan bahaya;

pembuatan peraturan Pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP dalam upaya mengelola kuantitas dan kualitas udara kerja; dan

penyediaan alat pelindung diri yang sesuai dengan kuantitas dan kualitas udara kerja di area kerja.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.6', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan bahaya iklim kerja sesuai prosedur yang ditetapkan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.6', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan bahaya iklim kerja dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasikan mengenai pengelolaan bahaya iklim kerja;

antisipasi dan pengenalan bahaya terkait iklim kerja dengan indikator Index Suhu Basah dan Bola (ISBB) pada setiap area kerja telah dilakukan;

pengukuran dan penilaian (evaluasi) belum dilakukan secara berkala yang terdokumentasikan;

pengukuran dan penilaian (evaluasi) belum dilakukan oleh Tenaga Teknis Pertambangan yang Berkompeten yang mengacu kepada ketentuan peraturan perundang-undangan;

pengukuran dan penilaian (evaluasi) belum menggunakan alat pemeriksaan yang terbukti telah dikalibrasi dan dipelihara sesuai prosedur; dan

pengendalian risiko terkait iklim kerja belum dilakukan mengacu kepada hierarki pengendalian.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.6', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan bahaya iklim kerja dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasikan mengenai pengelolaan bahaya iklim kerja;

antisipasi dan pengenalan bahaya terkait iklim kerja dengan indikator Index Suhu Basah dan Bola (ISBB) pada setiap area kerja telah dilakukan;

pengukuran dan penilaian (evaluasi) telah dilakukan secara berkala yang terdokumentasikan;

pengukuran dan penilaian (evaluasi) telah dilakukan oleh Tenaga Teknis Pertambangan yang Berkompeten yang mengacu kepada ketentuan peraturan perundang-undangan;

pengukuran dan penilaian (evaluasi) telah menggunakan alat pemeriksaan yang terbukti telah dikalibrasi dan dipelihara sesuai prosedur; dan

hasil pengukuran dan penilaian (evaluasi) belum ditindaklanjuti untuk pengendalian risiko terkait iklim kerja sesuai hirarki pengendalian.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.6', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan bahaya iklim kerja dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasikan mengenai pengelolaan bahaya iklim kerja;

antisipasi dan pengenalan bahaya terkait iklim kerja dengan indikator Index Suhu Basah dan Bola (ISBB) pada setiap area kerja telah dilakukan;

pengukuran dan penilaian (evaluasi) telah dilakukan secara berkala yang terdokumentasikan;

pengukuran dan penilaian (evaluasi) telah dilakukan oleh Tenaga Teknis Pertambangan yang Berkompeten yang mengacu kepada ketentuan peraturan perundang-undangan;

pengukuran dan penilaian (evaluasi) telah menggunakan alat pemeriksaan yang terbukti telah dikalibrasi dan dipelihara sesuai prosedur; dan

sebagian hasil pengukuran dan penilaian (evaluasi) telah ditindaklanjuti untuk pengendalian risiko terkait iklim kerja sesuai hirarki pengendalian.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.6', '4', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan bahaya iklim kerja dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasikan mengenai pengelolaan bahaya iklim kerja;

antisipasi dan pengenalan bahaya terkait iklim kerja dengan indikator Index Suhu Basah dan Bola (ISBB) pada setiap area kerja telah dilakukan;

pengukuran dan penilaian (evaluasi) telah dilakukan secara berkala yang terdokumentasikan;

pengukuran dan penilaian (evaluasi) telah dilakukan oleh Tenaga Teknis Pertambangan yang Berkompeten yang mengacu kepada ketentuan peraturan perundang-undangan;

pengukuran dan penilaian (evaluasi) telah menggunakan alat pemeriksaan yang terbukti telah dikalibrasi dan dipelihara sesuai prosedur; dan

seluruh hasil pengukuran dan penilaian (evaluasi) telah ditindaklanjuti untuk pengendalian risiko terkait iklim kerja sesuai hirarki pengendalian, paling sedikit mencakup:

penyesuaian iklim kerja di setiap area kerja yang iklim dengan syarat ketentuan peraturan perundang-undangan atau standar yang diakui;

penyediaan sarana dan prasarana untuk mengendalikan iklim kerja di setiap area kerja;

pengaturan siklus kerja sesuai dengan kondisi iklim kerja di setiap area kerja;

pembuatan peraturan Pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP dalam upaya mengelola iklim kerja; dan

penyediaan alat pelindung diri yang sesuai dengan kondisi iklim kerja di area kerja.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.7', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan bahaya radiasi sesuai prosedur yang ditetapkan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.7', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan bahaya radiasi dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasikan mengenai pengelolaan bahaya radiasi;

antisipasi dan pengenalan bahaya terkait radiasi yang mencakup radiasi alamiah dan buatan, serta radiasi pengion dan non-pengion pada setiap area kerja telah dilakukan;

pengukuran dan penilaian (evaluasi) belum dilakukan secara berkala yang terdokumentasikan;

pengukuran dan penilaian (evaluasi) belum dilakukan oleh Tenaga Teknis Pertambangan yang Berkompeten yang mengacu kepada ketentuan peraturan perundang-undangan;

pengukuran dan penilaian (evaluasi) belum menggunakan alat pemeriksaan yang terbukti telah dikalibrasi dan dipelihara sesuai prosedur; dan

pengendalian risiko terkait radiasi belum dilakukan mengacu kepada hierarki pengendalian.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.7', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan bahaya radiasi dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasikan mengenai pengelolaan bahaya radiasi;

antisipasi dan pengenalan bahaya terkait radiasi yang mencakup radiasi alamiah dan buatan, serta radiasi pengion dan non-pengion pada setiap area kerja telah dilakukan;

pengukuran dan penilaian (evaluasi) telah dilakukan secara berkala yang terdokumentasikan;

pengukuran dan penilaian (evaluasi) telah dilakukan oleh Tenaga Teknis Pertambangan yang Berkompeten yang mengacu kepada ketentuan peraturan perundang-undangan;

pengukuran dan penilaian (evaluasi) telah menggunakan alat pemeriksaan yang terbukti telah dikalibrasi dan dipelihara sesuai prosedur; dan

hasil pengukuran dan penilaian (evaluasi) belum ditindaklanjuti untuk pengendalian risiko terkait radiasi sesuai hirarki pengendalian.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.7', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan bahaya radiasi dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasikan mengenai pengelolaan bahaya radiasi;

antisipasi dan pengenalan bahaya terkait radiasi yang mencakup radiasi alamiah dan buatan, serta radiasi pengion dan non-pengion pada setiap area kerja telah dilakukan;

pengukuran dan penilaian (evaluasi) telah dilakukan secara berkala yang terdokumentasikan;

pengukuran dan penilaian (evaluasi) telah dilakukan oleh Tenaga Teknis Pertambangan yang Berkompeten yang mengacu kepada ketentuan peraturan perundang-undangan;

pengukuran dan penilaian (evaluasi) telah menggunakan alat pemeriksaan yang terbukti telah dikalibrasi dan dipelihara sesuai prosedur; dan

sebagian hasil pengukuran dan penilaian (evaluasi) telah ditindaklanjuti untuk pengendalian risiko terkait radiasi sesuai hirarki pengendalian.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.7', '4', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan bahaya radiasi dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasikan mengenai pengelolaan bahaya radiasi;

antisipasi dan pengenalan bahaya terkait radiasi yang mencakup radiasi alamiah dan buatan, serta radiasi pengion dan non-pengion pada setiap area kerja telah dilakukan;

pengukuran dan penilaian (evaluasi) telah dilakukan secara berkala yang terdokumentasikan;

pengukuran dan penilaian (evaluasi) telah dilakukan oleh Tenaga Teknis Pertambangan yang Berkompeten yang mengacu kepada ketentuan peraturan perundang-undangan;

pengukuran dan penilaian (evaluasi) telah menggunakan alat pemeriksaan yang terbukti telah dikalibrasi dan dipelihara sesuai prosedur; dan

seluruh hasil pengukuran dan penilaian (evaluasi) telah ditindaklanjuti untuk pengendalian risiko terkait radiasi sesuai hirarki pengendalian.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.8', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan faktor kimia sesuai prosedur yang ditetapkan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.8', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan faktor kimia dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasikan mengenai pengelolaan faktor kimia;

antisipasi dan pengenalan bahaya terkait penggunaan bahan kimia, baik sebagai bahan kimia itu sendiri, reaksi yang terjadi pada saat digunakan, maupun produk antara, akhir, dan sampingan yang dihasilkan pada setiap area kerja telah dilakukan;

pengukuran dan penilaian (evaluasi) belum dilakukan secara berkala yang terdokumentasikan;');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.8', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan faktor kimia dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasikan mengenai pengelolaan faktor kimia;

antisipasi dan pengenalan bahaya terkait penggunaan bahan kimia, baik sebagai bahan kimia itu sendiri, reaksi yang terjadi pada saat digunakan, maupun produk antara, akhir, dan sampingan yang dihasilkan pada setiap area kerja telah dilakukan;

pengukuran dan penilaian (evaluasi) telah dilakukan secara berkala yang terdokumentasikan;

pengukuran dan penilaian (evaluasi) telah dilakukan oleh Tenaga Teknis Pertambangan yang Berkompeten yang mengacu kepada ketentuan peraturan perundang-undangan;

pengukuran dan penilaian (evaluasi) telah menggunakan alat pemeriksaan yang terbukti telah dikalibrasi dan dipelihara sesuai prosedur; dan

hasil pengukuran dan penilaian (evaluasi) belum ditindaklanjuti untuk pengendalian risiko terkait faktor kimia sesuai hirarki pengendalian.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.8', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan faktor kimia dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasikan mengenai pengelolaan faktor kimia;

antisipasi dan pengenalan bahaya terkait penggunaan bahan kimia, baik sebagai bahan kimia itu sendiri, reaksi yang terjadi pada saat digunakan, maupun produk antara, akhir, dan sampingan yang dihasilkan pada setiap area kerja telah dilakukan;

pengukuran dan penilaian (evaluasi) telah dilakukan secara berkala yang terdokumentasikan;

pengukuran dan penilaian (evaluasi) telah dilakukan oleh Tenaga Teknis Pertambangan yang Berkompeten yang mengacu kepada ketentuan peraturan perundang-undangan;

pengukuran dan penilaian (evaluasi) telah menggunakan alat pemeriksaan yang terbukti telah dikalibrasi dan dipelihara sesuai prosedur; dan

sebagian hasil pengukuran dan penilaian (evaluasi) telah ditindaklanjuti untuk pengendalian risiko terkait faktor kimia sesuai hirarki pengendalian.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.8', '4', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan faktor kimia dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasikan mengenai pengelolaan faktor kimia;

antisipasi dan pengenalan bahaya terkait penggunaan bahan kimia, baik sebagai bahan kimia itu sendiri, reaksi yang terjadi pada saat digunakan, maupun produk antara, akhir, dan sampingan yang dihasilkan pada setiap area kerja telah dilakukan;

pengukuran dan penilaian (evaluasi) telah dilakukan secara berkala yang terdokumentasikan;

pengukuran dan penilaian (evaluasi) telah dilakukan oleh Tenaga Teknis Pertambangan yang Berkompeten yang mengacu kepada ketentuan peraturan perundang-undangan;

pengukuran dan penilaian (evaluasi) telah menggunakan alat pemeriksaan yang terbukti telah dikalibrasi dan dipelihara sesuai prosedur; dan

seluruh hasil pengukuran dan penilaian (evaluasi) telah ditindaklanjuti untuk pengendalian risiko terkait faktor kimia sesuai hirarki pengendalian');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.9', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan faktor biologi sesuai prosedur yang ditetapkan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.9', '1', 'Berikut adalah hasil konversi teks dari gambar terbaru yang Anda unggah:

4.2.9 Penilaian Penerapan “Pelaksanaan Pengelolaan Faktor Biologi”
0 Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan faktor biologi sesuai prosedur yang ditetapkan.

1 Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan faktor biologi dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasikan mengenai pengelolaan faktor biologi;

antisipasi dan pengenalan bahaya terkait faktor biologi, baik yang berasal dari mikro organisme maupun makro organisme pada setiap area kerja telah dilakukan;

pengukuran dan penilaian (evaluasi) belum dilakukan secara berkala yang terdokumentasikan;

pengukuran dan penilaian (evaluasi) belum dilakukan oleh Tenaga Teknis Pertambangan yang Berkompeten yang mengacu kepada ketentuan peraturan perundang-undangan;

pengukuran dan penilaian (evaluasi) belum menggunakan alat pemeriksaan yang terbukti telah dikalibrasi dan dipelihara sesuai prosedur; dan

pengendalian risiko terkait faktor biologi belum dilakukan mengacu kepada hierarki pengendalian.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.9', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan faktor biologi dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasikan mengenai pengelolaan faktor biologi;

antisipasi dan pengenalan bahaya terkait faktor biologi, baik yang berasal dari mikro organisme maupun makro organisme pada setiap area kerja telah dilakukan;

pengukuran dan penilaian (evaluasi) telah dilakukan secara berkala yang terdokumentasikan;

pengukuran dan penilaian (evaluasi) telah dilakukan oleh Tenaga Teknis Pertambangan yang Berkompeten yang mengacu kepada ketentuan peraturan perundang-undangan;

pengukuran dan penilaian (evaluasi) telah menggunakan alat pemeriksaan yang terbukti telah dikalibrasi dan dipelihara sesuai prosedur; dan

hasil pengukuran dan penilaian (evaluasi) belum ditindaklanjuti untuk pengendalian risiko terkait faktor biologi sesuai hirarki pengendalian.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.9', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan faktor biologi dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasikan mengenai pengelolaan faktor biologi;

antisipasi dan pengenalan bahaya terkait faktor biologi, baik yang berasal dari mikro organisme maupun makro organisme pada setiap area kerja telah dilakukan;

pengukuran dan penilaian (evaluasi) telah dilakukan secara berkala yang terdokumentasikan;

pengukuran dan penilaian (evaluasi) telah dilakukan oleh Tenaga Teknis Pertambangan yang Berkompeten yang mengacu kepada ketentuan peraturan perundang-undangan;

pengukuran dan penilaian (evaluasi) telah menggunakan alat pemeriksaan yang terbukti telah dikalibrasi dan dipelihara sesuai prosedur; dan

sebagian hasil pengukuran dan penilaian (evaluasi) telah ditindaklanjuti untuk pengendalian risiko terkait faktor biologi sesuai hirarki pengendalian.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.9', '4', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan faktor biologi dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasikan mengenai pengelolaan faktor biologi;

antisipasi dan pengenalan bahaya terkait faktor biologi, baik yang berasal dari mikro organisme maupun makro organisme pada setiap area kerja telah dilakukan;

pengukuran dan penilaian (evaluasi) telah dilakukan secara berkala yang terdokumentasikan;

pengukuran dan penilaian (evaluasi) telah dilakukan oleh Tenaga Teknis Pertambangan yang Berkompeten yang mengacu kepada ketentuan peraturan perundang-undangan;

pengukuran dan penilaian (evaluasi) telah menggunakan alat pemeriksaan yang terbukti telah dikalibrasi dan dipelihara sesuai prosedur; dan

seluruh hasil pengukuran dan penilaian (evaluasi) telah ditindaklanjuti untuk pengendalian risiko terkait faktor biologi sesuai hirarki pengendalian.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.10', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan kebersihan lingkungan kerja sesuai prosedur yang ditetapkan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.10', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan kebersihan lingkungan kerja dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasikan mengenai pelaksanaan kebersihan lingkungan kerja;

antisipasi dan pengenalan bahaya akibat pengelolaan kebersihan lingkungan kerja yang kurang optimal pada setiap area kerja telah dilakukan;

pemantauan dan evaluasi kebersihan lingkungan kerja belum dilakukan secara berkala yang terdokumentasikan; dan

hasil evaluasi kebersihan lingkungan kerja belum ditindaklanjuti dengan melakukan pengendalian.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.10', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan kebersihan lingkungan kerja dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasikan mengenai pelaksanaan kebersihan lingkungan kerja;

antisipasi dan pengenalan bahaya akibat pengelolaan kebersihan lingkungan kerja yang kurang optimal pada setiap area kerja telah dilakukan;

pemantauan dan evaluasi kebersihan lingkungan kerja telah dilakukan secara berkala yang terdokumentasikan; dan

hasil evaluasi kebersihan lingkungan kerja belum ditindaklanjuti dengan melakukan pengendalian.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.10', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan kebersihan lingkungan kerja dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasikan mengenai pelaksanaan kebersihan lingkungan kerja;

antisipasi dan pengenalan bahaya akibat pengelolaan kebersihan lingkungan kerja yang kurang optimal pada setiap area kerja telah dilakukan;

pemantauan dan evaluasi kebersihan lingkungan kerja telah dilakukan secara berkala yang terdokumentasikan; dan

sebagian hasil evaluasi kebersihan lingkungan kerja telah ditindaklanjuti dengan melakukan pengendalian.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.2.10', '4', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan kebersihan lingkungan kerja dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasikan mengenai pelaksanaan kebersihan lingkungan kerja;

antisipasi dan pengenalan bahaya akibat pengelolaan kebersihan lingkungan kerja yang kurang optimal pada setiap area kerja telah dilakukan;

pemantauan dan evaluasi kebersihan lingkungan kerja telah dilakukan secara berkala yang terdokumentasikan; dan

seluruh hasil evaluasi kebersihan lingkungan kerja telah ditindaklanjuti dengan melakukan pengendalian.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.3.1', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melaksanakan pemeriksaan kesehatan awal, pemeriksaan kesehatan berkala, pemeriksaan kesehatan khusus, dan pemeriksaan kesehatan akhir untuk pekerja sesuai peraturan perundang-undangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.3.1', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melaksanakan pemeriksaan kesehatan dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan prosedur pemeriksaan kesehatan;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP baru melaksanakan sebagian dari empat jenis pemeriksaan kesehatan (pemeriksaan kesehatan awal, pemeriksaan kesehatan berkala, pemeriksaan kesehatan khusus, dan pemeriksaan kesehatan akhir) untuk Pekerja sesuai peraturan perundang-undangan; dan

pemeriksaan kesehatan belum mengacu kepada pedoman pemeriksaan dan penilaian kelayakan kesehatan kerja yang disusun oleh dokter perusahaan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.3.1', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melaksanakan pemeriksaan kesehatan dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan prosedur pemeriksaan kesehatan;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melaksanakan seluruh jenis pemeriksaan kesehatan (pemeriksaan kesehatan awal, pemeriksaan kesehatan berkala, pemeriksaan kesehatan khusus, dan pemeriksaan kesehatan akhir) untuk Pekerja sesuai peraturan perundang-undangan;

pemeriksaan kesehatan telah mengacu kepada pedoman pemeriksaan dan penilaian kelayakan kesehatan kerja yang disusun oleh dokter perusahaan; dan

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP belum menindaklanjuti seluruh hasil pemeriksaan kesehatan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.3.1', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melaksanakan pemeriksaan kesehatan dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan prosedur pemeriksaan kesehatan;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melaksanakan seluruh jenis pemeriksaan kesehatan (pemeriksaan kesehatan awal, pemeriksaan kesehatan berkala, pemeriksaan kesehatan khusus, dan pemeriksaan kesehatan akhir) untuk Pekerja sesuai peraturan perundang-undangan;

pemeriksaan kesehatan telah mengacu kepada pedoman pemeriksaan dan penilaian kelayakan kesehatan kerja yang disusun oleh dokter perusahaan;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menindaklanjuti seluruh hasil pemeriksaan kesehatan; dan

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP belum membuat dan mendokumentasikan serta mengevaluasi catatan kesehatan Pekerja.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.3.1', '4', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melaksanakan pemeriksaan kesehatan dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan prosedur pemeriksaan kesehatan;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melaksanakan seluruh pemeriksaan kesehatan (pemeriksaan kesehatan awal, pemeriksaan kesehatan berkala, pemeriksaan kesehatan khusus, dan pemeriksaan kesehatan akhir) untuk Pekerja sesuai peraturan perundang-undangan;

pemeriksaan kesehatan telah mengacu kepada pedoman pemeriksaan dan penilaian kelayakan kesehatan kerja yang disusun oleh dokter perusahaan;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menindaklanjuti seluruh hasil pemeriksaan kesehatan; dan

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah membuat dan mendokumentasikan serta mengevaluasi catatan kesehatan Pekerja.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.3.2', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyediakan tenaga kesehatan kerja Pertambangan serta sarana dan prasarana pelayanan sesuai dengan ketentuan peraturan perundang-undangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.3.2', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pelayanan kesehatan kerja, dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan prosedur pelayanan kesehatan kerja;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyediakan tenaga kesehatan kerja Pertambangan, namun belum sesuai dengan ketentuan peraturan perundang-undangan;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyediakan sarana dan prasarana pelayanan kesehatan kerja, namun belum sesuai dengan ketentuan peraturan perundang-undangan; dan

kualifikasi pelayanan kesehatan kerja belum ditetapkan berdasarkan tingkat keterisoliran lokasi tambang dan ketentuan peraturan perundang-undangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.3.2', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pelayanan kesehatan kerja, dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan prosedur pelayanan kesehatan kerja;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyediakan Tenaga Kesehatan Kerja Pertambangan yang sesuai dengan ketentuan peraturan perundang-undangan;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyediakan sarana dan prasarana pelayanan kesehatan kerja yang sesuai dengan ketentuan peraturan perundang-undangan; dan

kualifikasi pelayanan kesehatan kerja telah ditetapkan berdasarkan tingkat keterisoliran lokasi tambang dan ketentuan peraturan perundang-undangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.3.3', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyediakan petugas, fasilitas, dan peralatan serta mengadakan pelatihan untuk pertolongan pertama pada kecelakaan sesuai dengan ketentuan peraturan perundangundangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.3.3', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pelayanan kesehatan kerja, dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan prosedur pelayanan kesehatan kerja;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyediakan tenaga kesehatan kerja Pertambangan, namun belum sesuai dengan ketentuan peraturan perundang-undangan;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyediakan sarana dan prasarana pelayanan kesehatan kerja, namun belum sesuai dengan ketentuan peraturan perundang-undangan; dan

kualifikasi pelayanan kesehatan kerja belum ditetapkan berdasarkan tingkat keterisoliran lokasi tambang dan ketentuan peraturan perundang-undangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.3.3', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pelayanan kesehatan kerja, dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan prosedur pelayanan kesehatan kerja;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyediakan Tenaga Kesehatan Kerja Pertambangan yang sesuai dengan ketentuan peraturan perundang-undangan;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyediakan sarana dan prasarana pelayanan kesehatan kerja yang sesuai dengan ketentuan peraturan perundang-undangan; dan

kualifikasi pelayanan kesehatan kerja telah ditetapkan berdasarkan tingkat keterisoliran lokasi tambang dan ketentuan peraturan perundang-undangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.3.4', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan kelelahan kerja Pekerja.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.3.4', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pelayanan kesehatan kerja, dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan prosedur pelayanan kesehatan kerja;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyediakan tenaga kesehatan kerja Pertambangan, namun belum sesuai dengan ketentuan peraturan perundang-undangan;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyediakan sarana dan prasarana pelayanan kesehatan kerja, namun belum sesuai dengan ketentuan peraturan perundang-undangan; dan

kualifikasi pelayanan kesehatan kerja belum ditetapkan berdasarkan tingkat keterisoliran lokasi tambang dan ketentuan peraturan perundang-undangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.3.4', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan kelelahan kerja Pekerja, dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan prosedur pengelolaan kelelahan kerja;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan identifikasi, evaluasi, dan pengendalian faktor yang dapat menimbulkan kelelahan Pekerja, namun hasilnya belum memadai; dan

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memberikan pelatihan dan/atau sosialisasi kepada semua Pekerja tentang pengetahuan pengelolaan dan pencegahan kelelahan, namun materi pelatihan dan/atau sosialisasi baru bersifat umum..');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.3.4', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan kelelahan kerja Pekerja, dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan prosedur pengelolaan kelelahan kerja;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan identifikasi, evaluasi, dan pengendalian faktor yang dapat menimbulkan kelelahan Pekerja, dan hasilnya telah memadai; dan

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memberikan pelatihan dan/atau sosialisasi kepada semua Pekerja tentang pengetahuan pengelolaan dan pencegahan kelelahan secara rinci.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.3.5', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan Pekerja yang bekerja pada tempat yang memiliki risiko kesehatan tinggi.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.3.5', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan Pekerja yang bekerja pada tempat yang memiliki risiko kesehatan tinggi, dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan prosedur pengelolaan Pekerja yang bekerja pada tempat yang memiliki risiko kesehatan tinggi;

risiko belum dikendalikan secara memadai; dan

Pekerja terkait belum memahami cara kerja aman dan konsekuensi bekerja di area tersebut.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.3.5', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan Pekerja yang bekerja pada tempat yang memiliki risiko kesehatan tinggi, dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan prosedur pengelolaan Pekerja yang bekerja pada tempat yang memiliki risiko kesehatan tinggi;

risiko telah dikendalikan secara memadai; dan

Pekerja terkait telah memahami cara kerja aman dan konsekuensi bekerja di area tersebut.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.3.6', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memelihara dan menjaga rekaman data kesehatan kerja Pertambangan sesuai dengan ketentuan peraturan perundang-undangan, serta menganalisis dan mengevaluasi rekaman data kesehatan kerja Pertambangan sebagai bahan untuk perbaikan kinerja kesehatan kerja.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.3.6', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan rekaman data kesehatan kerja, dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan prosedur pengelolaan rekaman data kesehatan kerja;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memelihara dan menjaga rekaman data kesehatan kerja Pertambangan, namun belum sesuai dengan ketentuan peraturan perundang-undangan; dan

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP belum menganalisis dan mengevaluasi rekaman data kesehatan kerja Pertambangan sebagai bahan untuk perbaikan kinerja kesehatan kerja.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.3.6', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan rekaman data kesehatan kerja, dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan prosedur pengelolaan rekaman data kesehatan kerja;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memelihara dan menjaga rekaman data kesehatan kerja Pertambangan sesuai dengan ketentuan peraturan perundang-undangan; dan

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP belum menganalisis dan mengevaluasi rekaman data kesehatan kerja Pertambangan sebagai bahan untuk perbaikan kinerja kesehatan kerja.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.3.6', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan rekaman data kesehatan kerja, dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan prosedur pengelolaan rekaman data kesehatan kerja;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memelihara dan menjaga rekaman data kesehatan kerja Pertambangan sesuai dengan ketentuan peraturan perundang-undangan;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menganalisis dan mengevaluasi rekaman data kesehatan kerja Pertambangan sebagai bahan untuk perbaikan kinerja kesehatan kerja; dan

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP belum membuat statistik kinerja kesehatan kerja dengan menggunakan 2 (dua) indikator yaitu indikator proses (leading indicator) dan indikator hasil akhir (lagging indicator).');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.3.6', '4', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan rekaman data kesehatan kerja, dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan prosedur pengelolaan rekaman data kesehatan kerja;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memelihara dan menjaga rekaman data kesehatan kerja Pertambangan sesuai dengan ketentuan peraturan perundang-undangan;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menganalisis dan mengevaluasi rekaman data kesehatan kerja Pertambangan sebagai bahan untuk perbaikan kinerja kesehatan kerja; dan

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah membuat statistik kinerja kesehatan kerja dengan menggunakan 2 (dua) indikator yaitu indikator proses (leading indicator) dan indikator hasil akhir (lagging indicator).');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.3.7', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyediakan fasilitas untuk menunjang tercapainya higienitas, serta melakukan pengelolaan sanitasi di area kerja');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.3.7', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan higiene dan sanitasi, dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan prosedur pengelolaan higiene dan sanitasi;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyediakan fasilitas untuk menunjang tercapainya higienitas namun belum memadai; dan

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan sanitasi di area kerja namun belum memadai.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.3.7', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan higiene dan sanitasi, dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan prosedur pengelolaan higiene dan sanitasi;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyediakan fasilitas untuk menunjang tercapainya higienitas sesuai dengan ketentuan peraturan perundang-undangan; dan

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan sanitasi di area kerja paling sedikit meliputi pengelolaan tempat sampah, toilet dan wastafel, kebersihan lantai dan bangunan, dan ruang ganti pakaian dan kamar mandi sesuai dengan ketentuan peraturan perundang-undangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.3.8', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan ergonomi dengan mengelola kesesuaian antara pekerjaan, lingkungan kerja, peralatan, dan Pekerja.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.3.8', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan ergonomi dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan prosedur pengelolaan ergonomi;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP belum melakukan identifikasi dan penilaian risiko ergonomi, serta pengendalian berdasarkan hasil ergonomic risk assessment; dan

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP belum menyediakan prosedur kerja, sarana, prasarana, instalasi, dan peralatan yang sesuai dengan kemampuan, kondisi, dan postur Pekerja.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.3.8', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan ergonomi dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan prosedur pengelolaan ergonomi;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan identifikasi dan penilaian risiko ergonomi, serta pengendalian berdasarkan hasil ergonomic risk assessment, namun hasil pengendalian risiko ergonomi belum memadai; dan

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyediakan sebagian prosedur kerja, sarana, prasarana, instalasi, dan peralatan yang sesuai dengan kemampuan, kondisi, dan postur Pekerja.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.3.8', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan ergonomi dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan prosedur pengelolaan ergonomi;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan identifikasi dan penilaian risiko ergonomi, serta pengendalian berdasarkan hasil ergonomic risk assessment, dan hasil pengendalian risiko ergonomi telah memadai; dan

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyediakan seluruh prosedur kerja, sarana, prasarana, instalasi, dan peralatan yang sesuai dengan kemampuan, kondisi, dan postur Pekerja.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.3.9', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan makanan, minuman, dan gizi Pekerja');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.3.9', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan makanan, minuman, dan gizi Pekerja, dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan prosedur pengelolaan makanan, minuman, dan gizi Pekerja, dan

penyediaan makanan dan minuman belum sepenuhnya memenuhi syarat keamanan, kecukupan, dan higienitas sesuai dengan ketentuan yang berlaku serta mempertimbangkan aspek keseimbangan gizi Pekerja.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.3.9', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan makanan, minuman, dan gizi Pekerja, dengan kondisi:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan prosedur pengelolaan makanan, minuman, dan gizi Pekerja, dan

penyediaan makanan dan minuman telah memenuhi syarat keamanan, kecukupan, dan higienitas sesuai dengan ketentuan yang berlaku serta mempertimbangkan aspek keseimbangan gizi Pekerja.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.3.10', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan diagnosis Penyakit Akibat Kerja oleh dokter perusahaan melalui serangkaian tahapan pemeriksaan klinis, kondisi Pekerja, serta lingkungan kerja.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.3.10', '1', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan prosedur diagnosis dan pemeriksaan penyakit akibat kerja;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP belum melakukan diagnosis Penyakit Akibat Kerja oleh dokter perusahaan melalui serangkaian tahapan pemeriksaan klinis, kondisi Pekerja, serta lingkungan kerja; dan

KTT atau PTL belum melaporkan kepada KaIT atau Kepala Dinas atas nama KaIT sesuai dengan kewenangannya.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.3.10', '2', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan prosedur diagnosis dan pemeriksaan penyakit akibat kerja;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan diagnosis Penyakit Akibat Kerja oleh dokter perusahaan melalui serangkaian tahapan pemeriksaan klinis, kondisi Pekerja, serta lingkungan kerja; dan

KTT atau PTL telah melaporkan kepada KaIT atau Kepala Dinas atas nama KaIT sesuai dengan kewenangannya, namun belum menggunakan formulir yang ditentukan dan belum pada batas waktu pelaporan yang ditetapkan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.3.10', '3', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan prosedur diagnosis dan pemeriksaan penyakit akibat kerja;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan diagnosis Penyakit Akibat Kerja oleh dokter perusahaan melalui serangkaian tahapan pemeriksaan klinis, kondisi Pekerja, serta lingkungan kerja;

KTT atau PTL telah melaporkan kepada KaIT atau Kepala Dinas atas nama KaIT sesuai dengan kewenangannya dengan menggunakan formulir yang ditentukan dan belum pada batas waktu pelaporan yang ditetapkan; dan

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP belum melakukan upaya kuratif dan rehabilitasi terhadap Pekerja yang didiagnosis menderita Penyakit Akibat Kerja.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.3.10', '4', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan prosedur diagnosis dan pemeriksaan penyakit akibat kerja;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan diagnosis Penyakit Akibat Kerja oleh dokter perusahaan melalui serangkaian tahapan pemeriksaan klinis, kondisi Pekerja, serta lingkungan kerja;

KTT atau PTL telah melaporkan kepada KaIT/Kepala Dinas atas nama KaIT sesuai dengan kewenangannya dengan menggunakan formulir yang ditentukan dan belum pada batas waktu pelaporan yang ditetapkan; dan

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan upaya kuratif dan rehabilitasi terhadap Pekerja yang didiagnosis menderita Penyakit Akibat Kerja.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.4.1', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah membuat sistem dan melaksanakan pemeliharaan/ perawatan sarana, prasarana, instalasi, dan peralatan Pertambangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.4.1', '1', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan prosedur pengelolaan sistem dan pelaksanaan pemeliharaan/ perawatan sarana, prasarana, instalasi, dan peralatan;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah membuat daftar sarana, prasarana, instalasi, dan peralatan Pertambangan; dan

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP belum membuat program, jadwal, dan prosedur pemeliharaan atau perawatan berdasarkan hasil identifikasi jenis dan karakteristik sarana, prasarana, instalasi, dan peralatan Pertambangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.4.1', '2', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan prosedur pengelolaan sistem dan pelaksanaan pemeliharaan/ perawatan sarana, prasarana, instalasi, dan peralatan;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah membuat daftar sarana, prasarana, instalasi, dan peralatan Pertambangan;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah membuat program, jadwal, dan prosedur pemeliharaan atau perawatan berdasarkan hasil identifikasi jenis dan karakteristik sarana, prasarana, instalasi, dan peralatan Pertambangan, namun pelaksanaannya belum sesuai program, jadwal, dan prosedur yang ditetapkan; dan

pelaksanaan pemeliharaan atau perawatan berdasarkan hasil identifikasi jenis dan karakteristik sarana, prasarana, instalasi, dan peralatan Pertambangan belum dilakukan oleh Tenaga Teknis Pertambangan yang Berkompeten.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.4.1', '3', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan prosedur pengelolaan sistem dan pelaksanaan pemeliharaan/ perawatan sarana, prasarana, instalasi, dan peralatan;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah membuat daftar sarana, prasarana, instalasi, dan peralatan Pertambangan;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah membuat program, jadwal, dan prosedur pemeliharaan atau perawatan berdasarkan hasil identifikasi jenis dan karakteristik sarana, prasarana, instalasi, dan peralatan Pertambangan, namun pelaksanaannya telah sesuai program, jadwal, dan prosedur yang ditetapkan; dan

pelaksanaan pemeliharaan atau perawatan berdasarkan hasil identifikasi jenis dan karakteristik sarana, prasarana, instalasi, dan peralatan Pertambangan belum dilakukan oleh Tenaga Teknis Pertambangan yang Berkompeten.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.4.1', '4', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan prosedur pengelolaan sistem dan pelaksanaan pemeliharaan/ perawatan sarana, prasarana, instalasi, dan peralatan;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah membuat daftar sarana, prasarana, instalasi, dan peralatan Pertambangan;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah membuat program, jadwal, dan prosedur pemeliharaan atau perawatan berdasarkan hasil identifikasi jenis dan karakteristik sarana, prasarana, instalasi, dan peralatan Pertambangan, dan pelaksanaannya telah sesuai program, jadwal, dan prosedur yang ditetapkan; dan

pelaksanaan pemeliharaan atau perawatan berdasarkan hasil identifikasi jenis dan karakteristik sarana, prasarana, instalasi, dan peralatan Pertambangan telah dilakukan oleh Tenaga Teknis Pertambangan yang Berkompeten.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.4.2', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menerapkan pengamanan instalasi.');


-- INSERT REMAINING KRITERIA PENILAIAN
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.4.3', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah membuat sistem dan melaksanakan pemeliharaan/perawatan sarana, prasarana, instalasi, dan peralatan Pertambangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.4.3', '1', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan prosedur pengelolaan sistem dan pelaksanaan pemeliharaan/perawatan sarana, prasarana, instalasi, dan peralatan;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah membuat daftar sarana, prasarana, instalasi, dan peralatan Pertambangan; dan

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP belum membuat program, jadwal, dan prosedur pemeliharaan atau perawatan berdasarkan hasil identifikasi jenis dan karakteristik sarana, prasarana, instalasi, dan peralatan Pertambangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.4.3', '2', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan prosedur pengelolaan sistem dan pelaksanaan pemeliharaan/perawatan sarana, prasarana, instalasi, dan peralatan;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah membuat daftar sarana, prasarana, instalasi, dan peralatan Pertambangan;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah membuat program, jadwal, dan prosedur pemeliharaan atau perawatan berdasarkan hasil identifikasi jenis dan karakteristik sarana, prasarana, instalasi, dan peralatan Pertambangan, namun pelaksanaannya belum sesuai program, jadwal, dan prosedur yang ditetapkan; dan

pelaksanaan pemeliharaan atau perawatan berdasarkan hasil identifikasi jenis dan karakteristik sarana, prasarana, instalasi, dan peralatan Pertambangan belum dilakukan oleh Tenaga Teknis Pertambangan yang Berkompeten.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.4.3', '3', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan prosedur pengelolaan sistem dan pelaksanaan pemeliharaan/perawatan sarana, prasarana, instalasi, dan peralatan;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah membuat daftar sarana, prasarana, instalasi, dan peralatan Pertambangan;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah membuat program, jadwal, dan prosedur pemeliharaan atau perawatan berdasarkan hasil identifikasi jenis dan karakteristik sarana, prasarana, instalasi, dan peralatan Pertambangan, dan pelaksanaannya telah sesuai program, jadwal, dan prosedur yang ditetapkan; dan

pelaksanaan pemeliharaan atau perawatan berdasarkan hasil identifikasi jenis dan karakteristik sarana, prasarana, instalasi, dan peralatan Pertambangan belum dilakukan oleh Tenaga Teknis Pertambangan yang Berkompeten.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.4.3', '4', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan prosedur pengelolaan sistem dan pelaksanaan pemeliharaan/perawatan sarana, prasarana, instalasi, dan peralatan;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah membuat daftar sarana, prasarana, instalasi, dan peralatan Pertambangan;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah membuat program, jadwal, dan prosedur pemeliharaan atau perawatan berdasarkan hasil identifikasi jenis dan karakteristik sarana, prasarana, instalasi, dan peralatan Pertambangan, dan pelaksanaannya telah sesuai program, jadwal, dan prosedur yang ditetapkan; dan

pelaksanaan pemeliharaan atau perawatan berdasarkan hasil identifikasi jenis dan karakteristik sarana, prasarana, instalasi, dan peralatan Pertambangan telah dilakukan oleh Tenaga Teknis Pertambangan yang Berkompeten.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.4.4', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menunjuk Tenaga Teknis Pertambangan yang Berkompeten di bidang Keselamatan Operasi untuk menyusun dan menetapkan prosedur, membuat program dan jadwal, serta melaksanakan pengujian kelayakan, pengamanan, dan pemeliharaan terhadap sarana, prasarana, instalasi dan peralatan Pertambangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.4.4', '1', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menunjuk Tenaga Teknis Pertambangan yang Berkompeten di bidang Keselamatan Operasi untuk menyusun dan menetapkan prosedur, membuat program dan jadwal, serta melaksanakan pengujian kelayakan, pengamanan, dan pemeliharaan terhadap sarana, prasarana, instalasi dan peralatan Pertambangan;

jumlah Tenaga Teknis Pertambangan yang Berkompeten belum memadai;

bukti kerja Tenaga Teknis Pertambangan yang Berkompeten untuk seluruh kegiatan pengelolaan Keselamatan Operasi tersebut belum ditemukan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.4.4', '2', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menunjuk Tenaga Teknis Pertambangan yang Berkompeten di bidang Keselamatan Operasi untuk menyusun dan menetapkan prosedur, membuat program dan jadwal, serta melaksanakan pengujian kelayakan, pengamanan, dan pemeliharaan terhadap sarana, prasarana, instalasi dan peralatan Pertambangan;

jumlah Tenaga Teknis Pertambangan yang Berkompeten telah memadai;

bukti kerja Tenaga Teknis Pertambangan yang Berkompeten belum memadai untuk seluruh kegiatan pengelolaan Keselamatan Operasi; dan

pelaksanaan program oleh Tenaga Teknis Pertambangan yang Berkompeten belum sesuai jadwal, dan prosedur yang ditetapkan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.4.4', '3', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menunjuk Tenaga Teknis Pertambangan yang Berkompeten di bidang Keselamatan Operasi untuk menyusun dan menetapkan prosedur, membuat program dan jadwal, serta melaksanakan pengujian kelayakan, pengamanan, dan pemeliharaan terhadap sarana, prasarana, instalasi dan peralatan Pertambangan;

jumlah Tenaga Teknis Pertambangan yang Berkompeten telah memadai;

bukti kerja Tenaga Teknis Pertambangan yang Berkompeten telah memadai untuk seluruh kegiatan pengelolaan Keselamatan Operasi; dan

pelaksanaan program oleh Tenaga Teknis Pertambangan yang Berkompeten belum sesuai jadwal, dan prosedur yang ditetapkan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.4.4', '4', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menunjuk Tenaga Teknis Pertambangan yang Berkompeten di bidang Keselamatan Operasi untuk menyusun dan menetapkan prosedur, membuat program dan jadwal, serta melaksanakan pengujian kelayakan, pengamanan, dan pemeliharaan terhadap sarana, prasarana, instalasi dan peralatan Pertambangan;

jumlah Tenaga Teknis Pertambangan yang Berkompeten telah memadai;

bukti kerja Tenaga Teknis Pertambangan yang Berkompeten telah memadai untuk seluruh kegiatan pengelolaan Keselamatan Operasi; dan

pelaksanaan program oleh Tenaga Teknis Pertambangan yang Berkompeten telah sesuai jadwal, dan prosedur yang ditetapkan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.4.5', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah membuat kajian teknis dilakukan pada saat awal kegiatan atau sebelum dimulainya kegiatan Pertambangan dan apabila terjadi perubahan atau modifikasi terhadap proses, sarana, prasarana, instalasi, dan peralatan Pertambangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.4.5', '1', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan prosedur evaluasi laporan hasil kajian teknis Pertambangan;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah membuat kajian teknis dilakukan pada saat awal kegiatan atau sebelum dimulainya kegiatan Pertambangan; dan

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP belum membuat kajian teknis apabila terjadi perubahan atau modifikasi terhadap proses, sarana, prasarana, instalasi, dan peralatan Pertambangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.4.5', '2', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan prosedur evaluasi laporan hasil kajian teknis Pertambangan;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah membuat kajian teknis dilakukan pada saat awal kegiatan atau sebelum dimulainya kegiatan Pertambangan;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah membuat kajian teknis apabila terjadi perubahan atau modifikasi terhadap proses, sarana, prasarana, instalasi, dan peralatan Pertambangan; dan

hasil kajian teknis belum memadai dan belum disampaikan kepada KaIT/Kepala Dinas atas nama KaIT.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.4.5', '3', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan prosedur evaluasi laporan hasil kajian teknis Pertambangan;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah membuat kajian teknis dilakukan pada saat awal kegiatan atau sebelum dimulainya kegiatan Pertambangan;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah membuat kajian teknis apabila terjadi perubahan atau modifikasi terhadap proses, sarana, prasarana, instalasi, dan peralatan Pertambangan; dan

hasil kajian teknis telah disampaikan kepada KaIT/Kepala Dinas atas nama KaIT, namun belum memadai.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.4.5', '4', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun dan menetapkan prosedur evaluasi laporan hasil kajian teknis Pertambangan;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah membuat kajian teknis dilakukan pada saat awal kegiatan atau sebelum dimulainya kegiatan Pertambangan;

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah membuat kajian teknis apabila terjadi perubahan atau modifikasi terhadap proses, sarana, prasarana, instalasi, dan peralatan Pertambangan; dan

hasil kajian teknis telah memadai dan telah disampaikan kepada KaIT/Kepala Dinas atas nama KaIT.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.5.1', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan Gudang Bahan Peledak Sesuai Peraturan perundang-undangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.5.1', '1', 'Terdapat bukti yang menunjukkan telah memiliki perizinan gudang bahan peledak yang masih berlaku, namun penjagaan, penyediaan fasilitas keselamatan/keamanan, dan pemeriksaan penangkal petir belum memadai.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.5.1', '2', 'Terdapat bukti yang menunjukkan telah memiliki perizinan yang masih berlaku, dilakukan penjagaan 24 jam, disediakan fasilitas keselamatan/keamanan, serta dilakukan pemeriksaan penangkal petir paling sedikit sekali dalam 6 bulan atau setelah petir hebat.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.5.2', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP telah melakukan penyimpanan bahan peledak sesuai peraturan perundang-undangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.5.2', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP memiliki prosedur penyimpanan bahan peledak, namun prosedur tersebut belum dijalankan dengan baik yang ditunjukkan dengan belum sesuainya penyimpanan dengan persetujuan dan perizinan, belum memadainya pengelolaan administrasi, belum ditunjuknya petugas administrasi dan petugas gudang, dan belum dilakukan pemeriksaan isi gudang paling sedikit satu kali dalam seminggu.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.5.2', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP memiliki prosedur penyimpanan bahan peledak, dan penyimpanan telah sesuai dengan persetujuan dan perizinan, pengelolaan administrasi telah memadai, pemeriksaan isi gudang telah dilakukan paling sedikit satu kali dalam seminggu, dan petugas administrasi dan petugas gudang telah ditunjuk, namun belum memiliki Kartu Pekerja Peledakan yang sesuai.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.5.2', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP memiliki prosedur penyimpanan bahan peledak, dan penyimpanan telah sesuai dengan persetujuan dan perizinan, pengelolaan administrasi telah memadai, pemeriksaan isi gudang telah dilakukan paling sedikit satu kali dalam seminggu, dan petugas administrasi dan petugas gudang telah ditunjuk dan memiliki Kartu Pekerja Peledakan yang sesuai, namun belum melaporkan jumlah penggunaan dan persediaan bahan peledak secara berkala kepada KaIT.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.5.2', '4', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP memiliki prosedur penyimpanan bahan peledak, dan penyimpanan telah sesuai dengan persetujuan dan perizinan, pengelolaan administrasi telah memadai, pemeriksaan isi gudang telah dilakukan paling sedikit satu kali dalam seminggu, dan petugas administrasi dan petugas gudang telah ditunjuk dan memiliki Kartu Pekerja Peledakan yang sesuai, serta telah melaporkan jumlah penggunaan dan persediaan bahan peledak secara berkala kepada KaIT.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.5.3', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP telah melakukan pengangkutan bahan peledak sesuai peraturan perundang-undangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.5.3', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP memiliki prosedur pengangkutan bahan peledak, namun belum dilakukan penetapan, pengamanan, kelayakan peralatan dan kendaraan untuk mengangkut, memindahkan, dan mengirim bahan peledak maupun yang berhubungan dengan pekerjaan peledakan, serta kompetensi Pekerja yang yang menangani pengangkutan, pemindahan, dan pengiriman bahan peledak belum sesuai dengan ketentuan peraturan perundang-undangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.5.3', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP memiliki prosedur pengangkutan bahan peledak, telah melakukan penetapan, pengamanan, kelayakan peralatan dan kendaraan untuk mengangkut, memindahkan, dan mengirim bahan peledak maupun yang berhubungan dengan pekerjaan peledakan, namun hasilnya belum memadai, dan kompetensi Pekerja yang menangani pengangkutan, pemindahan, dan pengiriman bahan peledak belum seluruhnya sesuai dengan peraturan perundang-undangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.5.3', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP memiliki prosedur pengangkutan bahan peledak, telah melakukan penetapan, pengamanan, kelayakan peralatan dan kendaraan untuk mengangkut, memindahkan, dan mengirim bahan peledak maupun yang berhubungan dengan pekerjaan peledakan, hasilnya telah memadai, namun kompetensi Pekerja yang menangani pengangkutan, pemindahan, dan pengiriman bahan peledak yang seluruhnya sesuai dengan peraturan perundang-undangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.5.3', '4', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP memiliki prosedur pengangkutan bahan peledak, telah melakukan penetapan, pengamanan, kelayakan peralatan dan kendaraan untuk mengangkut, memindahkan, dan mengirim bahan peledak maupun yang berhubungan dengan pekerjaan peledakan, hasilnya telah memadai dan telah memiliki kompetensi Pekerja yang menangani pengangkutan, pemindahan, dan pengiriman bahan peledak yang seluruhnya sesuai dengan peraturan perundang-undangan. (Sesuai instruksi Anda, bagian ini diubah angkanya menjadi 4 dari teks aslinya di poin 2 gambar terakhir).');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.5.4', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian atau IUJP telah melaksanakan pekerjaan peledakan sesuai peraturan perundang-undangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.5.4', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP memiliki prosedur pekerjaan peledakan yang mencakup penanganan dan pengamanan peledakan mangkir, namun peralatan dan bahan yang digunakan belum sesuai, penyimpanan, pemeriksaan dan pemeliharaan peralatan belum memadai, dokumentasi hasil pemeriksaan peralatan peledakan belum memadai, dan Pekerja peledakan belum memiliki Kartu Izin Meledakkan/ Kartu Pekerja Peledakan yang sesuai');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.5.4', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP memiliki prosedur pekerjaan peledakan yang mencakup penanganan dan pengamanan peledakan mangkir, telah menggunakan peralatan dan bahan yang digunakan yang sesuai, telah menyimpan, memeriksa dan memelihara, namun belum mendokumentasikan pemeliharaan peralatan secara memadai, dan Pekerja peledakan belum memiliki Kartu Izin Meledakkan/ Kartu Pekerja Peledakan yang sesuai.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.5.4', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP memiliki prosedur pekerjaan peledakan yang mencakup penanganan dan pengamanan peledakan mangkir, telah menggunakan peralatan dan bahan yang digunakan yang sesuai, telah menyimpan, memeriksa dan memelihara, serta mendokumentasikan pemeliharaan peralatan secara memadai, namun Pekerja peledakan belum memiliki Kartu Izin Meledakkan/ Kartu Pekerja Peledakan yang sesuai.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.5.4', '4', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP memiliki prosedur pekerjaan peledakan yang mencakup penanganan dan pengamanan peledakan mangkir, telah menggunakan peralatan dan bahan yang digunakan yang sesuai, telah menyimpan, memeriksa dan memelihara, serta mendokumentasikan pemeliharaan peralatan secara memadai, serta Pekerja peledakan telah memiliki Kartu Izin Meledakkan/ Kartu Pekerja Peledakan yang sesuai.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.6.1', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasi dengan mempertimbangkan aspek Keselamatan Pertambangan pada tahap perancangan dan rekayasa terhadap sarana, prasarana, instalasi, peralatan Pertambangan, dan penambangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.6.1', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasi dengan mempertimbangkan aspek Keselamatan Pertambangan pada tahap perancangan dan rekayasa terhadap sarana, prasarana, instalasi, peralatan Pertambangan, dan penambangan, namun prosedur tersebut belum mencakup mengenai petugas yang memiliki kompetensi untuk melakukan verifikasi dan orang yang bertanggungjawab memberikan persetujuan, serta prosedur belum dilaksanakan dengan baik.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.6.1', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasi dengan mempertimbangkan aspek Keselamatan Pertambangan pada tahap perancangan dan rekayasa terhadap sarana, prasarana, instalasi, peralatan Pertambangan, dan penambangan, yang telah mencakup mengenai petugas yang memiliki kompetensi untuk melakukan verifikasi dan orang yang bertanggungjawab memberikan persetujuan, namun prosedur tersebut belum dilaksanakan dengan baik.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.6.1', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasi dengan mempertimbangkan aspek Keselamatan Pertambangan pada tahap perancangan dan rekayasa terhadap sarana, prasarana, instalasi, peralatan Pertambangan, dan penambangan, yang telah mencakup mengenai petugas yang memiliki kompetensi untuk melakukan verifikasi dan orang yang bertanggungjawab memberikan persetujuan, dan prosedur tersebut telah dilaksanakan dengan baik.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.6.2', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang mengatur perubahan dan modifikasi perancangan dan rekayasa.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.6.2', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang mengatur perubahan dan modifikasi perancangan dan rekayasa yang mempunyai risiko Keselamatan Pertambangan dan/atau mempunyai implikasi terhadap ketentuan peraturan perundang-undangan diidentifikasi, didokumentasi, ditinjau ulang, dan disetujui oleh orang yang berwenang, namun prosedur tersebut belum mencakup mengenai petugas yang memiliki kompetensi untuk melakukan identifikasi dan tinjauan ulang, serta prosedur belum dilaksanakan dengan baik.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.6.2', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP memiliki prosedur pengangkutan bahan peledak, telah melakukan penetapan, pengamanan, kelayakan peralatan dan kendaraan untuk mengangkut, memindahkan, dan mengirim bahan peledak maupun yang berhubungan dengan pekerjaan peledakan, namun hasilnya belum memadai, dan kompetensi Pekerja yang menangani pengangkutan, pemindahan, dan pengiriman bahan peledak belum seluruhnya sesuai dengan peraturan perundang-undangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.6.2', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP memiliki prosedur pengangkutan bahan peledak, telah melakukan penetapan, pengamanan, kelayakan peralatan dan kendaraan untuk mengangkut, memindahkan, dan mengirim bahan peledak maupun yang berhubungan dengan pekerjaan peledakan, hasilnya telah memadai, namun kompetensi Pekerja yang yang menangani pengangkutan, pemindahan, dan pengiriman bahan peledak yang seluruhnya sesuai dengan peraturan perundang-undangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.7', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur pembelian yang terdokumentasi untuk menjamin bahwa spesifikasi teknik, persyaratan Keselamatan Pertambangan, serta ketentuan peraturan perundang-undangan menjadi pertimbangan utama dalam setiap keputusan untuk membeli sarana Pertambangan, bahan kimia, dan/ atau jasa.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.7', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur pembelian yang terdokumentasi untuk menjamin bahwa spesifikasi teknik, persyaratan Keselamatan Pertambangan, serta ketentuan peraturan perundang-undangan menjadi pertimbangan utama dalam setiap keputusan untuk membeli sarana Pertambangan, bahan kimia, dan/ atau jasa, namun prosedur tersebut belum mencakup penetapan spesifikasi pembelian, proses seleksi pembelian, dan proses verifikasi kesesuaian, serta belum dilaksanakan dengan baik.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.7', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur pembelian yang terdokumentasi untuk menjamin bahwa spesifikasi teknik, persyaratan Keselamatan Pertambangan, serta ketentuan peraturan perundang-undangan menjadi pertimbangan utama dalam setiap keputusan untuk membeli sarana Pertambangan, bahan kimia, dan/ atau jasa, yang telah mencakup penetapan spesifikasi pembelian, proses seleksi pembelian, dan proses verifikasi kesesuaian, namun prosedur tersebut belum dilaksanakan dengan baik.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.7', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur pembelian yang terdokumentasi untuk menjamin bahwa spesifikasi teknik, persyaratan Keselamatan Pertambangan, serta ketentuan peraturan perundang-undangan menjadi pertimbangan utama dalam setiap keputusan untuk membeli sarana Pertambangan, bahan kimia, dan/ atau jasa, yang telah mencakup penetapan spesifikasi pembelian, proses seleksi pembelian, dan proses verifikasi kesesuaian, dan prosedur tersebut telah dilaksanakan dengan baik, namun pada saat sarana Pertambangan, bahan kimia, dan/atau jasa diterima di tempat kerja, namun pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP belum memberikan penjelasan kepada semua pihak terkait yang akan menggunakan sarana Pertambangan, bahan kimia, dan/atau jasa tersebut, terkait dengan identifikasi, penilaian, dan pengendalian risiko.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.7', '4', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur pembelian yang terdokumentasi untuk menjamin bahwa spesifikasi teknik, persyaratan Keselamatan Pertambangan, serta ketentuan peraturan perundang-undangan menjadi pertimbangan utama dalam setiap keputusan untuk membeli sarana Pertambangan, bahan kimia, dan/ atau jasa, yang telah mencakup penetapan spesifikasi pembelian, proses seleksi pembelian, dan proses verifikasi kesesuaian, dan prosedur tersebut telah dilaksanakan dengan baik, serta pada saat sarana Pertambangan, bahan kimia, dan/atau jasa diterima di tempat kerja, pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memberikan penjelasan kepada semua pihak terkait yang akan menggunakan sarana Pertambangan, bahan kimia, dan/atau jasa tersebut, terkait dengan identifikasi, penilaian, dan pengendalian risiko.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.8.1', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP telah menetapkan prosedur yang terdokumentasi mengenai persyaratan, seleksi, dan penetapan perusahaan jasa Pertambangan untuk menjamin setiap perusahaan jasa Pertambangan memenuhi persyaratan Keselamatan Pertambangan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.8.1', '1', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP telah menetapkan prosedur yang terdokumentasi mengenai persyaratan, seleksi, dan penetapan perusahaan jasa Pertambangan untuk menjamin setiap perusahaan jasa Pertambangan memenuhi persyaratan Keselamatan Pertambangan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP, atau

kontrak kerja belum sepenuhnya memuat komitmen perusahaan jasa Pertambangan untuk mematuhi persyaratan Keselamatan Pertambangan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.8.1', '2', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP telah menetapkan prosedur yang terdokumentasi mengenai persyaratan, seleksi, dan penetapan perusahaan jasa Pertambangan untuk menjamin setiap perusahaan jasa Pertambangan memenuhi persyaratan Keselamatan Pertambangan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP, dan

kontrak kerja telah sepenuhnya memuat komitmen perusahaan jasa Pertambangan untuk mematuhi persyaratan Keselamatan Pertambangan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.8.2', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP telah menetapkan prosedur yang terdokumentasi mengenai tanggung jawab, pemantauan, dan pelaporan perusahaan jasa Pertambangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.8.2', '1', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP telah menetapkan prosedur yang terdokumentasi mengenai tanggung jawab, pemantauan, dan pelaporan perusahaan jasa Pertambangan,

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP belum memastikan perusahaan jasa Pertambangan memiliki Pekerja yang memiliki bukti-bukti kompetensi sesuai dengan kebutuhan untuk melaksanakan pekerjaan yang ada dalam kontrak kerja,

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP belum memastikan perusahaan jasa Pertambangan menggunakan seluruh sarana, prasarana, dan peralatan Pertambangan yang memiliki bukti-bukti kelayakan sesuai persyaratan Keselamatan Pertambangan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP, dan

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP belum menetapkan kewajiban kepada perusahaan jasa Pertambangan untuk melaporkan kepada KTT, PTL atau PJO mengenai pelaksanaan program Keselamatan Pertambangan secara berkala serta mengenai setiap nearmiss, kerusakan properti, kejadian berbahaya, cidera, dan sakit akibat kerja kepada KTT, PTL atau PJO.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.8.2', '2', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP telah menetapkan prosedur yang terdokumentasi mengenai tanggung jawab, pemantauan, dan pelaporan perusahaan jasa Pertambangan,

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP telah memastikan perusahaan jasa Pertambangan memiliki Pekerja yang memiliki bukti-bukti kompetensi sesuai dengan kebutuhan untuk melaksanakan pekerjaan yang ada dalam kontrak kerja,

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP telah memastikan perusahaan jasa Pertambangan menggunakan seluruh sarana, prasarana, dan peralatan Pertambangan yang memiliki bukti-bukti kelayakan sesuai persyaratan Keselamatan Pertambangan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP, dan

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP telah menetapkan kewajiban kepada perusahaan jasa Pertambangan untuk melaporkan kepada KTT, PTL atau PJO mengenai pelaksanaan program Keselamatan Pertambangan secara berkala serta mengenai setiap nearmiss, kerusakan properti, kejadian berbahaya, cidera, dan sakit akibat kerja kepada KTT, PTL atau PJO.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.8.3', '0', 'Tidak ada bukti yang menunjukkan pemegang pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP telah menetapkan prosedur yang terdokumentasi mengenai evaluasi perusahaan jasa Pertambangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.8.3', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP telah menetapkan prosedur yang terdokumentasi mengenai evaluasi perusahaan jasa Pertambangan, namun pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP belum melakukan evaluasi sesuai prosedur tersebut, dan belum memberikan umpan balik hasil evaluasi tersebut.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.8.3', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP telah menetapkan prosedur yang terdokumentasi mengenai evaluasi perusahaan jasa Pertambangan, dan pemegang pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP telah melakukan evaluasi sesuai prosedur tersebut, serta memberikan umpan balik hasil evaluasi tersebut.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.9', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pengelolaan keadaan darurat.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.9', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasi mengenai pengelolaan keadaan darurat, namun prosedur tersebut belum mencakup identifikasi dan penilaian potensi keadaan darurat, pencegahan keadaan darurat, kesiapsiagaan keadaan darurat, respon keadaan darurat, dan pemulihan keadaan darurat.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.9', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasi mengenai pengelolaan keadaan darurat, dan telah mencakup identifikasi dan penilaian potensi keadaan darurat, pencegahan keadaan darurat, kesiapsiagaan keadaan darurat, respon keadaan darurat, dan pemulihan keadaan darurat, namun prosedur tersebut belum dilaksanakan dengan baik.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.9', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasi mengenai pengelolaan keadaan darurat, dan telah mencakup identifikasi dan penilaian potensi keadaan darurat, pencegahan keadaan darurat, kesiapsiagaan keadaan darurat, respon keadaan darurat, dan pemulihan keadaan darurat, dan prosedur tersebut telah dilaksanakan dengan baik.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.10', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan penyediaan dan penyiapan pertolongan pertama pada kecelakaan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.10', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasi mengenai penyediaan dan penyiapan pertolongan pertama pada kecelakaan, namun prosedur tersebut belum mencakup petugas P3K, kotak P3K, isi kotak P3K, dan pencatatan penggunaan isi kotak P3K.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.10', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasi mengenai penyediaan dan penyiapan pertolongan pertama pada kecelakaan yang telah mencakup petugas P3K, kotak P3K, isi kotak P3K, dan pencatatan penggunaan isi kotak P3K, namun prosedur tersebut belum dilaksanakan dengan baik');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.10', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan prosedur yang terdokumentasi mengenai penyediaan dan penyiapan pertolongan pertama pada kecelakaan yang telah mencakup petugas P3K, kotak P3K, isi kotak P3K, dan pencatatan penggunaan isi kotak P3K, dan prosedur tersebut telah dilaksanakan dengan baik');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.11', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah mengkomunikasikan keselamatan di luar pekerjaan kepada semua Pekerja dan keluarganya.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.11', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah mengkomunikasikan keselamatan di luar pekerjaan kepada semua Pekerja dan keluarganya, namun materi promosi dan kegiatan keselamatan di luar pekerjaan belum sepenuhnya didokumentasikan dan belum dilakukan di seluruh departemen/bagian dari Pekerja.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.11', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah mengkomunikasikan keselamatan di luar pekerjaan kepada semua Pekerja dan keluarganya, dan materi promosi dan kegiatan keselamatan di luar pekerjaan telah didokumentasikan, namun belum dilakukan di seluruh departemen/bagian dari Pekerja.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('IV.11', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah mengkomunikasikan keselamatan di luar pekerjaan kepada semua Pekerja dan keluarganya, dan materi promosi dan kegiatan keselamatan di luar pekerjaan telah didokumentasikan, dan telah dilakukan di seluruh departemen/bagian dari Pekerja.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.1.1', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pemantauan dan pengukuran pencapaian tujuan, sasaran, dan program Keselamatan Pertambangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.1.1', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memiliki prosedur pemantauan dan pengukuran pencapaian tujuan, sasaran, dan program Keselamatan Pertambangan. Prosedur tersebut belum memadai (belum menjelaskan metode, frekuensi, ruang lingkup, dan peralatan yang sesuai), dan pelaksanaan pemantauan dan pengukuran tujuan, sasaran, dan program Keselamatan Pertambangan belum seluruhnya dilaksanakan sesuai prosedur.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.1.1', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memiliki prosedur pemantauan dan pengukuran pencapaian tujuan, sasaran, dan program Keselamatan Pertambangan. Prosedur tersebut telah menjelaskan metode, frekuensi, ruang lingkup, dan peralatan yang sesuai, dan pelaksanaan pemantauan dan pengukuran tujuan, sasaran, dan program Keselamatan Pertambangan belum seluruhnya dilaksanakan sesuai prosedur.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.1.1', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memiliki prosedur pemantauan dan pengukuran pencapaian tujuan, sasaran, dan program Keselamatan Pertambangan. Prosedur tersebut telah menjelaskan metode, frekuensi, ruang lingkup, dan peralatan yang sesuai, dan pelaksanaan pemantauan dan pengukuran tujuan, sasaran, dan program Keselamatan Pertambangan telah seluruhnya dilaksanakan sesuai prosedur, dan hasil pemantauan dan pengukuran pencapaian tujuan, sasaran, dan program Keselamatan Pertambangan belum seluruhnya didokumentasikan, dievaluasi, dan dibuat rencana pelaksanaan perbaikan atau tindak lanjutnya.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.1.1', '4', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP menunjukkan: • pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memiliki prosedur pemantauan dan pengukuran pencapaian tujuan, sasaran, dan program Keselamatan Pertambangan, • prosedur tersebut telah menjelaskan metode, frekuensi, ruang lingkup, dan peralatan yang sesuai, • pelaksanaan pemantauan dan pengukuran tujuan, sasaran, dan program Keselamatan Pertambangan telah seluruhnya dilaksanakan sesuai prosedur, dan • hasil pemantauan dan pengukuran pencapaian tujuan, sasaran, dan program Keselamatan Pertambangan telah seluruhnya didokumentasikan, dievaluasi, dan dibuat rencana pelaksanaan perbaikan atau tindak lanjutnya.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.1.2', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pemantauan dan pengukuran kinerja pengelolaan lingkungan kerja oleh petugas higiene industri yang ditetapkan Pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.1.2', '1', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP belum memiliki prosedur pemantauan, pengukuran kinerja, evaluasi, dan tindak lanjut pengelolaan lingkungan kerja yang menjelaskan metode, frekuensi, ruang lingkup, dan peralatan yang sesuai,

pemantauan, pengukuran, dan evaluasi pengelolaan lingkungan kerja belum dilaksanakan sesuai prosedur,

pemantauan dan pengukuran pengelolaan lingkungan kerja belum dilaksanakan oleh petugas higiene industri, dan

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP belum menetapkan rencana tindak lanjut dan perbaikan berdasarkan hasil evaluasi.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.1.2', '2', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memiliki prosedur pemantauan, pengukuran kinerja, evaluasi, dan tindak lanjut pengelolaan lingkungan kerja yang menjelaskan metode, frekuensi, ruang lingkup, dan peralatan yang sesuai.

pemantauan, pengukuran, dan evaluasi pengelolaan lingkungan kerja telah dilaksanakan sesuai prosedur.

pemantauan dan pengukuran pengelolaan lingkungan kerja telah dilaksanakan oleh petugas higiene industri.

belum menetapkan rencana tindak lanjut dan perbaikan berdasarkan hasil evaluasi.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.1.2', '3', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memiliki prosedur pemantauan, pengukuran kinerja, evaluasi, dan tindak lanjut pengelolaan lingkungan kerja yang menjelaskan metode, frekuensi, ruang lingkup, dan peralatan yang sesuai.

pemantauan, pengukuran, dan evaluasi pengelolaan lingkungan kerja telah dilaksanakan sesuai prosedur.

pemantauan dan pengukuran pengelolaan lingkungan kerja telah dilaksanakan oleh petugas higiene industri.

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan rencana tindak lanjut dan perbaikan berdasarkan hasil evaluasi.

sebagian tindak lanjut dan perbaikan berdasarkan hasil evaluasi telah dilaksanakan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.1.2', '4', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memiliki prosedur pemantauan, pengukuran kinerja, evaluasi, dan tindak lanjut pengelolaan lingkungan kerja yang menjelaskan metode, frekuensi, ruang lingkup, dan peralatan yang sesuai.

pemantauan, pengukuran, dan evaluasi pengelolaan lingkungan kerja telah dilaksanakan sesuai prosedur.

pemantauan dan pengukuran pengelolaan lingkungan kerja telah dilaksanakan oleh petugas higiene industri.

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan rencana tindak lanjut dan perbaikan berdasarkan hasil evaluasi.

seluruh tindak lanjut dan perbaikan berdasarkan hasil evaluasi telah dilaksanakan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.1.3', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pemantauan dan pengukuran kinerja pengelolaan kesehatan kerja Pertambangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.1.3', '1', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP belum memiliki prosedur pemantauan, pengukuran kinerja, evaluasi, dan tindak lanjut pengelolaan kesehatan kerja Pertambangan,

pemantauan, pengukuran, dan evaluasi pengelolaan kesehatan kerja belum dilaksanakan sesuai prosedur, dan

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP belum menetapkan rencana tindak lanjut dan perbaikan berdasarkan hasil evaluasi.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.1.3', '2', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memiliki prosedur pemantauan, pengukuran kinerja, evaluasi, dan tindak lanjut pengelolaan kesehatan kerja Pertambangan,

pemantauan, pengukuran, dan evaluasi pengelolaan kesehatan kerja Pertambangan telah dilaksanakan sesuai prosedur, dan

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP belum menetapkan rencana tindak lanjut dan perbaikan berdasarkan hasil evaluasi.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.1.3', '3', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memiliki prosedur pemantauan, pengukuran kinerja, evaluasi, dan tindak lanjut pengelolaan kesehatan kerja Pertambangan,

pemantauan, pengukuran, dan evaluasi pengelolaan kesehatan kerja Pertambangan telah dilaksanakan sesuai prosedur,

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan rencana tindak lanjut dan perbaikan berdasarkan hasil evaluasi, dan

sebagian tindak lanjut dan perbaikan berdasarkan hasil evaluasi telah dilaksanakan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.1.3', '4', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memiliki prosedur pemantauan, pengukuran kinerja, evaluasi, dan tindak lanjut pengelolaan kesehatan kerja Pertambangan yang menjelaskan metode, frekuensi, ruang lingkup, dan peralatan yang sesuai,

pemantauan, pengukuran, dan evaluasi pengelolaan kesehatan kerja Pertambangan telah dilaksanakan sesuai prosedur,

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan rencana tindak lanjut dan perbaikan berdasarkan hasil evaluasi, dan

seluruh tindak lanjut dan perbaikan berdasarkan hasil evaluasi telah dilaksanakan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.1.4', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan pemantauan dan pengukuran kinerja pengelolaan Keselamatan Operasi Pertambangan oleh Tenaga Teknis Pertambangan yang Berkompeten di bidang Keselamatan Operasi.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.1.4', '1', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP belum memiliki prosedur pemantauan, pengukuran kinerja, evaluasi, dan tindak lanjut pengelolaan Keselamatan Operasi Pertambangan yang menjelaskan metode, frekuensi, ruang lingkup, dan peralatan yang sesuai,

pemantauan, pengukuran, dan evaluasi pengelolaan Keselamatan Operasi Pertambangan belum dilaksanakan sesuai prosedur,

pemantauan dan pengukuran pengelolaan Keselamatan Operasi Pertambangan belum dilaksanakan oleh Tenaga Teknis Pertambangan yang Berkompeten di bidang Keselamatan Operasi, dan

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP belum menetapkan rencana tindak lanjut dan perbaikan berdasarkan hasil evaluasi.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.1.4', '2', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memiliki prosedur pemantauan, pengukuran kinerja, evaluasi, dan tindak lanjut pengelolaan Keselamatan Operasi Pertambangan yang menjelaskan metode, frekuensi, ruang lingkup, dan peralatan yang sesuai,

pemantauan, pengukuran, dan evaluasi pengelolaan Keselamatan Operasi Pertambangan telah dilaksanakan sesuai prosedur,

pemantauan dan pengukuran pengelolaan Keselamatan Operasi Pertambangan telah dilaksanakan oleh Tenaga Teknis Pertambangan yang Berkompeten di bidang Keselamatan Operasi, dan

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP belum menetapkan rencana tindak lanjut dan perbaikan berdasarkan hasil evaluasi.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.1.4', '3', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memiliki prosedur pemantauan, pengukuran kinerja, evaluasi, dan tindak lanjut pengelolaan Keselamatan Operasi Pertambangan yang menjelaskan metode, frekuensi, ruang lingkup, dan peralatan yang sesuai,

pemantauan, pengukuran, dan evaluasi pengelolaan Keselamatan Operasi Pertambangan telah dilaksanakan sesuai prosedur,

pemantauan dan pengukuran pengelolaan Keselamatan Operasi Pertambangan telah dilaksanakan oleh Tenaga Teknis Pertambangan yang Berkompeten di bidang Keselamatan Operasi,

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan rencana tindak lanjut dan perbaikan berdasarkan hasil evaluasi, dan

sebagian tindak lanjut dan perbaikan berdasarkan hasil evaluasi telah dilaksanakan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.1.4', '4', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memiliki prosedur pemantauan, pengukuran kinerja, evaluasi, dan tindak lanjut pengelolaan Keselamatan Operasi Pertambangan yang menjelaskan metode, frekuensi, ruang lingkup, dan peralatan yang sesuai,

pemantauan, pengukuran, dan evaluasi pengelolaan Keselamatan Operasi Pertambangan telah dilaksanakan sesuai prosedur,

pemantauan dan pengukuran pengelolaan keselamatan operasi Pertambangan telah dilaksanakan oleh Tenaga Teknis Pertambangan yang Kompeten di bidang Keselamatan Operasi,

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan rencana tindak lanjut dan perbaikan berdasarkan hasil evaluasi, dan

seluruh tindak lanjut dan perbaikan berdasarkan hasil evaluasi telah dilaksanakan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.1.5', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP telah melakukan pemantauan dan pengukuran kinerja pengelolaan bahan peledak dan peledakan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.1.5', '1', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP belum memiliki prosedur pemantauan, pengukuran kinerja, evaluasi, dan tindak lanjut pengelolaan bahan peledak dan peledakan yang menjelaskan metode, frekuensi, ruang lingkup, dan peralatan yang sesuai,

pemantauan, pengukuran, dan evaluasi pengelolaan bahan peledak dan peledakan belum dilaksanakan sesuai prosedur, dan

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP belum menetapkan rencana tindak lanjut dan perbaikan berdasarkan hasil evaluasi.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.1.5', '2', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP telah memiliki prosedur pemantauan, pengukuran kinerja, evaluasi, dan tindak lanjut pengelolaan bahan peledak dan peledakan yang menjelaskan metode, frekuensi, ruang lingkup, dan peralatan yang sesuai,

pemantauan, pengukuran, dan evaluasi pengelolaan bahan peledak dan peledakan telah dilaksanakan sesuai prosedur, dan

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP belum menetapkan rencana tindak lanjut dan perbaikan berdasarkan hasil evaluasi.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.1.5', '3', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memiliki prosedur pemantauan, pengukuran kinerja, evaluasi, dan tindak lanjut pengelolaan bahan peledak dan peledakan yang menjelaskan metode, frekuensi, ruang lingkup, dan peralatan yang sesuai,

pemantauan, pengukuran, dan evaluasi pengelolaan bahan peledak dan peledakan telah dilaksanakan sesuai prosedur,

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP telah menetapkan rencana tindak lanjut dan perbaikan berdasarkan hasil evaluasi, dan

sebagian tindak lanjut dan perbaikan berdasarkan hasil evaluasi telah dilaksanakan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.1.5', '4', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memiliki prosedur pemantauan, pengukuran kinerja, evaluasi, dan tindak lanjut pengelolaan bahan peledak dan peledakan yang menjelaskan metode, frekuensi, ruang lingkup, dan peralatan yang sesuai,

pemantauan, pengukuran, dan evaluasi pengelolaan bahan peledak dan peledakan telah dilaksanakan sesuai prosedur, dan

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IUJP telah menetapkan rencana tindak lanjut dan perbaikan berdasarkan hasil evaluasi.

seluruh tindak lanjut dan perbaikan berdasarkan hasil evaluasi telah dilaksanakan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.2', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan inspeksi pelaksanaan Keselamatan Pertambangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.2', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memiliki prosedur inspeksi Keselamatan Pertambangan,

prosedur inspeksi Keselamatan Pertambangan belum memadai (belum menjelaskan tujuan, jenis, pelaksana, objek, jadwal dan frekuensi, lembar periksa, peralatan, metode atau tata cara, pelaksanaan, klasifikasi bahaya, laporan, tindak lanjut, evaluasi, dan dokumentasi yang sesuai), dan

berdasarkan evaluasi, pelaksanaan inspeksi Keselamatan Pertambangan belum dilaksanakan sesuai prosedur.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.2', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memiliki prosedur inspeksi Keselamatan Pertambangan,

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memiliki prosedur inspeksi Keselamatan Pertambangan yang menjelaskan tujuan, jenis, pelaksana, objek, jadwal dan frekuensi, lembar periksa, peralatan, metode atau tata cara, pelaksanaan, klasifikasi bahaya, laporan, tindak lanjut, evaluasi, dan dokumentasi yang sesuai, dan

berdasarkan evaluasi, pelaksanaan inspeksi Keselamatan Pertambangan belum dilaksanakan sesuai prosedur.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.2', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memiliki prosedur inspeksi Keselamatan Pertambangan,

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memiliki prosedur inspeksi Keselamatan Pertambangan yang menjelaskan tujuan, jenis, pelaksana, objek, jadwal dan frekuensi, lembar periksa, peralatan, metode atau tata cara, pelaksanaan, klasifikasi bahaya, laporan, tindak lanjut, evaluasi, dan dokumentasi yang sesuai,

berdasarkan evaluasi, pelaksanaan inspeksi Keselamatan Pertambangan telah sebagian dilaksanakan sesuai prosedur, dan

hasil inspeksi Keselamatan Pertambangan belum seluruhnya didokumentasikan dan dibuat rencana pelaksanaan perbaikan atau tindak lanjutnya.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.2', '4', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memiliki prosedur inspeksi Keselamatan Pertambangan,

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memiliki prosedur inspeksi Keselamatan Pertambangan yang menjelaskan tujuan, jenis, pelaksana, objek, jadwal dan frekuensi, lembar periksa, peralatan, metode atau tata cara, pelaksanaan, klasifikasi bahaya, laporan, tindak lanjut, evaluasi, dan dokumentasi yang sesuai,

berdasarkan evaluasi, pelaksanaan inspeksi Keselamatan Pertambangan telah dilaksanakan sesuai prosedur,

hasil inspeksi Keselamatan Pertambangan telah seluruhnya didokumentasikan dan dibuat rencana pelaksanaan perbaikan atau tindak lanjutnya, dan

rencana perbaikan atas hasil inspeksi Keselamatan Pertambangan tersebut telah dilaksanakan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.3', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan evaluasi kepatuhan terhadap ketentuan peraturan perundang-undangan dan persyaratan lainnya yang terkait.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.3', '1', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memiliki prosedur untuk melakukan evaluasi kepatuhan terhadap ketentuan peraturan perundang-undangan dan persyaratan lainnya yang terkait,

prosedur tersebut belum memadai,

pelaksanaan evaluasi tersebut belum dilaksanakan sesuai prosedur, dan

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP belum menyusun rencana dan pelaksanaan tindak lanjut berdasarkan hasil evaluasi.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.3', '2', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memiliki prosedur untuk melakukan evaluasi kepatuhan terhadap ketentuan peraturan perundang-undangan dan persyaratan lainnya yang terkait,

prosedur tersebut telah memadai,

pelaksanaan evaluasi tersebut belum dilaksanakan sesuai prosedur, dan

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP belum menyusun rencana dan pelaksanaan tindak lanjut berdasarkan hasil evaluasi.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.3', '3', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memiliki prosedur untuk melakukan evaluasi kepatuhan terhadap ketentuan peraturan perundang-undangan dan persyaratan lainnya yang terkait,

prosedur tersebut telah memadai,

pelaksanaan evaluasi tersebut telah dilaksanakan sesuai prosedur,

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun rencana dan pelaksanaan tindak lanjut berdasarkan hasil evaluasi, dan

sebagian rencana pelaksanaan perbaikan atau tindak lanjut hasil perbaikan telah ditindaklanjuti.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.3', '4', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memiliki prosedur untuk melakukan evaluasi kepatuhan terhadap ketentuan peraturan perundang-undangan dan persyaratan lainnya yang terkait,

prosedur tersebut telah memadai,

pelaksanaan evaluasi tersebut telah dilaksanakan sesuai prosedur,

Pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun rencana dan pelaksanaan tindak lanjut berdasarkan hasil evaluasi, dan

seluruh rencana pelaksanaan perbaikan atau tindak lanjut hasil perbaikan telah ditindaklanjuti.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.4', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan evaluasi dan menindaklanjuti hasillaporan dari penyelidikan kecelakaan, Kejadian Berbahaya, kejadian akibat penyakit tenaga kerja, dan PenyakitAkibat Kerja.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.4', '1', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memiliki prosedur penyelidikan kecelakaan, Kejadian Berbahaya, kejadian akibat penyakit tenaga kerja, dan Penyakit Akibat Kerja,

prosedur tersebut belum memadai,

pelaksanaan penyelidikan kecelakaan, kejadian berbahaya, kejadian akibat penyakit tenaga kerja, dan penyakit akibat kerja belum dilaksanakan sesuai prosedur, dan

pelaksanaan tersebut belum seluruhnya didokumentasikan, dikomunikasikan dan dibuat rencana pelaksanaan tindakan koreksi serta pemantauan tindak lanjutnya.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.4', '2', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memiliki prosedur penyelidikan kecelakaan, Kejadian Berbahaya, kejadian akibat penyakit tenaga kerja, dan Penyakit Akibat Kerja,

prosedur tersebut telah meliputi pelaporan, pengamanan lokasi dan barang bukti di tempat kejadian, pembentukan tim penyelidikan, tahapan penyelidikan, tahap pemantauan dan tahap evaluasi penyelidikan kecelakaan atau Kejadian Berbahaya,

pelaksanaan penyelidikan kecelakaan, kejadian berbahaya, kejadian akibat penyakit tenaga kerja, dan Penyakit Akibat Kerja belum sepenuhnya dilaksanakan sesuai prosedur, dan

pelaksanaan tersebut belum seluruhnya didokumentasikan, dikomunikasikan dan dibuat rencana pelaksanaan tindakan koreksi serta pemantauan tindak lanjutnya.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.4', '3', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memiliki prosedur penyelidikan kecelakaan, Bejadian Berbahaya, kejadian akibat penyakit tenaga kerja, dan Penyakit Akibat Kerja,

prosedur tersebut telah meliputi pelaporan, pengamanan lokasi dan barang bukti di tempat kejadian, pembentukan tim penyelidikan, tahapan penyelidikan, tahap pemantauan dan tahap evaluasi penyelidikan kecelakaan atau Kejadian Berbahaya,

pelaksanaan penyelidikan kecelakaan, kejadian berbahaya, kejadian akibat penyakit tenaga kerja, dan Penyakit Akibat Kerja telah dilaksanakan sesuai prosedur, dan

pelaksanaan tersebut belum seluruhnya didokumentasikan, dikomunikasikan dan dibuat rencana pelaksanaan tindakan koreksi serta pemantauan tindak lanjutnya.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.4', '4', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memiliki prosedur penyelidikan kecelakaan, Bejadian Berbahaya, kejadian akibat penyakit tenaga kerja, dan Penyakit Akibat Kerja,

prosedur tersebut telah meliputi pelaporan, pengamanan lokasi dan barang bukti di tempat kejadian, pembentukan tim penyelidikan, tahapan penyelidikan, tahap pemantauan dan tahap evaluasi penyelidikan kecelakaan atau Kejadian Berbahaya,

pelaksanaan penyelidikan kecelakaan, kejadian berbahaya, kejadian akibat penyakit tenaga kerja, dan Penyakit Akibat Kerja telah dilaksanakan sesuai prosedur,

pelaksanaan tersebut telah seluruhnya didokumentasikan, dikomunikasikan sebagai bentuk edukasi, dan

tindakan koreksi telah seluruhnya dilaksanakan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.5.1', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IPR telah melakukan evaluasi pengelolaan buku tambang.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.5.1', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IPR telah melakukan evaluasi pengelolaan buku tambang, dengan kondisi:

evaluasi belum seluruhnya mencakup pelaksanaan perintah, larangan, petunjuk, serta pemberitahuan dari KaIT dan Inspektur Tambang, pendaftaran-pendaftaran yang dipersyaratkan dalam ketentuan peraturan perundang-undangan,

evaluasi belum dilakukan paling sedikit 1 (satu) kali dalam jangka waktu 6 (enam) bulan,

hasil evaluasi belum ditindaklanjuti, dan

berdasarkan hasil evaluasi, KTT atau PTL belum melaksanakan larangan, perintah, dan petunjuk Inspektur Tambang dalam buku tambang, dan/atau KTT atau PTL belum mencatat hal-hal yang diwajibkan untuk didaftarkan di buku tambang berdasarkan ketentuan peraturan perundang-undangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.5.1', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IPR telah melakukan evaluasi pengelolaan buku tambang, dengan kondisi:

evaluasi telah mencakup pelaksanaan perintah, larangan, petunjuk, serta pemberitahuan KaIT dan Inspektur Tambang, pendaftaran-pendaftaran yang dipersyaratkan dalam ketentuan peraturan perundang-undangan,

evaluasi telah dilakukan paling sedikit 1 (satu) kali dalam jangka waktu 6 (enam) bulan,

hasil evaluasi belum ditindaklanjuti, dan

berdasarkan hasil evaluasi, KTT atau PTL belum melaksanakan larangan, perintah, dan petunjuk Inspektur Tambang dalam buku tambang, dan/atau KTT atau PTL belum mencatat hal-hal yang diwajibkan untuk didaftarkan di buku tambang berdasarkan ketentuan peraturan perundang-undangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.5.1', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IPR telah melakukan evaluasi pengelolaan buku tambang, dengan kondisi:

evaluasi telah mencakup pelaksanaan perintah, larangan, petunjuk, serta pemberitahuan dari KaIT dan Inspektur Tambang, pendaftaran-pendaftaran yang dipersyaratkan dalam ketentuan peraturan perundang-undangan,

evaluasi telah dilakukan paling sedikit 1 (satu) kali dalam jangka waktu 6 (enam) bulan,

hasil evaluasi telah ditindaklanjuti, dan

berdasarkan hasil evaluasi, KTT atau PTL belum melaksanakan larangan, perintah, dan petunjuk Inspektur Tambang dalam buku tambang, dan/atau KTT atau PTL belum mencatat hal-hal yang diwajibkan untuk didaftarkan di buku tambang berdasarkan ketentuan peraturan perundang-undangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.5.1', '4', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IPR telah melakukan evaluasi pengelolaan buku tambang, dengan kondisi:

evaluasi telah mencakup pelaksanaan perintah, larangan, petunjuk, serta pemberitahuan dari KaIT dan Inspektur Tambang, pendaftaran-pendaftaran yang dipersyaratkan dalam ketentuan peraturan perundang-undangan,

evaluasi telah dilakukan paling sedikit 1 (satu) kali dalam jangka waktu 6 (enam) bulan,

hasil evaluasi telah ditindaklanjuti, dan

berdasarkan hasil evaluasi, KTT atau PTL telah melaksanakan larangan, perintah, dan petunjuk Inspektur Tambang dalam buku tambang, dan/atau KTT atau PTL telah mencatat hal-hal yang diwajibkan untuk didaftarkan di buku tambang berdasarkan ketentuan peraturan perundang-undangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.5.2', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IPR telah melakukan evaluasi pengelolaan buku daftar kecelakaan tambang.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.5.2', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IPR telah melakukan evaluasi pengelolaan buku daftar kecelakaan tambang, dengan kondisi:

evaluasi belum mencakup nomor urut kecelakaan tambang untuk 1 (satu) korban dengan 1 (satu) penomoran; waktu, hari, dan jam kecelakaan; tempat kecelakaan; nama, jenis kelamin, dan umur dari korban kecelakaan; jabatan dan berapa lama dipegang oleh orang yang mendapat kecelakaan; sifat kecelakaan; pekerjaan yang sedang dilakukan pada saat kecelakaan; saksi-saksi kecelakaan; uraian tentang kecelakaan dan sebab-sebabnya yang dibuat dan ditandatangani oleh KTT atau PTL atau orang yang ditunjuk; dan waktu dilaporkan kepada KaIT,

evaluasi belum dilakukan paling sedikit 1 (satu) kali dalam jangka waktu 6 (enam) bulan,

hasil evaluasi belum ditindaklanjuti, dan

berdasarkan hasil evaluasi, KTT atau PTL belum seluruhnya mendaftarkan setiap kecelakaan tambang yang berakibat cidera ringan, berat, dan mati (jika ada) dalam buku daftar kecelakaan tambang.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.5.2', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IPR telah melakukan evaluasi pengelolaan buku daftar kecelakaan tambang, dengan kondisi:

evaluasi telah mencakup nomor urut kecelakaan tambang untuk 1 (satu) korban dengan 1 (satu) penomoran; waktu, hari, dan jam kecelakaan; tempat kecelakaan; nama, jenis kelamin, dan umur dari korban kecelakaan; jabatan dan berapa lama dipegang oleh orang yang mendapat kecelakaan; sifat kecelakaan; pekerjaan yang sedang dilakukan pada saat kecelakaan; saksi-saksi kecelakaan; uraian tentang kecelakaan dan sebab-sebabnya yang dibuat dan ditandatangani oleh KTT atau PTL atau orang yang ditunjuk; dan waktu dilaporkan kepada KaIT,

evaluasi telah dilakukan paling sedikit 1 (satu) kali dalam jangka waktu 6 (enam) bulan,

hasil evaluasi belum ditindaklanjuti, dan

berdasarkan hasil evaluasi, KTT atau PTL belum seluruhnya mendaftarkan setiap kecelakaan tambang yang berakibat cidera ringan, berat, dan mati (jika ada) dalam buku daftar kecelakaan tambang.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.5.2', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IPR telah melakukan evaluasi pengelolaan buku daftar kecelakaan tambang, dengan kondisi:

evaluasi telah mencakup nomor urut kecelakaan tambang untuk 1 (satu) korban dengan 1 (satu) penomoran; waktu, hari, dan jam kecelakaan; tempat kecelakaan; nama, jenis kelamin, dan umur dari korban kecelakaan; jabatan dan berapa lama dipegang oleh orang yang mendapat kecelakaan; sifat kecelakaan; pekerjaan yang sedang dilakukan pada saat kecelakaan; saksi-saksi kecelakaan; uraian tentang kecelakaan dan sebab-sebabnya yang dibuat dan ditandatangani oleh KTT atau PTL atau orang yang ditunjuk; dan waktu dilaporkan kepada KaIT,

evaluasi telah dilakukan paling sedikit 1 (satu) kali dalam jangka waktu 6 (enam) bulan,

hasil evaluasi telah ditindaklanjuti, dan

berdasarkan hasil evaluasi, KTT atau PTL belum seluruhnya mendaftarkan setiap kecelakaan tambang yang berakibat cidera ringan, berat, dan mati (jika ada) dalam buku daftar kecelakaan tambang.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.5.2', '4', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, atau IPR telah melakukan evaluasi pengelolaan buku daftar kecelakaan tambang, dengan kondisi:

evaluasi telah mencakup nomor urut kecelakaan tambang untuk 1 (satu) korban dengan 1 (satu) penomoran; waktu, hari, dan jam kecelakaan; tempat kecelakaan; nama, jenis kelamin, dan umur dari korban kecelakaan; jabatan dan berapa lama dipegang oleh orang yang mendapat kecelakaan; sifat kecelakaan; pekerjaan yang sedang dilakukan pada saat kecelakaan; saksi-saksi kecelakaan; uraian tentang kecelakaan dan sebab-sebabnya yang dibuat dan ditandatangani oleh KTT atau PTL atau orang yang ditunjuk; dan waktu dilaporkan kepada KaIT,

evaluasi telah dilakukan paling sedikit 1 (satu) kali dalam jangka waktu 6 (enam) bulan,

hasil evaluasi telah ditindaklanjuti,

berdasarkan hasil evaluasi, KTT atau PTL telah mendaftarkan setiap kecelakaan tambang yang berakibat cidera ringan, berat, dan mati (jika ada) dalam buku daftar kecelakaan tambang.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.5.3', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan evaluasi pelaporan Keselamatan Pertambangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.5.3', '1', 'diberikan jika perusahaan telah melakukan evaluasi, namun evaluasi tersebut menunjukkan banyak kekurangan, yaitu: evaluasi belum mencakup ketepatan waktu, kesesuaian isi, dan isi laporan; belum dilakukan minimal 1 kali dalam 6 bulan; hasilnya belum ditindaklanjuti; dan yang paling penting, KTT atau PTL belum menyampaikan seluruh laporan wajib kepada KaIT, pelaporan belum sesuai format, dan tidak memenuhi tata waktu yang ditetapkan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.5.3', '2', 'menandakan adanya peningkatan kualitas sistem karena evaluasi yang dilakukan perusahaan telah mencakup seluruh aspek wajib pelaporan dan telah dilakukan minimal 1 kali dalam 6 bulan. Namun, perusahaan masih dinilai rendah karena hasil evaluasi belum ditindaklanjuti, dan KTT atau PTL tetap belum memenuhi seluruh kriteria pelaporan (ketepatan waktu, kesesuaian format, dan kelengkapan laporan) yang diwajibkan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.5.3', '3', 'diberikan jika perusahaan telah memenuhi kriteria Nilai 2 dan menunjukkan perbaikan nyata karena hasil evaluasi telah ditindaklanjuti. Namun, perusahaan belum mencapai ketaatan penuh karena KTT atau PTL masih belum menyampaikan seluruh laporan wajib kepada KaIT, pelaporan belum sesuai format, dan penyampaian laporan tidak memenuhi tata waktu yang ditetapkan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.5.3', '4', 'merupakan nilai tertinggi yang diberikan jika perusahaan mencapai kepatuhan sempurna. Semua persyaratan telah terpenuhi: evaluasi lengkap dan rutin (minimal 1 kali dalam 6 bulan), hasil evaluasi telah ditindaklanjuti, dan yang terpenting, KTT atau PTL telah membuktikan bahwa mereka telah menyampaikan seluruh laporan tertulis aspek Keselamatan Pertambangan kepada KaIT, pelaporan telah sesuai format, dan telah memenuhi tata waktu yang ditetapkan sesuai ketentuan peraturan perundang-undangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.5.4', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan evaluasi dokumentasi Kejadian Berbahaya, kejadian akibat penyakit tenaga kerja, dan Penyakit Akibat Kerja.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.5.4', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan evaluasi dokumentasi Kejadian Berbahaya, kejadian akibat penyakit tenaga kerja, dan Penyakit Akibat Kerja, dengan kondisi:

evaluasi belum mencakup kesesuaian isi, kesesuaian format, hasil analisis terhadap penyebab kejadian, dan pelaksanaan tindak lanjut.

evaluasi belum dilakukan paling sedikit 1 (satu) kali dalam jangka waktu 6 (enam) bulan,

hasil evaluasi belum ditindaklanjuti, dan

berdasarkan hasil evaluasi, KTT, PTL, atau PJO belum mendokumentasikan sebagian Kejadian Berbahaya, kejadian akibat penyakit tenaga kerja, dan Penyakit Akibat Kerja dengan menggunakan format khusus yang ditentukan oleh KaIT.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.5.4', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan evaluasi dokumentasi Kejadian Berbahaya, kejadian akibat penyakit tenaga kerja, dan Penyakit Akibat Kerja, dengan kondisi:

evaluasi telah mencakup kesesuaian isi, kesesuaian format, hasil analisis terhadap penyebab kejadian, dan pelaksanaan tindak lanjut.

evaluasi telah dilakukan paling sedikit 1 (satu) kali dalam jangka waktu 6 (enam) bulan,

hasil evaluasi belum ditindaklanjuti, dan

berdasarkan hasil evaluasi, KTT, PTL, atau PJO belum mendokumentasikan sebagian Kejadian Berbahaya, kejadian akibat penyakit tenaga kerja, dan Penyakit Akibat Kerja dengan menggunakan format khusus yang ditentukan oleh KaIT.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.5.4', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan evaluasi dokumentasi Kejadian Berbahaya, kejadian akibat penyakit tenaga kerja, dan Penyakit Akibat Kerja, dengan kondisi:

evaluasi telah mencakup kesesuaian isi, kesesuaian format, hasil analisis terhadap penyebab kejadian, dan pelaksanaan tindak lanjut.

evaluasi telah dilakukan paling sedikit 1 (satu) kali dalam jangka waktu 6 (enam) bulan,

hasil evaluasi belum ditindaklanjuti, dan

berdasarkan hasil evaluasi, KTT, PTL, atau PJO belum mendokumentasikan sebagian Kejadian Berbahaya, kejadian akibat penyakit tenaga kerja, dan Penyakit Akibat Kerja dengan menggunakan format khusus yang ditentukan oleh KaIT.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.5.4', '4', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan evaluasi dokumentasi Kejadian Berbahaya, kejadian akibat penyakit tenaga kerja, dan Penyakit Akibat Kerja, dengan kondisi:

evaluasi telah mencakup kesesuaian isi, kesesuaian format, hasil analisis terhadap penyebab kejadian, dan pelaksanaan tindak lanjut.

evaluasi telah dilakukan paling sedikit 1 (satu) kali dalam jangka waktu 6 (enam) bulan,

hasil evaluasi belum ditindaklanjuti, dan

berdasarkan hasil evaluasi, KTT atau PTL telah mendokumentasikan Kejadian Berbahaya, kejadian akibat penyakit tenaga kerja, dan Penyakit Akibat Kerja, telah menggunakan format khusus yang ditentukan oleh KaIT.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.5.5', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan evaluasi dokumen dan laporan pemenuhan kompetensi sesuai ketentuan peraturan perundang-undangan serta persyaratan lainnya.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.5.5', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan evaluasi dokumen dan laporan pemenuhan kompetensi sesuai ketentuan peraturan perundang-undangan serta persyaratan lainnya, dengan kondisi:

evaluasi belum mencakup tingkat pemenuhan persyaratan dan pelaksanaan tindak lanjut.

evaluasi belum dilakukan paling sedikit 1 (satu) kali dalam jangka waktu 6 (enam) bulan, dan

hasil evaluasi belum ditindaklanjuti sepenuhnya.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.5.5', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan evaluasi dokumen dan laporan pemenuhan kompetensi sesuai ketentuan peraturan perundang-undangan serta persyaratan lainnya, dengan kondisi:

evaluasi telah mencakup tingkat pemenuhan persyaratan dan pelaksanaan tindak lanjut.

evaluasi telah dilakukan paling sedikit 1 (satu) kali dalam jangka waktu 6 (enam) bulan, dan

hasil evaluasi belum ditindaklanjuti sepenuhnya.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.5.5', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan evaluasi dokumen dan laporan pemenuhan kompetensi sesuai ketentuan peraturan perundang-undangan serta persyaratan lainnya, dengan kondisi:

evaluasi telah mencakup tingkat pemenuhan persyaratan dan pelaksanaan tindak lanjut.

evaluasi telah dilakukan paling sedikit 1 (satu) kali dalam jangka waktu 6 (enam) bulan, dan

hasil evaluasi belum ditindaklanjuti sepenuhnya.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.5.5', '4', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan evaluasi dokumen dan laporan pemenuhan kompetensi sesuai ketentuan peraturan perundang-undangan serta persyaratan lainnya, dengan kondisi:

evaluasi telah mencakup tingkat pemenuhan persyaratan dan pelaksanaan tindak lanjut.

evaluasi telah dilakukan paling sedikit 1 (satu) kali dalam jangka waktu 6 (enam) bulan, dan

hasil evaluasi belum ditindaklanjuti sepenuhnya.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.6', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan audit internal penerapan SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian paling sedikit 1 (satu) kali dalam jangka waktu 1 (satu) tahun.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.6', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memiliki prosedur pelaksanaan audit internal penerapan SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian, dengan kondisi:

prosedur pelaksanaan audit internal belum memadai, dan

pelaksanaan audit internal penerapan SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian belum dilaksanakan sesuai prosedur.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.6', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memiliki prosedur pelaksanaan audit internal penerapan SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian, dengan kondisi:

prosedur pelaksanaan audit internal telah meliputi ruang lingkup, frekuensi, metodologi, kompetensi auditor, tanggung jawab dan persyaratan pelaksanaan audit, serta pelaporan hasil audit, dan

pelaksanaan audit internal penerapan SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian belum dilaksanakan sesuai prosedur.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.6', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memiliki prosedur pelaksanaan audit internal penerapan SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian, dengan kondisi:

prosedur pelaksanaan audit internal telah meliputi ruang lingkup, frekuensi, metodologi, kompetensi auditor, tanggung jawab dan persyaratan pelaksanaan audit, serta pelaporan hasil audit,

pelaksanaan audit internal penerapan SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian telah dilaksanakan sesuai prosedur, dan

hasil audit belum seluruhnya didokumentasikan dan dibuat rencana pelaksanaan tindak lanjutnya.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.6', '4', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memiliki prosedur pelaksanaan audit internal penerapan SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian, dengan kondisi:

prosedur pelaksanaan audit internal telah meliputi ruang lingkup, frekuensi, metodologi, kompetensi auditor, tanggung jawab dan persyaratan pelaksanaan audit, serta pelaporan hasil audit,

pelaksanaan audit internal penerapan SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian telah dilaksanakan sesuai prosedur, dan

hasil audit telah seluruhnya didokumentasikan dan dibuat rencana pelaksanaan tindak lanjutnya, dan rencana tersebut telah dilaksanakan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.7', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memiliki rencana perbaikan dan tindak lanjut ketidaksesuaian terhadap standar kerja, praktik kerja, prosedur kerja, persyaratan dalam ketentuan peraturan perundang-undangan, dan persyaratan-persyaratan SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian yang dapat menyebabkan cidera atau penyakit, kerusakan sarana, prasarana, instalasi, dan peralatan Pertambangan, dan/atau kerusakan lingkungan kerja Pertambangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.7', '1', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memiliki prosedur untuk menindaklanjuti ketidaksesuaian, namun prosedur tersebut belum memadai, dan

pelaksanaan perencanaan perbaikan dan tindak lanjut tersebut belum dilaksanakan sesuai prosedur.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.7', '2', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memiliki prosedur untuk menindaklanjuti ketidaksesuaian yang telah mencakup identifikasi dan perbaikan ketidaksesuaian, analisis penyebab ketidaksesuaian, evaluasi kebutuhan tindakan untuk mencegah ketidaksesuaian, catatan dan komunikasi hasil tindakan perbaikan dan pencegahan, dan evaluasi efektifitas tindakan perbaikan dan pencegahan, dan

pelaksanaan perencanaan perbaikan dan tindak lanjut tersebut belum dilaksanakan sesuai prosedur.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.7', '3', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memiliki prosedur untuk menindaklanjuti ketidaksesuaian yang telah mencakup identifikasi dan perbaikan ketidaksesuaian, analisis penyebab ketidaksesuaian, evaluasi kebutuhan tindakan untuk mencegah ketidaksesuaian, catatan dan komunikasi hasil tindakan perbaikan dan pencegahan, dan evaluasi efektifitas tindakan perbaikan dan pencegahan,

pelaksanaan perencanaan perbaikan dan tindak lanjut telah dilaksanakan sesuai prosedur, dan

perbaikan dan tindak lanjut belum seluruhnya didokumentasikan dan dilaksanakan sesuai perencanaan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('V.7', '4', 'Terdapat bukti yang menunjukkan:

pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah memiliki prosedur untuk menindaklanjuti ketidaksesuaian yang telah mencakup identifikasi dan perbaikan ketidaksesuaian, analisis penyebab ketidaksesuaian, evaluasi kebutuhan tindakan untuk mencegah ketidaksesuaian, catatan dan komunikasi hasil tindakan perbaikan dan pencegahan, dan evaluasi efektifitas tindakan perbaikan dan pencegahan,

pelaksanaan perencanaan perbaikan dan tindak lanjut telah dilaksanakan sesuai prosedur, dan

perbaikan dan tindak lanjut telah seluruhnya didokumentasikan dan dilaksanakan sesuai perencanaan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('VI.1', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun, menetapkan, dan mendokumentasikan manual SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('VI.1', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun, menetapkan, dan mendokumentasikan manual SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian, dengan kondisi:

manual SMKP belum mencakup ruang lingkup SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian, prosedur terdokumentasi yang ditetapkan untuk SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian, dan uraian dari interaksi antara elemen-elemen dalam SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian dan acuan dokumen dari elemen terkait; dan

manual SMKP belum disahkan oleh manajemen Pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, dan IUJP.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('VI.1', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun, menetapkan, dan mendokumentasikan manual SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian, dengan kondisi:

manual SMKP telah mencakup ruang lingkup SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian, prosedur terdokumentasi yang ditetapkan untuk SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian, dan uraian dari interaksi antara elemen-elemen dalam SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian dan acuan dokumen dari elemen terkait;

manual SMKP telah disahkan oleh manajemen Pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, dan IUJP;

manual SMKP belum disosialisasikan kepada seluruh departemen/bagian dari Pekerja; dan

manual SMKP belum secara konsisten digunakan dalam penyusunan dokumen level selanjutnya.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('VI.1', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun, menetapkan, dan mendokumentasikan manual SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian, dengan kondisi:

manual SMKP telah mencakup ruang lingkup SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian, prosedur terdokumentasi yang ditetapkan untuk SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian, dan uraian dari interaksi antara elemen-elemen dalam SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian dan acuan dokumen dari elemen terkait;

manual SMKP telah disahkan oleh manajemen Pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, dan IUJP;

manual SMKP telah disosialisasikan kepada seluruh departemen/bagian dari Pekerja; dan

manual SMKP belum secara konsisten digunakan dalam penyusunan dokumen level selanjutnya.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('VI.1', '4', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun, menetapkan, dan mendokumentasikan manual SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian, dengan kondisi:

manual SMKP telah mencakup ruang lingkup SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian, prosedur terdokumentasi yang ditetapkan untuk SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian, dan uraian dari interaksi antara elemen-elemen dalam SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian dan acuan dokumen dari elemen terkait;

manual SMKP telah disahkan oleh manajemen Pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, dan IUJP;

manual SMKP telah disosialisasikan kepada seluruh departemen/bagian dari Pekerja; dan

manual SMKP telah secara konsisten digunakan dalam penyusunan dokumen level selanjutnya.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('VI.2', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun, menetapkan, menerapkan, dan mendokumentasikan prosedur pengendalian dokumen Keselamatan Pertambangan oleh personel yang ditunjuk oleh Pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, dan IUJP.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('VI.2', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun, menetapkan, dan mendokumentasikan prosedur pengendalian dokumen Keselamatan Pertambangan, dengan kondisi:

prosedur pengendalian dokumen belum memadai; dan

prosedur pengendalian dokumen belum diterapkan secara konsisten oleh personel yang ditunjuk oleh Pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, dan IUJP..');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('VI.2', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun, menetapkan, dan mendokumentasikan prosedur pengendalian dokumen Keselamatan Pertambangan, dengan kondisi:

prosedur pengendalian dokumen telah meliputi persetujuan pengeluaran/penerbitan dan pengendalian dokumen, perubahan dan modifikasi dokumen, dan identifikasi dan pengelolaan dokumen yang berasal dari luar yang terkait; dan

prosedur pengendalian dokumen belum diterapkan secara konsisten oleh personel yang ditunjuk oleh Pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, dan IUJP.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('VI.2', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun, menetapkan, dan mendokumentasikan prosedur pengendalian dokumen Keselamatan Pertambangan, dengan kondisi:

prosedur pengendalian dokumen telah meliputi persetujuan pengeluaran/penerbitan dan pengendalian dokumen, perubahan dan modifikasi dokumen, dan identifikasi dan pengelolaan dokumen yang berasal dari luar yang terkait; dan

prosedur pengendalian dokumen telah diterapkan secara konsisten oleh personel yang ditunjuk oleh Pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, dan IUJP.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('VI.3', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun, menetapkan, menerapkan, dan mendokumentasikan prosedur pengendalian rekaman Keselamatan Pertambangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('VI.3', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun, menetapkan, dan mendokumentasikan prosedur pengendalian rekaman Keselamatan Pertambangan, dengan kondisi:

prosedur pengendalian rekaman belum memadai;

prosedur pengendalian rekaman belum diterapkan secara konsisten; dan

rekaman Keselamatan Pertambangan belum didokumentasikan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('VI.3', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun, menetapkan, dan mendokumentasikan prosedur pengendalian rekaman Keselamatan Pertambangan, dengan kondisi:

prosedur pengendalian rekaman telah meliputi proses identifikasi, penyimpanan, perlindungan, akses, penentuan masa simpan, dan pemusnahan rekaman;

prosedur pengendalian rekaman belum diterapkan secara konsisten; dan

rekaman Keselamatan Pertambangan belum didokumentasikan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('VI.3', '3', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menyusun, menetapkan, dan mendokumentasikan prosedur pengendalian rekaman Keselamatan Pertambangan, dengan kondisi:

prosedur pengendalian rekaman telah meliputi proses identifikasi, penyimpanan, perlindungan, akses, penentuan masa simpan, dan pemusnahan rekaman;

prosedur pengendalian rekaman telah diterapkan secara konsisten; dan

rekaman Keselamatan Pertambangan telah didokumentasikan dengan konsisten sehingga tetap dapat dibaca, diidentifikasi, dan ditelusuri.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('VI.4', '0', 'Tidak ada bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan jenis dokumen dan rekaman sesuai dengan elemen-elemen SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('VI.4', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan jenis dokumen dan rekaman, namun belum mencakup seluruh elemen-elemen SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('VI.4', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menetapkan jenis dokumen dan rekaman yang telah mencakup seluruh elemen-elemen SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('VII.1', '0', 'diberikan apabila tidak ada bukti yang menunjukkan bahwa manajemen tertinggi perusahaan telah melakukan tinjauan manajemen terhadap penerapan SMKP secara terencana dan berkala minimal 1 (satu) tahun sekali.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('VII.1', '1', 'Terdapat bukti yang menunjukkan manajemen pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan tinjauan manajemen terhadap penerapan SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian, dengan kondisi:

tidak dilakukan oleh pimpinan tertinggi; dan

tidak dilakukan secara terencana dan berkala.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('VII.1', '2', 'Terdapat bukti yang menunjukkan manajemen pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan tinjauan manajemen terhadap penerapan SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian, dengan kondisi:

tidak dilakukan oleh pimpinan tertinggi; dan

dilakukan secara terencana dan berkala.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('VII.1', '3', 'Terdapat bukti yang menunjukkan manajemen pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan tinjauan manajemen terhadap penerapan SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian, dengan kondisi:

dilakukan oleh pimpinan tertinggi; dan

tidak dilakukan secara terencana dan berkala.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('VII.1', '4', 'Terdapat bukti yang menunjukkan manajemen pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melakukan tinjauan manajemen terhadap penerapan SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian, dengan kondisi:

dilakukan oleh pimpinan tertinggi; dan

dilakukan secara terencana dan berkala paling sedikit 1 (satu) tahun sekali dan hasilnya didokumentasikan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('VII.2', '0', 'diberikan apabila tidak ada bukti yang menunjukkan bahwa perusahaan telah mendokumentasikan catatan hasil tinjauan manajemen yang dilakukan oleh manajemen tertinggi.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('VII.2', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah mendokumentasikan catatan hasil tinjauan manajemen, namun masukan tinjauan manajemen berdasarkan catatan tersebut belum memadai.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('VII.2', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah mendokumentasikan catatan hasil tinjauan manajemen, dan masukan tinjauan manajemen berdasarkan catatan tersebut telah memadai (mencakup masukan mengenai kebijakan Keselamatan Pertambangan, hasil audit penerapan SMKP Minerba, daftar risiko, hasil evaluasi kepatuhan terhadap ketentuan peraturan perundang-undangan dan persyaratan lainnya yang terkait, tindak lanjut terhadap tinjauan manajemen sebelumnya, hasil dari partisipasi dan konsultasi, komunikasi yang berhubungan dengan pihak eksternal terkait, termasuk keluhan-keluhan, tingkat pencapaian kinerja Keselamatan Pertambangan termasuk tujuan, sasaran, dan program, status penyelidikan kecelakaan, Kejadian Berbahaya, kejadian akibat tenaga kerja, dan Penyakit Akibat Kerja, tindakan perbaikan, dan pencegahan, perubahan yang terjadi, termasuk peraturan perundang-undangan dan struktur organisasi Keselamatan Pertambangan, dan rekomendasi peningkatan Keselamatan Pertambangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('VII.3', '0', 'diberikan apabila tidak ada bukti yang menunjukkan bahwa perusahaan telah mendokumentasikan catatan hasil tinjauan manajemen yang dilakukan oleh manajemen tertinggi.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('VII.3', '1', 'Terdapat bukti yang menunjukkan keluaran dari tinjauan manajemen Keselamatan Pertambangan telah menghasilkan keputusan dan tindakan yang berhubungan dengan efektifitas sistem manajemen dan kegiatan/prosesnya, peningkatan kinerja Keselamatan Pertambangan, namun belum sepenuhnya mempertimbangkan kebijakan Keselamatan Pertambangan, kinerja Keselamatan Pertambangan, sumber daya, dan elemen-elemen lain SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('VII.3', '2', 'Terdapat bukti yang menunjukkan keluaran dari tinjauan manajemen Keselamatan Pertambangan telah menghasilkan keputusan dan tindakan yang berhubungan dengan efektifitas sistem manajemen dan kegiatan/prosesnya, peningkatan kinerja Keselamatan Pertambangan, dan telah sepenuhnya mempertimbangkan kebijakan Keselamatan Pertambangan, kinerja Keselamatan Pertambangan, sumber daya, dan elemen-elemen lain SMKP Minerba atau SMKP khusus pada Pengolahan dan/atau Pemurnian.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('VII.4', '0', 'diberikan apabila tidak ada bukti yang menunjukkan bahwa perusahaan telah melakukan pencatatan, pendokumentasian, dan pelaporan hasil tinjauan manajemen kepada pihak yang berkepentingan, dan hasilnya belum dikomunikasikan kepada pihak-pihak yang memerlukannya.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('VII.4', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah dilakukan pencatatan, pendokumentasian, pelaporan hasil tinjauan manajemen kepada pihak-pihak yang berkepentingan, namun belum dilakukan komunikasi kepada yang memerlukannya.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('VII.4', '2', 'Terdapat bukti yang menunjukkan telah dilakukan pencatatan, pendokumentasian, pelaporan hasil tinjauan manajemen kepada pihak-pihak yang berkepentingan dan telah dilakukan komunikasi kepada yang memerlukannya.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('VII.5', '0', 'Tidak ada bukti yang menunjukkan perusahaan telah melaksanakan peningkatan kinerja dalam hal terjadi perubahan peraturan perundang-undangan, adanya tuntutan dari pemangku kepentingan, adanya perubahan bisnis pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP, terjadi perubahan struktur organisasi pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP, adanya perkembangan pemanfaatan teknologi, kemampuan rekayasa, rancang bangun, pengembangan, dan penerapan teknologi Pertambangan, adanya hasil kajian kecelakaan, Kejadian Berbahaya, kejadian akibat penyakit tenaga kerja, dan/atau Penyakit Akibat Kerja di tempat kerja, adanya pelaporan; dan/atau adanya masukan dari pekerja.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('VII.5', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah melaksanakan peningkatan kinerja dalam hal terjadi perubahan peraturan perundang-undangan, adanya tuntutan dari pemangku kepentingan, adanya perubahan bisnis pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, dan IUJP, terjadi perubahan struktur organisasi pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP, adanya perkembangan pemanfaatan teknologi, kemampuan rekayasa, rancang bangun, pengembangan, dan penerapan teknologi Pertambangan, adanya hasil kajian kecelakaan, Kejadian Berbahaya, kejadian akibat penyakit tenaga kerja, dan/atau Penyakit Akibat Kerja di tempat kerja, adanya pelaporan; dan/atau adanya masukan dari pekerja.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('VII.6', '0', 'diberikan apabila tidak ada bukti yang menunjukkan bahwa perusahaan menggunakan tinjauan hasil dari tindak lanjut perbaikan (tindakan korektif) sebagai dasar penting dalam penentuan kebijakan atas proses peningkatan kinerja Keselamatan Pertambangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('VII.6', '1', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menggunakan sebagian dari tinjauan hasil dari tindak lanjut perbaikan sebagai dasar dalam penentuan kebijakan atas proses peningkatan kinerja Keselamatan Pertambangan.');
INSERT INTO tb_kriteria_penilaian (kode_item_audit, skor, deskripsi) VALUES ('VII.6', '2', 'Terdapat bukti yang menunjukkan pemegang IUP, IUPK, IUP Operasi Produksi khusus untuk Pengolahan dan/atau Pemurnian, IPR, atau IUJP telah menggunakan seluruh dari tinjauan hasil dari tindak lanjut perbaikan sebagai dasar dalam penentuan kebijakan atas proses peningkatan kinerja Keselamatan Pertambangan.');