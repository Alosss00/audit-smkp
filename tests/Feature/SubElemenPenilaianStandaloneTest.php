<?php

namespace Tests\Feature;

use App\Models\AuditDetail;
use App\Models\AuditSesi;
use App\Models\Elemen;
use App\Models\Kriteria;
use App\Models\SubElemen;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubElemenPenilaianStandaloneTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Elemen $elemen;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role'      => 'admin',
            'is_active' => true,
        ]);

        $this->elemen = Elemen::create([
            'kode_elemen' => 'TEST',
            'nama_elemen' => 'Elemen Uji Standalone',
            'bobot'       => 10.00,
        ]);
    }

    public function test_sub_elemen_without_kriterias_auto_creates_standalone_kriteria()
    {
        $sub = SubElemen::create([
            'elemen_id'      => $this->elemen->id,
            'kode_sub'       => 'TEST.1',
            'nama_sub'       => 'Sub Elemen Tanpa Sub-sub',
            'nilai_maksimal' => 6.00,
        ]);

        $sub->syncDefaultKriteria();

        $this->assertDatabaseHas('kriterias', [
            'sub_elemen_id'  => $sub->id,
            'kode_kriteria'  => 'TEST.1',
            'deskripsi'      => 'Sub Elemen Tanpa Sub-sub',
            'nilai_maksimal' => 6.00,
        ]);
    }

    public function test_audit_sesi_matrix_includes_standalone_sub_elemen()
    {
        $sub = SubElemen::create([
            'elemen_id'      => $this->elemen->id,
            'kode_sub'       => 'TEST.2',
            'nama_sub'       => 'Sub Elemen Standalone Audit',
            'nilai_maksimal' => 4.00,
        ]);
        $sub->syncDefaultKriteria();

        $this->actingAs($this->admin);

        $response = $this->post(route('admin.audit-sesi.store'), [
            'area_selection'  => 'Area Test Standalone',
            'tanggal_mulai'   => now()->toDateString(),
            'tanggal_selesai' => now()->addDays(2)->toDateString(),
        ]);

        $sesi = AuditSesi::where('area_audit', 'Area Test Standalone')->first();
        $this->assertNotNull($sesi);

        $kriteria = Kriteria::where('kode_kriteria', 'TEST.2')->first();
        $this->assertNotNull($kriteria);

        $detail = AuditDetail::where('audit_sesi_id', $sesi->id)
            ->where('kriteria_id', $kriteria->id)
            ->first();
        $this->assertNotNull($detail);

        // Update score via matrix
        $updateResp = $this->post(route('admin.audit-sesi.matrix.update', $sesi->id), [
            'details' => [
                $detail->id => [
                    'nilai'   => 3,
                    'is_na'   => 0,
                    'catatan' => 'Penerapan bagus',
                ],
            ],
        ]);

        $updateResp->assertRedirect();
        $detail->refresh();
        $this->assertEquals(3, $detail->nilai);
    }

    public function test_adding_explicit_child_kriteria_removes_standalone_kriteria()
    {
        $sub = SubElemen::create([
            'elemen_id'      => $this->elemen->id,
            'kode_sub'       => 'TEST.3',
            'nama_sub'       => 'Sub Elemen Transisi',
            'nilai_maksimal' => 4.00,
        ]);
        $sub->syncDefaultKriteria();

        $this->assertDatabaseHas('kriterias', ['kode_kriteria' => 'TEST.3']);

        $this->actingAs($this->admin);

        // Create explicit child kriteria TEST.3.1
        $response = $this->post(route('admin.kriterias.store'), [
            'sub_elemen_id' => $sub->id,
            'kode_kriteria' => 'TEST.3.1',
            'deskripsi'     => 'Sub-sub kriteria pertama',
            'pedoman_nilai' => [
                '0' => 'Pedoman 0',
                '1' => 'Pedoman 1',
                '2' => 'Pedoman 2',
                '3' => 'Pedoman 3',
                '4' => 'Pedoman 4',
            ],
        ]);

        $response->assertRedirect();

        // Standalone TEST.3 should be removed, and TEST.3.1 present
        $this->assertDatabaseMissing('kriterias', ['kode_kriteria' => 'TEST.3', 'deleted_at' => null]);
        $this->assertDatabaseHas('kriterias', ['kode_kriteria' => 'TEST.3.1']);
    }
}
