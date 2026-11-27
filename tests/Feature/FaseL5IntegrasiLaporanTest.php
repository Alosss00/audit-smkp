<?php

namespace Tests\Feature;

use App\Models\AuditDokumenAcuan;
use App\Models\AuditLaporan;
use App\Models\AuditSesi;
use App\Models\Perusahaan;
use App\Models\User;
use App\Services\RestorePointService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use ReflectionProperty;
use Tests\TestCase;

class FaseL5IntegrasiLaporanTest extends TestCase
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

    protected function makePerusahaan(string $nama = 'PT Contoh Integrasi'): Perusahaan
    {
        return Perusahaan::create([
            'nama_perusahaan' => $nama,
            'kode_perusahaan' => 'CI-' . rand(100, 999),
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
            'skor_akhir'      => 85.0,
        ]);
    }

    public function test_dokumen_acuan_upload_checklist_and_attachment_access(): void
    {
        Storage::fake('attachments');

        $auditorSmkp = $this->makeUser('auditor_smkp');
        $perusahaan = $this->makePerusahaan('PT Contoh Dokumen');
        $sesi = $this->makeSesi($perusahaan, $auditorSmkp, 2026);

        // Awalnya RKAB, SOP, Manual belum tersedia
        $checklistBefore = collect($sesi->getKelengkapanDokumenWajib())->keyBy('key');
        $this->assertFalse($checklistBefore['rkab']['tersedia']);
        $this->assertFalse($checklistBefore['sop_audit']['tersedia']);
        $this->assertFalse($checklistBefore['manual_smkp']['tersedia']);

        // Upload RKAB
        $file = UploadedFile::fake()->create('rkab-2026.pdf', 120, 'application/pdf');
        $this->actingAs($auditorSmkp)
            ->post(route('admin.audit-sesi.laporan.dokumen-acuan.store', $sesi->id), [
                'jenis_dokumen' => 'rkab',
                'judul'         => 'RKAB Tahun 2026',
                'nomor_dokumen' => 'RKAB-001/2026',
                'berkas'        => $file,
            ])
            ->assertRedirect(route('admin.audit-sesi.laporan.show', $sesi->id));

        $doc = AuditDokumenAcuan::where('audit_sesi_id', $sesi->id)->firstOrFail();
        Storage::disk('attachments')->assertExists($doc->file_path);

        $checklistAfter = collect($sesi->getKelengkapanDokumenWajib())->keyBy('key');
        $this->assertTrue($checklistAfter['rkab']['tersedia']);

        // Download via AttachmentController
        $this->actingAs($auditorSmkp)
            ->get(route('attachments.show', ['type' => 'dokumen_acuan', 'id' => $doc->id]))
            ->assertStatus(200);

        // Soft delete
        $this->actingAs($auditorSmkp)
            ->delete(route('admin.audit-sesi.laporan.dokumen-acuan.destroy', [$sesi->id, $doc->id]))
            ->assertRedirect(route('admin.audit-sesi.laporan.show', $sesi->id));
        $this->assertSoftDeleted('audit_dokumen_acuans', ['id' => $doc->id]);
    }

    public function test_auditor_menu_only_shows_approved_reports_in_their_area(): void
    {
        $auditorSmkp = $this->makeUser('auditor_smkp');
        $ptA = $this->makePerusahaan('PT Auditor Alfa');
        $ptB = $this->makePerusahaan('PT Auditor Beta');

        $sesiADraft = $this->makeSesi($ptA, $auditorSmkp, 2025);
        $sesiAApproved = $this->makeSesi($ptA, $auditorSmkp, 2026);
        $sesiBApproved = $this->makeSesi($ptB, $auditorSmkp, 2026);

        $lapDraft = AuditLaporan::getOrCreateForSesi($sesiADraft);
        $lapDraft->update(['nomor_laporan' => 'LAP-DRAFT-HIDDEN', 'status' => 'draft']);

        $lapApprovedA = AuditLaporan::getOrCreateForSesi($sesiAApproved);
        $lapApprovedA->update(['nomor_laporan' => 'LAP-ALFA-SAH', 'status' => 'disahkan', 'tanggal_pengesahan' => now()]);

        $lapApprovedB = AuditLaporan::getOrCreateForSesi($sesiBApproved);
        $lapApprovedB->update(['nomor_laporan' => 'LAP-BETA-SAH', 'status' => 'disahkan', 'tanggal_pengesahan' => now()]);

        $auditorA = $this->makeUser('auditor', 'PT Auditor Alfa');

        $res = $this->actingAs($auditorA)->get(route('auditor.laporan.index'));
        $res->assertStatus(200);
        $res->assertSee('LAP-ALFA-SAH');
        $res->assertDontSee('LAP-DRAFT-HIDDEN');
        $res->assertDontSee('LAP-BETA-SAH');
    }

    public function test_admin_dashboard_shows_laporan_monitoring_widget_and_restore_point_includes_new_tables(): void
    {
        $admin = $this->makeUser('admin');
        $pt = $this->makePerusahaan('PT Dashboard Contoh');
        $sesi = $this->makeSesi($pt, $admin, 2025);

        $lap = AuditLaporan::getOrCreateForSesi($sesi);
        $lap->update([
            'status'                => 'disahkan',
            'batas_waktu_pelaporan' => Carbon::yesterday(),
        ]);

        $res = $this->actingAs($admin)->get(route('admin.dashboard'));
        $res->assertStatus(200);
        $res->assertSee('Monitoring Pelaporan Audit Internal SMKP');

        // Verify RestorePointService registers all 16 new tables
        $prop = new ReflectionProperty(RestorePointService::class, 'targetTables');
        $prop->setAccessible(true);
        $tables = $prop->getValue(new RestorePointService());

        foreach ([
            'perusahaan_legalitas',
            'audit_rencana_areas',
            'audit_laporans',
            'audit_laporan_profils',
            'audit_laporan_peralatans',
            'audit_laporan_narasi_elemens',
            'audit_laporan_snapshots',
            'audit_laporan_distribusis',
            'master_dasar_hukum',
            'master_template_laporan',
            'master_aturan_pelaporan',
            'audit_daftar_hadirs',
            'audit_daftar_hadir_pesertas',
            'audit_respon_manajemens',
            'audit_laporan_iujps',
            'audit_dokumen_acuans',
        ] as $tbl) {
            $this->assertContains($tbl, $tables, "Tabel {$tbl} belum terdaftar di RestorePointService.");
        }
    }
}
