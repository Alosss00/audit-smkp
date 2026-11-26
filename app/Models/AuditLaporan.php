<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditLaporan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'audit_laporans';

    protected $fillable = [
        'audit_sesi_id',
        'nomor_laporan',
        'tanggal_laporan',
        'tempat_penerbitan',
        'status',
        'diajukan_oleh',
        'tanggal_diajukan',
        'ditinjau_oleh',
        'tanggal_ditinjau',
        'disahkan_oleh',
        'tanggal_pengesahan',
        'tanggal_dilaporkan',
        'batas_waktu_pelaporan',
        'penanggung_jawab_nama',
        'teks_override',
        'revisi_ke',
    ];

    protected $casts = [
        'tanggal_laporan'       => 'date',
        'tanggal_diajukan'      => 'datetime',
        'tanggal_ditinjau'      => 'datetime',
        'tanggal_pengesahan'    => 'datetime',
        'tanggal_dilaporkan'    => 'date',
        'batas_waktu_pelaporan' => 'date',
        'teks_override'         => 'array',
        'revisi_ke'             => 'integer',
    ];

    protected function casts(): array
    {
        return $this->casts;
    }

    public function auditSesi(): BelongsTo
    {
        return $this->belongsTo(AuditSesi::class, 'audit_sesi_id');
    }

    public function profil(): HasOne
    {
        return $this->hasOne(AuditLaporanProfil::class, 'audit_laporan_id');
    }

    public function peralatans(): HasMany
    {
        return $this->hasMany(AuditLaporanPeralatan::class, 'audit_laporan_id')->orderBy('kelompok')->orderBy('urutan')->orderBy('id');
    }

    public function narasiElemens(): HasMany
    {
        return $this->hasMany(AuditLaporanNarasiElemen::class, 'audit_laporan_id');
    }

    public function pengaju(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diajukan_oleh');
    }

    public function peninjau(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ditinjau_oleh');
    }

    public function pengesah(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disahkan_oleh');
    }

    public static function getOrCreateForSesi(AuditSesi $sesi): self
    {
        $laporan = self::where('audit_sesi_id', $sesi->id)->first();
        if (! $laporan) {
            $leadName = null;
            $rencana = AuditRencana::where('audit_sesi_id', $sesi->id)->with('tims')->first();
            if ($rencana) {
                $lead = $rencana->tims->firstWhere('peran', 'lead_auditor');
                $leadName = $lead?->nama;
            }

            $laporan = self::create([
                'audit_sesi_id'         => $sesi->id,
                'status'                => 'draft',
                'penanggung_jawab_nama' => $leadName,
                'revisi_ke'             => 0,
            ]);
        }

        if (! $laporan->profil()->exists()) {
            AuditLaporanProfil::create(['audit_laporan_id' => $laporan->id]);
        }

        return $laporan;
    }

    /**
     * Hitung total unit peralatan per kelompok secara otomatis dari database.
     *
     * @return array<string, int>
     */
    public function getTotalPeralatanPerKelompok(): array
    {
        $this->loadMissing('peralatans');
        $totals = [];
        foreach ($this->peralatans as $alat) {
            $k = $alat->kelompok ?: 'Lainnya';
            $totals[$k] = ($totals[$k] ?? 0) + (int) $alat->jumlah;
        }

        return $totals;
    }

    /**
     * Hitung grand total seluruh peralatan dari database.
     */
    public function getTotalPeralatan(): int
    {
        $this->loadMissing('peralatans');

        return (int) $this->peralatans->sum('jumlah');
    }
}
