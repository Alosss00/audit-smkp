<?php

namespace Tests\Feature;

use App\Models\AuditRencana;
use App\Models\AuditSesi;
use App\Models\AuditTahapAwal;
use App\Models\Elemen;
use App\Models\Perusahaan;
use App\Models\SubElemen;
use App\Models\User;
use App\Services\RencanaAuditService;
use Database\Seeders\MasterPenandatanganRencanaSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class Fase2RencanaAuditTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $auditorSmkp;
    protected User $auditorMatch;
    protected User $auditorOther;
    protected Perusahaan $perusahaan;
    protected AuditSesi $sesi;
    protected Elemen $elemen;
    protected SubElemen $sub1;
    protected SubElemen $sub2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(MasterPenandatanganRencanaSeeder::class);

        $this->admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->auditorSmkp = User::factory()->create(['role' => 'auditor_smkp', 'is_active' => true]);
        $this->auditorMatch = User::factory()->create(['role' => 'auditor', 'area' => 'PT Tambang Rencana', 'is_active' => true]);
        $this->auditorOther = User::factory()->create(['role' => 'auditor', 'area' => 'PT Area Lain', 'is_active' => true]);

        $this->perusahaan = Perusahaan::create([
            'nama_perusahaan' => 'PT Tambang Rencana',
            'kode_perusahaan' => 'PT-TR',
            'kategori'        => 'Pemegang IUP',
            'jenis_usaha'     => 'tambang',
            'is_active'       => true,
        ]);

        $this->elemen = Elemen::create([
            'kode_elemen' => 'I',
            'nama_elemen' => 'Kebijakan',
            'bobot'       => 10,
        ]);
        $this->sub1 = SubElemen::create([
            'elemen_id'      => $this->elemen->id,
            'kode_sub'       => 'I.1',
            'nama_sub'       => 'Penyusunan Kebijakan',
            'nilai_maksimal' => 4,
            'is_na'          => false,
        ]);
        $this->sub2 = SubElemen::create([
            'elemen_id'      => $this->elemen->id,
            'kode_sub'       => 'I.2',
            'nama_sub'       => 'Isi Kebijakan',
            'nilai_maksimal' => 4,
            'is_na'          => false,
        ]);

        $this->sesi = AuditSesi::create([
            'user_id'         => $this->admin->id,
            'perusahaan_id'   => $this->perusahaan->id,
            'tahun_periode'   => 2026,
            'tanggal_mulai'   => '2026-03-10',
            'tanggal_selesai' => '2026-03-12',
            'area_audit'      => $this->perusahaan->nama_perusahaan,
            'status'          => 'berjalan',
            'skor_akhir'      => 0,
        ]);
        AuditTahapAwal::createEmptyForSesi($this->sesi);
    }

    public function test_rencana_initialization_populates_samples_and_signatories(): void
    {
        $res = $this->actingAs($this->admin)
            ->get(route('admin.audit-sesi.rencana.edit', $this->sesi->id));

        $res->assertOk();
        $rencana = AuditRencana::where('audit_sesi_id', $this->sesi->id)
            ->with(['sampels', 'pengesahans'])
            ->first();

        $this->assertNotNull($rencana);
        $this->assertSame('TT-MGT-FRS-026A', $rencana->no_dokumen);
        $this->assertSame('draft', $rencana->status);
        $this->assertCount(2, $rencana->sampels);
        // Tambang => Ketua Tim, KTT, KaIT
        $this->assertSame(['Ketua Tim', 'KTT', 'KaIT'], $rencana->pengesahans->pluck('jabatan')->all());
    }

    public function test_slovin_formula_calculation_and_validation(): void
    {
        $service = app(RencanaAuditService::class);

        // N = 100, e = 0.05 => 100 / (1 + 100 * 0.0025) = 100 / 1.25 = 80
        $this->assertSame(80, $service->hitungSlovin(100, 0.05));
        // N = 1000, e = 0.05 => 1000 / 3.5 = 285.714... => ceil = 286
        $this->assertSame(286, $service->hitungSlovin(1000, 0.05));

        $this->expectException(InvalidArgumentException::class);
        $service->hitungSlovin(100, 1.0);
    }

    public function test_schedule_and_sample_validation_rules(): void
    {
        $rencana = AuditRencana::getOrInitializeForSesi($this->sesi);
        $sampelId = $rencana->sampels()->first()->id;

        // 1. Tanggal di luar rentang sesi & jam selesai <= jam mulai ditolak
        $resBadSchedule = $this->actingAs($this->admin)
            ->put(route('admin.audit-sesi.rencana.update', $this->sesi->id), [
                'no_dokumen' => 'TT-MGT-FRS-026A',
                'jadwals'    => [
                    [
                        'hari_ke'        => 1,
                        'tanggal'        => '2026-03-01', // sebelum 10 Maret
                        'jam_mulai'      => '10:00',
                        'jam_selesai'    => '09:00', // sebelum jam mulai
                        'jenis_kegiatan' => 'rapat_pembukaan',
                        'kegiatan'       => 'Opening',
                    ],
                ],
            ]);
        $resBadSchedule->assertSessionHasErrors(['jadwals.0.tanggal', 'jadwals.0.jam_selesai']);

        // 2. Metode diisi tanpa deskripsi_sampel / jumlah_sampel ditolak (slide 29)
        $resBadSample = $this->actingAs($this->admin)
            ->put(route('admin.audit-sesi.rencana.update', $this->sesi->id), [
                'no_dokumen' => 'TT-MGT-FRS-026A',
                'sampels'    => [
                    $sampelId => [
                        'metode'           => ['wawancara'],
                        'teknik_sampling'  => 'purposive_risk_based',
                        'jumlah_sampel'    => '',
                        'deskripsi_sampel' => '',
                    ],
                ],
            ]);
        $resBadSample->assertSessionHasErrors([
            "sampels.{$sampelId}.deskripsi_sampel",
            "sampels.{$sampelId}.jumlah_sampel",
        ]);
    }

    public function test_finalization_rules_locking_and_copy_tim_tahap_2(): void
    {
        AuditTahapAwal::where('audit_sesi_id', $this->sesi->id)->update([
            'tim_audit_tahap_2' => [
                ['nama' => 'Jenry Tolu', 'jabatan' => 'Ketua Tim Audit', 'no_registrasi' => 'REG-01'],
                ['nama' => 'Fiska Polii', 'jabatan' => 'Auditor', 'no_registrasi' => 'REG-02'],
                ['nama' => 'Budi Observer', 'jabatan' => 'Observer', 'no_registrasi' => ''],
            ],
        ]);

        // Salin Tim dari Tahap II
        $this->actingAs($this->auditorSmkp)
            ->post(route('admin.audit-sesi.rencana.copy-tim', $this->sesi->id))
            ->assertRedirect(route('admin.audit-sesi.rencana.edit', $this->sesi->id));

        $rencana = AuditRencana::where('audit_sesi_id', $this->sesi->id)->with('tims')->first();
        $this->assertSame(['lead_auditor', 'auditor', 'pengamat'], $rencana->tims->pluck('peran')->all());

        // Finalisasi gagal jika jadwal belum punya rapat pembukaan & penutupan
        $this->actingAs($this->auditorSmkp)
            ->post(route('admin.audit-sesi.rencana.finalize', $this->sesi->id))
            ->assertSessionHasErrors(['jadwals']);

        // Isi jadwal valid (hari 1 pembukaan, hari 2 penutupan) + sampel Slovin
        $sampelId = $rencana->sampels()->first()->id;
        $this->actingAs($this->auditorSmkp)
            ->put(route('admin.audit-sesi.rencana.update', $this->sesi->id), [
                'no_dokumen' => 'TT-MGT-FRS-026A',
                'tims'       => [
                    ['nama' => 'Jenry Tolu', 'jabatan' => 'Ketua Tim', 'peran' => 'lead_auditor'],
                    ['nama' => 'Fiska Polii', 'jabatan' => 'Auditor', 'peran' => 'auditor'],
                ],
                'jadwals'    => [
                    [
                        'hari_ke'        => 1,
                        'tanggal'        => '2026-03-10',
                        'jam_mulai'      => '08:00',
                        'jam_selesai'    => '09:00',
                        'jenis_kegiatan' => 'rapat_pembukaan',
                        'kegiatan'       => 'Rapat Pembukaan',
                    ],
                    [
                        'hari_ke'        => 2,
                        'tanggal'        => '2026-03-11',
                        'jam_mulai'      => '15:00',
                        'jam_selesai'    => '16:30',
                        'jenis_kegiatan' => 'rapat_penutupan',
                        'kegiatan'       => 'Rapat Penutupan',
                    ],
                ],
                'sampels'    => [
                    $sampelId => [
                        'jenis_data'       => 'primer',
                        'metode'           => ['wawancara', 'observasi'],
                        'teknik_sampling'  => 'simple_random',
                        'metode_penentuan' => 'slovin',
                        'populasi_n'       => 100,
                        'margin_error_e'   => '0.05',
                        'jumlah_sampel'    => '',
                        'deskripsi_sampel' => '80 pekerja operasional tambang di area Pit',
                    ],
                ],
            ])
            ->assertRedirect(route('admin.audit-sesi.rencana.edit', $this->sesi->id));

        // Pastikan Slovin terhitung di server = 80
        $this->assertSame(80, $rencana->sampels()->find($sampelId)->jumlah_sampel);

        // Finalisasi sukses
        $this->actingAs($this->auditorSmkp)
            ->post(route('admin.audit-sesi.rencana.finalize', $this->sesi->id))
            ->assertRedirect(route('admin.audit-sesi.rencana.edit', $this->sesi->id));

        $rencana->refresh();
        $this->assertSame('final', $rencana->status);

        // Setelah final, auditor_smkp ditolak 403 saat mencoba ubah
        $this->actingAs($this->auditorSmkp)
            ->put(route('admin.audit-sesi.rencana.update', $this->sesi->id), [
                'no_dokumen' => 'TT-MGT-FRS-026A-REV',
            ])
            ->assertForbidden();

        // Admin tetap boleh mengubah dan tercatat di AuditLog
        $this->flushSession();
        $this->actingAs($this->admin)
            ->put(route('admin.audit-sesi.rencana.update', $this->sesi->id), [
                'no_dokumen' => 'TT-MGT-FRS-026A-REV',
            ])
            ->assertRedirect();
        $this->assertSame('TT-MGT-FRS-026A-REV', $rencana->fresh()->no_dokumen);
    }

    public function test_auditor_area_scoping_and_print_access(): void
    {
        AuditRencana::getOrInitializeForSesi($this->sesi);

        // Auditor area cocok boleh baca & cetak
        $this->actingAs($this->auditorMatch)
            ->get(route('auditor.audit-sesi.rencana.show', $this->sesi->id))
            ->assertOk()
            ->assertSee('Mode <strong>Hanya Baca</strong>', false);

        $this->actingAs($this->auditorMatch)
            ->get(route('auditor.audit-sesi.rencana.cetak', $this->sesi->id))
            ->assertOk()
            ->assertSee('FORMULIR RENCANA AUDIT INTERNAL SMKP MINERBA');

        // Auditor area cocok ditolak 403 pada route tulis admin
        $this->actingAs($this->auditorMatch)
            ->put(route('admin.audit-sesi.rencana.update', $this->sesi->id), ['no_dokumen' => 'X'])
            ->assertForbidden();

        // Auditor area lain ditolak 404
        $this->flushSession();
        $this->actingAs($this->auditorOther)
            ->get(route('auditor.audit-sesi.rencana.show', $this->sesi->id))
            ->assertNotFound();

        $this->actingAs($this->auditorOther)
            ->get(route('auditor.audit-sesi.rencana.cetak', $this->sesi->id))
            ->assertNotFound();
    }
}
