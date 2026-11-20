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

class PrivateAttachmentTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $auditorAlpha;
    protected User $auditorBeta;
    protected Perusahaan $companyAlpha;
    protected AuditSesi $sessionAlpha;
    protected AuditDetail $detailAlpha;
    protected Pica $picaAlpha;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        Storage::fake('attachments');

        $this->companyAlpha = Perusahaan::create([
            'nama_perusahaan' => 'PT Alpha Gold',
            'kode_perusahaan' => 'AG',
            'is_active'       => true,
        ]);

        $this->admin = User::factory()->create([
            'role'      => 'admin',
            'is_active' => true,
        ]);

        $this->auditorAlpha = User::factory()->create([
            'role'      => 'auditor',
            'area'      => 'PT Alpha Gold',
            'is_active' => true,
        ]);

        $this->auditorBeta = User::factory()->create([
            'role'      => 'auditor',
            'area'      => 'PT Beta Site',
            'is_active' => true,
        ]);

        $this->sessionAlpha = AuditSesi::create([
            'user_id'         => $this->admin->id,
            'perusahaan_id'   => $this->companyAlpha->id,
            'tahun_periode'   => 2024,
            'tanggal_mulai'   => '2024-01-01',
            'tanggal_selesai' => '2024-01-05',
            'area_audit'      => 'Processing Plant Alpha',
            'status'          => 'berjalan',
        ]);

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

        $this->detailAlpha = AuditDetail::create([
            'audit_sesi_id' => $this->sessionAlpha->id,
            'kriteria_id'   => $kriteria->id,
            'nilai'         => 3,
            'is_na'         => false,
        ]);

        $this->picaAlpha = Pica::create([
            'audit_detail_id'           => $this->detailAlpha->id,
            'deskripsi_temuan'          => 'Temuan bukti private',
            'deskripsi_ketidaksesuaian' => 'Ketidaksesuaian private',
            'kategori_temuan'           => 'Kritikal',
            'status'                    => 'open',
        ]);
    }

    /**
     * Uploaded PICA proof is stored in private 'attachments' disk, NOT in 'public'.
     */
    public function test_uploaded_pica_proof_is_stored_in_attachments_disk(): void
    {
        $file = UploadedFile::fake()->create('bukti_perbaikan.pdf', 150, 'application/pdf');

        $response = $this->actingAs($this->auditorAlpha)
            ->put(route('auditor.pica.update', $this->picaAlpha->id), [
                'akar_masalah'    => 'Akar masalah ditemukan',
                'bukti_perbaikan' => $file,
            ]);

        $response->assertRedirect();

        $this->picaAlpha->refresh();
        $storedPath = $this->picaAlpha->bukti_perbaikan;

        $this->assertNotNull($storedPath);
        $this->assertStringStartsWith('bukti_pica/', $storedPath);

        // Verify stored in private attachments disk
        Storage::disk('attachments')->assertExists($storedPath);

        // Verify NOT stored in public disk
        Storage::disk('public')->assertMissing($storedPath);
    }

    /**
     * AttachmentController serves file from private disk with secure HTTP headers.
     */
    public function test_attachment_controller_serves_files_with_correct_security_headers(): void
    {
        $file = UploadedFile::fake()->create('laporan.pdf', 200, 'application/pdf');
        $storedPath = $file->store('bukti_pica', 'attachments');

        $this->picaAlpha->update(['bukti_perbaikan' => $storedPath]);

        $response = $this->actingAs($this->auditorAlpha)
            ->get(route('attachments.show', ['type' => 'pica', 'id' => $this->picaAlpha->id]));

        $response->assertStatus(200);
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Content-Type', 'application/pdf');

        $disposition = $response->headers->get('Content-Disposition');
        $this->assertStringContainsString('inline', $disposition);
        $this->assertStringContainsString('filename="' . basename($storedPath) . '"', $disposition);
    }

    /**
     * AttachmentController falls back to public disk for legacy unmigrated files.
     */
    public function test_attachment_controller_falls_back_to_public_disk_for_legacy_files(): void
    {
        // Stored ONLY in public disk
        $file = UploadedFile::fake()->create('legacy_bukti.png', 100, 'image/png');
        $storedPath = $file->store('bukti_pica', 'public');

        $this->picaAlpha->update(['bukti_perbaikan' => $storedPath]);

        // attachments disk does NOT have it
        Storage::disk('attachments')->assertMissing($storedPath);

        $response = $this->actingAs($this->auditorAlpha)
            ->get(route('attachments.show', ['type' => 'pica', 'id' => $this->picaAlpha->id]));

        $response->assertStatus(200);
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Content-Type', 'image/png');
        $this->assertStringContainsString('inline', $response->headers->get('Content-Disposition'));
    }

    /**
     * AttachmentController blocks unauthorized auditor from accessing files.
     */
    public function test_attachment_controller_blocks_unauthorized_auditor(): void
    {
        $file = UploadedFile::fake()->create('secret.pdf', 100, 'application/pdf');
        $storedPath = $file->store('bukti_pica', 'attachments');
        $this->picaAlpha->update(['bukti_perbaikan' => $storedPath]);

        // auditorBeta has different area
        $response = $this->actingAs($this->auditorBeta)
            ->get(route('attachments.show', ['type' => 'pica', 'id' => $this->picaAlpha->id]));

        $response->assertStatus(403);
    }

    /**
     * Unauthenticated guest is redirected to login.
     */
    public function test_attachment_controller_redirects_unauthenticated_user(): void
    {
        $response = $this->get(route('attachments.show', ['type' => 'pica', 'id' => $this->picaAlpha->id]));
        $response->assertRedirect(route('login'));
    }

    /**
     * Artisan migration command correctly copies files from public to attachments disk.
     */
    public function test_artisan_migration_command(): void
    {
        Storage::disk('public')->put('lampiran/sample1.pdf', 'dummy content 1');
        Storage::disk('public')->put('bukti_pica/sample2.png', 'dummy content 2');

        // Dry run: does not copy
        $this->artisan('attachments:migrate-to-private', ['--dry-run' => true])
            ->assertSuccessful();

        Storage::disk('attachments')->assertMissing('lampiran/sample1.pdf');

        // Actual migration with --delete-source
        $this->artisan('attachments:migrate-to-private', ['--delete-source' => true])
            ->assertSuccessful();

        Storage::disk('attachments')->assertExists('lampiran/sample1.pdf');
        Storage::disk('attachments')->assertExists('bukti_pica/sample2.png');
        Storage::disk('public')->assertMissing('lampiran/sample1.pdf');
        Storage::disk('public')->assertMissing('bukti_pica/sample2.png');
    }
}
