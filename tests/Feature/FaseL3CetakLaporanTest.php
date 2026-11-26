<?php

namespace Tests\Feature;

use App\Models\AuditDetail;
use App\Models\AuditLaporan;
use App\Models\AuditLaporanSnapshot;
use App\Models\AuditSesi;
use App\Models\Elemen;
use App\Models\Kriteria;
use App\Models\Perusahaan;
use App\Models\Pica;
use App\Models\SubElemen;
use App\Models\User;
use App\Services\LaporanAuditWorkflowService;
use App\Services\RadarChartSvg;
use App\Support\Terbilang;
use Database\Seeders\MasterAturanPelaporanSeeder;
use Database\Seeders\MasterDasarHukumSeeder;
use Database\Seeders\MasterTemplateLaporanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FaseL3CetakLaporanTest extends TestCase
{
    use RefreshDatabase;

    protected function makeUser(string $role, ?string $area = null): User
    {
        return User::create([
            'name'      => 'User ' . $role . ' ' . uniqid(),
            'username'  => $role . '_' . uniqid(),
            'email'     => $role . '_' . uniqid() . '@contoh.test',
            'password'  => bcrypt('password'),
            'role'      => $role,
            'area'      => $area,
            'is_active' => true,
        ]);
    }

    protected function makePerusahaan(string $nama = 'PT Contoh Cetak'): Perusahaan
    {
        return Perusahaan::create([
            'nama_perusahaan' => $nama,
            'kode_perusahaan' => 'CT-' . rand(100, 999),
            'kategori'        => 'Pemegang IUP',
            'jenis_usaha'     => 'tambang',
            'is_active'       => true,
        ]);
    }

    protected function makeSesi(Perusahaan $perusahaan, User $owner, int $tahun = 2026): AuditSesi
    {
        return AuditSesi::create([
            'user_id'         => $owner->id,
            'perusahaan_id'   => $perusahaan->id,
            'tahun_periode'   => $tahun,
            'tanggal_mulai'   => "{$tahun}-05-01",
            'tanggal_selesai' => "{$tahun}-05-05",
            'area_audit'      => $perusahaan->nama_perusahaan,
            'status'          => 'selesai',
            'skor_akhir'      => 0,
        ]);
    }

    public function test_terbilang_converts_integers_and_decimals_accurately(): void
    {
        $this->assertEquals('nol', Terbilang::integer(0));
        $this->assertEquals('sebelas', Terbilang::integer(11));
        $this->assertEquals('sembilan belas', Terbilang::integer(19));
        $this->assertEquals('seratus dua puluh lima', Terbilang::integer(125));
        $this->assertEquals('seribu lima ratus', Terbilang::integer(1500));
        $this->assertEquals('delapan puluh dua koma lima', Terbilang::decimal(82.50, 2, true));
        $this->assertEquals('tiga puluh satu koma enam nol', Terbilang::decimal(31.60, 2, false));
        $this->assertEquals('seratus', Terbilang::decimal(100.00, 2, true));
    }

    public function test_radar_chart_svg_renders_valid_csp_safe_svg(): void
    {
        $svg = (new RadarChartSvg())->render([
            ['kode_elemen' => 'I', 'nama_elemen' => 'Kebijakan', 'persentase' => 80.0],
            ['kode_elemen' => 'II', 'nama_elemen' => 'Perencanaan', 'persentase' => 65.5],
            ['kode_elemen' => 'III', 'nama_elemen' => 'Organisasi', 'persentase' => 90.0],
            ['kode_elemen' => 'IV', 'nama_elemen' => 'Implementasi', 'persentase' => 72.25],
            ['kode_elemen' => 'V', 'nama_elemen' => 'Evaluasi', 'persentase' => 50.0],
            ['kode_elemen' => 'VI', 'nama_elemen' => 'Dokumentasi', 'persentase' => 100.0],
            ['kode_elemen' => 'VII', 'nama_elemen' => 'Tinjauan', 'persentase' => 40.0],
        ]);

        $this->assertStringContainsString('<svg ', $svg);
        $this->assertStringContainsString('</svg>', $svg);
        $this->assertStringContainsString('Elemen I', $svg);
        $this->assertStringContainsString('Elemen VII', $svg);
        $this->assertStringContainsString('80,00%', $svg);
        $this->assertStringNotContainsString('<script', $svg);
        $this->assertStringNotContainsString('style=', $svg);
    }

    public function test_uniform_finding_code_format(): void
    {
        $this->assertEquals('NC-KRT-001', LaporanAuditWorkflowService::formatKodeTemuan('kritikal', 1));
        $this->assertEquals('NC-MYR-012', LaporanAuditWorkflowService::formatKodeTemuan('mayor', 12));
        $this->assertEquals('NC-MNR-005', LaporanAuditWorkflowService::formatKodeTemuan('minor', 5));
    }

    public function test_cetak_draft_uses_live_data_watermark_and_teks_override_and_disahkan_uses_immutable_snapshot(): void
    {
        $this->seed(MasterDasarHukumSeeder::class);
        $this->seed(MasterTemplateLaporanSeeder::class);
        $this->seed(MasterAturanPelaporanSeeder::class);

        $auditorSmkp = $this->makeUser('auditor_smkp');
        $perusahaan = $this->makePerusahaan('PT Contoh Awal');
        $sesi = $this->makeSesi($perusahaan, $auditorSmkp, 2026);

        $elemen = Elemen::create(['kode_elemen' => 'I', 'nama_elemen' => 'Kebijakan', 'bobot' => 10]);
        $sub = SubElemen::create(['elemen_id' => $elemen->id, 'kode_sub' => 'I.1', 'nama_sub' => 'Penyusunan Kebijakan', 'bobot' => 10, 'is_na' => false]);
        $krit = Kriteria::create(['sub_elemen_id' => $sub->id, 'kode_kriteria' => 'I.1.1', 'deskripsi' => 'Kebijakan disahkan', 'nilai_maksimal' => 4]);

        $detail = AuditDetail::create([
            'audit_sesi_id' => $sesi->id,
            'kriteria_id'   => $krit->id,
            'nilai'         => 2,
            'is_na'         => false,
        ]);

        $pica = Pica::create([
            'audit_detail_id'         => $detail->id,
            'kategori_temuan'         => 'mayor',
            'deskripsi_temuan'        => 'Temuan contoh mayor',
            'plor_problem'            => 'Kebijakan belum ditinjau ulang.',
            'plor_location'           => 'Kantor Site',
            'plor_objective_evidence' => 'Dokumen revisi terakhir tahun 2022.',
            'plor_reference'          => 'Kriteria I.1.1',
            'status'                  => 'open',
        ]);
        $pica->audit_sesi_id = $sesi->id;
        $pica->nomor_urut = 1;
        $pica->save();

        // Set teks_override while draft
        $this->actingAs($auditorSmkp)->put(route('admin.audit-sesi.laporan.teks-override', $sesi->id), [
            'teks_override' => [
                'latar_belakang' => 'NARASI KHUSUS OVERRIDE DRAFT',
            ],
        ])->assertRedirect(route('admin.audit-sesi.laporan.show', $sesi->id));

        // Cetak while draft -> watermark DRAFT, live data, teks_override, kode NC-MYR-001, SVG radar
        $draftRes = $this->actingAs($auditorSmkp)->get(route('admin.audit-sesi.laporan.cetak', $sesi->id));
        $draftRes->assertStatus(200);
        $draftRes->assertSee('DRAFT — BELUM DISAHKAN');
        $draftRes->assertSee('NARASI KHUSUS OVERRIDE DRAFT');
        $draftRes->assertSee('NC-MYR-001');
        $draftRes->assertSee('<svg ', false);

        // Submit & Approve -> locks snapshot
        $this->actingAs($auditorSmkp)->post(route('admin.audit-sesi.laporan.submit-review', $sesi->id));
        $this->actingAs($auditorSmkp)->post(route('admin.audit-sesi.laporan.approve', $sesi->id));

        // Mutate live company name AFTER approval
        $perusahaan->update(['nama_perusahaan' => 'PT NAMA BERUBAH SETELAH DISAHKAN']);

        // Cetak while disahkan -> renders from snapshot (still shows PT Contoh Awal, no DRAFT watermark, has SHA-256)
        $approvedRes = $this->actingAs($auditorSmkp)->get(route('admin.audit-sesi.laporan.cetak', $sesi->id));
        $approvedRes->assertStatus(200);
        $approvedRes->assertDontSee('DRAFT — BELUM DISAHKAN');
        $approvedRes->assertSee('PT CONTOH AWAL');
        $approvedRes->assertDontSee('PT NAMA BERUBAH SETELAH DISAHKAN');
        $approvedRes->assertSee('SHA-256:');

        // Verify hash endpoint succeeds
        $this->actingAs($auditorSmkp)
            ->post(route('admin.audit-sesi.laporan.verify-hash', $sesi->id))
            ->assertRedirect(route('admin.audit-sesi.laporan.show', $sesi->id))
            ->assertSessionHas('success');

        // Tamper snapshot payload -> verify hash detects mismatch
        $laporan = AuditLaporan::where('audit_sesi_id', $sesi->id)->firstOrFail();
        $snap = AuditLaporanSnapshot::where('audit_laporan_id', $laporan->id)->firstOrFail();
        $snap->update(['payload_json' => $snap->payload_json . ' ']);

        $this->actingAs($auditorSmkp)
            ->post(route('admin.audit-sesi.laporan.verify-hash', $sesi->id))
            ->assertRedirect(route('admin.audit-sesi.laporan.show', $sesi->id))
            ->assertSessionHas('error');
    }

    public function test_auditor_area_scoping_on_cetak_and_teks_override_forbidden(): void
    {
        $auditorSmkp = $this->makeUser('auditor_smkp');
        $perusahaanA = $this->makePerusahaan('PT Cetak Alfa');
        $perusahaanB = $this->makePerusahaan('PT Cetak Beta');
        $sesiA = $this->makeSesi($perusahaanA, $auditorSmkp, 2026);
        $sesiB = $this->makeSesi($perusahaanB, $auditorSmkp, 2026);

        AuditLaporan::getOrCreateForSesi($sesiA);
        AuditLaporan::getOrCreateForSesi($sesiB);

        $auditorA = $this->makeUser('auditor', 'PT Cetak Alfa');

        $this->actingAs($auditorA)->get(route('auditor.audit-sesi.laporan.cetak', $sesiA->id))->assertStatus(200);
        $this->actingAs($auditorA)->get(route('auditor.audit-sesi.laporan.cetak', $sesiB->id))->assertStatus(404);
        $this->actingAs($auditorA)->put(route('admin.audit-sesi.laporan.teks-override', $sesiA->id), [
            'teks_override' => ['penutup' => 'Blocked'],
        ])->assertStatus(403);
    }
}
