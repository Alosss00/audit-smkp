<?php

namespace Tests\Feature;

use App\Models\AuditDetail;
use App\Models\AuditSesi;
use App\Models\Elemen;
use App\Models\Kriteria;
use App\Models\KriteriaGatingRule;
use App\Models\SubElemen;
use App\Models\User;
use App\Services\GatingRuleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GatingRuleServiceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Elemen $elemen;
    protected SubElemen $subHulu;
    protected SubElemen $subHilir;
    protected Kriteria $kriteriaHulu;
    protected Kriteria $kriteriaHilir;
    protected AuditSesi $sesi;
    protected AuditDetail $detailHulu;
    protected AuditDetail $detailHilir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role'      => 'admin',
            'is_active' => true,
        ]);

        $this->elemen = Elemen::create([
            'kode_elemen' => 'I',
            'nama_elemen' => 'Elemen Uji Kebijakan',
            'bobot'       => 10.00,
        ]);

        $this->subHulu = SubElemen::create([
            'elemen_id'      => $this->elemen->id,
            'kode_sub'       => 'I.2',
            'nama_sub'       => 'Isi Kebijakan',
            'nilai_maksimal' => 4.0,
        ]);

        $this->subHilir = SubElemen::create([
            'elemen_id'      => $this->elemen->id,
            'kode_sub'       => 'II.4',
            'nama_sub'       => 'Tujuan dan Program',
            'nilai_maksimal' => 4.0,
        ]);

        $this->kriteriaHulu = Kriteria::create([
            'sub_elemen_id'  => $this->subHulu->id,
            'kode_kriteria'  => 'I.2',
            'deskripsi'      => 'Kriteria Hulu Kebijakan',
            'nilai_maksimal' => 4.0,
        ]);

        $this->kriteriaHilir = Kriteria::create([
            'sub_elemen_id'  => $this->subHilir->id,
            'kode_kriteria'  => 'II.4',
            'deskripsi'      => 'Kriteria Hilir Program',
            'nilai_maksimal' => 4.0,
        ]);

        $this->sesi = AuditSesi::create([
            'user_id'         => $this->admin->id,
            'tanggal_mulai'   => now()->toDateString(),
            'tanggal_selesai' => now()->addDays(2)->toDateString(),
            'area_audit'      => 'Site Test',
            'status'          => 'draft',
            'skor_akhir'      => 0.0,
        ]);

        $this->detailHulu = AuditDetail::create([
            'audit_sesi_id' => $this->sesi->id,
            'kriteria_id'   => $this->kriteriaHulu->id,
            'nilai'         => 1,
            'is_na'         => false,
        ]);

        $this->detailHilir = AuditDetail::create([
            'audit_sesi_id' => $this->sesi->id,
            'kriteria_id'   => $this->kriteriaHilir->id,
            'nilai'         => 4,
            'is_na'         => false,
        ]);
    }

    public function test_hard_block_triggers_violation_when_downstream_exceeds_threshold(): void
    {
        KriteriaGatingRule::create([
            'kriteria_hulu_id'  => $this->kriteriaHulu->id,
            'kriteria_hilir_id' => $this->kriteriaHilir->id,
            'ambang_hulu'       => 2.00,
            'skor_maks_hilir'   => 2.00,
            'mode'              => 'hard_block',
            'deskripsi_simpul'  => 'Simpul Uji Hard Block',
            'is_active'         => true,
        ]);

        $service = app(GatingRuleService::class);
        $violations = $service->evaluate($this->sesi);

        $this->assertCount(1, $violations);
        $this->assertEquals('hard_block', $violations->first()['mode']);
        $this->assertTrue($service->hasHardBlockViolations($this->sesi));
    }

    public function test_hard_block_passes_when_downstream_is_within_limit(): void
    {
        KriteriaGatingRule::create([
            'kriteria_hulu_id'  => $this->kriteriaHulu->id,
            'kriteria_hilir_id' => $this->kriteriaHilir->id,
            'ambang_hulu'       => 2.00,
            'skor_maks_hilir'   => 2.00,
            'mode'              => 'hard_block',
            'deskripsi_simpul'  => 'Simpul Uji Hard Block',
            'is_active'         => true,
        ]);

        // Downstream score is 2 (within max limit 2)
        $this->detailHilir->update(['nilai' => 2]);

        $service = app(GatingRuleService::class);
        $this->assertFalse($service->hasHardBlockViolations($this->sesi));
    }

    public function test_dynamic_benchmark_soft_flag_warns_when_downstream_exceeds_upstream(): void
    {
        KriteriaGatingRule::create([
            'kriteria_hulu_id'  => $this->kriteriaHulu->id,
            'kriteria_hilir_id' => $this->kriteriaHilir->id,
            'ambang_hulu'       => null,
            'skor_maks_hilir'   => null,
            'mode'              => 'soft_flag',
            'deskripsi_simpul'  => 'Simpul Uji Soft Flag Dinamis',
            'is_active'         => true,
        ]);

        // Hulu = 3, Hilir = 4 (Hilir exceeds Hulu)
        $this->detailHulu->update(['nilai' => 3]);
        $this->detailHilir->update(['nilai' => 4]);

        $service = app(GatingRuleService::class);
        $warnings = $service->getSoftFlagWarnings($this->sesi);

        $this->assertCount(1, $warnings);
        $this->assertStringContainsString('melebihi batas gating', $warnings[0]);
    }

    public function test_controller_rejects_save_when_hard_block_is_violated(): void
    {
        KriteriaGatingRule::create([
            'kriteria_hulu_id'  => $this->kriteriaHulu->id,
            'kriteria_hilir_id' => $this->kriteriaHilir->id,
            'ambang_hulu'       => 2.00,
            'skor_maks_hilir'   => 2.00,
            'mode'              => 'hard_block',
            'deskripsi_simpul'  => 'Simpul Uji Controller',
            'is_active'         => true,
        ]);

        $payload = [
            'details' => [
                $this->detailHulu->id  => ['nilai' => 1, 'is_na' => 0],
                $this->detailHilir->id => ['nilai' => 4, 'is_na' => 0],
            ],
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.audit-sesi.matrix.update', $this->sesi->id), $payload);

        $response->assertSessionHas('error');
    }
}
