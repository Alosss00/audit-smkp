<?php

namespace Tests\Feature;

use App\Models\AuditLaporan;
use App\Models\AuditLaporanDistribusi;
use App\Models\AuditLaporanSnapshot;
use App\Models\AuditLog;
use App\Models\AuditRencana;
use App\Models\AuditRencanaJadwal;
use App\Models\AuditSesi;
use App\Models\MasterAturanPelaporan;
use App\Models\MasterDasarHukum;
use App\Models\MasterTemplateLaporan;
use App\Models\Perusahaan;
use App\Models\User;
use App\Services\LaporanTemplateRenderer;
use Carbon\Carbon;
use Database\Seeders\MasterAturanPelaporanSeeder;
use Database\Seeders\MasterDasarHukumSeeder;
use Database\Seeders\MasterTemplateLaporanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FaseL2LaporanWorkflowTest extends TestCase
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

    protected function makePerusahaan(string $nama = 'PT Contoh Tambang'): Perusahaan
    {
        return Perusahaan::create([
            'nama_perusahaan' => $nama,
            'kode_perusahaan' => 'CT-' . rand(100, 999),
            'kategori'        => 'Pemegang IUP',
            'jenis_usaha'     => 'tambang',
            'is_active'       => true,
        ]);
    }

    protected function makeSesi(Perusahaan $perusahaan, User $owner, int $tahun = 2025, string $status = 'selesai'): AuditSesi
    {
        return AuditSesi::create([
            'user_id'         => $owner->id,
            'perusahaan_id'   => $perusahaan->id,
            'tahun_periode'   => $tahun,
            'tanggal_mulai'   => "{$tahun}-11-01",
            'tanggal_selesai' => "{$tahun}-11-05",
            'area_audit'      => $perusahaan->nama_perusahaan,
            'status'          => $status,
            'skor_akhir'      => 82.50,
        ]);
    }

    public function test_seeders_create_unverified_records_idempotently(): void
    {
        $this->seed(MasterDasarHukumSeeder::class);
        $this->seed(MasterTemplateLaporanSeeder::class);
        $this->seed(MasterAturanPelaporanSeeder::class);

        $countDH = MasterDasarHukum::count();
        $countTpl = MasterTemplateLaporan::count();
        $countAturan = MasterAturanPelaporan::count();

        $this->assertGreaterThanOrEqual(4, $countDH);
        $this->assertEquals(4, $countTpl);
        $this->assertEquals(1, $countAturan);

        $this->assertEquals(0, MasterDasarHukum::where('is_verified', true)->count());
        $this->assertEquals(0, MasterTemplateLaporan::where('is_verified', true)->count());
        $this->assertEquals(0, MasterAturanPelaporan::where('is_verified', true)->count());

        // Re-running seeders stays idempotent
        $this->seed(MasterDasarHukumSeeder::class);
        $this->seed(MasterTemplateLaporanSeeder::class);
        $this->seed(MasterAturanPelaporanSeeder::class);

        $this->assertEquals($countDH, MasterDasarHukum::count());
        $this->assertEquals($countTpl, MasterTemplateLaporan::count());
        $this->assertEquals($countAturan, MasterAturanPelaporan::count());
    }

    public function test_template_renderer_uses_whitelist_and_escapes_html(): void
    {
        $renderer = new LaporanTemplateRenderer();
        $tpl = 'Audit {{nama_perusahaan}} tahun {{tahun_periode}} - {{unknown_expr}} - <?php echo 1; ?>';
        $rendered = $renderer->renderEscaped($tpl, [
            'nama_perusahaan' => 'PT Contoh <script>alert(1)</script>',
            'tahun_periode'   => '2026',
            'unknown_expr'    => 'SHOULD_NOT_REPLACE',
        ]);

        $this->assertStringContainsString('PT Contoh &lt;script&gt;alert(1)&lt;/script&gt;', $rendered);
        $this->assertStringContainsString('tahun 2026', $rendered);
        $this->assertStringContainsString('{{unknown_expr}}', $rendered);
        $this->assertStringNotContainsString('SHOULD_NOT_REPLACE', $rendered);
        $this->assertStringContainsString('&lt;?php echo 1; ?&gt;', $rendered);
    }

    public function test_admin_can_crud_and_verify_masters_while_non_admin_gets_403(): void
    {
        $this->seed(MasterDasarHukumSeeder::class);
        $this->seed(MasterTemplateLaporanSeeder::class);
        $this->seed(MasterAturanPelaporanSeeder::class);

        $admin = $this->makeUser('admin');
        $auditorSmkp = $this->makeUser('auditor_smkp');

        // Admin creates & verifies Dasar Hukum
        $this->actingAs($admin)->post(route('admin.master-dasar-hukum.store'), [
            'kode'            => 'TEST_REG_2026',
            'nomor_peraturan' => 'Peraturan Uji No. 1 Tahun 2026',
            'judul'           => 'Pedoman Uji Pelaporan',
            'urutan'          => 10,
        ])->assertRedirect(route('admin.master-dasar-hukum.index'));

        $dh = MasterDasarHukum::where('kode', 'TEST_REG_2026')->firstOrFail();
        $this->assertFalse($dh->is_verified);

        $this->actingAs($admin)->patch(route('admin.master-dasar-hukum.toggle-verify', $dh->id))
            ->assertRedirect(route('admin.master-dasar-hukum.index'));
        $this->assertTrue($dh->fresh()->is_verified);

        // Admin updates & verifies Template
        $tpl = MasterTemplateLaporan::firstOrFail();
        $this->actingAs($admin)->put(route('admin.master-template-laporan.update', $tpl->id), [
            'judul'        => 'Judul Diperbarui',
            'isi_template' => 'Isi untuk {{nama_perusahaan}}.',
            'is_active'    => 1,
        ])->assertRedirect(route('admin.master-template-laporan.index'));
        $this->actingAs($admin)->patch(route('admin.master-template-laporan.toggle-verify', $tpl->id))
            ->assertRedirect(route('admin.master-template-laporan.index'));
        $this->assertTrue($tpl->fresh()->is_verified);

        // Admin verifies Aturan Pelaporan
        $aturan = MasterAturanPelaporan::firstOrFail();
        $this->actingAs($admin)->patch(route('admin.master-aturan-pelaporan.toggle-verify', $aturan->id))
            ->assertRedirect(route('admin.master-aturan-pelaporan.index'));
        $this->assertTrue($aturan->fresh()->is_verified);

        // Non-admin gets 403
        $this->flushSession();
        $this->actingAs($auditorSmkp)->get(route('admin.master-dasar-hukum.index'))->assertStatus(403);
        $this->actingAs($auditorSmkp)->get(route('admin.master-template-laporan.index'))->assertStatus(403);
        $this->actingAs($auditorSmkp)->get(route('admin.master-aturan-pelaporan.index'))->assertStatus(403);
    }

    public function test_full_workflow_snapshot_sha256_distribusi_pelaporan_and_revision(): void
    {
        Storage::fake('attachments');
        $this->seed(MasterDasarHukumSeeder::class);
        $this->seed(MasterTemplateLaporanSeeder::class);
        $this->seed(MasterAturanPelaporanSeeder::class);

        $admin = $this->makeUser('admin');
        $auditorSmkp = $this->makeUser('auditor_smkp');
        $perusahaan = $this->makePerusahaan('PT Contoh Workflow');
        $sesi = $this->makeSesi($perusahaan, $auditorSmkp, 2025, 'selesai');

        // Initialize & update metadata
        $this->actingAs($auditorSmkp)->put(route('admin.audit-sesi.laporan.metadata', $sesi->id), [
            'nomor_laporan'         => 'LAP-SMKP/2025/001',
            'tanggal_laporan'       => '2025-12-15',
            'tempat_penerbitan'     => 'Site Contoh',
            'penanggung_jawab_nama' => 'Ketua Tim Contoh',
        ])->assertRedirect(route('admin.audit-sesi.laporan.show', $sesi->id));

        $laporan = AuditLaporan::where('audit_sesi_id', $sesi->id)->firstOrFail();
        $this->assertEquals('draft', $laporan->status);
        // Batas waktu otomatis: 2025-12-31 + 30 hari = 2026-01-30
        $this->assertEquals('2026-01-30', $laporan->batas_waktu_pelaporan->format('Y-m-d'));
        $this->assertTrue($laporan->isOverdue(Carbon::create(2026, 2, 1)));
        $this->assertFalse($laporan->isOverdue(Carbon::create(2026, 1, 15)));

        // Draft -> Ditinjau
        $this->actingAs($auditorSmkp)->post(route('admin.audit-sesi.laporan.submit-review', $sesi->id))
            ->assertRedirect(route('admin.audit-sesi.laporan.show', $sesi->id));
        $this->assertEquals('ditinjau', $laporan->fresh()->status);

        // Ditinjau -> Disahkan (creates SHA-256 snapshot)
        $this->actingAs($auditorSmkp)->post(route('admin.audit-sesi.laporan.approve', $sesi->id))
            ->assertRedirect(route('admin.audit-sesi.laporan.show', $sesi->id));

        $laporan->refresh();
        $this->assertEquals('disahkan', $laporan->status);
        $snap0 = AuditLaporanSnapshot::where('audit_laporan_id', $laporan->id)->firstOrFail();
        $this->assertEquals(0, $snap0->revisi_ke);
        $this->assertEquals(64, strlen($snap0->hash_sha256));
        $this->assertTrue($snap0->isHashValid());
        $this->assertTrue($laporan->verifyLatestSnapshotIntegrity()['valid']);

        // Disahkan -> Didistribusikan (with attachment)
        $bukti = UploadedFile::fake()->create('tanda-terima.pdf', 80, 'application/pdf');
        $this->actingAs($auditorSmkp)->post(route('admin.audit-sesi.laporan.distribusi.store', $sesi->id), [
            'penerima_nama'      => 'Kepala Teknik Tambang',
            'penerima_jabatan'   => 'KTT',
            'penerima_instansi'  => 'PT Contoh Workflow',
            'jenis_penerima'     => 'ktt_pjo',
            'tanggal_distribusi' => '2026-01-10',
            'metode'             => 'hardcopy',
            'nomor_tanda_terima' => 'TT-001',
            'bukti'              => $bukti,
        ])->assertRedirect(route('admin.audit-sesi.laporan.show', $sesi->id));

        $laporan->refresh();
        $this->assertEquals('didistribusikan', $laporan->status);
        $dist = AuditLaporanDistribusi::where('audit_laporan_id', $laporan->id)->firstOrFail();
        Storage::disk('attachments')->assertExists($dist->bukti_path);
        $this->actingAs($auditorSmkp)
            ->get(route('attachments.show', ['type' => 'distribusi_laporan', 'id' => $dist->id]))
            ->assertStatus(200);

        // Didistribusikan -> Dilaporkan
        $this->actingAs($auditorSmkp)->post(route('admin.audit-sesi.laporan.mark-reported', $sesi->id), [
            'tanggal_dilaporkan' => '2026-01-20',
        ])->assertRedirect(route('admin.audit-sesi.laporan.show', $sesi->id));

        $laporan->refresh();
        $this->assertEquals('dilaporkan', $laporan->status);
        $this->assertFalse($laporan->isOverdue(Carbon::create(2026, 2, 15)));

        // Non-admin cannot reopen revision (403)
        $this->actingAs($auditorSmkp)->post(route('admin.audit-sesi.laporan.reopen-revision', $sesi->id), [
            'catatan_revisi' => 'Coba revisi non admin',
        ])->assertStatus(403);

        // Admin reopens revision: revisi_ke = 1, status = draft, old snapshot preserved
        $this->flushSession();
        $this->actingAs($admin)->post(route('admin.audit-sesi.laporan.reopen-revision', $sesi->id), [
            'catatan_revisi' => 'Perbaikan lampiran peralatan.',
        ])->assertRedirect(route('admin.audit-sesi.laporan.show', $sesi->id));

        $laporan->refresh();
        $this->assertEquals('draft', $laporan->status);
        $this->assertEquals(1, $laporan->revisi_ke);
        $this->assertEquals(1, AuditLaporanSnapshot::where('audit_laporan_id', $laporan->id)->count());
        $this->assertTrue(AuditLog::where('modul', 'Laporan Audit')->exists());
    }

    public function test_audit_dinyatakan_selesai_and_auditor_area_scoping(): void
    {
        $auditorSmkp = $this->makeUser('auditor_smkp');
        $perusahaanA = $this->makePerusahaan('PT Selesai Alfa');
        $perusahaanB = $this->makePerusahaan('PT Selesai Beta');
        $sesiA = $this->makeSesi($perusahaanA, $auditorSmkp, 2026, 'selesai');
        $sesiB = $this->makeSesi($perusahaanB, $auditorSmkp, 2026, 'selesai');

        $laporanA = AuditLaporan::getOrCreateForSesi($sesiA);
        AuditLaporan::getOrCreateForSesi($sesiB);

        // Belum ada jadwal -> belum selesai
        $this->assertFalse($laporanA->auditDinyatakanSelesai()['selesai']);

        $rencana = AuditRencana::create([
            'audit_sesi_id' => $sesiA->id,
            'no_dokumen'    => 'TT-MGT-FRS-026A',
            'status'        => 'draft',
        ]);
        $j1 = AuditRencanaJadwal::create([
            'audit_rencana_id'   => $rencana->id,
            'hari_ke'            => 1,
            'tanggal'            => '2026-11-01',
            'jam_mulai'          => '08:00',
            'jam_selesai'        => '09:00',
            'jenis_kegiatan'     => 'rapat_pembukaan',
            'kegiatan'           => 'Rapat Pembukaan',
            'status_pelaksanaan' => 'direncanakan',
        ]);
        $j2 = AuditRencanaJadwal::create([
            'audit_rencana_id'   => $rencana->id,
            'hari_ke'            => 2,
            'tanggal'            => '2026-11-02',
            'jam_mulai'          => '15:00',
            'jam_selesai'        => '16:00',
            'jenis_kegiatan'     => 'rapat_penutupan',
            'kegiatan'           => 'Rapat Penutupan',
            'status_pelaksanaan' => 'direncanakan',
        ]);

        $this->assertFalse($laporanA->fresh()->auditDinyatakanSelesai()['selesai']);

        // Update status jadwal ke terlaksana via controller
        $this->actingAs($auditorSmkp)->patch(route('admin.audit-sesi.laporan.jadwal-status', $sesiA->id), [
            'statuses' => [
                $j1->id => 'terlaksana',
                $j2->id => 'terlaksana',
            ],
        ])->assertRedirect(route('admin.audit-sesi.laporan.show', $sesiA->id));

        $this->assertTrue($laporanA->fresh()->auditDinyatakanSelesai()['selesai']);

        // Auditor area scoping
        $this->flushSession();
        $auditorA = $this->makeUser('auditor', 'PT Selesai Alfa');
        $this->actingAs($auditorA)->get(route('auditor.audit-sesi.laporan.show', $sesiA->id))->assertStatus(200);
        $this->actingAs($auditorA)->get(route('auditor.audit-sesi.laporan.show', $sesiB->id))->assertStatus(404);
        $this->actingAs($auditorA)->post(route('admin.audit-sesi.laporan.approve', $sesiA->id))->assertStatus(403);
    }
}
