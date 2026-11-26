<?php

namespace Tests\Feature;

use App\Models\AuditSesi;
use App\Models\AuditTahapAwal;
use App\Models\MasterFaktorPenyesuaian;
use App\Models\MasterHariKerjaAudit;
use App\Models\Perusahaan;
use App\Models\User;
use App\Services\HariKerjaAuditService;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class Fase1HariKerjaAuditTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $auditorSmkp;
    protected User $auditor;
    protected Perusahaan $perusahaan;
    protected AuditSesi $sesi;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->auditorSmkp = User::factory()->create(['role' => 'auditor_smkp', 'is_active' => true]);
        $this->auditor = User::factory()->create(['role' => 'auditor', 'area' => 'PT Tambang Uji', 'is_active' => true]);

        $this->perusahaan = Perusahaan::create([
            'nama_perusahaan' => 'PT Tambang Uji',
            'kode_perusahaan' => 'PT-TU',
            'kategori'        => 'Pemegang IUP',
            'jenis_usaha'     => 'tambang',
            'is_active'       => true,
        ]);

        $this->sesi = AuditSesi::create([
            'user_id'         => $this->admin->id,
            'perusahaan_id'   => $this->perusahaan->id,
            'tahun_periode'   => 2026,
            'tanggal_mulai'   => '2026-02-01',
            'tanggal_selesai' => '2026-02-05',
            'area_audit'      => $this->perusahaan->nama_perusahaan,
            'status'          => 'berjalan',
            'skor_akhir'      => 0,
        ]);
        AuditTahapAwal::createEmptyForSesi($this->sesi);
    }

    public function test_perusahaan_kelas_risiko_default_mapping(): void
    {
        $cases = [
            'tambang'                     => 'Tinggi',
            'pengolahan_pemurnian'        => 'Tinggi',
            'jasa_pertambangan_inti'      => 'Tinggi',
            'jasa_konsultasi_perencanaan' => 'Menengah',
            'jasa_non_inti'               => 'Rendah',
            null                          => null,
        ];

        foreach ($cases as $jenis => $expected) {
            $p = new Perusahaan(['jenis_usaha' => $jenis]);
            $this->assertSame($expected, $p->kelasRisikoDefault(), "Failed for jenis_usaha={$jenis}");
        }
    }

    public function test_service_calculates_matching_range_and_factors_with_unverified_warning(): void
    {
        $baris = MasterHariKerjaAudit::create([
            'jumlah_min'    => 101,
            'jumlah_max'    => 500,
            'hari_tinggi'   => 12.0,
            'hari_menengah' => 9.0,
            'hari_rendah'   => 6.0,
            'is_verified'   => false,
            'sumber'        => 'Uji',
        ]);

        $fTambah = MasterFaktorPenyesuaian::create([
            'kode'       => 'F_TAMBAH',
            'nama'       => 'Faktor Tambah',
            'tipe'       => 'tambah',
            'nilai_hari' => 2.5,
            'urutan'     => 1,
            'is_active'  => true,
        ]);
        $fKurang = MasterFaktorPenyesuaian::create([
            'kode'       => 'F_KURANG',
            'nama'       => 'Faktor Kurang',
            'tipe'       => 'kurang',
            'nilai_hari' => 1.0,
            'urutan'     => 2,
            'is_active'  => true,
        ]);

        $service = app(HariKerjaAuditService::class);
        $result = $service->hitung(250, 'Tinggi', [$fTambah->id, $fKurang->id]);

        $this->assertSame(12.0, $result['mandays_awal']);
        $this->assertSame(1.5, $result['faktor_hari']);
        $this->assertSame(13.5, $result['total_mandays']);
        $this->assertSame($baris->id, $result['baris_acuan_id']);
        $this->assertFalse($result['is_verified']);
        $this->assertNotNull($result['warning']);
    }

    public function test_service_rejects_unmatched_range_and_null_factor_value(): void
    {
        $service = app(HariKerjaAuditService::class);

        // No range in DB
        try {
            $service->hitung(250, 'Tinggi', []);
            $this->fail('Expected DomainException when no range matches.');
        } catch (DomainException $e) {
            $this->assertStringContainsString('Tidak ditemukan baris acuan', $e->getMessage());
        }

        // Range exists, factor has null nilai_hari
        MasterHariKerjaAudit::create([
            'jumlah_min'    => 1,
            'jumlah_max'    => null,
            'hari_tinggi'   => 10.0,
            'hari_menengah' => 8.0,
            'hari_rendah'   => 5.0,
            'is_verified'   => true,
        ]);
        $fNull = MasterFaktorPenyesuaian::create([
            'kode'       => 'F_NULL',
            'nama'       => 'Faktor Belum Dinilai',
            'tipe'       => 'tambah',
            'nilai_hari' => null,
            'urutan'     => 1,
            'is_active'  => true,
        ]);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('F_NULL');
        $service->hitung(250, 'Menengah', [$fNull->id]);
    }

    public function test_admin_crud_rejects_overlapping_ranges(): void
    {
        MasterHariKerjaAudit::create([
            'jumlah_min'    => 1,
            'jumlah_max'    => 100,
            'hari_tinggi'   => 5.0,
            'hari_menengah' => 4.0,
            'hari_rendah'   => 3.0,
            'is_verified'   => true,
        ]);

        $res = $this->actingAs($this->admin)->post(route('admin.master-hari-kerja.store'), [
            'jumlah_min'    => 50,
            'jumlah_max'    => 200,
            'hari_tinggi'   => 8.0,
            'hari_menengah' => 6.0,
            'hari_rendah'   => 4.0,
        ]);

        $res->assertRedirect();
        $res->assertSessionHas('error');
        $this->assertDatabaseCount('master_hari_kerja_audit', 1);
    }

    public function test_csv_import_validation_and_confirm_flow(): void
    {
        // 1. Header salah ditolak
        $badHeaderCsv = UploadedFile::fake()->createWithContent(
            'bad.csv',
            "min,max,tinggi,menengah,rendah,sumber\n1,50,5,4,3,Test\n"
        );
        $resBad = $this->actingAs($this->admin)
            ->post(route('admin.master-hari-kerja.import-preview'), ['csv_file' => $badHeaderCsv]);
        $resBad->assertSessionHas('error');

        // 2. Rentang tumpang tindih di CSV ditolak di pratinjau
        $overlapCsv = UploadedFile::fake()->createWithContent(
            'overlap.csv',
            "jumlah_min,jumlah_max,hari_tinggi,hari_menengah,hari_rendah,sumber\n1,100,5,4,3,A\n80,200,8,6,4,B\n"
        );
        $resOverlap = $this->actingAs($this->admin)
            ->post(route('admin.master-hari-kerja.import-preview'), ['csv_file' => $overlapCsv]);
        $resOverlap->assertOk();
        $resOverlap->assertViewHas('hasErrors', true);

        // 3. CSV valid (dengan baris kosong yang diabaikan) -> pratinjau -> konfirmasi (is_verified = false)
        $validCsv = UploadedFile::fake()->createWithContent(
            'valid.csv',
            "jumlah_min,jumlah_max,hari_tinggi,hari_menengah,hari_rendah,sumber\n\n1,100,5.0,4.0,3.0,Kepdirjen 185\n101,,10.0,8.0,6.0,Kepdirjen 185\n\n"
        );
        $resPreview = $this->actingAs($this->admin)
            ->post(route('admin.master-hari-kerja.import-preview'), ['csv_file' => $validCsv]);
        $resPreview->assertOk();
        $resPreview->assertViewHas('hasErrors', false);
        $resPreview->assertViewHas('validCount', 2);

        $resConfirm = $this->actingAs($this->admin)
            ->post(route('admin.master-hari-kerja.import-confirm'));
        $resConfirm->assertRedirect(route('admin.master-hari-kerja.index'));
        $this->assertDatabaseCount('master_hari_kerja_audit', 2);
        $this->assertSame(0, MasterHariKerjaAudit::where('is_verified', true)->count());
    }

    public function test_role_authorization_and_hitung_hari_endpoint(): void
    {
        MasterHariKerjaAudit::create([
            'jumlah_min'    => 1,
            'jumlah_max'    => 1000,
            'hari_tinggi'   => 14.0,
            'hari_menengah' => 10.0,
            'hari_rendah'   => 7.0,
            'is_verified'   => true,
        ]);

        // Non-admin ditolak 403 pada CRUD master
        $this->actingAs($this->auditor)
            ->get(route('admin.master-hari-kerja.index'))
            ->assertForbidden();

        $this->flushSession();
        $this->actingAs($this->auditorSmkp)
            ->get(route('admin.master-hari-kerja.index'))
            ->assertForbidden();

        // Role auditor ditolak 403 pada endpoint hitung-hari
        $this->flushSession();
        $this->actingAs($this->auditor)
            ->postJson(route('admin.audit-sesi.tahap-awal.hitung-hari', $this->sesi->id), [
                'jumlah_pekerja' => 500,
                'kelas_risiko'   => 'Tinggi',
            ])
            ->assertForbidden();

        // Role admin & auditor_smkp diizinkan menghitung dari tabel acuan
        $this->flushSession();
        $res = $this->actingAs($this->auditorSmkp)
            ->postJson(route('admin.audit-sesi.tahap-awal.hitung-hari', $this->sesi->id), [
                'jumlah_pekerja' => 500,
                'kelas_risiko'   => 'Tinggi',
                'faktor_ids'     => [],
            ]);

        $res->assertOk()
            ->assertJson([
                'success' => true,
                'data'    => [
                    'mandays_awal'  => 14.0,
                    'faktor_hari'   => 0.0,
                    'total_mandays' => 14.0,
                    'is_verified'   => true,
                ],
            ]);
    }

    public function test_hitung_hari_endpoint_has_csrf_and_throttle_middleware(): void
    {
        $route = app('router')->getRoutes()->getByName('admin.audit-sesi.tahap-awal.hitung-hari');
        $this->assertNotNull($route);

        $middleware = $route->gatherMiddleware();
        $this->assertContains('web', $middleware);
        $this->assertContains('throttle:30,1', $middleware);

        $webGroup = app(\Illuminate\Contracts\Http\Kernel::class)->getMiddlewareGroups()['web'] ?? [];
        $this->assertContains(\App\Http\Middleware\VerifyCsrfToken::class, $webGroup);
    }
}
