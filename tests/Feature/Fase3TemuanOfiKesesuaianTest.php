<?php

namespace Tests\Feature;

use App\Models\AuditDetail;
use App\Models\AuditOfi;
use App\Models\AuditSesi;
use App\Models\Elemen;
use App\Models\Kriteria;
use App\Models\Perusahaan;
use App\Models\Pica;
use App\Models\SubElemen;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Fase3TemuanOfiKesesuaianTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $auditorSmkp;
    protected User $auditorMatch;
    protected User $auditorOther;
    protected Perusahaan $perusahaan;
    protected AuditSesi $sesi;
    protected Elemen $elemen;
    protected SubElemen $subSesuai;
    protected SubElemen $subTemuan;
    protected Kriteria $kriSesuai;
    protected Kriteria $kriTemuan1;
    protected Kriteria $kriTemuan2;
    protected AuditDetail $detailSesuai;
    protected AuditDetail $detailTemuan1;
    protected AuditDetail $detailTemuan2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->auditorSmkp = User::factory()->create(['role' => 'auditor_smkp', 'is_active' => true]);
        $this->auditorMatch = User::factory()->create(['role' => 'auditor', 'area' => 'PT Fase Tiga', 'is_active' => true]);
        $this->auditorOther = User::factory()->create(['role' => 'auditor', 'area' => 'PT Area Lain', 'is_active' => true]);

        $this->perusahaan = Perusahaan::create([
            'nama_perusahaan' => 'PT Fase Tiga',
            'kode_perusahaan' => 'PT-F3',
            'kategori'        => 'Pemegang IUP',
            'is_active'       => true,
        ]);

        $this->elemen = Elemen::create([
            'kode_elemen' => 'I',
            'nama_elemen' => 'Kebijakan',
            'bobot'       => 10,
        ]);
        $this->subSesuai = SubElemen::create([
            'elemen_id'      => $this->elemen->id,
            'kode_sub'       => 'I.1',
            'nama_sub'       => 'Sub Elemen Sesuai Penuh',
            'nilai_maksimal' => 4,
            'is_na'          => false,
        ]);
        $this->subTemuan = SubElemen::create([
            'elemen_id'      => $this->elemen->id,
            'kode_sub'       => 'I.2',
            'nama_sub'       => 'Sub Elemen Dengan Temuan',
            'nilai_maksimal' => 4,
            'is_na'          => false,
        ]);
        $this->kriSesuai = Kriteria::create([
            'sub_elemen_id'  => $this->subSesuai->id,
            'kode_kriteria'  => 'I.1.a',
            'deskripsi'      => 'Kriteria Sesuai',
            'nilai_maksimal' => 4,
        ]);
        $this->kriTemuan1 = Kriteria::create([
            'sub_elemen_id'  => $this->subTemuan->id,
            'kode_kriteria'  => 'I.2.a',
            'deskripsi'      => 'Kriteria Temuan 1',
            'nilai_maksimal' => 2,
        ]);
        $this->kriTemuan2 = Kriteria::create([
            'sub_elemen_id'  => $this->subTemuan->id,
            'kode_kriteria'  => 'I.2.b',
            'deskripsi'      => 'Kriteria Temuan 2',
            'nilai_maksimal' => 2,
        ]);

        $this->sesi = AuditSesi::create([
            'user_id'         => $this->admin->id,
            'perusahaan_id'   => $this->perusahaan->id,
            'tahun_periode'   => 2026,
            'tanggal_mulai'   => '2026-04-10',
            'tanggal_selesai' => '2026-04-15',
            'area_audit'      => $this->perusahaan->nama_perusahaan,
            'status'          => 'berjalan',
            'skor_akhir'      => 0,
        ]);

        $this->detailSesuai = AuditDetail::create([
            'audit_sesi_id' => $this->sesi->id,
            'kriteria_id'   => $this->kriSesuai->id,
            'nilai'         => 4,
            'is_na'         => false,
            'catatan'       => 'Bukti SK Kebijakan KP No. 01/2026 lengkap.',
        ]);
        $this->detailTemuan1 = AuditDetail::create([
            'audit_sesi_id' => $this->sesi->id,
            'kriteria_id'   => $this->kriTemuan1->id,
            'nilai'         => 2,
            'is_na'         => false,
        ]);
        $this->detailTemuan2 = AuditDetail::create([
            'audit_sesi_id' => $this->sesi->id,
            'kriteria_id'   => $this->kriTemuan2->id,
            'nilai'         => 2,
            'is_na'         => false,
        ]);
    }

    public function test_pica_sequential_numbering_is_never_reused_after_delete(): void
    {
        // Trigger PICA #1 pada detailTemuan1 via updateMatrix
        $this->actingAs($this->admin)
            ->post(route('admin.audit-sesi.matrix.update', $this->sesi->id), [
                'details' => [
                    $this->detailTemuan1->id => ['nilai' => 1, 'catatan' => 'Temuan 1'],
                ],
            ])
            ->assertRedirect();

        $pica1 = Pica::where('audit_detail_id', $this->detailTemuan1->id)->first();
        $this->assertNotNull($pica1);
        $this->assertSame($this->sesi->id, $pica1->audit_sesi_id);
        $this->assertSame(1, $pica1->nomor_urut);

        // Perbaiki nilai menjadi maksimal -> PICA #1 ter-soft-delete
        $this->actingAs($this->admin)
            ->post(route('admin.audit-sesi.matrix.update', $this->sesi->id), [
                'details' => [
                    $this->detailTemuan1->id => ['nilai' => 2, 'catatan' => 'Sudah lengkap'],
                ],
            ])
            ->assertRedirect();

        $this->assertSoftDeleted('picas', ['id' => $pica1->id]);

        // Trigger PICA baru pada detailTemuan2 -> nomor_urut harus 2 (tidak memakai ulang 1!)
        $this->actingAs($this->admin)
            ->post(route('admin.audit-sesi.matrix.update', $this->sesi->id), [
                'details' => [
                    $this->detailTemuan2->id => ['nilai' => 0, 'catatan' => 'Temuan 2'],
                ],
            ])
            ->assertRedirect();

        $pica2 = Pica::where('audit_detail_id', $this->detailTemuan2->id)->first();
        $this->assertNotNull($pica2);
        $this->assertSame(2, $pica2->nomor_urut);
    }

    public function test_plor_approval_permissions_and_finalization_config(): void
    {
        $pica = new Pica([
            'audit_detail_id'           => $this->detailTemuan1->id,
            'deskripsi_temuan'          => 'Dokumen belum ditinjau',
            'deskripsi_ketidaksesuaian' => 'Dokumen belum ditinjau',
            'kategori_temuan'           => 'minor',
            'status'                    => 'open',
        ]);
        $pica->audit_sesi_id = $this->sesi->id;
        $pica->nomor_urut = 1;
        $pica->save();

        // 1. Jika plor_wajib_saat_finalisasi = true, finalisasi ditolak saat PLOR kosong
        config(['audit.plor_wajib_saat_finalisasi' => true]);
        $this->actingAs($this->admin)
            ->post(route('admin.audit-sesi.finalisasi', $this->sesi->id))
            ->assertSessionHas('error');
        $this->assertSame('berjalan', $this->sesi->fresh()->status);

        // 2. Role auditor TIDAK bisa mengisi PLOR atau persetujuan Lead Auditor
        $this->flushSession();
        $this->actingAs($this->auditorMatch)
            ->put(route('auditor.pica.update', $pica->id), [
                'akar_masalah'                  => 'Jadwal terlewat',
                'plor_problem'                  => 'Coba injeksi PLOR',
                'persetujuan_lead_auditor_oleh' => $this->auditorMatch->id,
                'persetujuan_auditi_nama'       => 'Wakil Auditi',
                'tanggal_persetujuan_auditi'    => '2026-04-16',
            ])
            ->assertRedirect();

        $pica->refresh();
        $this->assertNull($pica->plor_problem);
        $this->assertNull($pica->persetujuan_lead_auditor_oleh);
        $this->assertSame('Wakil Auditi', $pica->persetujuan_auditi_nama);

        // 3. Role auditor_smkp melengkapi PLOR + persetujuan Lead Auditor
        $this->flushSession();
        $this->actingAs($this->auditorSmkp)
            ->put(route('admin.pica.update', $pica->id), [
                'deskripsi_ketidaksesuaian' => 'Dokumen belum ditinjau',
                'plor_problem'              => 'Tinjauan kebijakan belum dilakukan',
                'plor_location'             => 'Kantor Site',
                'plor_objective_evidence'   => 'Dokumen SK-01 tanggal 10 Jan 2025 belum ada revisi 2026',
                'plor_reference'            => 'Kepdirjen 185 Elemen I.2',
                'status'                    => 'in_progress',
                'setujui_lead_auditor'      => 1,
                'tanggal_persetujuan_lead'  => '2026-04-16',
            ])
            ->assertRedirect();

        $pica->refresh();
        $this->assertTrue($pica->isPlorLengkap());
        $this->assertSame($this->auditorSmkp->id, $pica->persetujuan_lead_auditor_oleh);

        // 4. Cetak ketidaksesuaian memuat nomor temuan, PLOR, dan tabel rekapitulasi
        $this->actingAs($this->auditorSmkp)
            ->get(route('admin.audit-sesi.cetak-ketidaksesuaian', $this->sesi->id))
            ->assertOk()
            ->assertSee('Rekapitulasi Temuan Ketidaksesuaian')
            ->assertSee('Tinjauan kebijakan belum dilakukan')
            ->assertSee('Kantor Site');

        // 5. Finalisasi sekarang sukses meski plor_wajib_saat_finalisasi = true
        $this->flushSession();
        $this->actingAs($this->admin)
            ->post(route('admin.audit-sesi.finalisasi', $this->sesi->id))
            ->assertRedirect(route('admin.audit-sesi.rekap', $this->sesi->id));
        $this->assertSame('selesai', $this->sesi->fresh()->status);
    }

    public function test_ofi_crud_numbering_scoping_and_print(): void
    {
        // Create OFI #1
        $this->actingAs($this->auditorSmkp)
            ->post(route('admin.audit-sesi.ofi.store', $this->sesi->id), [
                'sub_elemen_id' => $this->subSesuai->id,
                'deskripsi'     => 'Digitalisasi formulir inspeksi',
                'rekomendasi'   => 'Gunakan aplikasi mobile inspeksi',
                'referensi'     => 'Elemen I.1',
            ])
            ->assertRedirect(route('admin.audit-sesi.ofi.index', $this->sesi->id));

        $ofi1 = AuditOfi::where('audit_sesi_id', $this->sesi->id)->first();
        $this->assertSame(1, $ofi1->nomor_urut);

        // Delete OFI #1 -> create OFI baru harus #2
        $this->actingAs($this->auditorSmkp)
            ->delete(route('admin.audit-sesi.ofi.destroy', [$this->sesi->id, $ofi1->id]))
            ->assertRedirect();

        $this->actingAs($this->auditorSmkp)
            ->post(route('admin.audit-sesi.ofi.store', $this->sesi->id), [
                'deskripsi'   => 'Peningkatan sosialisasi SMKP',
                'rekomendasi' => 'Tambahkan papan informasi di pit',
            ])
            ->assertRedirect();

        $ofi2 = AuditOfi::where('audit_sesi_id', $this->sesi->id)->first();
        $this->assertSame(2, $ofi2->nomor_urut);

        // Auditor area cocok boleh baca & cetak TT-MGT-FRS-026G
        $this->flushSession();
        $this->actingAs($this->auditorMatch)
            ->get(route('auditor.audit-sesi.ofi.cetak', $this->sesi->id))
            ->assertOk()
            ->assertSee('TT-MGT-FRS-026G')
            ->assertSee('Peningkatan sosialisasi SMKP');

        // Auditor area lain ditolak 404
        $this->flushSession();
        $this->actingAs($this->auditorOther)
            ->get(route('auditor.audit-sesi.ofi.cetak', $this->sesi->id))
            ->assertNotFound();
    }

    public function test_cetak_kesesuaian_lists_only_full_compliance_and_warns_early_date(): void
    {
        // Ubah detailTemuan1 jadi nilai kurang dari maks agar subTemuan tidak 100%
        $this->detailTemuan1->update(['nilai' => 1]);

        $res = $this->actingAs($this->admin)
            ->get(route('admin.audit-sesi.cetak-kesesuaian', ['id' => $this->sesi->id, 'tanggal_ttd' => '2026-04-12']));

        $res->assertOk()
            ->assertSee('TT-MGT-FRS-026E')
            ->assertSee('Sub Elemen Sesuai Penuh')
            ->assertSee('Bukti SK Kebijakan KP No. 01/2026 lengkap.')
            ->assertDontSee('Sub Elemen Dengan Temuan')
            ->assertSee('Peringatan: Tanggal tanda tangan formulir');

        // Auditor area lain ditolak 404
        $this->flushSession();
        $this->actingAs($this->auditorOther)
            ->get(route('auditor.audit-sesi.cetak-kesesuaian', $this->sesi->id))
            ->assertNotFound();
    }
}
