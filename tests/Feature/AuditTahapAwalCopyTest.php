<?php

namespace Tests\Feature;

use App\Models\AuditSesi;
use App\Models\AuditTahapAwal;
use App\Models\Perusahaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditTahapAwalCopyTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Perusahaan $perusahaan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->perusahaan = Perusahaan::create([
            'nama_perusahaan' => 'PT Test Tambang',
            'kode_perusahaan' => 'PT TT',
            'jenis_perizinan' => 'IUP-001/2020',
            'jenis_komoditas' => 'Emas DMP',
            'alamat_kantor_site' => 'Site Winuri',
            'alamat_lokasi_tambang' => 'Minahasa Utara',
            'alamat_kantor_pusat' => 'Jakarta Selatan',
            'nama_ktt_default' => 'Budi Santoso',
            'is_active' => true,
        ]);
    }

    public function test_new_audit_session_starts_with_empty_stage1_except_company_data(): void
    {
        $sesi = AuditSesi::create([
            'user_id' => $this->admin->id,
            'perusahaan_id' => $this->perusahaan->id,
            'tahun_periode' => 2026,
            'tanggal_mulai' => '2026-01-10',
            'tanggal_selesai' => '2026-01-14',
            'area_audit' => $this->perusahaan->nama_perusahaan,
            'status' => 'berjalan',
            'skor_akhir' => 0,
        ]);

        $tahapAwal = AuditTahapAwal::createEmptyForSesi($sesi);

        // Data Perusahaan Auditi harus tetap terisi sesuai data perusahaan
        $this->assertSame('PT Test Tambang', $tahapAwal->nama_perusahaan_auditi);
        $this->assertSame('IUP-001/2020', $tahapAwal->jenis_perizinan);
        $this->assertSame('Emas DMP', $tahapAwal->jenis_komoditas);
        $this->assertSame('Site Winuri', $tahapAwal->alamat_kantor_site);
        $this->assertSame('Minahasa Utara', $tahapAwal->alamat_lokasi_tambang);
        $this->assertSame('Jakarta Selatan', $tahapAwal->alamat_kantor_pusat);
        $this->assertSame('1 Januari 2026 s/d 31 Desember 2026', $tahapAwal->periode_audit_label);

        // Semua form lainnya harus kosong
        $this->assertEmpty($tahapAwal->kinerja_kp);
        $this->assertEmpty($tahapAwal->faktor_penyesuaian);
        $this->assertNull($tahapAwal->jumlah_pekerja);
        $this->assertNull($tahapAwal->kelas_risiko);
        $this->assertEmpty($tahapAwal->tim_audit_tahap_1);
        $this->assertEmpty($tahapAwal->tim_audit_tahap_2);
        $this->assertEmpty($tahapAwal->kontak_awal);
        $this->assertEmpty($tahapAwal->kelayakan_audit);
        $this->assertEmpty($tahapAwal->kecukupan_dokumen);
        $this->assertEmpty($tahapAwal->top_risks_present);
        $this->assertEmpty($tahapAwal->top_risks_future);
        $this->assertEmpty($tahapAwal->trend_data);
        $this->assertEmpty($tahapAwal->unjuk_kerja_peralatan);
        $this->assertEmpty($tahapAwal->capaian_kli);
        $this->assertEmpty($tahapAwal->data_audit_sebelumnya);
        $this->assertEmpty($tahapAwal->harapan_pemangku);
        $this->assertNull($tahapAwal->nama_ktt);
        $this->assertNull($tahapAwal->ketua_tim_audit);
        $this->assertNull($tahapAwal->no_dokumen_header);
    }

    public function test_edit_page_provides_previous_period_copy_data_excluding_audit_team(): void
    {
        $prevSesi = AuditSesi::create([
            'user_id' => $this->admin->id,
            'perusahaan_id' => $this->perusahaan->id,
            'tahun_periode' => 2025,
            'tanggal_mulai' => '2026-01-05',
            'tanggal_selesai' => '2026-01-09',
            'area_audit' => $this->perusahaan->nama_perusahaan,
            'status' => 'selesai',
            'skor_akhir' => 82.5,
        ]);

        AuditTahapAwal::create([
            'audit_sesi_id' => $prevSesi->id,
            'no_dokumen_header' => 'TT-MGT-FRS-026L',
            'no_dokumen_footer' => 'TT-MGT-FRS-026K',
            'no_revisi' => '01',
            'nama_perusahaan_auditi' => $this->perusahaan->nama_perusahaan,
            'kinerja_kp' => ['fr' => '0.15', 'sr' => '1.25'],
            'jumlah_pekerja' => 1500,
            'kelas_risiko' => 'Tinggi',
            'no_surat_tugas' => '001/SK/2025',
            'tim_audit_tahap_1' => [
                ['nama' => 'Auditor Rahasia', 'jabatan' => 'Ketua Tim Audit', 'no_registrasi' => 'REG-001'],
            ],
            'tim_audit_tahap_2' => [
                ['nama' => 'Auditor Lapangan', 'jabatan' => 'Auditor', 'no_registrasi' => 'REG-002'],
            ],
            'kontak_awal' => ['media' => 'Meeting', 'hari_tanggal' => 'Senin, 5 Jan 2026'],
            'nama_ktt' => 'Budi Santoso',
        ]);

        $currSesi = AuditSesi::create([
            'user_id' => $this->admin->id,
            'perusahaan_id' => $this->perusahaan->id,
            'tahun_periode' => 2026,
            'tanggal_mulai' => '2027-01-05',
            'tanggal_selesai' => '2027-01-09',
            'area_audit' => $this->perusahaan->nama_perusahaan,
            'status' => 'berjalan',
            'skor_akhir' => 0,
        ]);
        AuditTahapAwal::createEmptyForSesi($currSesi);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.audit-sesi.tahap-awal', $currSesi->id));

        $response->assertOk();
        $response->assertViewHas('prevTahunLabel', '2025');

        $prevData = $response->viewData('prevTahapAwalData');
        $this->assertIsArray($prevData);
        $this->assertArrayHasKey('kinerja_kp', $prevData);
        $this->assertArrayHasKey('mandays', $prevData);
        $this->assertArrayHasKey('kecukupan_dokumen', $prevData);
        $this->assertArrayHasKey('metadata_dokumen', $prevData);

        // Pastikan daftar tim audit tidak disertakan dalam payload copy
        $this->assertArrayNotHasKey('tim_audit_tahap_1', $prevData);
        $this->assertArrayNotHasKey('tim_audit_tahap_2', $prevData);
        $this->assertArrayNotHasKey('no_surat_tugas', $prevData);
        $this->assertArrayNotHasKey('tanggal_surat_tugas', $prevData);

        // Pastikan nama tim audit periode sebelumnya tidak muncul di halaman baru
        $response->assertDontSee('Auditor Rahasia');
        $response->assertDontSee('Auditor Lapangan');
        $response->assertSee('data-copy-section="kinerja_kp"', false);
        $response->assertSee('data-copy-section="kecukupan_dokumen"', false);
        $response->assertSee('data-copy-section="top_risks_present"', false);
        $response->assertSee('data-copy-section="metadata_dokumen"', false);
        $response->assertDontSee('data-copy-section="tim_audit_tahap_1"', false);
        $response->assertDontSee('data-copy-section="tim_audit_tahap_2"', false);

        // Pastikan nama_ktt pada sesi baru tetap kosong saat halaman edit dibuka
        $fresh = AuditTahapAwal::where('audit_sesi_id', $currSesi->id)->first();
        $this->assertNull($fresh->nama_ktt);
    }

    public function test_edit_page_hides_copy_buttons_when_no_previous_period_exists(): void
    {
        $sesi = AuditSesi::create([
            'user_id' => $this->admin->id,
            'perusahaan_id' => $this->perusahaan->id,
            'tahun_periode' => 2023,
            'tanggal_mulai' => '2024-01-05',
            'tanggal_selesai' => '2024-01-09',
            'area_audit' => $this->perusahaan->nama_perusahaan,
            'status' => 'berjalan',
            'skor_akhir' => 0,
        ]);
        AuditTahapAwal::createEmptyForSesi($sesi);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.audit-sesi.tahap-awal', $sesi->id));

        $response->assertOk();
        $response->assertViewHas('prevTahapAwalData', null);
        $response->assertDontSee('data-copy-section="kinerja_kp"', false);
    }

    public function test_contractor_session_uses_dedicated_contractor_form_and_pdf_layout(): void
    {
        Perusahaan::create([
            'nama_perusahaan' => 'PT Meares Soputan Mining',
            'kode_perusahaan' => 'PT MSM',
            'kategori' => 'Pemegang IUP',
            'nama_ktt_default' => 'Tejo Prihantoro',
            'is_active' => true,
        ]);

        $kontraktor = Perusahaan::create([
            'nama_perusahaan' => 'PT. Intertek Utama Services',
            'kode_perusahaan' => 'INTERTEK',
            'kategori' => 'Kontraktor',
            'jenis_perizinan' => 'NIB 9120204321416',
            'jenis_komoditas' => 'Jasa Pengujian Laboratorium',
            'alamat_kantor_site' => 'C/O PT Meares Soputan Mining Toka Tindung Project',
            'nama_ktt_default' => 'Mus Hermawan',
            'is_active' => true,
        ]);

        $sesi = AuditSesi::create([
            'user_id' => $this->admin->id,
            'perusahaan_id' => $kontraktor->id,
            'tahun_periode' => 2025,
            'tanggal_mulai' => '2025-12-08',
            'tanggal_selesai' => '2025-12-10',
            'area_audit' => $kontraktor->nama_perusahaan,
            'status' => 'berjalan',
            'skor_akhir' => 0,
        ]);
        AuditTahapAwal::createEmptyForSesi($sesi);

        // 1. Cek halaman edit menampilkan form khusus kontraktor
        $editRes = $this->actingAs($this->admin)
            ->get(route('admin.audit-sesi.tahap-awal', $sesi->id));

        $editRes->assertOk();
        $editRes->assertViewHas('isKontraktor', true);
        $editRes->assertSee('Form Khusus Kontraktor (IUJP)');
        $editRes->assertSee('name="perusahaan_induk"', false);
        $editRes->assertSee('name="tanggal_surat_tugas_tahap_2"', false);
        $editRes->assertSee('name="nama_pjo"', false);
        $editRes->assertSee('value="Mus Hermawan"', false);
        $editRes->assertSee('<select name="nama_ktt"', false);
        $editRes->assertSee('Tejo Prihantoro — PT Meares Soputan Mining');
        $editRes->assertSee('Observer');
        $editRes->assertDontSee('Nilai Risiko Awal');
        $editRes->assertDontSee('Nilai Risiko Sisa');

        // 2. Simpan data khusus kontraktor
        $updateRes = $this->actingAs($this->admin)
            ->put(route('admin.audit-sesi.tahap-awal.update', $sesi->id), [
                'nama_perusahaan_auditi' => 'PT. Intertek Utama Services',
                'perusahaan_induk' => 'PT Meares Soputan Mining',
                'jenis_perizinan' => 'NIB 9120204321416',
                'jenis_komoditas' => 'Jasa Pengujian Laboratorium',
                'alamat_kantor_site' => 'C/O PT Meares Soputan Mining Toka Tindung Project',
                'periode_audit_label' => '10 Oktober 2024 – 9 Oktober 2025',
                'no_surat_tugas' => '0094/MSM/4/2022/I',
                'tanggal_surat_tugas' => '2022-04-20',
                'tanggal_surat_tugas_tahap_2' => '2022-04-29',
                'tim_audit_tahap_1' => [
                    ['nama' => 'Ronald Timbuleng', 'jabatan' => 'Ketua Tim Audit', 'no_registrasi' => '049/AUD-SMKP/37.04/DBT/2023'],
                ],
                'tim_audit_tahap_2' => [
                    ['nama' => 'Ronald Timbuleng', 'jabatan' => 'Ketua Tim Audit', 'no_registrasi' => '049/AUD-SMKP/37.04/DBT/2023'],
                    ['nama' => 'Fernando Wantania', 'jabatan' => 'Observer', 'no_registrasi' => ''],
                ],
                'top_risks_present' => [
                    ['kegiatan' => 'Chemical Handling', 'risiko' => 'Cacat tetap', 'nilai_awal' => 'Ekstrem'],
                ],
                'tanggal_berita_acara' => '2025-12-07',
                'ketua_tim_audit' => 'Ronald Timbuleng',
                'nama_pjo' => 'Mus Hermawan',
                'nama_ktt' => 'Tejo Prihantoro',
            ]);

        $updateRes->assertRedirect();
        $saved = AuditTahapAwal::where('audit_sesi_id', $sesi->id)->first();
        $this->assertSame('PT Meares Soputan Mining', $saved->perusahaan_induk);
        $this->assertSame('Mus Hermawan', $saved->nama_pjo);
        $this->assertSame('2022-04-29', $saved->tanggal_surat_tugas_tahap_2?->format('Y-m-d'));
        $this->assertSame('Observer', $saved->tim_audit_tahap_2[1]['jabatan'] ?? null);

        // 3. Cek halaman cetak PDF kontraktor
        $cetakRes = $this->actingAs($this->admin)
            ->get(route('admin.audit-sesi.tahap-awal.cetak', $sesi->id));

        $cetakRes->assertOk();
        $cetakRes->assertSee('untuk area PT Meares Soputan Mining');
        $cetakRes->assertSee('Penanggung Jawab Operasional dari Auditi (khusus untuk audit perusahaan jasa pertambangan)');
        $cetakRes->assertSee('Mus Hermawan');
        $cetakRes->assertSee('Tejo Prihantoro');
        $cetakRes->assertSee('Fernando Wantania');
        $cetakRes->assertSee('Observer');
    }
}
