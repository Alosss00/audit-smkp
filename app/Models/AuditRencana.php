<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditRencana extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'audit_rencanas';

    public const PERAN_OPTIONS = [
        'lead_auditor' => 'Ketua Tim (Lead Auditor)',
        'auditor'      => 'Auditor',
        'tenaga_ahli'  => 'Tenaga Ahli',
        'pengamat'     => 'Pengamat (Observer)',
    ];

    public const JENIS_KEGIATAN_OPTIONS = [
        'rapat_pembukaan'    => 'Rapat Pembukaan',
        'audit_elemen'       => 'Audit Elemen / Verifikasi Lapangan',
        'rapat_internal_tim' => 'Rapat Internal Tim Audit',
        'rapat_penutupan'    => 'Rapat Penutupan',
        'lainnya'            => 'Kegiatan Lainnya',
    ];

    public const METODE_OPTIONS = [
        'wawancara'        => 'Wawancara',
        'kuesioner'        => 'Kuesioner',
        'observasi'        => 'Observasi',
        'pengujian'        => 'Pengujian',
        'pengukuran'       => 'Pengukuran',
        'tinjauan_dokumen' => 'Tinjauan Dokumen',
        'tinjauan_rekaman' => 'Tinjauan Rekaman',
    ];

    public const TEKNIK_SAMPLING_OPTIONS = [
        'purposive_key_area'       => 'Purposive — Key Area',
        'purposive_risk_based'     => 'Purposive — Risk Based',
        'purposive_accident_based' => 'Purposive — Accident Based',
        'cluster'                  => 'Cluster Sampling',
        'simple_random'            => 'Simple Random Sampling',
        'accidental'               => 'Accidental Sampling',
        'stratified_random'        => 'Stratified Random Sampling',
        'professional_judgement'   => 'Professional Judgement',
    ];

    public const METODE_PENENTUAN_OPTIONS = [
        'professional_judgement' => 'Professional Judgement',
        'slovin'                 => 'Rumus Slovin',
        'best_practice'          => 'Best Practice',
        'triangulasi'            => 'Triangulasi',
    ];

    public const KELOMPOK_PENANDATANGAN_OPTIONS = [
        'tambang'              => 'Perusahaan Pertambangan (Tambang)',
        'pengolahan_pemurnian' => 'Pengolahan dan/atau Pemurnian',
        'jasa_pertambangan'    => 'Perusahaan Jasa Pertambangan',
    ];

    protected $fillable = [
        'audit_sesi_id',
        'no_dokumen',
        'jenis_audit',
        'kriteria_audit',
        'tujuan',
        'ruang_lingkup',
        'pengecualian',
        'dokumen_relevan',
        'fasilitas',
        'distribusi_laporan',
        'kelompok_penandatangan',
        'status',
    ];

    protected $casts = [
        'dokumen_relevan' => 'array',
    ];

    protected function casts(): array
    {
        return $this->casts;
    }

    public function auditSesi(): BelongsTo
    {
        return $this->belongsTo(AuditSesi::class, 'audit_sesi_id');
    }

    public function tims(): HasMany
    {
        return $this->hasMany(AuditRencanaTim::class, 'audit_rencana_id')->orderBy('urutan')->orderBy('id');
    }

    public function jadwals(): HasMany
    {
        return $this->hasMany(AuditRencanaJadwal::class, 'audit_rencana_id')
            ->orderBy('hari_ke')
            ->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->orderBy('id');
    }

    public function sampels(): HasMany
    {
        return $this->hasMany(AuditRencanaSampel::class, 'audit_rencana_id')->orderBy('id');
    }

    public function pengesahans(): HasMany
    {
        return $this->hasMany(AuditRencanaPengesahan::class, 'audit_rencana_id')->orderBy('urutan')->orderBy('id');
    }

    public function isFinal(): bool
    {
        return $this->status === 'final';
    }

    /**
     * Get or initialize AuditRencana for an AuditSesi:
     * - Automatically creates 1 empty AuditRencanaSampel row per active SubElemen.
     * - Automatically copies AuditRencanaPengesahan rows from master according to company's kelompokPenandatangan().
     */
    public static function getOrInitializeForSesi(AuditSesi $sesi): self
    {
        $sesi->loadMissing('perusahaan');
        $existing = self::where('audit_sesi_id', $sesi->id)->first();
        if ($existing) {
            $existing->syncMissingSubElemenSampels();
            return $existing;
        }

        $kelompok = $sesi->perusahaan ? $sesi->perusahaan->kelompokPenandatangan() : null;

        $rencana = self::create([
            'audit_sesi_id'          => $sesi->id,
            'no_dokumen'             => 'TT-MGT-FRS-026A',
            'jenis_audit'            => 'Audit Internal SMKP Minerba',
            'kriteria_audit'         => 'Lampiran II Keputusan Direktur Jenderal Mineral dan Batubara Nomor 185.K/37.04/DJB/2019',
            'kelompok_penandatangan' => $kelompok,
            'status'                 => 'draft',
        ]);

        $rencana->syncMissingSubElemenSampels();
        if ($kelompok) {
            $rencana->seedPengesahanFromMaster($kelompok);
        }

        return $rencana;
    }

    /**
     * Ensure every active (non-N/A) SubElemen has a blank sample planning row.
     */
    public function syncMissingSubElemenSampels(): void
    {
        $existingIds = $this->sampels()->pluck('sub_elemen_id')->all();
        $activeSubElemens = SubElemen::where('is_na', false)
            ->orderBy('elemen_id')
            ->orderBy('kode_sub')
            ->get(['id']);

        foreach ($activeSubElemens as $sub) {
            if (! in_array($sub->id, $existingIds, true)) {
                AuditRencanaSampel::create([
                    'audit_rencana_id' => $this->id,
                    'sub_elemen_id'    => $sub->id,
                ]);
            }
        }
    }

    /**
     * Populate pengesahan rows from MasterPenandatanganRencana for a given kelompok.
     */
    public function seedPengesahanFromMaster(string $kelompok): void
    {
        if ($this->pengesahans()->exists()) {
            return;
        }

        $masters = MasterPenandatanganRencana::where('kelompok', $kelompok)
            ->where('is_active', true)
            ->orderBy('urutan')
            ->get();

        foreach ($masters as $m) {
            AuditRencanaPengesahan::create([
                'audit_rencana_id' => $this->id,
                'urutan'           => $m->urutan,
                'jabatan'          => $m->jabatan,
                'nama'             => null,
                'tanggal_ttd'      => null,
            ]);
        }
    }
}
