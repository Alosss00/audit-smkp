<?php

namespace App\Models;

use Carbon\Carbon;
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

    public const STATUS_OPTIONS = [
        'draft'           => 'Draft',
        'ditinjau'        => 'Ditinjau',
        'disahkan'        => 'Disahkan',
        'didistribusikan' => 'Didistribusikan',
        'dilaporkan'      => 'Dilaporkan',
    ];

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
        'catatan_revisi',
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

    public function snapshots(): HasMany
    {
        return $this->hasMany(AuditLaporanSnapshot::class, 'audit_laporan_id')->orderByDesc('revisi_ke')->orderByDesc('id');
    }

    public function latestSnapshot(): HasOne
    {
        return $this->hasOne(AuditLaporanSnapshot::class, 'audit_laporan_id')->latestOfMany('id');
    }

    public function distribusis(): HasMany
    {
        return $this->hasMany(AuditLaporanDistribusi::class, 'audit_laporan_id')->orderByDesc('tanggal_distribusi')->orderByDesc('id');
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

            $batasWaktu = self::hitungBatasWaktuUntukPeriode($sesi->tahun_periode);

            $laporan = self::create([
                'audit_sesi_id'         => $sesi->id,
                'status'                => 'draft',
                'penanggung_jawab_nama' => $leadName,
                'batas_waktu_pelaporan' => $batasWaktu,
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

    /**
     * Apakah laporan sudah disahkan (termasuk didistribusikan / dilaporkan).
     */
    public function isDisahkanAtauLebih(): bool
    {
        return in_array($this->status, ['disahkan', 'didistribusikan', 'dilaporkan'], true);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_OPTIONS[$this->status] ?? (string) $this->status;
    }

    /**
     * Hitung batas waktu pelaporan dari master_aturan_pelaporan aktif untuk tahun periode tertentu.
     */
    public static function hitungBatasWaktuUntukPeriode(?int $tahunPeriode): ?Carbon
    {
        if (! $tahunPeriode) {
            return null;
        }

        $aturan = MasterAturanPelaporan::where('is_active', true)
            ->whereNotNull('hari_setelah_akhir_periode')
            ->orderByDesc('is_verified')
            ->orderBy('id')
            ->first();

        if (! $aturan || $aturan->hari_setelah_akhir_periode === null) {
            return null;
        }

        return Carbon::create($tahunPeriode, 12, 31)->addDays((int) $aturan->hari_setelah_akhir_periode);
    }

    /**
     * Cek apakah laporan sudah melewati batas_waktu_pelaporan dan belum berstatus dilaporkan.
     */
    public function isOverdue(?Carbon $referenceDate = null): bool
    {
        if ($this->status === 'dilaporkan' || ! $this->batas_waktu_pelaporan) {
            return false;
        }

        $ref = ($referenceDate ?? Carbon::today())->startOfDay();

        return $ref->gt($this->batas_waktu_pelaporan->copy()->startOfDay());
    }

    /**
     * Sisa hari menuju batas_waktu_pelaporan (negatif jika sudah terlambat).
     */
    public function getHariTersisa(?Carbon $referenceDate = null): ?int
    {
        if (! $this->batas_waktu_pelaporan) {
            return null;
        }

        $ref = ($referenceDate ?? Carbon::today())->startOfDay();

        return (int) $ref->diffInDays($this->batas_waktu_pelaporan->copy()->startOfDay(), false);
    }

    /**
     * Evaluasi definisi "Audit Dinyatakan Selesai" (materi slide 12-15):
     * 1. Rencana Audit memiliki minimal 1 jadwal termasuk rapat_penutupan.
     * 2. Seluruh kegiatan pada jadwal berstatus terlaksana atau dibatalkan (tidak ada direncanakan/ditunda).
     * 3. Penilaian matriks sesi audit telah selesai (status sesi 'selesai' atau progress >= 100%).
     *
     * @return array{selesai: bool, alasan: array<int, string>}
     */
    public function auditDinyatakanSelesai(): array
    {
        $this->loadMissing('auditSesi.rencana.jadwals');
        $sesi = $this->auditSesi;
        $alasan = [];

        if (! $sesi) {
            return ['selesai' => false, 'alasan' => ['Sesi audit tidak ditemukan.']];
        }

        $rencana = $sesi->rencana;
        if (! $rencana || $rencana->jadwals->isEmpty()) {
            $alasan[] = 'Jadwal pelaksanaan pada Rencana Audit belum diisi.';
        } else {
            $hasPenutupan = $rencana->jadwals->contains(fn ($j) => $j->jenis_kegiatan === 'rapat_penutupan');
            if (! $hasPenutupan) {
                $alasan[] = 'Jadwal Rencana Audit belum mencantumkan kegiatan Rapat Penutupan.';
            }

            $belumSelesai = $rencana->jadwals->filter(
                fn ($j) => ! in_array($j->status_pelaksanaan, ['terlaksana', 'dibatalkan'], true)
            );
            if ($belumSelesai->isNotEmpty()) {
                $alasan[] = 'Masih terdapat ' . $belumSelesai->count() . ' kegiatan pada jadwal audit yang belum berstatus terlaksana/dibatalkan.';
            }
        }

        $progress = $sesi->hitungProgressPenilaian();
        if ($sesi->status !== 'selesai' && $progress < 100.0) {
            $alasan[] = 'Penilaian matriks sesi audit belum lengkap (' . number_format($progress, 1) . '%).';
        }

        return [
            'selesai' => empty($alasan),
            'alasan'  => $alasan,
        ];
    }

    /**
     * Verifikasi integritas snapshot terbaru berdasarkan SHA-256.
     *
     * @return array{has_snapshot: bool, valid: bool, hash_stored: ?string, hash_computed: ?string, revisi_ke: ?int}
     */
    public function verifyLatestSnapshotIntegrity(): array
    {
        $snapshot = $this->latestSnapshot()->first();
        if (! $snapshot) {
            return [
                'has_snapshot'  => false,
                'valid'         => false,
                'hash_stored'   => null,
                'hash_computed' => null,
                'revisi_ke'     => null,
            ];
        }

        $computed = hash('sha256', (string) $snapshot->payload_json);

        return [
            'has_snapshot'  => true,
            'valid'         => hash_equals((string) $snapshot->hash_sha256, $computed),
            'hash_stored'   => (string) $snapshot->hash_sha256,
            'hash_computed' => $computed,
            'revisi_ke'     => $snapshot->revisi_ke,
        ];
    }
}
