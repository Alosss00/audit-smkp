<?php

namespace Tests\Feature;

use App\Models\AuditDetail;
use App\Models\AuditOfi;
use App\Models\AuditRencana;
use App\Models\AuditSesi;
use App\Models\AuditTahapAwal;
use App\Models\Elemen;
use App\Models\Kriteria;
use App\Models\Perusahaan;
use App\Models\SubElemen;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Fase4NavigasiIntegrasiTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Perusahaan $perusahaan;
    protected AuditSesi $sesi;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->perusahaan = Perusahaan::create([
            'nama_perusahaan' => 'PT Integrasi Fase 4',
            'kode_perusahaan' => 'PT-F4',
            'kategori'        => 'Pemegang IUP',
            'jenis_usaha'     => 'tambang',
            'is_active'       => true,
        ]);

        $elemen = Elemen::create(['kode_elemen' => 'I', 'nama_elemen' => 'Kebijakan', 'bobot' => 10]);
        $sub = SubElemen::create([
            'elemen_id'      => $elemen->id,
            'kode_sub'       => 'I.1',
            'nama_sub'       => 'Penyusunan Kebijakan',
            'nilai_maksimal' => 4,
            'is_na'          => false,
        ]);
        $kri = Kriteria::create([
            'sub_elemen_id'  => $sub->id,
            'kode_kriteria'  => 'I.1.a',
            'deskripsi'      => 'Kriteria 1',
            'nilai_maksimal' => 4,
        ]);

        $this->sesi = AuditSesi::create([
            'user_id'         => $this->admin->id,
            'perusahaan_id'   => $this->perusahaan->id,
            'tahun_periode'   => 2026,
            'tanggal_mulai'   => '2026-05-01',
            'tanggal_selesai' => '2026-05-04',
            'area_audit'      => $this->perusahaan->nama_perusahaan,
            'status'          => 'berjalan',
            'skor_akhir'      => 100,
        ]);

        AuditTahapAwal::createEmptyForSesi($this->sesi);
        AuditDetail::create([
            'audit_sesi_id' => $this->sesi->id,
            'kriteria_id'   => $kri->id,
            'nilai'         => 4,
            'is_na'         => false,
        ]);
    }

    public function test_perangkat_audit_7_steps_status_and_navigation_rendering(): void
    {
        AuditRencana::getOrInitializeForSesi($this->sesi);
        $ofi = new AuditOfi([
            'deskripsi'   => 'OFI Uji',
            'rekomendasi' => 'Rekomendasi Uji',
            'dibuat_oleh' => $this->admin->id,
        ]);
        $ofi->audit_sesi_id = $this->sesi->id;
        $ofi->nomor_urut = 1;
        $ofi->save();

        $steps = $this->sesi->getPerangkatAuditSteps();
        $this->assertCount(7, $steps);
        $this->assertSame(['tahap_awal', 'rencana', 'matrix', 'kesesuaian', 'pica', 'ofi', 'rekap'], array_column($steps, 'key'));

        // Verifikasi panel 7 langkah tampil di Rekap, Tahap Awal, Rencana, dan OFI
        $this->actingAs($this->admin)
            ->get(route('admin.audit-sesi.rekap', $this->sesi->id))
            ->assertOk()
            ->assertSee('Perangkat Audit SMKP (7 Langkah)');

        $this->actingAs($this->admin)
            ->get(route('admin.audit-sesi.rencana.edit', $this->sesi->id))
            ->assertOk()
            ->assertSee('Perangkat Audit SMKP (7 Langkah)');

        $this->actingAs($this->admin)
            ->get(route('admin.audit-sesi.ofi.index', $this->sesi->id))
            ->assertOk()
            ->assertSee('Perangkat Audit SMKP (7 Langkah)');
    }

    public function test_cetak_lengkap_includes_available_forms_checklist(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.audit-sesi.cetak-lengkap', $this->sesi->id))
            ->assertOk()
            ->assertSee('Daftar Formulir Perangkat Audit Tersedia')
            ->assertSee('Rencana Audit')
            ->assertSee('Kesesuaian');
    }
}
