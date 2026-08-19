<?php

namespace Tests\Feature;

use App\Models\AuditDetail;
use App\Models\AuditSesi;
use App\Models\Elemen;
use App\Models\Kriteria;
use App\Models\Pica;
use App\Models\SubElemen;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditKategoriTemuanTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $auditor;
    protected AuditSesi $sesi;
    protected Elemen $elemen;
    protected SubElemen $subElemen;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Admin & Auditor users
        $this->admin = User::factory()->create([
            'role'      => 'admin',
            'username'  => 'admin_tester',
            'email'     => 'admin@test.com',
            'is_active' => true,
        ]);

        $this->auditor = User::factory()->create([
            'role'      => 'auditor',
            'username'  => 'auditor_tester',
            'email'     => 'auditor@test.com',
            'area'      => 'Tambang Utama',
            'is_active' => true,
        ]);

        // Create Elemen & SubElemen
        $this->elemen = Elemen::create([
            'kode_elemen' => 'IV',
            'nama_elemen' => 'Implementasi',
            'bobot' => 20.00,
        ]);

        $this->subElemen = SubElemen::create([
            'elemen_id' => $this->elemen->id,
            'kode_sub' => 'IV.4',
            'nama_sub' => 'Pengelolaan Lingkungan Kerja',
        ]);

        // Create Audit Session
        $this->sesi = AuditSesi::create([
            'user_id' => $this->auditor->id,
            'area_audit' => 'Tambang Utama',
            'tanggal_mulai' => now()->subDays(5),
            'tanggal_selesai' => now(),
            'status' => 'berjalan',
        ]);
    }

    /**
     * Test 1: Sub elemen IV.4 dengan nilai 7 dari 20 (35% < 50%) -> Kategori MAYOR.
     */
    public function test_sub_elemen_kurang_dari_50_persen_dikategorikan_mayor()
    {
        // Sub elemen IV.4 punya 2 kriteria @ 10 poin = total 20 poin
        $k1 = Kriteria::create(['sub_elemen_id' => $this->subElemen->id, 'kode_kriteria' => 'IV.4.1', 'deskripsi' => 'K1', 'nilai_maksimal' => 10]);
        $k2 = Kriteria::create(['sub_elemen_id' => $this->subElemen->id, 'kode_kriteria' => 'IV.4.2', 'deskripsi' => 'K2', 'nilai_maksimal' => 10]);

        // Diberi nilai: K1 = 7, K2 = 0 (Total 7 / 20 = 35%)
        $d1 = AuditDetail::create(['audit_sesi_id' => $this->sesi->id, 'kriteria_id' => $k1->id, 'nilai' => 7, 'is_na' => false]);
        $d2 = AuditDetail::create(['audit_sesi_id' => $this->sesi->id, 'kriteria_id' => $k2->id, 'nilai' => 0, 'is_na' => false]);

        $response = $this->actingAs($this->admin)->post(route('admin.audit-sesi.matrix.update', $this->sesi->id), [
            'details' => [
                $d1->id => ['nilai' => 7, 'is_na' => 0, 'catatan' => 'Temuan K1'],
                $d2->id => ['nilai' => 0, 'is_na' => 0, 'catatan' => 'Temuan K2'],
            ]
        ]);

        $response->assertRedirect();

        // Keduanya < max -> terbuat PICA
        $picas = Pica::whereHas('auditDetail', fn($q) => $q->where('audit_sesi_id', $this->sesi->id))->get();
        $this->assertCount(2, $picas);
        foreach ($picas as $pica) {
            $this->assertEquals('mayor', $pica->kategori_temuan, 'Temuan dengan persentase 35% (<50%) harus berkategori Mayor');
            $this->assertFalse($pica->kategori_ditetapkan_manual);
        }
    }

    /**
     * Test 2: Sub elemen IV.4 dengan nilai tepat 10 dari 20 (50.0%) -> Kategori MINOR (Bukan Mayor!).
     * Strict less than verification (< 0.5).
     */
    public function test_sub_elemen_tepat_50_persen_dikategorikan_minor_bukan_mayor()
    {
        $k1 = Kriteria::create(['sub_elemen_id' => $this->subElemen->id, 'kode_kriteria' => 'IV.4.1', 'deskripsi' => 'K1', 'nilai_maksimal' => 10]);
        $k2 = Kriteria::create(['sub_elemen_id' => $this->subElemen->id, 'kode_kriteria' => 'IV.4.2', 'deskripsi' => 'K2', 'nilai_maksimal' => 10]);

        // Diberi nilai: K1 = 10 (pass), K2 = 0 (Total 10 / 20 = 50.0%)
        $d1 = AuditDetail::create(['audit_sesi_id' => $this->sesi->id, 'kriteria_id' => $k1->id, 'nilai' => 10, 'is_na' => false]);
        $d2 = AuditDetail::create(['audit_sesi_id' => $this->sesi->id, 'kriteria_id' => $k2->id, 'nilai' => 0, 'is_na' => false]);

        $response = $this->actingAs($this->admin)->post(route('admin.audit-sesi.matrix.update', $this->sesi->id), [
            'details' => [
                $d1->id => ['nilai' => 10, 'is_na' => 0, 'catatan' => 'Sesuai'],
                $d2->id => ['nilai' => 0, 'is_na' => 0, 'catatan' => 'Temuan K2'],
            ]
        ]);

        $response->assertRedirect();

        // PICA hanya terbuat untuk d2 (nilai 0 < 10)
        $pica = Pica::where('audit_detail_id', $d2->id)->first();
        $this->assertNotNull($pica);
        $this->assertEquals('minor', $pica->kategori_temuan, 'Temuan dengan persentase 50.0% (persis 50%) HARUS Minor, BUKAN Mayor!');
    }

    /**
     * Test 3: Sub elemen VI dengan nilai 7 dari 12 (58.3% >= 50%) -> Kategori MINOR.
     */
    public function test_sub_elemen_lebih_dari_50_persen_dikategorikan_minor()
    {
        $k1 = Kriteria::create(['sub_elemen_id' => $this->subElemen->id, 'kode_kriteria' => 'VI.1', 'deskripsi' => 'K1', 'nilai_maksimal' => 6]);
        $k2 = Kriteria::create(['sub_elemen_id' => $this->subElemen->id, 'kode_kriteria' => 'VI.2', 'deskripsi' => 'K2', 'nilai_maksimal' => 6]);

        // Nilai: K1 = 4, K2 = 3 (Total 7 / 12 = 58.3%)
        $d1 = AuditDetail::create(['audit_sesi_id' => $this->sesi->id, 'kriteria_id' => $k1->id, 'nilai' => 4, 'is_na' => false]);
        $d2 = AuditDetail::create(['audit_sesi_id' => $this->sesi->id, 'kriteria_id' => $k2->id, 'nilai' => 3, 'is_na' => false]);

        $this->actingAs($this->admin)->post(route('admin.audit-sesi.matrix.update', $this->sesi->id), [
            'details' => [
                $d1->id => ['nilai' => 4, 'is_na' => 0, 'catatan' => 'Temuan K1'],
                $d2->id => ['nilai' => 3, 'is_na' => 0, 'catatan' => 'Temuan K2'],
            ]
        ]);

        $picas = Pica::whereHas('auditDetail', fn($q) => $q->where('audit_sesi_id', $this->sesi->id))->get();
        $this->assertCount(2, $picas);
        foreach ($picas as $pica) {
            $this->assertEquals('minor', $pica->kategori_temuan, 'Temuan dengan persentase 58.3% harus Minor');
        }
    }

    /**
     * Test 4: Edge case jika seluruh kriteria N/A -> Kategori default MINOR (tanpa exception division by zero).
     */
    public function test_edge_case_seluruh_kriteria_na_dikategorikan_minor()
    {
        $k1 = Kriteria::create(['sub_elemen_id' => $this->subElemen->id, 'kode_kriteria' => 'IV.4.1', 'deskripsi' => 'K1', 'nilai_maksimal' => 10]);
        $d1 = AuditDetail::create(['audit_sesi_id' => $this->sesi->id, 'kriteria_id' => $k1->id, 'nilai' => 0, 'is_na' => true]);

        // Call method directly
        $kategori = $this->sesi->hitungKategoriMayorPath1($this->subElemen->id);
        $this->assertEquals('minor', $kategori);
    }

    /**
     * Test 5: Idempotensi & Proteksi Manual Override.
     * PICA yang kategori_ditetapkan_manual = true TIDAK ter-timpa saat updateMatrix dipanggil ulang.
     */
    public function test_manual_override_tidak_tertimpa_saat_update_matrix_ulang()
    {
        $k1 = Kriteria::create(['sub_elemen_id' => $this->subElemen->id, 'kode_kriteria' => 'IV.4.1', 'deskripsi' => 'K1', 'nilai_maksimal' => 10]);
        $d1 = AuditDetail::create(['audit_sesi_id' => $this->sesi->id, 'kriteria_id' => $k1->id, 'nilai' => 2, 'is_na' => false]);

        // Simpan matriks (harus otomatis Mayor karena 2/10 = 20%)
        $this->actingAs($this->admin)->post(route('admin.audit-sesi.matrix.update', $this->sesi->id), [
            'details' => [
                $d1->id => ['nilai' => 2, 'is_na' => 0, 'catatan' => 'Temuan K1'],
            ]
        ]);

        $pica = Pica::where('audit_detail_id', $d1->id)->first();
        $this->assertEquals('mayor', $pica->kategori_temuan);

        // Admin override manual menjadi KRITIKAL dengan justifikasi
        $pica->update([
            'kategori_temuan' => 'kritikal',
            'kategori_ditetapkan_manual' => true,
            'justifikasi_kategori' => 'Terlihat potensi bahaya fatal di area kerja',
        ]);

        // Simpan matriks ulang beberapa kali
        $this->actingAs($this->admin)->post(route('admin.audit-sesi.matrix.update', $this->sesi->id), [
            'details' => [
                $d1->id => ['nilai' => 2, 'is_na' => 0, 'catatan' => 'Temuan K1 updated'],
            ]
        ]);

        $picaFresh = $pica->fresh();
        $this->assertEquals('kritikal', $picaFresh->kategori_temuan, 'PICA manual override tidak boleh ter-timpa kembali ke otomatis!');
        $this->assertTrue($picaFresh->kategori_ditetapkan_manual);
        $this->assertEquals('Terlihat potensi bahaya fatal di area kerja', $picaFresh->justifikasi_kategori);
    }

    /**
     * Test 6: Validasi Admin PicaController — Justifikasi wajib diisi saat memilih kategori Kritikal.
     */
    public function test_justifikasi_kategori_wajib_saat_kategori_kritikal()
    {
        $k1 = Kriteria::create(['sub_elemen_id' => $this->subElemen->id, 'kode_kriteria' => 'IV.4.1', 'deskripsi' => 'K1', 'nilai_maksimal' => 10]);
        $d1 = AuditDetail::create(['audit_sesi_id' => $this->sesi->id, 'kriteria_id' => $k1->id, 'nilai' => 2, 'is_na' => false]);

        $pica = Pica::create([
            'audit_detail_id' => $d1->id,
            'deskripsi_temuan' => 'Temuan',
            'status' => 'open',
        ]);

        // Update tanpa justifikasi -> error validasi
        $response = $this->actingAs($this->admin)->put(route('admin.pica.update', $pica->id), [
            'kategori_temuan' => 'kritikal',
            'status' => 'open',
            'justifikasi_kategori' => '',
        ]);

        $response->assertSessionHasErrors('justifikasi_kategori');

        // Update dengan justifikasi -> berhasil
        $responseOk = $this->actingAs($this->admin)->put(route('admin.pica.update', $pica->id), [
            'kategori_temuan' => 'kritikal',
            'status' => 'open',
            'justifikasi_kategori' => 'Ada potensi bahaya fatality tinggi',
        ]);

        $responseOk->assertSessionHasNoErrors();
        $this->assertEquals('kritikal', $pica->fresh()->kategori_temuan);
        $this->assertTrue($pica->fresh()->kategori_ditetapkan_manual);
    }

    /**
     * Test 7: Field Security Auditor — Auditor (Auditee) TIDAK bisa mengubah kategori_temuan.
     */
    public function test_auditor_tidak_bisa_mengubah_kategori_temuan()
    {
        $k1 = Kriteria::create(['sub_elemen_id' => $this->subElemen->id, 'kode_kriteria' => 'IV.4.1', 'deskripsi' => 'K1', 'nilai_maksimal' => 10]);
        $d1 = AuditDetail::create(['audit_sesi_id' => $this->sesi->id, 'kriteria_id' => $k1->id, 'nilai' => 6, 'is_na' => false]);

        $pica = Pica::create([
            'audit_detail_id' => $d1->id,
            'deskripsi_temuan' => 'Temuan Minor',
            'kategori_temuan' => 'minor',
            'status' => 'open',
        ]);

        // Auditor mencoba kirim payload kategori_temuan = 'minor' -> 'kritikal'
        $response = $this->actingAs($this->auditor)->put(route('auditor.pica.update', $pica->id), [
            'akar_masalah' => 'Sudah dianalisis',
            'kategori_temuan' => 'kritikal', // Malicious attempt to lower/change severity
        ]);

        $response->assertRedirect();

        // Kategori temuan harus tetap 'minor'
        $this->assertEquals('minor', $pica->fresh()->kategori_temuan, 'Auditor/Auditee dilarang mengubah kategori_temuan PICA!');
    }
}
