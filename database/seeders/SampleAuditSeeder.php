<?php

namespace Database\Seeders;

use App\Models\AuditDetail;
use App\Models\AuditSesi;
use App\Models\Departemen;
use App\Models\Kriteria;
use App\Models\Perusahaan;
use App\Models\Pica;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SampleAuditSeeder extends Seeder
{
    /**
     * Run the database seeds for 10 realistic audit sessions and PICA entries.
     */
    public function run(): void
    {
        $auditor = User::where('role', 'auditor')->first() ?? User::first();
        if (!$auditor) {
            return;
        }

        $kriterias = Kriteria::all();
        if ($kriterias->isEmpty()) {
            return;
        }

        $perusahaans = Perusahaan::all();
        $departemens = Departemen::all();

        // Clean existing sessions & picas to prevent duplicates
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Pica::truncate();
        AuditDetail::truncate();
        AuditSesi::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 10 Realistic Audit Session Definitions
        $sessionsData = [
            [
                'area_audit' => 'Area Tambang Utama Pit West & Haulage Road',
                'perusahaan_nama' => 'PT. Meares Soputan Mining',
                'departemen_nama' => 'Departemen Mining',
                'tanggal_mulai' => now()->subDays(5)->toDateString(),
                'tanggal_selesai' => now()->subDays(2)->toDateString(),
                'status' => 'berjalan',
                'findings' => [
                    0 => ['nilai' => 2, 'catatan' => 'Kebijakan KP belum dievaluasi secara berkala dalam 12 bulan terakhir.', 'pica' => ['status' => 'in_progress', 'kategori' => 'minor', 'akar' => 'Jadwal tinjauan tahunan terlewat karena perubahan struktur organisasi.', 'koreksi' => 'Melaksanakan rapat evaluasi Kebijakan KP bersama KTT.', 'pencegahan' => 'Menetapkan kalender pengingat otomatis di portal HSE.', 'tenggat' => now()->addDays(10)->toDateString()]],
                    3 => ['nilai' => 3, 'catatan' => 'Dokumen IBPR lereng barat belum mencakup analisis risiko longsoran musim hujan.', 'pica' => ['status' => 'closed', 'kategori' => 'minor', 'akar' => 'Kajian geoteknik lereng barat baru selesai minggu lalu.', 'koreksi' => 'Revisi dokumen IBPR lereng barat revisi 02.', 'pencegahan' => 'SOP Wajib pemutakhiran IBPR setiap ada laporan geoteknik baru.', 'tenggat' => now()->subDay()->toDateString(), 'verifikasi' => 'Telah diverifikasi fisik: Dokumen IBPR revisi 02 disahkan KTT.']],
                    7 => ['nilai' => 0, 'catatan' => 'Rambu peringatan di area penimbunan bahan peledak sementara rusak fisik.', 'pica' => ['status' => 'open', 'kategori' => 'mayor', 'akar' => null]],
                ]
            ],
            [
                'area_audit' => 'Processing Plant & Heavy Equipment Workshop',
                'perusahaan_nama' => 'PT. Macmahon Indonesia',
                'departemen_nama' => 'Departemen Maintenance',
                'tanggal_mulai' => now()->subDays(10)->toDateString(),
                'tanggal_selesai' => now()->subDays(5)->toDateString(),
                'status' => 'selesai',
                'findings' => [
                    1 => ['nilai' => 3, 'catatan' => 'Pemeriksaan harian P2H alat berat terlambat diarsip digital.', 'pica' => ['status' => 'closed', 'kategori' => 'minor', 'akar' => 'Form P2H fisik terlambat di-input admin shift malam.', 'koreksi' => 'Penerapan aplikasi P2H tablet di workshop.', 'pencegahan' => 'Pengadaan 5 unit rugged tablet mekanik.', 'tenggat' => now()->subDays(3)->toDateString(), 'verifikasi' => 'Sistem P2H digital tablet telah beroperasi 100%.']],
                    5 => ['nilai' => 2, 'catatan' => 'Penampung ceceran oli (oil trap) workshop tersumbat lumpur.', 'pica' => ['status' => 'in_progress', 'kategori' => 'minor', 'akar' => 'Pembersihan rutin bulanan terlewat saat hujan deras.', 'koreksi' => 'Pembersihan dan pengerukan sedimentasi oil trap.', 'pencegahan' => 'Penambahan strainer pemisah sedimentasi.', 'tenggat' => now()->addDays(5)->toDateString()]],
                ]
            ],
            [
                'area_audit' => 'Area Pelabuhan (Port & Coal Terminal Jetty 1)',
                'perusahaan_nama' => 'PT. Samudera Mulia Abadi',
                'departemen_nama' => 'Departemen Commercial',
                'tanggal_mulai' => now()->subDays(12)->toDateString(),
                'tanggal_selesai' => now()->subDays(8)->toDateString(),
                'status' => 'selesai',
                'findings' => [
                    2 => ['nilai' => 2, 'catatan' => 'Pelampung keselamatan (life buoy) di dermaga 1 kurang 2 unit.', 'pica' => ['status' => 'closed', 'kategori' => 'minor', 'akar' => 'Pelampung lama rusak dan belum diganti stok baru.', 'koreksi' => 'Memasang 2 unit life buoy baru standar SOLAS.', 'pencegahan' => 'Inspeksi mingguan sarana tanggap darurat perairan.', 'tenggat' => now()->subDays(4)->toDateString(), 'verifikasi' => 'Life buoy baru telah terpasang dan siap pakai.']],
                ]
            ],
            [
                'area_audit' => 'Area Gudang Bahan Peledak & Handak Central',
                'perusahaan_nama' => 'PT. Hanwha Mining Services Indonesia',
                'departemen_nama' => 'Departemen HSE & Formalities',
                'tanggal_mulai' => now()->subDays(16)->toDateString(),
                'tanggal_selesai' => now()->subDays(12)->toDateString(),
                'status' => 'berjalan',
                'findings' => [
                    4 => ['nilai' => 0, 'catatan' => 'Sertifikat kalibrasi termohigrometer gudang handak telah kadaluarsa.', 'pica' => ['status' => 'in_progress', 'kategori' => 'mayor', 'akar' => 'Keterlambatan proses kalibrasi di laboratorium akreditasi.', 'koreksi' => 'Pengiriman alat ke lab terakreditasi KAN.', 'pencegahan' => 'Penyediaan 1 unit termohigrometer cadangan terkalibrasi.', 'tenggat' => now()->addDays(7)->toDateString()]],
                    6 => ['nilai' => 2, 'catatan' => 'Daftar piket pengamanan gudang handak belum diperbarui bulan ini.', 'pica' => ['status' => 'open', 'kategori' => 'minor', 'akar' => null]],
                ]
            ],
            [
                'area_audit' => 'Area Power Plant & Substation 33kV',
                'perusahaan_nama' => 'PT. Tambang Tondano Nusajaya',
                'departemen_nama' => 'Departemen Process Plant',
                'tanggal_mulai' => now()->subDays(18)->toDateString(),
                'tanggal_selesai' => now()->subDays(15)->toDateString(),
                'status' => 'draft',
                'findings' => []
            ],
            [
                'area_audit' => 'Area Mess Karyawan & Central Kitchen',
                'perusahaan_nama' => 'PT. Tata Wisata',
                'departemen_nama' => 'Departemen HCCS',
                'tanggal_mulai' => now()->subDays(22)->toDateString(),
                'tanggal_selesai' => now()->subDays(18)->toDateString(),
                'status' => 'selesai',
                'findings' => [
                    8 => ['nilai' => 3, 'catatan' => 'Sertifikat laik higiene sanitasi dapur belum diperpanjang.', 'pica' => ['status' => 'closed', 'kategori' => 'minor', 'akar' => 'Pengurusan dokumen terhambat jadwal inspeksi dinkes.', 'koreksi' => 'Penyelesaian sertifikasi higiene dengan Dinkes.', 'pencegahan' => 'Pengajuan perpanjangan 3 bulan sebelum masa berlaku habis.', 'tenggat' => now()->subDays(5)->toDateString(), 'verifikasi' => 'Sertifikat Laik Higiene Sanitasi baru telah diterbitkan Dinkes.']],
                ]
            ],
            [
                'area_audit' => 'Area Tangki Timbun Bahan Bakar (Fuel Farm 500kL)',
                'perusahaan_nama' => 'PT. AKR',
                'departemen_nama' => 'Departemen Supply Chain',
                'tanggal_mulai' => now()->subDays(25)->toDateString(),
                'tanggal_selesai' => now()->subDays(21)->toDateString(),
                'status' => 'berjalan',
                'findings' => [
                    1 => ['nilai' => 2, 'catatan' => 'Grounding strap pada dispenser fuel truck longgar.', 'pica' => ['status' => 'in_progress', 'kategori' => 'minor', 'akar' => 'Klem pengikat aus akibat gesekan operasional harian.', 'koreksi' => 'Penggantian klem grounding tembaga baru.', 'pencegahan' => 'Inspeksi kontinuitas grounding bulanan.', 'tenggat' => now()->addDays(3)->toDateString()]],
                ]
            ],
            [
                'area_audit' => 'Area Laboratorium K3 & Pengujian Lingkungan',
                'perusahaan_nama' => 'PT. Intertek',
                'departemen_nama' => 'Departemen Environmental',
                'tanggal_mulai' => now()->subDays(28)->toDateString(),
                'tanggal_selesai' => now()->subDays(25)->toDateString(),
                'status' => 'selesai',
                'findings' => [
                    3 => ['nilai' => 3, 'catatan' => 'Laporan Pemantauan Kualitas Udara Ambien terlambat disahkan KTT.', 'pica' => ['status' => 'closed', 'kategori' => 'minor', 'akar' => 'KTT berada di luar site saat laporan selesai disusun.', 'koreksi' => 'Penandatanganan digital laporan KTT.', 'pencegahan' => 'Penerapan e-signature untuk dokumen K3PL.', 'tenggat' => now()->subDays(10)->toDateString(), 'verifikasi' => 'Laporan lengkap dengan tanda tangan digital KTT telah diunggah.']],
                ]
            ],
            [
                'area_audit' => 'Area Pit East & Disposal Dumping Area',
                'perusahaan_nama' => 'PT Batu Biru Nusantara',
                'departemen_nama' => 'Departemen Mining Tech Service',
                'tanggal_mulai' => now()->subDays(32)->toDateString(),
                'tanggal_selesai' => now()->subDays(28)->toDateString(),
                'status' => 'berjalan',
                'findings' => [
                    0 => ['nilai' => 1, 'catatan' => 'Tinggit berm di sepanjang haulroad disposal kurang dari 3/4 diameter roda terbesar.', 'pica' => ['status' => 'open', 'kategori' => 'mayor', 'akar' => null]],
                    5 => ['nilai' => 2, 'catatan' => 'Lampu penerangan tower malam di dumping area 2 redup.', 'pica' => ['status' => 'in_progress', 'kategori' => 'minor', 'akar' => 'Baterai solar cell tower penerangan mengalami penurunan daya.', 'koreksi' => 'Penggantian modul baterai solar cell baru.', 'pencegahan' => 'Jadwal pembersihan panel surya mingguan.', 'tenggat' => now()->addDays(6)->toDateString()]],
                ]
            ],
            [
                'area_audit' => 'Area Kantor Administrasi Utama & Klinik K3',
                'perusahaan_nama' => 'Siloam Hospital',
                'departemen_nama' => 'Departemen OHS',
                'tanggal_mulai' => now()->subDays(35)->toDateString(),
                'tanggal_selesai' => now()->subDays(30)->toDateString(),
                'status' => 'selesai',
                'findings' => [
                    2 => ['nilai' => 3, 'catatan' => 'Jadwal simulasi tanggap darurat kebakaran gedung admin terlewat 1 triwulan.', 'pica' => ['status' => 'closed', 'kategori' => 'minor', 'akar' => 'Bentrok jadwal dengan audit eksternal ESDM.', 'koreksi' => 'Pelaksanaan simulasi evakuasi gedung admin.', 'pencegahan' => 'Penetapan jadwal simulasi di awal tahun fiskal.', 'tenggat' => now()->subDays(15)->toDateString(), 'verifikasi' => 'Simulasi evakuasi kebakaran gedung admin telah sukses dilaksanakan.']],
                ]
            ],
        ];

        foreach ($sessionsData as $data) {
            $perusahaanObj = Perusahaan::where('nama_perusahaan', $data['perusahaan_nama'])->first();
            $perusahaanId = $perusahaanObj ? $perusahaanObj->id : null;

            $departemenObj = Departemen::where('nama_departemen', $data['departemen_nama'])->first();
            $departemenId = $departemenObj ? $departemenObj->id : null;

            $sesi = AuditSesi::create([
                'user_id' => $auditor->id,
                'perusahaan_id' => $perusahaanId,
                'departemen_id' => $departemenId,
                'tanggal_mulai' => $data['tanggal_mulai'],
                'tanggal_selesai' => $data['tanggal_selesai'],
                'area_audit' => $data['area_audit'],
                'status' => $data['status'],
                'skor_akhir' => 0.00,
            ]);

            foreach ($kriterias as $index => $kriteria) {
                $max = (int) $kriteria->nilai_maksimal;
                $nilai = $max;
                $catatan = null;
                $isNa = false;

                if (isset($data['findings'][$index])) {
                    $finding = $data['findings'][$index];
                    $nilai = (int) $finding['nilai'];
                    $catatan = $finding['catatan'];
                }

                $detail = AuditDetail::create([
                    'audit_sesi_id' => $sesi->id,
                    'kriteria_id' => $kriteria->id,
                    'nilai' => $nilai,
                    'is_na' => $isNa,
                    'catatan' => $catatan,
                ]);

                if (isset($data['findings'][$index]['pica'])) {
                    $picaInfo = $data['findings'][$index]['pica'];

                    Pica::create([
                        'audit_detail_id' => $detail->id,
                        'deskripsi_temuan' => $catatan,
                        'kategori_temuan' => $picaInfo['kategori'] ?? 'minor',
                        'akar_masalah' => $picaInfo['akar'] ?? null,
                        'tindakan_koreksi' => $picaInfo['koreksi'] ?? null,
                        'tindakan_pencegahan' => $picaInfo['pencegahan'] ?? null,
                        'tenggat_waktu' => $picaInfo['tenggat'] ?? null,
                        'status' => $picaInfo['status'],
                        'catatan_verifikasi_auditor' => $picaInfo['verifikasi'] ?? null,
                    ]);
                }
            }

            $sesi->hitungSkorAkhir();
        }
    }
}
