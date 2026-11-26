<?php

namespace Tests\Feature;

use App\Models\AuditDaftarHadir;
use App\Models\AuditLaporan;
use App\Models\AuditLaporanIujp;
use App\Models\AuditRencana;
use App\Models\AuditRencanaPengesahan;
use App\Models\AuditRencanaTim;
use App\Models\AuditResponManajemen;
use App\Models\AuditSesi;
use App\Models\Perusahaan;
use App\Models\User;
use Database\Seeders\MasterTemplateLaporanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FaseL4LampiranLaporanTest extends TestCase
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

    protected function makePerusahaan(string $nama = 'PT Contoh Tambang', string $jenisUsaha = 'tambang', string $kategori = 'Pemegang IUP'): Perusahaan
    {
        return Perusahaan::create([
            'nama_perusahaan' => $nama,
            'kode_perusahaan' => 'CT-' . rand(100, 999),
            'kategori'        => $kategori,
            'jenis_usaha'     => $jenisUsaha,
            'nama_ktt_default'=> 'KTT Contoh',
            'is_active'       => true,
        ]);
    }

    protected function makeSesi(Perusahaan $perusahaan, User $owner, int $tahun = 2026, string $status = 'selesai', float $skor = 80.0): AuditSesi
    {
        return AuditSesi::create([
            'user_id'         => $owner->id,
            'perusahaan_id'   => $perusahaan->id,
            'tahun_periode'   => $tahun,
            'tanggal_mulai'   => "{$tahun}-05-04", // Senin, 4 Mei 2026
            'tanggal_selesai' => "{$tahun}-05-08", // Jumat, 8 Mei 2026
            'area_audit'      => $perusahaan->nama_perusahaan,
            'status'          => $status,
            'skor_akhir'      => $skor,
        ]);
    }

    public function test_daftar_hadir_auto_title_indonesian_day_prefill_warning_and_scan_upload(): void
    {
        Storage::fake('attachments');

        $auditorSmkp = $this->makeUser('auditor_smkp');
        $perusahaan = $this->makePerusahaan('PT Contoh Hadir');
        $sesi = $this->makeSesi($perusahaan, $auditorSmkp, 2026);

        $rencana = AuditRencana::create([
            'audit_sesi_id' => $sesi->id,
            'no_dokumen'    => 'TT-MGT-FRS-026A',
            'status'        => 'draft',
        ]);
        AuditRencanaTim::create([
            'audit_rencana_id' => $rencana->id,
            'nama'             => 'Lead Contoh',
            'jabatan'          => 'Lead Auditor',
            'peran'            => 'lead_auditor',
            'urutan'           => 1,
        ]);
        AuditRencanaPengesahan::create([
            'audit_rencana_id' => $rencana->id,
            'urutan'           => 1,
            'jabatan'          => 'KTT',
            'nama'             => 'KTT Contoh',
        ]);

        // Prefill Penutupan from Rencana Audit
        $this->actingAs($auditorSmkp)
            ->post(route('admin.audit-sesi.laporan.daftar-hadir.prefill', [$sesi->id, 'penutupan']))
            ->assertRedirect(route('admin.audit-sesi.laporan.lampiran', $sesi->id));

        $dh = AuditDaftarHadir::with('pesertas')
            ->where('audit_sesi_id', $sesi->id)
            ->where('jenis', 'penutupan')
            ->firstOrFail();

        $this->assertEquals('Penutupan Audit Internal SMKP', $dh->judul_otomatis);
        $this->assertEquals(2, $dh->pesertas->count());

        // Save with date outside session range + scan upload
        $scan = UploadedFile::fake()->create('scan-penutupan.pdf', 100, 'application/pdf');
        $this->actingAs($auditorSmkp)
            ->put(route('admin.audit-sesi.laporan.daftar-hadir.save', [$sesi->id, 'penutupan']), [
                'tanggal'  => '2026-05-11', // Senin, after tanggal_selesai (2026-05-08)
                'lokasi'   => 'Ruang Rapat Utama',
                'scan'     => $scan,
                'pesertas' => [
                    ['nama' => 'Lead Contoh', 'jabatan' => 'Lead Auditor', 'unit_instansi' => 'Tim Audit', 'peran' => 'auditor'],
                ],
            ])
            ->assertRedirect(route('admin.audit-sesi.laporan.lampiran', $sesi->id));

        $dh->refresh();
        $this->assertEquals('Senin', $dh->nama_hari_indonesia);
        $this->assertNotNull($dh->getDateWarning($sesi));
        Storage::disk('attachments')->assertExists($dh->scan_path);

        // Download scan via AttachmentController & print Daftar Hadir
        $this->actingAs($auditorSmkp)
            ->get(route('attachments.show', ['type' => 'daftar_hadir_scan', 'id' => $dh->id]))
            ->assertStatus(200);

        $this->actingAs($auditorSmkp)
            ->get(route('admin.audit-sesi.laporan.daftar-hadir.cetak', [$sesi->id, 'penutupan']))
            ->assertStatus(200)
            ->assertSee('Penutupan Audit Internal SMKP')
            ->assertSee('Senin, 11/05/2026');
    }

    public function test_respon_manajemen_prefill_date_warning_and_upload(): void
    {
        Storage::fake('attachments');
        $this->seed(MasterTemplateLaporanSeeder::class);

        $auditorSmkp = $this->makeUser('auditor_smkp');
        $perusahaan = $this->makePerusahaan('PT Contoh Respon');
        $sesi = $this->makeSesi($perusahaan, $auditorSmkp, 2026);

        // Prefill from template
        $this->actingAs($auditorSmkp)
            ->post(route('admin.audit-sesi.laporan.respon-manajemen.prefill', $sesi->id))
            ->assertRedirect(route('admin.audit-sesi.laporan.lampiran', $sesi->id));

        $rm = AuditResponManajemen::where('audit_sesi_id', $sesi->id)->firstOrFail();
        $this->assertStringContainsString('PT Contoh Respon', (string) $rm->isi_respon);

        // Save with date earlier than tanggal_selesai -> warning triggered
        $dok = UploadedFile::fake()->create('respon.pdf', 80, 'application/pdf');
        $this->actingAs($auditorSmkp)
            ->put(route('admin.audit-sesi.laporan.respon-manajemen.save', $sesi->id), [
                'tanggal_respon'        => '2026-05-01', // before 2026-05-08
                'nama_penandatangan'    => 'KTT Contoh',
                'jabatan_penandatangan' => 'Kepala Teknik Tambang',
                'isi_respon'            => 'Manajemen berkomitmen menindaklanjuti seluruh temuan.',
                'dokumen'               => $dok,
            ])
            ->assertRedirect(route('admin.audit-sesi.laporan.lampiran', $sesi->id));

        $rm->refresh();
        $this->assertNotNull($rm->getDateWarning($sesi));
        Storage::disk('attachments')->assertExists($rm->dokumen_path);
        $this->actingAs($auditorSmkp)
            ->get(route('attachments.show', ['type' => 'respon_manajemen', 'id' => $rm->id]))
            ->assertStatus(200);
    }

    public function test_iujp_allowed_for_tambang_auto_pulls_session_and_rejected_for_jasa(): void
    {
        Storage::fake('attachments');

        $auditorSmkp = $this->makeUser('auditor_smkp');
        $ptTambang = $this->makePerusahaan('PT Tambang Utama', 'tambang', 'Pemegang IUP');
        $ptIujp = $this->makePerusahaan('PT Jasa Kontraktor', 'jasa_pertambangan_inti', 'Kontraktor');

        $sesiTambang = $this->makeSesi($ptTambang, $auditorSmkp, 2026, 'selesai', 85.0);
        $sesiIujp = $this->makeSesi($ptIujp, $auditorSmkp, 2026, 'selesai', 77.5);

        // Tambang can auto-pull from finished IUJP session
        $this->actingAs($auditorSmkp)
            ->post(route('admin.audit-sesi.laporan.iujp.store', $sesiTambang->id), [
                'sumber_audit_sesi_id' => $sesiIujp->id,
                'bidang_usaha'         => 'Pengangkutan & Penambangan',
            ])
            ->assertRedirect(route('admin.audit-sesi.laporan.lampiran', $sesiTambang->id));

        $laporanTambang = AuditLaporan::where('audit_sesi_id', $sesiTambang->id)->firstOrFail();
        $iujpRow = AuditLaporanIujp::where('audit_laporan_id', $laporanTambang->id)->firstOrFail();
        $this->assertEquals('PT Jasa Kontraktor', $iujpRow->nama_perusahaan_iujp);
        $this->assertEquals(77.5, (float) $iujpRow->skor_pencapaian);

        // Jasa company gets 422 when trying to add IUJP
        $this->actingAs($auditorSmkp)
            ->post(route('admin.audit-sesi.laporan.iujp.store', $sesiIujp->id), [
                'nama_perusahaan_iujp' => 'PT Tidak Boleh',
            ])
            ->assertStatus(422);
    }

    public function test_rtl_print_has_signature_blocks_and_cetak_laporan_includes_lampiran_a_to_f_and_auditor_scoping(): void
    {
        $auditorSmkp = $this->makeUser('auditor_smkp');
        $ptA = $this->makePerusahaan('PT Lampiran Alfa', 'tambang');
        $ptB = $this->makePerusahaan('PT Lampiran Beta', 'tambang');
        $sesiA = $this->makeSesi($ptA, $auditorSmkp, 2026);
        $sesiB = $this->makeSesi($ptB, $auditorSmkp, 2026);

        AuditLaporan::getOrCreateForSesi($sesiA);
        AuditLaporan::getOrCreateForSesi($sesiB);

        // RTL print has approval signature blocks
        $this->actingAs($auditorSmkp)
            ->get(route('admin.audit-sesi.cetak-ketidaksesuaian', $sesiA->id))
            ->assertStatus(200)
            ->assertSee('Ketua Tim Auditor (Lead Auditor)')
            ->assertSee('Perwakilan Auditi (KTT / PJO / Penanggung Jawab)');

        // Laporan print includes Lampiran A-F
        $this->actingAs($auditorSmkp)
            ->get(route('admin.audit-sesi.laporan.cetak', $sesiA->id))
            ->assertStatus(200)
            ->assertSee('Lampiran A')
            ->assertSee('Lampiran B')
            ->assertSee('Lampiran C')
            ->assertSee('Lampiran D')
            ->assertSee('Lampiran E')
            ->assertSee('Lampiran F');

        // Auditor area scoping
        $this->flushSession();
        $auditorA = $this->makeUser('auditor', 'PT Lampiran Alfa');
        $this->actingAs($auditorA)->get(route('auditor.audit-sesi.laporan.lampiran', $sesiA->id))->assertStatus(200);
        $this->actingAs($auditorA)->get(route('auditor.audit-sesi.laporan.lampiran', $sesiB->id))->assertStatus(404);
        $this->actingAs($auditorA)->put(route('admin.audit-sesi.laporan.respon-manajemen.save', $sesiA->id), [
            'isi_respon' => 'Blocked',
        ])->assertStatus(403);
    }
}
