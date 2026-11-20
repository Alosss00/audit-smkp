<?php

namespace Tests\Feature;

use App\Models\AuditDetail;
use App\Models\AuditSesi;
use App\Models\Elemen;
use App\Models\Kriteria;
use App\Models\Perusahaan;
use App\Models\Pica;
use App\Models\SubElemen;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AreaScopingTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $auditorSmkp;
    protected User $auditorAreaMatch;
    protected User $auditorCompanyMatch;
    protected User $auditorOtherArea;
    protected User $auditorNoArea;

    protected Perusahaan $compAlpha;
    protected Perusahaan $compBeta;

    protected AuditSesi $sesiAreaMatch;
    protected AuditSesi $sesiCompanyMatch;
    protected AuditSesi $sesiOtherArea;

    protected Pica $picaAreaMatch;
    protected Pica $picaCompanyMatch;
    protected Pica $picaOtherArea;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        // Master Perusahaan
        $this->compAlpha = Perusahaan::create([
            'nama_perusahaan' => 'PT Alpha Mining',
            'kode_perusahaan' => 'ALPHA',
            'is_active' => true,
        ]);

        $this->compBeta = Perusahaan::create([
            'nama_perusahaan' => 'PT Beta Mining',
            'kode_perusahaan' => 'BETA',
            'is_active' => true,
        ]);

        // Users
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->auditorSmkp = User::factory()->create([
            'role' => 'auditor_smkp',
            'is_active' => true,
        ]);

        $this->auditorAreaMatch = User::factory()->create([
            'role' => 'auditor',
            'area' => 'Site Pit Alpha',
            'is_active' => true,
        ]);

        $this->auditorCompanyMatch = User::factory()->create([
            'role' => 'auditor',
            'area' => 'PT Alpha Mining',
            'is_active' => true,
        ]);

        $this->auditorOtherArea = User::factory()->create([
            'role' => 'auditor',
            'area' => 'Site Gamma Other',
            'is_active' => true,
        ]);

        $this->auditorNoArea = User::factory()->create([
            'role' => 'auditor',
            'area' => null,
            'is_active' => true,
        ]);

        // Master SMKP Hierarchy for Details
        $elemen = Elemen::firstOrCreate(
            ['kode_elemen' => 'I'],
            ['nama_elemen' => 'Kebijakan', 'bobot' => 10.00]
        );
        $subElemen = SubElemen::firstOrCreate(
            ['elemen_id' => $elemen->id, 'kode_sub' => '1.1'],
            ['nama_sub' => 'Penyusunan Kebijakan', 'nilai_maksimal' => 10]
        );
        $kriteria = Kriteria::firstOrCreate(
            ['sub_elemen_id' => $subElemen->id, 'kode_kriteria' => '1.1.1'],
            ['deskripsi' => 'Kebijakan K3', 'nilai_maksimal' => 4]
        );

        // Dummy attachments
        $dummyFilePica = UploadedFile::fake()->create('bukti.pdf', 100);
        $pathPica = $dummyFilePica->store('bukti_pica', 'public');

        $dummyFileLampiran = UploadedFile::fake()->create('dokumen.pdf', 100);
        $pathLampiran = $dummyFileLampiran->store('lampiran', 'public');

        // Sessions
        // 1. Matches by area_audit ('Site Pit Alpha')
        $this->sesiAreaMatch = AuditSesi::create([
            'user_id'         => $this->auditorSmkp->id,
            'perusahaan_id'   => $this->compAlpha->id,
            'tahun_periode'   => 2024,
            'tanggal_mulai'   => '2024-01-01',
            'tanggal_selesai' => '2024-01-05',
            'area_audit'      => 'Site Pit Alpha',
            'status'          => 'berjalan',
        ]);
        $detail1 = AuditDetail::create([
            'audit_sesi_id' => $this->sesiAreaMatch->id,
            'kriteria_id'   => $kriteria->id,
            'nilai'         => 3,
            'is_na'         => false,
            'lampiran'      => $pathLampiran,
        ]);
        $this->picaAreaMatch = Pica::create([
            'audit_detail_id'           => $detail1->id,
            'deskripsi_temuan'          => 'Temuan area alpha',
            'deskripsi_ketidaksesuaian' => 'Ketidaksesuaian area alpha',
            'kategori_temuan'           => 'Kritikal',
            'status'                    => 'open',
            'bukti_perbaikan'           => $pathPica,
        ]);

        // 2. Matches by company name ('PT Alpha Mining'), but different area_audit ('Processing Plant X')
        $this->sesiCompanyMatch = AuditSesi::create([
            'user_id'         => $this->auditorSmkp->id,
            'perusahaan_id'   => $this->compAlpha->id,
            'tahun_periode'   => 2024,
            'tanggal_mulai'   => '2024-01-01',
            'tanggal_selesai' => '2024-01-05',
            'area_audit'      => 'Processing Plant X',
            'status'          => 'berjalan',
        ]);
        $detail2 = AuditDetail::create([
            'audit_sesi_id' => $this->sesiCompanyMatch->id,
            'kriteria_id'   => $kriteria->id,
            'nilai'         => 3,
            'is_na'         => false,
            'lampiran'      => $pathLampiran,
        ]);
        $this->picaCompanyMatch = Pica::create([
            'audit_detail_id'           => $detail2->id,
            'deskripsi_temuan'          => 'Temuan company match',
            'deskripsi_ketidaksesuaian' => 'Ketidaksesuaian company match',
            'kategori_temuan'           => 'Kritikal',
            'status'                    => 'open',
            'bukti_perbaikan'           => $pathPica,
        ]);

        // 3. Different area and different company
        $this->sesiOtherArea = AuditSesi::create([
            'user_id'         => $this->auditorSmkp->id,
            'perusahaan_id'   => $this->compBeta->id,
            'tahun_periode'   => 2024,
            'tanggal_mulai'   => '2024-01-01',
            'tanggal_selesai' => '2024-01-05',
            'area_audit'      => 'Site Beta Pit',
            'status'          => 'berjalan',
        ]);
        $detail3 = AuditDetail::create([
            'audit_sesi_id' => $this->sesiOtherArea->id,
            'kriteria_id'   => $kriteria->id,
            'nilai'         => 3,
            'is_na'         => false,
            'lampiran'      => $pathLampiran,
        ]);
        $this->picaOtherArea = Pica::create([
            'audit_detail_id'           => $detail3->id,
            'deskripsi_temuan'          => 'Temuan other area',
            'deskripsi_ketidaksesuaian' => 'Ketidaksesuaian other area',
            'kategori_temuan'           => 'Kritikal',
            'status'                    => 'open',
            'bukti_perbaikan'           => $pathPica,
        ]);
    }

    /**
     * Auditor can see sessions and PICA that match area_audit.
     */
    public function test_auditor_sees_sessions_matching_area_audit(): void
    {
        $response = $this->actingAs($this->auditorAreaMatch)
            ->get(route('auditor.audit-sesi.index'));
        $response->assertStatus(200);
        $response->assertSee('Site Pit Alpha');
        $response->assertDontSee('Site Beta Pit');

        $picaResponse = $this->actingAs($this->auditorAreaMatch)
            ->get(route('auditor.pica.index'));
        $picaResponse->assertStatus(200);
        $picaResponse->assertSee('Site Pit Alpha');
        $picaResponse->assertDontSee('Site Beta Pit');
    }

    /**
     * Auditor can see sessions and PICA that match company name even if area_audit differs.
     */
    public function test_auditor_sees_sessions_matching_company_name(): void
    {
        $response = $this->actingAs($this->auditorCompanyMatch)
            ->get(route('auditor.audit-sesi.index'));
        $response->assertStatus(200);
        $response->assertSee('PT Alpha Mining');
        $response->assertDontSee('PT Beta Mining');

        // PICA index
        $picaResponse = $this->actingAs($this->auditorCompanyMatch)
            ->get(route('auditor.pica.index'));
        $picaResponse->assertStatus(200);
        $picaResponse->assertSee('Processing Plant X');
        $picaResponse->assertSee('Site Pit Alpha');
        $picaResponse->assertDontSee('Site Beta Pit');

        // PICA edit & update
        $editResponse = $this->actingAs($this->auditorCompanyMatch)
            ->get(route('auditor.pica.edit', $this->picaCompanyMatch->id));
        $editResponse->assertStatus(200);

        $updateResponse = $this->actingAs($this->auditorCompanyMatch)
            ->put(route('auditor.pica.update', $this->picaCompanyMatch->id), [
                'akar_masalah' => 'Akar masalah diperbarui oleh auditee',
            ]);
        $updateResponse->assertRedirect();
        $this->assertDatabaseHas('picas', [
            'id' => $this->picaCompanyMatch->id,
            'akar_masalah' => 'Akar masalah diperbarui oleh auditee',
        ]);
    }

    /**
     * Auditor cannot see or access sessions and PICA of other areas.
     */
    public function test_auditor_cannot_see_sessions_of_other_area(): void
    {
        // 404 on session rekap
        $this->actingAs($this->auditorAreaMatch)
            ->get(route('auditor.audit-sesi.rekap', $this->sesiOtherArea->id))
            ->assertStatus(404);

        // 404 on PICA edit
        $this->actingAs($this->auditorAreaMatch)
            ->get(route('auditor.pica.edit', $this->picaOtherArea->id))
            ->assertStatus(404);

        // 404 on PICA update
        $this->actingAs($this->auditorAreaMatch)
            ->put(route('auditor.pica.update', $this->picaOtherArea->id), [
                'akar_masalah' => 'Unauthorized update',
            ])
            ->assertStatus(404);
    }

    /**
     * Auditor without assigned area receives 403 Forbidden.
     */
    public function test_auditor_without_area_gets_403(): void
    {
        $this->actingAs($this->auditorNoArea)
            ->get(route('auditor.audit-sesi.index'))
            ->assertStatus(403);

        $this->actingAs($this->auditorNoArea)
            ->get(route('auditor.pica.index'))
            ->assertStatus(403);
    }

    /**
     * Admin is not restricted by area scoping.
     */
    public function test_admin_not_restricted(): void
    {
        $adminResponse = $this->actingAs($this->admin)
            ->get(route('admin.audit-sesi.index'));
        $adminResponse->assertStatus(200);
    }

    /**
     * Auditor SMKP is not restricted by area scoping.
     */
    public function test_auditor_smkp_not_restricted(): void
    {
        $smkpResponse = $this->actingAs($this->auditorSmkp)
            ->get(route('admin.audit-sesi.index'));
        $smkpResponse->assertStatus(200);
    }

    /**
     * AttachmentController allows auditor with matching area.
     */
    public function test_attachment_controller_allows_auditor_with_matching_area(): void
    {
        $this->actingAs($this->auditorAreaMatch)
            ->get(route('attachments.show', ['type' => 'pica', 'id' => $this->picaAreaMatch->id]))
            ->assertStatus(200);
    }

    /**
     * AttachmentController allows auditor with matching company.
     */
    public function test_attachment_controller_allows_auditor_with_matching_company(): void
    {
        $this->actingAs($this->auditorCompanyMatch)
            ->get(route('attachments.show', ['type' => 'pica', 'id' => $this->picaCompanyMatch->id]))
            ->assertStatus(200);
    }

    /**
     * AttachmentController denies auditor with other area.
     */
    public function test_attachment_controller_denies_auditor_with_other_area(): void
    {
        $this->actingAs($this->auditorOtherArea)
            ->get(route('attachments.show', ['type' => 'pica', 'id' => $this->picaAreaMatch->id]))
            ->assertStatus(403);
    }

    /**
     * AttachmentController allows lampiran for matching auditor.
     */
    public function test_attachment_controller_allows_lampiran_for_matching_auditor(): void
    {
        $detailId = $this->picaAreaMatch->audit_detail_id;
        $this->actingAs($this->auditorAreaMatch)
            ->get(route('attachments.show', ['type' => 'lampiran', 'id' => $detailId]))
            ->assertStatus(200);
    }

    /**
     * AttachmentController denies lampiran for auditor with other area.
     */
    public function test_attachment_controller_denies_lampiran_for_other_auditor(): void
    {
        $detailId = $this->picaAreaMatch->audit_detail_id;
        $this->actingAs($this->auditorOtherArea)
            ->get(route('attachments.show', ['type' => 'lampiran', 'id' => $detailId]))
            ->assertStatus(403);
    }
}
