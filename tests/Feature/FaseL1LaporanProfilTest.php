<?php

namespace Tests\Feature;

use App\Models\AuditLaporan;
use App\Models\AuditLaporanNarasiElemen;
use App\Models\AuditLaporanPeralatan;
use App\Models\AuditLog;
use App\Models\AuditRencana;
use App\Models\AuditSesi;
use App\Models\AuditTahapAwal;
use App\Models\Elemen;
use App\Models\Perusahaan;
use App\Models\PerusahaanLegalitas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FaseL1LaporanProfilTest extends TestCase
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

    protected function makePerusahaan(string $nama = 'PT Contoh Tambang', string $jenisUsaha = 'tambang'): Perusahaan
    {
        return Perusahaan::create([
            'nama_perusahaan' => $nama,
            'kode_perusahaan' => 'CT-' . rand(100, 999),
            'kategori'        => 'Pemegang IUP',
            'jenis_usaha'     => $jenisUsaha,
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
            'status'          => 'draft',
            'skor_akhir'      => 0,
        ]);
    }

    public function test_admin_can_save_and_update_legalitas_1_per_perusahaan_and_non_admin_is_403(): void
    {
        $admin = $this->makeUser('admin');
        $auditorSmkp = $this->makeUser('auditor_smkp');
        $perusahaan = $this->makePerusahaan('PT Contoh Legalitas');

        // Admin creates legalitas
        $res = $this->actingAs($admin)->put(route('admin.perusahaans.legalitas.update', $perusahaan->id), [
            'jenis_izin'                            => 'iup_operasi_produksi',
            'nomor_sk_izin'                         => 'SK-001/IUP/2025',
            'tanggal_sk'                            => '2025-01-10',
            'tanggal_mulai_berlaku'                 => '2025-01-10',
            'tanggal_berakhir'                      => '2035-01-10',
            'status_izin'                           => 'Operasi Produksi',
            'kode_wiup'                             => 'WIUP-123456',
            'luas_ha'                               => 1250.50,
            'lokasi_desa'                           => 'Desa Contoh',
            'lokasi_kecamatan'                      => 'Kecamatan Contoh',
            'lokasi_kabupaten'                      => 'Kabupaten Contoh',
            'lokasi_provinsi'                       => 'Provinsi Contoh',
            'nomor_persetujuan_studi_kelayakan'     => 'FS-001/2024',
            'tanggal_persetujuan_studi_kelayakan'   => '2024-06-15',
            'nomor_persetujuan_dokumen_lingkungan'  => 'AMDAL-001/2024',
            'tanggal_persetujuan_dokumen_lingkungan'=> '2024-08-20',
        ]);

        $res->assertRedirect(route('admin.perusahaans.index'));
        $this->assertDatabaseHas('perusahaan_legalitas', [
            'perusahaan_id' => $perusahaan->id,
            'jenis_izin'    => 'iup_operasi_produksi',
            'kode_wiup'     => 'WIUP-123456',
        ]);

        // Update again — remains 1 row per perusahaan
        $this->actingAs($admin)->put(route('admin.perusahaans.legalitas.update', $perusahaan->id), [
            'jenis_izin'  => 'iup_operasi_produksi',
            'kode_wiup'   => 'WIUP-999999',
            'status_izin' => 'Aktif Diperpanjang',
        ])->assertRedirect(route('admin.perusahaans.index'));

        $this->assertEquals(1, PerusahaanLegalitas::where('perusahaan_id', $perusahaan->id)->count());
        $this->assertEquals('WIUP-999999', $perusahaan->fresh()->legalitas->kode_wiup);
        $this->assertTrue(AuditLog::where('modul', 'Legalitas Perusahaan')->exists());

        // Non-admin (auditor_smkp) gets 403
        $this->flushSession();
        $this->actingAs($auditorSmkp)
            ->put(route('admin.perusahaans.legalitas.update', $perusahaan->id), [
                'kode_wiup' => 'WIUP-BLOCKED',
            ])
            ->assertStatus(403);
    }

    public function test_rencana_audit_saves_structured_areas_and_pemandu_role(): void
    {
        $auditorSmkp = $this->makeUser('auditor_smkp');
        $perusahaan = $this->makePerusahaan('PT Contoh Rencana');
        $sesi = $this->makeSesi($perusahaan, $auditorSmkp, 2026);

        $res = $this->actingAs($auditorSmkp)->put(route('admin.audit-sesi.rencana.update', $sesi->id), [
            'no_dokumen'    => 'TT-MGT-FRS-026A',
            'jenis_audit'   => 'Audit Internal SMKP Minerba',
            'ruang_lingkup' => 'Seluruh area operasional tambang.',
            'areas'         => ['Area Pit Tambang', 'Workshop Mekanik', 'Gudang Bahan Peledak'],
            'tims'          => [
                ['nama' => 'Ketua Contoh', 'jabatan' => 'Lead Auditor', 'peran' => 'lead_auditor'],
                ['nama' => 'Pemandu Contoh', 'jabatan' => 'KTT Delegate', 'peran' => 'pemandu'],
            ],
        ]);

        $res->assertRedirect(route('admin.audit-sesi.rencana.edit', $sesi->id));

        $rencana = AuditRencana::where('audit_sesi_id', $sesi->id)->with(['areas', 'tims'])->first();
        $this->assertNotNull($rencana);
        $this->assertEquals(['Area Pit Tambang', 'Workshop Mekanik', 'Gudang Bahan Peledak'], $rencana->areas->pluck('nama_area')->all());
        $this->assertTrue($rencana->tims->contains(fn ($t) => $t->peran === 'pemandu' && $t->nama === 'Pemandu Contoh'));
    }

    public function test_profil_laporan_saves_data_peralatans_totals_narasi_and_uploads_struktur_organisasi(): void
    {
        Storage::fake('attachments');

        $auditorSmkp = $this->makeUser('auditor_smkp');
        $perusahaan = $this->makePerusahaan('PT Contoh Profil');
        $sesi = $this->makeSesi($perusahaan, $auditorSmkp, 2026);

        $elemen1 = Elemen::create([
            'kode_elemen' => 'I',
            'nama_elemen' => 'Kebijakan',
            'bobot'       => 10,
        ]);
        $elemen2 = Elemen::create([
            'kode_elemen' => 'II',
            'nama_elemen' => 'Perencanaan',
            'bobot'       => 15,
        ]);

        $file = UploadedFile::fake()->create('struktur-org.pdf', 120, 'application/pdf');

        $res = $this->actingAs($auditorSmkp)->put(route('admin.audit-sesi.laporan.profil.update', $sesi->id), [
            'kapasitas_produksi'    => 500000.50,
            'realisasi_produksi'    => 420000.25,
            'satuan_produksi'       => 'Ton',
            'jumlah_pekerja'        => 350,
            'jumlah_pekerja_asing'  => 4,
            'narasi_kegiatan'       => 'Kegiatan penambangan terbuka dan pengolahan mineral.',
            'narasi_penerapan_smkp' => 'SMKP diterapkan pada seluruh lini operasi.',
            'struktur_organisasi'   => $file,
            'peralatans'            => [
                ['kelompok' => 'Alat Berat', 'jenis' => 'Excavator', 'tipe' => 'EX-200', 'kapasitas' => '20 Ton', 'jumlah' => 5],
                ['kelompok' => 'Alat Berat', 'jenis' => 'Bulldozer', 'tipe' => 'DZ-85', 'kapasitas' => '30 Ton', 'jumlah' => 3],
                ['kelompok' => 'Angkut', 'jenis' => 'Dump Truck', 'tipe' => 'DT-40', 'kapasitas' => '40 Ton', 'jumlah' => 12],
            ],
            'narasi_elemens'        => [
                $elemen1->id => 'Kebijakan keselamatan telah disahkan pimpinan.',
                $elemen2->id => 'IBPR diperbarui secara berkala.',
            ],
        ]);

        $res->assertRedirect(route('admin.audit-sesi.laporan.profil', $sesi->id));

        $laporan = AuditLaporan::where('audit_sesi_id', $sesi->id)->with(['profil', 'peralatans', 'narasiElemens'])->first();
        $this->assertNotNull($laporan);
        $this->assertEquals(350, $laporan->profil->jumlah_pekerja);
        $this->assertEquals(4, $laporan->profil->jumlah_pekerja_asing);
        $this->assertNotNull($laporan->profil->struktur_organisasi_path);
        Storage::disk('attachments')->assertExists($laporan->profil->struktur_organisasi_path);

        // Automatic totals
        $this->assertEquals(['Alat Berat' => 8, 'Angkut' => 12], $laporan->getTotalPeralatanPerKelompok());
        $this->assertEquals(20, $laporan->getTotalPeralatan());

        // Narasi per elemen
        $this->assertEquals(2, $laporan->narasiElemens->count());
        $this->assertEquals(
            'Kebijakan keselamatan telah disahkan pimpinan.',
            $laporan->narasiElemens->firstWhere('elemen_id', $elemen1->id)->narasi
        );

        // Attachment download works
        $this->actingAs($auditorSmkp)
            ->get(route('attachments.show', ['type' => 'struktur_organisasi', 'id' => $laporan->profil->id]))
            ->assertStatus(200);

        // Reject invalid file type (.exe / .txt)
        $badFile = UploadedFile::fake()->create('script.txt', 10, 'text/plain');
        $this->actingAs($auditorSmkp)
            ->put(route('admin.audit-sesi.laporan.profil.update', $sesi->id), [
                'struktur_organisasi' => $badFile,
            ])
            ->assertSessionHasErrors('struktur_organisasi');
    }

    public function test_copy_from_tahap_awal_and_previous_period(): void
    {
        $auditorSmkp = $this->makeUser('auditor_smkp');
        $perusahaan = $this->makePerusahaan('PT Contoh Salin');
        $elemen = Elemen::create([
            'kode_elemen' => 'I',
            'nama_elemen' => 'Kebijakan',
            'bobot'       => 10,
        ]);

        // Previous period 2025
        $sesi2025 = $this->makeSesi($perusahaan, $auditorSmkp, 2025);
        $lap2025 = AuditLaporan::getOrCreateForSesi($sesi2025);
        $lap2025->profil->update([
            'narasi_kegiatan'       => 'Narasi kegiatan periode 2025.',
            'narasi_penerapan_smkp' => 'Penerapan SMKP periode 2025.',
            'satuan_produksi'       => 'Ton',
        ]);
        AuditLaporanPeralatan::create([
            'audit_laporan_id' => $lap2025->id,
            'kelompok'         => 'Alat Berat',
            'jenis'            => 'Loader',
            'tipe'             => 'LD-50',
            'kapasitas'        => '15 Ton',
            'jumlah'           => 6,
            'urutan'           => 1,
        ]);
        AuditLaporanNarasiElemen::create([
            'audit_laporan_id' => $lap2025->id,
            'elemen_id'        => $elemen->id,
            'narasi'           => 'Narasi Elemen I dari 2025.',
        ]);

        // Current period 2026 with Tahap Awal
        $sesi2026 = $this->makeSesi($perusahaan, $auditorSmkp, 2026);
        AuditTahapAwal::create([
            'audit_sesi_id'  => $sesi2026->id,
            'jumlah_pekerja' => 480,
        ]);

        // Copy jumlah pekerja from Tahap Awal
        $this->actingAs($auditorSmkp)
            ->post(route('admin.audit-sesi.laporan.profil.copy-tahap-awal', $sesi2026->id))
            ->assertRedirect(route('admin.audit-sesi.laporan.profil', $sesi2026->id));

        $lap2026 = AuditLaporan::where('audit_sesi_id', $sesi2026->id)->with('profil')->first();
        $this->assertEquals(480, $lap2026->profil->jumlah_pekerja);

        // Copy peralatan & narasi from previous period (2025)
        $this->actingAs($auditorSmkp)
            ->post(route('admin.audit-sesi.laporan.profil.copy-prev-period', $sesi2026->id))
            ->assertRedirect(route('admin.audit-sesi.laporan.profil', $sesi2026->id));

        $lap2026->refresh()->load(['profil', 'peralatans', 'narasiElemens']);
        $this->assertEquals('Narasi kegiatan periode 2025.', $lap2026->profil->narasi_kegiatan);
        $this->assertEquals('Penerapan SMKP periode 2025.', $lap2026->profil->narasi_penerapan_smkp);
        $this->assertEquals(6, $lap2026->getTotalPeralatan());
        $this->assertEquals('Narasi Elemen I dari 2025.', $lap2026->narasiElemens->firstWhere('elemen_id', $elemen->id)->narasi);
    }

    public function test_auditor_area_scoping_and_write_forbidden_for_profil_and_attachment(): void
    {
        Storage::fake('attachments');

        $auditorSmkp = $this->makeUser('auditor_smkp');
        $perusahaanA = $this->makePerusahaan('PT Area Alfa');
        $perusahaanB = $this->makePerusahaan('PT Area Beta');

        $sesiA = $this->makeSesi($perusahaanA, $auditorSmkp, 2026);
        $sesiB = $this->makeSesi($perusahaanB, $auditorSmkp, 2026);

        $lapA = AuditLaporan::getOrCreateForSesi($sesiA);
        $lapB = AuditLaporan::getOrCreateForSesi($sesiB);

        $pathA = UploadedFile::fake()->create('org-a.pdf', 50, 'application/pdf')->store('struktur_organisasi', 'attachments');
        $pathB = UploadedFile::fake()->create('org-b.pdf', 50, 'application/pdf')->store('struktur_organisasi', 'attachments');
        $lapA->profil->update(['struktur_organisasi_path' => $pathA]);
        $lapB->profil->update(['struktur_organisasi_path' => $pathB]);

        $auditorA = $this->makeUser('auditor', 'PT Area Alfa');

        // Own area readable
        $this->actingAs($auditorA)
            ->get(route('auditor.audit-sesi.laporan.profil', $sesiA->id))
            ->assertStatus(200);

        $this->actingAs($auditorA)
            ->get(route('attachments.show', ['type' => 'struktur_organisasi', 'id' => $lapA->profil->id]))
            ->assertStatus(200);

        // Other area blocked (404 from forUserArea()->findOrFail, 403 from AttachmentController)
        $this->actingAs($auditorA)
            ->get(route('auditor.audit-sesi.laporan.profil', $sesiB->id))
            ->assertStatus(404);

        $this->actingAs($auditorA)
            ->get(route('attachments.show', ['type' => 'struktur_organisasi', 'id' => $lapB->profil->id]))
            ->assertStatus(403);

        // Write route forbidden (403)
        $this->actingAs($auditorA)
            ->put(route('admin.audit-sesi.laporan.profil.update', $sesiA->id), [
                'jumlah_pekerja' => 999,
            ])
            ->assertStatus(403);
    }
}
